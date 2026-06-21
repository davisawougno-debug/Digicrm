<?php
$pageTitle = 'Dashboard Commercial';
require __DIR__ . '/commercial_header.php';
require __DIR__ . '/commercial_navbar.php';
require __DIR__ . '/commercial_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$userId = Session::get('user_id');
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/commercial_alerts.php'; ?>

    <!-- Welcome Banner -->
    <div class="welcome-banner">
      <div class="welcome-text">
        <h1>Bonjour, <?= Security::escape(Session::get('user_prenom')) ?> 👋</h1>
        <p>Voici votre tableau de bord commercial du jour.</p>
      </div>
      <div class="welcome-datetime">
        <div class="welcome-date" id="currentDate"></div>
        <div class="welcome-time" id="currentTime"></div>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-icon blue"><i class="fas fa-user-plus"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $data['prospectsCount'] ?? 0 ?></div>
          <div class="stat-card-label">Mes prospects</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon green"><i class="fas fa-building"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $data['clientsCount'] ?? 0 ?></div>
          <div class="stat-card-label">Clients</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon purple"><i class="fas fa-file-invoice"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $data['devisEnvoyes'] ?? 0 ?></div>
          <div class="stat-card-label">Devis envoyés</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon success"><i class="fas fa-check-circle"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $data['devisAcceptes'] ?? 0 ?></div>
          <div class="stat-card-label">Devis acceptés</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon teal"><i class="fas fa-file-signature"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= $data['contratsCount'] ?? 0 ?></div>
          <div class="stat-card-label">Contrats signés</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon orange"><i class="fas fa-euro-sign"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= number_format($data['totalCA'] ?? 0, 0, ',', ' ') ?> €</div>
          <div class="stat-card-label">CA généré</div>
        </div>
      </div>
    </div>

    <!-- Pipeline -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fas fa-funnel-dollar"></i> Pipeline commercial</div>
      </div>
      <div class="card-body">
        <div class="pipeline">
          <?php
          $pipelineStages = [
            'nouveau'   => ['label' => 'Nouveau',      'color' => 'var(--gray-400)',   'icon' => 'fa-star'],
            'contacte'  => ['label' => 'Contacté',      'color' => 'var(--info)',       'icon' => 'fa-phone'],
            'qualifie'  => ['label' => 'Qualifié',      'color' => 'var(--primary)',    'icon' => 'fa-check'],
            'perdu'     => ['label' => 'Perdu',         'color' => 'var(--danger)',     'icon' => 'fa-times'],
            'converti'  => ['label' => 'Converti',      'color' => 'var(--success)',    'icon' => 'fa-user-check'],
          ];
          $pipelineCounts = $data['pipelineCounts'] ?? [];
          ?>
          <?php foreach ($pipelineStages as $stage => $info):
            $count = $pipelineCounts[$stage] ?? 0;
          ?>
          <div class="pipeline-stage">
            <div class="pipeline-stage-header" style="border-left-color: <?= $info['color'] ?>">
              <i class="fas <?= $info['icon'] ?>" style="color: <?= $info['color'] ?>"></i>
              <span class="pipeline-stage-label"><?= $info['label'] ?></span>
              <span class="pipeline-stage-count"><?= $count ?></span>
            </div>
            <div class="pipeline-stage-bar">
              <div class="pipeline-bar-fill" style="width: <?= max(5, ($data['prospectsCount'] ?? 1) > 0 ? ($count / max($data['prospectsCount'], 1)) * 100 : 0) ?>%; background: <?= $info['color'] ?>"></div>
            </div>
          </div>
          <?php endforeach ?>
        </div>
      </div>
    </div>

    <!-- Bottom grid: Recent items -->
    <div class="dashboard-bottom-grid">
      <!-- Recent Prospects -->
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-user-plus"></i> Derniers prospects</div>
          <a href="<?= BASE_URL ?>/commercial_index.php?module=prospects" class="card-header-link">Voir tout</a>
        </div>
        <div class="card-body p-0">
          <?php $recentProspects = $data['recentProspects'] ?? [] ?>
          <?php if (empty($recentProspects)): ?>
            <div class="empty-state">Aucun prospect pour le moment.</div>
          <?php else: ?>
            <div class="table-mini">
              <?php foreach ($recentProspects as $p): ?>
              <a href="<?= BASE_URL ?>/commercial_index.php?module=prospects&action=edit&id=<?= $p['id'] ?>" class="table-mini-row">
                <div class="table-mini-avatar"><?= mb_strtoupper(mb_substr($p['prenom'] ?? $p['entreprise'] ?? '?', 0, 1)) ?></div>
                <div class="table-mini-info">
                  <div class="table-mini-name"><?= Security::escape(($p['prenom'] ?? '') . ' ' . ($p['nom'] ?? '')) ?></div>
                  <div class="table-mini-sub"><?= Security::escape($p['email'] ?? '') ?> · <span class="badge badge--<?= $p['statut'] ?>"><?= $p['statut'] ?></span></div>
                </div>
                <div class="table-mini-action">
                  <i class="fas fa-chevron-right"></i>
                </div>
              </a>
              <?php endforeach ?>
            </div>
          <?php endif ?>
        </div>
      </div>

      <!-- Recent Devis -->
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-file-invoice"></i> Derniers devis</div>
          <a href="<?= BASE_URL ?>/commercial_index.php?module=devis" class="card-header-link">Voir tout</a>
        </div>
        <div class="card-body p-0">
          <?php $recentDevis = $data['recentDevis'] ?? [] ?>
          <?php if (empty($recentDevis)): ?>
            <div class="empty-state">Aucun devis récent.</div>
          <?php else: ?>
            <div class="table-mini">
              <?php foreach ($recentDevis as $d): ?>
              <a href="<?= BASE_URL ?>/commercial_index.php?module=devis&action=view&id=<?= $d['id'] ?>" class="table-mini-row">
                <div class="table-mini-avatar table-mini-avatar--alt"><i class="fas fa-file-invoice"></i></div>
                <div class="table-mini-info">
                  <div class="table-mini-name"><?= Security::escape($d['numero_devis'] ?? 'Devis #' . $d['id']) ?></div>
                  <div class="table-mini-sub"><?= number_format($d['montant_total'] ?? 0, 2) ?> € · <span class="badge badge--<?= $d['statut'] ?>"><?= $d['statut'] ?></span></div>
                </div>
                <div class="table-mini-action">
                  <i class="fas fa-chevron-right"></i>
                </div>
              </a>
              <?php endforeach ?>
            </div>
          <?php endif ?>
        </div>
      </div>
    </div>

    <!-- Recent Activities -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fas fa-history"></i> Activités récentes</div>
      </div>
      <div class="card-body p-0">
        <?php $activities = $data['recentActivities'] ?? [] ?>
        <?php if (empty($activities)): ?>
          <div class="empty-state">Aucune activité récente.</div>
        <?php else: ?>
          <div class="table-mini">
            <?php foreach ($activities as $a): ?>
            <div class="table-mini-row">
              <div class="table-mini-avatar table-mini-avatar--alt">
                <i class="fas fa-<?= match($a['action']) {
                  'creation' => 'plus-circle',
                  'modification' => 'edit',
                  'conversion' => 'user-check',
                  'connexion' => 'sign-in-alt',
                  default => 'circle'
                } ?>"></i>
              </div>
              <div class="table-mini-info">
                <div class="table-mini-name"><?= Security::escape($a['description'] ?? $a['action']) ?></div>
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
    var opts = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    var dateEl = document.getElementById('currentDate');
    var timeEl = document.getElementById('currentTime');
    if (dateEl) dateEl.textContent = now.toLocaleDateString('fr-FR', opts);
    if (timeEl) timeEl.textContent = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
  }
  updateDateTime();
  setInterval(updateDateTime, 1000);
});
</script>

<?php require __DIR__ . '/commercial_footer.php'; ?>
