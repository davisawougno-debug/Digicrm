<?php
$pageTitle = 'Dashboard';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$stats = $data;
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <!-- Welcome Banner -->
    <div class="welcome-banner">
      <div class="welcome-text">
        <h1>Bienvenue, <?= Security::escape(Session::get('user_prenom')) ?> 👋</h1>
        <p>Voici le résumé de votre activité DigiCRM aujourd'hui.</p>
      </div>
      <div class="welcome-datetime">
        <div class="welcome-date" id="currentDate"></div>
        <div class="welcome-time" id="currentTime"></div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card" data-evolution="<?= $stats['statsEvolution']['users'] ?? 0 ?>">
        <div class="stat-card-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= (int)($stats['usersCount'] ?? 0) ?></div>
          <div class="stat-card-label">Utilisateurs</div>
          <div class="stat-card-evolution"><?= ($stats['statsEvolution']['users'] ?? 0) > 0 ? '+' : '' ?><?= $stats['statsEvolution']['users'] ?? 0 ?>%</div>
        </div>
      </div>
      <div class="stat-card" data-evolution="<?= $stats['statsEvolution']['prospects'] ?? 0 ?>">
        <div class="stat-card-icon green"><i class="fas fa-user-plus"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= (int)($stats['prospectsCount'] ?? 0) ?></div>
          <div class="stat-card-label">Prospects</div>
          <div class="stat-card-evolution"><?= ($stats['statsEvolution']['prospects'] ?? 0) > 0 ? '+' : '' ?><?= $stats['statsEvolution']['prospects'] ?? 0 ?>%</div>
        </div>
      </div>
      <div class="stat-card" data-evolution="<?= $stats['statsEvolution']['clients'] ?? 0 ?>">
        <div class="stat-card-icon purple"><i class="fas fa-building"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= (int)($stats['clientsCount'] ?? 0) ?></div>
          <div class="stat-card-label">Clients</div>
          <div class="stat-card-evolution"><?= ($stats['statsEvolution']['clients'] ?? 0) > 0 ? '+' : '' ?><?= $stats['statsEvolution']['clients'] ?? 0 ?>%</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon orange"><i class="fas fa-project-diagram"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= (int)($stats['projetsCount'] ?? 0) ?></div>
          <div class="stat-card-label">Projets</div>
          <div class="stat-card-label-sub"><?= (int)($stats['projetsEnCours'] ?? 0) ?> en cours · <?= (int)($stats['projetsTermines'] ?? 0) ?> terminés</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon teal"><i class="fas fa-file-signature"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= (int)($stats['contratsCount'] ?? 0) ?></div>
          <div class="stat-card-label">Contrats</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon indigo"><i class="fas fa-file-invoice"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= (int)($stats['devisCount'] ?? 0) ?></div>
          <div class="stat-card-label">Devis</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon red"><i class="fas fa-file-invoice-dollar"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= (int)($stats['facturesCount'] ?? 0) ?></div>
          <div class="stat-card-label">Factures</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon pink"><i class="fas fa-tasks"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= (int)($stats['tachesCount'] ?? 0) ?></div>
          <div class="stat-card-label">Tâches</div>
          <div class="stat-card-label-sub"><?= (int)($stats['tachesEnRetard'] ?? 0) ?> en retard</div>
        </div>
      </div>
    </div>

    <!-- Financial Stats -->
    <div class="stats-grid stats-grid-3">
      <div class="stat-card stat-card--accent">
        <div class="stat-card-icon cyan"><i class="fas fa-chart-line"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= number_format((float)($stats['totalSales'] ?? 0), 0, ',', ' ') ?> FCFA</div>
          <div class="stat-card-label">Chiffre d'affaires</div>
        </div>
      </div>
      <div class="stat-card stat-card--accent">
        <div class="stat-card-icon green"><i class="fas fa-wallet"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= number_format((float)($stats['totalCollected'] ?? 0), 0, ',', ' ') ?> FCFA</div>
          <div class="stat-card-label">Encaissé</div>
        </div>
      </div>
      <div class="stat-card stat-card--accent">
        <div class="stat-card-icon red"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= (int)($stats['unpaidCount'] ?? 0) ?></div>
          <div class="stat-card-label">Factures impayées</div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-chart-line"></i> Évolution des ventes</h3>
          <div class="card-header-actions">
            <span class="card-badge">6 mois</span>
          </div>
        </div>
        <div class="card-body">
          <canvas id="salesChart" height="200"></canvas>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-users"></i> Évolution des clients</h3>
          <div class="card-header-actions">
            <span class="card-badge">6 mois</span>
          </div>
        </div>
        <div class="card-body">
          <canvas id="clientsChart" height="200"></canvas>
        </div>
      </div>
    </div>

    <div class="charts-grid">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-project-diagram"></i> Évolution des projets</h3>
          <div class="card-header-actions">
            <span class="card-badge">6 mois</span>
          </div>
        </div>
        <div class="card-body">
          <canvas id="projectsChart" height="200"></canvas>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-chart-pie"></i> Services les plus demandés</h3>
        </div>
        <div class="card-body">
          <canvas id="servicesChart" height="200"></canvas>
        </div>
      </div>
    </div>

    <!-- Tables and Activity -->
    <div class="dashboard-bottom-grid">
      <!-- Recent Activities -->
      <div class="card card--full">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-history"></i> Activités récentes</h3>
          <a href="admin/index.php?module=activity-log" class="card-header-link">Voir tout</a>
        </div>
        <div class="card-body p-0">
          <div class="activity-feed">
            <?php $activities = $stats['recentActivities'] ?? []; ?>
            <?php if (!empty($activities)): ?>
              <?php foreach ($activities as $a): ?>
                <div class="activity-item">
                  <div class="activity-icon activity-icon--<?= Security::escape($a['action'] ?? 'info') ?>">
                    <i class="fas fa-<?= str_contains($a['action'] ?? '', 'creation') ? 'plus-circle' : (str_contains($a['action'] ?? '', 'suppression') ? 'trash' : (str_contains($a['action'] ?? '', 'connexion') ? 'sign-in-alt' : (str_contains($a['action'] ?? '', 'paiement') ? 'money-bill' : 'circle'))) ?>"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-text">
                      <strong><?= Security::escape(($a['prenom'] ?? '') . ' ' . ($a['nom'] ?? 'Système')) ?></strong>
                      <span><?= Security::escape($a['description'] ?? $a['action'] ?? '') ?></span>
                    </div>
                    <div class="activity-time"><?= date('d/m/Y H:i', strtotime($a['created_at'] ?? 'now')) ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="empty-state">Aucune activité récente.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Recent Users -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-users"></i> Derniers utilisateurs</h3>
          <a href="admin/index.php?module=users" class="card-header-link">Voir tout</a>
        </div>
        <div class="card-body p-0">
          <div class="table-mini">
            <?php $users = $stats['recentUsers'] ?? []; ?>
            <?php if (!empty($users)): ?>
              <?php foreach ($users as $u): ?>
                <div class="table-mini-row">
                  <div class="table-mini-avatar"><?= mb_strtoupper(mb_substr($u['prenom'] ?? '', 0, 1)) . mb_strtoupper(mb_substr($u['nom'] ?? '', 0, 1)) ?></div>
                  <div class="table-mini-info">
                    <div class="table-mini-name"><?= Security::escape(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? '')) ?></div>
                    <div class="table-mini-sub"><?= Security::escape($u['email'] ?? '') ?></div>
                  </div>
                  <span class="badge badge--<?= Security::escape($u['role'] ?? '') ?>"><?= Security::escape(match($u['role'] ?? '') { 'admin' => 'Admin', 'commercial' => 'Commercial', 'chef_projet' => 'Chef', 'employe' => 'Employé', default => $u['role'] ?? '' }) ?></span>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="empty-state">Aucun utilisateur.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="dashboard-bottom-grid">
      <!-- Recent Clients -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-building"></i> Derniers clients</h3>
          <a href="admin/index.php?module=clients" class="card-header-link">Voir tout</a>
        </div>
        <div class="card-body p-0">
          <div class="table-mini">
            <?php $clients = $stats['recentClients'] ?? []; ?>
            <?php if (!empty($clients)): ?>
              <?php foreach ($clients as $c): ?>
                <div class="table-mini-row">
                  <div class="table-mini-avatar table-mini-avatar--alt"><?= mb_strtoupper(mb_substr($c['prenom'] ?? ($c['nom'] ?? ''), 0, 1)) ?></div>
                  <div class="table-mini-info">
                    <div class="table-mini-name"><?= Security::escape(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')) ?></div>
                    <div class="table-mini-sub"><?= Security::escape($c['email'] ?? $c['societe'] ?? '') ?></div>
                  </div>
                  <span class="badge badge--client">Client</span>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="empty-state">Aucun client.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Recent Projects -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-project-diagram"></i> Derniers projets</h3>
          <a href="admin/index.php?module=projets" class="card-header-link">Voir tout</a>
        </div>
        <div class="card-body p-0">
          <div class="table-mini">
            <?php $projets = $stats['recentProjets'] ?? []; ?>
            <?php if (!empty($projets)): ?>
              <?php foreach ($projets as $p): ?>
                <div class="table-mini-row">
                  <div class="table-mini-info">
                    <div class="table-mini-name"><?= Security::escape($p['nom_projet'] ?? '') ?></div>
                    <div class="table-mini-sub"><?= date('d/m/Y', strtotime($p['created_at'] ?? 'now')) ?></div>
                  </div>
                  <span class="badge badge--<?= Security::escape($p['statut'] ?? '') ?>"><?= Security::escape(match($p['statut'] ?? '') { 'en_attente' => 'En attente', 'en_cours' => 'En cours', 'termine' => 'Terminé', 'bloque' => 'Bloqué', default => $p['statut'] ?? '' }) ?></span>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="empty-state">Aucun projet.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Notifications -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-bell"></i> Notifications</h3>
          <a href="admin/index.php?module=notifications" class="card-header-link">Voir tout</a>
        </div>
        <div class="card-body p-0">
          <div class="activity-feed">
            <?php $notifs = $stats['recentNotifications'] ?? []; ?>
            <?php if (!empty($notifs)): ?>
              <?php foreach ($notifs as $n): ?>
                <div class="activity-item">
                  <div class="activity-icon activity-icon--<?= Security::escape($n['type'] ?? 'info') ?>">
                    <i class="fas fa-<?= $n['type'] === 'success' ? 'check-circle' : ($n['type'] === 'warning' ? 'exclamation-triangle' : ($n['type'] === 'danger' ? 'times-circle' : 'info-circle')) ?>"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-text">
                      <strong><?= Security::escape($n['titre'] ?? '') ?></strong>
                      <span><?= Security::escape($n['message'] ?? '') ?></span>
                    </div>
                    <div class="activity-time"><?= date('d/m/Y H:i', strtotime($n['created_at'] ?? 'now')) ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="empty-state">Aucune notification.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Rendez-vous -->
    <?php if (!empty($stats['rendezVous'])): ?>
    <div class="card mt-4">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Prochains rendez-vous</h3>
      </div>
      <div class="card-body">
        <div class="appointments-list">
          <?php foreach ($stats['rendezVous'] as $rv): ?>
            <div class="appointment-item">
              <div class="appointment-date">
                <span class="appointment-day"><?= date('d', strtotime($rv['date'])) ?></span>
                <span class="appointment-month"><?= date('M', strtotime($rv['date'])) ?></span>
              </div>
              <div class="appointment-info">
                <div class="appointment-title"><?= Security::escape($rv['titre'] ?? 'Rendez-vous') ?></div>
                <div class="appointment-desc"><?= Security::escape($rv['description'] ?? '') ?></div>
                <div class="appointment-meta"><?= Security::escape($rv['time'] ?? '') ?> · <?= Security::escape(($rv['prenom'] ?? '') . ' ' . ($rv['nom'] ?? '')) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Recent Invoices Table -->
    <div class="card mt-4">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Dernières factures</h3>
        <a href="admin/index.php?module=factures" class="card-header-link">Voir tout</a>
      </div>
      <div class="card-body p-0">
        <table class="admin-table">
          <thead>
            <tr>
              <th>N°</th>
              <th>Client</th>
              <th>Montant</th>
              <th>Statut</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $factures = $stats['recentFactures'] ?? []; ?>
            <?php if (!empty($factures)): ?>
              <?php foreach ($factures as $f): ?>
                <tr>
                  <td><?= Security::escape($f['numero_facture'] ?? '#') ?></td>
                  <td><?= Security::escape($f['client_id'] ?? '-') ?></td>
                  <td><?= number_format((float)($f['montant_total'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                  <td><span class="badge badge--<?= Security::escape($f['statut'] ?? '') ?>"><?= Security::escape(match($f['statut'] ?? '') { 'payee' => 'Payée', 'impayee' => 'Impayée', 'partielle' => 'Partielle', default => $f['statut'] ?? '' }) ?></span></td>
                  <td><?= date('d/m/Y', strtotime($f['created_at'] ?? 'now')) ?></td>
                  <td>
                    <a href="admin/index.php?module=factures&action=view&id=<?= (int)($f['id'] ?? 0) ?>" class="btn btn--sm btn--outline"><i class="fas fa-eye"></i></a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center">Aucune facture récente.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Date/Time
  function updateDateTime() {
    const now = new Date();
    const opts = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('currentDate').textContent = now.toLocaleDateString('fr-FR', opts);
    document.getElementById('currentTime').textContent = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
  }
  updateDateTime();
  setInterval(updateDateTime, 30000);

  const chartDefaults = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: { display: true, position: 'bottom', labels: { usePointStyle: true, padding: 16, font: { family: "'Inter',-apple-system,sans-serif", size: 12 } } }
    },
    scales: {
      x: { grid: { display: false }, ticks: { font: { size: 11, family: "'Inter',-apple-system,sans-serif" } } },
      y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false }, ticks: { font: { size: 11, family: "'Inter',-apple-system,sans-serif" } } }
    }
  };

  const salesCtx = document.getElementById('salesChart');
  if (salesCtx) {
    new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: <?= json_encode($stats['monthlySales']['labels'] ?? []) ?>,
        datasets: [{
          label: 'Ventes (FCFA)',
          data: <?= json_encode($stats['monthlySales']['data'] ?? []) ?>,
          borderColor: '#6366f1',
          backgroundColor: 'rgba(99,102,241,0.08)',
          borderWidth: 2,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#6366f1',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6
        }]
      },
      options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, legend: { ...chartDefaults.plugins.legend, display: false } } }
    });
  }

  const clientsCtx = document.getElementById('clientsChart');
  if (clientsCtx) {
    new Chart(clientsCtx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($stats['monthlyClients']['labels'] ?? []) ?>,
        datasets: [{
          label: 'Nouveaux clients',
          data: <?= json_encode($stats['monthlyClients']['data'] ?? []) ?>,
          backgroundColor: 'rgba(16,185,129,0.8)',
          borderColor: '#10b981',
          borderWidth: 1,
          borderRadius: 4,
          barPercentage: 0.6
        }]
      },
      options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, legend: { ...chartDefaults.plugins.legend, display: false } } }
    });
  }

  const projectsCtx = document.getElementById('projectsChart');
  if (projectsCtx) {
    new Chart(projectsCtx, {
      type: 'line',
      data: {
        labels: <?= json_encode($stats['monthlyProjets']['labels'] ?? []) ?>,
        datasets: [{
          label: 'Projets',
          data: <?= json_encode($stats['monthlyProjets']['data'] ?? []) ?>,
          borderColor: '#f59e0b',
          backgroundColor: 'rgba(245,158,11,0.08)',
          borderWidth: 2,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#f59e0b',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6
        }]
      },
      options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, legend: { ...chartDefaults.plugins.legend, display: false } } }
    });
  }

  const servicesCtx = document.getElementById('servicesChart');
  if (servicesCtx) {
    new Chart(servicesCtx, {
      type: 'doughnut',
      data: {
        labels: <?= json_encode($stats['servicesPop']['labels'] ?? []) ?>,
        datasets: [{
          data: <?= json_encode($stats['servicesPop']['data'] ?? []) ?>,
          backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12, font: { size: 11, family: "'Inter',-apple-system,sans-serif" } } }
        },
        cutout: '65%'
      }
    });
  }
});
</script>
<?php require __DIR__ . '/admin_footer.php'; ?>
