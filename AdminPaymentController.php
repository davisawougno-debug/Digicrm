<?php

require_once MODELS_PATH . '/Payment.php';
require_once MODELS_PATH . '/Invoice.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';

class PaymentController
{
    public static function paiements(string $action, ?int $id = null): void
    {
        $recalcInvoiceMontantPaye = function ($invoiceId) {
            $pdo = getPDO();
            $stmt = $pdo->prepare('SELECT COALESCE(SUM(montant), 0) FROM payments WHERE invoice_id = :invoice_id');
            $stmt->execute([':invoice_id' => $invoiceId]);
            $total = (float)$stmt->fetchColumn();
            $invoice = Invoice::findById($invoiceId);
            if ($invoice) {
                $newStatut = $total >= (float)$invoice['montant_ttc'] ? 'payee' : ($total > 0 ? 'partielle' : $invoice['statut']);
                Invoice::update($invoiceId, [
                    'montant_paye' => $total,
                    'statut'       => $newStatut,
                ]);
            }
        };

        switch ($action) {
            case 'list':
                $paymentList = Payment::getAll();
                $GLOBALS['viewData'] = compact('paymentList');
                $pageTitle = 'Paiements';
                require __DIR__ . '/admin_paiements.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $invoiceId = (int)($_POST['invoice_id'] ?? 0);
                        $montant = (float)($_POST['montant'] ?? 0);
                        $mode = $_POST['mode_paiement'] ?? 'virement';
                        $datePaiement = $_POST['date_paiement'] ?? date('Y-m-d');
                        $reference = trim($_POST['reference'] ?? '');
                        $notes = trim($_POST['notes'] ?? '');

                        Validation::reset();
                        Validation::required('invoice_id', 'Facture', $invoiceId);
                        if ($montant <= 0) {
                            Validation::addError('montant', 'Le montant doit être supérieur à 0.');
                        }

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $paymentId = Payment::create([
                                'invoice_id'    => $invoiceId,
                                'montant'       => $montant,
                                'mode_paiement' => $mode,
                                'date_paiement' => $datePaiement,
                                'reference'     => $reference,
                                'notes'         => $notes,
                            ]);
                            if ($paymentId) {
                                $recalcInvoiceMontantPaye($invoiceId);
                                Activity::log(Session::get('user_id'), 'creation_paiement', "Paiement de {$montant} créé");
                                Session::setFlash('success', 'Paiement enregistré avec succès.');
                                header('Location: ' . BASE_URL . '/admin_index.php?module=paiements');
                                exit;
                            } else {
                                Session::setFlash('error', 'Une erreur est survenue lors de l\'enregistrement.');
                            }
                        }
                    }
                }
                $invoices = Invoice::getAll();
                $GLOBALS['viewData'] = compact('invoices');
                $pageTitle = 'Nouveau paiement';
                require __DIR__ . '/admin_paiements.php';
                exit;

            case 'edit':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Paiement invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=paiements');
                    exit;
                }
                $payment = Payment::findById($id);
                if (!$payment) {
                    Session::setFlash('error', 'Paiement introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=paiements');
                    exit;
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $invoiceId = (int)($_POST['invoice_id'] ?? 0);
                        $montant = (float)($_POST['montant'] ?? 0);
                        $mode = $_POST['mode_paiement'] ?? 'virement';
                        $datePaiement = $_POST['date_paiement'] ?? date('Y-m-d');
                        $reference = trim($_POST['reference'] ?? '');
                        $notes = trim($_POST['notes'] ?? '');

                        Validation::reset();
                        Validation::required('invoice_id', 'Facture', $invoiceId);
                        if ($montant <= 0) {
                            Validation::addError('montant', 'Le montant doit être supérieur à 0.');
                        }

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $oldInvoiceId = $payment['invoice_id'];
                            Payment::update($id, [
                                'invoice_id'    => $invoiceId,
                                'montant'       => $montant,
                                'mode_paiement' => $mode,
                                'date_paiement' => $datePaiement,
                                'reference'     => $reference,
                                'notes'         => $notes,
                            ]);
                            $recalcInvoiceMontantPaye($oldInvoiceId);
                            if ($oldInvoiceId !== $invoiceId) {
                                $recalcInvoiceMontantPaye($invoiceId);
                            }
                            Activity::log(Session::get('user_id'), 'modification_paiement', "Paiement #{$id} modifié");
                            Session::setFlash('success', 'Paiement mis à jour avec succès.');
                            header('Location: ' . BASE_URL . '/admin_index.php?module=paiements');
                            exit;
                        }
                    }
                }
                $invoices = Invoice::getAll();
                $GLOBALS['viewData'] = compact('payment', 'invoices');
                $pageTitle = 'Modifier paiement';
                require __DIR__ . '/admin_paiements.php';
                exit;

            case 'delete':
                if ($id && Security::validateId($id)) {
                    $payment = Payment::findById($id);
                    if ($payment) {
                        $invoiceId = $payment['invoice_id'];
                        Payment::delete($id);
                        $recalcInvoiceMontantPaye($invoiceId);
                        Activity::log(Session::get('user_id'), 'suppression_paiement', "Paiement #{$id} supprimé");
                        Session::setFlash('success', 'Paiement supprimé avec succès.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=paiements');
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=paiements');
                exit;
        }
    }
}
