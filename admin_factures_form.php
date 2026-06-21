<?php
$data = $GLOBALS['viewData'] ?? [];
$facture = $data['facture'] ?? [];
$clients = $data['clients'] ?? [];
$contrats = $data['contrats'] ?? [];
$isEdit = !empty($facture['id']);
$pageTitle = $isEdit ? 'Modifier facture' : 'Nouvelle facture';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);

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
      <h1 class="page-title"><?= $isEdit ? 'Modifier la facture' : 'Nouvelle facture' ?></h1>
      <a href="<?= BASE_URL ?>/admin_index.php?module=factures" class="btn btn--outline">
        <i class="fas fa-arrow-left"></i> Retour aux factures
      </a>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice"></i> Informations générales</h3>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=factures&action=<?= $isEdit ? 'edit&id=' . (int)($facture['id'] ?? 0) : 'create' ?>" id="factureForm">
          <?= Security::csrfField() ?>

          <div class="form-row">
            <div class="form-group">
              <label for="client_id" class="form-label">Client</label>
              <select id="client_id" name="client_id" class="form-input" required>
                <option value="">Sélectionner un client</option>
                <?php foreach ($clients as $client): ?>
                  <option value="<?= (int)($client['id'] ?? 0) ?>" <?= ((int)($facture['client_id'] ?? 0) === (int)($client['id'] ?? 0)) ? 'selected' : '' ?>>
                    <?= Security::escape(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '') . ($client['entreprise'] ? ' (' . $client['entreprise'] . ')' : '')) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="contract_id" class="form-label">Contrat (optionnel)</label>
              <select id="contract_id" name="contract_id" class="form-input">
                <option value="">Sélectionner un contrat</option>
                <?php foreach ($contrats as $contrat): ?>
                  <option value="<?= (int)($contrat['id'] ?? 0) ?>" <?= ((int)($facture['contract_id'] ?? 0) === (int)($contrat['id'] ?? 0)) ? 'selected' : '' ?>>
                    <?= Security::escape($contrat['numero'] ?? '') ?> - <?= Security::escape($contrat['client_nom'] ?? '-') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="date_emission" class="form-label">Date d'émission</label>
              <input type="date" id="date_emission" name="date_emission" class="form-input"
                     value="<?= Security::escape($facture['date_emission'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="form-group">
              <label for="date_echeance" class="form-label">Date d'échéance</label>
              <input type="date" id="date_echeance" name="date_echeance" class="form-input"
                     value="<?= Security::escape($facture['date_echeance'] ?? '') ?>" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="montant_total" class="form-label">Montant total (FCFA)</label>
              <input type="number" id="montant_total" name="montant_total" class="form-input" step="0.01" min="0"
                     value="<?= Security::escape($facture['montant_total'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label for="statut" class="form-label">Statut</label>
              <select id="statut" name="statut" class="form-input">
                <?php foreach ($statutLabels as $val => $label): ?>
                  <option value="<?= Security::escape($val) ?>" <?= (($facture['statut'] ?? 'impayee') === $val) ? 'selected' : '' ?>>
                    <?= Security::escape($label) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="notes" class="form-label">Notes</label>
            <textarea id="notes" name="notes" class="form-input" rows="3"><?= Security::escape($facture['notes'] ?? '') ?></textarea>
          </div>

          <div class="form-actions" style="margin-top:30px">
            <button type="submit" class="btn btn--primary">
              <i class="fas fa-save"></i> <?= $isEdit ? 'Enregistrer' : 'Créer la facture' ?>
            </button>
            <a href="<?= BASE_URL ?>/admin_index.php?module=factures" class="btn btn--outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<?php require __DIR__ . '/admin_footer.php'; ?>
