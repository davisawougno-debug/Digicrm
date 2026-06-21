<?php
/**
 * Dashboard Administrateur
 * Accès complet à tous les modules DigiCRM.
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once HELPERS_PATH . '/Session.php';
require_once MODELS_PATH . '/User.php';
require_once MIDDLEWARE_PATH . '/AuthMiddleware.php';
require_once MIDDLEWARE_PATH . '/RoleMiddleware.php';
Session::start();
AuthMiddleware::check();
RoleMiddleware::require(ROLE_ADMIN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur - DigiCRM</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }
        .navbar {
            background: #fff;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-brand { font-weight: 700; font-size: 18px; color: #2563eb; }
        .navbar-user { display: flex; align-items: center; gap: 12px; font-size: 14px; }
        .navbar-user span { color: #64748b; }
        .navbar-user strong { color: #1e293b; }
        .navbar-user a {
            padding: 6px 14px;
            background: #fee2e2;
            color: #dc2626;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        .navbar-user a:hover { background: #fecaca; }
        .container { max-width: 1200px; margin: 0 auto; padding: 24px; }
        .welcome { margin-bottom: 24px; }
        .welcome h1 { font-size: 22px; }
        .welcome p { color: #64748b; font-size: 14px; margin-top: 4px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: #fff; border-radius: 12px; padding: 20px;
            border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .stat-card .number { font-size: 28px; font-weight: 700; color: #2563eb; }
        .stat-card .label { font-size: 13px; color: #64748b; margin-top: 4px; }
        .modules { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
        .module-card {
            background: #fff; border-radius: 12px; padding: 20px;
            border: 1px solid #e2e8f0; text-decoration: none; color: inherit;
            transition: all 0.2s; display: block;
        }
        .module-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-2px); }
        .module-card .icon { font-size: 24px; margin-bottom: 8px; }
        .module-card h3 { font-size: 15px; }
        .module-card p { font-size: 13px; color: #64748b; margin-top: 4px; }
        .logout-btn {
            float: right;
            padding: 8px 16px;
            background: #2563eb;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .logout-btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">&#9670; DigiCRM</div>
        <div class="navbar-user">
            <span>Connecté en tant que</span>
            <strong><?= htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']) ?></strong>
            <span class="badge">Administrateur</span>
            <a href="logout.php">Déconnexion</a>
        </div>
    </nav>

    <div class="container">
        <div class="welcome">
            <h1>Dashboard Administrateur</h1>
            <p>Gérez l'ensemble de la plateforme DigiCRM.</p>
        </div>

        <div class="stats">
            <?php
            $counts = [
                getPDO()->query("SELECT COUNT(*) FROM users")->fetchColumn(),
                getPDO()->query("SELECT COUNT(*) FROM prospects")->fetchColumn(),
                getPDO()->query("SELECT COUNT(*) FROM clients")->fetchColumn(),
                getPDO()->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
            ];
            $labels = ['Utilisateurs', 'Prospects', 'Clients', 'Projets'];
            foreach (array_combine($labels, $counts) as $label => $count):
            ?>
            <div class="stat-card">
                <div class="number"><?= $count ?></div>
                <div class="label"><?= $label ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="modules">
            <a href="admin.php" class="module-card">
                <div class="icon">&#9881;</div>
                <h3>Administration</h3>
                <p>Panneau d'administration complet</p>
            </a>
            <a href="admin.php?module=users" class="module-card">
                <div class="icon">&#128101;</div>
                <h3>Utilisateurs</h3>
                <p>Gérer les comptes et les rôles</p>
            </a>
            <a href="admin.php?module=prospects" class="module-card">
                <div class="icon">&#128229;</div>
                <h3>Prospects</h3>
                <p>Suivi des opportunités commerciales</p>
            </a>
            <a href="admin.php?module=clients" class="module-card">
                <div class="icon">&#127970;</div>
                <h3>Clients</h3>
                <p>Gestion du portefeuille client</p>
            </a>
            <a href="admin.php?module=projets" class="module-card">
                <div class="icon">&#128196;</div>
                <h3>Projets</h3>
                <p>Suivi des projets en cours</p>
            </a>
            <a href="admin.php?module=factures" class="module-card">
                <div class="icon">&#128202;</div>
                <h3>Factures</h3>
                <p>Gestion de la facturation</p>
            </a>
            <a href="admin.php?module=parametres" class="module-card">
                <div class="icon">&#9881;</div>
                <h3>Paramètres</h3>
                <p>Configuration de la plateforme</p>
            </a>
        </div>
    </div>
</body>
</html>
