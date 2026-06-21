<?php

require_once MODELS_PATH . '/Service.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';

class ServiceController
{
    public static function services(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':
                $services = Service::getAll();
                $GLOBALS['viewData'] = compact('services');
                $pageTitle = 'Services';
                require __DIR__ . '/admin_services.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $nom = trim($_POST['nom'] ?? '');
                        $description = trim($_POST['description'] ?? '');
                        $prix = (float)($_POST['prix'] ?? 0);
                        $duree_estimee = $_POST['duree_estimee'] ?? null;

                        Validation::reset();
                        Validation::required('nom', 'Nom du service', $nom);
                        if ($prix < 0) {
                            Validation::addError('prix', 'Le prix doit être un nombre positif.');
                        }

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $serviceId = Service::create([
                                'nom'            => $nom,
                                'description'    => $description,
                                'prix'           => $prix,
                                'duree_estimee'  => $duree_estimee,
                            ]);
                            if ($serviceId) {
                                Activity::log(Session::get('user_id'), 'creation_service', "Création du service {$nom}");
                                Session::setFlash('success', 'Service créé avec succès.');
                                header('Location: ' . BASE_URL . '/admin_index.php?module=services');
                                exit;
                            } else {
                                Session::setFlash('error', 'Une erreur est survenue lors de la création.');
                            }
                        }
                    }
                }
                $pageTitle = 'Nouveau service';
                require __DIR__ . '/admin_services.php';
                exit;

            case 'edit':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Service invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=services');
                    exit;
                }
                $service = Service::findById($id);
                if (!$service) {
                    Session::setFlash('error', 'Service introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=services');
                    exit;
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $nom = trim($_POST['nom'] ?? '');
                        $description = trim($_POST['description'] ?? '');
                        $prix = (float)($_POST['prix'] ?? 0);
                        $duree_estimee = $_POST['duree_estimee'] ?? null;

                        Validation::reset();
                        Validation::required('nom', 'Nom du service', $nom);
                        if ($prix < 0) {
                            Validation::addError('prix', 'Le prix doit être un nombre positif.');
                        }

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            Service::update($id, [
                                'nom'            => $nom,
                                'description'    => $description,
                                'prix'           => $prix,
                                'duree_estimee'  => $duree_estimee,
                            ]);
                            Activity::log(Session::get('user_id'), 'modification_service', "Modification du service {$nom}");
                            Session::setFlash('success', 'Service mis à jour avec succès.');
                            header('Location: ' . BASE_URL . '/admin_index.php?module=services');
                            exit;
                        }
                    }
                }
                $GLOBALS['viewData'] = compact('service');
                $pageTitle = 'Modifier service';
                require __DIR__ . '/admin_services.php';
                exit;

            case 'delete':
                if ($id && Security::validateId($id)) {
                    $service = Service::findById($id);
                    if ($service) {
                        Service::delete($id);
                        Activity::log(Session::get('user_id'), 'suppression_service', "Suppression du service {$service['nom']}");
                        Session::setFlash('success', 'Service supprimé avec succès.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=services');
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=services');
                exit;
        }
    }
}
