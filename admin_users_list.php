<?php
$pageTitle = 'Gestion des utilisateurs';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$users = $data['users'] ?? [];

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
      <h1 class="page-title">Gestion des utilisateurs</h1>
      <a href="<?= BASE_URL ?>/admin_index.php?module=users&action=create" class="btn btn--primary">
        <i class="fas fa-plus"></i> Nouvel utilisateur
      </a>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Photo</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Statut</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
            <tr>
              <td>
                <div class="avatar avatar--sm">
                  <?= Security::escape(mb_strtoupper(mb_substr($user['prenom'] ?? '', 0, 1)) . mb_strtoupper(mb_substr($user['nom'] ?? '', 0, 1))) ?>
                </div>
              </td>
              <td><?= Security::escape($user['nom'] ?? '') ?></td>
              <td><?= Security::escape($user['prenom'] ?? '') ?></td>
              <td><?= Security::escape($user['email'] ?? '') ?></td>
              <td><span class="badge badge--role"><?= Security::escape($roleLabels[$user['role'] ?? ''] ?? $user['role'] ?? '') ?></span></td>
              <td>
                <?php if (($user['statut'] ?? '') === 'actif'): ?>
                  <span class="badge badge--actif">Actif</span>
                <?php else: ?>
                  <span class="badge badge--inactif">Inactif</span>
                <?php endif; ?>
              </td>
              <td><?= date('d/m/Y', strtotime($user['created_at'] ?? '')) ?></td>
              <td class="actions-cell">
                <a href="<?= BASE_URL ?>/admin_index.php?module=users&action=edit&id=<?= (int)($user['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Modifier">
                  <i class="fas fa-edit"></i>
                </a>
                <a href="<?= BASE_URL ?>/admin_index.php?module=users&action=toggle-status&id=<?= (int)($user['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Activer/Désactiver">
                  <i class="fas fa-<?= ($user['statut'] ?? '') === 'actif' ? 'ban' : 'check' ?>"></i>
                </a>
                <a href="#" class="btn btn--sm btn--outline btn--danger" title="Supprimer"
                   onclick="event.preventDefault(); openDeleteModal(<?= (int)($user['id'] ?? 0) ?>, '<?= Security::escape(addslashes(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''))) ?>')">
                  <i class="fas fa-trash"></i>
                </a>
                <a href="<?= BASE_URL ?>/admin_index.php?module=users&action=reset-password&id=<?= (int)($user['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Réinitialiser mot de passe"
                   onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur ?')">
                  <i class="fas fa-key"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center">Aucun utilisateur trouvé.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if (($data['pages'] ?? 1) > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $data['pages']; $i++): ?>
        <a href="<?= BASE_URL ?>/admin_index.php?module=users&action=list&page=<?= $i ?>"
           class="pagination-link <?= ($data['page'] ?? 1) === $i ? 'pagination-link--active' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

  </div>
</div>

<script>
function openDeleteModal(id, name) {
  document.getElementById('deleteModalMessage').textContent = 'Êtes-vous sûr de vouloir supprimer l\'utilisateur "' + name + '" ? Cette action est irréversible.';
  document.getElementById('deleteModalConfirm').href = '<?= BASE_URL ?>/admin_index.php?module=users&action=delete&id=' + id;
  openModal('deleteModal');
}
</script>

<?php require __DIR__ . '/admin_modals.php'; ?>
<?php require __DIR__ . '/admin_footer.php'; ?>
