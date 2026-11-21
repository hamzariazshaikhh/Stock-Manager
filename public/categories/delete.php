<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();

$isOwner = hasRole(ROLE_OWNER);


requirePermission(PERMISSION_MANAGE_CATEGORIES);

if (!isPostRequest()) {
    setFlash('error', 'Invalid request.');
    redirect('/categories/index.php');
}

verifyCsrfToken($_POST['csrf_token'] ?? null);

$categoryId = filter_var(
    $_POST['id'] ?? null,
    FILTER_VALIDATE_INT
);

if ($categoryId === false || $categoryId === null || $categoryId < 1) {
    setFlash('error', 'Invalid category.');
    redirect('/categories/index.php');
}

$pdo = getDatabaseConnection();
$repository = new CategoryRepository($pdo);
$service = new CategoryService($repository);

$deleted = $service->delete(
    $categoryId,
    $userId,
    $isOwner
);

if (!$deleted) {
    setFlash('error', 'Category not found.');
    redirect('/categories/index.php');
}

setFlash(
    'success',
    'Category deleted successfully.'
);

redirect('/categories/index.php');