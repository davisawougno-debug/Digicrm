<?php

require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/Contract.php';
require_once MODELS_PATH . '/Project.php';
require_once MODELS_PATH . '/Invoice.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';

class ClientController
{
    public static function clients(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':
                $clients = Client::getAll();
                $GLOBALS['viewData'] = compact('clients');
                $pageTitle = 'Clients';
                require __DIR__ . '/admin_clients.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $nom = trim($_POST['nom'] ?? '');
                        $prenom = trim($_POST['prenom'] ?? '');
                        $email = trim($_POST['email'] ?? '');
                        $telephone = trim($_POST['telephone'] ?? '');
                        $entreprise = trim($_POST['entreprise'] ?? '');
                        $adresse = trim($_POST['adresse'] ?? '');
                        $secteurActivite = $_POST['secteur_activite'] ?? null;
                        $statut = $_POST['statut'] ?? 'actif';

                        Validation::reset();
                        Validation::required('nom', 'Nom', $nom);
                        Validation::required('prenom', 'Prénom', $prenom);
                        Validation::required('email', 'Email', $email);
                        Validation::email('email', $email);
                        if (!empty($telephone)) {
                            Validation::telephone('telephone', $telephone);
                        }

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $clientId = Client::create([
                                'nom'              => $nom,
                                'prenom'           => $prenom,
                                'email'            => $email,
                                'telephone'        => $telephone,
                                'entreprise'       => $entreprise,
                                'adresse'          => $adresse,
                                'secteur_activite' => $secteurActivite,
                                'statut'           => $statut,
                            ]);
                            if ($clientId) {
                                Activity::log(Session::get('user_id'), 'creation_client', "Création du client {$prenom} {$nom}");
                                Session::setFlash('success', 'Client créé avec succès.');
                                header('Location: ' . BASE_URL . '/admin_index.php?module=clients');
                                exit;
                            } else {
                                Session::setFlash('error', 'Une erreur est survenue lors de la création.');
                            }
                        }
                    }
                }
                $pageTitle = 'Nouveau client';
                require __DIR__ . '/admin_clients.php';
                exit;

            case 'edit':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Client invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=clients');
                    exit;
                }
                $client = Client::findById($id);
                if (!$client) {
                    Session::setFlash('error', 'Client introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=clients');
                    exit;
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $nom = trim($_POST['nom'] ?? '');
                        $prenom = trim($_POST['prenom'] ?? '');
                        $email = trim($_POST['email'] ?? '');
                        $telephone = trim($_POST['telephone'] ?? '');
                        $entreprise = trim($_POST['entreprise'] ?? '');
                        $adresse = trim($_POST['adresse'] ?? '');
                        $secteurActivite = $_POST['secteur_activite'] ?? null;
                        $statut = $_POST['statut'] ?? 'actif';

                        Validation::reset();
                        Validation::required('nom', 'Nom', $nom);
                        Validation::required('prenom', 'Prénom', $prenom);
                        Validation::required('email', 'Email', $email);
                        Validation::email('email', $email);
                        if (!empty($telephone)) {
                            Validation::telephone('telephone', $telephone);
                        }

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            Client::update($id, [
                                'nom'              => $nom,
                                'prenom'           => $prenom,
                                'email'            => $email,
                                'telephone'        => $telephone,
                                'entreprise'       => $entreprise,
                                'adresse'          => $adresse,
                                'secteur_activite' => $secteurActivite,
                                'statut'           => $statut,
                            ]);
                            Activity::log(Session::get('user_id'), 'modification_client', "Modification du client {$prenom} {$nom}");
                            Session::setFlash('success', 'Client mis à jour avec succès.');
                            header('Location: ' . BASE_URL . '/admin_index.php?module=clients');
                            exit;
                        }
                    }
                }
                $GLOBALS['viewData'] = compact('client');
                $pageTitle = 'Modifier client';
                require __DIR__ . '/admin_clients.php';
                exit;

            case 'view':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Client invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=clients');
                    exit;
                }
                $client = Client::findById($id);
                if (!$client) {
                    Session::setFlash('error', 'Client introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=clients');
                    exit;
                }
                $contracts = Contract::getByClient($id);
                $projets   = Project::getByClient($id);
                $invoices  = Invoice::getByClient($id);
                $GLOBALS['viewData'] = compact('client', 'contracts', 'projets', 'invoices');
                $pageTitle = $client['entreprise'] ?: $client['prenom'] . ' ' . $client['nom'];
                require __DIR__ . '/admin_clients_view.php';
                exit;

            case 'delete':
                if ($id && Security::validateId($id)) {
                    $client = Client::findById($id);
                    if ($client) {
                        Client::delete($id);
                        Activity::log(Session::get('user_id'), 'suppression_client', "Suppression du client {$client['prenom']} {$client['nom']}");
                        Session::setFlash('success', 'Client supprimé avec succès.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=clients');
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=clients');
                exit;
        }
    }
}
