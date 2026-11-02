<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();

if (!isPostRequest()) {
    setFlash('error', 'Invalid request.');
    redirect('/suppliers/index.php');
}

verifyCsrfToken($_POST['csrf_token'] ?? null);

$supplierId = filter_var(
    $_POST['id'] ?? null,
    FILTER_VALIDATE_INT
);

if (
    $supplierId === false
    || $supplierId === null
    || $supplierId < 1
) {
    setFlash('error', 'Invalid supplier.');
    redirect('/suppliers/index.php');
}

$pdo = getDatabaseConnection();

$supplierRepository = new SupplierRepository($pdo);
$supplierService = new SupplierService($supplierRepository);

$deleted = $supplierService->delete(
    $supplierId,
    $userId
);

if (!$deleted) {
    setFlash('error', 'Supplier not found.');
    redirect('/suppliers/index.php');
}

setFlash(
    'success',
    'Supplier deleted successfully.'
);

redirect('/suppliers/index.php');