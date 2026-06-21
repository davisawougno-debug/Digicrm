<?php
$pageTitle = $pageTitle ?? 'Clients';
require __DIR__ . '/chef_projet_header.php';
require __DIR__ . '/chef_projet_navbar.php';
require __DIR__ . '/chef_projet_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$clients = $data['clients'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">
    <?php require __DIR__ . '/chef_projet_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Clients</h1>
        <p class="page-description"><?= count($clients) ?> client(s)</p>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <?php if (empty($clients)): ?>
          <div class="empty-state"><i class="fas fa-users empty-icon"></i><h3>Aucun client</h3></div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="admin-table">
            <thead><tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Entreprise</th><th>Statut</th></tr></thead>
            <tbody>
              <?php foreach ($clients as $c): ?>
              <tr>
                <td><strong><?= Security::escape($c['prenom'] . ' ' . $c['nom']) ?></strong></td>
                <td><?= Security::escape($c['email'] ?? '-') ?></td>
                <td><?= Security::escape($c['telephone'] ?? '-') ?></td>
                <td><?= Security::escape($c['entreprise'] ?? '-') ?></td>
                <td><span class="badge badge--<?= $c['statut'] ?? 'actif' ?>"><?= $c['statut'] ?? 'actif' ?></span></td>
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
