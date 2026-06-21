<?php
require_once __DIR__ . '/app.php';
require_once __DIR__ . '/database.php';
require_once HELPERS_PATH . '/Session.php';
require_once MODELS_PATH . '/User.php';
require_once MIDDLEWARE_PATH . '/AuthMiddleware.php';
require_once MIDDLEWARE_PATH . '/RoleMiddleware.php';
Session::start();
AuthMiddleware::check();
RoleMiddleware::require([ROLE_ADMIN, ROLE_CHEF_PROJET, ROLE_EMPLOYE]);
$pageTitle = 'Dashboard Employé';
$userId = $_SESSION['user_id'];

$stmt = getPDO()->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = :id");
$stmt->execute([':id' => $userId]);
$totalTaches = $stmt->fetchColumn();

$stmt2 = getPDO()->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = :id AND statut = 'termine'");
$stmt2->execute([':id' => $userId]);
$tachesTerminees = $stmt2->fetchColumn();

$stmt3 = getPDO()->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = :id AND statut != 'termine' AND date_fin < CURDATE()");
$stmt3->execute([':id' => $userId]);
$enRetard = $stmt3->fetchColumn();

$taches = getPDO()->prepare("
    SELECT t.*, p.nom_projet as projet_nom
    FROM tasks t
    LEFT JOIN projects p ON t.project_id = p.id
    WHERE t.assigned_to = :id AND t.statut != 'termine'
    ORDER BY t.date_fin ASC
    LIMIT 10
");
$taches->execute([':id' => $userId]);
$taches = $taches->fetchAll();

$u = getPDO()->prepare("SELECT * FROM users WHERE id = :id");
$u->execute([':id' => $userId]);
$u = $u->fetch();
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
                    <span class="navbar-user-role">Employé</span>
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
                    <p>Consultez vos tâches et gérez votre travail au quotidien.</p>
                </div>
                <div class="welcome-datetime">
                    <div class="welcome-date"><?= date('l d F Y') ?></div>
                    <div class="welcome-time"><?= date('H:i') ?></div>
                </div>
            </div>

            <div class="stats-grid stats-grid-3">
                <div class="stat-card">
                    <div class="stat-card-icon blue"><i class="fas fa-tasks"></i></div>
                    <div class="stat-card-info">
                        <div class="stat-card-number"><?= $totalTaches ?></div>
                        <div class="stat-card-label">Mes tâches</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon green"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-card-info">
                        <div class="stat-card-number"><?= $tachesTerminees ?></div>
                        <div class="stat-card-label">Terminées</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon red"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-card-info">
                        <div class="stat-card-number"><?= $enRetard ?></div>
                        <div class="stat-card-label">En retard</div>
                    </div>
                </div>
            </div>

            <div class="dashboard-bottom-grid">
                <div class="card card--full">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-tasks"></i> Mes tâches en cours</div>
                        <a href="admin/index.php?module=taches" class="card-header-link">Voir toutes mes tâches →</a>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Tâche</th>
                                    <th>Projet</th>
                                    <th>Priorité</th>
                                    <th>Statut</th>
                                    <th>Échéance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($taches)): ?>
                                    <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--gray-400);">Aucune tâche en cours.</td></tr>
                                <?php else: foreach ($taches as $t): ?>
                                    <tr>
                                        <td><strong style="color:var(--gray-700);"><?= htmlspecialchars($t['titre']) ?></strong></td>
                                        <td style="color:var(--gray-500);"><?= htmlspecialchars($t['projet_nom'] ?? '-') ?></td>
                                        <td><span class="badge badge--<?= $t['priorite'] ?>"><?= htmlspecialchars($t['priorite']) ?></span></td>
                                        <td><span class="badge badge--<?= $t['statut'] ?>"><?= htmlspecialchars($t['statut']) ?></span></td>
                                        <td>
                                            <?php if ($t['date_fin']): ?>
                                                <?php if ($t['date_fin'] < date('Y-m-d')): ?>
                                                    <span style="color:var(--danger);font-weight:600;"><?= date('d/m/Y', strtotime($t['date_fin'])) ?> ⚠</span>
                                                <?php else: ?>
                                                    <?= date('d/m/Y', strtotime($t['date_fin'])) ?>
                                                <?php endif; ?>
                                            <?php else: ?>-<?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="dashboard-bottom-grid">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-user"></i> Mes informations</div>
                        <a href="profile.php" class="card-header-link">Modifier</a>
                    </div>
                    <div class="card-body">
                        <?php if ($u): ?>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div>
                                <div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Nom</div>
                                <div style="font-size:14px;font-weight:600;color:var(--gray-700);margin-top:2px;"><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></div>
                            </div>
                            <div>
                                <div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Email</div>
                                <div style="font-size:14px;color:var(--gray-600);margin-top:2px;"><?= htmlspecialchars($u['email']) ?></div>
                            </div>
                            <div>
                                <div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Téléphone</div>
                                <div style="font-size:14px;color:var(--gray-600);margin-top:2px;"><?= htmlspecialchars($u['telephone'] ?? 'Non renseigné') ?></div>
                            </div>
                            <div>
                                <div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Rôle</div>
                                <div style="margin-top:2px;"><span class="badge badge--employe">Employé</span></div>
                            </div>
                            <div>
                                <div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Membre depuis</div>
                                <div style="font-size:14px;color:var(--gray-600);margin-top:2px;"><?= date('d/m/Y', strtotime($u['created_at'])) ?></div>
                            </div>
                            <div>
                                <div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Statut</div>
                                <div style="margin-top:2px;"><span class="badge badge--<?= $u['statut'] ?>"><?= htmlspecialchars($u['statut']) ?></span></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-th-large"></i> Accès rapide</div>
                    </div>
                    <div class="card-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <a href="admin/index.php?module=taches" class="stat-card" style="text-decoration:none;padding:16px;">
                                <div class="stat-card-icon blue"><i class="fas fa-tasks"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Tâches</div>
                                    <div class="stat-card-label-sub">Gérez vos tâches</div>
                                </div>
                            </a>
                            <a href="profile.php" class="stat-card" style="text-decoration:none;padding:16px;">
                                <div class="stat-card-icon purple"><i class="fas fa-user-cog"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Mon profil</div>
                                    <div class="stat-card-label-sub">Modifiez vos infos</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>var BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="<?= BASE_URL ?>/admin.js"></script>
</body>
</html>
