<?php if (defined('AJAX_REQUEST') && AJAX_REQUEST) return; ?>
<header class="admin-navbar" id="adminNavbar">
    <div class="navbar-left">
        <button class="navbar-toggle" id="sidebarToggle" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
        <a href="<?= BASE_URL ?>/admin_index.php" class="navbar-brand">
            <span class="navbar-logo"><i class="fas fa-diamond"></i></span>
            <span class="navbar-brand-text">DigiCRM</span>
        </a>
        <div class="navbar-search">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="globalSearch" class="navbar-search-input"
                   placeholder="Rechercher clients, prospects, projets..."
                   autocomplete="off">
            <div id="searchResults" class="search-dropdown"></div>
        </div>
    </div>

    <div class="navbar-right">
        <button class="navbar-icon-btn" id="themeToggle" title="Mode sombre">
            <i class="fas fa-moon"></i>
        </button>

        <button class="navbar-icon-btn" id="notifBtn" title="Notifications">
            <i class="fas fa-bell"></i>
            <span class="navbar-badge" id="notifBadge">
                <?php $notifCount = Notification::countNonLu(Session::get('user_id'));
                echo $notifCount > 0 ? $notifCount : ''; ?>
            </span>
        </button>

        <button class="navbar-icon-btn" title="Messages">
            <i class="fas fa-envelope"></i>
        </button>

        <div class="navbar-user" id="userMenuBtn">
            <div class="navbar-user-avatar">
                <?= mb_strtoupper(mb_substr(Session::get('user_prenom'), 0, 1)) . mb_strtoupper(mb_substr(Session::get('user_nom'), 0, 1)) ?>
            </div>
            <div class="navbar-user-info">
                <span class="navbar-user-name"><?= Security::escape(Session::get('user_prenom') . ' ' . Session::get('user_nom')) ?></span>
                <span class="navbar-user-role"><?= Security::escape(Session::get('user_role')) ?></span>
            </div>
            <i class="fas fa-chevron-down navbar-user-arrow"></i>

            <div class="navbar-dropdown" id="userDropdown">
                <a href="<?= BASE_URL ?>/profile.php" class="dropdown-item">
                    <i class="fas fa-user"></i> Mon profil
                </a>
                <a href="<?= BASE_URL ?>/admin_index.php?module=parametres" class="dropdown-item">
                    <i class="fas fa-cog"></i> Paramètres
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= BASE_URL ?>/logout.php" class="dropdown-item dropdown-item--danger">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </div>

    <!-- Notifications Panel -->
    <div class="notif-panel" id="notifPanel">
        <div class="notif-panel-header">
            <h3>Notifications</h3>
            <a href="<?= BASE_URL ?>/admin_index.php?module=notifications&action=mark-all-read"
               class="notif-mark-all">Tout marquer comme lu</a>
        </div>
        <div class="notif-panel-body" id="notifPanelBody">
            <?php
            $notifs = Notification::getNonLu(Session::get('user_id'));
            if (!empty($notifs)): foreach ($notifs as $n):
            ?>
                <div class="notif-item">
                    <div class="notif-item-icon notif-item-icon--<?= Security::escape($n['type']) ?>">
                        <i class="fas fa-<?= $n['type'] === 'success' ? 'check-circle' : ($n['type'] === 'warning' ? 'exclamation-triangle' : ($n['type'] === 'danger' ? 'times-circle' : 'info-circle')) ?>"></i>
                    </div>
                    <div class="notif-item-content">
                        <span class="notif-item-title"><?= Security::escape($n['titre']) ?></span>
                        <span class="notif-item-text"><?= Security::escape($n['message']) ?></span>
                        <span class="notif-item-time"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></span>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="notif-empty">Aucune notification</div>
            <?php endif; ?>
        </div>
        <div class="notif-panel-footer">
            <a href="<?= BASE_URL ?>/admin_index.php?module=notifications">Voir toutes les notifications</a>
        </div>
    </div>
</header>
