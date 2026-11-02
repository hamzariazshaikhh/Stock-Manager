<?php

declare(strict_types=1);

function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isPositiveInteger(mixed $value): bool
{
    if (is_int($value)) {
        return $value >= 1;
    }

    if (!is_string($value) || $value === '') {
        return false;
    }

    return filter_var(
        $value,
        FILTER_VALIDATE_INT,
        [
            'options' => [
                'min_range' => 1,
            ],
        ]
    ) !== false;
}

function isNonNegativeInteger(mixed $value): bool
{
    if (is_int($value)) {
        return $value >= 0;
    }

    if (!is_string($value) || $value === '') {
        return false;
    }

    return filter_var(
        $value,
        FILTER_VALIDATE_INT,
        [
            'options' => [
                'min_range' => 0,
            ],
        ]
    ) !== false;
}

function isNonNegativeNumber(mixed $value): bool
{
    if ($value === '' || $value === null) {
        return false;
    }

    return is_numeric($value) && (float) $value >= 0;
}

function validateRequiredString(
    mixed $value,
    string $field,
    int $maxLength = 255
): ?string {
    if (!is_string($value)) {
        return "{$field} is required.";
    }

    $value = trim($value);

    if ($value === '') {
        return "{$field} is required.";
    }

    if (mb_strlen($value) > $maxLength) {
        return "{$field} must not exceed {$maxLength} characters.";
    }

    return null;
}

function validateEmail(mixed $value, string $field = 'Email'): ?string
{
    if (!is_string($value)) {
        return "{$field} is required.";
    }

    $email = trim($value);

    if ($email === '') {
        return "{$field} is required.";
    }

    if (mb_strlen($email) > 191) {
        return "{$field} must not exceed 191 characters.";
    }

    if (!isValidEmail($email)) {
        return "{$field} must be a valid email address.";
    }

    return null;
}

function validatePassword(
    mixed $value,
    string $field = 'Password'
): ?string {
    if (!is_string($value) || $value === '') {
        return "{$field} is required.";
    }

    if (mb_strlen($value) < 8) {
        return "{$field} must be at least 8 characters.";
    }

    if (mb_strlen($value) > 72) {
        return "{$field} must not exceed 72 characters.";
    }

    return null;
}

function validateMoney(mixed $value, string $field): ?string
{
    if (!isNonNegativeNumber($value)) {
        return "{$field} must be a non-negative number.";
    }

    if (strlen((string) $value) > 12) {
        return "{$field} is too large.";
    }

    return null;
}