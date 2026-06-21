<?php
$pageTitle = $pageTitle ?? 'Rendez-vous';
require __DIR__ . '/commercial_header.php';
require __DIR__ . '/commercial_navbar.php';
require __DIR__ . '/commercial_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$rendezVous = $data['rendezVous'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/commercial_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Rendez-vous</h1>
        <p class="page-description"><?= count($rendezVous) ?> rendez-vous</p>
      </div>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/admin_index.php?module=prospects&action=create" class="btn btn--primary">
          <i class="fas fa-plus"></i> Planifier un rendez-vous
        </a>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <?php if (empty($rendezVous)): ?>
          <div class="empty-state">
            <i class="fas fa-calendar-check empty-icon"></i>
            <h3>Aucun rendez-vous</h3>
            <p>Planifiez votre premier rendez-vous.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Heure</th>
                  <th>Lieu</th>
                  <th>Motif</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($rendezVous as $r): ?>
                <tr>
                  <td><?= date('d/m/Y', strtotime($r['date_rdv'])) ?></td>
                  <td><?= Security::escape($r['heure'] ?? '-') ?></td>
                  <td><?= Security::escape($r['lieu'] ?? '-') ?></td>
                  <td><?= Security::escape(mb_substr($r['motif'] ?? '', 0, 60)) ?></td>
                  <td><span class="badge badge--<?= $r['statut'] ?? 'planifie' ?>"><?= $r['statut'] ?? 'planifie' ?></span></td>
                </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          </div>
        <?php endif ?>
      </div>
    </div>

  </div>
</div>
<?php require __DIR__ . '/commercial_footer.php'; ?>
