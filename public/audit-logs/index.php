<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

$userId = requireAuth();
requirePermission(PERMISSION_MANAGE_USERS); // Restricted to Admins & Owners

$pdo = getDatabaseConnection();
$logRepository = new ActivityLogRepository($pdo);

$perPage = 25;
$page = currentPage();
$totalLogs = $logRepository->countAll();
$totalPages = totalPages($totalLogs, $perPage);
$page = min($page, $totalPages);

$logs = $logRepository->findPage($perPage, paginationOffset($page, $perPage));

$pageTitle = 'Activity Audit Logs';
$activePage = 'audit-logs';

require_once __DIR__ . '/../../app/views/layout/header.php';

$currentPage = $page;
$pageCount = $totalPages;
require __DIR__ . '/../../app/views/partials/pagination.php';
?>

<section class="page-header">
    <div>
        <h1>Activity Audit Logs</h1>
        <p class="text-muted">Track system activity, user actions, and security events.</p>
    </div>
</section>

<section class="table-card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">No audit logs recorded yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= e($log['created_at']) ?></td>
                            <td><?= e($log['user_email']) ?></td>
                            <td><span class="badge"><?= e(ucfirst($log['user_role'])) ?></span></td>
                            <td><strong><?= e($log['action']) ?></strong></td>
                            <td><?= e($log['description']) ?></td>
                            <td><?= e($log['ip_address'] ?? 'Unknown') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../../app/views/layout/footer.php'; ?>
