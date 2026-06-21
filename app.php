<?php
/**
 * Configuration générale de l'application DigiCRM
 */

define('BASE_PATH', __DIR__);
define('CONFIG_PATH', __DIR__);
define('CONTROLLERS_PATH', __DIR__);
define('MODELS_PATH', __DIR__);
define('VIEWS_PATH', __DIR__);
define('HELPERS_PATH', __DIR__);
define('MIDDLEWARE_PATH', __DIR__);
define('ASSETS_PATH', __DIR__);

define('BASE_URL', 'http://localhost/Digicrm');

define('SESSION_LIFETIME', 3600);
define('SESSION_TIMEOUT', 1800);
define('SESSION_NAME', 'DIGICRM_SESSION');

define('PASSWORD_MIN_LENGTH', 6);
define('BCRYPT_COST', 12);
define('ITEMS_PER_PAGE', 15);

// === Rôles (correspondent à la base) ===
define('ROLE_ADMIN', 'admin');
define('ROLE_COMMERCIAL', 'commercial');
define('ROLE_CHEF_PROJET', 'chef_projet');
define('ROLE_EMPLOYE', 'employe');

define('ROLES_LIST', serialize([
    ROLE_ADMIN,
    ROLE_COMMERCIAL,
    ROLE_CHEF_PROJET,
    ROLE_EMPLOYE,
]));

// === Statuts ===
define('STATUT_ACTIF', 'actif');
define('STATUT_INACTIF', 'inactif');

date_default_timezone_set('Africa/Dakar');

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/error.log');

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
