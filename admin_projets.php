<?php
$pageTitle = 'Gestion des projets';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$projets = $data['projets'] ?? [];
$clients = $data['clients'] ?? [];
$users = $data['users'] ?? [];
$totalPages = $data['totalPages'] ?? 1;
$currentPage = $data['page'] ?? 1;
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);

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
$statuts = ['en_attente', 'en_cours', 'termine', 'bloque'];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Gestion des projets</h1>
      <button class="btn btn--primary" onclick="openProjetModal()">
        <i class="fas fa-plus"></i> Nouveau projet
      </button>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Client</th>
            <th>Chef de projet</th>
            <th>Date début</th>
            <th>Date fin</th>
            <th>Budget</th>
            <th>Progression</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($projets)): ?>
            <?php foreach ($projets as $p): ?>
            <tr>
              <td><?= Security::escape($p['nom_projet'] ?? '') ?></td>
              <td><?= Security::escape($p['client_nom'] ?? ($p['client_prenom'] ?? '') . ' ' . ($p['client_nom'] ?? '')) ?></td>
              <td><?= Security::escape($p['chef_nom'] ?? ($p['chef_prenom'] ?? '') . ' ' . ($p['chef_nom'] ?? '')) ?></td>
              <td><?= $p['date_debut'] ? date('d/m/Y', strtotime($p['date_debut'])) : '-' ?></td>
              <td><?= $p['date_fin'] ? date('d/m/Y', strtotime($p['date_fin'])) : '-' ?></td>
              <td><?= number_format((float)($p['budget'] ?? 0), 0, ',', ' ') ?> FCFA</td>
              <td>
                <div class="progress-bar">
                  <div class="progress-bar-fill" style="width: <?= (int)($p['progression'] ?? 0) ?>%"></div>
                  <span><?= (int)($p['progression'] ?? 0) ?>%</span>
                </div>
              </td>
              <td>
                <?php $s = $p['statut'] ?? 'en_attente'; ?>
                <span class="badge badge--<?= Security::escape($statutColors[$s] ?? 'blue') ?>">
                  <?= Security::escape($statutLabels[$s] ?? ucfirst($s)) ?>
                </span>
              </td>
              <td class="actions-cell">
                <a href="<?= BASE_URL ?>/admin_index.php?module=projets&action=view&id=<?= (int)($p['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Voir">
                  <i class="fas fa-eye"></i>
                </a>
                <button class="btn btn--sm btn--outline" title="Modifier"
                        onclick="editProjet(<?= (int)($p['id'] ?? 0) ?>)">
                  <i class="fas fa-edit"></i>
                </button>
                <a href="#" class="btn btn--sm btn--outline btn--danger" title="Supprimer"
                   onclick="event.preventDefault(); openDeleteModal(<?= (int)($p['id'] ?? 0) ?>, '<?= Security::escape(addslashes($p['nom_projet'] ?? '')) ?>')">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center">Aucun projet trouvé.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="<?= BASE_URL ?>/admin_index.php?module=projets&page=<?= $i ?>"
           class="pagination-link <?= $currentPage === $i ? 'pagination-link--active' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

  </div>
</div>

<div class="modal" id="projetModal">
  <div class="modal-backdrop" onclick="closeModal('projetModal')"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="projetModalTitle">Nouveau projet</h3>
        <button class="modal-close" onclick="closeModal('projetModal')">&times;</button>
      </div>
      <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=projets&action=create" id="projetForm" class="modal-form">
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
          <input type="hidden" id="projet_id" name="id" value="">

          <div class="form-group">
            <label for="projet_nom_projet" class="form-label">Nom du projet</label>
            <input type="text" id="projet_nom_projet" name="nom_projet" class="form-control" required>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="projet_client_id" class="form-label">Client</label>
              <select id="projet_client_id" name="client_id" class="form-control" required>
                <option value="">Sélectionner un client</option>
                <?php foreach ($clients as $client): ?>
                  <option value="<?= (int)($client['id'] ?? 0) ?>">
                    <?= Security::escape(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '') . ($client['entreprise'] ? ' (' . $client['entreprise'] . ')' : '')) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="projet_contract_id" class="form-label">Contrat associé (optionnel)</label>
              <select id="projet_contract_id" name="contract_id" class="form-control">
                <option value="">Sélectionner un contrat</option>
                <?php foreach (($data['contrats_list'] ?? []) as $contratOpt): ?>
                  <option value="<?= (int)($contratOpt['id'] ?? 0) ?>">
                    <?= Security::escape($contratOpt['numero'] ?? '') ?> - <?= Security::escape($contratOpt['client_nom'] ?? '-') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="projet_chef_projet_id" class="form-label">Chef de projet</label>
            <select id="projet_chef_projet_id" name="chef_projet_id" class="form-control" required>
              <option value="">Sélectionner un chef de projet</option>
              <?php foreach ($users as $user): ?>
                <option value="<?= (int)($user['id'] ?? 0) ?>">
                  <?= Security::escape(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="projet_date_debut" class="form-label">Date de début</label>
              <input type="date" id="projet_date_debut" name="date_debut" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="projet_date_fin" class="form-label">Date de fin</label>
              <input type="date" id="projet_date_fin" name="date_fin" class="form-control">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="projet_budget" class="form-label">Budget (FCFA)</label>
              <input type="number" id="projet_budget" name="budget" class="form-control" step="0.01" min="0">
            </div>
            <div class="form-group">
              <label for="projet_statut" class="form-label">Statut</label>
              <select id="projet_statut" name="statut" class="form-control">
                <?php foreach ($statuts as $statut): ?>
                  <option value="<?= Security::escape($statut) ?>"><?= Security::escape($statutLabels[$statut]) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="projet_description" class="form-label">Description</label>
            <textarea id="projet_description" name="description" class="form-control" rows="4"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn--outline" onclick="closeModal('projetModal')">Annuler</button>
          <button type="submit" class="btn btn--primary" id="projetSubmitBtn">
            <i class="fas fa-save"></i> Créer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openProjetModal() {
  document.getElementById('projetModalTitle').textContent = 'Nouveau projet';
  document.getElementById('projetForm').action = '<?= BASE_URL ?>/admin_index.php?module=projets&action=create';
  document.getElementById('projetForm').reset();
  document.getElementById('projet_id').value = '';
  document.getElementById('projetSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Créer';
  openModal('projetModal');
}

function editProjet(id) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '<?= BASE_URL ?>/admin_index.php?module=ajax&action=get-projet&id=' + id, true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      var p = JSON.parse(xhr.responseText);
      document.getElementById('projetModalTitle').textContent = 'Modifier le projet';
      document.getElementById('projetForm').action = '<?= BASE_URL ?>/admin_index.php?module=projets&action=edit&id=' + id;
      document.getElementById('projet_id').value = id;
      document.getElementById('projet_nom_projet').value = p.nom_projet || '';
      document.getElementById('projet_client_id').value = p.client_id || '';
      document.getElementById('projet_contract_id').value = p.contract_id || '';
      document.getElementById('projet_chef_projet_id').value = p.chef_projet_id || '';
      document.getElementById('projet_date_debut').value = p.date_debut || '';
      document.getElementById('projet_date_fin').value = p.date_fin || '';
      document.getElementById('projet_budget').value = p.budget || '';
      document.getElementById('projet_statut').value = p.statut || 'en_attente';
      document.getElementById('projet_description').value = p.description || '';
      document.getElementById('projetSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
      openModal('projetModal');
    }
  };
  xhr.send();
}

function openDeleteModal(id, name) {
  document.getElementById('deleteModalMessage').textContent = 'Êtes-vous sûr de vouloir supprimer le projet "' + name + '" ? Cette action est irréversible.';
  document.getElementById('deleteModalConfirm').href = '<?= BASE_URL ?>/admin_index.php?module=projets&action=delete&id=' + id;
  openModal('deleteModal');
}
</script>

<?php require __DIR__ . '/admin_modals.php'; ?>
<?php require __DIR__ . '/admin_footer.php'; ?>
