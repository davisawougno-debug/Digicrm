<?php
$pageTitle = 'Dashboard Chef de Projet';
require __DIR__ . '/chef_projet_header.php';
require __DIR__ . '/chef_projet_navbar.php';
require __DIR__ . '/chef_projet_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/chef_projet_alerts.php'; ?>

    <!-- Welcome -->
    <div class="welcome-banner">
      <div class="welcome-text">
        <h1>Bonjour, <?= Security::escape(Session::get('user_prenom')) ?> 👋</h1>
        <p>Vue d'ensemble de vos projets et équipes.</p>
      </div>
      <div class="welcome-datetime">
        <div class="welcome-date" id="currentDate"></div>
        <div class="welcome-time" id="currentTime"></div>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-icon blue"><i class="fas fa-rocket"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $data['projetsActifs'] ?? 0 ?></div>
          <div class="stat-card-label">Projets actifs</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $data['projetsTermines'] ?? 0 ?></div>
          <div class="stat-card-label">Projets terminés</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon purple"><i class="fas fa-tasks"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $data['totalTaches'] ?? 0 ?></div>
          <div class="stat-card-label">Tâches totales</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon success"><i class="fas fa-clipboard-check"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $data['tachesTerminees'] ?? 0 ?></div>
          <div class="stat-card-label">Tâches terminées</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon orange"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $data['tachesEnRetard'] ?? 0 ?></div>
          <div class="stat-card-label">Tâches en retard</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon teal"><i class="fas fa-users"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $data['employesActifs'] ?? 0 ?></div>
          <div class="stat-card-label">Membres actifs</div>
        </div>
      </div>
    </div>

    <!-- Projects Progress -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fas fa-chart-line"></i> Progression des projets</div>
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=projets" class="card-header-link">Voir tout</a>
      </div>
      <div class="card-body">
        <?php $progression = $data['progressionProjets'] ?? [] ?>
        <?php if (empty($progression)): ?>
          <div class="empty-state">Aucun projet pour le moment.</div>
        <?php else: ?>
          <div class="progress-list">
            <?php foreach ($progression as $p): ?>
            <div class="progress-item">
              <div class="progress-item-header">
                <span class="progress-item-name"><?= Security::escape($p['nom_projet']) ?></span>
                <span class="progress-item-pct"><?= (int)$p['progression'] ?>%</span>
              </div>
              <div class="progress-item-bar">
                <div class="progress-fill" style="width: <?= (int)$p['progression'] ?>%"></div>
              </div>
              <span class="badge badge--<?= $p['statut'] ?>"><?= $p['statut'] ?></span>
            </div>
            <?php endforeach ?>
          </div>
        <?php endif ?>
      </div>
    </div>

    <!-- Bottom Grid: Recent Projects + Recent Deliverables -->
    <div class="dashboard-bottom-grid">
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-project-diagram"></i> Projets récents</div>
          <a href="<?= BASE_URL ?>/chef_projet_index.php?module=projets" class="card-header-link">Voir tout</a>
        </div>
        <div class="card-body p-0">
          <?php $projetsRecents = $data['projetsRecents'] ?? [] ?>
          <?php if (empty($projetsRecents)): ?>
            <div class="empty-state">Aucun projet.</div>
          <?php else: ?>
            <div class="table-mini">
              <?php foreach ($projetsRecents as $p): ?>
              <a href="<?= BASE_URL ?>/chef_projet_index.php?module=projets&action=view&id=<?= $p['id'] ?>" class="table-mini-row">
                <div class="table-mini-avatar table-mini-avatar--alt"><i class="fas fa-project-diagram"></i></div>
                <div class="table-mini-info">
                  <div class="table-mini-name"><?= Security::escape($p['nom_projet']) ?></div>
                  <div class="table-mini-sub"><span class="badge badge--<?= $p['statut'] ?>"><?= $p['statut'] ?></span> · <?= (int)$p['progression'] ?>%</div>
                </div>
                <div class="table-mini-action"><i class="fas fa-chevron-right"></i></div>
              </a>
              <?php endforeach ?>
            </div>
          <?php endif ?>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-box"></i> Derniers livrables</div>
          <a href="<?= BASE_URL ?>/chef_projet_index.php?module=livrables" class="card-header-link">Voir tout</a>
        </div>
        <div class="card-body p-0">
          <?php $delivs = $data['deliverablesRecents'] ?? [] ?>
          <?php if (empty($delivs)): ?>
            <div class="empty-state">Aucun livrable.</div>
          <?php else: ?>
            <div class="table-mini">
              <?php foreach ($delivs as $d): ?>
              <div class="table-mini-row">
                <div class="table-mini-avatar table-mini-avatar--alt"><i class="fas fa-box"></i></div>
                <div class="table-mini-info">
                  <div class="table-mini-name"><?= Security::escape($d['titre']) ?></div>
                  <div class="table-mini-sub"><?= Security::escape($d['nom_projet'] ?? '') ?> · <span class="badge badge--<?= $d['statut'] ?>"><?= $d['statut'] ?></span></div>
                </div>
              </div>
              <?php endforeach ?>
            </div>
          <?php endif ?>
        </div>
      </div>
    </div>

    <!-- Activities -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fas fa-history"></i> Activités récentes</div>
      </div>
      <div class="card-body p-0">
        <?php $activities = $data['recentActivities'] ?? [] ?>
        <?php if (empty($activities)): ?>
          <div class="empty-state">Aucune activité.</div>
        <?php else: ?>
          <div class="table-mini">
            <?php foreach ($activities as $a): ?>
            <div class="table-mini-row">
              <div class="table-mini-avatar table-mini-avatar--alt"><i class="fas fa-circle"></i></div>
              <div class="table-mini-info">
                <div class="table-mini-name"><?= Security::escape($a['description'] ?? $a['action'] ?? '') ?></div>
                <div class="table-mini-sub"><?= date('d/m/Y H:i', strtotime($a['created_at'] ?? 'now')) ?></div>
              </div>
            </div>
            <?php endforeach ?>
          </div>
        <?php endif ?>
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  function updateDateTime() {
    var now = new Date();
    var dateEl = document.getElementById('currentDate');
    var timeEl = document.getElementById('currentTime');
    if (dateEl) dateEl.textContent = now.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    if (timeEl) timeEl.textContent = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
  }
  updateDateTime();
  setInterval(updateDateTime, 1000);
});
</script>

<?php require __DIR__ . '/chef_projet_footer.php'; ?>
