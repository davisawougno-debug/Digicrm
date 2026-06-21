<?php
$pageTitle = 'Gestion des tâches';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$taches = $data['taches'] ?? [];
$projets = $data['projets'] ?? [];
$users = $data['users'] ?? [];
$totalPages = $data['totalPages'] ?? 1;
$currentPage = $data['page'] ?? 1;
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);

$priorites = ['basse', 'moyenne', 'haute'];
$statuts = ['a_faire', 'en_cours', 'termine'];

$prioriteLabels = [
  'haute' => 'Haute',
  'moyenne' => 'Moyenne',
  'basse' => 'Basse',
];
$prioriteColors = [
  'haute' => 'red',
  'moyenne' => 'orange',
  'basse' => 'green',
];
$statutLabels = [
  'a_faire' => 'À faire',
  'en_cours' => 'En cours',
  'termine' => 'Terminé',
];
$statutColors = [
  'a_faire' => 'gray',
  'en_cours' => 'blue',
  'termine' => 'green',
];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Gestion des tâches</h1>
      <button class="btn btn--primary" onclick="openTacheModal()">
        <i class="fas fa-plus"></i> Nouvelle tâche
      </button>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Titre</th>
            <th>Projet</th>
            <th>Assigné à</th>
            <th>Priorité</th>
            <th>Statut</th>
            <th>Échéance</th>
            <th>Retard</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($taches)): ?>
            <?php foreach ($taches as $t): ?>
            <tr class="<?= (!empty($t['est_en_retard']) && ($t['statut'] ?? '') !== 'termine') ? 'row--warning' : '' ?>">
              <td><?= Security::escape($t['titre'] ?? '') ?></td>
              <td><?= Security::escape($t['projet_nom'] ?? '-') ?></td>
              <td><?= Security::escape(($t['assigned_prenom'] ?? '') . ' ' . ($t['assigned_nom'] ?? '')) ?></td>
              <td>
                <?php $p = $t['priorite'] ?? 'moyenne'; ?>
                <span class="badge badge--<?= Security::escape($prioriteColors[$p] ?? 'orange') ?>">
                  <?= Security::escape($prioriteLabels[$p] ?? ucfirst($p)) ?>
                </span>
              </td>
              <td>
                <?php $s = $t['statut'] ?? 'a_faire'; ?>
                <span class="badge badge--<?= Security::escape($statutColors[$s] ?? 'gray') ?>">
                  <?= Security::escape($statutLabels[$s] ?? ucfirst($s)) ?>
                </span>
              </td>
              <td><?= $t['date_fin'] ? date('d/m/Y', strtotime($t['date_fin'])) : '-' ?></td>
              <td>
                <?php if (!empty($t['est_en_retard']) && ($t['statut'] ?? '') !== 'termine'): ?>
                  <i class="fas fa-exclamation-triangle" style="color:#e74a3b" title="En retard"></i>
                <?php else: ?>
                  <i class="fas fa-check-circle" style="color:#1cc88a"></i>
                <?php endif; ?>
              </td>
              <td class="actions-cell">
                <button class="btn btn--sm btn--outline" title="Modifier"
                        onclick="editTache(<?= (int)($t['id'] ?? 0) ?>)">
                  <i class="fas fa-edit"></i>
                </button>
                <?php if (($t['statut'] ?? '') !== 'termine'): ?>
                <a href="<?= BASE_URL ?>/admin_index.php?module=taches&action=terminer&id=<?= (int)($t['id'] ?? 0) ?>" class="btn btn--sm btn--outline btn--success" title="Marquer terminé"
                   onclick="return confirm('Marquer cette tâche comme terminée ?')">
                  <i class="fas fa-check"></i>
                </a>
                <?php endif; ?>
                <a href="#" class="btn btn--sm btn--outline btn--danger" title="Supprimer"
                   onclick="event.preventDefault(); openDeleteModal(<?= (int)($t['id'] ?? 0) ?>, '<?= Security::escape(addslashes($t['titre'] ?? '')) ?>')">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center">Aucune tâche trouvée.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="<?= BASE_URL ?>/admin_index.php?module=taches&page=<?= $i ?>"
           class="pagination-link <?= $currentPage === $i ? 'pagination-link--active' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

  </div>
</div>

<div class="modal" id="tacheModal">
  <div class="modal-backdrop" onclick="closeModal('tacheModal')"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="tacheModalTitle">Nouvelle tâche</h3>
        <button class="modal-close" onclick="closeModal('tacheModal')">&times;</button>
      </div>
      <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=taches&action=create" id="tacheForm" class="modal-form">
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
          <input type="hidden" id="tache_id" name="id" value="">

          <div class="form-group">
            <label for="tache_project_id" class="form-label">Projet</label>
            <select id="tache_project_id" name="project_id" class="form-control" required>
              <option value="">Sélectionner un projet</option>
              <?php foreach ($projets as $projet): ?>
                <option value="<?= (int)($projet['id'] ?? 0) ?>">
                  <?= Security::escape($projet['nom_projet'] ?? $projet['nom'] ?? '') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="tache_titre" class="form-label">Titre</label>
            <input type="text" id="tache_titre" name="titre" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="tache_assigned_to" class="form-label">Assigné à</label>
            <select id="tache_assigned_to" name="assigned_to" class="form-control" required>
              <option value="">Sélectionner un utilisateur</option>
              <?php foreach ($users as $user): ?>
                <option value="<?= (int)($user['id'] ?? 0) ?>">
                  <?= Security::escape(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="tache_priorite" class="form-label">Priorité</label>
              <select id="tache_priorite" name="priorite" class="form-control">
                <?php foreach ($priorites as $priorite): ?>
                  <option value="<?= Security::escape($priorite) ?>"><?= Security::escape($prioriteLabels[$priorite]) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="tache_statut" class="form-label">Statut</label>
              <select id="tache_statut" name="statut" class="form-control">
                <?php foreach ($statuts as $statut): ?>
                  <option value="<?= Security::escape($statut) ?>"><?= Security::escape($statutLabels[$statut]) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="tache_date_debut" class="form-label">Date de début</label>
              <input type="date" id="tache_date_debut" name="date_debut" class="form-control">
            </div>
            <div class="form-group">
              <label for="tache_date_fin" class="form-label">Date d'échéance</label>
              <input type="date" id="tache_date_fin" name="date_fin" class="form-control" required>
            </div>
          </div>

          <div class="form-group">
            <label for="tache_description" class="form-label">Description</label>
            <textarea id="tache_description" name="description" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn--outline" onclick="closeModal('tacheModal')">Annuler</button>
          <button type="submit" class="btn btn--primary" id="tacheSubmitBtn">
            <i class="fas fa-save"></i> Créer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openTacheModal() {
  document.getElementById('tacheModalTitle').textContent = 'Nouvelle tâche';
  document.getElementById('tacheForm').action = '<?= BASE_URL ?>/admin_index.php?module=taches&action=create';
  document.getElementById('tacheForm').reset();
  document.getElementById('tache_id').value = '';
  document.getElementById('tacheSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Créer';
  openModal('tacheModal');
}

function editTache(id) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '<?= BASE_URL ?>/admin_index.php?module=ajax&action=get-tache&id=' + id, true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      var t = JSON.parse(xhr.responseText);
      document.getElementById('tacheModalTitle').textContent = 'Modifier la tâche';
      document.getElementById('tacheForm').action = '<?= BASE_URL ?>/admin_index.php?module=taches&action=edit&id=' + id;
      document.getElementById('tache_id').value = id;
      document.getElementById('tache_project_id').value = t.project_id || '';
      document.getElementById('tache_titre').value = t.titre || '';
      document.getElementById('tache_assigned_to').value = t.assigned_to || '';
      document.getElementById('tache_priorite').value = t.priorite || 'moyenne';
      document.getElementById('tache_statut').value = t.statut || 'a_faire';
      document.getElementById('tache_date_debut').value = t.date_debut || '';
      document.getElementById('tache_date_fin').value = t.date_fin || '';
      document.getElementById('tache_description').value = t.description || '';
      document.getElementById('tacheSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
      openModal('tacheModal');
    }
  };
  xhr.send();
}

function openDeleteModal(id, name) {
  document.getElementById('deleteModalMessage').textContent = 'Êtes-vous sûr de vouloir supprimer la tâche "' + name + '" ? Cette action est irréversible.';
  document.getElementById('deleteModalConfirm').href = '<?= BASE_URL ?>/admin_index.php?module=taches&action=delete&id=' + id;
  openModal('deleteModal');
}
</script>

<?php require __DIR__ . '/admin_modals.php'; ?>
<?php require __DIR__ . '/admin_footer.php'; ?>
