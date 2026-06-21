<?php
$pageTitle = $pageTitle ?? 'Livrables';
require __DIR__ . '/chef_projet_header.php';
require __DIR__ . '/chef_projet_navbar.php';
require __DIR__ . '/chef_projet_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$livrables = $data['livrables'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">
    <?php require __DIR__ . '/chef_projet_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Livrables</h1>
        <p class="page-description"><?= count($livrables) ?> livrable(s)</p>
      </div>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=livrables&action=create" class="btn btn--primary"><i class="fas fa-plus"></i> Nouveau livrable</a>
      </div>
    </div>

    <?php if (isset($data['projets'])): ?>
    <!-- Create Deliverable Form -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fas fa-plus-circle"></i> Nouveau livrable</div>
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=livrables" class="card-header-link">Retour</a>
      </div>
      <div class="card-body">
        <form method="post" class="chef-form" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
          <div class="form-grid">
            <div class="form-group form-group--full">
              <label class="form-label">Titre *</label>
              <input type="text" name="titre" class="form-input" required>
            </div>
            <div class="form-group form-group--full">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-input form-textarea" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Projet *</label>
              <select name="project_id" class="form-input" required>
                <option value="">Sélectionner</option>
                <?php foreach ($data['projets'] as $p): ?>
                <option value="<?= $p['id'] ?>"><?= Security::escape($p['nom_projet']) ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Fichier</label>
              <input type="file" name="fichier" class="form-input">
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn--primary"><i class="fas fa-save"></i> Créer le livrable</button>
            <a href="<?= BASE_URL ?>/chef_projet_index.php?module=livrables" class="btn btn--ghost">Annuler</a>
          </div>
        </form>
      </div>
    </div>

    <?php else: ?>
    <div class="card">
      <div class="card-body p-0">
        <?php if (empty($livrables)): ?>
          <div class="empty-state"><i class="fas fa-box empty-icon"></i><h3>Aucun livrable</h3><p>Créez votre premier livrable.</p></div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="admin-table">
            <thead><tr><th>Titre</th><th>Projet</th><th>Statut</th><th>Date envoi</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach ($livrables as $l): ?>
              <tr>
                <td><strong><?= Security::escape($l['titre']) ?></strong></td>
                <td><?= Security::escape($l['nom_projet'] ?? '-') ?></td>
                <td><span class="badge badge--<?= $l['statut'] ?? 'soumis' ?>"><?= $l['statut'] ?? 'soumis' ?></span></td>
                <td class="text-muted"><?= !empty($l['date_envoi']) ? date('d/m/Y', strtotime($l['date_envoi'])) : '-' ?></td>
                <td>
                  <div class="action-buttons">
                    <?php if (($l['statut'] ?? '') === 'soumis'): ?>
                      <a href="<?= BASE_URL ?>/chef_projet_index.php?module=livrables&action=valider&id=<?= $l['id'] ?>" class="btn btn--sm btn--success" title="Valider"><i class="fas fa-check"></i></a>
                      <a href="<?= BASE_URL ?>/chef_projet_index.php?module=livrables&action=rejeter&id=<?= $l['id'] ?>" class="btn btn--sm btn--danger" title="Rejeter"><i class="fas fa-times"></i></a>
                    <?php endif ?>
                    <?php if (!empty($l['fichier_url'])): ?>
                      <a href="<?= Security::escape($l['fichier_url']) ?>" class="btn btn--sm btn--outline" target="_blank" title="Télécharger"><i class="fas fa-download"></i></a>
                    <?php endif ?>
                  </div>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
        <?php endif ?>
      </div>
    </div>
    <?php endif ?>
  </div>
</div>
<?php require __DIR__ . '/chef_projet_footer.php'; ?>
