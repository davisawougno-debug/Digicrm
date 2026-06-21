<?php

require_once MODELS_PATH . '/Invoice.php';
require_once MODELS_PATH . '/Payment.php';
require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';

class InvoiceController
{
    public static function factures(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':
                $invoiceList = Invoice::getAll();
                $GLOBALS['viewData'] = compact('invoiceList');
                $pageTitle = 'Factures';
                require __DIR__ . '/admin_factures.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $clientId = (int)($_POST['client_id'] ?? 0);
                        $contractId = $_POST['contract_id'] ?? null;
                        $dateEmission = $_POST['date_emission'] ?? date('Y-m-d');
                        $dateEcheance = $_POST['date_echeance'] ?? null;
                        $notes = trim($_POST['notes'] ?? '');
                        $statut = $_POST['statut'] ?? 'impayee';

                        Validation::reset();
                        Validation::required('client_id', 'Client', $clientId);

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $numero = Invoice::generateNumero();
                            $montantHt = 0;
                            $tvaTotal = 0;
                            $montantTtc = 0;

                            $invoiceId = Invoice::create([
                                'numero_facture' => $numero,
                                'client_id'      => $clientId,
                                'contract_id'    => $contractId ?: null,
                                'montant_ht'     => $montantHt,
                                'tva'            => $tvaTotal,
                                'montant_ttc'    => $montantTtc,
                                'montant_paye'   => 0,
                                'statut'         => $statut,
                                'date_emission'  => $dateEmission,
                                'date_echeance'  => $dateEcheance,
                                'notes'          => $notes,
                            ]);

                            if ($invoiceId) {
                                Activity::log(Session::get('user_id'), 'creation_facture', "Création de la facture {$numero}");
                                Session::setFlash('success', 'Facture créée avec succès.');
                                header('Location: ' . BASE_URL . '/admin_index.php?module=factures');
                                exit;
                            } else {
                                Session::setFlash('error', 'Une erreur est survenue lors de la création.');
                            }
                        }
                    }
                }
                $clients = Client::getAll();
                $GLOBALS['viewData'] = compact('clients');
                $pageTitle = 'Nouvelle facture';
                require __DIR__ . '/admin_factures_form.php';
                exit;

            case 'edit':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Facture invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=factures');
                    exit;
                }
                $invoice = Invoice::findById($id);
                if (!$invoice) {
                    Session::setFlash('error', 'Facture introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=factures');
                    exit;
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $clientId = (int)($_POST['client_id'] ?? 0);
                        $contractId = $_POST['contract_id'] ?? null;
                        $dateEmission = $_POST['date_emission'] ?? date('Y-m-d');
                        $dateEcheance = $_POST['date_echeance'] ?? null;
                        $notes = trim($_POST['notes'] ?? '');
                        $statut = $_POST['statut'] ?? 'impayee';

                        Invoice::update($id, [
                            'client_id'     => $clientId,
                            'contract_id'   => $contractId ?: null,
                            'statut'        => $statut,
                            'date_emission' => $dateEmission,
                            'date_echeance' => $dateEcheance,
                            'notes'         => $notes,
                        ]);
                        Activity::log(Session::get('user_id'), 'modification_facture', "Modification de la facture {$invoice['numero_facture']}");
                        Session::setFlash('success', 'Facture mise à jour avec succès.');
                        header('Location: ' . BASE_URL . '/admin_index.php?module=factures');
                        exit;
                    }
                }
                $clients = Client::getAll();
                $GLOBALS['viewData'] = compact('invoice', 'clients');
                $pageTitle = 'Modifier facture';
                require __DIR__ . '/admin_factures_form.php';
                exit;

            case 'view':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Facture invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=factures');
                    exit;
                }
                $invoice = Invoice::findById($id);
                if (!$invoice) {
                    Session::setFlash('error', 'Facture introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=factures');
                    exit;
                }
                $client = Client::findById($invoice['client_id']);
                $paiements = Payment::getByInvoice($id);
                $GLOBALS['viewData'] = compact('invoice', 'client', 'paiements');
                $pageTitle = 'Facture ' . $invoice['numero_facture'];
                require __DIR__ . '/admin_factures_view.php';
                exit;

            case 'delete':
                if ($id && Security::validateId($id)) {
                    $invoice = Invoice::findById($id);
                    if ($invoice) {
                        Invoice::delete($id);
                        Activity::log(Session::get('user_id'), 'suppression_facture', "Suppression de la facture {$invoice['numero_facture']}");
                        Session::setFlash('success', 'Facture supprimée avec succès.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=factures');
                exit;

            case 'send':
                if ($id && Security::validateId($id)) {
                    $invoice = Invoice::findById($id);
                    if ($invoice) {
                        Invoice::update($id, ['statut' => 'impayee']);
                        Activity::log(Session::get('user_id'), 'envoi_facture', "Envoi de la facture {$invoice['numero_facture']}");
                        Session::setFlash('success', 'Facture envoyée.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=factures');
                exit;

            case 'pay':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Facture invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=factures');
                    exit;
                }
                $invoice = Invoice::findById($id);
                if (!$invoice) {
                    Session::setFlash('error', 'Facture introuvable.');
                } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $montant = (float)($_POST['montant'] ?? 0);
                        $mode = $_POST['mode_paiement'] ?? 'virement';
                        $reference = trim($_POST['reference'] ?? '');
                        $notes = trim($_POST['notes'] ?? '');
                        $datePaiement = $_POST['date_paiement'] ?? date('Y-m-d');

                        Validation::reset();
                        Validation::required('montant', 'Montant', $montant);
                        if ($montant <= 0) {
                            Validation::addError('montant', 'Le montant doit être supérieur à 0.');
                        }

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $paymentId = Payment::create([
                                'invoice_id'    => $id,
                                'montant'       => $montant,
                                'mode_paiement' => $mode,
                                'date_paiement' => $datePaiement,
                                'reference'     => $reference,
                                'notes'         => $notes,
                            ]);

                            if ($paymentId) {
                                $totalPaye = (float)$invoice['montant_paye'] + $montant;
                                $newStatut = $totalPaye >= (float)$invoice['montant_ttc'] ? 'payee' : 'partielle';
                                Invoice::update($id, [
                                    'montant_paye' => $totalPaye,
                                    'statut'       => $newStatut,
                                ]);
                                Activity::log(Session::get('user_id'), 'paiement_facture', "Paiement de {$montant} sur la facture {$invoice['numero_facture']}");
                                Session::setFlash('success', 'Paiement enregistré avec succès.');
                                header('Location: ' . BASE_URL . '/admin_index.php?module=factures&action=view&id=' . $id);
                                exit;
                            } else {
                                Session::setFlash('error', 'Une erreur est survenue lors de l\'enregistrement du paiement.');
                            }
                        }
                    }
                }
                $GLOBALS['viewData'] = compact('invoice');
                $pageTitle = 'Paiement - ' . $invoice['numero_facture'];
                require __DIR__ . '/admin_factures.php';
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=factures');
                exit;
        }
    }
}
