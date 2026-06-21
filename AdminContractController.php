<?php

require_once MODELS_PATH . '/Contract.php';
require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/Devis.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';

class ContractController
{
    public static function contrats(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':
                $contractList = Contract::getAll();
                $GLOBALS['viewData'] = compact('contractList');
                $pageTitle = 'Contrats';
                require __DIR__ . '/admin_contrats.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $clientId = (int)($_POST['client_id'] ?? 0);
                        $devisId = $_POST['devis_id'] ?? null;
                        $dateDebut = $_POST['date_debut'] ?? null;
                        $dateFin = $_POST['date_fin'] ?? null;
                        $montant = (float)($_POST['montant'] ?? 0);
                        $description = trim($_POST['description'] ?? '');
                        $statut = $_POST['statut'] ?? 'actif';

                        Validation::reset();
                        Validation::required('client_id', 'Client', $clientId);

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $numero = Contract::generateNumero();
                            $contractId = Contract::create([
                                'numero'      => $numero,
                                'client_id'   => $clientId,
                                'devis_id'    => $devisId ?: null,
                                'date_debut'  => $dateDebut,
                                'date_fin'    => $dateFin,
                                'montant'     => $montant,
                                'statut'      => $statut,
                                'description' => $description,
                            ]);
                            if ($contractId) {
                                Activity::log(Session::get('user_id'), 'creation_contrat', "Création du contrat {$numero}");
                                Session::setFlash('success', 'Contrat créé avec succès.');
                                header('Location: ' . BASE_URL . '/admin_index.php?module=contrats');
                                exit;
                            } else {
                                Session::setFlash('error', 'Une erreur est survenue lors de la création.');
                            }
                        }
                    }
                }
                $clients = Client::getAll();
                $devisList = Devis::getAll();
                $GLOBALS['viewData'] = compact('clients', 'devisList');
                $pageTitle = 'Nouveau contrat';
                require __DIR__ . '/admin_contrats.php';
                exit;

            case 'edit':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Contrat invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=contrats');
                    exit;
                }
                $contract = Contract::findById($id);
                if (!$contract) {
                    Session::setFlash('error', 'Contrat introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=contrats');
                    exit;
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $clientId = (int)($_POST['client_id'] ?? 0);
                        $devisId = $_POST['devis_id'] ?? null;
                        $dateDebut = $_POST['date_debut'] ?? null;
                        $dateFin = $_POST['date_fin'] ?? null;
                        $montant = (float)($_POST['montant'] ?? 0);
                        $description = trim($_POST['description'] ?? '');
                        $statut = $_POST['statut'] ?? 'actif';

                        Validation::reset();
                        Validation::required('client_id', 'Client', $clientId);

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            Contract::update($id, [
                                'client_id'   => $clientId,
                                'devis_id'    => $devisId ?: null,
                                'date_debut'  => $dateDebut,
                                'date_fin'    => $dateFin,
                                'montant'     => $montant,
                                'statut'      => $statut,
                                'description' => $description,
                            ]);
                            Activity::log(Session::get('user_id'), 'modification_contrat', "Modification du contrat {$contract['numero']}");
                            Session::setFlash('success', 'Contrat mis à jour avec succès.');
                            header('Location: ' . BASE_URL . '/admin_index.php?module=contrats');
                            exit;
                        }
                    }
                }
                $clients = Client::getAll();
                $devisList = Devis::getAll();
                $GLOBALS['viewData'] = compact('contract', 'clients', 'devisList');
                $pageTitle = 'Modifier contrat';
                require __DIR__ . '/admin_contrats.php';
                exit;

            case 'delete':
                if ($id && Security::validateId($id)) {
                    $contract = Contract::findById($id);
                    if ($contract) {
                        Contract::delete($id);
                        Activity::log(Session::get('user_id'), 'suppression_contrat', "Suppression du contrat {$contract['numero']}");
                        Session::setFlash('success', 'Contrat supprimé avec succès.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=contrats');
                exit;

            case 'activate':
                if ($id && Security::validateId($id)) {
                    $contract = Contract::findById($id);
                    if ($contract) {
                        Contract::update($id, ['statut' => 'actif']);
                        Activity::log(Session::get('user_id'), 'activation_contrat', "Activation du contrat {$contract['numero']}");
                        Session::setFlash('success', 'Contrat activé avec succès.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=contrats');
                exit;

            case 'suspend':
                if ($id && Security::validateId($id)) {
                    $contract = Contract::findById($id);
                    if ($contract) {
                        Contract::update($id, ['statut' => 'suspendu']);
                        Activity::log(Session::get('user_id'), 'suspension_contrat', "Suspension du contrat {$contract['numero']}");
                        Session::setFlash('success', 'Contrat suspendu.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=contrats');
                exit;

            case 'terminate':
                if ($id && Security::validateId($id)) {
                    $contract = Contract::findById($id);
                    if ($contract) {
                        Contract::update($id, ['statut' => 'termine']);
                        Activity::log(Session::get('user_id'), 'resiliation_contrat', "Résiliation du contrat {$contract['numero']}");
                        Session::setFlash('success', 'Contrat résilié.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=contrats');
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=contrats');
                exit;
        }
    }
}
