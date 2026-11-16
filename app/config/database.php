<?php
// secure database.php with environment support

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function getDatabaseConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // Validate required environment variables efficiently
    $envVars = [
        'DB_HOST' => '127.0.0.1',      // Default localhost
        'DB_PORT' => '8889',          // Default MAMP port  
        'DB_NAME' => 'inventory_saas_db',  // Default database name
        'DB_USER' => null,            // Required - no safe default
        'DB_PASSWORD' => null,        // Required - no safe default
    ];

    // Check each required environment variable
    foreach ($envVars as $varName => $defaultValue) {
        $envValue = $_ENV[$varName] ?? null;
        
        if ($envValue === null && $defaultValue === null) {
            throw new RuntimeException(
                "Environment variable {$varName} is required. " .
                "Please check your .env file or environment configuration."
            );
        }
        
        // Set the constant if value is available
        if ($envValue !== null && !defined($varName)) {
            define($varName, $envValue);
        }
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        DB_HOST,
        DB_PORT,
        DB_NAME
    );

    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
    ]);

    return $pdo;
}
