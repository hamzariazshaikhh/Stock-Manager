<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? APP_NAME;
$activePage = $activePage ?? '';
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= e($pageTitle) ?> - <?= e(APP_NAME) ?>
    </title>

    <link
        rel="stylesheet"
        href="<?= e(BASE_URL) ?>/assets/css/reset.css"
    >

    <link
        rel="stylesheet"
        href="<?= e(BASE_URL) ?>/assets/css/variables.css"
    >

    <link
        rel="stylesheet"
        href="<?= e(BASE_URL) ?>/assets/css/layout.css"
    >

    <link
        rel="stylesheet"
        href="<?= e(BASE_URL) ?>/assets/css/app.css"
    >
</head>

<body>
<header class="site-header">
    <div class="container header-content">
        <a
            class="brand"
            href="<?= e(BASE_URL) ?>/dashboard.php"
        >
            <?= e(APP_NAME) ?>
        </a>

        <?php if (isLoggedIn()): ?>
            <div class="header-actions">
                <span class="logged-in-label">
                    <?= e('Logged in') ?>
                </span>

                <form
                    method="post"
                    action="<?= e(BASE_URL) ?>/logout.php"
                >
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(csrfToken()) ?>"
                    >

                    <button
                        class="button button-secondary"
                        type="submit"
                    >
                        Logout
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isLoggedIn()): ?>
        <div class="container navigation-container">
            <?php require __DIR__ . '/navbar.php'; ?>
        </div>
    <?php endif; ?>
</header>

<main class="container page-content">
    <?php if ($flash !== null): ?>
        <div class="alert alert-<?= e($flash['type']) ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>