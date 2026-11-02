<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();

$pdo = getDatabaseConnection();

$productRepository = new ProductRepository($pdo);
$products = $productRepository->findAllByUser($userId);

$errors = [];

$formData = [
    'product_id' => '',
    'movement_type' => 'in',
    'quantity' => '',
    'note' => '',
];

if (isPostRequest()) {
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    $formData = [
        'product_id' => $_POST['product_id'] ?? '',
        'movement_type' => $_POST['movement_type'] ?? '',
        'quantity' => $_POST['quantity'] ?? '',
        'note' => trim((string) ($_POST['note'] ?? '')),
    ];

    $productId = filter_var(
        $formData['product_id'],
        FILTER_VALIDATE_INT
    );

    if (
        $productId === false
        || $productId === null
        || $productId < 1
    ) {
        $errors[] = 'Please select a valid product.';
    }

    if ($errors === []) {
        $movementRepository = new StockMovementRepository($pdo);

        $stockService = new StockService(
            $pdo,
            $productRepository,
            $movementRepository
        );

        $errors = $stockService->recordMovement(
            $userId,
            $productId,
            (string) $formData['movement_type'],
            $formData['quantity'],
            $formData['note']
        );

        if ($errors === []) {
            setFlash(
                'success',
                'Stock movement recorded successfully.'
            );

            redirect('/stock/index.php');
        }
    }
}

$pageTitle = 'Record Stock Movement';
$activePage = 'stock';

require_once __DIR__ . '/../../app/views/layout/header.php';
?>

<section class="form-card">
    <h1>Record Stock Movement</h1>

    <p class="text-muted">
        Increase or decrease a product's stock quantity.
    </p>

    <?php require __DIR__ . '/../../app/views/partials/alert.php'; ?>

    <form method="post" novalidate>
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrfToken()) ?>"
        >

        <div class="form-group">
            <label for="product_id">Product</label>

            <select
                id="product_id"
                name="product_id"
                required
            >
                <option value="">Select a product</option>

                <?php foreach ($products as $product): ?>
                    <option
                        value="<?= e($product['id']) ?>"
                        <?= (string) $formData['product_id']
                            === (string) $product['id']
                            ? 'selected'
                            : '' ?>
                    >
                        <?= e($product['name']) ?>
                        -
                        <?= e($product['sku']) ?>
                        (Current:
                        <?= e($product['quantity']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="movement_type">Movement type</label>

            <select
                id="movement_type"
                name="movement_type"
                required
            >
                <option
                    value="in"
                    <?= $formData['movement_type'] === 'in'
                        ? 'selected'
                        : '' ?>
                >
                    Stock In
                </option>

                <option
                    value="out"
                    <?= $formData['movement_type'] === 'out'
                        ? 'selected'
                        : '' ?>
                >
                    Stock Out
                </option>

                <option
                    value="adjustment"
                    <?= $formData['movement_type'] === 'adjustment'
                        ? 'selected'
                        : '' ?>
                >
                    Adjustment Increase
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>

            <input
                id="quantity"
                name="quantity"
                type="number"
                min="1"
                step="1"
                value="<?= e($formData['quantity']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="note">Note</label>

            <textarea
                id="note"
                name="note"
                rows="4"
                maxlength="255"
            ><?= e($formData['note']) ?></textarea>
        </div>

        <div class="form-actions">
            <button
                class="button button-primary"
                type="submit"
            >
                Record Movement
            </button>

            <a
                class="button button-secondary"
                href="<?= e(BASE_URL) ?>/stock/index.php"
            >
                Cancel
            </a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/../../app/views/layout/footer.php'; ?>