<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

if (isLoggedIn()) {
    redirect('/dashboard.php');
}

redirect('/login.php');