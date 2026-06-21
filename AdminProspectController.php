<?php

require_once MODELS_PATH . '/Prospect.php';
require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';

class ProspectController
{
    public static function prospects(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':
                $prospects = Prospect::getAll();
                $commerciaux = User::getAll();
                $commerciaux = array_filter($commerciaux, function ($u) {
                    return $u['role'] === ROLE_COMMERCIAL || $u['role'] === ROLE_ADMIN;
                });
                $GLOBALS['viewData'] = compact('prospects', 'commerciaux');
                $pageTitle = 'Prospects';
                require __DIR__ . '/admin_prospects.php';
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
                        $source = $_POST['source'] ?? null;
                        $statut = $_POST['statut'] ?? 'nouveau';
                        $assignedTo = $_POST['assigned_to'] ?? null;
                        if ($assignedTo === '') $assignedTo = null;
                        $besoin = trim($_POST['besoin'] ?? '');

                        Validation::reset();
                        Validation::required('nom', 'Nom', $nom);
                        Validation::required('prenom', 'Prénom', $prenom);
                        Validation::required('email', 'Email', $email);
                        Validation::email('email', $email);
                        if (!empty($telephone)) {
                            Validation::telephone('telephone', $telephone);
                        }
                        if ($assignedTo && !User::findById((int)$assignedTo)) {
                            Validation::addError('assigned_to', 'Le commercial sélectionné n\'existe pas.');
                        }

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $prospectId = Prospect::create([
                                'nom'           => $nom,
                                'prenom'        => $prenom,
                                'email'         => $email,
                                'telephone'     => $telephone,
                                'entreprise'    => $entreprise,
                                'source'        => $source,
                                'statut'        => $statut,
                                'assigned_to'   => $assignedTo,
                                'besoin'        => $besoin,
                            ]);
                            if ($prospectId) {
                                Activity::log(Session::get('user_id'), 'creation_prospect', "Création du prospect {$prenom} {$nom}");
                                Session::setFlash('success', 'Prospect créé avec succès.');
                                header('Location: ' . BASE_URL . '/admin_index.php?module=prospects');
                                exit;
                            } else {
                                Session::setFlash('error', 'Une erreur est survenue lors de la création.');
                            }
                        }
                    }
                }
                $commerciaux = User::getAll();
                $commerciaux = array_filter($commerciaux, function ($u) {
                    return $u['role'] === ROLE_COMMERCIAL || $u['role'] === ROLE_ADMIN;
                });
                $GLOBALS['viewData'] = compact('commerciaux');
                $pageTitle = 'Nouveau prospect';
                require __DIR__ . '/admin_prospects.php';
                exit;

            case 'edit':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Prospect invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=prospects');
                    exit;
                }
                $prospect = Prospect::findById($id);
                if (!$prospect) {
                    Session::setFlash('error', 'Prospect introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=prospects');
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
                        $source = $_POST['source'] ?? null;
                        $statut = $_POST['statut'] ?? 'nouveau';
                        $assignedTo = $_POST['assigned_to'] ?? null;
                        if ($assignedTo === '') $assignedTo = null;
                        $besoin = trim($_POST['besoin'] ?? '');

                        Validation::reset();
                        Validation::required('nom', 'Nom', $nom);
                        Validation::required('prenom', 'Prénom', $prenom);
                        Validation::required('email', 'Email', $email);
                        Validation::email('email', $email);
                        if (!empty($telephone)) {
                            Validation::telephone('telephone', $telephone);
                        }
                        if ($assignedTo && !User::findById((int)$assignedTo)) {
                            Validation::addError('assigned_to', 'Le commercial sélectionné n\'existe pas.');
                        }

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            Prospect::update($id, [
                                'nom'           => $nom,
                                'prenom'        => $prenom,
                                'email'         => $email,
                                'telephone'     => $telephone,
                                'entreprise'    => $entreprise,
                                'source'        => $source,
                                'statut'        => $statut,
                                'assigned_to'   => $assignedTo,
                                'besoin'        => $besoin,
                            ]);
                            Activity::log(Session::get('user_id'), 'modification_prospect', "Modification du prospect {$prenom} {$nom}");
                            Session::setFlash('success', 'Prospect mis à jour avec succès.');
                            header('Location: ' . BASE_URL . '/admin_index.php?module=prospects');
                            exit;
                        }
                    }
                }
                $commerciaux = User::getAll();
                $commerciaux = array_filter($commerciaux, function ($u) {
                    return $u['role'] === ROLE_COMMERCIAL || $u['role'] === ROLE_ADMIN;
                });
                $GLOBALS['viewData'] = compact('prospect', 'commerciaux');
                $pageTitle = 'Modifier prospect';
                require __DIR__ . '/admin_prospects.php';
                exit;

            case 'delete':
                if ($id && Security::validateId($id)) {
                    $prospect = Prospect::findById($id);
                    if ($prospect) {
                        Prospect::delete($id);
                        Activity::log(Session::get('user_id'), 'suppression_prospect', "Suppression du prospect {$prospect['prenom']} {$prospect['nom']}");
                        Session::setFlash('success', 'Prospect supprimé avec succès.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=prospects');
                exit;

            case 'convert':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Prospect invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=prospects');
                    exit;
                }
                $prospect = Prospect::findById($id);
                if (!$prospect) {
                    Session::setFlash('error', 'Prospect introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=prospects');
                    exit;
                }

                $clientId = Client::create([
                    'nom'         => $prospect['nom'],
                    'prenom'      => $prospect['prenom'],
                    'email'       => $prospect['email'],
                    'telephone'   => $prospect['telephone'],
                    'entreprise'  => $prospect['entreprise'],
                    'created_from_prospect_id' => $id,
                    'statut'      => 'actif',
                ]);

                if ($clientId) {
                    Prospect::convertToClient($id);
                    Activity::log(Session::get('user_id'), 'conversion_prospect', "Conversion du prospect {$prospect['prenom']} {$prospect['nom']} en client");
                    Session::setFlash('success', "Le prospect {$prospect['prenom']} {$prospect['nom']} a été converti en client avec succès.");
                } else {
                    Session::setFlash('error', 'Une erreur est survenue lors de la conversion.');
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=prospects');
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=prospects');
                exit;
        }
    }
}
