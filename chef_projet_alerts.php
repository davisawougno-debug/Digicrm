<?php
$flashMessages = Session::getFlash();
foreach ($flashMessages as $flash):
    $type = $flash['type'] ?? 'info';
    $icon = match($type) {
        'success' => 'fa-check-circle',
        'error'   => 'fa-exclamation-circle',
        'warning' => 'fa-exclamation-triangle',
        default   => 'fa-info-circle',
    };
?>
<div class="admin-alert admin-alert--<?= $type ?>">
    <i class="fas <?= $icon ?>"></i>
    <span><?= Security::escape($flash['message']) ?></span>
</div>
<?php endforeach; ?>
