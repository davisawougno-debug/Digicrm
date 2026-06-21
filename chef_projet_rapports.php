<?php
$pageTitle = $pageTitle ?? 'Rapports';
require __DIR__ . '/chef_projet_header.php';
require __DIR__ . '/chef_projet_navbar.php';
require __DIR__ . '/chef_projet_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$mois = $data['mois'] ?? date('m');
$annee = $data['annee'] ?? date('Y');
$tachesStat = $data['tachesStat'] ?? [];
$projetsStat = $data['projetsStat'] ?? [];
$employesStat = $data['employesStat'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">
    <?php require __DIR__ . '/chef_projet_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Rapports</h1>
        <p class="page-description">Analyses mensuelles</p>
      </div>
      <div class="page-header-actions">
        <form method="get" class="form-inline">
          <input type="hidden" name="module" value="rapports">
          <select name="mois" class="form-input form-input--sm">
            <?php $moisNoms = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre']; ?>
            <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= $m == $mois ? 'selected' : '' ?>><?= $moisNoms[$m - 1] ?></option>
            <?php endfor ?>
          </select>
          <select name="annee" class="form-input form-input--sm">
            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
            <option value="<?= $y ?>" <?= $y == $annee ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor ?>
          </select>
          <button type="submit" class="btn btn--outline"><i class="fas fa-filter"></i> Filtrer</button>
        </form>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-icon blue"><i class="fas fa-tasks"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $tachesStat['total'] ?? 0 ?></div>
          <div class="stat-card-label">Tâches créées</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon success"><i class="fas fa-check-circle"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $tachesStat['terminees'] ?? 0 ?></div>
          <div class="stat-card-label">Tâches terminées</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon green"><i class="fas fa-rocket"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $projetsStat['total'] ?? 0 ?></div>
          <div class="stat-card-label">Projets actifs</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon purple"><i class="fas fa-users"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $employesStat['total'] ?? 0 ?></div>
          <div class="stat-card-label">Employés actifs</div>
        </div>
      </div>
    </div>

    <!-- Table par employé -->
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="fas fa-user-check"></i> Productivité par employé</div></div>
      <div class="card-body p-0">
        <?php $employes = $employesStat['liste'] ?? [] ?>
        <?php if (empty($employes)): ?>
          <div class="empty-state">Aucune donnée pour cette période.</div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="admin-table">
            <thead><tr><th>Employé</th><th>Tâches assignées</th><th>Tâches terminées</th><th>Taux complétion</th></tr></thead>
            <tbody>
              <?php foreach ($employes as $e): ?>
              <?php $pct = ($e['assignees'] ?? 0) > 0 ? round(($e['terminees'] ?? 0) / $e['assignees'] * 100) : 0; ?>
              <tr>
                <td><?= Security::escape($e['prenom'] . ' ' . $e['nom']) ?></td>
                <td><?= (int)($e['assignees'] ?? 0) ?></td>
                <td><?= (int)($e['terminees'] ?? 0) ?></td>
                <td>
                  <div class="progress-sm">
                    <div class="progress-sm-bar"><div class="progress-sm-fill" style="width:<?= $pct ?>%"></div></div>
                    <span class="progress-sm-label"><?= $pct ?>%</span>
                  </div>
                </td>
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
