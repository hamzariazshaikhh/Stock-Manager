<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();
requirePermission(PERMISSION_VIEW_PRODUCTS);

$pdo = getDatabaseConnection();
$productRepository = new ProductRepository($pdo);
$products = $productRepository->findAllByUser($userId);

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="inventory_products_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// Output CSV column headers
fputcsv($output, ['Name', 'SKU', 'Description', 'Quantity', 'Reorder Level', 'Unit Price', 'Category', 'Supplier', 'Created At']);

// Output data rows
foreach ($products as $product) {
    fputcsv($output, [
        $product['name'],
        $product['sku'],
        $product['description'] ?? '',
        $product['quantity'],
        $product['reorder_level'],
        $product['unit_price'],
        $product['category_name'] ?? '',
        $product['supplier_name'] ?? '',
        $product['created_at'],
    ]);
}

fclose($output);
exit;
