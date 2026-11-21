<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();
requirePermission(PERMISSION_VIEW_CATEGORIES);
$pdo = getDatabaseConnection();

$isOwner = hasRole(ROLE_OWNER);

$repository = new CategoryRepository($pdo);
$categories = $repository->findAllByUser($userId, $isOwner);

$pageTitle = 'Categories';
$activePage = 'categories';

require_once __DIR__ . '/../../app/views/layout/header.php';
?>

<section class="page-header">
    <div>
        <h1>Categories</h1>
        <p class="text-muted">
            Organize your products by category.
        </p>
    </div>



<?php if (hasPermission(PERMISSION_MANAGE_CATEGORIES)): ?>
    <a
        class="button button-primary"
        href="<?= e(BASE_URL) ?>/categories/create.php"
    >
        Add Category
    </a>

<?php endif; ?>

</section>

<?php if ($categories === []): ?>
    <section class="dashboard-card">
        <h2>No categories yet</h2>
        <p class="text-muted">
            Create your first category to organize your products.
        </p>

        <a
            class="button button-primary"
            href="<?= e(BASE_URL) ?>/categories/create.php"
        >
            Create Category
        </a>
    </section>
<?php else: ?>
    <section class="table-card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Created</th>
                        <th>Added By</th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= e($category['name']) ?></td>
                            <td>
                                <?= e($category['created_at']) ?>
                            </td>
                            <td>
                                <?= e($category['user_email'] ?? '') ?>
                            </td>
                            <td class="actions">
                                <a
                                    class="button button-small"
                                    href="<?= e(BASE_URL) ?>/categories/edit.php?id=<?= e($category['id']) ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    method="post"
                                    action="<?= e(BASE_URL) ?>/categories/delete.php"
                                >
                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e(csrfToken()) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= e($category['id']) ?>"
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