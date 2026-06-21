<?php
$flashMessages = Session::getFlash();
?>
<?php if (!empty($flashMessages)): ?>
    <?php foreach ($flashMessages as $flash): ?>
        <div class="admin-alert admin-alert--<?= Security::escape($flash['type']) ?>">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'times-circle' : 'exclamation-circle') ?>"></i>
            <span><?= Security::escape($flash['message']) ?></span>
            <button class="admin-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
