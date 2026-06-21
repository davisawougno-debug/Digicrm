<?php
$pageTitle = $pageTitle ?? 'Contrats';
require __DIR__ . '/chef_projet_header.php';
require __DIR__ . '/chef_projet_navbar.php';
require __DIR__ . '/chef_projet_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$contrats = $data['contrats'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">
    <?php require __DIR__ . '/chef_projet_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Contrats</h1>
        <p class="page-description"><?= count($contrats) ?> contrat(s)</p>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <?php if (empty($contrats)): ?>
          <div class="empty-state"><i class="fas fa-file-contract empty-icon"></i><h3>Aucun contrat</h3></div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="admin-table">
            <thead><tr><th>Réf</th><th>Client</th><th>Montant</th><th>Date début</th><th>Date fin</th><th>Statut</th></tr></thead>
            <tbody>
              <?php foreach ($contrats as $c): ?>
              <tr>
                <td><strong>#<?= $c['id'] ?></strong></td>
                <td><?= Security::escape($c['client'] ?? '-') ?></td>
                <td><?= number_format((float)($c['montant'] ?? 0), 2, ',', ' ') ?> €</td>
                <td class="text-muted"><?= !empty($c['date_debut']) ? date('d/m/Y', strtotime($c['date_debut'])) : '-' ?></td>
                <td class="text-muted"><?= !empty($c['date_fin']) ? date('d/m/Y', strtotime($c['date_fin'])) : '-' ?></td>
                <td><span class="badge badge--<?= $c['statut'] ?? 'en_cours' ?>"><?= $c['statut'] ?? 'en_cours' ?></span></td>
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
<?php require __DIR__ . '/chef_projet_footer.php'; ?>
