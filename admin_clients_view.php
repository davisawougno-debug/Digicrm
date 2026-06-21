<?php
$data = $GLOBALS['viewData'] ?? [];
$client = $data['client'] ?? [];
$contrats = $data['contrats'] ?? [];
$projets = $data['projets'] ?? [];
$factures = $data['factures'] ?? [];
$pageTitle = 'Détail client : ' . ($client['entreprise'] ?: ($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? ''));
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title"><?= Security::escape($client['entreprise'] ?: ($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')) ?></h1>
      <a href="<?= BASE_URL ?>/admin_index.php?module=clients" class="btn btn--outline">
        <i class="fas fa-arrow-left"></i> Retour aux clients
      </a>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-building"></i> Informations client</h3>
      </div>
      <div class="card-body">
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Entreprise</span>
            <span class="info-value"><?= Security::escape($client['entreprise'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Contact</span>
            <span class="info-value"><?= Security::escape(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')) ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Email</span>
            <span class="info-value"><?= Security::escape($client['email'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Téléphone</span>
            <span class="info-value"><?= Security::escape($client['telephone'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Secteur d'activité</span>
            <span class="info-value"><?= Security::escape($client['secteur_activite'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Adresse</span>
            <span class="info-value"><?= Security::escape($client['adresse'] ?? '-') ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-signature"></i> Contrats associés</h3>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Numéro</th>
                <th>Montant</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($contrats)): ?>
                <?php foreach ($contrats as $contrat): ?>
                <tr>
                  <td><?= Security::escape($contrat['numero'] ?? '') ?></td>
                  <td><?= number_format((float)($contrat['montant_total'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                  <td><?= date('d/m/Y', strtotime($contrat['date_debut'] ?? '')) ?></td>
                  <td><?= $contrat['date_fin'] ? date('d/m/Y', strtotime($contrat['date_fin'])) : '-' ?></td>
                  <td><span class="badge"><?= Security::escape($contrat['statut'] ?? '') ?></span></td>
                  <td class="actions-cell">
                    <a href="<?= BASE_URL ?>/admin_index.php?module=contrats&action=show&id=<?= (int)($contrat['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Voir">
                      <i class="fas fa-eye"></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center">Aucun contrat associé.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

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
                    <a href="<?= BASE_URL ?>/admin_index.php?module=projets&action=show&id=<?= (int)($projet['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Voir">
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

    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Factures associées</h3>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Numéro</th>
                <th>Montant</th>
                <th>Date émission</th>
                <th>Date échéance</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($factures)): ?>
                <?php foreach ($factures as $facture): ?>
                <tr>
                  <td><?= Security::escape($facture['numero_facture'] ?? '') ?></td>
                  <td><?= number_format((float)($facture['montant_total'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                  <td><?= $facture['date_emission'] ? date('d/m/Y', strtotime($facture['date_emission'])) : '-' ?></td>
                  <td><?= $facture['date_echeance'] ? date('d/m/Y', strtotime($facture['date_echeance'])) : '-' ?></td>
                  <td><span class="badge"><?= Security::escape($facture['statut'] ?? '') ?></span></td>
                  <td class="actions-cell">
                    <a href="<?= BASE_URL ?>/admin_index.php?module=factures&action=show&id=<?= (int)($facture['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Voir">
                      <i class="fas fa-eye"></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center">Aucune facture associée.</td>
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
