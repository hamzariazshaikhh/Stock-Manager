<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();

$pdo = getDatabaseConnection();

$movementRepository = new StockMovementRepository($pdo);
$movements = $movementRepository->findAllByUser($userId);

$pageTitle = 'Stock Movements';
$activePage = 'stock';

require_once __DIR__ . '/../../app/views/layout/header.php';
?>

<section class="page-header">
    <div>
        <h1>Stock Movements</h1>

        <p class="text-muted">
            Review inventory additions, removals, and adjustments.
        </p>
    </div>

    <a
        class="button button-primary"
        href="<?= e(BASE_URL) ?>/stock/create.php"
    >
        Record Movement
    </a>
</section>

<?php if ($movements === []): ?>
    <section class="dashboard-card">
        <h2>No stock movements yet</h2>

        <p class="text-muted">
            Record your first stock movement to begin tracking inventory.
        </p>

        <a
            class="button button-primary"
            href="<?= e(BASE_URL) ?>/stock/create.php"
        >
            Record Movement
        </a>
    </section>
<?php else: ?>
    <section class="table-card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Change</th>
                        <th>Before</th>
                        <th>After</th>
                        <th>Note</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($movements as $movement): ?>
                        <tr>
                            <td>
                                <?= e($movement['created_at']) ?>
                            </td>

                            <td>
                                <?= e($movement['product_name']) ?>
                                <br>
                                <small>
                                    <?= e($movement['sku']) ?>
                                </small>
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
                                <?= e($movement['quantity_before']) ?>
                            </td>

                            <td>
                                <?= e($movement['quantity_after']) ?>
                            </td>

                            <td>
                                <?= e($movement['note'] ?? '') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../../app/views/layout/footer.php'; ?>