<?php

require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Service.php';
require_once MODELS_PATH . '/Prospect.php';
require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/Devis.php';
require_once MODELS_PATH . '/Contract.php';
require_once MODELS_PATH . '/Project.php';
require_once MODELS_PATH . '/Task.php';
require_once MODELS_PATH . '/Invoice.php';
require_once MODELS_PATH . '/Payment.php';
require_once MODELS_PATH . '/Notification.php';
require_once MODELS_PATH . '/Activity.php';
require_once MODELS_PATH . '/Parametre.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';
require_once MIDDLEWARE_PATH . '/AuthMiddleware.php';
require_once MIDDLEWARE_PATH . '/RoleMiddleware.php';

class DashboardController
{
    public static function dashboard(): void
    {
        $userId = Session::get('user_id');
        $pdo = getPDO();

        $stats = [
            'usersCount'     => User::countAll(),
            'prospectsCount' => Prospect::count(),
            'clientsCount'   => Client::count(),
            'projetsCount'   => Project::count(),
            'tachesCount'    => Task::count(),
            'contratsCount'  => Contract::count(),
            'devisCount'     => Devis::count(),
            'facturesCount'  => Invoice::count(),
        ];

        $stats['totalSales']     = Invoice::totalMontant();
        $stats['totalCollected'] = Invoice::totalEncaisser();
        $stats['unpaidCount']    = Invoice::countByStatut()['impayee'] ?? 0;

        $projetsByStatut = Project::countByStatut();
        $stats['projetsEnCours']  = $projetsByStatut['en_cours'] ?? 0;
        $stats['projetsTermines'] = $projetsByStatut['termine'] ?? 0;
        $stats['tachesEnRetard']  = count(Task::getEnRetard());

        $stats['recentActivities'] = Activity::getAll(10);
        $stats['recentUsers']      = User::getAll(1, 5);
        $stats['recentClients']    = Client::getRecent(5);
        $stats['recentNotifications'] = Notification::getByUser($userId, 10);

        $stats['recentProjets'] = Project::getRecent(5);
        $stats['recentFactures'] = Invoice::getRecent(5);

        $stats['monthlySales']      = self::getMonthlyData('invoices', 'montant_ttc');
        $stats['monthlyClients']    = self::getMonthlyData('clients', null);
        $stats['monthlyProjets']    = self::getMonthlyData('projects', null);
        $stats['servicesPop']       = self::getServicePopularity();
        $stats['statsEvolution']    = self::getEvolution();

        $stats['rendezVous'] = self::getUpcomingAppointments();

        $pageTitle = 'Tableau de bord';
        $GLOBALS['viewData'] = $stats;
        require __DIR__ . '/admin_dashboard.php';
        exit;
    }

    private static function getMonthlyData(string $table, ?string $valueCol): array
    {
        $pdo = getPDO();
        $months = [];
        $values = [];

        $sixMonthsAgo = date('Y-m-01', strtotime('-5 months'));
        $select = $valueCol ? "COALESCE(SUM($valueCol),0)" : 'COUNT(*)';
        $stmt = $pdo->prepare("SELECT DATE_FORMAT(created_at,'%Y-%m') AS m, $select AS v FROM $table WHERE created_at >= :since GROUP BY m ORDER BY m ASC");
        $stmt->execute([':since' => $sixMonthsAgo]);
        $rows = $stmt->fetchAll();
        $dataByMonth = [];
        foreach ($rows as $row) {
            $dataByMonth[$row['m']] = $valueCol ? (float)$row['v'] : (int)$row['v'];
        }

        for ($i = 5; $i >= 0; $i--) {
            $m = date('Y-m', strtotime("first day of -{$i} months"));
            $months[] = match ((int)date('m', strtotime($m))) {
                1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr', 5 => 'Mai', 6 => 'Juin',
                7 => 'Juil', 8 => 'Aoû', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc',
                default => $m
            } . ' ' . date('Y', strtotime($m));
            $values[] = $dataByMonth[$m] ?? 0;
        }
        return ['labels' => $months, 'data' => $values];
    }

    private static function getServicePopularity(): array
    {
        $pdo = getPDO();
        $labels = [];
        $data = [];
        $services = $pdo->query("SELECT s.nom, COUNT(dd.id) as cnt FROM services s LEFT JOIN devis_details dd ON dd.service_id = s.id GROUP BY s.id ORDER BY cnt DESC LIMIT 6")->fetchAll();
        foreach ($services as $s) {
            $labels[] = $s['nom'];
            $data[] = (int)$s['cnt'];
        }
        return ['labels' => $labels, 'data' => $data];
    }

    private static function getEvolution(): array
    {
        $pdo = getPDO();
        $now = (int)date('m');
        $last = $now > 1 ? $now - 1 : 12;
        $yearNow = date('Y');
        $yearLast = $now > 1 ? $yearNow : $yearNow - 1;
        $res = [];
        foreach (['users', 'prospects', 'clients', 'invoices'] as $t) {
            $cNow = $pdo->prepare("SELECT COUNT(*) FROM $t WHERE MONTH(created_at) = :m AND YEAR(created_at) = :y");
            $cNow->execute([':m' => $now, ':y' => $yearNow]);
            $cLast = $pdo->prepare("SELECT COUNT(*) FROM $t WHERE MONTH(created_at) = :m AND YEAR(created_at) = :y");
            $cLast->execute([':m' => $last, ':y' => $yearLast]);
            $n = (int)$cNow->fetchColumn();
            $l = (int)$cLast->fetchColumn();
            $res[$t] = $l > 0 ? round((($n - $l) / $l) * 100, 1) : ($n > 0 ? 100 : 0);
        }
        return $res;
    }

    private static function getUpcomingAppointments(): array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT a.*, u.nom, u.prenom FROM appointments a LEFT JOIN users u ON a.user_id = u.id WHERE a.date_rdv >= CURDATE() ORDER BY a.date_rdv, a.heure LIMIT 5");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getJsonFor(string $type, string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        switch ($type) {
            case 'users':
                echo json_encode(User::getAll());
                break;
            case 'clients':
                echo json_encode(Client::getAll());
                break;
            case 'prospects':
                echo json_encode(Prospect::getAll());
                break;
            case 'services':
                echo json_encode(Service::getAll());
                break;
            case 'projects':
                echo json_encode(Project::getAll());
                break;
            case 'contracts':
                echo json_encode(Contract::getAll());
                break;
            case 'invoices':
                echo json_encode(Invoice::getAll());
                break;
            case 'devis':
                echo json_encode(Devis::getAll());
                break;
            case 'taches':
                $projectId = (int)($_GET['project_id'] ?? 0);
                echo json_encode($projectId ? Task::getByProject($projectId) : Task::getAll());
                break;
            case 'paiements':
                $invoiceId = (int)($_GET['invoice_id'] ?? 0);
                echo json_encode($invoiceId ? Payment::getByInvoice($invoiceId) : Payment::getAll());
                break;
            case 'notifications':
                $userId = Session::get('user_id');
                echo json_encode([
                    'notifications' => Notification::getNonLu($userId),
                    'count'         => Notification::countNonLu($userId),
                ]);
                break;
            case 'prospect':
                $prospectId = (int)($_GET['id'] ?? 0);
                echo json_encode($prospectId ? Prospect::findById($prospectId) : null);
                break;
            case 'stats':
                echo json_encode([
                    'users'     => User::countAll(),
                    'clients'   => Client::count(),
                    'prospects' => Prospect::count(),
                    'projects'  => Project::count(),
                    'invoices'  => Invoice::count(),
                    'devis'     => Devis::count(),
                ]);
                break;
            default:
                echo json_encode(['error' => 'Type de données invalide.']);
                break;
        }
        exit;
    }
}
