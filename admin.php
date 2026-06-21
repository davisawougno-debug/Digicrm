<?php
/**
 * Routeur du module Administration DigiCRM
 *
 * Point d'entrée unique pour toutes les pages du dashboard admin.
 * Vérifie l'authentification et le rôle administrateur, puis
 * dispatch vers le contrôleur approprié.
 *
 * URL : admin.php?module=MODULE&action=ACTION&id=ID
 */

require_once __DIR__ . '/config/app.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';
require_once HELPERS_PATH . '/Security.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Activity.php';
require_once MODELS_PATH . '/Notification.php';
require_once MODELS_PATH . '/Parametre.php';
require_once MIDDLEWARE_PATH . '/AuthMiddleware.php';
require_once MIDDLEWARE_PATH . '/RoleMiddleware.php';

Session::configureCookie();
AuthMiddleware::check();
RoleMiddleware::require(ROLE_ADMIN);

require_once CONTROLLERS_PATH . '/admin/DashboardController.php';
require_once CONTROLLERS_PATH . '/admin/UserController.php';
require_once CONTROLLERS_PATH . '/admin/ServiceController.php';
require_once CONTROLLERS_PATH . '/admin/ProspectController.php';
require_once CONTROLLERS_PATH . '/admin/ClientController.php';
require_once CONTROLLERS_PATH . '/admin/DevisController.php';
require_once CONTROLLERS_PATH . '/admin/ContractController.php';
require_once CONTROLLERS_PATH . '/admin/ProjectController.php';
require_once CONTROLLERS_PATH . '/admin/TaskController.php';
require_once CONTROLLERS_PATH . '/admin/InvoiceController.php';
require_once CONTROLLERS_PATH . '/admin/PaymentController.php';
require_once CONTROLLERS_PATH . '/admin/NotificationController.php';
require_once CONTROLLERS_PATH . '/admin/ReportController.php';
require_once CONTROLLERS_PATH . '/admin/SettingsController.php';
require_once CONTROLLERS_PATH . '/admin/SearchController.php';

$module = $_GET['module'] ?? 'dashboard';
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    switch ($module) {
        case 'dashboard':
            DashboardController::dashboard();
            break;

        case 'users':
            UserController::users($action, $id);
            break;

        case 'services':
            ServiceController::services($action, $id);
            break;

        case 'prospects':
            ProspectController::prospects($action, $id);
            break;

        case 'clients':
            ClientController::clients($action, $id);
            break;

        case 'devis':
            DevisController::devis($action, $id);
            break;

        case 'contrats':
            ContractController::contrats($action, $id);
            break;

        case 'projets':
            ProjectController::projets($action, $id);
            break;

        case 'taches':
            TaskController::taches($action, $id);
            break;

        case 'factures':
            InvoiceController::factures($action, $id);
            break;

        case 'paiements':
            PaymentController::paiements($action, $id);
            break;

        case 'notifications':
            NotificationController::notifications($action, $id);
            break;

        case 'activity-log':
            NotificationController::activityLog($action);
            break;

        case 'rapports':
            ReportController::rapports($action);
            break;

        case 'parametres':
            SettingsController::parametres($action);
            break;

        case 'search':
            SearchController::search();
            break;

        case 'ajax':
            DashboardController::getJsonFor($_GET['type'] ?? '', $_GET['action'] ?? '');
            break;

        default:
            DashboardController::dashboard();
            break;
    }
} catch (Exception $e) {
    error_log('Admin Error: ' . $e->getMessage());
    Session::setFlash('error', 'Une erreur est survenue. Veuillez réessayer.');
    header('Location: ' . BASE_URL . '/admin.php');
    exit;
}
