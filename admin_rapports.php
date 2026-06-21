<?php
$pageTitle = 'Rapports et statistiques';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$monthlyLabels = $data['monthlyLabels'] ?? [];
$monthlyClients = $data['monthlyClients'] ?? [];
$monthlyProspects = $data['monthlyProspects'] ?? [];
$monthlyFactures = $data['monthlyFactures'] ?? [];
$revenueData = $data['revenueData'] ?? [];
$tasksByStatut = $data['tasksByStatut'] ?? [];
$servicesData = $data['servicesData'] ?? [];
$usersByRole = $data['usersByRole'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Rapports et statistiques</h1>
      <div class="page-header-actions">
        <button class="btn btn--outline" onclick="exportPDF()">
          <i class="fas fa-file-pdf"></i> Exporter PDF
        </button>
        <button class="btn btn--outline" onclick="exportExcel()">
          <i class="fas fa-file-excel"></i> Exporter Excel
        </button>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= (int)($data['totalClients'] ?? 0) ?></div>
          <div class="stat-card-label">Total clients</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon orange"><i class="fas fa-user-plus"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= (int)($data['totalProspects'] ?? 0) ?></div>
          <div class="stat-card-label">Prospects</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon purple"><i class="fas fa-project-diagram"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= (int)($data['totalProjets'] ?? 0) ?></div>
          <div class="stat-card-label">Projets</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon green"><i class="fas fa-chart-line"></i></div>
        <div class="stat-card-info">
          <div class="stat-card-number"><?= number_format((float)($data['totalRevenus'] ?? 0), 0, ',', ' ') ?> FCFA</div>
          <div class="stat-card-label">Revenus</div>
        </div>
      </div>
    </div>

    <div class="charts-grid">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-chart-line"></i> Évolution mensuelle</h3>
        </div>
        <div class="card-body">
          <canvas id="monthlyChart"></canvas>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-chart-bar"></i> Revenus mensuels</h3>
        </div>
        <div class="card-body">
          <canvas id="revenueChart"></canvas>
        </div>
      </div>
    </div>

    <div class="charts-grid">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-tasks"></i> Tâches par statut</h3>
        </div>
        <div class="card-body">
          <?php if (!empty($tasksByStatut)): ?>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Statut</th>
                <th>Nombre</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tasksByStatut as $task): ?>
              <tr>
                <td><?= Security::escape($task['statut'] ?? '') ?></td>
                <td><?= (int)($task['count'] ?? 0) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
          <p class="text-muted">Aucune donnée disponible.</p>
          <?php endif; ?>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-cogs"></i> Services populaires</h3>
        </div>
        <div class="card-body">
          <?php if (!empty($servicesData)): ?>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Service</th>
                <th>Utilisations</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($servicesData as $service): ?>
              <tr>
                <td><?= Security::escape($service['nom'] ?? $service['service'] ?? '') ?></td>
                <td><?= (int)($service['count'] ?? $service['total'] ?? 0) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
          <p class="text-muted">Aucune donnée disponible.</p>
          <?php endif; ?>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-user-shield"></i> Utilisateurs par rôle</h3>
        </div>
        <div class="card-body">
          <?php if (!empty($usersByRole)): ?>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Rôle</th>
                <th>Nombre</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usersByRole as $role): ?>
              <tr>
                <td><?= Security::escape($role['role'] ?? '') ?></td>
                <td><?= (int)($role['count'] ?? 0) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
          <p class="text-muted">Aucune donnée disponible.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var chartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: { display: true }
    }
  };

  var monthlyCtx = document.getElementById('monthlyChart');
  if (monthlyCtx) {
    new Chart(monthlyCtx, {
      type: 'line',
      data: {
        labels: <?= json_encode($monthlyLabels) ?>,
        datasets: [
          {
            label: 'Clients',
            data: <?= json_encode($monthlyClients) ?>,
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            tension: 0.3
          },
          {
            label: 'Prospects',
            data: <?= json_encode($monthlyProspects) ?>,
            borderColor: '#f6c23e',
            backgroundColor: 'rgba(246, 194, 62, 0.05)',
            tension: 0.3
          },
          {
            label: 'Factures',
            data: <?= json_encode($monthlyFactures) ?>,
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.05)',
            tension: 0.3
          }
        ]
      },
      options: chartOptions
    });
  }

  var revenueCtx = document.getElementById('revenueChart');
  if (revenueCtx) {
    new Chart(revenueCtx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($monthlyLabels) ?>,
        datasets: [{
          label: 'Revenus (FCFA)',
          data: <?= json_encode($revenueData) ?>,
          backgroundColor: '#4e73df',
          borderColor: '#4e73df',
          borderWidth: 1
        }]
      },
      options: chartOptions
    });
  }
});

function exportPDF() {
  var element = document.querySelector('.admin-main');
  html2pdf().set({ margin: 10, filename: 'rapports-digicrm.pdf' }).from(element).save();
}

function exportExcel() {
  var wb = XLSX.utils.book_new();
  var wsData = [['Rapport', 'Valeur']];
  document.querySelectorAll('.stat-card').forEach(function(card) {
    var label = card.querySelector('.stat-card-label');
    var number = card.querySelector('.stat-card-number');
    if (label && number) {
      wsData.push([label.textContent, number.textContent]);
    }
  });
  var ws = XLSX.utils.aoa_to_sheet(wsData);
  XLSX.utils.book_append_sheet(wb, ws, 'Rapports');
  XLSX.writeFile(wb, 'rapports-digicrm.xlsx');
}
</script>

<?php require __DIR__ . '/admin_footer.php'; ?>
