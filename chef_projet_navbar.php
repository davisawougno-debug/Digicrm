<?php if (defined('AJAX_REQUEST') && AJAX_REQUEST) return; ?>
<header class="admin-navbar chef-navbar" id="chefNavbar">
    <div class="navbar-left">
        <button class="navbar-toggle" id="sidebarToggle" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
        <a href="<?= BASE_URL ?>/chef_projet_index.php" class="navbar-brand">
            <span class="navbar-logo"><i class="fas fa-diamond"></i></span>
            <span class="navbar-brand-text">DigiCRM</span>
            <span class="navbar-brand-badge">Chef de Projet</span>
        </a>
    </div>

    <div class="navbar-center">
        <div class="navbar-search">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="navbar-search-input" id="globalSearch" placeholder="Rechercher projets, tâches, membres..." autocomplete="off">
            <div class="search-dropdown" id="searchResults"></div>
        </div>
    </div>

    <div class="navbar-right">
        <div class="navbar-datetime">
            <span id="currentDate"></span>
            <span class="navbar-datetime-sep">|</span>
            <span id="currentTime"></span>
        </div>

        <button class="navbar-icon-btn" id="themeToggle" title="Mode sombre">
            <i class="fas fa-moon"></i>
        </button>

        <div class="navbar-notif-container">
            <button class="navbar-icon-btn" id="notifBtn" title="Notifications">
                <i class="fas fa-bell"></i>
                <?php $notifCount = $GLOBALS['notifCount'] ?? 0; ?>
                <?php if ($notifCount > 0): ?>
                    <span class="navbar-badge"><?= $notifCount > 99 ? '99+' : $notifCount ?></span>
                <?php endif ?>
            </button>
        </div>

        <div class="navbar-user" id="userMenuBtn">
            <div class="navbar-user-avatar">
                <?= mb_strtoupper(mb_substr(Session::get('user_prenom'), 0, 1)) . mb_strtoupper(mb_substr(Session::get('user_nom'), 0, 1)) ?>
            </div>
            <div class="navbar-user-info">
                <span class="navbar-user-name"><?= Security::escape(Session::get('user_prenom') . ' ' . Session::get('user_nom')) ?></span>
                <span class="navbar-user-role">Chef de Projet</span>
            </div>
            <i class="fas fa-chevron-down navbar-user-arrow"></i>
            <div class="navbar-dropdown" id="userDropdown">
                <a href="<?= BASE_URL ?>/chef_projet_index.php?module=profil" class="dropdown-item">
                    <i class="fas fa-user-cog"></i> Mon profil
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= BASE_URL ?>/logout.php" class="dropdown-item dropdown-item--danger">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </div>
</header>
