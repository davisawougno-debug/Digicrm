<?php
$pageTitle = $pageTitle ?? 'Mes prospects';
require __DIR__ . '/commercial_header.php';
require __DIR__ . '/commercial_navbar.php';
require __DIR__ . '/commercial_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$prospects = $data['prospects'] ?? [];
$editProspect = $data['prospect'] ?? null;
$errors = $_SESSION['validation_errors'] ?? [];
unset($_SESSION['validation_errors']);
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/commercial_alerts.php'; ?>

    <?php if ($editProspect): ?>
    <!-- Edit Form -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fas fa-edit"></i> Modifier le prospect</div>
        <a href="<?= BASE_URL ?>/commercial_index.php?module=prospects" class="card-header-link">Retour à la liste</a>
      </div>
      <div class="card-body">
        <form method="post" class="commercial-form">
          <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Prénom *</label>
              <input type="text" name="prenom" class="form-input" value="<?= Security::escape($editProspect['prenom'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Nom *</label>
              <input type="text" name="nom" class="form-input" value="<?= Security::escape($editProspect['nom'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email *</label>
              <input type="email" name="email" class="form-input" value="<?= Security::escape($editProspect['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Téléphone</label>
              <input type="tel" name="telephone" class="form-input" value="<?= Security::escape($editProspect['telephone'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Entreprise</label>
              <input type="text" name="entreprise" class="form-input" value="<?= Security::escape($editProspect['entreprise'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Source</label>
              <select name="source" class="form-input">
                <option value="">Sélectionner</option>
                <option value="site_web" <?= ($editProspect['source'] ?? '') === 'site_web' ? 'selected' : '' ?>>Site web</option>
                <option value="recommendation" <?= ($editProspect['source'] ?? '') === 'recommendation' ? 'selected' : '' ?>>Recommandation</option>
                <option value="linkedin" <?= ($editProspect['source'] ?? '') === 'linkedin' ? 'selected' : '' ?>>LinkedIn</option>
                <option value="appel" <?= ($editProspect['source'] ?? '') === 'appel' ? 'selected' : '' ?>>Appel entrant</option>
                <option value="evenement" <?= ($editProspect['source'] ?? '') === 'evenement' ? 'selected' : '' ?>>Événement</option>
                <option value="autre" <?= ($editProspect['source'] ?? '') === 'autre' ? 'selected' : '' ?>>Autre</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Statut</label>
              <select name="statut" class="form-input">
                <option value="nouveau" <?= ($editProspect['statut'] ?? '') === 'nouveau' ? 'selected' : '' ?>>Nouveau</option>
                <option value="contacte" <?= ($editProspect['statut'] ?? '') === 'contacte' ? 'selected' : '' ?>>Contacté</option>
                <option value="qualifie" <?= ($editProspect['statut'] ?? '') === 'qualifie' ? 'selected' : '' ?>>Qualifié</option>
                <option value="perdu" <?= ($editProspect['statut'] ?? '') === 'perdu' ? 'selected' : '' ?>>Perdu</option>
                <option value="converti" <?= ($editProspect['statut'] ?? '') === 'converti' ? 'selected' : '' ?>>Converti</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Budget estimé (€)</label>
              <input type="number" step="0.01" name="budget_estime" class="form-input" value="<?= Security::escape($editProspect['budget_estime'] ?? '') ?>">
            </div>
            <div class="form-group form-group--full">
              <label class="form-label">Besoin</label>
              <textarea name="besoin" class="form-input form-textarea" rows="3"><?= Security::escape($editProspect['besoin'] ?? '') ?></textarea>
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn--primary"><i class="fas fa-save"></i> Enregistrer</button>
            <a href="<?= BASE_URL ?>/commercial_index.php?module=prospects" class="btn btn--ghost">Annuler</a>
          </div>
        </form>
      </div>
    </div>

    <?php else: ?>
    <!-- Header -->
    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Mes prospects</h1>
        <p class="page-description"><?= count($prospects) ?> prospect(s) suivi(s)</p>
      </div>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/commercial_index.php?module=prospects&action=create" class="btn btn--primary">
          <i class="fas fa-plus"></i> Nouveau prospect
        </a>
      </div>
    </div>

    <!-- Table -->
    <div class="card">
      <div class="card-body p-0">
        <?php if (empty($prospects)): ?>
          <div class="empty-state">
            <i class="fas fa-user-plus empty-icon"></i>
            <h3>Aucun prospect</h3>
            <p>Commencez par ajouter votre premier prospect.</p>
            <a href="<?= BASE_URL ?>/commercial_index.php?module=prospects&action=create" class="btn btn--primary">
              <i class="fas fa-plus"></i> Ajouter un prospect
            </a>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Email</th>
                  <th>Entreprise</th>
                  <th>Source</th>
                  <th>Statut</th>
                  <th>Budget</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($prospects as $p): ?>
                <tr>
                  <td>
                    <div class="user-cell">
                      <div class="user-avatar"><?= mb_strtoupper(mb_substr($p['prenom'] ?? $p['entreprise'] ?? '?', 0, 1)) ?></div>
                      <div>
                        <div class="user-name"><?= Security::escape(($p['prenom'] ?? '') . ' ' . ($p['nom'] ?? '')) ?></div>
                      </div>
                    </div>
                  </td>
                  <td><?= Security::escape($p['email'] ?? '') ?></td>
                  <td><?= Security::escape($p['entreprise'] ?? '-') ?></td>
                  <td><span class="badge badge--source"><?= $p['source'] ?? '-' ?></span></td>
                  <td><span class="badge badge--<?= $p['statut'] ?? 'nouveau' ?>"><?= $p['statut'] ?? 'nouveau' ?></span></td>
                  <td><?= $p['budget_estime'] ? number_format($p['budget_estime'], 0, ',', ' ') . ' €' : '-' ?></td>
                  <td class="text-muted"><?= date('d/m/Y', strtotime($p['created_at'] ?? 'now')) ?></td>
                  <td>
                    <div class="action-buttons">
                      <a href="<?= BASE_URL ?>/commercial_index.php?module=prospects&action=edit&id=<?= $p['id'] ?>" class="btn btn--sm btn--outline" title="Modifier">
                        <i class="fas fa-edit"></i>
                      </a>
                      <?php if (($p['statut'] ?? '') !== 'converti'): ?>
                      <a href="<?= BASE_URL ?>/commercial_index.php?module=prospects&action=convert&id=<?= $p['id'] ?>" class="btn btn--sm btn--success" title="Convertir en client" onclick="return confirm('Convertir ce prospect en client ?')">
                        <i class="fas fa-user-check"></i>
                      </a>
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
<?php require __DIR__ . '/commercial_footer.php'; ?>
