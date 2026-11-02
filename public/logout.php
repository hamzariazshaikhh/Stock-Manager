<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

if (!isPostRequest()) {
    redirect('/dashboard.php');
}

verifyCsrfToken($_POST['csrf_token'] ?? null);

logoutUser();

session_start();

setFlash(
    'success',
    'You have been logged out.'
);

redirect('/login.php');