<?php

require_once __DIR__ . '/app.php';
require_once HELPERS_PATH . '/Session.php';
require_once HELPERS_PATH . '/Validation.php';
require_once HELPERS_PATH . '/Security.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Activity.php';
require_once MODELS_PATH . '/Notification.php';
require_once MODELS_PATH . '/Project.php';
require_once MODELS_PATH . '/Task.php';
require_once MODELS_PATH . '/Deliverable.php';
require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/Contract.php';
require_once MIDDLEWARE_PATH . '/AuthMiddleware.php';
require_once MIDDLEWARE_PATH . '/RoleMiddleware.php';

Session::configureCookie();
AuthMiddleware::check();
RoleMiddleware::require([ROLE_CHEF_PROJET, ROLE_ADMIN]);

require_once __DIR__ . '/ChefProjetDashboardController.php';

$isAjax = !empty($_GET['ajax']);
define('AJAX_REQUEST', $isAjax);

$module = $_GET['module'] ?? 'dashboard';
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    switch ($module) {
        case 'dashboard':
            ChefProjetDashboardController::dashboard();
            break;
        case 'projets':
            ChefProjetDashboardController::projets($action, $id);
            break;
        case 'taches':
            ChefProjetDashboardController::taches($action, $id);
            break;
        case 'equipes':
            ChefProjetDashboardController::equipes($action);
            break;
        case 'livrables':
            ChefProjetDashboardController::livrables($action, $id);
            break;
        case 'calendrier':
            ChefProjetDashboardController::calendrier($action);
            break;
        case 'clients':
            ChefProjetDashboardController::clients($action, $id);
            break;
        case 'contrats':
            ChefProjetDashboardController::contrats($action, $id);
            break;
        case 'notifications':
            ChefProjetDashboardController::notifications($action, $id);
            break;
        case 'rapports':
            ChefProjetDashboardController::rapports($action);
            break;
        case 'profil':
            ChefProjetDashboardController::profil($action);
            break;
        default:
            ChefProjetDashboardController::dashboard();
            break;
    }
} catch (Exception $e) {
    error_log('ChefProjet Error: ' . $e->getMessage());
    Session::setFlash('error', 'Une erreur est survenue.');
    header('Location: ' . BASE_URL . '/chef_projet_index.php');
    exit;
}
