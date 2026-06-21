<?php

require_once MODELS_PATH . '/Prospect.php';
require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/Devis.php';
require_once MODELS_PATH . '/Contract.php';
require_once MODELS_PATH . '/Notification.php';
require_once MODELS_PATH . '/Activity.php';
require_once MODELS_PATH . '/User.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';

class CommercialDashboardController
{
    public static function dashboard(): void
    {
        $userId = Session::get('user_id');
        $pdo = getPDO();

        $prospects = Prospect::getByUser($userId);
        $prospectsCount = count($prospects);
        $clientsCount = Client::count();
        $devisCount = Devis::count();
        $contratsCount = Contract::count();

        $devisEnvoyes = count(Devis::getByUser($userId));
        $devisAcceptes = count(Devis::getByUserAndStatut($userId, 'accepte'));

        $stats = compact(
            'prospectsCount', 'clientsCount', 'devisCount', 'contratsCount',
            'devisEnvoyes', 'devisAcceptes'
        );

        $stats['prospectsByStatut'] = Prospect::countByStatut();
        $stats['pipelineCounts'] = self::getPipelineCounts($userId);
        $stats['recentActivities'] = Activity::getByUser($userId, 10);
        $stats['recentProspects'] = array_slice($prospects, 0, 5);
        $stats['recentDevis'] = Devis::getRecentByUser($userId, 5);
        $stats['unreadNotifications'] = Notification::getNonLu($userId);
        $stats['notifCount'] = Notification::countNonLu($userId);
        $stats['monthlyDevis'] = self::getMonthlyData('devis', 'montant_total', $userId);
        $stats['monthlyClients'] = self::getMonthlyClients($userId);

        $stats['totalCA'] = Devis::sumMontantByUser($userId);

        $pageTitle = 'Dashboard Commercial';
        $GLOBALS['viewData'] = $stats;
        require __DIR__ . '/commercial_dashboard.php';
        exit;
    }

    public static function prospects(string $action, ?int $id = null): void
    {
        $userId = Session::get('user_id');

        switch ($action) {
            case 'list':
                $prospects = Prospect::getByUser($userId);
                $pageTitle = 'Mes prospects';
                $GLOBALS['viewData'] = compact('prospects');
                require __DIR__ . '/commercial_prospects.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $data = [
                            'nom' => trim($_POST['nom'] ?? ''),
                            'prenom' => trim($_POST['prenom'] ?? ''),
                            'email' => trim($_POST['email'] ?? ''),
                            'telephone' => trim($_POST['telephone'] ?? ''),
                            'entreprise' => trim($_POST['entreprise'] ?? ''),
                            'source' => $_POST['source'] ?? null,
                            'statut' => $_POST['statut'] ?? 'nouveau',
                            'assigned_to' => $userId,
                            'budget_estime' => $_POST['budget_estime'] ?? null,
                            'besoin' => trim($_POST['besoin'] ?? ''),
                        ];

                        Validation::reset();
                        Validation::required('nom', 'Nom', $data['nom']);
                        Validation::required('prenom', 'Prénom', $data['prenom']);
                        Validation::required('email', 'Email', $data['email']);
                        Validation::email('email', $data['email']);

                        if (Validation::hasErrors()) {
                            $_SESSION['validation_errors'] = Validation::errors();
                        } else {
                            $prospectId = Prospect::create($data);
                            if ($prospectId) {
                                Activity::log($userId, 'creation', "Prospect créé : {$data['prenom']} {$data['nom']}", 'prospects');
                                Notification::create([
                                    'user_id' => $userId,
                                    'titre' => 'Nouveau prospect',
                                    'message' => "Prospect {$data['prenom']} {$data['nom']} ajouté avec succès.",
                                    'type' => 'success',
                                ]);
                                Session::setFlash('success', 'Prospect ajouté avec succès.');
                                header('Location: ' . BASE_URL . '/commercial_index.php?module=prospects');
                                exit;
                            }
                            Session::setFlash('error', 'Erreur lors de la création du prospect.');
                        }
                    }
                }
                $pageTitle = 'Nouveau prospect';
                $GLOBALS['viewData'] = [];
                require __DIR__ . '/commercial_prospects.php';
                exit;

            case 'edit':
                $prospect = $id ? Prospect::findById($id) : null;
                if (!$prospect) {
                    Session::setFlash('error', 'Prospect introuvable.');
                    header('Location: ' . BASE_URL . '/commercial_index.php?module=prospects');
                    exit;
                }
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation.');
                    } else {
                        $data = [
                            'nom' => trim($_POST['nom'] ?? ''),
                            'prenom' => trim($_POST['prenom'] ?? ''),
                            'email' => trim($_POST['email'] ?? ''),
                            'telephone' => trim($_POST['telephone'] ?? ''),
                            'entreprise' => trim($_POST['entreprise'] ?? ''),
                            'source' => $_POST['source'] ?? null,
                            'statut' => $_POST['statut'] ?? 'nouveau',
                            'budget_estime' => $_POST['budget_estime'] ?? null,
                            'besoin' => trim($_POST['besoin'] ?? ''),
                        ];
                        Prospect::update($id, $data);
                        Activity::log($userId, 'modification', "Prospect modifié : {$data['prenom']} {$data['nom']}", 'prospects');
                        Session::setFlash('success', 'Prospect mis à jour.');
                        header('Location: ' . BASE_URL . '/commercial_index.php?module=prospects');
                        exit;
                    }
                }
                $pageTitle = 'Modifier le prospect';
                $GLOBALS['viewData'] = compact('prospect');
                require __DIR__ . '/commercial_prospects.php';
                exit;

            case 'convert':
                if ($id) {
                    Prospect::convertToClient($id);
                    $p = Prospect::findById($id);
                    Client::create([
                        'nom' => $p['nom'],
                        'prenom' => $p['prenom'],
                        'email' => $p['email'],
                        'telephone' => $p['telephone'] ?? '',
                        'entreprise' => $p['entreprise'] ?? '',
                        'created_from_prospect_id' => $id,
                    ]);
                    Activity::log($userId, 'conversion', "Prospect converti en client : {$p['prenom']} {$p['nom']}", 'prospects');
                    Session::setFlash('success', 'Prospect converti en client avec succès.');
                }
                header('Location: ' . BASE_URL . '/commercial_index.php?module=prospects');
                exit;

            default:
                $prospects = Prospect::getByUser($userId);
                $pageTitle = 'Mes prospects';
                $GLOBALS['viewData'] = compact('prospects');
                require __DIR__ . '/commercial_prospects.php';
                exit;
        }
    }

    public static function clients(string $action, ?int $id = null): void
    {
        $userId = Session::get('user_id');

        switch ($action) {
            case 'list':
                $clients = Client::getAll();
                $pageTitle = 'Clients';
                $GLOBALS['viewData'] = compact('clients');
                require __DIR__ . '/commercial_clients.php';
                exit;

            case 'view':
                $client = $id ? Client::findById($id) : null;
                if (!$client) {
                    Session::setFlash('error', 'Client introuvable.');
                    header('Location: ' . BASE_URL . '/commercial_index.php?module=clients');
                    exit;
                }
                $devis = Devis::getByClient($id);
                $contrats = Contract::getByClient($id);
                $pageTitle = $client['entreprise'] ?: $client['prenom'] . ' ' . $client['nom'];
                $GLOBALS['viewData'] = compact('client', 'devis', 'contrats');
                require __DIR__ . '/commercial_clients.php';
                exit;

            default:
                $clients = Client::getAll();
                $pageTitle = 'Clients';
                $GLOBALS['viewData'] = compact('clients');
                require __DIR__ . '/commercial_clients.php';
                exit;
        }
    }

    public static function devis(string $action, ?int $id = null): void
    {
        $userId = Session::get('user_id');

        switch ($action) {
            case 'list':
                $devis = Devis::getAll();
                $pageTitle = 'Devis';
                $GLOBALS['viewData'] = compact('devis');
                require __DIR__ . '/commercial_devis.php';
                exit;

            case 'view':
                $devis = $id ? Devis::findById($id) : null;
                if (!$devis) {
                    Session::setFlash('error', 'Devis introuvable.');
                    header('Location: ' . BASE_URL . '/commercial_index.php?module=devis');
                    exit;
                }
                $pageTitle = 'Devis #' . ($devis['numero_devis'] ?? $id);
                $GLOBALS['viewData'] = compact('devis');
                require __DIR__ . '/commercial_devis.php';
                exit;

            default:
                $devis = Devis::getAll();
                $pageTitle = 'Devis';
                $GLOBALS['viewData'] = compact('devis');
                require __DIR__ . '/commercial_devis.php';
                exit;
        }
    }

    public static function contrats(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':
                $contrats = Contract::getAll();
                $pageTitle = 'Contrats';
                $GLOBALS['viewData'] = compact('contrats');
                require __DIR__ . '/commercial_contrats.php';
                exit;

            case 'view':
                $contrat = $id ? Contract::findById($id) : null;
                if (!$contrat) {
                    Session::setFlash('error', 'Contrat introuvable.');
                    header('Location: ' . BASE_URL . '/commercial_index.php?module=contrats');
                    exit;
                }
                $pageTitle = 'Contrat #' . ($contrat['numero'] ?? $id);
                $GLOBALS['viewData'] = compact('contrat');
                require __DIR__ . '/commercial_contrats.php';
                exit;

            default:
                $contrats = Contract::getAll();
                $pageTitle = 'Contrats';
                $GLOBALS['viewData'] = compact('contrats');
                require __DIR__ . '/commercial_contrats.php';
                exit;
        }
    }

    public static function rendezVous(string $action, ?int $id = null): void
    {
        $userId = Session::get('user_id');
        $pdo = getPDO();

        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE user_id = :uid OR assigned_to = :uid2 ORDER BY date_rdv DESC");
        $stmt->execute([':uid' => $userId, ':uid2' => $userId]);
        $rendezVous = $stmt->fetchAll();

        $pageTitle = 'Rendez-vous';
        $GLOBALS['viewData'] = compact('rendezVous');
        require __DIR__ . '/commercial_rendez_vous.php';
        exit;
    }

    public static function notifications(string $action, ?int $id = null): void
    {
        $userId = Session::get('user_id');

        if ($action === 'mark-read' && $id) {
            Notification::markAsLu($id);
            header('Location: ' . BASE_URL . '/commercial_index.php?module=notifications');
            exit;
        }

        if ($action === 'mark-all-read') {
            Notification::markAllAsLu($userId);
            Session::setFlash('success', 'Toutes les notifications marquées comme lues.');
            header('Location: ' . BASE_URL . '/commercial_index.php?module=notifications');
            exit;
        }

        $notifications = Notification::getByUser($userId, 50);
        $pageTitle = 'Notifications';
        $GLOBALS['viewData'] = compact('notifications');
        require __DIR__ . '/commercial_notifications.php';
        exit;
    }

    public static function rapports(string $action): void
    {
        $userId = Session::get('user_id');
        $pdo = getPDO();

        $prospectsParMois = $pdo->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') as mois, COUNT(*) as total FROM prospects WHERE assigned_to = :uid GROUP BY mois ORDER BY mois DESC LIMIT 12");
        $prospectsParMois->execute([':uid' => $userId]);

        $devisParMois = $pdo->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') as mois, COUNT(*) as total, SUM(montant_total) as ca FROM devis WHERE user_id = :uid GROUP BY mois ORDER BY mois DESC LIMIT 12");
        $devisParMois->execute([':uid' => $userId]);

        $pageTitle = 'Rapports';
        $GLOBALS['viewData'] = [
            'prospectsParMois' => $prospectsParMois->fetchAll(),
            'devisParMois' => $devisParMois->fetchAll(),
        ];
        require __DIR__ . '/commercial_rapports.php';
        exit;
    }

    public static function profil(string $action): void
    {
        $userId = Session::get('user_id');
        $user = User::findById($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Erreur de validation.');
            } else {
                User::update($userId, [
                    'nom' => trim($_POST['nom'] ?? ''),
                    'prenom' => trim($_POST['prenom'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'telephone' => trim($_POST['telephone'] ?? ''),
                ]);
                Session::set('user_nom', trim($_POST['nom'] ?? ''));
                Session::set('user_prenom', trim($_POST['prenom'] ?? ''));
                Session::setFlash('success', 'Profil mis à jour.');
                header('Location: ' . BASE_URL . '/commercial_index.php?module=profil');
                exit;
            }
        }

        $pageTitle = 'Mon profil';
        $GLOBALS['viewData'] = compact('user');
        require __DIR__ . '/commercial_profil.php';
        exit;
    }

    private static function getPipelineCounts(int $userId): array
    {
        $pdo = getPDO();
        $stages = ['nouveau', 'contacte', 'qualifie', 'perdu', 'converti'];
        $counts = [];
        foreach ($stages as $stage) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM prospects WHERE assigned_to = :uid AND statut = :statut");
            $stmt->execute([':uid' => $userId, ':statut' => $stage]);
            $counts[$stage] = (int)$stmt->fetchColumn();
        }
        return $counts;
    }

    private static function getMonthlyData(string $table, string $valueCol, int $userId): array
    {
        $pdo = getPDO();
        $months = [];
        $values = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = date('Y-m', strtotime("-{$i} months"));
            $months[] = (new DateTime($m))->format('M Y');
            $stmt = $pdo->prepare("SELECT COALESCE(SUM($valueCol),0) FROM $table WHERE user_id = :uid AND DATE_FORMAT(created_at,'%Y-%m') = :m");
            $stmt->execute([':uid' => $userId, ':m' => $m]);
            $values[] = (float)$stmt->fetchColumn();
        }
        return ['labels' => $months, 'data' => $values];
    }

    private static function getMonthlyClients(int $userId): array
    {
        $pdo = getPDO();
        $months = [];
        $values = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = date('Y-m', strtotime("-{$i} months"));
            $months[] = (new DateTime($m))->format('M Y');
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE DATE_FORMAT(created_at,'%Y-%m') = :m");
            $stmt->execute([':m' => $m]);
            $values[] = (int)$stmt->fetchColumn();
        }
        return ['labels' => $months, 'data' => $values];
    }
}
