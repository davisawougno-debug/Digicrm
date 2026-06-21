<?php

require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';

class UserController
{
    public static function users(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':
                $page = max(1, (int)($_GET['page'] ?? 1));
                $users = User::getAll($page, ITEMS_PER_PAGE);
                $total = User::countAll();
                $pages = max(1, ceil($total / ITEMS_PER_PAGE));
                $GLOBALS['viewData'] = compact('users', 'total', 'page', 'pages');
                require __DIR__ . '/admin_users_list.php';
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
                        $role = $_POST['role'] ?? ROLE_EMPLOYE;
                        $password = $_POST['password'] ?? '';
                        $roles = unserialize(ROLES_LIST);

                        Validation::reset();
                        Validation::required('nom', 'Nom', $nom);
                        Validation::required('prenom', 'Prénom', $prenom);
                        Validation::required('email', 'Email', $email);
                        Validation::email('email', $email);
                        Validation::required('password', 'Mot de passe', $password);
                        Validation::minLength('password', 'Mot de passe', $password, PASSWORD_MIN_LENGTH);
                        Validation::passwordStrength('password', $password);
                        Validation::inList('role', 'Rôle', $role, $roles);
                        if (!empty($telephone)) {
                            Validation::telephone('telephone', $telephone);
                        }
                        if (!empty($email) && User::emailExists($email)) {
                            Validation::addError('email', 'Cette adresse email est déjà utilisée.');
                        }

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $userId = User::create([
                                'nom'       => $nom,
                                'prenom'    => $prenom,
                                'email'     => $email,
                                'telephone' => $telephone,
                                'password'  => Security::hashPassword($password),
                                'role'      => $role,
                                'statut'    => 'actif',
                            ]);
                            if ($userId) {
                                Activity::log(Session::get('user_id'), 'creation_utilisateur', "Création de l'utilisateur {$prenom} {$nom} ({$email})");
                                Session::setFlash('success', 'Utilisateur créé avec succès.');
                                header('Location: ' . BASE_URL . '/admin_index.php?module=users');
                                exit;
                            } else {
                                Session::setFlash('error', 'Une erreur est survenue lors de la création.');
                            }
                        }
                    }
                }
                $pageTitle = 'Nouvel utilisateur';
                require __DIR__ . '/admin_users_form.php';
                exit;

            case 'edit':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Utilisateur invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=users');
                    exit;
                }
                $user = User::findById($id);
                if (!$user) {
                    Session::setFlash('error', 'Utilisateur introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=users');
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
                        $role = $_POST['role'] ?? $user['role'];
                        $roles = unserialize(ROLES_LIST);

                        Validation::reset();
                        Validation::required('nom', 'Nom', $nom);
                        Validation::required('prenom', 'Prénom', $prenom);
                        Validation::required('email', 'Email', $email);
                        Validation::email('email', $email);
                        Validation::inList('role', 'Rôle', $role, $roles);
                        if (!empty($telephone)) {
                            Validation::telephone('telephone', $telephone);
                        }
                        if (!empty($email) && User::emailExists($email, $id)) {
                            Validation::addError('email', 'Cette adresse email est déjà utilisée.');
                        }

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            User::update($id, [
                                'nom'       => $nom,
                                'prenom'    => $prenom,
                                'email'     => $email,
                                'telephone' => $telephone,
                                'role'      => $role,
                            ]);
                            Activity::log(Session::get('user_id'), 'modification_utilisateur', "Modification de l'utilisateur {$prenom} {$nom}");
                            Session::setFlash('success', 'Utilisateur mis à jour avec succès.');
                            header('Location: ' . BASE_URL . '/admin_index.php?module=users');
                            exit;
                        }
                    }
                }
                $GLOBALS['viewData'] = compact('user');
                $pageTitle = 'Modifier utilisateur';
                require __DIR__ . '/admin_users_form.php';
                exit;

            case 'toggle-status':
                if ($id && Security::validateId($id)) {
                    $user = User::findById($id);
                    if (!$user) {
                        Session::setFlash('error', 'Utilisateur introuvable.');
                    } elseif ((int)$user['id'] === Session::get('user_id')) {
                        Session::setFlash('error', 'Vous ne pouvez pas désactiver votre propre compte.');
                    } else {
                        User::toggleStatus($id);
                        $newStatus = $user['statut'] === 'actif' ? 'inactif' : 'actif';
                        $statusText = $newStatus === 'actif' ? 'activé' : 'désactivé';
                        Activity::log(Session::get('user_id'), 'statut_utilisateur', "Compte de {$user['prenom']} {$user['nom']} {$statusText}");
                        Session::setFlash('success', "Le compte de {$user['prenom']} {$user['nom']} a été {$statusText}.");
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=users');
                exit;

            case 'delete':
                if ($id && Security::validateId($id)) {
                    $user = User::findById($id);
                    if (!$user) {
                        Session::setFlash('error', 'Utilisateur introuvable.');
                    } elseif ((int)$user['id'] === Session::get('user_id')) {
                        Session::setFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
                    } else {
                        User::delete($id);
                        Activity::log(Session::get('user_id'), 'suppression_utilisateur', "Suppression du compte de {$user['prenom']} {$user['nom']} ({$user['email']})");
                        Session::setFlash('success', "Le compte de {$user['prenom']} {$user['nom']} a été supprimé.");
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=users');
                exit;

            case 'reset-password':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Utilisateur invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=users');
                    exit;
                }
                $user = User::findById($id);
                if (!$user) {
                    Session::setFlash('error', 'Utilisateur introuvable.');
                } else {
                    $newPassword = bin2hex(random_bytes(4));
                    User::updatePassword($id, Security::hashPassword($newPassword));
                    Activity::log(Session::get('user_id'), 'reset_password', "Réinitialisation du mot de passe de {$user['prenom']} {$user['nom']}");
                    Session::setFlash('success', "Le mot de passe de {$user['prenom']} {$user['nom']} a été réinitialisé. Nouveau mot de passe : {$newPassword}");
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=users&action=view&id=' . $id);
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=users');
                exit;
        }
    }
}
