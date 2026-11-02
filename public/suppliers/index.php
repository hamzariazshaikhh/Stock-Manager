<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();

$pdo = getDatabaseConnection();

$supplierRepository = new SupplierRepository($pdo);
$suppliers = $supplierRepository->findAllByUser($userId);

$pageTitle = 'Suppliers';
$activePage = 'suppliers';

require_once __DIR__ . '/../../app/views/layout/header.php';
?>

<section class="page-header">
    <div>
        <h1>Suppliers</h1>

        <p class="text-muted">
            Manage the suppliers connected to your products.
        </p>
    </div>

    <a
        class="button button-primary"
        href="<?= e(BASE_URL) ?>/suppliers/create.php"
    >
        Add Supplier
    </a>
</section>

<?php if ($suppliers === []): ?>
    <section class="dashboard-card">
        <h2>No suppliers yet</h2>

        <p class="text-muted">
            Add your first supplier.
        </p>

        <a
            class="button button-primary"
            href="<?= e(BASE_URL) ?>/suppliers/create.php"
        >
            Create Supplier
        </a>
    </section>
<?php else: ?>
    <section class="table-card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td><?= e($supplier['name']) ?></td>
                            <td><?= e($supplier['email'] ?? '') ?></td>
                            <td><?= e($supplier['phone'] ?? '') ?></td>
                            <td><?= e($supplier['address'] ?? '') ?></td>
                            <td class="actions">
                                <a
                                    class="button button-small"
                                    href="<?= e(BASE_URL) ?>/suppliers/edit.php?id=<?= e($supplier['id']) ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    method="post"
                                    action="<?= e(BASE_URL) ?>/suppliers/delete.php"
                                >
                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e(csrfToken()) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= e($supplier['id']) ?>"
                                    >

                                    <button
                                        class="button button-small button-danger"
                                        type="submit"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../../app/views/layout/footer.php'; ?>