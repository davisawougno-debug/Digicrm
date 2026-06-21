<?php
$pageTitle = 'Gestion des clients';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$clients = $data['clients'] ?? [];
$editClient = $data['client'] ?? null;
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Gestion des clients</h1>
      <button class="btn btn--primary" onclick="openClientModal()">
        <i class="fas fa-plus"></i> Nouveau client
      </button>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Entreprise</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Secteur d'activité</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($clients)): ?>
            <?php foreach ($clients as $client): ?>
            <tr>
              <td><?= Security::escape($client['entreprise'] ?? '') ?></td>
              <td><?= Security::escape(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')) ?></td>
              <td><?= Security::escape($client['email'] ?? '') ?></td>
              <td><?= Security::escape($client['telephone'] ?? '') ?></td>
              <td><?= Security::escape($client['secteur_activite'] ?? '') ?></td>
              <td><?= date('d/m/Y', strtotime($client['created_at'] ?? '')) ?></td>
              <td class="actions-cell">
                <a href="<?= BASE_URL ?>/admin_index.php?module=clients&action=view&id=<?= (int)($client['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Voir">
                  <i class="fas fa-eye"></i>
                </a>
                <button class="btn btn--sm btn--outline" title="Modifier"
                        onclick="editClient(<?= (int)($client['id'] ?? 0) ?>)">
                  <i class="fas fa-edit"></i>
                </button>
                <a href="#" class="btn btn--sm btn--outline btn--danger" title="Supprimer"
                   onclick="event.preventDefault(); openDeleteModal(<?= (int)($client['id'] ?? 0) ?>, '<?= Security::escape(addslashes($client['entreprise'] ?? ($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? ''))) ?>')">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center">Aucun client trouvé.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<div class="modal" id="clientModal">
  <div class="modal-backdrop" onclick="closeModal('clientModal')"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="clientModalTitle">Ajouter un client</h3>
        <button class="modal-close" onclick="closeModal('clientModal')">&times;</button>
      </div>
      <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=clients&action=create" id="clientForm" class="modal-form">
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
          <input type="hidden" id="client_id" name="id" value="">

          <div class="form-grid">
            <div class="form-group">
              <label for="client_entreprise" class="form-label">Entreprise</label>
              <input type="text" id="client_entreprise" name="entreprise" class="form-control" required>
            </div>

            <div class="form-group">
              <label for="client_nom" class="form-label">Nom</label>
              <input type="text" id="client_nom" name="nom" class="form-control" required>
            </div>

            <div class="form-group">
              <label for="client_prenom" class="form-label">Prénom</label>
              <input type="text" id="client_prenom" name="prenom" class="form-control">
            </div>

            <div class="form-group">
              <label for="client_email" class="form-label">Email</label>
              <input type="email" id="client_email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
              <label for="client_telephone" class="form-label">Téléphone</label>
              <input type="tel" id="client_telephone" name="telephone" class="form-control">
            </div>

            <div class="form-group">
              <label for="client_secteur_activite" class="form-label">Secteur d'activité</label>
              <input type="text" id="client_secteur_activite" name="secteur_activite" class="form-control">
            </div>
          </div>

          <div class="form-group">
            <label for="client_adresse" class="form-label">Adresse</label>
            <textarea id="client_adresse" name="adresse" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn--outline" onclick="closeModal('clientModal')">Annuler</button>
          <button type="submit" class="btn btn--primary" id="clientSubmitBtn">
            <i class="fas fa-save"></i> Créer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openClientModal() {
  document.getElementById('clientModalTitle').textContent = 'Ajouter un client';
  document.getElementById('clientForm').action = '<?= BASE_URL ?>/admin_index.php?module=clients&action=create';
  document.getElementById('clientForm').reset();
  document.getElementById('client_id').value = '';
  document.getElementById('clientSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Créer';
  openModal('clientModal');
}

function editClient(id) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '<?= BASE_URL ?>/admin_index.php?module=ajax&action=get-client&id=' + id, true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      var c = JSON.parse(xhr.responseText);
      document.getElementById('clientModalTitle').textContent = 'Modifier le client';
      document.getElementById('clientForm').action = '<?= BASE_URL ?>/admin_index.php?module=clients&action=edit&id=' + id;
      document.getElementById('client_id').value = id;
      document.getElementById('client_entreprise').value = c.entreprise || '';
      document.getElementById('client_nom').value = c.nom || '';
      document.getElementById('client_prenom').value = c.prenom || '';
      document.getElementById('client_email').value = c.email || '';
      document.getElementById('client_telephone').value = c.telephone || '';
      document.getElementById('client_secteur_activite').value = c.secteur_activite || '';
      document.getElementById('client_adresse').value = c.adresse || '';
      document.getElementById('clientSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
      openModal('clientModal');
    }
  };
  xhr.send();
}
</script>

<?php require __DIR__ . '/admin_modals.php'; ?>
<?php require __DIR__ . '/admin_footer.php'; ?>
