<?php if (defined('AJAX_REQUEST') && AJAX_REQUEST): ?><!-- AJAX content start --><?php return; ?><?php endif ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Security::escape($pageTitle ?? 'Dashboard') ?> - DigiCRM Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin.css">
</head>
<body>
