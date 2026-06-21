<?php
$pageTitle = 'Résultats de recherche';
require __DIR__ . '/admin_header.php';
require __DIR__ . '/admin_navbar.php';
require __DIR__ . '/admin_sidebar.php';

$data = $GLOBALS['viewData'] ?? [];
$results = $data['results'] ?? [];
$query = $data['query'] ?? '';

$sectionConfig = [
  'users' => ['icon' => 'fa-users', 'title' => 'Utilisateurs', 'link' => BASE_URL . '/admin_index.php?module=users', 'fields' => ['nom', 'prenom', 'email']],
  'clients' => ['icon' => 'fa-building', 'title' => 'Clients', 'link' => '<?= BASE_URL ?>/admin_index.php?module=clients', 'fields' => ['entreprise', 'nom', 'prenom', 'email']],
  'prospects' => ['icon' => 'fa-user-plus', 'title' => 'Prospects', 'link' => '<?= BASE_URL ?>/admin_index.php?module=prospects', 'fields' => ['nom', 'prenom', 'entreprise', 'email']],
  'projets' => ['icon' => 'fa-project-diagram', 'title' => 'Projets', 'link' => '<?= BASE_URL ?>/admin_index.php?module=projets', 'fields' => ['nom_projet', 'statut']],
  'contrats' => ['icon' => 'fa-file-signature', 'title' => 'Contrats', 'link' => '<?= BASE_URL ?>/admin_index.php?module=contrats', 'fields' => ['numero', 'montant_total']],
  'factures' => ['icon' => 'fa-file-invoice-dollar', 'title' => 'Factures', 'link' => '<?= BASE_URL ?>/admin_index.php?module=factures', 'fields' => ['numero_facture', 'montant_total', 'statut']],
  'services' => ['icon' => 'fa-cogs', 'title' => 'Services', 'link' => '<?= BASE_URL ?>/admin_index.php?module=services', 'fields' => ['nom', 'prix']],
];

$resultCount = 0;
foreach ($results as $key => $items) {
  if (!empty($items) && isset($sectionConfig[$key])) {
    $resultCount += count($items);
  }
}
?>
<div class="admin-main">
  <div class="admin-container">

    <?php require __DIR__ . '/admin_alerts.php'; ?>

    <div class="page-header">
      <h1 class="page-title">Résultats de recherche</h1>
    </div>

    <?php if (empty($query) || $resultCount === 0): ?>
      <div class="card">
        <div class="card-body text-center">
          <p style="font-size:1.1em;color:#858796">
            <?php if (empty($query)): ?>
              Veuillez saisir un terme de recherche.
            <?php else: ?>
              Aucun résultat trouvé pour '<strong><?= Security::escape($query) ?></strong>'.
            <?php endif; ?>
          </p>
          <a href="<?= BASE_URL ?>/admin_index.php?module=dashboard" class="btn btn--outline" style="margin-top:10px">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
          </a>
        </div>
      </div>
    <?php else: ?>
      <p style="margin-bottom:20px;color:#858796">
        Résultats pour '<strong><?= Security::escape($query) ?></strong>' — <?= $resultCount ?> résultat(s) trouvé(s).
      </p>

      <?php foreach ($sectionConfig as $key => $config): ?>
        <?php $items = $results[$key] ?? []; ?>
        <?php if (!empty($items)): ?>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas <?= Security::escape($config['icon']) ?>"></i>
              <?= Security::escape($config['title']) ?>
              <span class="badge" style="margin-left:10px"><?= count($items) ?></span>
            </h3>
            <a href="<?= Security::escape($config['link']) ?>" class="btn btn--sm btn--outline">
              Voir tout <i class="fas fa-arrow-right"></i>
            </a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="admin-table">
                <thead>
                  <tr>
                    <?php foreach ($config['fields'] as $field): ?>
                      <th><?= Security::escape(ucfirst(str_replace('_', ' ', $field))) ?></th>
                    <?php endforeach; ?>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($items as $item): ?>
                  <tr>
                    <?php foreach ($config['fields'] as $field): ?>
                      <td>
                        <?php
                          if (in_array($field, ['montant_total', 'prix'])) {
                            echo number_format((float)($item[$field] ?? 0), 0, ',', ' ') . ' FCFA';
                          } else {
                            echo Security::escape($item[$field] ?? '-');
                          }
                        ?>
                      </td>
                    <?php endforeach; ?>
                    <td class="actions-cell">
                      <a href="<?= BASE_URL ?>/admin_index.php?module=<?= Security::escape($key) ?>&action=view&id=<?= (int)($item['id'] ?? 0) ?>" class="btn btn--sm btn--outline" title="Voir">
                        <i class="fas fa-eye"></i>
                      </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>
</div>

<?php require __DIR__ . '/admin_footer.php'; ?>
