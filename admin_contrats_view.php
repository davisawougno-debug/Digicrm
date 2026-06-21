<?php
$data = $GLOBALS['viewData'] ?? [];
$contrat = $data['contrat'] ?? [];
$projets = $data['projets'] ?? [];
$pageTitle = 'Contrat N° ' . ($contrat['numero'] ?? '');
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$statutColors = [
  'actif' => 'green',
  'suspendu' => 'orange',
  'termine' => 'blue',
];
$statutLabels = [
  'actif' => 'Actif',
  'suspendu' => 'Suspendu',
  'termine' => 'Terminé',
];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Contrat N° <?= Security::escape($contrat['numero'] ?? '') ?></h1>
      <a href="<?= BASE_URL ?>/admin_index.php?module=contrats" class="btn btn--outline">
        <i class="fas fa-arrow-left"></i> Retour aux contrats
      </a>
    </div>

    <div class="info-grid-2">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-user"></i> Informations client</h3>
        </div>
        <div class="card-body">
          <div class="info-item">
            <span class="info-label">Nom</span>
            <span class="info-value"><?= Security::escape(($contrat['client_prenom'] ?? '') . ' ' . ($contrat['client_nom'] ?? '')) ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Entreprise</span>
            <span class="info-value"><?= Security::escape($contrat['client_entreprise'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Email</span>
            <span class="info-value"><?= Security::escape($contrat['client_email'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Téléphone</span>
            <span class="info-value"><?= Security::escape($contrat['client_telephone'] ?? '-') ?></span>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-file-signature"></i> Informations contrat</h3>
        </div>
        <div class="card-body">
          <div class="info-item">
            <span class="info-label">Numéro</span>
            <span class="info-value"><?= Security::escape($contrat['numero'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Date de début</span>
            <span class="info-value"><?= $contrat['date_debut'] ? date('d/m/Y', strtotime($contrat['date_debut'])) : '-' ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Date de fin</span>
            <span class="info-value"><?= $contrat['date_fin'] ? date('d/m/Y', strtotime($contrat['date_fin'])) : '-' ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Date de signature</span>
            <span class="info-value"><?= $contrat['date_signature'] ? date('d/m/Y', strtotime($contrat['date_signature'])) : '-' ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Montant total</span>
            <span class="info-value"><?= number_format((float)($contrat['montant_total'] ?? 0), 0, ',', ' ') ?> FCFA</span>
          </div>
          <div class="info-item">
            <span class="info-label">Statut</span>
            <span class="info-value">
              <?php $s = $contrat['statut'] ?? 'actif'; ?>
              <span class="badge badge--<?= Security::escape($statutColors[$s] ?? 'gray') ?>">
                <?= Security::escape($statutLabels[$s] ?? ucfirst($s)) ?>
              </span>
            </span>
          </div>
        </div>
      </div>
    </div>

    <?php if (!empty($contrat['description'])): ?>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-align-left"></i> Description</h3>
      </div>
      <div class="card-body">
        <p><?= nl2br(Security::escape($contrat['description'])) ?></p>
      </div>
    </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-project-diagram"></i> Projets associés</h3>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Budget</th>
                <th>Progression</th>
                <th>Statut</th>
                <th>Date début</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($projets)): ?>
                <?php foreach ($projets as $projet): ?>
                <tr>
                  <td><?= Security::escape($projet['nom_projet'] ?? $projet['nom'] ?? '') ?></td>
                  <td><?= number_format((float)($projet['budget'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                  <td>
                    <div class="progress-bar">
                      <div class="progress-bar-fill" style="width: <?= (int)($projet['progression'] ?? 0) ?>%"></div>
                      <span><?= (int)($projet['progression'] ?? 0) ?>%</span>
                    </div>
                  </td>
                  <td><span class="badge"><?= Security::escape($projet['statut'] ?? '') ?></span></td>
                  <td><?= $projet['date_debut'] ? date('d/m/Y', strtotime($projet['date_debut'])) : '-' ?></td>
                  <td class="actions-cell">
                    <a href="<?= BASE_URL ?>/admin_index.php?module=projets&action=view&id=<?= (int)($projet['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Voir">
                      <i class="fas fa-eye"></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center">Aucun projet associé.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="form-actions">
      <a href="<?= BASE_URL ?>/admin_index.php?module=contrats&action=activer&id=<?= (int)($contrat['id'] ?? 0) ?>" class="btn btn--success"
         onclick="return confirm('Activer ce contrat ?')">
        <i class="fas fa-play"></i> Activer
      </a>
      <a href="<?= BASE_URL ?>/admin_index.php?module=contrats&action=suspendre&id=<?= (int)($contrat['id'] ?? 0) ?>" class="btn btn--warning"
         onclick="return confirm('Suspendre ce contrat ?')">
        <i class="fas fa-pause"></i> Suspendre
      </a>
      <a href="<?= BASE_URL ?>/admin_index.php?module=contrats&action=terminer&id=<?= (int)($contrat['id'] ?? 0) ?>" class="btn btn--outline"
         onclick="return confirm('Terminer ce contrat ?')">
        <i class="fas fa-stop"></i> Terminer
      </a>
    </div>

  </div>
</div>

<?php require __DIR__ . '/admin_footer.php'; ?>
