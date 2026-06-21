<?php

require_once __DIR__ . '/app.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';
require_once HELPERS_PATH . '/Security.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Activity.php';
require_once MODELS_PATH . '/Notification.php';
require_once MODELS_PATH . '/Prospect.php';
require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/Devis.php';
require_once MODELS_PATH . '/Contract.php';
require_once MIDDLEWARE_PATH . '/AuthMiddleware.php';
require_once MIDDLEWARE_PATH . '/RoleMiddleware.php';

Session::configureCookie();
AuthMiddleware::check();
RoleMiddleware::require([ROLE_COMMERCIAL, ROLE_ADMIN]);

require_once __DIR__ . '/CommercialDashboardController.php';

$isAjax = !empty($_GET['ajax']);
define('AJAX_REQUEST', $isAjax);

$module = $_GET['module'] ?? 'dashboard';
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    switch ($module) {
        case 'dashboard':
            CommercialDashboardController::dashboard();
            break;

        case 'prospects':
            CommercialDashboardController::prospects($action, $id);
            break;

        case 'clients':
            CommercialDashboardController::clients($action, $id);
            break;

        case 'devis':
            CommercialDashboardController::devis($action, $id);
            break;

        case 'contrats':
            CommercialDashboardController::contrats($action, $id);
            break;

        case 'rendez-vous':
            CommercialDashboardController::rendezVous($action, $id);
            break;

        case 'notifications':
            CommercialDashboardController::notifications($action, $id);
            break;

        case 'rapports':
            CommercialDashboardController::rapports($action);
            break;

        case 'profil':
            CommercialDashboardController::profil($action);
            break;

        default:
            CommercialDashboardController::dashboard();
            break;
    }
} catch (Exception $e) {
    error_log('Commercial Error: ' . $e->getMessage());
    Session::setFlash('error', 'Une erreur est survenue. Veuillez réessayer.');
    header('Location: ' . BASE_URL . '/commercial_index.php');
    exit;
}
