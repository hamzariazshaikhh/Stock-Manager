<?php

declare(strict_types=1);

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] > 0;
}

function currentUserId(): ?int
{
    if (!isLoggedIn()) {
        return null;
    }

    return (int) $_SESSION['user_id'];
}

function requireAuth(): int
{
    $userId = currentUserId();

    if ($userId === null) {
        setFlash('error', 'Please log in to continue.');
        redirect('/login.php');
    }

    return $userId;
}

function redirectIfLoggedIn(): void
{
    if (isLoggedIn()) {
        redirect('/dashboard.php');
    }
}

function loginUser(int $userId): void
{
    session_regenerate_id(true);

    $_SESSION['user_id'] = $userId;
}

function logoutUser(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'] ?? '',
            (bool) $params['secure'],
            (bool) $params['httponly']
        );
    }

    session_destroy();
}