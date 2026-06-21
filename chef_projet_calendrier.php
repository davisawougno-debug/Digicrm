<?php
$pageTitle = $pageTitle ?? 'Calendrier';
require __DIR__ . '/chef_projet_header.php';
require __DIR__ . '/chef_projet_navbar.php';
require __DIR__ . '/chef_projet_sidebar.php';
$data = $GLOBALS['viewData'] ?? [];
$events = $data['events'] ?? [];
$year = $data['year'] ?? date('Y');
$month = $data['month'] ?? date('m');
$monthName = $data['monthName'] ?? '';
?>
<div class="admin-main">
  <div class="admin-container">
    <?php require __DIR__ . '/chef_projet_alerts.php'; ?>

    <div class="page-header">
      <div class="page-header-info">
        <h1 class="page-title">Calendrier</h1>
        <p class="page-description">Échéances et jalons</p>
      </div>
      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=calendrier&month=<?= $month == 1 ? 12 : $month - 1 ?>&year=<?= $month == 1 ? $year - 1 : $year ?>" class="btn btn--sm btn--outline"><i class="fas fa-chevron-left"></i></a>
        <span class="calendar-nav-label"><?= $monthName ?> <?= $year ?></span>
        <a href="<?= BASE_URL ?>/chef_projet_index.php?module=calendrier&month=<?= $month == 12 ? 1 : $month + 1 ?>&year=<?= $month == 12 ? $year + 1 : $year ?>" class="btn btn--sm btn--outline"><i class="fas fa-chevron-right"></i></a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="calendar-grid">
          <div class="calendar-header">Lun</div>
          <div class="calendar-header">Mar</div>
          <div class="calendar-header">Mer</div>
          <div class="calendar-header">Jeu</div>
          <div class="calendar-header">Ven</div>
          <div class="calendar-header">Sam</div>
          <div class="calendar-header">Dim</div>

          <?php
          $firstDay = mktime(0, 0, 0, $month, 1, $year);
          $startDow = (date('N', $firstDay) - 1 + 7) % 7;
          $daysInMonth = date('t', $firstDay);
          $today = date('Y-m-d');

          for ($i = 0; $i < $startDow; $i++):
          ?>
            <div class="calendar-day calendar-day--empty"></div>
          <?php endfor ?>

          <?php for ($day = 1; $day <= $daysInMonth; $day++):
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $dayEvents = array_filter($events, function($e) use ($date) {
              return ($e['date_echeance'] ?? $e['date_fin'] ?? '') === $date;
            });
            $isToday = $date === $today;
          ?>
            <div class="calendar-day <?= $isToday ? 'calendar-day--today' : '' ?> <?= !empty($dayEvents) ? 'calendar-day--has-events' : '' ?>">
              <div class="calendar-day-number"><?= $day ?></div>
              <?php foreach (array_slice($dayEvents, 0, 3) as $e): ?>
                <div class="calendar-event calendar-event--<?= $e['type'] ?? 'task' ?>">
                  <?= Security::escape(mb_substr($e['titre'] ?? $e['nom_projet'] ?? '', 0, 15)) ?>
                </div>
              <?php endforeach ?>
              <?php if (count($dayEvents) > 3): ?>
                <div class="calendar-event calendar-event--more">+<?= count($dayEvents) - 3 ?> autres</div>
              <?php endif ?>
            </div>
          <?php endfor ?>
        </div>
      </div>
    </div>

    <?php if (!empty($events)): ?>
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="fas fa-list"></i> Tous les événements</div></div>
      <div class="card-body p-0">
        <div class="table-mini">
          <?php foreach ($events as $e): ?>
          <div class="table-mini-row">
            <div class="table-mini-avatar table-mini-avatar--alt"><i class="fas fa-<?= ($e['type'] ?? 'task') === 'milestone' ? 'flag' : 'calendar-day' ?>"></i></div>
            <div class="table-mini-info">
              <div class="table-mini-name"><?= Security::escape($e['titre'] ?? $e['nom_projet'] ?? '') ?></div>
              <div class="table-mini-sub">
                <span class="badge badge--<?= $e['statut'] ?? '' ?>"><?= $e['statut'] ?? '' ?></span>
                · <?= date('d/m/Y', strtotime($e['date_echeance'] ?? $e['date_fin'] ?? '')) ?>
                · <?= $e['type'] === 'milestone' ? 'Jalon' : 'Tâche' ?>
              </div>
            </div>
          </div>
          <?php endforeach ?>
        </div>
      </div>
    </div>
    <?php endif ?>
  </div>
</div>
<?php require __DIR__ . '/chef_projet_footer.php'; ?>
