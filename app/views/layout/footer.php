<?php
declare(strict_types=1);
?>

</main>

<footer class="site-footer">
    <div class="container">
        <p>
            &copy; <?= date('Y') ?> <?= e(APP_NAME) ?>
        </p>
    </div>
</footer>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>

<!-- Local application scripts -->
<script src="<?= e(BASE_URL) ?>/assets/js/app.js"></script>
<script src="<?= e(BASE_URL) ?>/assets/js/charts.js"></script>
</body>
</html>
