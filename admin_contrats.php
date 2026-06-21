<?php
$pageTitle = 'Gestion des contrats';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$contrats = $data['contrats'] ?? [];
$clients = $data['clients'] ?? [];
$totalPages = $data['totalPages'] ?? 1;
$currentPage = $data['page'] ?? 1;
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);

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
      <h1 class="page-title">Gestion des contrats</h1>
      <button class="btn btn--primary" onclick="openContratModal()">
        <i class="fas fa-plus"></i> Nouveau contrat
      </button>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Numéro</th>
            <th>Client</th>
            <th>Montant</th>
            <th>Date début</th>
            <th>Date fin</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($contrats)): ?>
            <?php foreach ($contrats as $c): ?>
            <tr>
              <td><?= Security::escape($c['numero'] ?? '') ?></td>
              <td><?= Security::escape($c['client_nom'] ?? ($c['client_prenom'] ?? '') . ' ' . ($c['client_nom'] ?? '')) ?></td>
              <td><?= number_format((float)($c['montant_total'] ?? 0), 0, ',', ' ') ?> FCFA</td>
              <td><?= $c['date_debut'] ? date('d/m/Y', strtotime($c['date_debut'])) : '-' ?></td>
              <td><?= $c['date_fin'] ? date('d/m/Y', strtotime($c['date_fin'])) : '-' ?></td>
              <td>
                <?php $s = $c['statut'] ?? 'actif'; ?>
                <span class="badge badge--<?= Security::escape($statutColors[$s] ?? 'gray') ?>">
                  <?= Security::escape($statutLabels[$s] ?? ucfirst($s)) ?>
                </span>
              </td>
              <td class="actions-cell">
                <a href="<?= BASE_URL ?>/admin_index.php?module=contrats&action=view&id=<?= (int)($c['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Voir">
                  <i class="fas fa-eye"></i>
                </a>
                <button class="btn btn--sm btn--outline" title="Modifier"
                        onclick="editContrat(<?= (int)($c['id'] ?? 0) ?>)">
                  <i class="fas fa-edit"></i>
                </button>
                <a href="<?= BASE_URL ?>/admin_index.php?module=contrats&action=activer&id=<?= (int)($c['id'] ?? 0) ?>" class="btn btn--sm btn--outline btn--success" title="Activer"
                   onclick="return confirm('Activer ce contrat ?')">
                  <i class="fas fa-play"></i>
                </a>
                <a href="<?= BASE_URL ?>/admin_index.php?module=contrats&action=suspendre&id=<?= (int)($c['id'] ?? 0) ?>" class="btn btn--sm btn--outline btn--warning" title="Suspendre"
                   onclick="return confirm('Suspendre ce contrat ?')">
                  <i class="fas fa-pause"></i>
                </a>
                <a href="<?= BASE_URL ?>/admin_index.php?module=contrats&action=terminer&id=<?= (int)($c['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Terminer"
                   onclick="return confirm('Terminer ce contrat ?')">
                  <i class="fas fa-stop"></i>
                </a>
                <a href="#" class="btn btn--sm btn--outline btn--danger" title="Supprimer"
                   onclick="event.preventDefault(); openDeleteModal(<?= (int)($c['id'] ?? 0) ?>, 'Contrat N° <?= Security::escape(addslashes($c['numero'] ?? '')) ?>')">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center">Aucun contrat trouvé.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="<?= BASE_URL ?>/admin_index.php?module=contrats&page=<?= $i ?>"
           class="pagination-link <?= $currentPage === $i ? 'pagination-link--active' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

  </div>
</div>

<div class="modal" id="contratModal">
  <div class="modal-backdrop" onclick="closeModal('contratModal')"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="contratModalTitle">Nouveau contrat</h3>
        <button class="modal-close" onclick="closeModal('contratModal')">&times;</button>
      </div>
      <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=contrats&action=create" id="contratForm" class="modal-form">
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
          <input type="hidden" id="contrat_id" name="id" value="">

          <div class="form-group">
            <label for="contrat_client_id" class="form-label">Client</label>
            <select id="contrat_client_id" name="client_id" class="form-control" required>
              <option value="">Sélectionner un client</option>
              <?php foreach ($clients as $client): ?>
                <option value="<?= (int)($client['id'] ?? 0) ?>">
                  <?= Security::escape(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '') . ($client['entreprise'] ? ' (' . $client['entreprise'] . ')' : '')) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="contrat_date_debut" class="form-label">Date de début</label>
              <input type="date" id="contrat_date_debut" name="date_debut" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="contrat_date_fin" class="form-label">Date de fin</label>
              <input type="date" id="contrat_date_fin" name="date_fin" class="form-control">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="contrat_date_signature" class="form-label">Date de signature</label>
              <input type="date" id="contrat_date_signature" name="date_signature" class="form-control">
            </div>
            <div class="form-group">
              <label for="contrat_montant_total" class="form-label">Montant total (FCFA)</label>
              <input type="number" id="contrat_montant_total" name="montant_total" class="form-control" step="0.01" min="0" required>
            </div>
          </div>

          <div class="form-group">
            <label for="contrat_description" class="form-label">Description</label>
            <textarea id="contrat_description" name="description" class="form-control" rows="4"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn--outline" onclick="closeModal('contratModal')">Annuler</button>
          <button type="submit" class="btn btn--primary" id="contratSubmitBtn">
            <i class="fas fa-save"></i> Créer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openContratModal() {
  document.getElementById('contratModalTitle').textContent = 'Nouveau contrat';
  document.getElementById('contratForm').action = '<?= BASE_URL ?>/admin_index.php?module=contrats&action=create';
  document.getElementById('contratForm').reset();
  document.getElementById('contrat_id').value = '';
  document.getElementById('contratSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Créer';
  openModal('contratModal');
}

function editContrat(id) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '<?= BASE_URL ?>/admin_index.php?module=ajax&action=get-contrat&id=' + id, true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      var c = JSON.parse(xhr.responseText);
      document.getElementById('contratModalTitle').textContent = 'Modifier le contrat';
      document.getElementById('contratForm').action = '<?= BASE_URL ?>/admin_index.php?module=contrats&action=edit&id=' + id;
      document.getElementById('contrat_id').value = id;
      document.getElementById('contrat_client_id').value = c.client_id || '';
      document.getElementById('contrat_date_debut').value = c.date_debut || '';
      document.getElementById('contrat_date_fin').value = c.date_fin || '';
      document.getElementById('contrat_date_signature').value = c.date_signature || '';
      document.getElementById('contrat_montant_total').value = c.montant_total || '';
      document.getElementById('contrat_description').value = c.description || '';
      document.getElementById('contratSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
      openModal('contratModal');
    }
  };
  xhr.send();
}

function openDeleteModal(id, name) {
  document.getElementById('deleteModalMessage').textContent = 'Êtes-vous sûr de vouloir supprimer "' + name + '" ? Cette action est irréversible.';
  document.getElementById('deleteModalConfirm').href = '<?= BASE_URL ?>/admin_index.php?module=contrats&action=delete&id=' + id;
  openModal('deleteModal');
}
</script>

<?php require __DIR__ . '/admin_modals.php'; ?>
<?php require __DIR__ . '/admin_footer.php'; ?>
