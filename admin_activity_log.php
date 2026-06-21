<?php
$pageTitle = "Journal d'activités";
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$activities = $data['activities'] ?? [];
$actionTypes = $data['actionTypes'] ?? [];
$users = $data['users'] ?? [];
$totalPages = $data['totalPages'] ?? 1;
$currentPage = $data['page'] ?? 1;

$actionBadgeColors = [
  'connexion' => 'green',
  'deconnexion' => 'blue',
  'creation' => 'blue',
  'modification' => 'orange',
  'suppression' => 'red',
  'statut' => 'purple',
];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Journal d'activités</h1>
    </div>

    <div class="card" style="margin-bottom:20px">
      <div class="card-body">
        <form method="GET" action="admin.php" class="form-row">
          <input type="hidden" name="module" value="activity-log">

          <div class="form-group">
            <label for="filter_action" class="form-label">Type d'action</label>
            <select id="filter_action" name="action" class="form-input">
              <option value="">Toutes les actions</option>
              <?php foreach ($actionTypes as $type): ?>
                <option value="<?= Security::escape($type) ?>" <?= (($_GET['action'] ?? '') === $type) ? 'selected' : '' ?>>
                  <?= Security::escape(ucfirst($type)) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="filter_user" class="form-label">Utilisateur</label>
            <select id="filter_user" name="user_id" class="form-input">
              <option value="">Tous les utilisateurs</option>
              <?php foreach ($users as $user): ?>
                <option value="<?= (int)($user['id'] ?? 0) ?>" <?= ((int)($_GET['user_id'] ?? 0) === (int)($user['id'] ?? 0)) ? 'selected' : '' ?>>
                  <?= Security::escape(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="filter_date_start" class="form-label">Date début</label>
            <input type="date" id="filter_date_start" name="date_start" class="form-input"
                   value="<?= Security::escape($_GET['date_start'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label for="filter_date_end" class="form-label">Date fin</label>
            <input type="date" id="filter_date_end" name="date_end" class="form-input"
                   value="<?= Security::escape($_GET['date_end'] ?? '') ?>">
          </div>

          <div class="form-group" style="align-self:flex-end">
            <button type="submit" class="btn btn--primary">
              <i class="fas fa-search"></i> Filtrer
            </button>
            <a href="<?= BASE_URL ?>/admin_index.php?module=activity-log" class="btn btn--outline">
              <i class="fas fa-undo"></i> Réinitialiser
            </a>
          </div>
        </form>
      </div>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Date/Heure</th>
            <th>Utilisateur</th>
            <th>Action</th>
            <th>Module</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($activities)): ?>
            <?php foreach ($activities as $a): ?>
            <tr>
              <td><?= $a['created_at'] ? date('d/m/Y H:i:s', strtotime($a['created_at'])) : '-' ?></td>
              <td><?= Security::escape(($a['user_prenom'] ?? '') . ' ' . ($a['user_nom'] ?? '')) ?></td>
              <td>
                <?php $badgeColor = $actionBadgeColors[$a['action'] ?? ''] ?? 'gray'; ?>
                <span class="badge badge--<?= Security::escape($badgeColor) ?>">
                  <?= Security::escape(ucfirst($a['action'] ?? 'other')) ?>
                </span>
              </td>
              <td><?= Security::escape($a['module'] ?? '-') ?></td>
              <td><?= Security::escape($a['description'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center">Aucune activité trouvée.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="<?= BASE_URL ?>/admin_index.php?module=activity-log&page=<?= $i ?><?= isset($_GET['action']) ? '&action=' . Security::escape($_GET['action']) : '' ?><?= isset($_GET['user_id']) ? '&user_id=' . (int)$_GET['user_id'] : '' ?><?= isset($_GET['date_start']) ? '&date_start=' . Security::escape($_GET['date_start']) : '' ?><?= isset($_GET['date_end']) ? '&date_end=' . Security::escape($_GET['date_end']) : '' ?>"
           class="pagination-link <?= $currentPage === $i ? 'pagination-link--active' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

  </div>
</div>

<?php require __DIR__ . '/admin_footer.php'; ?>
