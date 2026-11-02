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