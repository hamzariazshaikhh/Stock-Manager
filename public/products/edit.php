<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();
$pdo = getDatabaseConnection();

$productRepository = new ProductRepository($pdo);
$categoryRepository = new CategoryRepository($pdo);
$supplierRepository = new SupplierRepository($pdo);

$productService = new ProductService(
    $productRepository,
    $categoryRepository,
    $supplierRepository
);

$productId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (
    $productId === false
    || $productId === null
    || $productId < 1
) {
    setFlash('error', 'Invalid product.');
    redirect('/products/index.php');
}

$product = $productRepository->findByIdForUser(
    $productId,
    $userId
);

if ($product === null) {
    setFlash('error', 'Product not found.');
    redirect('/products/index.php');
}

$categories = $categoryRepository->findAllByUser($userId);
$suppliers = $supplierRepository->findAllByUser($userId);

$errors = [];

$formData = [
    'name' => $product['name'],
    'sku' => $product['sku'],
    'description' => $product['description'] ?? '',
    'category_id' => $product['category_id'] ?? '',
    'supplier_id' => $product['supplier_id'] ?? '',
    'quantity' => $product['quantity'],
    'reorder_level' => $product['reorder_level'],
    'unit_price' => $product['unit_price'],
];

if (isPostRequest()) {
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    $formData = [
        'name' => trim((string) ($_POST['name'] ?? '')),
        'sku' => trim((string) ($_POST['sku'] ?? '')),
        'description' => trim(
            (string) ($_POST['description'] ?? '')
        ),
        'category_id' => $_POST['category_id'] ?? '',
        'supplier_id' => $_POST['supplier_id'] ?? '',
        'quantity' => $product['quantity'],
        'reorder_level' => $_POST['reorder_level'] ?? '',
        'unit_price' => $_POST['unit_price'] ?? '',
    ];

    $errors = $productService->update(
        $productId,
        $formData,
        $userId
    );

    if ($errors === []) {
        setFlash(
            'success',
            'Product updated successfully.'
        );

        redirect('/products/index.php');
    }
}

$pageTitle = 'Edit Product';
$activePage = 'products';

require_once __DIR__ . '/../../app/views/layout/header.php';
?>

<section class="form-card">
    <h1>Edit Product</h1>
    <p class="text-muted">
        Update product details. Use Stock Management to change quantity.
    </p>

    <?php require __DIR__ . '/../../app/views/partials/alert.php'; ?>

    <form method="post" novalidate>
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrfToken()) ?>"
        >

        <div class="form-group">
            <label for="name">Product name</label>
            <input
                id="name"
                name="name"
                type="text"
                maxlength="150"
                value="<?= e($formData['name']) ?>"
                required
                autofocus
            >
        </div>

        <div class="form-group">
            <label for="sku">SKU</label>
            <input
                id="sku"
                name="sku"
                type="text"
                maxlength="100"
                value="<?= e($formData['sku']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                rows="4"
            ><?= e($formData['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
                <option value="">No category</option>

                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= e($category['id']) ?>"
                        <?= (string) $formData['category_id']
                            === (string) $category['id']
                            ? 'selected'
                            : '' ?>
                    >
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="supplier_id">Supplier</label>
            <select id="supplier_id" name="supplier_id">
                <option value="">No supplier</option>

                <?php foreach ($suppliers as $supplier): ?>
                    <option
                        value="<?= e($supplier['id']) ?>"
                        <?= (string) $formData['supplier_id']
                            === (string) $supplier['id']
                            ? 'selected'
                            : '' ?>
                    >
                        <?= e($supplier['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity">Current quantity</label>
            <input
                id="quantity"
                type="number"
                value="<?= e($formData['quantity']) ?>"
                readonly
            >
            <small class="text-muted">
                Change quantity from the stock movement screen.
            </small>
        </div>

        <div class="form-group">
            <label for="reorder_level">Reorder level</label>
            <input
                id="reorder_level"
                name="reorder_level"
                type="number"
                min="0"
                step="1"
                value="<?= e($formData['reorder_level']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="unit_price">Unit price</label>
            <input
                id="unit_price"
                name="unit_price"
                type="number"
                min="0"
                step="0.01"
                value="<?= e($formData['unit_price']) ?>"
                required
            >
        </div>

        <div class="form-actions">
            <button
                class="button button-primary"
                type="submit"
            >
                Save Changes
            </button>

            <a
                class="button button-secondary"
                href="<?= e(BASE_URL) ?>/products/index.php"
            >
                Cancel
            </a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/../../app/views/layout/footer.php'; ?>