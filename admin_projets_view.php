<?php
$data = $GLOBALS['viewData'] ?? [];
$projet = $data['projet'] ?? [];
$taches = $data['taches'] ?? [];
$equipe = $data['equipe'] ?? [];
$pageTitle = 'Projet : ' . ($projet['nom_projet'] ?? '');
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$statutColors = [
  'en_attente' => 'blue',
  'en_cours' => 'green',
  'termine' => 'gray',
  'bloque' => 'red',
];
$statutLabels = [
  'en_attente' => 'En attente',
  'en_cours' => 'En cours',
  'termine' => 'Terminé',
  'bloque' => 'Bloqué',
];
$prioriteColors = [
  'haute' => 'red',
  'moyenne' => 'orange',
  'basse' => 'green',
];
$prioriteLabels = [
  'haute' => 'Haute',
  'moyenne' => 'Moyenne',
  'basse' => 'Basse',
];
$tacheStatutColors = [
  'a_faire' => 'gray',
  'en_cours' => 'blue',
  'termine' => 'green',
];
$tacheStatutLabels = [
  'a_faire' => 'À faire',
  'en_cours' => 'En cours',
  'termine' => 'Terminé',
];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title"><?= Security::escape($projet['nom_projet'] ?? '') ?></h1>
      <a href="<?= BASE_URL ?>/admin_index.php?module=projets" class="btn btn--outline">
        <i class="fas fa-arrow-left"></i> Retour aux projets
      </a>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-project-diagram"></i> Informations projet</h3>
      </div>
      <div class="card-body">
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Nom</span>
            <span class="info-value"><?= Security::escape($projet['nom_projet'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Client</span>
            <span class="info-value"><?= Security::escape(($projet['client_prenom'] ?? '') . ' ' . ($projet['client_nom'] ?? '')) ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Chef de projet</span>
            <span class="info-value"><?= Security::escape(($projet['chef_prenom'] ?? '') . ' ' . ($projet['chef_nom'] ?? '')) ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Date de début</span>
            <span class="info-value"><?= $projet['date_debut'] ? date('d/m/Y', strtotime($projet['date_debut'])) : '-' ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Date de fin</span>
            <span class="info-value"><?= $projet['date_fin'] ? date('d/m/Y', strtotime($projet['date_fin'])) : '-' ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Budget</span>
            <span class="info-value"><?= number_format((float)($projet['budget'] ?? 0), 0, ',', ' ') ?> FCFA</span>
          </div>
          <div class="info-item">
            <span class="info-label">Statut</span>
            <span class="info-value">
              <?php $s = $projet['statut'] ?? 'en_attente'; ?>
              <span class="badge badge--<?= Security::escape($statutColors[$s] ?? 'blue') ?>">
                <?= Security::escape($statutLabels[$s] ?? ucfirst($s)) ?>
              </span>
            </span>
          </div>
          <div class="info-item">
            <span class="info-label">Progression</span>
            <span class="info-value">
              <div class="progress-bar">
                <div class="progress-bar-fill" style="width: <?= (int)($projet['progression'] ?? 0) ?>%"></div>
                <span><?= (int)($projet['progression'] ?? 0) ?>%</span>
              </div>
            </span>
          </div>
        </div>

        <?php if (!empty($projet['description'])): ?>
        <div class="info-item" style="margin-top:15px">
          <span class="info-label">Description</span>
          <p class="info-value"><?= nl2br(Security::escape($projet['description'])) ?></p>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($equipe)): ?>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users"></i> Équipe</h3>
      </div>
      <div class="card-body">
        <div class="equipe-list">
          <?php foreach ($equipe as $membre): ?>
          <div class="equipe-item">
            <div class="avatar avatar--sm">
              <?= Security::escape(mb_strtoupper(mb_substr($membre['prenom'] ?? '', 0, 1)) . mb_strtoupper(mb_substr($membre['nom'] ?? '', 0, 1))) ?>
            </div>
            <div class="equipe-info">
              <span class="equipe-nom"><?= Security::escape(($membre['prenom'] ?? '') . ' ' . ($membre['nom'] ?? '')) ?></span>
              <span class="equipe-role"><?= Security::escape($membre['role'] ?? $membre['fonction'] ?? 'Membre') ?></span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-tasks"></i> Tâches</h3>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Titre</th>
                <th>Assigné à</th>
                <th>Priorité</th>
                <th>Statut</th>
                <th>Échéance</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($taches)): ?>
                <?php foreach ($taches as $tache): ?>
                <tr>
                  <td><?= Security::escape($tache['titre'] ?? '') ?></td>
                  <td><?= Security::escape(($tache['assigned_prenom'] ?? '') . ' ' . ($tache['assigned_nom'] ?? '')) ?></td>
                  <td>
                    <?php $p = $tache['priorite'] ?? 'moyenne'; ?>
                    <span class="badge badge--<?= Security::escape($prioriteColors[$p] ?? 'orange') ?>">
                      <?= Security::escape($prioriteLabels[$p] ?? ucfirst($p)) ?>
                    </span>
                  </td>
                  <td>
                    <?php $s = $tache['statut'] ?? 'a_faire'; ?>
                    <span class="badge badge--<?= Security::escape($tacheStatutColors[$s] ?? 'gray') ?>">
                      <?= Security::escape($tacheStatutLabels[$s] ?? ucfirst($s)) ?>
                    </span>
                  </td>
                  <td><?= $tache['date_fin'] ? date('d/m/Y', strtotime($tache['date_fin'])) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center">Aucune tâche associée.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<?php require __DIR__ . '/admin_footer.php'; ?>
