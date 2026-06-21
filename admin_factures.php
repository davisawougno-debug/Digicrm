<?php
$pageTitle = 'Gestion des factures';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$factures = $data['factures'] ?? [];
$clients = $data['clients'] ?? [];
$contrats = $data['contrats'] ?? [];
$totalPages = $data['totalPages'] ?? 1;
$currentPage = $data['page'] ?? 1;
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);

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
      <h1 class="page-title">Gestion des factures</h1>
      <a href="<?= BASE_URL ?>/admin_index.php?module=factures&action=create" class="btn btn--primary">
        <i class="fas fa-plus"></i> Nouvelle facture
      </a>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Numéro</th>
            <th>Client</th>
            <th>Montant</th>
            <th>Statut</th>
            <th>Date émission</th>
            <th>Échéance</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($factures)): ?>
            <?php foreach ($factures as $f): ?>
            <tr>
              <td><?= Security::escape($f['numero_facture'] ?? '') ?></td>
              <td><?= Security::escape($f['client_nom'] ?? ($f['client_prenom'] ?? '') . ' ' . ($f['client_nom'] ?? '')) ?></td>
              <td><?= number_format((float)($f['montant_total'] ?? 0), 0, ',', ' ') ?> FCFA</td>
              <td>
                <?php $s = $f['statut'] ?? 'impayee'; ?>
                <span class="badge badge--<?= Security::escape($statutColors[$s] ?? 'gray') ?>">
                  <?= Security::escape($statutLabels[$s] ?? ucfirst($s)) ?>
                </span>
              </td>
              <td><?= $f['date_emission'] ? date('d/m/Y', strtotime($f['date_emission'])) : '-' ?></td>
              <td><?= $f['date_echeance'] ? date('d/m/Y', strtotime($f['date_echeance'])) : '-' ?></td>
              <td class="actions-cell">
                <a href="<?= BASE_URL ?>/admin_index.php?module=factures&action=view&id=<?= (int)($f['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Voir">
                  <i class="fas fa-eye"></i>
                </a>
                <button class="btn btn--sm btn--outline" title="Modifier"
                        onclick="editFacture(<?= (int)($f['id'] ?? 0) ?>)">
                  <i class="fas fa-edit"></i>
                </button>
                <a href="<?= BASE_URL ?>/admin_index.php?module=factures&action=envoyer&id=<?= (int)($f['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Envoyer"
                   onclick="return confirm('Envoyer cette facture ?')">
                  <i class="fas fa-paper-plane"></i>
                </a>
                <a href="<?= BASE_URL ?>/admin_index.php?module=factures&action=paiement&id=<?= (int)($f['id'] ?? 0) ?>" class="btn btn--sm btn--outline btn--success" title="Enregistrer paiement">
                  <i class="fas fa-money-bill-wave"></i>
                </a>
                <a href="#" class="btn btn--sm btn--outline btn--danger" title="Supprimer"
                   onclick="event.preventDefault(); openDeleteModal(<?= (int)($f['id'] ?? 0) ?>, 'Facture N° <?= Security::escape(addslashes($f['numero_facture'] ?? '')) ?>')">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center">Aucune facture trouvée.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="<?= BASE_URL ?>/admin_index.php?module=factures&page=<?= $i ?>"
           class="pagination-link <?= $currentPage === $i ? 'pagination-link--active' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

  </div>
</div>

<div class="modal" id="factureModal">
  <div class="modal-backdrop" onclick="closeModal('factureModal')"></div>
  <div class="modal-dialog modal--lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="factureModalTitle">Nouvelle facture</h3>
        <button class="modal-close" onclick="closeModal('factureModal')">&times;</button>
      </div>
      <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=factures&action=create" id="factureForm" class="modal-form">
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
          <input type="hidden" id="facture_id" name="id" value="">

          <div class="form-row">
            <div class="form-group">
              <label for="facture_client_id" class="form-label">Client</label>
              <select id="facture_client_id" name="client_id" class="form-control" required>
                <option value="">Sélectionner un client</option>
                <?php foreach ($clients as $client): ?>
                  <option value="<?= (int)($client['id'] ?? 0) ?>">
                    <?= Security::escape(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '') . ($client['entreprise'] ? ' (' . $client['entreprise'] . ')' : '')) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="facture_contract_id" class="form-label">Contrat</label>
              <select id="facture_contract_id" name="contract_id" class="form-control">
                <option value="">Sélectionner un contrat</option>
                <?php foreach ($contrats as $contrat): ?>
                  <option value="<?= (int)($contrat['id'] ?? 0) ?>">
                    <?= Security::escape($contrat['numero'] ?? '') ?> - <?= Security::escape($contrat['client_nom'] ?? '-') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="facture_date_emission" class="form-label">Date d'émission</label>
              <input type="date" id="facture_date_emission" name="date_emission" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="facture_date_echeance" class="form-label">Date d'échéance</label>
              <input type="date" id="facture_date_echeance" name="date_echeance" class="form-control" required>
            </div>
          </div>

          <div class="form-group">
            <label for="facture_montant_total" class="form-label">Montant total (FCFA)</label>
            <input type="number" id="facture_montant_total" name="montant_total" class="form-control" step="0.01" min="0" required>
          </div>

          <div class="form-group">
            <label for="facture_statut" class="form-label">Statut</label>
            <select id="facture_statut" name="statut" class="form-control">
              <?php foreach ($statutLabels as $val => $label): ?>
                <option value="<?= Security::escape($val) ?>"><?= Security::escape($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="facture_notes" class="form-label">Notes</label>
            <textarea id="facture_notes" name="notes" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn--outline" onclick="closeModal('factureModal')">Annuler</button>
          <button type="submit" class="btn btn--primary" id="factureSubmitBtn">
            <i class="fas fa-save"></i> Créer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openFactureModal() {
  document.getElementById('factureModalTitle').textContent = 'Nouvelle facture';
  document.getElementById('factureForm').action = '<?= BASE_URL ?>/admin_index.php?module=factures&action=create';
  document.getElementById('factureForm').reset();
  document.getElementById('facture_id').value = '';
  document.getElementById('factureSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Créer';
  openModal('factureModal');
}

function editFacture(id) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '<?= BASE_URL ?>/admin_index.php?module=ajax&action=get-facture&id=' + id, true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      var f = JSON.parse(xhr.responseText);
      document.getElementById('factureModalTitle').textContent = 'Modifier la facture';
      document.getElementById('factureForm').action = '<?= BASE_URL ?>/admin_index.php?module=factures&action=edit&id=' + id;
      document.getElementById('facture_id').value = id;
      document.getElementById('facture_client_id').value = f.client_id || '';
      document.getElementById('facture_contract_id').value = f.contract_id || '';
      document.getElementById('facture_date_emission').value = f.date_emission || '';
      document.getElementById('facture_date_echeance').value = f.date_echeance || '';
      document.getElementById('facture_montant_total').value = f.montant_total || '';
      document.getElementById('facture_statut').value = f.statut || 'impayee';
      document.getElementById('facture_notes').value = f.notes || '';
      document.getElementById('factureSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
      openModal('factureModal');
    }
  };
  xhr.send();
}

function openDeleteModal(id, name) {
  document.getElementById('deleteModalMessage').textContent = 'Êtes-vous sûr de vouloir supprimer "' + name + '" ? Cette action est irréversible.';
  document.getElementById('deleteModalConfirm').href = '<?= BASE_URL ?>/admin_index.php?module=factures&action=delete&id=' + id;
  openModal('deleteModal');
}
</script>

<?php require __DIR__ . '/admin_modals.php'; ?>
<?php require __DIR__ . '/admin_footer.php'; ?>
