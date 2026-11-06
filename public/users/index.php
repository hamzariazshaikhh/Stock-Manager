<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$currentUserId = requireAuth();

requirePermission(PERMISSION_MANAGE_USERS);

$pdo = getDatabaseConnection();
$userRepository = new UserRepository($pdo);
$users = $userRepository->findAll();

$pageTitle = 'Users';
$activePage = 'users';

require_once __DIR__ . '/../../app/views/layout/header.php';
?>

<section class="page-header">
    <div>
        <h1>Users</h1>

        <p class="text-muted">
            Manage access levels for users of this inventory system.
        </p>
    </div>
</section>

<section class="table-card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e($user['name']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= e(roleLabel($user['role'])) ?></td>
                        <td><?= e($user['created_at']) ?></td>
                        <td>
                            <?php if ((int) $user['id'] === $currentUserId): ?>
                                <span class="text-muted">
                                    Current user
                                </span>
                            <?php else: ?>
                                <a
                                    class="button button-small"
                                    href="<?= e(BASE_URL) ?>/users/edit.php?id=<?= e($user['id']) ?>"
                                >
                                    Change role
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../../app/views/layout/footer.php'; ?>