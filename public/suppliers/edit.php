<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();

$pdo = getDatabaseConnection();

$supplierRepository = new SupplierRepository($pdo);
$supplierService = new SupplierService($supplierRepository);

$supplierId = filter_input(
    INPUT_GET,
    'id',
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

$supplier = $supplierRepository->findByIdForUser(
    $supplierId,
    $userId
);

if ($supplier === null) {
    setFlash('error', 'Supplier not found.');
    redirect('/suppliers/index.php');
}

$errors = [];

$formData = [
    'name' => $supplier['name'],
    'email' => $supplier['email'] ?? '',
    'phone' => $supplier['phone'] ?? '',
    'address' => $supplier['address'] ?? '',
];

if (isPostRequest()) {
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    $formData = [
        'name' => trim((string) ($_POST['name'] ?? '')),
        'email' => trim((string) ($_POST['email'] ?? '')),
        'phone' => trim((string) ($_POST['phone'] ?? '')),
        'address' => trim((string) ($_POST['address'] ?? '')),
    ];

    $errors = $supplierService->update(
        $supplierId,
        $formData,
        $userId
    );

    if ($errors === []) {
        setFlash(
            'success',
            'Supplier updated successfully.'
        );

        redirect('/suppliers/index.php');
    }
}

$pageTitle = 'Edit Supplier';
$activePage = 'suppliers';

require_once __DIR__ . '/../../app/views/layout/header.php';
?>

<section class="form-card">
    <h1>Edit Supplier</h1>

    <p class="text-muted">
        Update supplier contact information.
    </p>

    <?php require __DIR__ . '/../../app/views/partials/alert.php'; ?>

    <form method="post" novalidate>
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrfToken()) ?>"
        >

        <div class="form-group">
            <label for="name">Supplier name</label>

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
            <label for="email">Email</label>

            <input
                id="email"
                name="email"
                type="email"
                maxlength="191"
                value="<?= e($formData['email']) ?>"
            >
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>

            <input
                id="phone"
                name="phone"
                type="text"
                maxlength="50"
                value="<?= e($formData['phone']) ?>"
            >
        </div>

        <div class="form-group">
            <label for="address">Address</label>

            <textarea
                id="address"
                name="address"
                maxlength="255"
                rows="4"
            ><?= e($formData['address']) ?></textarea>
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
                href="<?= e(BASE_URL) ?>/suppliers/index.php"
            >
                Cancel
            </a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/../../app/views/layout/footer.php'; ?>