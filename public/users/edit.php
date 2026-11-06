<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$currentUserId = requireAuth();

requirePermission(PERMISSION_MANAGE_USERS);

$userId = filter_var(
    $_GET['id'] ?? null,
    FILTER_VALIDATE_INT
);

if ($userId === false || $userId < 1) {
    setFlash('error', 'Invalid user.');
    redirect('/users/index.php');
}

if ($userId === $currentUserId) {
    setFlash('error', 'You cannot change your own role.');
    redirect('/users/index.php');
}

$pdo = getDatabaseConnection();
$userRepository = new UserRepository($pdo);
$user = $userRepository->findById($userId);

if ($user === null) {
    setFlash('error', 'User not found.');
    redirect('/users/index.php');
}

$errors = [];
$role = $user['role'];

if (isPostRequest()) {
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    $role = (string) ($_POST['role'] ?? '');

    if (!array_key_exists($role, availableRoles())) {
        $errors[] = 'Please select a valid role.';
    }

    if ($errors === [] && $user['role'] === ROLE_OWNER) {
        $errors[] = 'The owner role cannot be changed here.';
    }

    if ($errors === []) {
        $updated = $userRepository->updateRole($userId, $role);

        if (!$updated) {
            $errors[] = 'Unable to update the user role.';
        } else {
            setFlash('success', 'User role updated successfully.');
            redirect('/users/index.php');
        }
    }
}

$pageTitle = 'Change User Role';
$activePage = 'users';

require_once __DIR__ . '/../../app/views/layout/header.php';
?>

<section class="form-card">
    <h1>Change User Role</h1>

    <p class="text-muted">
        Update access for <?= e($user['name']) ?>.
    </p>

    <?php require __DIR__ . '/../../app/views/partials/alert.php'; ?>

    <form method="post" novalidate>
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrfToken()) ?>"
        >

        <div class="form-group">
            <label for="role">Role</label>

            <select id="role" name="role" required>
                <?php foreach (availableRoles() as $roleValue => $label): ?>
                    <option
                        value="<?= e($roleValue) ?>"
                        <?= $role === $roleValue ? 'selected' : '' ?>
                    >
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <button
                class="button button-primary"
                type="submit"
            >
                Save Role
            </button>

            <a
                class="button button-secondary"
                href="<?= e(BASE_URL) ?>/users/index.php"
            >
                Cancel
            </a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/../../app/views/layout/footer.php'; ?>