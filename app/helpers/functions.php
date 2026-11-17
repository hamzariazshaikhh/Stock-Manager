<?php

declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function redirect(string $path): never
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function isPostRequest(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function old(string $field): string
{
    return e($_POST[$field] ?? '');
}



function isDuplicateKeyException(PDOException $exception): bool
{
    return $exception->getCode() === '23000'
        && str_contains(
            strtolower($exception->getMessage()),
            'duplicate'
        );
}



function currentPage(): int
{
    $page = filter_input(
        INPUT_GET,
        'page',
        FILTER_VALIDATE_INT
    );

    if ($page === false || $page === null || $page < 1) {
        return 1;
    }

    return $page;
}

function paginationOffset(int $page, int $perPage): int
{
    return ($page - 1) * $perPage;
}

function totalPages(int $totalItems, int $perPage): int
{
    return max(1, (int) ceil($totalItems / $perPage));
}

function logUserAction(PDO $pdo, int $userId, string $action, string $description): void
{
    try {
        $repo = new ActivityLogRepository($pdo);
        $repo->log($userId, $action, $description);
    } catch (Exception $e) {
        // Silently catch logging errors to prevent breaking main user requests
        error_log('Audit logging failed: ' . $e->getMessage());
    }
}
