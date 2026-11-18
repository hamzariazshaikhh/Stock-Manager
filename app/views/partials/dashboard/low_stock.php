<?php if ($summary['low_stock_count'] > 0): ?>
    <div class="stat-card stat-card-warning">
        <span class="stat-label">Low-stock Products</span>
        <strong class="stat-value"><?= e($summary['low_stock_count']) ?></strong>
    </div>
<?php endif; ?>
