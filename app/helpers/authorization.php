<?php

declare(strict_types=1);

const ROLE_OWNER = 'owner';
const ROLE_ADMIN = 'admin';
const ROLE_MANAGER = 'manager';
const ROLE_VIEWER = 'viewer';

const PERMISSION_VIEW_DASHBOARD = 'view_dashboard';
const PERMISSION_VIEW_PRODUCTS = 'view_products';
const PERMISSION_MANAGE_PRODUCTS = 'manage_products';
const PERMISSION_VIEW_CATEGORIES = 'view_categories';
const PERMISSION_MANAGE_CATEGORIES = 'manage_categories';
const PERMISSION_VIEW_SUPPLIERS = 'view_suppliers';
const PERMISSION_MANAGE_SUPPLIERS = 'manage_suppliers';
const PERMISSION_VIEW_STOCK = 'view_stock';
const PERMISSION_MANAGE_STOCK = 'manage_stock';
const PERMISSION_MANAGE_USERS = 'manage_users';

function rolePermissions(): array
{
    return [
        ROLE_OWNER => [
            PERMISSION_VIEW_DASHBOARD,
            PERMISSION_VIEW_PRODUCTS,
            PERMISSION_MANAGE_PRODUCTS,
            PERMISSION_VIEW_CATEGORIES,
            PERMISSION_MANAGE_CATEGORIES,
            PERMISSION_VIEW_SUPPLIERS,
            PERMISSION_MANAGE_SUPPLIERS,
            PERMISSION_VIEW_STOCK,
            PERMISSION_MANAGE_STOCK,
            PERMISSION_MANAGE_USERS,
        ],

        ROLE_ADMIN => [
            PERMISSION_VIEW_DASHBOARD,
            PERMISSION_VIEW_PRODUCTS,
            PERMISSION_MANAGE_PRODUCTS,
            PERMISSION_VIEW_CATEGORIES,
            PERMISSION_MANAGE_CATEGORIES,
            PERMISSION_VIEW_SUPPLIERS,
            PERMISSION_MANAGE_SUPPLIERS,
            PERMISSION_VIEW_STOCK,
            PERMISSION_MANAGE_STOCK,
            PERMISSION_MANAGE_USERS,
        ],

        ROLE_MANAGER => [
            PERMISSION_VIEW_DASHBOARD,
            PERMISSION_VIEW_PRODUCTS,
            PERMISSION_MANAGE_PRODUCTS,
            PERMISSION_VIEW_CATEGORIES,
            PERMISSION_MANAGE_CATEGORIES,
            PERMISSION_VIEW_SUPPLIERS,
            PERMISSION_MANAGE_SUPPLIERS,
            PERMISSION_VIEW_STOCK,
            PERMISSION_MANAGE_STOCK,
        ],

        ROLE_VIEWER => [
            PERMISSION_VIEW_DASHBOARD,
            PERMISSION_VIEW_PRODUCTS,
            PERMISSION_VIEW_CATEGORIES,
            PERMISSION_VIEW_SUPPLIERS,
            PERMISSION_VIEW_STOCK,
        ],
    ];
}

function currentUserRole(): ?string
{
    return $_SESSION['user_role'] ?? null;
}

function hasRole(string ...$roles): bool
{
    $role = currentUserRole();

    return $role !== null && in_array($role, $roles, true);
}

function hasPermission(string $permission): bool
{
    $role = currentUserRole();

    if ($role === null) {
        return false;
    }

    $permissions = rolePermissions()[$role] ?? [];

    return in_array($permission, $permissions, true);
}

function requirePermission(string $permission): void
{
    if (!hasPermission($permission)) {
        http_response_code(403);

        redirect('/403.php');
    }
}

function requireRole(string ...$roles): void
{
    if (!hasRole(...$roles)) {
        http_response_code(403);

       redirect('/403.php');
    }
}

function roleLabel(string $role): string
{
    return match ($role) {
        ROLE_OWNER => 'Owner',
        ROLE_ADMIN => 'Administrator',
        ROLE_MANAGER => 'Manager',
        ROLE_VIEWER => 'Viewer',
        default => 'Unknown',
    };
}

function availableRoles(): array
{
    return [
        ROLE_ADMIN => 'Administrator',
        ROLE_MANAGER => 'Manager',
        ROLE_VIEWER => 'Viewer',
    ];
}