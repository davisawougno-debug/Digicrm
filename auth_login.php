<?php
/**
 * Vue : Page de connexion
 */

$pageTitle = 'Connexion';
require __DIR__ . '/partials_header.php';

$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);
$flashMessages = Session::getFlash();
?>

<main class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">&#9670;</div>
                <h1 class="login-title">DigiCRM</h1>
                <p class="login-subtitle">Gestion de la Relation Client</p>
            </div>

            <?php if (!empty($flashMessages)): ?>
                <?php foreach ($flashMessages as $flash): ?>
                    <div class="alert alert--<?= Security::escape($flash['type']) ?>">
                        <?= Security::escape($flash['message']) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/login.php" method="POST" class="login-form" novalidate>
                <?= Security::csrfField() ?>

                <div class="form-group">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-input <?= isset($errors['email']) ? 'form-input--error' : '' ?>"
                           placeholder="exemple@digicrm.com"
                           value="<?= Security::escape($_POST['email'] ?? '') ?>"
                           required
                           autofocus>
                    <?php if (isset($errors['email'])): ?>
                        <span class="form-error"><?= Security::escape($errors['email']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-input <?= isset($errors['password']) ? 'form-input--error' : '' ?>"
                           placeholder="Votre mot de passe"
                           required>
                    <?php if (isset($errors['password'])): ?>
                        <span class="form-error"><?= Security::escape($errors['password']) ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn--primary btn--full login-btn">
                    Se connecter
                </button>
            </form>

            <div class="login-footer">
                <p>&copy; <?= date('Y') ?> DigiCRM - Tous droits réservés</p>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/partials_footer.php'; ?>
