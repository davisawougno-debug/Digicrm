<?php

require_once MODELS_PATH . '/Notification.php';
require_once MODELS_PATH . '/Activity.php';
require_once MODELS_PATH . '/User.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';

class NotificationController
{
    public static function notifications(string $action, ?int $id = null): void
    {
        $userId = Session::get('user_id');

        switch ($action) {
            case 'list':
                $userNotifications = Notification::getByUser($userId, 50);
                $globalNotifications = Notification::getGlobales();
                $GLOBALS['viewData'] = compact('userNotifications', 'globalNotifications');
                $pageTitle = 'Notifications';
                require __DIR__ . '/admin_notifications.php';
                exit;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                        Session::setFlash('error', 'Erreur de validation du formulaire.');
                    } else {
                        $titre = trim($_POST['titre'] ?? '');
                        $message = trim($_POST['message'] ?? '');
                        $type = $_POST['type'] ?? 'info';
                        $targetUserId = $_POST['user_id'] ?? null;

                        if (empty($titre) || empty($message)) {
                            Session::setFlash('error', 'Veuillez remplir tous les champs.');
                        } else {
                            Notification::create([
                                'user_id' => $targetUserId ?: null,
                                'titre'   => $titre,
                                'message' => $message,
                                'type'    => $type,
                                'lu'      => 0,
                            ]);
                            Activity::log($userId, 'creation_notification', "Notification créée : {$titre}");
                            Session::setFlash('success', 'Notification créée avec succès.');
                            header('Location: ' . BASE_URL . '/admin_index.php?module=notifications');
                            exit;
                        }
                    }
                }
                $users = User::getAll();
                $GLOBALS['viewData'] = compact('users');
                $pageTitle = 'Nouvelle notification';
                require __DIR__ . '/admin_notifications.php';
                exit;

            case 'delete':
                if ($id && Security::validateId($id)) {
                    Notification::delete($id);
                    Session::setFlash('success', 'Notification supprimée.');
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=notifications');
                exit;

            case 'mark-read':
                if ($id && Security::validateId($id)) {
                    Notification::markAsLu($id);
                }
                header('Location: ' . BASE_URL . '/admin_index.php?module=notifications');
                exit;

            case 'mark-all-read':
                Notification::markAllAsLu($userId);
                Session::setFlash('success', 'Toutes les notifications ont été marquées comme lues.');
                header('Location: ' . BASE_URL . '/admin_index.php?module=notifications');
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=notifications');
                exit;
        }
    }

    public static function activityLog(string $action = 'list'): void
    {
        switch ($action) {
            case 'list':
                $filterAction = $_GET['action_type'] ?? '';
                $filterUserId = $_GET['user_id'] ?? '';
                $filterDate = $_GET['date'] ?? '';

                if ($filterAction) {
                    $activities = Activity::getByAction($filterAction, 200);
                } else {
                    $activities = Activity::getAll(200);
                }

                if ($filterUserId) {
                    $activities = array_filter($activities, function ($a) use ($filterUserId) {
                        return (int)$a['user_id'] === (int)$filterUserId;
                    });
                }
                if ($filterDate) {
                    $activities = array_filter($activities, function ($a) use ($filterDate) {
                        return strpos($a['created_at'], $filterDate) === 0;
                    });
                }

                $users = User::getAll();
                $GLOBALS['viewData'] = compact('activities', 'users', 'filterAction', 'filterUserId', 'filterDate');
                $pageTitle = "Journal d'activités";
                require __DIR__ . '/admin_activity_log.php';
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=activity-log');
                exit;
        }
    }
}
