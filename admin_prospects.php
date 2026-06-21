<?php
$pageTitle = 'Gestion des prospects';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$prospects = $data['prospects'] ?? [];
$commerciaux = $data['commerciaux'] ?? [];
$editProspect = $data['prospect'] ?? null;
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);

$sources = ['Web', 'Référence', 'Appel', 'Email', 'Salon', 'Autre'];
$statuts = ['nouveau', 'contacte', 'qualifie', 'perdu', 'converti'];
$statutLabels = [
  'nouveau' => 'Nouveau',
  'contacte' => 'Contacté',
  'qualifie' => 'Qualifié',
  'perdu' => 'Perdu',
  'converti' => 'Converti',
];
$statutColors = [
  'nouveau' => 'blue',
  'contacte' => 'orange',
  'qualifie' => 'green',
  'perdu' => 'red',
  'converti' => 'purple',
];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Gestion des prospects</h1>
      <button class="btn btn--primary" onclick="openProspectModal()">
        <i class="fas fa-plus"></i> Nouveau prospect
      </button>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Entreprise</th>
            <th>Source</th>
            <th>Statut</th>
            <th>Commercial</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($prospects)): ?>
            <?php foreach ($prospects as $prospect): ?>
            <tr>
              <td><?= Security::escape($prospect['nom'] ?? '') ?></td>
              <td><?= Security::escape($prospect['prenom'] ?? '') ?></td>
              <td><?= Security::escape($prospect['email'] ?? '') ?></td>
              <td><?= Security::escape($prospect['telephone'] ?? '') ?></td>
              <td><?= Security::escape($prospect['entreprise'] ?? '') ?></td>
              <td><?= Security::escape($prospect['source'] ?? '') ?></td>
              <td>
                <?php $s = $prospect['statut'] ?? 'nouveau'; ?>
                <span class="badge badge--<?= Security::escape($statutColors[$s] ?? 'blue') ?>">
                  <?= Security::escape($statutLabels[$s] ?? ucfirst($s)) ?>
                </span>
              </td>
              <td><?= Security::escape($prospect['assigned_nom'] ?? ($prospect['assigned_prenom'] ?? '') . ' ' . ($prospect['assigned_nom'] ?? '')) ?></td>
              <td><?= date('d/m/Y', strtotime($prospect['created_at'] ?? '')) ?></td>
              <td class="actions-cell">
                <button class="btn btn--sm btn--outline" title="Modifier"
                        onclick="editProspect(<?= (int)($prospect['id'] ?? 0) ?>)">
                  <i class="fas fa-edit"></i>
                </button>
                <a href="<?= BASE_URL ?>/admin_index.php?module=prospects&action=convert&id=<?= (int)($prospect['id'] ?? 0) ?>"
                   class="btn btn--sm btn--outline" title="Convertir en client"
                   onclick="return confirm('Confirmer la conversion de ce prospect en client ?')">
                  <i class="fas fa-user-check"></i>
                </a>
                <a href="#" class="btn btn--sm btn--outline btn--danger" title="Supprimer"
                   onclick="event.preventDefault(); openDeleteModal(<?= (int)($prospect['id'] ?? 0) ?>, '<?= Security::escape(addslashes(($prospect['prenom'] ?? '') . ' ' . ($prospect['nom'] ?? ''))) ?>')">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="10" class="text-center">Aucun prospect trouvé.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>

<div class="modal" id="prospectModal">
  <div class="modal-backdrop" onclick="closeModal('prospectModal')"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="prospectModalTitle">Ajouter un prospect</h3>
        <button class="modal-close" onclick="closeModal('prospectModal')">&times;</button>
      </div>
      <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=prospects&action=create" id="prospectForm" class="modal-form">
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
          <input type="hidden" id="prospect_id" name="id" value="">

          <div class="form-grid">
            <div class="form-group">
              <label for="prospect_nom" class="form-label">Nom</label>
              <input type="text" id="prospect_nom" name="nom" class="form-control" required>
            </div>

            <div class="form-group">
              <label for="prospect_prenom" class="form-label">Prénom</label>
              <input type="text" id="prospect_prenom" name="prenom" class="form-control" required>
            </div>

            <div class="form-group">
              <label for="prospect_email" class="form-label">Email</label>
              <input type="email" id="prospect_email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
              <label for="prospect_telephone" class="form-label">Téléphone</label>
              <input type="tel" id="prospect_telephone" name="telephone" class="form-control">
            </div>

            <div class="form-group">
              <label for="prospect_entreprise" class="form-label">Entreprise</label>
              <input type="text" id="prospect_entreprise" name="entreprise" class="form-control">
            </div>

            <div class="form-group">
              <label for="prospect_source" class="form-label">Source</label>
              <select id="prospect_source" name="source" class="form-control">
                <option value="">Sélectionner une source</option>
                <?php foreach ($sources as $source): ?>
                  <option value="<?= Security::escape($source) ?>"><?= Security::escape($source) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="prospect_statut" class="form-label">Statut</label>
              <select id="prospect_statut" name="statut" class="form-control">
                <?php foreach ($statuts as $statut): ?>
                  <option value="<?= Security::escape($statut) ?>"><?= Security::escape($statutLabels[$statut]) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="prospect_assigned_to" class="form-label">Commercial</label>
              <select id="prospect_assigned_to" name="assigned_to" class="form-control">
                <option value="">Sélectionner un commercial</option>
                <?php foreach ($commerciaux as $commercial): ?>
                  <option value="<?= (int)($commercial['id'] ?? 0) ?>">
                    <?= Security::escape(($commercial['prenom'] ?? '') . ' ' . ($commercial['nom'] ?? '')) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="prospect_besoin" class="form-label">Besoin</label>
            <textarea id="prospect_besoin" name="besoin" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn--outline" onclick="closeModal('prospectModal')">Annuler</button>
          <button type="submit" class="btn btn--primary" id="prospectSubmitBtn">
            <i class="fas fa-save"></i> Créer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

<script>
function openProspectModal() {
  document.getElementById('prospectModalTitle').textContent = 'Ajouter un prospect';
  document.getElementById('prospectForm').action = '<?= BASE_URL ?>/admin_index.php?module=prospects&action=create';
  document.getElementById('prospectForm').reset();
  document.getElementById('prospect_id').value = '';
  document.getElementById('prospectSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Créer';
  openModal('prospectModal');
}

function editProspect(id) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '<?= BASE_URL ?>/admin_index.php?module=ajax&type=prospect&id=' + id, true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      var p = JSON.parse(xhr.responseText);
      document.getElementById('prospectModalTitle').textContent = 'Modifier le prospect';
      document.getElementById('prospectForm').action = '<?= BASE_URL ?>/admin_index.php?module=prospects&action=edit&id=' + id;
      document.getElementById('prospect_id').value = id;
      document.getElementById('prospect_nom').value = p.nom || '';
      document.getElementById('prospect_prenom').value = p.prenom || '';
      document.getElementById('prospect_email').value = p.email || '';
      document.getElementById('prospect_telephone').value = p.telephone || '';
      document.getElementById('prospect_entreprise').value = p.entreprise || '';
      document.getElementById('prospect_source').value = p.source || '';
      document.getElementById('prospect_statut').value = p.statut || 'nouveau';
      document.getElementById('prospect_assigned_to').value = p.assigned_to || '';
      document.getElementById('prospect_besoin').value = p.besoin || '';
      document.getElementById('prospectSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
      openModal('prospectModal');
    }
  };
  xhr.send();
}
</script>

<?php require __DIR__ . '/admin_modals.php'; ?>
<?php require __DIR__ . '/admin_footer.php'; ?>
