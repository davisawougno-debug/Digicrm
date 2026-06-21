<?php

require_once MODELS_PATH . '/Project.php';
require_once MODELS_PATH . '/Task.php';
require_once MODELS_PATH . '/Deliverable.php';
require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/Contract.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Notification.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';

class ChefProjetDashboardController
{
    private static function getPdo(): PDO
    {
        return getPDO();
    }

    public static function dashboard(): void
    {
        $userId = Session::get('user_id');
        $pdo = self::getPdo();

        $projects = Project::getAll();
        $projetsActifs = count(array_filter($projects, fn($p) => $p['statut'] === 'en_cours'));
        $projetsTermines = count(array_filter($projects, fn($p) => $p['statut'] === 'termine'));
        $totalProjets = count($projects);

        $tasks = Task::getAll();
        $totalTaches = count($tasks);
        $tachesTerminees = count(array_filter($tasks, fn($t) => $t['statut'] === 'termine'));
        $tachesEnRetard = count(Task::getEnRetard());

        $deliverables = Deliverable::getAll();
        $livrablesEnAttente = count(array_filter($deliverables, fn($d) => $d['statut'] === 'soumis'));

        $employesActifs = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'employe' AND statut = 'actif'")->fetchColumn();

        $progressionMoyenne = $totalProjets > 0
            ? round(array_sum(array_column($projects, 'progression')) / $totalProjets)
            : 0;

        $stats = compact(
            'projetsActifs', 'projetsTermines', 'totalProjets',
            'totalTaches', 'tachesTerminees', 'tachesEnRetard',
            'livrablesEnAttente', 'employesActifs', 'progressionMoyenne'
        );

        $stats['projetsRecents'] = Project::getRecent(5);
        $stats['tachesEnCours'] = Task::getByStatut('en_cours');
        $stats['tachesParStatut'] = Task::countByStatut();
        $stats['tachesParPriorite'] = Task::countByPriorite();
        $stats['progressionProjets'] = $pdo->query("SELECT nom_projet, progression, statut FROM projects ORDER BY created_at DESC LIMIT 10")->fetchAll();
        $stats['recentActivities'] = Activity::getAll(10);
        $stats['deliverablesRecents'] = Deliverable::getRecent(5);
        $stats['notifCount'] = Notification::countNonLu($userId);
        $stats['monthlyTasks'] = self::getMonthlyCount('tasks', 'created_at');
        $stats['membresEquipe'] = $pdo->query("SELECT u.*, COUNT(t.id) as taches_en_cours FROM users u LEFT JOIN tasks t ON t.assigned_to = u.id AND t.statut = 'en_cours' WHERE u.role = 'employe' OR u.role = 'chef_projet' GROUP BY u.id ORDER BY u.nom")->fetchAll();

        $pageTitle = 'Dashboard Chef de Projet';
        $GLOBALS['viewData'] = $stats;
        require __DIR__ . '/chef_projet_dashboard.php';
        exit;
    }

    public static function projets(string $action, ?int $id = null): void
    {
        $pdo = self::getPdo();

        switch ($action) {
            case 'list':
                $projets = $pdo->query("SELECT p.*, c.entreprise as client, u.prenom, u.nom FROM projects p LEFT JOIN clients c ON p.client_id = c.id LEFT JOIN users u ON p.chef_projet_id = u.id ORDER BY p.created_at DESC")->fetchAll();
                $GLOBALS['viewData'] = compact('projets');
                $pageTitle = 'Projets';
                require __DIR__ . '/chef_projet_projets.php';
                exit;

            case 'view':
                $projet = Project::findById($id ?? 0);
                if (!$projet) { Session::setFlash('error', 'Projet introuvable.'); header('Location: ' . BASE_URL . '/chef_projet_index.php?module=projets'); exit; }
                $taches = Task::getByProject($id);
                $livrables = Deliverable::getByProject($id);
                $membres = $pdo->prepare("SELECT u.* FROM users u WHERE u.role = 'employe' OR u.role = 'chef_projet' ORDER BY u.nom");
                $membres->execute();
                $allMembres = $membres->fetchAll();
                $pageTitle = $projet['nom_projet'];
                $GLOBALS['viewData'] = compact('projet', 'taches', 'livrables', 'allMembres');
                require __DIR__ . '/chef_projet_projets.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { Session::setFlash('error', 'Erreur de validation.'); }
                    else {
                        $projetId = Project::create([
                            'nom_projet'     => trim($_POST['nom_projet'] ?? ''),
                            'description'    => trim($_POST['description'] ?? ''),
                            'client_id'      => $_POST['client_id'] ?? null,
                            'chef_projet_id' => Session::get('user_id'),
                            'date_debut'     => $_POST['date_debut'] ?? null,
                            'date_fin'       => $_POST['date_fin'] ?? null,
                            'budget'         => $_POST['budget'] ?? 0,
                            'statut'         => 'en_attente',
                            'progression'    => 0,
                        ]);
                        if ($projetId) {
                            Activity::log(Session::get('user_id'), 'creation', "Projet créé : " . ($_POST['nom_projet'] ?? ''), 'projets');
                            Session::setFlash('success', 'Projet créé avec succès.');
                        } else { Session::setFlash('error', 'Erreur lors de la création.'); }
                        header('Location: ' . BASE_URL . '/chef_projet_index.php?module=projets');
                        exit;
                    }
                }
                $clients = Client::getAll();
                $pageTitle = 'Nouveau projet';
                $GLOBALS['viewData'] = compact('clients');
                require __DIR__ . '/chef_projet_projets.php';
                exit;

            default:
                $projets = $pdo->query("SELECT p.*, c.entreprise as client FROM projects p LEFT JOIN clients c ON p.client_id = c.id ORDER BY p.created_at DESC")->fetchAll();
                $GLOBALS['viewData'] = compact('projets');
                $pageTitle = 'Projets';
                require __DIR__ . '/chef_projet_projets.php';
                exit;
        }
    }

    public static function taches(string $action, ?int $id = null): void
    {
        $pdo = self::getPdo();

        switch ($action) {
            case 'list':
                $taches = $pdo->query("SELECT t.*, p.nom_projet, u.prenom, u.nom FROM tasks t LEFT JOIN projects p ON t.project_id = p.id LEFT JOIN users u ON t.assigned_to = u.id ORDER BY t.created_at DESC")->fetchAll();
                $pageTitle = 'Tâches';
                $GLOBALS['viewData'] = compact('taches');
                require __DIR__ . '/chef_projet_taches.php';
                exit;

            case 'kanban':
                $aFaire = $pdo->query("SELECT t.*, p.nom_projet, u.prenom, u.nom FROM tasks t LEFT JOIN projects p ON t.project_id = p.id LEFT JOIN users u ON t.assigned_to = u.id WHERE t.statut = 'a_faire' ORDER BY t.priorite DESC, t.created_at ASC")->fetchAll();
                $enCours = $pdo->query("SELECT t.*, p.nom_projet, u.prenom, u.nom FROM tasks t LEFT JOIN projects p ON t.project_id = p.id LEFT JOIN users u ON t.assigned_to = u.id WHERE t.statut = 'en_cours' ORDER BY t.date_fin ASC")->fetchAll();
                $termine = $pdo->query("SELECT t.*, p.nom_projet, u.prenom, u.nom FROM tasks t LEFT JOIN projects p ON t.project_id = p.id LEFT JOIN users u ON t.assigned_to = u.id WHERE t.statut = 'termine' ORDER BY t.created_at DESC LIMIT 20")->fetchAll();
                $pageTitle = 'Tableau Kanban';
                $GLOBALS['viewData'] = compact('aFaire', 'enCours', 'termine');
                require __DIR__ . '/chef_projet_taches.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { Session::setFlash('error', 'Erreur de validation.'); }
                    else {
                        $taskId = Task::create([
                            'project_id'  => $_POST['project_id'],
                            'titre'       => trim($_POST['titre'] ?? ''),
                            'description' => trim($_POST['description'] ?? ''),
                            'assigned_to' => $_POST['assigned_to'] ?? null,
                            'priorite'    => $_POST['priorite'] ?? 'moyenne',
                            'statut'      => 'a_faire',
                            'date_debut'  => $_POST['date_debut'] ?? null,
                            'date_fin'    => $_POST['date_fin'] ?? null,
                        ]);
                        if ($taskId) {
                            Activity::log(Session::get('user_id'), 'creation', "Tâche créée : " . ($_POST['titre'] ?? ''), 'taches');
                            Session::setFlash('success', 'Tâche créée avec succès.');
                        } else { Session::setFlash('error', 'Erreur lors de la création.'); }
                        header('Location: ' . BASE_URL . '/chef_projet_index.php?module=taches');
                        exit;
                    }
                }
                $projets = Project::getAll();
                $membres = $pdo->query("SELECT * FROM users WHERE statut = 'actif' ORDER BY prenom")->fetchAll();
                $pageTitle = 'Nouvelle tâche';
                $GLOBALS['viewData'] = compact('projets', 'membres');
                require __DIR__ . '/chef_projet_taches.php';
                exit;

            case 'update-status':
                if ($id && isset($_GET['statut'])) {
                    Task::update($id, ['statut' => $_GET['statut']]);
                    Activity::log(Session::get('user_id'), 'modification', "Statut tâche #{$id} → {$_GET['statut']}", 'taches');
                }
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/chef_projet_index.php?module=taches'));
                exit;

            default:
                $taches = $pdo->query("SELECT t.*, p.nom_projet, u.prenom, u.nom FROM tasks t LEFT JOIN projects p ON t.project_id = p.id LEFT JOIN users u ON t.assigned_to = u.id ORDER BY t.created_at DESC")->fetchAll();
                $pageTitle = 'Tâches';
                $GLOBALS['viewData'] = compact('taches');
                require __DIR__ . '/chef_projet_taches.php';
                exit;
        }
    }

    public static function equipes(string $action): void
    {
        $pdo = self::getPdo();

        $membres = $pdo->query("
            SELECT u.*,
                (SELECT COUNT(*) FROM tasks WHERE assigned_to = u.id AND statut = 'en_cours') as taches_en_cours,
                (SELECT COUNT(*) FROM tasks WHERE assigned_to = u.id AND statut = 'a_faire') as taches_a_faire,
                (SELECT COUNT(*) FROM tasks WHERE assigned_to = u.id AND statut = 'termine') as taches_terminees
            FROM users u
            WHERE u.statut = 'actif'
            ORDER BY u.role, u.prenom
        ")->fetchAll();

        $pageTitle = 'Équipes';
        $GLOBALS['viewData'] = compact('membres');
        require __DIR__ . '/chef_projet_equipes.php';
        exit;
    }

    public static function livrables(string $action, ?int $id = null): void
    {
        $pdo = self::getPdo();

        switch ($action) {
            case 'list':
                $livrables = $pdo->query("SELECT d.*, p.nom_projet FROM deliverables d LEFT JOIN projects p ON d.project_id = p.id ORDER BY d.created_at DESC")->fetchAll();
                $pageTitle = 'Livrables';
                $GLOBALS['viewData'] = compact('livrables');
                require __DIR__ . '/chef_projet_livrables.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { Session::setFlash('error', 'Erreur de validation.'); }
                    else {
                        Deliverable::create([
                            'project_id'  => $_POST['project_id'],
                            'titre'       => trim($_POST['titre'] ?? ''),
                            'description' => trim($_POST['description'] ?? ''),
                            'statut'      => 'soumis',
                        ]);
                        Session::setFlash('success', 'Livrable ajouté.');
                        header('Location: ' . BASE_URL . '/chef_projet_index.php?module=livrables');
                        exit;
                    }
                }
                $projets = Project::getAll();
                $pageTitle = 'Nouveau livrable';
                $GLOBALS['viewData'] = compact('projets');
                require __DIR__ . '/chef_projet_livrables.php';
                exit;

            case 'update-status':
                if ($id && isset($_GET['statut'])) {
                    Deliverable::update($id, ['statut' => $_GET['statut']]);
                }
                header('Location: ' . BASE_URL . '/chef_projet_index.php?module=livrables');
                exit;

            default:
                $livrables = $pdo->query("SELECT d.*, p.nom_projet FROM deliverables d LEFT JOIN projects p ON d.project_id = p.id ORDER BY d.created_at DESC")->fetchAll();
                $pageTitle = 'Livrables';
                $GLOBALS['viewData'] = compact('livrables');
                require __DIR__ . '/chef_projet_livrables.php';
                exit;
        }
    }

    public static function calendrier(string $action): void
    {
        $pdo = self::getPdo();

        $echeances = $pdo->query("SELECT id, titre as title, date_fin as start, 'task' as type FROM tasks WHERE date_fin IS NOT NULL UNION ALL SELECT id, nom_projet as title, end_date as start, 'project' as type FROM projects WHERE end_date IS NOT NULL ORDER BY start")->fetchAll();
        $projets = Project::getAll();
        $taches = Task::getAll();

        $pageTitle = 'Calendrier';
        $GLOBALS['viewData'] = compact('echeances', 'projets', 'taches');
        require __DIR__ . '/chef_projet_calendrier.php';
        exit;
    }

    public static function clients(string $action, ?int $id = null): void
    {
        $clients = Client::getAll();
        $pageTitle = 'Clients';
        $GLOBALS['viewData'] = compact('clients');
        require __DIR__ . '/chef_projet_clients.php';
        exit;
    }

    public static function contrats(string $action, ?int $id = null): void
    {
        $contrats = Contract::getAll();
        $pageTitle = 'Contrats';
        $GLOBALS['viewData'] = compact('contrats');
        require __DIR__ . '/chef_projet_contrats.php';
        exit;
    }

    public static function notifications(string $action, ?int $id = null): void
    {
        $userId = Session::get('user_id');
        if ($action === 'mark-all-read') { Notification::markAllAsLu($userId); Session::setFlash('success', 'Toutes lues.'); header('Location: ' . BASE_URL . '/chef_projet_index.php?module=notifications'); exit; }
        if ($action === 'mark-read' && $id) { Notification::markAsLu($id); header('Location: ' . BASE_URL . '/chef_projet_index.php?module=notifications'); exit; }
        $notifications = Notification::getByUser($userId, 50);
        $pageTitle = 'Notifications';
        $GLOBALS['viewData'] = compact('notifications');
        require __DIR__ . '/chef_projet_notifications.php';
        exit;
    }

    public static function rapports(string $action): void
    {
        $pdo = self::getPdo();
        $projetsParMois = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as mois, COUNT(*) as total FROM projects GROUP BY mois ORDER BY mois DESC LIMIT 12")->fetchAll();
        $tachesParMois = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as mois, COUNT(*) as total FROM tasks GROUP BY mois ORDER BY mois DESC LIMIT 12")->fetchAll();
        $pageTitle = 'Rapports';
        $GLOBALS['viewData'] = compact('projetsParMois', 'tachesParMois');
        require __DIR__ . '/chef_projet_rapports.php';
        exit;
    }

    public static function profil(string $action): void
    {
        $userId = Session::get('user_id');
        $user = User::findById($userId);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { Session::setFlash('error', 'Erreur de validation.'); }
            else {
                User::update($userId, ['nom' => trim($_POST['nom'] ?? ''), 'prenom' => trim($_POST['prenom'] ?? ''), 'email' => trim($_POST['email'] ?? ''), 'telephone' => trim($_POST['telephone'] ?? '')]);
                Session::set('user_nom', trim($_POST['nom'] ?? ''));
                Session::set('user_prenom', trim($_POST['prenom'] ?? ''));
                Session::setFlash('success', 'Profil mis à jour.');
                header('Location: ' . BASE_URL . '/chef_projet_index.php?module=profil');
                exit;
            }
        }
        $pageTitle = 'Mon profil';
        $GLOBALS['viewData'] = compact('user');
        require __DIR__ . '/chef_projet_profil.php';
        exit;
    }

    private static function getMonthlyCount(string $table, string $dateCol): array
    {
        $pdo = self::getPdo();
        $months = []; $values = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = date('Y-m', strtotime("-{$i} months"));
            $months[] = (new \DateTime($m))->format('M Y');
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE DATE_FORMAT($dateCol,'%Y-%m') = :m");
            $stmt->execute([':m' => $m]);
            $values[] = (int)$stmt->fetchColumn();
        }
        return ['labels' => $months, 'data' => $values];
    }
}
