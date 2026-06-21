<?php
$pageTitle = $pageTitle ?? 'Devis';
require __DIR__ . '/commercial_header.php';
require __DIR__ . '/commercial_navbar.php';
require __DIR__ . '/commercial_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$devis = $data['devis'] ?? [];
$devisDetail = $data['devis'] ?? null;
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/commercial_alerts.php'; ?>

    <?php if (isset($devisDetail) && isset($data['devis']['id'])): ?>
    <!-- Devis Detail -->
    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Devis <?= Security::escape($devisDetail['numero_devis'] ?? '#'.$devisDetail['id']) ?></h1>
        <p class="page-description">Détail du devis</p>
      </div>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/commercial_index.php?module=devis" class="btn btn--ghost"><i class="fas fa-arrow-left"></i> Retour</a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="detail-grid">
          <div class="detail-item">
            <span class="detail-label">Numéro</span>
            <span class="detail-value"><?= Security::escape($devisDetail['numero_devis'] ?? '-') ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Montant HT</span>
            <span class="detail-value"><?= number_format($devisDetail['montant_ht'] ?? 0, 2) ?> €</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">TVA</span>
            <span class="detail-value"><?= number_format($devisDetail['tva'] ?? 0, 2) ?> %</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Montant TTC</span>
            <span class="detail-value"><?= number_format($devisDetail['montant_ttc'] ?? 0, 2) ?> €</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Statut</span>
            <span class="detail-value"><span class="badge badge--<?= $devisDetail['statut'] ?? 'brouillon' ?>"><?= $devisDetail['statut'] ?? 'brouillon' ?></span></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Date expiration</span>
            <span class="detail-value"><?= $devisDetail['date_expiration'] ? date('d/m/Y', strtotime($devisDetail['date_expiration'])) : '-' ?></span>
          </div>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- Devis list -->
    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Devis</h1>
        <p class="page-description"><?= count($devis) ?> devis</p>
      </div>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/admin_index.php?module=devis&action=create" class="btn btn--primary">
          <i class="fas fa-plus"></i> Nouveau devis
        </a>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <?php if (empty($devis)): ?>
          <div class="empty-state">
            <i class="fas fa-file-invoice empty-icon"></i>
            <h3>Aucun devis</h3>
            <p>Créez votre premier devis.</p>
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
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($devis as $d): ?>
                <tr>
                  <td><strong><?= Security::escape($d['numero_devis'] ?? '#'.$d['id']) ?></strong></td>
                  <td><?= Security::escape($d['entreprise'] ?? ($d['client_id'] ? 'Client #'.$d['client_id'] : '-')) ?></td>
                  <td><?= number_format($d['montant_total'] ?? 0, 2) ?> €</td>
                  <td><span class="badge badge--<?= $d['statut'] ?? 'brouillon' ?>"><?= $d['statut'] ?? 'brouillon' ?></span></td>
                  <td class="text-muted"><?= date('d/m/Y', strtotime($d['created_at'] ?? 'now')) ?></td>
                  <td>
                    <div class="action-buttons">
                      <a href="<?= BASE_URL ?>/commercial_index.php?module=devis&action=view&id=<?= $d['id'] ?>" class="btn btn--sm btn--outline" title="Voir">
                        <i class="fas fa-eye"></i>
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
<?php require __DIR__ . '/commercial_footer.php'; ?>
