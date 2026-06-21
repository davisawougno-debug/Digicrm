<?php
$pageTitle = $pageTitle ?? 'Profil';
require __DIR__ . '/chef_projet_header.php';
require __DIR__ . '/chef_projet_navbar.php';
require __DIR__ . '/chef_projet_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$user = $data['user'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">
    <?php require __DIR__ . '/chef_projet_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Mon Profil</h1>
      </div>
    </div>

    <div class="profile-card">
      <div class="profile-card-header">
        <div class="profile-card-avatar">
          <?php if (!empty($user['avatar'])): ?>
            <img src="<?= Security::escape($user['avatar']) ?>" alt="Avatar">
          <?php else: ?>
            <div class="profile-card-initials"><?= strtoupper(substr($user['prenom'] ?? '', 0, 1) . substr($user['nom'] ?? '', 0, 1)) ?></div>
          <?php endif ?>
        </div>
        <div class="profile-card-info">
          <h2><?= Security::escape(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></h2>
          <p><?= Security::escape($user['email'] ?? '') ?></p>
          <span class="badge badge--primary">Chef de Projet</span>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><div class="card-title"><i class="fas fa-edit"></i> Modifier le profil</div></div>
      <div class="card-body">
        <form method="post" class="chef-form">
          <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Prénom</label>
              <input type="text" name="prenom" class="form-input" value="<?= Security::escape($user['prenom'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Nom</label>
              <input type="text" name="nom" class="form-input" value="<?= Security::escape($user['nom'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-input" value="<?= Security::escape($user['email'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Téléphone</label>
              <input type="text" name="telephone" class="form-input" value="<?= Security::escape($user['telephone'] ?? '') ?>">
            </div>
            <div class="form-group form-group--full">
              <label class="form-label">Nouveau mot de passe (laisser vide pour conserver)</label>
              <input type="password" name="password" class="form-input">
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn--primary"><i class="fas fa-save"></i> Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/chef_projet_footer.php'; ?>
