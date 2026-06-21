<?php

require_once MODELS_PATH . '/Task.php';
require_once MODELS_PATH . '/Project.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';

class TaskController
{
    public static function taches(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':
                $taskList = Task::getAll();
                $projets = Project::getAll();
                $users = User::getAll();
                $GLOBALS['viewData'] = compact('taskList', 'projets', 'users');
                $pageTitle = 'Tâches';
                require __DIR__ . '/admin_taches.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $projectId = (int)($_POST['project_id'] ?? 0);
                        $titre = trim($_POST['titre'] ?? '');
                        $description = trim($_POST['description'] ?? '');
                        $assignedTo = $_POST['assigned_to'] ?? null;
                        $priorite = $_POST['priorite'] ?? 'moyenne';
                        $dateEcheance = $_POST['date_echeance'] ?? null;
                        $dateDebut = $_POST['date_debut'] ?? null;
                        $statut = $_POST['statut'] ?? 'a_faire';

                        Validation::reset();
                        Validation::required('project_id', 'Projet', $projectId);
                        Validation::required('titre', 'Titre', $titre);

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $taskId = Task::create([
                                'project_id'    => $projectId,
                                'titre'         => $titre,
                                'description'   => $description,
                                'assigned_to'   => $assignedTo ?: null,
                                'priorite'      => $priorite,
                                'statut'        => $statut,
                                'date_echeance' => $dateEcheance,
                                'date_debut'    => $dateDebut,
                            ]);
                            if ($taskId) {
                                Activity::log(Session::get('user_id'), 'creation_tache', "Création de la tâche {$titre}");
                                Session::setFlash('success', 'Tâche créée avec succès.');
                                header('Location: ' . BASE_URL . '/admin_index.php?module=taches');
                                exit;
                            } else {
                                Session::setFlash('error', 'Une erreur est survenue lors de la création.');
                            }
                        }
                    }
                }
                $projets = Project::getAll();
                $users = User::getAll();
                $GLOBALS['viewData'] = compact('projets', 'users');
                $pageTitle = 'Nouvelle tâche';
                require __DIR__ . '/admin_taches.php';
                exit;

            case 'edit':
                if (!$id || !Security::validateId($id)) {
                    Session::setFlash('error', 'Tâche invalide.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=taches');
                    exit;
                }
                $task = Task::findById($id);
                if (!$task) {
                    Session::setFlash('error', 'Tâche introuvable.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=taches');
                    exit;
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $projectId = (int)($_POST['project_id'] ?? 0);
                        $titre = trim($_POST['titre'] ?? '');
                        $description = trim($_POST['description'] ?? '');
                        $assignedTo = $_POST['assigned_to'] ?? null;
                        $priorite = $_POST['priorite'] ?? 'moyenne';
                        $dateEcheance = $_POST['date_echeance'] ?? null;
                        $dateDebut = $_POST['date_debut'] ?? null;
                        $statut = $_POST['statut'] ?? 'a_faire';

                        Validation::reset();
                        Validation::required('project_id', 'Projet', $projectId);
                        Validation::required('titre', 'Titre', $titre);

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            Task::update($id, [
                                'project_id'    => $projectId,
                                'titre'         => $titre,
                                'description'   => $description,
                                'assigned_to'   => $assignedTo ?: null,
                                'priorite'      => $priorite,
                                'statut'        => $statut,
                                'date_echeance' => $dateEcheance,
                                'date_debut'    => $dateDebut,
                            ]);
                            Activity::log(Session::get('user_id'), 'modification_tache', "Modification de la tâche {$titre}");
                            Session::setFlash('success', 'Tâche mise à jour avec succès.');
                            header('Location: ' . BASE_URL . '/admin_index.php?module=taches');
                            exit;
                        }
                    }
                }
                $projets = Project::getAll();
                $users = User::getAll();
                $GLOBALS['viewData'] = compact('task', 'projets', 'users');
                $pageTitle = 'Modifier tâche';
                require __DIR__ . '/admin_taches.php';
                exit;

            case 'delete':
                if ($id && Security::validateId($id)) {
                    $task = Task::findById($id);
                    if ($task) {
                        Task::delete($id);
                        Activity::log(Session::get('user_id'), 'suppression_tache', "Suppression de la tâche {$task['titre']}");
                        Session::setFlash('success', 'Tâche supprimée avec succès.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=taches');
                exit;

            case 'complete':
                if ($id && Security::validateId($id)) {
                    $task = Task::findById($id);
                    if ($task) {
                        Task::update($id, ['statut' => 'termine']);
                        Activity::log(Session::get('user_id'), 'achevement_tache', "Tâche terminée : {$task['titre']}");
                        Session::setFlash('success', 'Tâche marquée comme terminée.');
                    }
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=taches');
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=taches');
                exit;
        }
    }
}
