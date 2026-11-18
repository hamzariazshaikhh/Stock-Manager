<section class="dashboard-stats" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <span class="stat-label">Products</span>
        <strong class="stat-value"><?= e($summary['product_count']) ?></strong>
    </div>
    <div class="stat-card">
        <span class="stat-label">Categories</span>
        <strong class="stat-value"><?= e($summary['category_count']) ?></strong>
    </div>
    <div class="stat-card">
        <span class="stat-label">Suppliers</span>
        <strong class="stat-value"><?= e($summary['supplier_count']) ?></strong>
    </div>
    <div class="stat-card">
        <span class="stat-label">Total Stock</span>
        <strong class="stat-value"><?= e($summary['total_stock']) ?></strong>
    </div>
</section>
