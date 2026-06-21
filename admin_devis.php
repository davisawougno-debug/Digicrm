<?php
$pageTitle = 'Gestion des devis';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$devis = $data['devis'] ?? [];
$clients = $data['clients'] ?? [];
$services = $data['services'] ?? [];
$totalPages = $data['totalPages'] ?? 1;
$currentPage = $data['page'] ?? 1;
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);

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
      <h1 class="page-title">Gestion des devis</h1>
      <button class="btn btn--primary" onclick="openDevisModal()">
        <i class="fas fa-plus"></i> Nouveau devis
      </button>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Numéro</th>
            <th>Client/Prospect</th>
            <th>Montant</th>
            <th>Statut</th>
            <th>Date création</th>
            <th>Expiration</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($devis)): ?>
            <?php foreach ($devis as $d): ?>
            <tr>
              <td><?= Security::escape($d['numero_devis'] ?? '') ?></td>
              <td><?= Security::escape($d['client_nom'] ?? $d['prospect_nom'] ?? '-') ?></td>
              <td><?= number_format((float)($d['montant_total'] ?? 0), 0, ',', ' ') ?> FCFA</td>
              <td>
                <?php $s = $d['statut'] ?? 'brouillon'; ?>
                <span class="badge badge--<?= Security::escape($statutColors[$s] ?? 'gray') ?>">
                  <?= Security::escape($statutLabels[$s] ?? ucfirst($s)) ?>
                </span>
              </td>
              <td><?= date('d/m/Y', strtotime($d['date_creation'] ?? '')) ?></td>
              <td><?= $d['date_expiration'] ? date('d/m/Y', strtotime($d['date_expiration'])) : '-' ?></td>
              <td class="actions-cell">
                <a href="<?= BASE_URL ?>/admin_index.php?module=devis&action=view&id=<?= (int)($d['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Voir">
                  <i class="fas fa-eye"></i>
                </a>
                <button class="btn btn--sm btn--outline" title="Modifier"
                        onclick="editDevis(<?= (int)($d['id'] ?? 0) ?>)">
                  <i class="fas fa-edit"></i>
                </button>
                <a href="<?= BASE_URL ?>/admin_index.php?module=devis&action=valider&id=<?= (int)($d['id'] ?? 0) ?>" class="btn btn--sm btn--outline btn--success" title="Valider"
                   onclick="return confirm('Confirmer la validation de ce devis ?')">
                  <i class="fas fa-check"></i>
                </a>
                <a href="<?= BASE_URL ?>/admin_index.php?module=devis&action=refuser&id=<?= (int)($d['id'] ?? 0) ?>" class="btn btn--sm btn--outline btn--danger" title="Refuser"
                   onclick="return confirm('Confirmer le refus de ce devis ?')">
                  <i class="fas fa-times"></i>
                </a>
                <a href="<?= BASE_URL ?>/admin_index.php?module=devis&action=convert&id=<?= (int)($d['id'] ?? 0) ?>" class="btn btn--sm btn--outline btn--warning" title="Convertir en contrat"
                   onclick="return confirm('Confirmer la conversion de ce devis en contrat ?')">
                  <i class="fas fa-file-signature"></i>
                </a>
                <a href="#" class="btn btn--sm btn--outline btn--danger" title="Supprimer"
                   onclick="event.preventDefault(); openDeleteModal(<?= (int)($d['id'] ?? 0) ?>, 'Devis N° <?= Security::escape(addslashes($d['numero_devis'] ?? '')) ?>')">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center">Aucun devis trouvé.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="<?= BASE_URL ?>/admin_index.php?module=devis&page=<?= $i ?>"
           class="pagination-link <?= $currentPage === $i ? 'pagination-link--active' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

  </div>
</div>

<div class="modal" id="devisModal">
  <div class="modal-backdrop" onclick="closeModal('devisModal')"></div>
  <div class="modal-dialog modal--lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="devisModalTitle">Nouveau devis</h3>
        <button class="modal-close" onclick="closeModal('devisModal')">&times;</button>
      </div>
      <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=devis&action=create" id="devisForm" class="modal-form">
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
          <input type="hidden" id="devis_id" name="id" value="">

          <div class="form-row">
            <div class="form-group">
              <label for="devis_client_id" class="form-label">Client</label>
              <select id="devis_client_id" name="client_id" class="form-control">
                <option value="">Sélectionner un client</option>
                <?php foreach ($clients as $client): ?>
                  <option value="<?= (int)($client['id'] ?? 0) ?>">
                    <?= Security::escape(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '') . ($client['entreprise'] ? ' (' . $client['entreprise'] . ')' : '')) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="devis_date_expiration" class="form-label">Date d'expiration</label>
              <input type="date" id="devis_date_expiration" name="date_expiration" class="form-control">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="devis_service_id" class="form-label">Service</label>
              <select id="devis_service_id" name="service_id" class="form-control">
                <option value="">Sélectionner un service</option>
                <?php foreach ($services as $service): ?>
                  <option value="<?= (int)($service['id'] ?? 0) ?>">
                    <?= Security::escape($service['nom'] ?? '') ?> - <?= number_format((float)($service['prix'] ?? 0), 0, ',', ' ') ?> FCFA
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="devis_montant_total" class="form-label">Montant total (FCFA)</label>
              <input type="number" id="devis_montant_total" name="montant_total" class="form-control" step="0.01" min="0">
            </div>
          </div>

          <div class="form-group">
            <label for="devis_notes" class="form-label">Notes</label>
            <textarea id="devis_notes" name="notes" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn--outline" onclick="closeModal('devisModal')">Annuler</button>
          <button type="submit" class="btn btn--primary" id="devisSubmitBtn">
            <i class="fas fa-save"></i> Créer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openDevisModal() {
  document.getElementById('devisModalTitle').textContent = 'Nouveau devis';
  document.getElementById('devisForm').action = '<?= BASE_URL ?>/admin_index.php?module=devis&action=create';
  document.getElementById('devisForm').reset();
  document.getElementById('devis_id').value = '';
  document.getElementById('devisSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Créer';
  openModal('devisModal');
}

function editDevis(id) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '<?= BASE_URL ?>/admin_index.php?module=ajax&action=get-devis&id=' + id, true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      var d = JSON.parse(xhr.responseText);
      document.getElementById('devisModalTitle').textContent = 'Modifier le devis';
      document.getElementById('devisForm').action = '<?= BASE_URL ?>/admin_index.php?module=devis&action=edit&id=' + id;
      document.getElementById('devis_id').value = id;
      document.getElementById('devis_client_id').value = d.client_id || '';
      document.getElementById('devis_date_expiration').value = d.date_expiration || '';
      document.getElementById('devis_service_id').value = d.service_id || '';
      document.getElementById('devis_montant_total').value = d.montant_total || '';
      document.getElementById('devis_notes').value = d.notes || '';
      document.getElementById('devisSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
      openModal('devisModal');
    }
  };
  xhr.send();
}

function openDeleteModal(id, name) {
  document.getElementById('deleteModalMessage').textContent = 'Êtes-vous sûr de vouloir supprimer "' + name + '" ? Cette action est irréversible.';
  document.getElementById('deleteModalConfirm').href = '<?= BASE_URL ?>/admin_index.php?module=devis&action=delete&id=' + id;
  openModal('deleteModal');
}
</script>

<?php require __DIR__ . '/admin_modals.php'; ?>
<?php require __DIR__ . '/admin_footer.php'; ?>
