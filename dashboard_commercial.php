<?php
require_once __DIR__ . '/app.php';
require_once __DIR__ . '/database.php';
require_once HELPERS_PATH . '/Session.php';
require_once MODELS_PATH . '/User.php';
require_once MIDDLEWARE_PATH . '/AuthMiddleware.php';
require_once MIDDLEWARE_PATH . '/RoleMiddleware.php';
Session::start();
AuthMiddleware::check();
RoleMiddleware::require([ROLE_ADMIN, ROLE_COMMERCIAL]);
$pageTitle = 'Dashboard Commercial';
$userId = $_SESSION['user_id'];

$stmt = getPDO()->prepare("SELECT COUNT(*) FROM prospects WHERE assigned_to = :id");
$stmt->execute([':id' => $userId]);
$prospects = $stmt->fetchColumn();

$clients = getPDO()->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$devis = getPDO()->query("SELECT COUNT(*) FROM devis")->fetchColumn();
$factures = getPDO()->query("SELECT COUNT(*) FROM invoices")->fetchColumn();

$devisRecents = getPDO()->query("SELECT d.*, c.entreprise FROM devis d LEFT JOIN clients c ON d.client_id = c.id ORDER BY d.created_at DESC LIMIT 5")->fetchAll();
$prospectsRecents = getPDO()->prepare("SELECT * FROM prospects WHERE assigned_to = :id ORDER BY created_at DESC LIMIT 5");
$prospectsRecents->execute([':id' => $userId]);
$prospectsRecents = $prospectsRecents->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr" data-theme="<?= isset($_COOKIE['digicrm-theme']) ? htmlspecialchars($_COOKIE['digicrm-theme']) : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - DigiCRM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin.css">
    <style>
        .admin-navbar { position: relative; }
        .admin-sidebar { margin-top: 0; height: 100vh; top: 0; }
        .commercial-content { min-height: calc(100vh - var(--navbar-height)); }
        .quick-actions { display: flex; gap: 12px; flex-wrap: wrap; }
        .quick-actions .btn { flex: 1; min-width: 140px; justify-content: center; }
    </style>
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
                    <span class="navbar-user-role">Commercial</span>
                </div>
            </div>
            <a href="logout.php" class="btn btn--danger btn--sm" style="margin-left:8px;">Déconnexion</a>
        </div>
    </header>

    <main class="admin-main commercial-content" style="margin-left:0;">
        <div class="admin-container">
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h1>Bonjour, <?= htmlspecialchars($_SESSION['user_prenom']) ?> 👋</h1>
                    <p>Bienvenue sur votre espace commercial – suivez vos prospects et vos ventes.</p>
                </div>
                <div class="welcome-datetime">
                    <div class="welcome-date"><?= date('l d F Y') ?></div>
                    <div class="welcome-time"><?= date('H:i') ?></div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-icon blue"><i class="fas fa-user-plus"></i></div>
                    <div class="stat-card-info">
                        <div class="stat-card-number"><?= $prospects ?></div>
                        <div class="stat-card-label">Mes prospects</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon green"><i class="fas fa-building"></i></div>
                    <div class="stat-card-info">
                        <div class="stat-card-number"><?= $clients ?></div>
                        <div class="stat-card-label">Clients</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon purple"><i class="fas fa-file-invoice"></i></div>
                    <div class="stat-card-info">
                        <div class="stat-card-number"><?= $devis ?></div>
                        <div class="stat-card-label">Devis</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon orange"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div class="stat-card-info">
                        <div class="stat-card-number"><?= $factures ?></div>
                        <div class="stat-card-label">Factures</div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom:20px;">
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="admin/index.php?module=prospects&action=create" class="btn btn--primary"><i class="fas fa-plus"></i> Nouveau prospect</a>
                        <a href="admin/index.php?module=clients&action=create" class="btn btn--outline"><i class="fas fa-plus"></i> Nouveau client</a>
                        <a href="admin/index.php?module=devis&action=create" class="btn btn--outline"><i class="fas fa-plus"></i> Nouveau devis</a>
                        <a href="admin/index.php?module=prospects" class="btn btn--secondary"><i class="fas fa-list"></i> Tous les prospects</a>
                    </div>
                </div>
            </div>

            <div class="dashboard-bottom-grid">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-file-invoice"></i> Devis récents</div>
                        <a href="admin/index.php?module=devis" class="card-header-link">Voir tout</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($devisRecents)): ?>
                            <div class="empty-state">Aucun devis récent.</div>
                        <?php else: ?>
                            <div class="table-mini">
                                <?php foreach ($devisRecents as $d): ?>
                                <a href="admin/index.php?module=devis&action=view&id=<?= $d['id'] ?>" class="table-mini-row" style="text-decoration:none;">
                                    <div class="table-mini-avatar table-mini-avatar--alt"><i class="fas fa-file-invoice"></i></div>
                                    <div class="table-mini-info">
                                        <div class="table-mini-name"><?= htmlspecialchars($d['entreprise'] ?? 'Client #' . $d['client_id']) ?></div>
                                        <div class="table-mini-sub"><?= number_format($d['montant_total'], 2) ?> € · <span class="badge badge--<?= $d['statut'] ?>"><?= htmlspecialchars($d['statut']) ?></span></div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-user-plus"></i> Mes prospects</div>
                        <a href="admin/index.php?module=prospects" class="card-header-link">Voir tout</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($prospectsRecents)): ?>
                            <div class="empty-state">Aucun prospect assigné.</div>
                        <?php else: ?>
                            <div class="table-mini">
                                <?php foreach ($prospectsRecents as $p): ?>
                                <a href="admin/index.php?module=prospects&action=view&id=<?= $p['id'] ?>" class="table-mini-row" style="text-decoration:none;">
                                    <div class="table-mini-avatar"><?= mb_strtoupper(mb_substr($p['prenom'] ?? $p['entreprise'] ?? '?', 0, 1)) ?></div>
                                    <div class="table-mini-info">
                                        <div class="table-mini-name"><?= htmlspecialchars(($p['prenom'] ?? '') . ' ' . ($p['nom'] ?? $p['entreprise'] ?? '')) ?></div>
                                        <div class="table-mini-sub"><?= htmlspecialchars($p['email'] ?? '') ?> · <span class="badge badge--<?= $p['statut'] ?>"><?= htmlspecialchars($p['statut']) ?></span></div>
                                    </div>
                                </a>
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
                        <a href="admin/index.php?module=prospects" class="stat-card" style="text-decoration:none;">
                            <div class="stat-card-icon blue"><i class="fas fa-user-plus"></i></div>
                            <div class="stat-card-info">
                                <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Prospects</div>
                                <div class="stat-card-label-sub">Suivez et qualifiez vos prospects</div>
                            </div>
                        </a>
                        <a href="admin/index.php?module=clients" class="stat-card" style="text-decoration:none;">
                            <div class="stat-card-icon green"><i class="fas fa-building"></i></div>
                            <div class="stat-card-info">
                                <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Clients</div>
                                <div class="stat-card-label-sub">Consultez le portefeuille client</div>
                            </div>
                        </a>
                        <a href="admin/index.php?module=devis" class="stat-card" style="text-decoration:none;">
                            <div class="stat-card-icon purple"><i class="fas fa-file-invoice"></i></div>
                            <div class="stat-card-info">
                                <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Devis</div>
                                <div class="stat-card-label-sub">Créez et gérez vos devis</div>
                            </div>
                        </a>
                        <a href="admin/index.php?module=factures" class="stat-card" style="text-decoration:none;">
                            <div class="stat-card-icon orange"><i class="fas fa-file-invoice-dollar"></i></div>
                            <div class="stat-card-info">
                                <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Factures</div>
                                <div class="stat-card-label-sub">Suivez la facturation</div>
                            </div>
                        </a>
                        <a href="admin/index.php?module=contrats" class="stat-card" style="text-decoration:none;">
                            <div class="stat-card-icon teal"><i class="fas fa-file-signature"></i></div>
                            <div class="stat-card-info">
                                <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Contrats</div>
                                <div class="stat-card-label-sub">Gérez les contrats</div>
                            </div>
                        </a>
                        <a href="admin/index.php?module=paiements" class="stat-card" style="text-decoration:none;">
                            <div class="stat-card-icon pink"><i class="fas fa-credit-card"></i></div>
                            <div class="stat-card-info">
                                <div class="stat-card-label" style="font-weight:600;color:var(--gray-800);">Paiements</div>
                                <div class="stat-card-label-sub">Suivez les paiements</div>
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
