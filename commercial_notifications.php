<?php
$pageTitle = $pageTitle ?? 'Notifications';
require __DIR__ . '/commercial_header.php';
require __DIR__ . '/commercial_navbar.php';
require __DIR__ . '/commercial_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$notifications = $data['notifications'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/commercial_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Notifications</h1>
        <p class="page-description"><?= count($notifications) ?> notification(s)</p>
      </div>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/commercial_index.php?module=notifications&action=mark-all-read" class="btn btn--outline">
          <i class="fas fa-check-double"></i> Tout marquer comme lu
        </a>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <?php if (empty($notifications)): ?>
          <div class="empty-state">
            <i class="fas fa-bell empty-icon"></i>
            <h3>Aucune notification</h3>
            <p>Vous serez notifié des nouveaux prospects, devis acceptés et rendez-vous.</p>
          </div>
        <?php else: ?>
          <div class="table-mini">
            <?php foreach ($notifications as $n): ?>
            <div class="table-mini-row <?= empty($n['lu']) ? 'table-mini-row--unread' : '' ?>">
              <div class="table-mini-avatar table-mini-avatar--alt">
                <i class="fas fa-<?= match($n['type'] ?? 'info') {
                  'success' => 'check-circle',
                  'warning' => 'exclamation-triangle',
                  'error'   => 'times-circle',
                  default   => 'bell',
                } ?>" style="color: <?= match($n['type'] ?? 'info') {
                  'success' => 'var(--success)',
                  'warning' => 'var(--warning)',
                  'error'   => 'var(--danger)',
                  default   => 'var(--primary)',
                } ?>"></i>
              </div>
              <div class="table-mini-info">
                <div class="table-mini-name"><?= Security::escape($n['titre'] ?? 'Notification') ?></div>
                <div class="table-mini-sub"><?= Security::escape($n['message'] ?? '') ?> · <?= date('d/m/Y H:i', strtotime($n['created_at'] ?? 'now')) ?></div>
              </div>
              <?php if (empty($n['lu'])): ?>
              <div class="table-mini-action">
                <a href="<?= BASE_URL ?>/commercial_index.php?module=notifications&action=mark-read&id=<?= $n['id'] ?>" class="btn btn--sm btn--ghost" title="Marquer comme lu">
                  <i class="fas fa-check"></i>
                </a>
              </div>
              <?php endif ?>
            </div>
            <?php endforeach ?>
          </div>
        <?php endif ?>
      </div>
    </div>

  </div>
</div>
<?php require __DIR__ . '/commercial_footer.php'; ?>
