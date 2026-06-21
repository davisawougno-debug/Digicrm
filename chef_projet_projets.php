<?php
$pageTitle = $pageTitle ?? 'Projets';
require __DIR__ . '/chef_projet_header.php';
require __DIR__ . '/chef_projet_navbar.php';
require __DIR__ . '/chef_projet_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$projets = $data['projets'] ?? [];
$projet = $data['projet'] ?? null;
$taches = $data['taches'] ?? [];
$livrables = $data['livrables'] ?? [];
$allMembres = $data['allMembres'] ?? [];
$clients = $data['clients'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">
    <?php require __DIR__ . '/chef_projet_alerts.php'; ?>

    <?php if (isset($data['clients'])): ?>
    <!-- Create Project Form -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fas fa-plus-circle"></i> Nouveau projet</div>
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=projets" class="card-header-link">Retour</a>
      </div>
      <div class="card-body">
        <form method="post" class="chef-form">
          <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
          <div class="form-grid">
            <div class="form-group form-group--full">
              <label class="form-label">Nom du projet *</label>
              <input type="text" name="nom_projet" class="form-input" required>
            </div>
            <div class="form-group form-group--full">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-input form-textarea" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Client</label>
              <select name="client_id" class="form-input">
                <option value="">Sélectionner</option>
                <?php foreach ($clients as $c): ?>
                <option value="<?= $c['id'] ?>"><?= Security::escape($c['entreprise'] ?: $c['prenom'] . ' ' . $c['nom']) ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Budget (€)</label>
              <input type="number" step="0.01" name="budget" class="form-input">
            </div>
            <div class="form-group">
              <label class="form-label">Date début</label>
              <input type="date" name="date_debut" class="form-input">
            </div>
            <div class="form-group">
              <label class="form-label">Date fin</label>
              <input type="date" name="date_fin" class="form-input">
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn--primary"><i class="fas fa-save"></i> Créer le projet</button>
            <a href="<?= BASE_URL ?>/chef_projet_index.php?module=projets" class="btn btn--ghost">Annuler</a>
          </div>
        </form>
      </div>
    </div>

    <?php elseif ($projet): ?>
    <!-- Project Detail -->
    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title"><?= Security::escape($projet['nom_projet']) ?></h1>
        <p class="page-description">
          <span class="badge badge--<?= $projet['statut'] ?? 'en_attente' ?>"><?= $projet['statut'] ?? 'en_attente' ?></span>
          · Progression : <?= (int)($projet['progression'] ?? 0) ?>%
        </p>
      </div>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=projets" class="btn btn--ghost"><i class="fas fa-arrow-left"></i> Retour</a>
      </div>
    </div>

    <!-- Progress bar -->
    <div class="card">
      <div class="card-body">
        <div class="progress-lg">
          <div class="progress-lg-bar">
            <div class="progress-lg-fill" style="width: <?= (int)($projet['progression'] ?? 0) ?>%"></div>
          </div>
          <span class="progress-lg-label"><?= (int)($projet['progression'] ?? 0) ?>%</span>
        </div>
      </div>
    </div>

    <div class="dashboard-bottom-grid">
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="fas fa-tasks"></i> Tâches</div></div>
        <div class="card-body p-0">
          <?php if (empty($taches)): ?><div class="empty-state">Aucune tâche.</div>
          <?php else: ?>
          <div class="table-mini">
            <?php foreach ($taches as $t): ?>
            <div class="table-mini-row">
              <div class="table-mini-info">
                <div class="table-mini-name"><?= Security::escape($t['titre']) ?></div>
                <div class="table-mini-sub"><span class="badge badge--<?= $t['statut'] ?>"><?= $t['statut'] ?></span> · Priorité : <?= $t['priorite'] ?></div>
              </div>
            </div>
            <?php endforeach ?>
          </div>
          <?php endif ?>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="fas fa-box"></i> Livrables</div></div>
        <div class="card-body p-0">
          <?php if (empty($livrables)): ?><div class="empty-state">Aucun livrable.</div>
          <?php else: ?>
          <div class="table-mini">
            <?php foreach ($livrables as $l): ?>
            <div class="table-mini-row">
              <div class="table-mini-info">
                <div class="table-mini-name"><?= Security::escape($l['titre']) ?></div>
                <div class="table-mini-sub"><span class="badge badge--<?= $l['statut'] ?>"><?= $l['statut'] ?></span></div>
              </div>
            </div>
            <?php endforeach ?>
          </div>
          <?php endif ?>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- Project list -->
    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Projets</h1>
        <p class="page-description"><?= count($projets) ?> projet(s)</p>
      </div>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=projets&action=create" class="btn btn--primary"><i class="fas fa-plus"></i> Nouveau projet</a>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <?php if (empty($projets)): ?>
          <div class="empty-state"><i class="fas fa-project-diagram empty-icon"></i><h3>Aucun projet</h3><p>Créez votre premier projet.</p></div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="admin-table">
            <thead><tr><th>Nom</th><th>Client</th><th>Progression</th><th>Statut</th><th>Début</th><th>Fin</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach ($projets as $p): ?>
              <tr>
                <td><strong><?= Security::escape($p['nom_projet']) ?></strong></td>
                <td><?= Security::escape($p['client'] ?? '-') ?></td>
                <td>
                  <div class="progress-sm">
                    <div class="progress-sm-bar"><div class="progress-sm-fill" style="width:<?= (int)($p['progression'] ?? 0) ?>%"></div></div>
                    <span class="progress-sm-label"><?= (int)($p['progression'] ?? 0) ?>%</span>
                  </div>
                </td>
                <td><span class="badge badge--<?= $p['statut'] ?? 'en_attente' ?>"><?= $p['statut'] ?? 'en_attente' ?></span></td>
                <td class="text-muted"><?= !empty($p['start_date']) ? date('d/m/Y', strtotime($p['start_date'])) : '-' ?></td>
                <td class="text-muted"><?= !empty($p['end_date']) ? date('d/m/Y', strtotime($p['end_date'])) : '-' ?></td>
                <td><a href="<?= BASE_URL ?>/chef_projet_index.php?module=projets&action=view&id=<?= $p['id'] ?>" class="btn btn--sm btn--outline"><i class="fas fa-eye"></i></a></td>
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
