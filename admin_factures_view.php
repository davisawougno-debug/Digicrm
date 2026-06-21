<?php
$data = $GLOBALS['viewData'] ?? [];
$facture = $data['facture'] ?? [];
$paiements = $data['paiements'] ?? [];
$pageTitle = 'Facture N° ' . ($facture['numero_facture'] ?? '');
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$statutColors = [
  'payee' => 'green',
  'impayee' => 'red',
  'partielle' => 'orange',
];
$statutLabels = [
  'payee' => 'Payée',
  'impayee' => 'Impayée',
  'partielle' => 'Partielle',
];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Facture N° <?= Security::escape($facture['numero_facture'] ?? '') ?></h1>
      <a href="<?= BASE_URL ?>/admin_index.php?module=factures" class="btn btn--outline">
        <i class="fas fa-arrow-left"></i> Retour aux factures
      </a>
    </div>

    <div class="info-grid-2">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-building"></i> Informations client</h3>
        </div>
        <div class="card-body">
          <div class="info-item">
            <span class="info-label">Entreprise</span>
            <span class="info-value"><?= Security::escape($facture['client_entreprise'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Contact</span>
            <span class="info-value"><?= Security::escape(($facture['client_prenom'] ?? '') . ' ' . ($facture['client_nom'] ?? '')) ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Email</span>
            <span class="info-value"><?= Security::escape($facture['client_email'] ?? '-') ?></span>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-file-invoice"></i> Informations facture</h3>
        </div>
        <div class="card-body">
          <div class="info-item">
            <span class="info-label">Numéro</span>
            <span class="info-value"><?= Security::escape($facture['numero_facture'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Date d'émission</span>
            <span class="info-value"><?= $facture['date_emission'] ? date('d/m/Y', strtotime($facture['date_emission'])) : '-' ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Date d'échéance</span>
            <span class="info-value"><?= $facture['date_echeance'] ? date('d/m/Y', strtotime($facture['date_echeance'])) : '-' ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Montant total</span>
            <span class="info-value"><?= number_format((float)($facture['montant_total'] ?? 0), 0, ',', ' ') ?> FCFA</span>
          </div>
          <div class="info-item">
            <span class="info-label">Statut</span>
            <span class="info-value">
              <?php $s = $facture['statut'] ?? 'impayee'; ?>
              <span class="badge badge--<?= Security::escape($statutColors[$s] ?? 'gray') ?>">
                <?= Security::escape($statutLabels[$s] ?? ucfirst($s)) ?>
              </span>
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-credit-card"></i> Paiements</h3>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Montant</th>
                <th>Mode de paiement</th>
                <th>Référence</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($paiements)): ?>
                <?php foreach ($paiements as $p): ?>
                <tr>
                  <td><?= $p['date_paiement'] ? date('d/m/Y', strtotime($p['date_paiement'])) : '-' ?></td>
                  <td><?= number_format((float)($p['montant'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                  <td><?= Security::escape($p['mode_paiement'] ?? '-') ?></td>
                  <td><?= Security::escape($p['reference'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center">Aucun paiement enregistré.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <?php if (!empty($facture['notes'])): ?>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-sticky-note"></i> Notes</h3>
      </div>
      <div class="card-body">
        <p><?= nl2br(Security::escape($facture['notes'])) ?></p>
      </div>
    </div>
    <?php endif; ?>

    <div class="form-actions">
      <a href="<?= BASE_URL ?>/admin_index.php?module=factures&action=envoyer&id=<?= (int)($facture['id'] ?? 0) ?>" class="btn btn--primary"
         onclick="return confirm('Envoyer cette facture ?')">
        <i class="fas fa-paper-plane"></i> Envoyer
      </a>
      <a href="<?= BASE_URL ?>/admin_index.php?module=factures&action=paiement&id=<?= (int)($facture['id'] ?? 0) ?>" class="btn btn--success">
        <i class="fas fa-money-bill-wave"></i> Enregistrer paiement
      </a>
      <a href="<?= BASE_URL ?>/admin_index.php?module=factures&action=pdf&id=<?= (int)($facture['id'] ?? 0) ?>" class="btn btn--outline">
        <i class="fas fa-download"></i> Télécharger PDF
      </a>
    </div>

  </div>
</div>

<?php require __DIR__ . '/admin_footer.php'; ?>
