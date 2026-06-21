<?php if (defined('AJAX_REQUEST') && AJAX_REQUEST) return; ?>
    <script>var BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="<?= BASE_URL ?>/admin.js"></script>
    <script src="<?= BASE_URL ?>/commercial.js"></script>
</body>
</html>
