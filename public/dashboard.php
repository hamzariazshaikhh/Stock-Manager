<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$userId = requireAuth();

$pdo = getDatabaseConnection();

$userStatement = $pdo->prepare(
    'SELECT name, email
     FROM users
     WHERE id = :user_id
     LIMIT 1'
);

$userStatement->execute([
    'user_id' => $userId,
]);

$user = $userStatement->fetch();

if ($user === false) {
    logoutUser();

    session_start();

    setFlash(
        'error',
        'Your account could not be found.'
    );

    redirect('/login.php');
}

$dashboardRepository = new DashboardRepository($pdo);
$dashboardService = new DashboardService(
    $dashboardRepository
);

$summary = $dashboardService->getSummary($userId);

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

require_once __DIR__ . '/../app/views/layout/header.php';
?>

<section class="page-header">
    <div>
        <h1>Dashboard</h1>

        <p class="text-muted">
            Welcome, <?= e($user['name']) ?>.
        </p>
    </div>
</section>

<section class="dashboard-stats">
    <div class="stat-card">
        <span class="stat-label">Products</span>
        <strong class="stat-value">
            <?= e($summary['product_count']) ?>
        </strong>
    </div>

    <div class="stat-card">
        <span class="stat-label">Categories</span>
        <strong class="stat-value">
            <?= e($summary['category_count']) ?>
        </strong>
    </div>

    <div class="stat-card">
        <span class="stat-label">Suppliers</span>
        <strong class="stat-value">
            <?= e($summary['supplier_count']) ?>
        </strong>
    </div>

    <div class="stat-card">
        <span class="stat-label">Total Stock</span>
        <strong class="stat-value">
            <?= e($summary['total_stock']) ?>
        </strong>
    </div>

    <div class="stat-card stat-card-warning">
        <span class="stat-label">Low-stock Products</span>
        <strong class="stat-value">
            <?= e($summary['low_stock_count']) ?>
        </strong>
    </div>

    <div class="stat-card">
        <span class="stat-label">Inventory Value</span>
        <strong class="stat-value">
            $<?= e($summary['inventory_value']) ?>
        </strong>
    </div>
</section>

<section class="dashboard-grid">
    <a
        class="dashboard-action-card"
        href="<?= e(BASE_URL) ?>/products/index.php"
    >
        <span class="dashboard-action-icon">📦</span>

        <h2>Products</h2>

        <p>
            Add, edit, view, and remove inventory products.
        </p>

        <span class="dashboard-action-link">
            Manage Products
        </span>
    </a>

    <a
        class="dashboard-action-card"
        href="<?= e(BASE_URL) ?>/categories/index.php"
    >
        <span class="dashboard-action-icon">🏷️</span>

        <h2>Categories</h2>

        <p>
            Organize your products into categories.
        </p>

        <span class="dashboard-action-link">
            Manage Categories
        </span>
    </a>

    <a
        class="dashboard-action-card"
        href="<?= e(BASE_URL) ?>/suppliers/index.php"
    >
        <span class="dashboard-action-icon">🚚</span>

        <h2>Suppliers</h2>

        <p>
            Manage supplier contact information.
        </p>

        <span class="dashboard-action-link">
            Manage Suppliers
        </span>
    </a>

    <a
        class="dashboard-action-card"
        href="<?= e(BASE_URL) ?>/stock/index.php"
    >
        <span class="dashboard-action-icon">📊</span>

        <h2>Stock Movements</h2>

        <p>
            Record and review inventory changes.
        </p>

        <span class="dashboard-action-link">
            Manage Stock
        </span>
    </a>
</section>

<section class="quick-actions-card">
    <div class="section-heading">
        <div>
            <h2>Quick Actions</h2>

            <p class="text-muted">
                Start managing your inventory.
            </p>
        </div>
    </div>

    <div class="quick-actions">
        <a
            class="button button-primary"
            href="<?= e(BASE_URL) ?>/products/create.php"
        >
            Add Product
        </a>

        <a
            class="button button-secondary"
            href="<?= e(BASE_URL) ?>/categories/create.php"
        >
            Add Category
        </a>

        <a
            class="button button-secondary"
            href="<?= e(BASE_URL) ?>/suppliers/create.php"
        >
            Add Supplier
        </a>

        <a
            class="button button-secondary"
            href="<?= e(BASE_URL) ?>/stock/create.php"
        >
            Record Stock Movement
        </a>
    </div>
</section>

<section class="dashboard-card">
    <h2>Recent Stock Movements</h2>

    <?php if ($summary['recent_movements'] === []): ?>
        <p class="text-muted">
            No stock movements have been recorded yet.
        </p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Change</th>
                        <th>After</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach (
                        $summary['recent_movements'] as $movement
                    ): ?>
                        <tr>
                            <td>
                                <?= e($movement['created_at']) ?>
                            </td>

                            <td>
                                <?= e($movement['product_name']) ?>
                            </td>

                            <td>
                                <?= e(
                                    ucfirst(
                                        $movement['movement_type']
                                    )
                                ) ?>
                            </td>

                            <td>
                                <?= e($movement['quantity_change']) ?>
                            </td>

                            <td>
                                <?= e($movement['quantity_after']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="dashboard-card">
    <h2>Account Information</h2>

    <dl class="details-list">
        <div>
            <dt>Name</dt>
            <dd><?= e($user['name']) ?></dd>
        </div>

        <div>
            <dt>Email</dt>
            <dd><?= e($user['email']) ?></dd>
        </div>
    </dl>
</section>

<?php require_once __DIR__ . '/../app/views/layout/footer.php'; ?>