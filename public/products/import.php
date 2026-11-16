<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();
requirePermission(PERMISSION_MANAGE_PRODUCTS);

$errors = [];
$successCount = 0;

if (isPostRequest()) {
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['csv_file']['tmp_name'];
        $fileName = $_FILES['csv_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExtension !== 'csv') {
            $errors[] = 'Please upload a valid .csv file.';
        } else {
            $pdo = getDatabaseConnection();
            $productService = new ProductService(
                new ProductRepository($pdo),
                new CategoryRepository($pdo),
                new SupplierRepository($pdo)
            );

            if (($handle = fopen($fileTmpPath, 'r')) !== false) {
                // Skip header row
                fgetcsv($handle);

                while (($row = fgetcsv($handle)) !== false) {
                    // Expected CSV format: Name, SKU, Description, Quantity, Reorder Level, Unit Price
                    $input = [
                        'name' => trim($row[0] ?? ''),
                        'sku' => trim($row[1] ?? ''),
                        'description' => trim($row[2] ?? ''),
                        'quantity' => trim($row[3] ?? '0'),
                        'reorder_level' => trim($row[4] ?? '0'),
                        'unit_price' => trim($row[5] ?? '0.00'),
                        'category_id' => null,
                        'supplier_id' => null,
                    ];

                    try {
                        $productService->create($input, $userId);
                        $successCount++;
                    } catch (Exception $e) {
                        // Skip invalid rows or log error
                    }
                }
                fclose($handle);

                setFlash('success', "Successfully imported {$successCount} products.");
                redirect('/products/index.php');
            } else {
                $errors[] = 'Could not read the uploaded CSV file.';
            }
        }
    } else {
        $errors[] = 'Please select a CSV file to upload.';
    }
}

$pageTitle = 'Import Products';
$activePage = 'products';

require_once __DIR__ . '/../../app/views/layout/header.php';
?>

<section class="page-header">
    <div>
        <h1>Import Products from CSV</h1>
        <p class="text-muted">Upload a CSV file to bulk add products to your inventory.</p>
    </div>
</section>

<section class="auth-card">
    <?php require __DIR__ . '/../../app/views/partials/alert.php'; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="form-group">
            <label for="csv_file">Select CSV File</label>
            <input id="csv_file" name="csv_file" type="file" accept=".csv" required>
        </div>

        <div class="form-actions">
            <button class="button button-primary" type="submit">Import Products</button>
            <a class="button button-secondary" href="<?= e(BASE_URL) ?>/products/index.php">Cancel</a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/../../app/views/layout/footer.php'; ?>
