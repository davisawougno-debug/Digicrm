<?php
/**
 * Routeur du module Administration DigiCRM
 *
 * Point d'entrée unique pour toutes les pages du dashboard admin.
 * Vérifie l'authentification et le rôle administrateur, puis
 * dispatch vers le contrôleur approprié.
 *
 * URL : admin/index.php?module=MODULE&action=ACTION&id=ID
 */

require_once __DIR__ . '/app.php';
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

require_once __DIR__ . '/AdminDashboardController.php';
require_once __DIR__ . '/AdminUserController.php';
require_once __DIR__ . '/AdminServiceController.php';
require_once __DIR__ . '/AdminProspectController.php';
require_once __DIR__ . '/AdminClientController.php';
require_once __DIR__ . '/AdminDevisController.php';
require_once __DIR__ . '/AdminContractController.php';
require_once __DIR__ . '/AdminProjectController.php';
require_once __DIR__ . '/AdminTaskController.php';
require_once __DIR__ . '/AdminInvoiceController.php';
require_once __DIR__ . '/AdminPaymentController.php';
require_once __DIR__ . '/AdminNotificationController.php';
require_once __DIR__ . '/AdminReportController.php';
require_once __DIR__ . '/AdminSettingsController.php';
require_once __DIR__ . '/AdminSearchController.php';

$isAjax = !empty($_GET['ajax']);
define('AJAX_REQUEST', $isAjax);

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
    header('Location: ' . BASE_URL . '/admin_index.php');
    exit;
}
