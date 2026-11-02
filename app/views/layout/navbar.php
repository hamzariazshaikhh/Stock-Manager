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

    <a
        class="<?= $activePage === 'products'
            ? 'navigation-link active'
            : 'navigation-link' ?>"
        href="<?= e(BASE_URL) ?>/products/index.php"
    >
        Products
    </a>

    <a
        class="<?= $activePage === 'categories'
            ? 'navigation-link active'
            : 'navigation-link' ?>"
        href="<?= e(BASE_URL) ?>/categories/index.php"
    >
        Categories
    </a>

    <a
        class="<?= $activePage === 'suppliers'
            ? 'navigation-link active'
            : 'navigation-link' ?>"
        href="<?= e(BASE_URL) ?>/suppliers/index.php"
    >
        Suppliers
    </a>

    <a
        class="<?= $activePage === 'stock'
            ? 'navigation-link active'
            : 'navigation-link' ?>"
        href="<?= e(BASE_URL) ?>/stock/index.php"
    >
        Stock Movements
    </a>
</nav>