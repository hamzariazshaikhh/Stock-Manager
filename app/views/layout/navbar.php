<?php

declare(strict_types=1);
?>

<nav class="main-navigation" aria-label="Main navigation">

<!-- Theme Toggle Button -->
    <button id="themeToggle" class="navigation-link" style="border:none; cursor:pointer;">
        🌓 
    </button>

    <a
        class="<?= $activePage === 'dashboard'
            ? 'navigation-link active'
            : 'navigation-link' ?>"
        href="<?= e(BASE_URL) ?>/dashboard.php"
    >
        Dashboard
    </a>

    <?php if (hasPermission(PERMISSION_VIEW_PRODUCTS)): ?>
        <a
            class="<?= $activePage === 'products'
                ? 'navigation-link active'
                : 'navigation-link' ?>"
            href="<?= e(BASE_URL) ?>/products/index.php"
        >
            Products
        </a>
    <?php endif; ?>

    <?php if (hasPermission(PERMISSION_VIEW_CATEGORIES)): ?>
        <a
            class="<?= $activePage === 'categories'
                ? 'navigation-link active'
                : 'navigation-link' ?>"
            href="<?= e(BASE_URL) ?>/categories/index.php"
        >
            Categories
        </a>
    <?php endif; ?>

    <?php if (hasPermission(PERMISSION_VIEW_SUPPLIERS)): ?>
        <a
            class="<?= $activePage === 'suppliers'
                ? 'navigation-link active'
                : 'navigation-link' ?>"
            href="<?= e(BASE_URL) ?>/suppliers/index.php"
        >
            Suppliers
        </a>
    <?php endif; ?>

    <?php if (hasPermission(PERMISSION_VIEW_STOCK)): ?>
        <a
            class="<?= $activePage === 'stock'
                ? 'navigation-link active'
                : 'navigation-link' ?>"
            href="<?= e(BASE_URL) ?>/stock/index.php"
        >
            Stock Movements
        </a>
    <?php endif; ?>

    <?php if (hasPermission(PERMISSION_MANAGE_USERS)): ?>
        <a
            class="<?= $activePage === 'users'
                ? 'navigation-link active'
                : 'navigation-link' ?>"
            href="<?= e(BASE_URL) ?>/users/index.php"
        >
            Users
        </a>
    <?php endif; ?>

    <?php if (hasPermission(PERMISSION_VIEW_REPORTS)): ?>
        <a
            class="<?= $activePage === 'reports'
                ? 'navigation-link active'
                : 'navigation-link' ?>"
            href="<?= e(BASE_URL) ?>/reports/index.php"
        >
            Reports
        </a>
    <?php endif; ?>

    <?php if (hasPermission(PERMISSION_MANAGE_USERS)): ?>
        <a
            class="<?= $activePage === 'audit-logs'
                ? 'navigation-link active'
                : 'navigation-link' ?>"
            href="<?= e(BASE_URL) ?>/audit-logs/index.php"
        >
            Audit Logs
        </a>
    <?php endif; ?>


<style>
    .navigation-link {
        padding: 10px 15px;
        text-decoration: none;
        color: var(--text-color);
        border-radius: 8px;
    }
    .navigation-link:hover {
        background-color: var(--accent-color);
        color: white;
    }
    .navigation-link.active {
        background-color: var(--accent-color);
        color: white;
    }
</style>

</nav>