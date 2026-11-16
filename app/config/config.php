<?php

declare(strict_types=1);

// Load environment variables from .env file
// .env file should be in the project root, not in app/config/
// Use a more reliable path calculation that works across all PHP versions
$projectRoot = dirname(__DIR__); // Get the parent directory of app/
$envFile = $projectRoot . '/.env';



// Parse .env file with comprehensive error handling
$env = parse_ini_file($envFile);
if ($env === false) {
    throw new RuntimeException(
        'Failed to parse .env file: ' . $envFile . '\\n' .
        'Please check the file format and permissions.'
    );
}


// Define Base URL for application redirects and links
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? '') == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8888';

define('BASE_URL', $protocol . $host . '/Stock_manager/public');

// Define Application Name
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Stock Manager');
}

// Define Maximum Stock Quantity limit for inventory validation
if (!defined('MAX_STOCK_QUANTITY')) {
    define('MAX_STOCK_QUANTITY', 1000000);
}

// Define Maximum Product Description Length
if (!defined('MAX_PRODUCT_DESCRIPTION_LENGTH')) {
    define('MAX_PRODUCT_DESCRIPTION_LENGTH', 1000);
}





// More robust .env file detection with clear error handling
if (!$envFile) {
    throw new RuntimeException(
        'Environment file (.env) not found. ' .
        'Please create it from .env.example. ' .
        'Expected location: ' . __DIR__ . '/app/config/../.env'
    );
}

if (!is_file($envFile)) {
    throw new RuntimeException(
        '.env file not found at expected location: ' . $envFile . '\\n' .
        'Please create it from .env.example in the project root directory.'
    );
}

// Parse .env file with comprehensive error handling
$env = parse_ini_file($envFile);
if ($env === false) {
    throw new RuntimeException(
        'Failed to parse .env file: ' . $envFile . '\\n' .
        'Please check the file format and permissions.'
    );
}

// Parse .env file with comprehensive error handling
$env = parse_ini_file($envFile);
if ($env === false) {
    throw new RuntimeException(
        'Failed to parse .env file: ' . $envFile . '\\n' .
        'Please check the file format and permissions.'
    );
}

// Populate $_ENV and $_SERVER so database.php can read environment variables
foreach ($env as $key => $value) {
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}


// Apply consistent environment variable loading with sensible defaults
define('DB_HOST', $env['DB_HOST'] ?? '127.0.0.1');
define('DB_PORT', $env['DB_PORT'] ?? '8889');
define('DB_NAME', $env['DB_NAME'] ?? 'inventory_saas_db');
define('DB_USER', $env['DB_USER'] ?? '');
define('DB_PASSWORD', $env['DB_PASSWORD'] ?? '');

// Database-specific configuration with validation
if (empty(DB_USER) || empty(DB_PASSWORD)) {
    throw new RuntimeException(
        'Database credentials (DB_USER and DB_PASSWORD) are required. ' .
        'Please check your .env file configuration. ' .
        'You can use .env.example as a template.'
    );
}

// Log successful configuration loading for debugging
error_log(sprintf(
    'Database configuration loaded successfully. ' .
    'Host: %s, Database: %s, User: %s',
    DB_HOST,
    DB_NAME,
    DB_USER
));
