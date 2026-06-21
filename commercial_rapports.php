<?php
$pageTitle = $pageTitle ?? 'Rapports';
require __DIR__ . '/commercial_header.php';
require __DIR__ . '/commercial_navbar.php';
require __DIR__ . '/commercial_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$prospectsParMois = $data['prospectsParMois'] ?? [];
$devisParMois = $data['devisParMois'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/commercial_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Rapports</h1>
        <p class="page-description">Analyse de votre activité commerciale</p>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-icon blue"><i class="fas fa-chart-line"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-label">Prospects (12 mois)</div>
          <div class="stat-card-number"><?= array_sum(array_column($prospectsParMois, 'total')) ?></div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon green"><i class="fas fa-euro-sign"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-label">CA (12 mois)</div>
          <div class="stat-card-number"><?= number_format(array_sum(array_column($devisParMois, 'ca')), 0, ',', ' ') ?> €</div>
        </div>
      </div>
    </div>

    <div class="dashboard-bottom-grid">
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-user-plus"></i> Prospects par mois</div>
        </div>
        <div class="card-body p-0">
          <?php if (empty($prospectsParMois)): ?>
            <div class="empty-state">Aucune donnée.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Mois</th>
                    <th>Prospects</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($prospectsParMois as $row): ?>
                  <tr>
                    <td><?= Security::escape($row['mois'] ?? '-') ?></td>
                    <td><?= (int)($row['total'] ?? 0) ?></td>
                  </tr>
                  <?php endforeach ?>
                </tbody>
              </table>
            </div>
          <?php endif ?>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-file-invoice"></i> Devis par mois</div>
        </div>
        <div class="card-body p-0">
          <?php if (empty($devisParMois)): ?>
            <div class="empty-state">Aucune donnée.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Mois</th>
                    <th>Devis</th>
                    <th>CA (€)</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($devisParMois as $row): ?>
                  <tr>
                    <td><?= Security::escape($row['mois'] ?? '-') ?></td>
                    <td><?= (int)($row['total'] ?? 0) ?></td>
                    <td><?= number_format((float)($row['ca'] ?? 0), 2) ?></td>
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
</div>
<?php require __DIR__ . '/commercial_footer.php'; ?>
