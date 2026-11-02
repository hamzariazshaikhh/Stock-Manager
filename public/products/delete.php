<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();

if (!isPostRequest()) {
    setFlash('error', 'Invalid request.');
    redirect('/products/index.php');
}

verifyCsrfToken($_POST['csrf_token'] ?? null);

$productId = filter_var(
    $_POST['id'] ?? null,
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

$pdo = getDatabaseConnection();
$productRepository = new ProductRepository($pdo);

$deleted = $productRepository->delete(
    $productId,
    $userId
);

if (!$deleted) {
    setFlash('error', 'Product not found.');
    redirect('/products/index.php');
}

setFlash(
    'success',
    'Product deleted successfully.'
);

redirect('/products/index.php');