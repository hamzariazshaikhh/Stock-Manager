<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

redirectIfLoggedIn();

$errors = [];
$email = '';

if (isPostRequest()) {
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    $emailError = validateEmail($email);
    if ($emailError !== null) {
        $errors[] = $emailError;
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if ($errors === []) {
        try {
            $pdo = getDatabaseConnection();

            $statement = $pdo->prepare(
                'SELECT id, password_hash
                 FROM users
                 WHERE email = :email
                 LIMIT 1'
            );

            $statement->execute([
                'email' => $email,
            ]);

            $user = $statement->fetch();

            if (
                $user === false ||
                !password_verify($password, $user['password_hash'])
            ) {
                $errors[] = 'Email or password is incorrect.';
            } else {
                loginUser((int) $user['id']);

                setFlash('success', 'Welcome back.');

                redirect('/dashboard.php');
            }
        } catch (PDOException $exception) {
            error_log($exception->getMessage());

            $errors[] = 'Unable to log you in right now.';
        }
    }
}

$pageTitle = 'Login';

require_once __DIR__ . '/../app/views/layout/header.php';
?>

<section class="auth-card">
    <h1>Login</h1>
    <p class="text-muted">
        Sign in to manage your inventory.
    </p>

    <?php require __DIR__ . '/../app/views/partials/alert.php'; ?>

    <form method="post" novalidate>
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrfToken()) ?>"
        >

        <div class="form-group">
            <label for="email">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                maxlength="191"
                value="<?= e($email) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                required
            >
        </div>

        <button class="button button-primary" type="submit">
            Login
        </button>
    </form>

    <p class="auth-footer">
        Do not have an account?
        <a href="<?= e(BASE_URL) ?>/register.php">
            Create one
        </a>
    </p>
</section>

<?php require_once __DIR__ . '/../app/views/layout/footer.php'; ?>