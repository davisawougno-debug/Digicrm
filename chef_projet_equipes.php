<?php
$pageTitle = $pageTitle ?? 'Équipes';
require __DIR__ . '/chef_projet_header.php';
require __DIR__ . '/chef_projet_navbar.php';
require __DIR__ . '/chef_projet_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$membres = $data['membres'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">
    <?php require __DIR__ . '/chef_projet_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Équipes</h1>
        <p class="page-description"><?= count($membres) ?> membre(s)</p>
      </div>
    </div>

    <?php if (empty($membres)): ?>
      <div class="empty-state"><i class="fas fa-users empty-icon"></i><h3>Aucun membre</h3><p>Les employés apparaîtront ici.</p></div>
    <?php else: ?>
    <div class="team-grid">
      <?php foreach ($membres as $m): ?>
      <div class="team-card">
        <div class="team-card-avatar">
          <?php if (!empty($m['avatar'])): ?>
            <img src="<?= Security::escape($m['avatar']) ?>" alt="Avatar">
          <?php else: ?>
            <div class="team-card-avatar-initials"><?= strtoupper(substr($m['prenom'] ?? '', 0, 1) . substr($m['nom'] ?? '', 0, 1)) ?></div>
          <?php endif ?>
        </div>
        <div class="team-card-info">
          <div class="team-card-name"><?= Security::escape($m['prenom'] . ' ' . $m['nom']) ?></div>
          <div class="team-card-email"><?= Security::escape($m['email'] ?? '') ?></div>
          <div class="team-card-stats">
            <span><strong><?= (int)($m['taches_encours'] ?? 0) ?></strong> en cours</span>
            <span><strong><?= (int)($m['taches_terminees'] ?? 0) ?></strong> terminées</span>
          </div>
          <?php if (isset($m['charge_travail'])): ?>
          <div class="progress-sm">
            <div class="progress-sm-bar"><div class="progress-sm-fill" style="width:<?= min(100, (int)$m['charge_travail']) ?>%"></div></div>
            <span class="progress-sm-label"><?= min(100, (int)$m['charge_travail']) ?>%</span>
          </div>
          <?php endif ?>
        </div>
        <div class="team-card-status">
          <span class="status-dot <?= ($m['statut'] ?? 'actif') === 'actif' ? 'status-dot--active' : '' ?>"></span>
          <?= $m['statut'] ?? 'actif' ?>
        </div>
      </div>
      <?php endforeach ?>
    </div>
    <?php endif ?>
  </div>
</div>
<?php require __DIR__ . '/chef_projet_footer.php'; ?>
