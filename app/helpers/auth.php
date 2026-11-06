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

    $pdo = getDatabaseConnection();
    refreshCurrentUserRole($pdo);

    return $userId;
}

function redirectIfLoggedIn(): void
{
    if (isLoggedIn()) {
        redirect('/dashboard.php');
    }
}

function loginUser(int $userId, string $role): void
{
    if (!in_array($role, [
        ROLE_OWNER,
        ROLE_ADMIN,
        ROLE_MANAGER,
        ROLE_VIEWER,
    ], true)) {
        throw new RuntimeException('Invalid user role.');
    }

    session_regenerate_id(true);

    $_SESSION['user_id'] = $userId;
    $_SESSION['user_role'] = $role;
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

function refreshCurrentUserRole(PDO $pdo): void
{
    $userId = currentUserId();

    if ($userId === null) {
        return;
    }

    $statement = $pdo->prepare(
        'SELECT role
         FROM users
         WHERE id = :user_id
         LIMIT 1'
    );

    $statement->execute([
        'user_id' => $userId,
    ]);

    $role = $statement->fetchColumn();

    if (
        !is_string($role)
        || !in_array($role, [
            ROLE_OWNER,
            ROLE_ADMIN,
            ROLE_MANAGER,
            ROLE_VIEWER,
        ], true)
    ) {
        logoutUser();

        redirect('/login.php');
    }

    $_SESSION['user_role'] = $role;
}