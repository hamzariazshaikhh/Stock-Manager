<?php

declare(strict_types=1);
?>

<nav class="main-navigation" aria-label="Main navigation">
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
</nav>