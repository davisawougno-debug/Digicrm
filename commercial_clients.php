<?php
$pageTitle = $pageTitle ?? 'Clients';
require __DIR__ . '/commercial_header.php';
require __DIR__ . '/commercial_navbar.php';
require __DIR__ . '/commercial_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$clients = $data['clients'] ?? [];
$client = $data['client'] ?? null;
$clientDevis = $data['devis'] ?? [];
$clientContrats = $data['contrats'] ?? [];
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/commercial_alerts.php'; ?>

    <?php if ($client): ?>
    <!-- Client Detail View -->
    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title"><?= Security::escape($client['entreprise'] ?: ($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')) ?></h1>
        <p class="page-description">Fiche client</p>
      </div>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/commercial_index.php?module=clients" class="btn btn--ghost"><i class="fas fa-arrow-left"></i> Retour</a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="detail-grid">
          <div class="detail-item">
            <span class="detail-label">Email</span>
            <span class="detail-value"><?= Security::escape($client['email'] ?? '-') ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Téléphone</span>
            <span class="detail-value"><?= Security::escape($client['telephone'] ?? '-') ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Entreprise</span>
            <span class="detail-value"><?= Security::escape($client['entreprise'] ?? '-') ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Secteur</span>
            <span class="detail-value"><?= Security::escape($client['secteur_activite'] ?? '-') ?></span>
          </div>
          <div class="detail-item detail-item--full">
            <span class="detail-label">Adresse</span>
            <span class="detail-value"><?= Security::escape($client['adresse'] ?? '-') ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="dashboard-bottom-grid">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Devis</div>
        </div>
        <div class="card-body p-0">
          <?php if (empty($clientDevis)): ?>
            <div class="empty-state">Aucun devis.</div>
          <?php else: ?>
            <div class="table-mini">
              <?php foreach ($clientDevis as $d): ?>
              <a href="<?= BASE_URL ?>/commercial_index.php?module=devis&action=view&id=<?= $d['id'] ?>" class="table-mini-row">
                <div class="table-mini-info">
                  <div class="table-mini-name"><?= $d['numero_devis'] ?? 'Devis #'.$d['id'] ?></div>
                  <div class="table-mini-sub"><?= number_format($d['montant_total'] ?? 0, 2) ?> € · <span class="badge badge--<?= $d['statut'] ?>"><?= $d['statut'] ?></span></div>
                </div>
              </a>
              <?php endforeach ?>
            </div>
          <?php endif ?>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">Contrats</div>
        </div>
        <div class="card-body p-0">
          <?php if (empty($clientContrats)): ?>
            <div class="empty-state">Aucun contrat.</div>
          <?php else: ?>
            <div class="table-mini">
              <?php foreach ($clientContrats as $c): ?>
              <div class="table-mini-row">
                <div class="table-mini-info">
                  <div class="table-mini-name"><?= $c['numero'] ?? 'Contrat #'.$c['id'] ?></div>
                  <div class="table-mini-sub"><?= number_format($c['montant'] ?? 0, 2) ?> € · <span class="badge badge--<?= $c['statut'] ?>"><?= $c['statut'] ?></span></div>
                </div>
              </div>
              <?php endforeach ?>
            </div>
          <?php endif ?>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- Client list -->
    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Clients</h1>
        <p class="page-description"><?= count($clients) ?> client(s)</p>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <?php if (empty($clients)): ?>
          <div class="empty-state">
            <i class="fas fa-building empty-icon"></i>
            <h3>Aucun client</h3>
            <p>Les prospects convertis apparaîtront ici.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Email</th>
                  <th>Entreprise</th>
                  <th>Téléphone</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($clients as $c): ?>
                <tr>
                  <td>
                    <div class="user-cell">
                      <div class="user-avatar"><?= mb_strtoupper(mb_substr($c['prenom'] ?? $c['entreprise'] ?? '?', 0, 1)) ?></div>
                      <div>
                        <div class="user-name"><?= Security::escape(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')) ?></div>
                      </div>
                    </div>
                  </td>
                  <td><?= Security::escape($c['email'] ?? '') ?></td>
                  <td><?= Security::escape($c['entreprise'] ?? '-') ?></td>
                  <td><?= Security::escape($c['telephone'] ?? '-') ?></td>
                  <td class="text-muted"><?= date('d/m/Y', strtotime($c['created_at'] ?? 'now')) ?></td>
                  <td>
                    <a href="<?= BASE_URL ?>/commercial_index.php?module=clients&action=view&id=<?= $c['id'] ?>" class="btn btn--sm btn--outline" title="Voir">
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
