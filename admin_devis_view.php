<?php
$data = $GLOBALS['viewData'] ?? [];
$devis = $data['devis'] ?? [];
$pageTitle = 'Devis N° ' . ($devis['numero_devis'] ?? '');
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$statutColors = [
  'brouillon' => 'gray',
  'envoye' => 'blue',
  'accepte' => 'green',
  'refuse' => 'red',
];
$statutLabels = [
  'brouillon' => 'Brouillon',
  'envoye' => 'Envoyé',
  'accepte' => 'Accepté',
  'refuse' => 'Refusé',
];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Devis N° <?= Security::escape($devis['numero_devis'] ?? '') ?></h1>
      <a href="<?= BASE_URL ?>/admin_index.php?module=devis" class="btn btn--outline">
        <i class="fas fa-arrow-left"></i> Retour aux devis
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
            <span class="info-value"><?= Security::escape($devis['client_nom'] ?? $devis['prospect_nom'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Prénom</span>
            <span class="info-value"><?= Security::escape($devis['client_prenom'] ?? $devis['prospect_prenom'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Entreprise</span>
            <span class="info-value"><?= Security::escape($devis['client_entreprise'] ?? $devis['prospect_entreprise'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Email</span>
            <span class="info-value"><?= Security::escape($devis['client_email'] ?? $devis['prospect_email'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Téléphone</span>
            <span class="info-value"><?= Security::escape($devis['client_telephone'] ?? $devis['prospect_telephone'] ?? '-') ?></span>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-file-invoice"></i> Informations devis</h3>
        </div>
        <div class="card-body">
          <div class="info-item">
            <span class="info-label">Numéro</span>
            <span class="info-value"><?= Security::escape($devis['numero_devis'] ?? '-') ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Date de création</span>
            <span class="info-value"><?= $devis['date_creation'] ? date('d/m/Y', strtotime($devis['date_creation'])) : '-' ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Date d'expiration</span>
            <span class="info-value"><?= $devis['date_expiration'] ? date('d/m/Y', strtotime($devis['date_expiration'])) : '-' ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Montant total</span>
            <span class="info-value"><?= number_format((float)($devis['montant_total'] ?? 0), 0, ',', ' ') ?> FCFA</span>
          </div>
          <div class="info-item">
            <span class="info-label">Statut</span>
            <span class="info-value">
              <?php $s = $devis['statut'] ?? 'brouillon'; ?>
              <span class="badge badge--<?= Security::escape($statutColors[$s] ?? 'gray') ?>">
                <?= Security::escape($statutLabels[$s] ?? ucfirst($s)) ?>
              </span>
            </span>
          </div>
          <div class="info-item">
            <span class="info-label">Service</span>
            <span class="info-value"><?= Security::escape($devis['service_nom'] ?? '-') ?></span>
          </div>
        </div>
      </div>
    </div>

    <?php if (!empty($devis['notes'])): ?>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-sticky-note"></i> Notes</h3>
      </div>
      <div class="card-body">
        <p><?= nl2br(Security::escape($devis['notes'])) ?></p>
      </div>
    </div>
    <?php endif; ?>

    <div class="form-actions">
      <a href="<?= BASE_URL ?>/admin_index.php?module=devis&action=valider&id=<?= (int)($devis['id'] ?? 0) ?>" class="btn btn--success"
         onclick="return confirm('Confirmer la validation de ce devis ?')">
        <i class="fas fa-check"></i> Valider
      </a>
      <a href="<?= BASE_URL ?>/admin_index.php?module=devis&action=refuser&id=<?= (int)($devis['id'] ?? 0) ?>" class="btn btn--danger"
         onclick="return confirm('Confirmer le refus de ce devis ?')">
        <i class="fas fa-times"></i> Refuser
      </a>
      <a href="<?= BASE_URL ?>/admin_index.php?module=devis&action=convert&id=<?= (int)($devis['id'] ?? 0) ?>" class="btn btn--warning"
         onclick="return confirm('Confirmer la conversion de ce devis en contrat ?')">
        <i class="fas fa-file-signature"></i> Convertir en contrat
      </a>
      <a href="<?= BASE_URL ?>/admin_index.php?module=devis&action=pdf&id=<?= (int)($devis['id'] ?? 0) ?>" class="btn btn--outline">
        <i class="fas fa-download"></i> Télécharger PDF
      </a>
    </div>

  </div>
</div>

<?php require __DIR__ . '/admin_footer.php'; ?>
