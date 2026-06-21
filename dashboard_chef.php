<?php
require_once __DIR__ . '/app.php';
require_once __DIR__ . '/database.php';
require_once HELPERS_PATH . '/Session.php';
require_once MODELS_PATH . '/User.php';
require_once MIDDLEWARE_PATH . '/AuthMiddleware.php';
require_once MIDDLEWARE_PATH . '/RoleMiddleware.php';
Session::start();
AuthMiddleware::check();
RoleMiddleware::require([ROLE_ADMIN, ROLE_CHEF_PROJET]);
$pageTitle = 'Dashboard Chef de projet';
$userId = $_SESSION['user_id'];

$stmt = getPDO()->prepare("SELECT COUNT(*) FROM projects WHERE chef_projet_id = :id");
$stmt->execute([':id' => $userId]);
$mesProjets = $stmt->fetchColumn();

$tachesEncours = getPDO()->query("SELECT COUNT(*) FROM tasks WHERE statut != 'termine'")->fetchColumn();
$tachesTerminees = getPDO()->query("SELECT COUNT(*) FROM tasks WHERE statut = 'termine'")->fetchColumn();

$projets = getPDO()->query("SELECT id, nom_projet, progression, statut FROM projects WHERE statut != 'termine' ORDER BY progression ASC LIMIT 5")->fetchAll();
$tachesRecentes = getPDO()->query("SELECT t.*, p.nom_projet as projet_nom FROM tasks t LEFT JOIN projects p ON t.project_id = p.id ORDER BY t.created_at DESC LIMIT 5")->fetchAll();
$clientsActifs = getPDO()->query("SELECT COUNT(*) FROM contracts WHERE statut = 'actif'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr" data-theme="<?= isset($_COOKIE['digicrm-theme']) ? htmlspecialchars($_COOKIE['digicrm-theme']) : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - DigiCRM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin.css">
</head>
<body>
    <header class="admin-navbar">
        <div class="navbar-left">
            <a href="index.php" class="navbar-brand">
                <span class="navbar-logo"><i class="fas fa-diamond"></i></span>
                <span class="navbar-brand-text">DigiCRM</span>
            </a>
        </div>
        <div class="navbar-right">
            <button class="navbar-icon-btn" id="themeToggle" title="Mode sombre">
                <i class="fas fa-moon"></i>
            </button>
            <div class="navbar-user" id="userMenuBtn" style="cursor:default;">
                <div class="navbar-user-avatar"><?= mb_strtoupper(mb_substr($_SESSION['user_prenom'], 0, 1)) . mb_strtoupper(mb_substr($_SESSION['user_nom'], 0, 1)) ?></div>
                <div class="navbar-user-info">
                    <span class="navbar-user-name"><?= htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']) ?></span>
                    <span class="navbar-user-role">Chef de projet</span>
                </div>
            </div>
            <a href="logout.php" class="btn btn--danger btn--sm" style="margin-left:8px;">Déconnexion</a>
        </div>
    </header>

    <main class="admin-main" style="margin-left:0;">
        <div class="admin-container">
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h1>Bonjour, <?= htmlspecialchars($_SESSION['user_prenom']) ?> 👋</h1>
                    <p>Supervisez vos projets et le travail de votre équipe.</p>
                </div>
                <div class="welcome-datetime">
                    <div class="welcome-date"><?= date('l d F Y') ?></div>
                    <div class="welcome-time"><?= date('H:i') ?></div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-icon blue"><i class="fas fa-project-diagram"></i></div>
                    <div class="stat-card-info">
                        <div class="stat-card-number"><?= $mesProjets ?></div>
                        <div class="stat-card-label">Mes projets</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon purple"><i class="fas fa-tasks"></i></div>
                    <div class="stat-card-info">
                        <div class="stat-card-number"><?= $tachesEncours ?></div>
                        <div class="stat-card-label">Tâches en cours</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon green"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-card-info">
                        <div class="stat-card-number"><?= $tachesTerminees ?></div>
                        <div class="stat-card-label">Tâches terminées</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon teal"><i class="fas fa-file-signature"></i></div>
                    <div class="stat-card-info">
                        <div class="stat-card-number"><?= $clientsActifs ?></div>
                        <div class="stat-card-label">Contrats actifs</div>
                    </div>
                </div>
            </div>

            <div class="dashboard-bottom-grid">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-project-diagram"></i> Progression des projets</div>
                        <a href="admin/index.php?module=projets" class="card-header-link">Voir tout</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($projets)): ?>
                            <div class="empty-state">Aucun projet en cours.</div>
                        <?php else: foreach ($projets as $p): ?>
                            <div style="margin-bottom:16px;">
                                <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                                    <span style="font-size:13px;font-weight:600;color:var(--gray-700);"><?= htmlspecialchars($p['nom_projet']) ?></span>
                                    <span style="font-size:12px;color:var(--gray-400);"><?= $p['progression'] ?>%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-bar__fill <?= $p['progression'] >= 80 ? 'progress-bar__fill--green' : ($p['progression'] >= 40 ? 'progress-bar__fill--orange' : '') ?>" style="width:<?= $p['progression'] ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-tasks"></i> Tâches récentes</div>
                        <a href="admin/index.php?module=taches" class="card-header-link">Voir tout</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($tachesRecentes)): ?>
                            <div class="empty-state">Aucune tâche récente.</div>
                        <?php else: ?>
                            <div class="table-mini">
                                <?php foreach ($tachesRecentes as $t): ?>
                                <div class="table-mini-row">
                                    <div class="table-mini-avatar table-mini-avatar--alt"><i class="fas fa-tasks"></i></div>
                                    <div class="table-mini-info">
                                        <div class="table-mini-name"><?= htmlspecialchars($t['titre']) ?></div>
                                        <div class="table-mini-sub"><?= htmlspecialchars($t['projet_nom'] ?? '-') ?> · <span class="badge badge--<?= $t['statut'] ?>"><?= htmlspecialchars($t['statut']) ?></span></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fas fa-th-large"></i> Modules</div>
                </div>
                <div class="card-body">
                    <div class="stats-grid stats-grid-3">
                        <a href="admin/index.php?module=projets" class="stat-card" style="text-decoration:none;">
                            <div class="stat-card-icon blue"><i class="fas fa-project-diagram"></i></div>
                            <div class="stat-card-info">
                                <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Projets</div>
                                <div class="stat-card-label-sub">Gérez les projets de l'agence</div>
                            </div>
                        </a>
                        <a href="admin/index.php?module=taches" class="stat-card" style="text-decoration:none;">
                            <div class="stat-card-icon purple"><i class="fas fa-tasks"></i></div>
                            <div class="stat-card-info">
                                <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Tâches</div>
                                <div class="stat-card-label-sub">Suivez l'avancement des tâches</div>
                            </div>
                        </a>
                        <a href="admin/index.php?module=clients" class="stat-card" style="text-decoration:none;">
                            <div class="stat-card-icon green"><i class="fas fa-building"></i></div>
                            <div class="stat-card-info">
                                <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Clients</div>
                                <div class="stat-card-label-sub">Consultez les clients</div>
                            </div>
                        </a>
                        <a href="admin/index.php?module=contrats" class="stat-card" style="text-decoration:none;">
                            <div class="stat-card-icon teal"><i class="fas fa-file-signature"></i></div>
                            <div class="stat-card-info">
                                <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Contrats</div>
                                <div class="stat-card-label-sub">Gérez les contrats</div>
                            </div>
                        </a>
                        <a href="admin/index.php?module=factures" class="stat-card" style="text-decoration:none;">
                            <div class="stat-card-icon orange"><i class="fas fa-file-invoice-dollar"></i></div>
                            <div class="stat-card-info">
                                <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Factures</div>
                                <div class="stat-card-label-sub">Suivez la facturation</div>
                            </div>
                        </a>
                        <a href="admin/index.php?module=rapports" class="stat-card" style="text-decoration:none;">
                            <div class="stat-card-icon pink"><i class="fas fa-chart-bar"></i></div>
                            <div class="stat-card-info">
                                <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Rapports</div>
                                <div class="stat-card-label-sub">Analysez les performances</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>var BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="<?= BASE_URL ?>/admin.js"></script>
</body>
</html>
