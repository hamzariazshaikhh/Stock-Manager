<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

http_response_code(403);

$pageTitle = 'Access Denied';

require_once __DIR__ . '/../app/views/layout/header.php';
?>

<section class="dashboard-card error-page">
    <h1>Access denied</h1>

    <p class="text-muted">
        You do not have permission to access this page.
    </p>

    <?php if (isLoggedIn()): ?>
        <a
            class="button button-primary"
            href="<?= e(BASE_URL) ?>/dashboard.php"
        >
            Return to Dashboard
        </a>
    <?php else: ?>
        <a
            class="button button-primary"
            href="<?= e(BASE_URL) ?>/login.php"
        >
            Log In
        </a>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../app/views/layout/footer.php'; ?>