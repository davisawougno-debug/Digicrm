<?php

require_once MODELS_PATH . '/Devis.php';
require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/Prospect.php';
require_once MODELS_PATH . '/Contract.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';

class DevisController
{
    public static function devis(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':
                $devisList = Devis::getAll();
                $clients   = Client::getAll();
                $GLOBALS['viewData'] = compact('devisList', 'clients');
                $pageTitle = 'Devis';
                require __DIR__ . '/admin_devis.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $clientId = $_POST['client_id'] ?? null;
                        $prospectId = $_POST['prospect_id'] ?? null;
                        $dateExpiration = $_POST['date_expiration'] ?? null;
                        $notes = trim($_POST['notes'] ?? '');
                        $lignes = $_POST['lignes'] ?? [];

                        Validation::reset();
                        if ($clientId) {
                            Validation::required('client_id', 'Client', $clientId);
                        }

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $numeroDevis = Devis::generateNumero();
                            $montantHt = 0;
                            $tvaTotal = 0;
                            $montantTtc = 0;

                            foreach ($lignes as $ligne) {
                                if (!empty($ligne['service_id'])) {
                                    $qte = (float)($ligne['quantite'] ?? 1);
                                    $pu = (float)($ligne['prix_unitaire'] ?? 0);
                                    $tva = (float)($ligne['tva'] ?? 0);
                                    $ligneHt = $qte * $pu;
                                    $ligneTtc = $ligneHt + ($ligneHt * $tva / 100);
                                    $montantHt += $ligneHt;
                                    $tvaTotal += $ligneTtc - $ligneHt;
                                    $montantTtc += $ligneTtc;
                                }
                            }

                            $devisId = Devis::create([
                                'numero_devis'   => $numeroDevis,
                                'client_id'      => $clientId ?: null,
                                'prospect_id'    => $prospectId ?: null,
                                'montant_ht'     => $montantHt,
                                'tva'            => $tvaTotal,
                                'montant_ttc'    => $montantTtc,
                                'statut'         => 'brouillon',
                                'date_expiration'=> $dateExpiration,
                                'notes'          => $notes,
                            ]);

                            if ($devisId) {
                                foreach ($lignes as $ligne) {
                                    if (!empty($ligne['service_id'])) {
                                        $qte = (float)($ligne['quantite'] ?? 1);
                                        $pu = (float)($ligne['prix_unitaire'] ?? 0);
                                        $tva = (float)($ligne['tva'] ?? 0);
                                        $ligneHt = $qte * $pu;
                                        $ligneTtc = $ligneHt + ($ligneHt * $tva / 100);
                                        Devis::addLigne($devisId, [
                                            'service_id'    => $ligne['service_id'],
                                            'quantite'      => $qte,
                                            'prix_unitaire' => $pu,
                                            'montant_ht'    => $ligneHt,
                                            'tva'           => $tva,
                                            'montant_ttc'   => $ligneTtc,
                                        ]);
                                    }
                                }
                                Devis::updateTotaux($devisId);
                                Activity::log(Session::get('user_id'), 'creation_devis', "Création du devis {$numeroDevis}");
                                Session::setFlash('success', 'Devis créé avec succès.');
                                header('Location: ' . BASE_URL . '/admin_index.php?module=devis');
                                exit;
                            } else {
                                Session::setFlash('error', 'Une erreur est survenue lors de la création.');
                            }
                        }
                    }
                }
                $clients = Client::getAll();
                $prospects = Prospect::getAll();
                $GLOBALS['viewData'] = compact('clients', 'prospects');
                $pageTitle = 'Nouveau devis';
                require __DIR__ . '/admin_devis_form.php';
                exit;

            case 'edit':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Devis invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=devis');
                    exit;
                }
                $devis = Devis::findById($id);
                if (!$devis) {
                    Session::setFlash('error', 'Devis introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=devis');
                    exit;
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $clientId = $_POST['client_id'] ?? null;
                        $prospectId = $_POST['prospect_id'] ?? null;
                        $statut = $_POST['statut'] ?? 'brouillon';
                        $dateExpiration = $_POST['date_expiration'] ?? null;
                        $notes = trim($_POST['notes'] ?? '');
                        $lignes = $_POST['lignes'] ?? [];

                        $montantHt = 0;
                        $tvaTotal = 0;
                        $montantTtc = 0;

                        $pdo = getPDO();
                        $stmt = $pdo->prepare('DELETE FROM devis_details WHERE devis_id = :devis_id');
                        $stmt->execute([':devis_id' => $id]);

                        foreach ($lignes as $ligne) {
                            if (!empty($ligne['service_id'])) {
                                $qte = (float)($ligne['quantite'] ?? 1);
                                $pu = (float)($ligne['prix_unitaire'] ?? 0);
                                $tva = (float)($ligne['tva'] ?? 0);
                                $ligneHt = $qte * $pu;
                                $ligneTtc = $ligneHt + ($ligneHt * $tva / 100);
                                $montantHt += $ligneHt;
                                $tvaTotal += $ligneTtc - $ligneHt;
                                $montantTtc += $ligneTtc;
                                Devis::addLigne($id, [
                                    'service_id'    => $ligne['service_id'],
                                    'quantite'      => $qte,
                                    'prix_unitaire' => $pu,
                                    'montant_ht'    => $ligneHt,
                                    'tva'           => $tva,
                                    'montant_ttc'   => $ligneTtc,
                                ]);
                            }
                        }

                        Devis::update($id, [
                            'client_id'      => $clientId ?: null,
                            'prospect_id'    => $prospectId ?: null,
                            'montant_ht'     => $montantHt,
                            'tva'            => $tvaTotal,
                            'montant_ttc'    => $montantTtc,
                            'statut'         => $statut,
                            'date_expiration'=> $dateExpiration,
                            'notes'          => $notes,
                        ]);
                        Devis::updateTotaux($id);
                        Activity::log(Session::get('user_id'), 'modification_devis', "Modification du devis {$devis['numero_devis']}");
                        Session::setFlash('success', 'Devis mis à jour avec succès.');
                        header('Location: ' . BASE_URL . '/admin_index.php?module=devis');
                        exit;
                    }
                }
                $lignes = Devis::getLignes($id);
                $clients = Client::getAll();
                $prospects = Prospect::getAll();
                $GLOBALS['viewData'] = compact('devis', 'lignes', 'clients', 'prospects');
                $pageTitle = 'Modifier devis';
                require __DIR__ . '/admin_devis_form.php';
                exit;

            case 'show':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Devis invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=devis');
                    exit;
                }
                $devis = Devis::findById($id);
                if (!$devis) {
                    Session::setFlash('error', 'Devis introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=devis');
                    exit;
                }
                $lignes = Devis::getLignes($id);
                $client = $devis['client_id'] ? Client::findById($devis['client_id']) : null;
                $GLOBALS['viewData'] = compact('devis', 'lignes', 'client');
                $pageTitle = 'Devis ' . $devis['numero_devis'];
                require __DIR__ . '/admin_devis_view.php';
                exit;

            case 'delete':
                if ($id && Security::validateId($id)) {
                    $devis = Devis::findById($id);
                    if ($devis) {
                        Devis::delete($id);
                        Activity::log(Session::get('user_id'), 'suppression_devis', "Suppression du devis {$devis['numero_devis']}");
                        Session::setFlash('success', 'Devis supprimé avec succès.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=devis');
                exit;

            case 'validate':
                if ($id && Security::validateId($id)) {
                    $devis = Devis::findById($id);
                    if ($devis) {
                        Devis::update($id, ['statut' => 'accepte']);
                        Activity::log(Session::get('user_id'), 'validation_devis', "Validation du devis {$devis['numero_devis']}");
                        Session::setFlash('success', 'Devis validé avec succès.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=devis');
                exit;

            case 'refuse':
                if ($id && Security::validateId($id)) {
                    $devis = Devis::findById($id);
                    if ($devis) {
                        Devis::update($id, ['statut' => 'refuse']);
                        Activity::log(Session::get('user_id'), 'refus_devis', "Refus du devis {$devis['numero_devis']}");
                        Session::setFlash('success', 'Devis refusé.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=devis');
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=devis');
                exit;
        }
    }
}
