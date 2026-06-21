<?php
$data = $GLOBALS['viewData'] ?? [];
$isEdit = isset($data['user']);
$pageTitle = $isEdit ? 'Modifier utilisateur' : 'Nouvel utilisateur';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$user = $data['user'] ?? [];
$roles = unserialize(ROLES_LIST);
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);

$roleLabels = [
  'admin' => 'Administrateur',
  'commercial' => 'Commercial',
  'chef_projet' => 'Chef de projet',
  'employe' => 'Employé',
];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title"><?= Security::escape($pageTitle) ?></h1>
      <a href="<?= BASE_URL ?>/admin_index.php?module=users" class="btn btn--outline">
        <i class="fas fa-arrow-left"></i> Retour
      </a>
    </div>

    <div class="card">
      <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=users&action=<?= $isEdit ? 'edit&id=' . (int)($user['id'] ?? 0) : 'create' ?>" class="admin-form">
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

          <div class="form-grid">
            <div class="form-group">
              <label for="nom" class="form-label">Nom</label>
              <input type="text" id="nom" name="nom" class="form-control"
                     value="<?= Security::escape($user['nom'] ?? '') ?>" required>
            </div>

            <div class="form-group">
              <label for="prenom" class="form-label">Prénom</label>
              <input type="text" id="prenom" name="prenom" class="form-control"
                     value="<?= Security::escape($user['prenom'] ?? '') ?>" required>
            </div>

            <div class="form-group">
              <label for="email" class="form-label">Email</label>
              <input type="email" id="email" name="email" class="form-control"
                     value="<?= Security::escape($user['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
              <label for="telephone" class="form-label">Téléphone</label>
              <input type="tel" id="telephone" name="telephone" class="form-control"
                     value="<?= Security::escape($user['telephone'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label for="role" class="form-label">Rôle</label>
              <select id="role" name="role" class="form-control" required>
                <?php foreach ($roles as $role): ?>
                  <option value="<?= Security::escape($role) ?>" <?= ($user['role'] ?? ROLE_EMPLOYE) === $role ? 'selected' : '' ?>>
                    <?= Security::escape($roleLabels[$role] ?? $role) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <?php if (!$isEdit): ?>
            <div class="form-group">
              <label for="password" class="form-label">Mot de passe</label>
              <input type="password" id="password" name="password" class="form-control"
                     <?= $isEdit ? '' : 'required' ?>>
            </div>
            <?php endif; ?>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn--primary">
              <i class="fas fa-save"></i> <?= $isEdit ? 'Enregistrer' : 'Créer' ?>
            </button>
            <a href="<?= BASE_URL ?>/admin_index.php?module=users" class="btn btn--outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<?php require __DIR__ . '/admin_footer.php'; ?>
