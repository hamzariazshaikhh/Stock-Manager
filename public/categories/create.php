<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';


$userId = requireAuth();
$pdo = getDatabaseConnection();

$repository = new CategoryRepository($pdo);
$service = new CategoryService($repository);

$errors = [];
$name = '';

if (isPostRequest()) {
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    $name = trim((string) ($_POST['name'] ?? ''));

    $errors = $service->create($name, $userId);

    if ($errors === []) {
        setFlash(
            'success',
            'Category created successfully.'
        );

        redirect('/categories/index.php');
    }
}

$pageTitle = 'Create Category';
$activePage = 'categories';

require_once __DIR__ . '/../../app/views/layout/header.php';
?>

<section class="form-card">
    <h1>Create Category</h1>
    <p class="text-muted">
        Add a category for your products.
    </p>

    <?php require __DIR__ . '/../../app/views/partials/alert.php'; ?>

    <form method="post" novalidate>
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrfToken()) ?>"
        >

        <div class="form-group">
            <label for="name">Category name</label>

            <input
                id="name"
                name="name"
                type="text"
                maxlength="120"
                value="<?= e($name) ?>"
                required
                autofocus
            >
        </div>

        <div class="form-actions">
            <button
                class="button button-primary"
                type="submit"
            >
                Create Category
            </button>

            <a
                class="button button-secondary"
                href="<?= e(BASE_URL) ?>/categories/index.php"
            >
                Cancel
            </a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/../../app/views/layout/footer.php'; ?>