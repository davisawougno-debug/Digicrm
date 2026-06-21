<?php
$pageTitle = 'Gestion des services';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$services = $data['services'] ?? [];
$editService = $data['service'] ?? null;
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Gestion des services</h1>
      <button class="btn btn--primary" onclick="openServiceModal()">
        <i class="fas fa-plus"></i> Ajouter un service
      </button>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Description</th>
            <th>Prix</th>
            <th>Durée</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
            <tr>
              <td><?= Security::escape($service['nom'] ?? '') ?></td>
              <td><?= Security::escape(mb_substr($service['description'] ?? '', 0, 100)) ?><?= mb_strlen($service['description'] ?? '') > 100 ? '...' : '' ?></td>
              <td><?= number_format((float)($service['prix'] ?? 0), 0, ',', ' ') ?> FCFA</td>
              <td><?= Security::escape($service['duree_estimee'] ?? '') ?></td>
              <td class="actions-cell">
                <button class="btn btn--sm btn--outline" title="Modifier"
                        onclick="editService(<?= (int)($service['id'] ?? 0) ?>, '<?= Security::escape(addslashes($service['nom'] ?? '')) ?>', '<?= Security::escape(addslashes($service['description'] ?? '')) ?>', '<?= Security::escape(addslashes($service['duree_estimee'] ?? '')) ?>', <?= (float)($service['prix'] ?? 0) ?>)">
                  <i class="fas fa-edit"></i>
                </button>
                <a href="#" class="btn btn--sm btn--outline btn--danger" title="Supprimer"
                   onclick="event.preventDefault(); openDeleteModal(<?= (int)($service['id'] ?? 0) ?>, '<?= Security::escape(addslashes($service['nom'] ?? '')) ?>')">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center">Aucun service trouvé.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<div class="modal" id="serviceModal">
  <div class="modal-backdrop" onclick="closeModal('serviceModal')"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="serviceModalTitle">Ajouter un service</h3>
        <button class="modal-close" onclick="closeModal('serviceModal')">&times;</button>
      </div>
      <form method="POST" action="<?= BASE_URL ?>/admin_index.php?module=services&action=create" id="serviceForm" class="modal-form">
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
          <div class="form-group">
            <label for="service_nom" class="form-label">Nom</label>
            <input type="text" id="service_nom" name="nom" class="form-control" value="<?= Security::escape($editService['nom'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label for="service_description" class="form-label">Description</label>
            <textarea id="service_description" name="description" class="form-control" rows="3"><?= Security::escape($editService['description'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label for="service_prix" class="form-label">Prix (FCFA)</label>
            <input type="number" id="service_prix" name="prix" class="form-control" step="0.01" min="0"
                   value="<?= Security::escape($editService['prix'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label for="service_duree_estimee" class="form-label">Durée estimée</label>
            <input type="text" id="service_duree_estimee" name="duree_estimee" class="form-control"
                   value="<?= Security::escape($editService['duree_estimee'] ?? '') ?>">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn--outline" onclick="closeModal('serviceModal')">Annuler</button>
          <button type="submit" class="btn btn--primary" id="serviceSubmitBtn">
            <i class="fas fa-save"></i> Enregistrer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openServiceModal() {
  document.getElementById('serviceModalTitle').textContent = 'Ajouter un service';
  document.getElementById('serviceForm').action = '<?= BASE_URL ?>/admin_index.php?module=services&action=create';
  document.getElementById('service_nom').value = '';
  document.getElementById('service_description').value = '';
  document.getElementById('service_prix').value = '';
  document.getElementById('service_duree_estimee').value = '';
  document.getElementById('serviceSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Créer';
  openModal('serviceModal');
}

function editService(id, nom, description, duree_estimee, prix) {
  document.getElementById('serviceModalTitle').textContent = 'Modifier le service';
  document.getElementById('serviceForm').action = '<?= BASE_URL ?>/admin_index.php?module=services&action=edit&id=' + id;
  document.getElementById('service_nom').value = nom;
  document.getElementById('service_description').value = description;
  document.getElementById('service_prix').value = prix;
  document.getElementById('service_duree_estimee').value = duree_estimee;
  document.getElementById('serviceSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
  openModal('serviceModal');
}
</script>

<?php require __DIR__ . '/admin_modals.php'; ?>
<?php require __DIR__ . '/admin_footer.php'; ?>
