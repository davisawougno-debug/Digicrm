<?php
$pageTitle = 'Gestion des paiements';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$paiements = $data['paiements'] ?? [];
$factures = $data['factures'] ?? [];
$editPaiement = $data['paiement'] ?? null;
$totalPages = $data['totalPages'] ?? 1;
$currentPage = $data['page'] ?? 1;
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);

$modeColors = [
  'virement' => 'blue',
  'carte' => 'green',
  'especes' => 'orange',
  'cheque' => 'purple',
  'prelevement' => 'teal',
  'mobile_money' => 'teal',
];
$modeLabels = [
  'virement' => 'Virement',
  'carte' => 'Carte',
  'especes' => 'Espèces',
  'cheque' => 'Chèque',
  'prelevement' => 'Prélèvement',
  'mobile_money' => 'Mobile Money',
];
$modesPaiement = ['virement', 'carte', 'especes', 'cheque', 'prelevement', 'mobile_money'];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Gestion des paiements</h1>
      <button class="btn btn--primary" onclick="openPaiementModal()">
        <i class="fas fa-plus"></i> Ajouter un paiement
      </button>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Facture</th>
            <th>Client</th>
            <th>Montant</th>
            <th>Mode de paiement</th>
            <th>Date paiement</th>
            <th>Référence</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($paiements)): ?>
            <?php foreach ($paiements as $p): ?>
            <tr>
              <td><?= Security::escape($p['facture_numero'] ?? '-') ?></td>
              <td><?= Security::escape($p['client_nom'] ?? ($p['client_prenom'] ?? '') . ' ' . ($p['client_nom'] ?? '')) ?></td>
              <td><?= number_format((float)($p['montant'] ?? 0), 0, ',', ' ') ?> FCFA</td>
              <td>
                <?php $m = $p['mode_paiement'] ?? ''; ?>
                <span class="badge badge--<?= Security::escape($modeColors[$m] ?? 'gray') ?>">
                  <?= Security::escape($modeLabels[$m] ?? $m) ?>
                </span>
              </td>
              <td><?= $p['date_paiement'] ? date('d/m/Y', strtotime($p['date_paiement'])) : '-' ?></td>
              <td><?= Security::escape($p['reference'] ?? '-') ?></td>
              <td class="actions-cell">
                <button class="btn btn--sm btn--outline" title="Modifier"
                        onclick="editPaiement(<?= (int)($p['id'] ?? 0) ?>)">
                  <i class="fas fa-edit"></i>
                </button>
                <a href="#" class="btn btn--sm btn--outline btn--danger" title="Supprimer"
                   onclick="event.preventDefault(); openDeleteModal(<?= (int)($p['id'] ?? 0) ?>, 'Paiement de <?= number_format((float)($p['montant'] ?? 0), 0, ',', ' ') ?> FCFA')">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center">Aucun paiement trouvé.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="<?= BASE_URL ?>/admin_index.php?module=paiements&page=<?= $i ?>"
           class="pagination-link <?= $currentPage === $i ? 'pagination-link--active' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

  </div>
</div>

<div class="modal" id="paiementModal">
  <div class="modal-backdrop" onclick="closeModal('paiementModal')"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="paiementModalTitle">Ajouter un paiement</h3>
        <button class="modal-close" onclick="closeModal('paiementModal')">&times;</button>
      </div>
      <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=paiements&action=create" id="paiementForm" class="modal-form">
        <?= Security::csrfField() ?>

        <?php if (!empty($errors)): ?>
        <div class="admin-alert admin-alert--error">
          <i class="fas fa-times-circle"></i>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= Security::escape($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <div class="modal-body">
          <input type="hidden" id="paiement_id" name="id" value="">

          <div class="form-group">
            <label for="paiement_invoice_id" class="form-label">Facture</label>
            <select id="paiement_invoice_id" name="invoice_id" class="form-control" required>
              <option value="">Sélectionner une facture</option>
              <?php foreach ($factures as $facture): ?>
                <option value="<?= (int)($facture['id'] ?? 0) ?>">
                  <?= Security::escape($facture['numero_facture'] ?? '') ?> - <?= Security::escape($facture['client_nom'] ?? '-') ?> (<?= number_format((float)($facture['montant_total'] ?? 0), 0, ',', ' ') ?> FCFA)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="paiement_montant" class="form-label">Montant (FCFA)</label>
            <input type="number" id="paiement_montant" name="montant" class="form-control" step="0.01" min="0" required>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="paiement_mode" class="form-label">Mode de paiement</label>
              <select id="paiement_mode" name="mode_paiement" class="form-control" required>
                <option value="">Sélectionner un mode</option>
                <?php foreach ($modesPaiement as $mode): ?>
                  <option value="<?= Security::escape($mode) ?>"><?= Security::escape($modeLabels[$mode]) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="paiement_date" class="form-label">Date de paiement</label>
              <input type="date" id="paiement_date" name="date_paiement" class="form-control" required>
            </div>
          </div>

          <div class="form-group">
            <label for="paiement_reference" class="form-label">Référence</label>
            <input type="text" id="paiement_reference" name="reference" class="form-control">
          </div>

          <div class="form-group">
            <label for="paiement_notes" class="form-label">Notes</label>
            <textarea id="paiement_notes" name="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn--outline" onclick="closeModal('paiementModal')">Annuler</button>
          <button type="submit" class="btn btn--primary" id="paiementSubmitBtn">
            <i class="fas fa-save"></i> Ajouter
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openPaiementModal() {
  document.getElementById('paiementModalTitle').textContent = 'Ajouter un paiement';
  document.getElementById('paiementForm').action = '<?= BASE_URL ?>/admin_index.php?module=paiements&action=create';
  document.getElementById('paiementForm').reset();
  document.getElementById('paiement_id').value = '';
  document.getElementById('paiementSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Ajouter';
  openModal('paiementModal');
}

function editPaiement(id) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '<?= BASE_URL ?>/admin_index.php?module=ajax&action=get-paiement&id=' + id, true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      var p = JSON.parse(xhr.responseText);
      document.getElementById('paiementModalTitle').textContent = 'Modifier le paiement';
      document.getElementById('paiementForm').action = '<?= BASE_URL ?>/admin_index.php?module=paiements&action=edit&id=' + id;
      document.getElementById('paiement_id').value = id;
      document.getElementById('paiement_invoice_id').value = p.invoice_id || '';
      document.getElementById('paiement_montant').value = p.montant || '';
      document.getElementById('paiement_mode').value = p.mode_paiement || '';
      document.getElementById('paiement_date').value = p.date_paiement || '';
      document.getElementById('paiement_reference').value = p.reference || '';
      document.getElementById('paiement_notes').value = p.notes || '';
      document.getElementById('paiementSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
      openModal('paiementModal');
    }
  };
  xhr.send();
}

function openDeleteModal(id, name) {
  document.getElementById('deleteModalMessage').textContent = 'Êtes-vous sûr de vouloir supprimer "' + name + '" ? Cette action est irréversible.';
  document.getElementById('deleteModalConfirm').href = '<?= BASE_URL ?>/admin_index.php?module=paiements&action=delete&id=' + id;
  openModal('deleteModal');
}
</script>

<?php require __DIR__ . '/admin_modals.php'; ?>
<?php require __DIR__ . '/admin_footer.php'; ?>
