<?php
$pageTitle = 'Paramètres de la plateforme';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$parametres = $data['parametres'] ?? [];
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Paramètres de la plateforme</h1>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=parametres&action=save" class="settings-form" enctype="multipart/form-data">
      <?= Security::csrfField() ?>

      <?php if (!empty($errors)): ?>
      <div class="admin-alert admin-alert--error">
        <i class="fas fa-times-circle"></i>
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= Security::escape($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-building"></i> Informations de l'agence</h3>
        </div>
        <div class="card-body">
          <div class="form-row">
            <div class="form-group">
              <label for="param_nom_agence" class="form-label">Nom de l'agence</label>
              <input type="text" id="param_nom_agence" name="nom_agence" class="form-input"
                     value="<?= Security::escape($parametres['nom_agence'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label for="param_email" class="form-label">Email</label>
              <input type="email" id="param_email" name="email" class="form-input"
                     value="<?= Security::escape($parametres['email'] ?? '') ?>">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="param_telephone" class="form-label">Téléphone</label>
              <input type="text" id="param_telephone" name="telephone" class="form-input"
                     value="<?= Security::escape($parametres['telephone'] ?? '') ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="param_adresse" class="form-label">Adresse</label>
            <textarea id="param_adresse" name="adresse" class="form-input" rows="3"><?= Security::escape($parametres['adresse'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-image"></i> Logo</h3>
        </div>
        <div class="card-body">
          <?php if (!empty($parametres['logo'])): ?>
          <div style="margin-bottom:15px">
            <img src="<?= Security::escape(BASE_URL . '/uploads/' . $parametres['logo']) ?>" alt="Logo" style="max-height:80px">
          </div>
          <?php endif; ?>
          <div class="form-group">
            <label for="param_logo" class="form-label">Changer le logo</label>
            <input type="file" id="param_logo" name="logo" class="form-input" accept="image/*">
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-cog"></i> Configuration</h3>
        </div>
        <div class="card-body">
          <div class="form-row">
            <div class="form-group">
              <label for="param_fuseau_horaire" class="form-label">Fuseau horaire</label>
              <input type="text" id="param_fuseau_horaire" name="fuseau_horaire" class="form-input"
                     value="<?= Security::escape($parametres['fuseau_horaire'] ?? 'Africa/Douala') ?>">
            </div>
            <div class="form-group">
              <label for="param_langue" class="form-label">Langue</label>
              <select id="param_langue" name="langue" class="form-input">
                <option value="fr" <?= (($parametres['langue'] ?? 'fr') === 'fr') ? 'selected' : '' ?>>Français</option>
                <option value="en" <?= (($parametres['langue'] ?? 'fr') === 'en') ? 'selected' : '' ?>>English</option>
              </select>
            </div>
            <div class="form-group">
              <label for="param_devise" class="form-label">Devise</label>
              <input type="text" id="param_devise" name="devise" class="form-input"
                     value="<?= Security::escape($parametres['devise'] ?? 'FCFA') ?>">
            </div>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn--primary">
          <i class="fas fa-save"></i> Enregistrer les paramètres
        </button>
      </div>
    </form>

  </div>
</div>

<?php require __DIR__ . '/admin_footer.php'; ?>
