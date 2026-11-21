<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();


$isOwner = hasRole(ROLE_OWNER);

requirePermission(PERMISSION_MANAGE_PRODUCTS);

if (!isPostRequest()) {
    setFlash('error', 'Invalid request.');
    redirect('/products/index.php');
}

verifyCsrfToken($_POST['csrf_token'] ?? null);

$productId = filter_var(
    $_POST['id'] ?? null,
    FILTER_VALIDATE_INT
);

if ($productId === false || $productId === null || $productId < 1) {
    setFlash('error', 'Invalid product.');
    redirect('/products/index.php');
}

$pdo = getDatabaseConnection();

// 1. Initialize ProductService correctly
$productService = new ProductService(
    new ProductRepository($pdo),
    new CategoryRepository($pdo),
    new SupplierRepository($pdo)
);

// 2. Call the Service method, NOT the Repository directly
// This triggers the Audit Log we added in the previous step
$deleted = $productService->delete($productId, $userId, $isOwner);

if (!$deleted) {
    setFlash('error', 'Product not found.');
    redirect('/products/index.php');
}

setFlash(
    'success',
    'Product deleted successfully.'
);

redirect('/products/index.php');
