<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();

if (!isPostRequest()) {
    setFlash('error', 'Invalid request.');
    redirect('/stock/index.php');
}

verifyCsrfToken($_POST['csrf_token'] ?? null);

$productId = filter_var(
    $_POST['product_id'] ?? null,
    FILTER_VALIDATE_INT
);

if ($productId === false || $productId === null || $productId < 1) {
    setFlash('error', 'Invalid product.');
    redirect('/stock/create.php');
}

$movementType = (string) (
    $_POST['movement_type'] ?? ''
);

$quantity = $_POST['quantity'] ?? null;
$note = $_POST['note'] ?? null;

$pdo = getDatabaseConnection();

$productRepository = new ProductRepository($pdo);
$movementRepository = new StockMovementRepository($pdo);

$stockService = new StockService(
    $pdo,
    $productRepository,
    $movementRepository
);

$errors = $stockService->recordMovement(
    $userId,
    $productId,
    $movementType,
    $quantity,
    $note
);

if ($errors !== []) {
    setFlash('error', implode(' ', $errors));
    redirect('/stock/create.php');
}

setFlash(
    'success',
    'Stock movement recorded successfully.'
);

redirect('/stock/index.php');