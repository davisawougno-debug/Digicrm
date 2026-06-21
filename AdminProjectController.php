<?php

require_once MODELS_PATH . '/Project.php';
require_once MODELS_PATH . '/Task.php';
require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';

class ProjectController
{
    public static function projets(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':
                $projectList = Project::getAll();
                $GLOBALS['viewData'] = compact('projectList');
                $pageTitle = 'Projets';
                require __DIR__ . '/admin_projets.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $nomProjet = trim($_POST['nom_projet'] ?? '');
                        $description = trim($_POST['description'] ?? '');
                        $clientId = $_POST['client_id'] ?? null;
                        $contractId = $_POST['contract_id'] ?? null;
                        $chefProjetId = $_POST['chef_projet_id'] ?? null;
                        $dateDebut = $_POST['date_debut'] ?? null;
                        $dateFin = $_POST['date_fin'] ?? null;
                        $budget = (float)($_POST['budget'] ?? 0);
                        $statut = $_POST['statut'] ?? 'en_attente';

                        Validation::reset();
                        Validation::required('nom_projet', 'Nom du projet', $nomProjet);

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $projectId = Project::create([
                                'nom_projet'     => $nomProjet,
                                'description'    => $description,
                                'client_id'      => $clientId ?: null,
                                'contract_id'    => $contractId ?: null,
                                'chef_projet_id' => $chefProjetId ?: null,
                                'date_debut'     => $dateDebut,
                                'date_fin'       => $dateFin,
                                'budget'         => $budget,
                                'progression'    => 0,
                                'statut'         => $statut,
                            ]);
                            if ($projectId) {
                                Activity::log(Session::get('user_id'), 'creation_projet', "Création du projet {$nomProjet}");
                                Session::setFlash('success', 'Projet créé avec succès.');
                                header('Location: ' . BASE_URL . '/admin_index.php?module=projets');
                                exit;
                            } else {
                                Session::setFlash('error', 'Une erreur est survenue lors de la création.');
                            }
                        }
                    }
                }
                $clients = Client::getAll();
                $chefs = User::getAll();
                $chefs = array_filter($chefs, function ($u) {
                    return $u['role'] === ROLE_CHEF_PROJET || $u['role'] === ROLE_ADMIN;
                });
                $GLOBALS['viewData'] = compact('clients', 'chefs');
                $pageTitle = 'Nouveau projet';
                require __DIR__ . '/admin_projets_form.php';
                exit;

            case 'edit':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Projet invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=projets');
                    exit;
                }
                $project = Project::findById($id);
                if (!$project) {
                    Session::setFlash('error', 'Projet introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=projets');
                    exit;
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $nomProjet = trim($_POST['nom_projet'] ?? '');
                        $description = trim($_POST['description'] ?? '');
                        $clientId = $_POST['client_id'] ?? null;
                        $contractId = $_POST['contract_id'] ?? null;
                        $chefProjetId = $_POST['chef_projet_id'] ?? null;
                        $dateDebut = $_POST['date_debut'] ?? null;
                        $dateFin = $_POST['date_fin'] ?? null;
                        $budget = (float)($_POST['budget'] ?? 0);
                        $progression = (int)($_POST['progression'] ?? 0);
                        $statut = $_POST['statut'] ?? 'en_attente';

                        Validation::reset();
                        Validation::required('nom_projet', 'Nom du projet', $nomProjet);

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            Project::update($id, [
                                'nom_projet'     => $nomProjet,
                                'description'    => $description,
                                'client_id'      => $clientId ?: null,
                                'contract_id'    => $contractId ?: null,
                                'chef_projet_id' => $chefProjetId ?: null,
                                'date_debut'     => $dateDebut,
                                'date_fin'       => $dateFin,
                                'budget'         => $budget,
                                'progression'    => $progression,
                                'statut'         => $statut,
                            ]);
                            Activity::log(Session::get('user_id'), 'modification_projet', "Modification du projet {$nomProjet}");
                            Session::setFlash('success', 'Projet mis à jour avec succès.');
                            header('Location: ' . BASE_URL . '/admin_index.php?module=projets');
                            exit;
                        }
                    }
                }
                $clients = Client::getAll();
                $chefs = User::getAll();
                $chefs = array_filter($chefs, function ($u) {
                    return $u['role'] === ROLE_CHEF_PROJET || $u['role'] === ROLE_ADMIN;
                });
                $GLOBALS['viewData'] = compact('project', 'clients', 'chefs');
                $pageTitle = 'Modifier projet';
                require __DIR__ . '/admin_projets_form.php';
                exit;

            case 'view':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Projet invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=projets');
                    exit;
                }
                $project = Project::findById($id);
                if (!$project) {
                    Session::setFlash('error', 'Projet introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=projets');
                    exit;
                }
                $taches = Task::getByProject($id);
                $chef = $project['chef_projet_id'] ? User::findById($project['chef_projet_id']) : null;
                $GLOBALS['viewData'] = compact('project', 'taches', 'chef');
                $pageTitle = $project['nom_projet'];
                require __DIR__ . '/admin_projets_view.php';
                exit;

            case 'delete':
                if ($id && Security::validateId($id)) {
                    $project = Project::findById($id);
                    if ($project) {
                        Project::delete($id);
                        Activity::log(Session::get('user_id'), 'suppression_projet', "Suppression du projet {$project['nom_projet']}");
                        Session::setFlash('success', 'Projet supprimé avec succès.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=projets');
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=projets');
                exit;
        }
    }
}
