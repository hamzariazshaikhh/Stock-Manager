<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

redirectIfLoggedIn();

$errors = [];
$name = '';
$email = '';

if (isPostRequest()) {
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    $name = trim((string) ($_POST['name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirmation = (string) (
        $_POST['password_confirmation'] ?? ''
    );

    $nameError = validateRequiredString($name, 'Name', 120);
    if ($nameError !== null) {
        $errors[] = $nameError;
    }

    $emailError = validateEmail($email);
    if ($emailError !== null) {
        $errors[] = $emailError;
    }

    $passwordError = validatePassword($password);
    if ($passwordError !== null) {
        $errors[] = $passwordError;
    }

    if ($password !== $passwordConfirmation) {
        $errors[] = 'Password confirmation does not match.';
    }

    if ($errors === []) {
        try {
            $pdo = getDatabaseConnection();

            $statement = $pdo->prepare(
                'SELECT id FROM users WHERE email = :email LIMIT 1'
            );

            $statement->execute([
                'email' => $email,
            ]);

            if ($statement->fetch() !== false) {
                $errors[] = 'An account with this email already exists.';
            } else {
                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $statement = $pdo->prepare(
                    'INSERT INTO users
                        (name, email, password_hash)
                     VALUES
                        (:name, :email, :password_hash)'
                );

                $statement->execute([
                    'name' => $name,
                    'email' => $email,
                    'password_hash' => $passwordHash,
                ]);

                loginUser((int) $pdo->lastInsertId());

                setFlash(
                    'success',
                    'Your account has been created successfully.'
                );

                redirect('/dashboard.php');
            }
        } catch (PDOException $exception) {
            error_log($exception->getMessage());

            $errors[] = 'Unable to create your account right now.';
        }
    }
}

$pageTitle = 'Create Account';

require_once __DIR__ . '/../app/views/layout/header.php';
?>

<section class="auth-card">
    <h1>Create Account</h1>
    <p class="text-muted">
        Create your inventory manager account.
    </p>

    <?php require __DIR__ . '/../app/views/partials/alert.php'; ?>

    <form method="post" novalidate>
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrfToken()) ?>"
        >

        <div class="form-group">
            <label for="name">Name</label>
            <input
                id="name"
                name="name"
                type="text"
                maxlength="120"
                value="<?= e($name) ?>"
                required
            >
        </div>

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
                minlength="8"
                maxlength="72"
                required
            >
        </div>

        <div class="form-group">
            <label for="password_confirmation">
                Confirm Password
            </label>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                minlength="8"
                maxlength="72"
                required
            >
        </div>

        <button class="button button-primary" type="submit">
            Create Account
        </button>
    </form>

    <p class="auth-footer">
        Already have an account?
        <a href="<?= e(BASE_URL) ?>/login.php">
            Log in
        </a>
    </p>
</section>

<?php require_once __DIR__ . '/../app/views/layout/footer.php'; ?>