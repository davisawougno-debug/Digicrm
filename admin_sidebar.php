<?php if (defined('AJAX_REQUEST') && AJAX_REQUEST) return; ?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'dashboard') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php" class="sidebar-link">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="sidebar-divider"><span>Gestion</span></li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'users') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=users" class="sidebar-link">
                    <i class="fas fa-users-cog"></i>
                    <span>Utilisateurs</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'prospects') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=prospects" class="sidebar-link">
                    <i class="fas fa-user-plus"></i>
                    <span>Prospects</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'clients') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=clients" class="sidebar-link">
                    <i class="fas fa-building"></i>
                    <span>Clients</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'services') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=services" class="sidebar-link">
                    <i class="fas fa-concierge-bell"></i>
                    <span>Services</span>
                </a>
            </li>

            <li class="sidebar-divider"><span>Commercial</span></li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'devis') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=devis" class="sidebar-link">
                    <i class="fas fa-file-invoice"></i>
                    <span>Devis</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'contrats') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=contrats" class="sidebar-link">
                    <i class="fas fa-file-signature"></i>
                    <span>Contrats</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'projets') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=projets" class="sidebar-link">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projets</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'taches') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=taches" class="sidebar-link">
                    <i class="fas fa-tasks"></i>
                    <span>Tâches</span>
                </a>
            </li>

            <li class="sidebar-divider"><span>Finance</span></li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'factures') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=factures" class="sidebar-link">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Factures</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'paiements') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=paiements" class="sidebar-link">
                    <i class="fas fa-credit-card"></i>
                    <span>Paiements</span>
                </a>
            </li>

            <li class="sidebar-divider"><span>Analytique</span></li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'notifications') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=notifications" class="sidebar-link">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'activity-log') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=activity-log" class="sidebar-link">
                    <i class="fas fa-history"></i>
                    <span>Journal d'activités</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'rapports') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=rapports" class="sidebar-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Rapports</span>
                </a>
            </li>

            <li class="sidebar-divider"><span>Système</span></li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'parametres') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/admin_index.php?module=parametres" class="sidebar-link">
                    <i class="fas fa-cogs"></i>
                    <span>Paramètres</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a href="<?= BASE_URL ?>/logout.php" class="sidebar-link sidebar-link--danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-footer-info">
            <i class="fas fa-diamond"></i>
            <span>DigiCRM v1.0</span>
        </div>
    </div>
</aside>
