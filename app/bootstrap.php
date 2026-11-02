<?php

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/helpers/csrf.php';
require_once __DIR__ . '/helpers/flash.php';
require_once __DIR__ . '/helpers/validations.php';



require_once __DIR__ . '/repositories/CategoryRepository.php';
require_once __DIR__ . '/repositories/ProductRepository.php';
require_once __DIR__ . '/repositories/SupplierRepository.php';
require_once __DIR__ . '/repositories/StockMovementRepository.php';
require_once __DIR__ . '/repositories/DashboardRepository.php';

require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/services/ProductService.php';
require_once __DIR__ . '/services/SupplierService.php';
require_once __DIR__ . '/services/StockService.php';
require_once __DIR__ . '/services/DashboardService.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);

    session_start();
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');