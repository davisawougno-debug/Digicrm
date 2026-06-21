<?php
$pageTitle = $pageTitle ?? 'Tâches';
require __DIR__ . '/chef_projet_header.php';
require __DIR__ . '/chef_projet_navbar.php';
require __DIR__ . '/chef_projet_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$taches = $data['taches'] ?? [];
$aFaire = $data['aFaire'] ?? [];
$enCours = $data['enCours'] ?? [];
$termine = $data['termine'] ?? [];
$projets = $data['projets'] ?? [];
$membres = $data['membres'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">
    <?php require __DIR__ . '/chef_projet_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Tâches</h1>
        <p class="page-description">Gestion des tâches</p>
      </div>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=taches&action=kanban" class="btn btn--outline <?= ($_GET['action'] ?? '') === 'kanban' ? 'btn--primary' : '' ?>"><i class="fas fa-columns"></i> Kanban</a>
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=taches" class="btn btn--outline <?= ($_GET['action'] ?? '') !== 'kanban' ? 'btn--primary' : '' ?>"><i class="fas fa-list"></i> Liste</a>
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=taches&action=create" class="btn btn--primary"><i class="fas fa-plus"></i> Nouvelle tâche</a>
      </div>
    </div>

    <?php if (isset($data['membres'])): ?>
    <!-- Create Task Form -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fas fa-plus-circle"></i> Nouvelle tâche</div>
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=taches" class="card-header-link">Retour</a>
      </div>
      <div class="card-body">
        <form method="post" class="chef-form">
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
                <?php foreach ($projets as $p): ?>
                <option value="<?= $p['id'] ?>"><?= Security::escape($p['nom_projet']) ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Assigné à</label>
              <select name="assigned_to" class="form-input">
                <option value="">Sélectionner</option>
                <?php foreach ($membres as $m): ?>
                <option value="<?= $m['id'] ?>"><?= Security::escape($m['prenom'] . ' ' . $m['nom']) ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Priorité</label>
              <select name="priorite" class="form-input">
                <option value="basse">Basse</option>
                <option value="moyenne" selected>Moyenne</option>
                <option value="haute">Haute</option>
              </select>
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
            <button type="submit" class="btn btn--primary"><i class="fas fa-save"></i> Créer la tâche</button>
            <a href="<?= BASE_URL ?>/chef_projet_index.php?module=taches" class="btn btn--ghost">Annuler</a>
          </div>
        </form>
      </div>
    </div>

    <?php elseif (isset($data['aFaire'])): ?>
    <!-- Kanban Board -->
    <div class="kanban-board">
      <div class="kanban-column">
        <div class="kanban-column-header"><span>À faire</span><span class="kanban-count"><?= count($aFaire) ?></span></div>
        <div class="kanban-cards">
          <?php foreach ($aFaire as $t): ?>
          <div class="kanban-card">
            <div class="kanban-card-title"><?= Security::escape($t['titre']) ?></div>
            <div class="kanban-card-sub"><?= Security::escape($t['nom_projet'] ?? '') ?></div>
            <div class="kanban-card-footer">
              <span class="badge badge--<?= $t['priorite'] ?>"><?= $t['priorite'] ?></span>
              <span><?= Security::escape($t['prenom'] ?? '') ?></span>
              <a href="<?= BASE_URL ?>/chef_projet_index.php?module=taches&action=update-status&id=<?= $t['id'] ?>&statut=en_cours" class="btn btn--sm btn--ghost" title="Déplacer en cours"><i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
          <?php endforeach ?>
        </div>
      </div>
      <div class="kanban-column">
        <div class="kanban-column-header kanban-column-header--active"><span>En cours</span><span class="kanban-count"><?= count($enCours) ?></span></div>
        <div class="kanban-cards">
          <?php foreach ($enCours as $t): ?>
          <div class="kanban-card kanban-card--active">
            <div class="kanban-card-title"><?= Security::escape($t['titre']) ?></div>
            <div class="kanban-card-sub"><?= Security::escape($t['nom_projet'] ?? '') ?></div>
            <div class="kanban-card-footer">
              <span class="badge badge--<?= $t['priorite'] ?>"><?= $t['priorite'] ?></span>
              <span><?= Security::escape($t['prenom'] ?? '') ?></span>
              <a href="<?= BASE_URL ?>/chef_projet_index.php?module=taches&action=update-status&id=<?= $t['id'] ?>&statut=termine" class="btn btn--sm btn--ghost" title="Marquer terminée"><i class="fas fa-check"></i></a>
            </div>
          </div>
          <?php endforeach ?>
        </div>
      </div>
      <div class="kanban-column">
        <div class="kanban-column-header kanban-column-header--done"><span>Terminé</span><span class="kanban-count"><?= count($termine) ?></span></div>
        <div class="kanban-cards">
          <?php foreach ($termine as $t): ?>
          <div class="kanban-card kanban-card--done">
            <div class="kanban-card-title"><?= Security::escape($t['titre']) ?></div>
            <div class="kanban-card-sub"><?= Security::escape($t['nom_projet'] ?? '') ?></div>
            <div class="kanban-card-footer">
              <span class="badge badge--<?= $t['priorite'] ?>"><?= $t['priorite'] ?></span>
              <span><?= Security::escape($t['prenom'] ?? '') ?></span>
            </div>
          </div>
          <?php endforeach ?>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- Task List -->
    <div class="card">
      <div class="card-body p-0">
        <?php if (empty($taches)): ?>
          <div class="empty-state"><i class="fas fa-tasks empty-icon"></i><h3>Aucune tâche</h3></div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="admin-table">
            <thead><tr><th>Titre</th><th>Projet</th><th>Assigné</th><th>Priorité</th><th>Statut</th><th>Échéance</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach ($taches as $t): ?>
              <tr>
                <td><strong><?= Security::escape($t['titre']) ?></strong></td>
                <td><?= Security::escape($t['nom_projet'] ?? '-') ?></td>
                <td><?= Security::escape(($t['prenom'] ?? '') . ' ' . ($t['nom'] ?? '')) ?: '-' ?></td>
                <td><span class="badge badge--<?= $t['priorite'] ?? 'moyenne' ?>"><?= $t['priorite'] ?? 'moyenne' ?></span></td>
                <td><span class="badge badge--<?= $t['statut'] ?? 'a_faire' ?>"><?= $t['statut'] ?? 'a_faire' ?></span></td>
                <td class="text-muted"><?= !empty($t['date_fin']) ? date('d/m/Y', strtotime($t['date_fin'])) : '-' ?></td>
                <td>
                  <div class="action-buttons">
                    <a href="<?= BASE_URL ?>/chef_projet_index.php?module=taches&action=update-status&id=<?= $t['id'] ?>&statut=<?= $t['statut'] === 'a_faire' ? 'en_cours' : ($t['statut'] === 'en_cours' ? 'termine' : 'a_faire') ?>" class="btn btn--sm btn--outline" title="Changer statut">
                      <i class="fas fa-<?= $t['statut'] === 'termine' ? 'undo' : 'arrow-right' ?>"></i>
                    </a>
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
