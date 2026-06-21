<?php
$pageTitle = 'Notifications';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$notifications = $data['notifications'] ?? [];
$users = $data['users'] ?? [];
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);

$typeColors = [
  'info' => 'blue',
  'success' => 'green',
  'warning' => 'orange',
  'danger' => 'red',
];
$typeIcons = [
  'info' => 'fa-info-circle',
  'success' => 'fa-check-circle',
  'warning' => 'fa-exclamation-triangle',
  'danger' => 'fa-times-circle',
];
$typeLabels = [
  'info' => 'Information',
  'success' => 'Succès',
  'warning' => 'Avertissement',
  'danger' => 'Erreur',
];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Notifications</h1>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/admin_index.php?module=notifications&action=mark-all-read" class="btn btn--outline" onclick="return confirm('Tout marquer comme lu ?')">
          <i class="fas fa-check-double"></i> Tout marquer comme lu
        </a>
        <button class="btn btn--primary" onclick="openNotifModal()">
          <i class="fas fa-plus"></i> Nouvelle notification
        </button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Titre</th>
            <th>Message</th>
            <th>Type</th>
            <th>Destinataire</th>
            <th>Lu</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $n): ?>
            <tr class="<?= empty($n['lu']) ? 'unread-row' : '' ?>">
              <td><strong><?= Security::escape($n['titre'] ?? '') ?></strong></td>
              <td>
                <?= Security::escape(mb_substr($n['message'] ?? '', 0, 100)) ?>
                <?= mb_strlen($n['message'] ?? '') > 100 ? '...' : '' ?>
              </td>
              <td>
                <?php $type = $n['type'] ?? 'info'; ?>
                <span class="badge badge--<?= Security::escape($typeColors[$type] ?? 'gray') ?>">
                  <i class="fas <?= Security::escape($typeIcons[$type] ?? 'fa-bell') ?>"></i>
                  <?= Security::escape($typeLabels[$type] ?? ucfirst($type)) ?>
                </span>
              </td>
              <td><?= Security::escape(($n['destinataire_prenom'] ?? '') . ' ' . ($n['destinataire_nom'] ?? '') ?: 'Globale') ?></td>
              <td>
                <?php if (!empty($n['lu'])): ?>
                  <i class="fas fa-check-circle" style="color:#1cc88a" title="Lu"></i>
                <?php else: ?>
                  <i class="fas fa-circle" style="color:#e74a3b" title="Non lu"></i>
                <?php endif; ?>
              </td>
              <td><?= $n['created_at'] ? date('d/m/Y H:i', strtotime($n['created_at'])) : '-' ?></td>
              <td class="actions-cell">
                <a href="<?= BASE_URL ?>/admin_index.php?module=notifications&action=mark-read&id=<?= (int)($n['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Marquer comme lu">
                  <i class="fas fa-check"></i>
                </a>
                <a href="#" class="btn btn--sm btn--outline btn--danger" title="Supprimer"
                   onclick="event.preventDefault(); openDeleteModal(<?= (int)($n['id'] ?? 0) ?>, 'Notification : <?= Security::escape(addslashes($n['titre'] ?? '')) ?>')">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center">Aucune notification.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<div class="modal" id="notifModal">
  <div class="modal-backdrop" onclick="closeModal('notifModal')"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Nouvelle notification</h3>
        <button class="modal-close" onclick="closeModal('notifModal')">&times;</button>
      </div>
      <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=notifications&action=create" class="modal-form">
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

        <div class="modal-body">
          <div class="form-group">
            <label for="notif_user_id" class="form-label">Destinataire</label>
            <select id="notif_user_id" name="user_id" class="form-control">
              <option value="">Tous les utilisateurs</option>
              <?php foreach ($users as $user): ?>
                <option value="<?= (int)($user['id'] ?? 0) ?>">
                  <?= Security::escape(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '') . ' (' . ($user['email'] ?? '') . ')') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="notif_titre" class="form-label">Titre</label>
            <input type="text" id="notif_titre" name="titre" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="notif_message" class="form-label">Message</label>
            <textarea id="notif_message" name="message" class="form-control" rows="4" required></textarea>
          </div>

          <div class="form-group">
            <label for="notif_type" class="form-label">Type</label>
            <select id="notif_type" name="type" class="form-control" required>
              <?php foreach ($typeLabels as $key => $label): ?>
                <option value="<?= Security::escape($key) ?>"><?= Security::escape($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn--outline" onclick="closeModal('notifModal')">Annuler</button>
          <button type="submit" class="btn btn--primary">
            <i class="fas fa-save"></i> Envoyer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openNotifModal() {
  openModal('notifModal');
}

function openDeleteModal(id, name) {
  document.getElementById('deleteModalMessage').textContent = 'Êtes-vous sûr de vouloir supprimer "' + name + '" ? Cette action est irréversible.';
  document.getElementById('deleteModalConfirm').href = '<?= BASE_URL ?>/admin_index.php?module=notifications&action=delete&id=' + id;
  openModal('deleteModal');
}
</script>

<?php require __DIR__ . '/admin_modals.php'; ?>
<?php require __DIR__ . '/admin_footer.php'; ?>
