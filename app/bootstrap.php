<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/helpers/authorization.php';
require_once __DIR__ . '/helpers/csrf.php';
require_once __DIR__ . '/helpers/flash.php';
require_once __DIR__ . '/helpers/validations.php';

require_once __DIR__ . '/repositories/CategoryRepository.php';
require_once __DIR__ . '/repositories/ProductRepository.php';
require_once __DIR__ . '/repositories/SupplierRepository.php';
require_once __DIR__ . '/repositories/StockMovementRepository.php';
require_once __DIR__ . '/repositories/DashboardRepository.php';
require_once __DIR__ . '/repositories/UserRepository.php';

require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/services/ProductService.php';
require_once __DIR__ . '/services/SupplierService.php';
require_once __DIR__ . '/services/StockService.php';
require_once __DIR__ . '/services/DashboardService.php';

/**
 * Force HTTPS connection for security
 * Redirects HTTP connections to HTTPS (skipped on localhost / local development)
 */
function forceHttps(): void
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    
    // Skip HTTPS enforcement for local development
    if (in_array($host, ['localhost', '127.0.0.1', 'localhost:8888', '127.0.0.1:8888'], true)) {
        return;
    }

    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $isHttps = ($_SERVER['HTTPS'] ?? 'off') === 'on';
    
    if (!$isHttps) {
        $httpsUrl = "https://" . ($host ?: $_SERVER['SERVER_NAME']) . $requestUri;
        
        if (!str_starts_with($requestUri, '/assets/')) {
            header("Location: $httpsUrl", true, 301);
            exit("Redirecting to HTTPS connection...");
        }
    }
}

/**
 * Set HSTS (HTTP Strict Transport Security) headers
 * Enforces HTTPS for specified duration
 */
function setHstsHeaders(): void
{
    $isHttps = ($_SERVER['HTTPS'] ?? 'off') === 'on';
    
    if ($isHttps) {
        // HSTS policy: max-age=31536000 (1 year), includeSubDomains, preload
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload", true);
    }
}

/**
 * Enhanced session security with HTTPS enforcement
 */
function setupSecureSession(): void
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $isHttps = ($_SERVER['HTTPS'] ?? 'off') === 'on';
    
    // Skip setting explicit domain for localhost / 127.0.0.1 so browsers accept session cookies
    $cookieDomain = '';
    if (!str_contains($host, 'localhost') && !str_contains($host, '127.0.0.1') && !empty($host)) {
        $cleanHost = explode(':', $host)[0];
        $cookieDomain = '.' . $cleanHost;
    }
    
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $cookieDomain,
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}



forceHttps();

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Content Security Policy
header(
    "Content-Security-Policy: "
    . "default-src 'self'; "
    . "script-src 'self'; "
    . "style-src 'self'; "
    . "img-src 'self' data:; "
    . "form-action 'self'; "
    . "base-uri 'self'; "
    . "frame-ancestors 'self'"
);

// HTTP Strict Transport Security
setHstsHeaders();

// Session security
setupSecureSession();

header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
