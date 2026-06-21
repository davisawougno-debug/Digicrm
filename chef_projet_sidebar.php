<?php if (defined('AJAX_REQUEST') && AJAX_REQUEST) return; ?>
<aside class="admin-sidebar chef-sidebar" id="chefSidebar">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar-user-card">
        <div class="sidebar-user-avatar">
            <?= mb_strtoupper(mb_substr(Session::get('user_prenom'), 0, 1)) . mb_strtoupper(mb_substr(Session::get('user_nom'), 0, 1)) ?>
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name"><?= Security::escape(Session::get('user_prenom') . ' ' . Session::get('user_nom')) ?></div>
            <div class="sidebar-user-role">Chef de Projet</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'dashboard') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/chef_projet_index.php" class="sidebar-link">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="sidebar-divider"><span>Gestion</span></li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'projets') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/chef_projet_index.php?module=projets" class="sidebar-link">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projets</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'taches') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/chef_projet_index.php?module=taches" class="sidebar-link">
                    <i class="fas fa-tasks"></i>
                    <span>Tâches</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'equipes') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/chef_projet_index.php?module=equipes" class="sidebar-link">
                    <i class="fas fa-users"></i>
                    <span>Équipes</span>
                </a>
            </li>

            <li class="sidebar-divider"><span>Projets</span></li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'livrables') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/chef_projet_index.php?module=livrables" class="sidebar-link">
                    <i class="fas fa-box"></i>
                    <span>Livrables</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'calendrier') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/chef_projet_index.php?module=calendrier" class="sidebar-link">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Calendrier</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'clients') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/chef_projet_index.php?module=clients" class="sidebar-link">
                    <i class="fas fa-building"></i>
                    <span>Clients</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'contrats') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/chef_projet_index.php?module=contrats" class="sidebar-link">
                    <i class="fas fa-file-signature"></i>
                    <span>Contrats</span>
                </a>
            </li>

            <li class="sidebar-divider"><span>Analytique</span></li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'notifications') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/chef_projet_index.php?module=notifications" class="sidebar-link">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'rapports') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/chef_projet_index.php?module=rapports" class="sidebar-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Rapports</span>
                </a>
            </li>

            <li class="sidebar-item <?= (($_GET['module'] ?? 'dashboard') === 'profil') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/chef_projet_index.php?module=profil" class="sidebar-link">
                    <i class="fas fa-user-cog"></i>
                    <span>Mon profil</span>
                </a>
            </li>

            <li class="sidebar-divider"></li>

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
