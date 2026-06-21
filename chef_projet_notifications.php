<?php
$pageTitle = $pageTitle ?? 'Notifications';
require __DIR__ . '/chef_projet_header.php';
require __DIR__ . '/chef_projet_navbar.php';
require __DIR__ . '/chef_projet_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$notifications = $data['notifications'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">
    <?php require __DIR__ . '/chef_projet_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Notifications</h1>
        <p class="page-description"><?= count($notifications) ?> notification(s)</p>
      </div>
      <?php if (!empty($notifications)): ?>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=notifications&action=mark-read" class="btn btn--outline"><i class="fas fa-check-double"></i> Tout marquer comme lu</a>
      </div>
      <?php endif ?>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <?php if (empty($notifications)): ?>
          <div class="empty-state"><i class="fas fa-bell empty-icon"></i><h3>Aucune notification</h3><p>Vous serez notifié des mises à jour importantes.</p></div>
        <?php else: ?>
          <?php foreach ($notifications as $n): ?>
          <div class="notification-row <?= empty($n['lu']) ? 'notification-row--unread' : '' ?>">
            <div class="notification-icon">
              <i class="fas fa-<?= $n['icone'] ?? 'bell' ?>"></i>
            </div>
            <div class="notification-content">
              <div class="notification-message"><?= Security::escape($n['message'] ?? '') ?></div>
              <div class="notification-time"><?= date('d/m/Y H:i', strtotime($n['created_at'] ?? 'now')) ?></div>
            </div>
            <div class="notification-action">
              <?php if (empty($n['lu'])): ?>
                <a href="<?= BASE_URL ?>/chef_projet_index.php?module=notifications&action=read&id=<?= $n['id'] ?>" class="btn btn--sm btn--ghost" title="Marquer comme lu"><i class="fas fa-check"></i></a>
              <?php endif ?>
            </div>
          </div>
          <?php endforeach ?>
        <?php endif ?>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/chef_projet_footer.php'; ?>
