<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();
requirePermission(PERMISSION_VIEW_DASHBOARD);

$pdo = getDatabaseConnection();
$dashboardRepository = new DashboardRepository($pdo);
$productRepository = new ProductRepository($pdo);

$summary = [
    'product_count' => $dashboardRepository->countProductsByUser($userId),
    'category_count' => $dashboardRepository->countCategoriesByUser($userId),
    'supplier_count' => $dashboardRepository->countSuppliersByUser($userId),
    'total_stock' => $dashboardRepository->totalStockByUser($userId),
    'low_stock_count' => $dashboardRepository->countLowStockProducts($userId),
    'inventory_value' => $dashboardRepository->inventoryValue($userId),
    'recent_movements' => $dashboardRepository->recentMovements($userId, 10),
];

$lowStockProducts = $productRepository->findAllByUser($userId);
$lowStockProducts = array_filter($lowStockProducts, fn($p) => (int)$p['quantity'] <= (int)$p['reorder_level']);

$pageTitle = 'Inventory Reports';
$activePage = 'reports';

require_once __DIR__ . '/../../app/views/layout/header.php';
?>

<section class="page-header print-hidden">
    <div>
        <h1>Inventory Summary Report</h1>
        <p class="text-muted">Generated on <?= date('F j, Y, g:i a') ?></p>
    </div>

    <!-- 100% Reliable Print Link styled as a Button -->
    <a href="javascript:window.print();" class="button button-primary">
        Print / Save as PDF
    </a>
</section>

<!-- Report Container -->
<div class="report-container">
    <div class="report-header" style="display: none; margin-bottom: 2rem;">
        <h1><?= e(APP_NAME) ?> - Inventory Report</h1>
        <p>Generated on <?= date('F j, Y, g:i a') ?></p>
        <hr style="margin-top: 1rem;">
    </div>

    <!-- Summary Cards Grid -->
    <section class="dashboard-stats" style="margin-bottom: 2rem;">
        <div class="stat-card">
            <span class="stat-label">Total Products</span>
            <strong class="stat-value"><?= e($summary['product_count']) ?></strong>
        </div>

        <div class="stat-card">
            <span class="stat-label">Total Stock Units</span>
            <strong class="stat-value"><?= e($summary['total_stock']) ?></strong>
        </div>

        <div class="stat-card">
            <span class="stat-label">Low-Stock Items</span>
            <strong class="stat-value" style="color: #e53e3e;"><?= e($summary['low_stock_count']) ?></strong>
        </div>

        <div class="stat-card">
            <span class="stat-label">Inventory Valuation</span>
            <strong class="stat-value">PKR <?= e($summary['inventory_value']) ?></strong>
        </div>
    </section>

    <!-- Low Stock Alert Table -->
    <section class="table-card" style="margin-bottom: 2rem;">
        <h2>Low-Stock Alert Items</h2>
        <?php if (empty($lowStockProducts)): ?>
            <p class="text-muted" style="padding: 1rem;">All stock levels are healthy.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Current Quantity</th>
                            <th>Reorder Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStockProducts as $product): ?>
                            <tr>
                                <td><?= e($product['name']) ?></td>
                                <td><?= e($product['sku']) ?></td>
                                <td style="color: #e53e3e; font-weight: bold;"><?= e($product['quantity']) ?></td>
                                <td><?= e($product['reorder_level']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <!-- Recent Stock Movements Table -->
    <section class="table-card">
        <h2>Recent Stock Activity</h2>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Change</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($summary['recent_movements'] as $movement): ?>
                        <tr>
                            <td><?= e($movement['created_at']) ?></td>
                            <td><?= e($movement['product_name']) ?></td>
                            <td><?= e(ucfirst($movement['movement_type'])) ?></td>
                            <td><?= e($movement['quantity_change']) ?></td>
                            <td><?= e($movement['note'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- Print Styles -->
<style>
@media print {
    .print-hidden, .site-header, .site-footer, .main-navigation {
        display: none !important;
    }
    .report-header {
        display: block !important;
    }
    body {
        background: #fff;
        color: #000;
    }
    .table-card, .stat-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<script>
(function() {
    function setupPrintButton() {
        const printButton = document.getElementById('printButton');
        if (printButton) {
            // Remove any existing listeners to avoid duplicates
            const newButton = printButton.cloneNode(true);
            printButton.parentNode.replaceChild(newButton, printButton);
            
            newButton.addEventListener('click', function (e) {
                e.preventDefault();
                window.print();
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupPrintButton);
    } else {
        setupPrintButton();
    }
})();
</script>

<!-- Print Styles -->
<style>
@media print {
    .print-hidden, .site-header, .site-footer, .main-navigation {
        display: none !important;
    }
    .report-header {
        display: block !important;
    }
    body {
        background: #fff;
        color: #000;
    }
    .table-card, .stat-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>


<?php require_once __DIR__ . '/../../app/views/layout/footer.php'; ?>
