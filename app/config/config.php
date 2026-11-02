<?php

declare(strict_types=1);

const APP_NAME = 'Inventory Manager';
const BASE_URL = '/Stock_manager/public';

$localConfig = __DIR__ . '/config.local.php';

if (is_file($localConfig)) {
    require_once $localConfig;
} else {
    define('DB_HOST', '127.0.0.1');
    define('DB_PORT', '8889');
    define('DB_NAME', 'inventory_saas_db');
    define('DB_USER', 'your_database_user');
    define('DB_PASSWORD', 'your_database_password');
}