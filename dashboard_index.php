<?php
/**
 * Vue : Tableau de bord après connexion
 *
 * Affiche les informations personnelles et les accès rapides
 * en fonction du rôle de l'utilisateur.
 */

$pageTitle = 'Tableau de bord';
require __DIR__ . '/partials_header.php';
require __DIR__ . '/partials_sidebar.php';

$user = AuthMiddleware::getUser();
$role = $user['role'];
?>

<div class="main-content" id="mainContent">
    <div class="container">
        <?php require __DIR__ . '/partials_alerts.php'; ?>

        <header class="page-header">
            <div>
                <h1 class="page-title">Tableau de bord</h1>
                <p class="page-description">
                    Bienvenue, <?= Security::escape($user['prenom'] . ' ' . $user['nom']) ?> |
                    Rôle : <?= Security::escape($role) ?>
                    <?php if ($user['last_login']): ?>
                        | Dernière connexion : <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                    <?php endif; ?>
                </p>
            </div>
        </header>

        <div class="dashboard-grid">
            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">Informations personnelles</h2>
                </div>
                <div class="card__body">
                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-item__label">Nom complet</span>
                            <span class="info-item__value"><?= Security::escape($user['prenom'] . ' ' . $user['nom']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-item__label">Email</span>
                            <span class="info-item__value"><?= Security::escape($user['email']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-item__label">Téléphone</span>
                            <span class="info-item__value"><?= Security::escape($user['telephone'] ?? 'Non renseigné') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-item__label">Rôle</span>
                            <span class="info-item__value">
                                <span class="badge badge--<?= strtolower(str_replace(' ', '-', $role)) ?>">
                                    <?= Security::escape($role) ?>
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-item__label">Membre depuis</span>
                            <span class="info-item__value"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
                <div class="card__footer">
                    <a href="<?= BASE_URL ?>/profile.php" class="btn btn--outline">Modifier mon profil</a>
                    <a href="<?= BASE_URL ?>/change-password.php" class="btn btn--outline">Changer mon mot de passe</a>
                </div>
            </div>

            <?php if ($role === ROLE_ADMIN): ?>
                <div class="card">
                    <div class="card__header">
                        <h2 class="card__title">Administration</h2>
                    </div>
                    <div class="card__body card__body--center">
                        <div class="stat-cards">
                            <div class="stat-card">
                                <span class="stat-card__number"><?= User::countAll() ?></span>
                                <span class="stat-card__label">Utilisateurs</span>
                            </div>
                            <?php $counts = User::countByRole(); ?>
                            <?php foreach ($counts as $roleName => $count): ?>
                                <div class="stat-card">
                                    <span class="stat-card__number"><?= $count ?></span>
                                    <span class="stat-card__label"><?= Security::escape($roleName) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="card__footer">
                        <a href="<?= BASE_URL ?>/users.php" class="btn btn--primary">Gérer les utilisateurs</a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">Actions rapides</h2>
                </div>
                <div class="card__body">
                    <div class="quick-actions">
                        <a href="<?= BASE_URL ?>/profile.php" class="quick-action">
                            <span class="quick-action__icon">&#9998;</span>
                            <span class="quick-action__text">Modifier mon profil</span>
                        </a>
                        <a href="<?= BASE_URL ?>/change-password.php" class="quick-action">
                            <span class="quick-action__icon">&#128274;</span>
                            <span class="quick-action__text">Changer mon mot de passe</span>
                        </a>
                        <a href="<?= BASE_URL ?>/logout.php" class="quick-action">
                            <span class="quick-action__icon">&#10140;</span>
                            <span class="quick-action__text">Se déconnecter</span>
                        </a>
                    </div>
                </div>
            </div>

            <?php $activities = ActivityLog::getByUser($user['id'], 5); ?>
            <?php if (!empty($activities)): ?>
                <div class="card">
                    <div class="card__header">
                        <h2 class="card__title">Dernières activités</h2>
                    </div>
                    <div class="card__body">
                        <ul class="activity-list">
                            <?php foreach ($activities as $activity): ?>
                                <li class="activity-item">
                                    <span class="activity-item__action badge badge--activity badge--<?= Security::escape($activity['action']) ?>">
                                        <?= Security::escape($activity['action']) ?>
                                    </span>
                                    <span class="activity-item__desc"><?= Security::escape($activity['description'] ?? '') ?></span>
                                    <span class="activity-item__date"><?= date('d/m/Y H:i', strtotime($activity['created_at'])) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials_footer.php'; ?>
