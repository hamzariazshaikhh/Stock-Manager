<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();
requirePermission(PERMISSION_VIEW_PRODUCTS);
$pdo = getDatabaseConnection();

$productRepository = new ProductRepository($pdo);
$perPage = 25;
$page = currentPage();

$totalProducts = $productRepository->countByUser($userId);
$totalPages = totalPages($totalProducts, $perPage);
$page = min($page, $totalPages);

$products = $productRepository->findPageByUser(
    $userId,
    $perPage,
    paginationOffset($page, $perPage)
);

$pageTitle = 'Products';
$activePage = 'products';

require_once __DIR__ . '/../../app/views/layout/header.php';


$currentPage = $page;
$pageCount = $totalPages;
require __DIR__ . '/../../app/views/partials/pagination.php';
?>

<section class="page-header">
    <div>
        <h1>Products</h1>
        <p class="text-muted">
            Manage your inventory products.
        </p>
    </div>



<?php /*if (hasPermission(PERMISSION_MANAGE_PRODUCTS)): ?>
    <a
        class="button button-primary"
        href="<?= e(BASE_URL) ?>/products/create.php"
    >
        Add Product
    </a>

<?php endif;  */ ?>

<?php if (hasPermission(PERMISSION_MANAGE_PRODUCTS)): ?>
    <a
        class="button button-secondary"
        href="<?= e(BASE_URL) ?>/products/export.php"
    >
        Export CSV
    </a>

    <a
        class="button button-secondary"
        href="<?= e(BASE_URL) ?>/products/import.php"
    >
        Import CSV
    </a>

    <a
        class="button button-primary"
        href="<?= e(BASE_URL) ?>/products/create.php"
    >
        Add Product
    </a>
<?php endif; ?>



</section>

<?php if ($products === []): ?>
    <section class="dashboard-card">
        <h2>No products yet</h2>
        <p class="text-muted">
            Add your first product to start managing inventory.
        </p>

        <a
            class="button button-primary"
            href="<?= e(BASE_URL) ?>/products/create.php"
        >
            Create Product
        </a>
    </section>
<?php else: ?>
    <section class="table-card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($products as $product): ?>
                        <?php
                        $isLowStock = (int) $product['quantity']
                            <= (int) $product['reorder_level'];
                        ?>
                        <tr>
                            <td><?= e($product['name']) ?></td>
                            <td><?= e($product['sku']) ?></td>
                            <td>
                                <?= e(
                                    $product['category_name']
                                    ?? 'Uncategorized'
                                ) ?>
                            </td>
                            <td>
                                <?= e(
                                    $product['supplier_name']
                                    ?? 'No supplier'
                                ) ?>
                            </td>
                            <td>
                                <?= e($product['quantity']) ?>

                                <?php if ($isLowStock): ?>
                                    <span class="text-muted">
                                        (Low stock)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                PKR <?= e(
                                    number_format(
                                        (float) $product['unit_price'],
                                        2
                                    )
                                ) ?>
                            </td>
                            <td class="actions">

                            <?php if (hasPermission(PERMISSION_MANAGE_PRODUCTS)): ?>
                                <a
                                    class="button button-small"
                                    href="<?= e(BASE_URL) ?>/products/edit.php?id=<?= e($product['id']) ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    method="post"
                                    action="<?= e(BASE_URL) ?>/products/delete.php"
                                    data-confirm="Archive this product? Its stock history will be preserved."
                                >
                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e(csrfToken()) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= e($product['id']) ?>"
                                    >

                                    <button
                                        class="button button-small button-danger"
                                        type="submit"
                                    >
                                        Delete
                                    </button>
                                </form>

                            <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../../app/views/layout/footer.php'; ?>