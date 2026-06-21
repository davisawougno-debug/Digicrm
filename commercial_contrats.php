<?php
$pageTitle = $pageTitle ?? 'Contrats';
require __DIR__ . '/commercial_header.php';
require __DIR__ . '/commercial_navbar.php';
require __DIR__ . '/commercial_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$contrats = $data['contrats'] ?? [];
$contrat = $data['contrat'] ?? null;
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/commercial_alerts.php'; ?>

    <?php if ($contrat): ?>
    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Contrat <?= Security::escape($contrat['numero'] ?? '#'.$contrat['id']) ?></h1>
        <p class="page-description">Détail du contrat</p>
      </div>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/commercial_index.php?module=contrats" class="btn btn--ghost"><i class="fas fa-arrow-left"></i> Retour</a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="detail-grid">
          <div class="detail-item">
            <span class="detail-label">Numéro</span>
            <span class="detail-value"><?= Security::escape($contrat['numero'] ?? '-') ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Montant</span>
            <span class="detail-value"><?= number_format($contrat['montant'] ?? 0, 2) ?> €</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Statut</span>
            <span class="detail-value"><span class="badge badge--<?= $contrat['statut'] ?? 'brouillon' ?>"><?= $contrat['statut'] ?? 'brouillon' ?></span></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Début</span>
            <span class="detail-value"><?= $contrat['date_debut'] ? date('d/m/Y', strtotime($contrat['date_debut'])) : '-' ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Fin</span>
            <span class="detail-value"><?= $contrat['date_fin'] ? date('d/m/Y', strtotime($contrat['date_fin'])) : '-' ?></span>
          </div>
        </div>
      </div>
    </div>

    <?php else: ?>
    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Contrats</h1>
        <p class="page-description"><?= count($contrats) ?> contrat(s)</p>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <?php if (empty($contrats)): ?>
          <div class="empty-state">
            <i class="fas fa-file-signature empty-icon"></i>
            <h3>Aucun contrat</h3>
            <p>Les contrats signés apparaîtront ici.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Numéro</th>
                  <th>Client</th>
                  <th>Montant</th>
                  <th>Statut</th>
                  <th>Début</th>
                  <th>Fin</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($contrats as $c): ?>
                <tr>
                  <td><strong><?= Security::escape($c['numero'] ?? '#'.$c['id']) ?></strong></td>
                  <td><?= Security::escape($c['client_id'] ? 'Client #'.$c['client_id'] : '-') ?></td>
                  <td><?= number_format($c['montant'] ?? 0, 2) ?> €</td>
                  <td><span class="badge badge--<?= $c['statut'] ?? 'brouillon' ?>"><?= $c['statut'] ?? 'brouillon' ?></span></td>
                  <td class="text-muted"><?= $c['date_debut'] ? date('d/m/Y', strtotime($c['date_debut'])) : '-' ?></td>
                  <td class="text-muted"><?= $c['date_fin'] ? date('d/m/Y', strtotime($c['date_fin'])) : '-' ?></td>
                  <td>
                    <a href="<?= BASE_URL ?>/commercial_index.php?module=contrats&action=view&id=<?= $c['id'] ?>" class="btn btn--sm btn--outline" title="Voir">
                      <i class="fas fa-eye"></i>
                    </a>
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
