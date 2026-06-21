<?php
$pageTitle = $pageTitle ?? 'Mon profil';
require __DIR__ . '/commercial_header.php';
require __DIR__ . '/commercial_navbar.php';
require __DIR__ . '/commercial_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$user = $data['user'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/commercial_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Mon profil</h1>
        <p class="page-description">Gérez vos informations personnelles</p>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="profile-header">
          <div class="profile-avatar">
            <?= mb_strtoupper(mb_substr($user['prenom'] ?? '?', 0, 1)) . mb_strtoupper(mb_substr($user['nom'] ?? '?', 0, 1)) ?>
          </div>
          <div class="profile-info">
            <h2><?= Security::escape(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></h2>
            <p><?= Security::escape($user['email'] ?? '') ?> · <?= $user['role'] ?? 'commercial' ?></p>
          </div>
        </div>

        <form method="post" class="commercial-form">
          <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Prénom</label>
              <input type="text" name="prenom" class="form-input" value="<?= Security::escape($user['prenom'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Nom</label>
              <input type="text" name="nom" class="form-input" value="<?= Security::escape($user['nom'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-input" value="<?= Security::escape($user['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Téléphone</label>
              <input type="tel" name="telephone" class="form-input" value="<?= Security::escape($user['telephone'] ?? '') ?>">
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" name="action" value="update" class="btn btn--primary"><i class="fas fa-save"></i> Mettre à jour</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
<?php require __DIR__ . '/commercial_footer.php'; ?>
