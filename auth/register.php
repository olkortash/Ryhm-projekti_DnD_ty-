<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if (is_logged_in()) {
    redirect('/index.php');
}

$pageTitle = 'Create Account';
$activeNav = '';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    // Basic validation
    if ($username === '' || $email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error = 'Username must be between 3 and 50 characters.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must contain at least 8 characters.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Passwords do not match.';
    }

    // Check whether username or email already exists
    if ($error === '') {

        $stmt = $pdo->prepare("
            SELECT user_id
            FROM users
            WHERE username = :username
               OR email = :email
            LIMIT 1
        ");

        $stmt->execute([
            'username' => $username,
            'email' => $email
        ]);

        if ($stmt->fetch()) {
            $error = 'Username or email is already in use.';
        }
    }

    // Create account
    if ($error === '') {

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $pdo->prepare("
            INSERT INTO users (
                username,
                email,
                password_hash
            )
            VALUES (
                :username,
                :email,
                :password_hash
            )
        ");

        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash
        ]);

        redirect('/auth/login.php?registered=1');
    }
}

require __DIR__ . '/../partials/header.php';

?>

<main class="auth-page">

    <section class="auth-card">

        <p class="eyebrow">THE GAMEMASTER'S SANCTUM</p>

        <h1>Create your account</h1>

        <p class="auth-intro">
            Create your GM account and start building your campaign.
        </p>


        <?php if ($error): ?>

            <div class="form-error">
                <?= e($error) ?>
            </div>

        <?php endif; ?>


        <form method="post" class="auth-form">

            <?= csrf_field() ?>


            <label for="username">
                Username
            </label>

            <input
                type="text"
                id="username"
                name="username"
                maxlength="50"
                value="<?= e($_POST['username'] ?? '') ?>"
                autocomplete="username"
                required
            >


            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                maxlength="75"
                value="<?= e($_POST['email'] ?? '') ?>"
                autocomplete="email"
                required
            >


            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                autocomplete="new-password"
                required
            >


            <label for="password_confirm">
                Confirm password
            </label>

            <input
                type="password"
                id="password_confirm"
                name="password_confirm"
                minlength="8"
                autocomplete="new-password"
                required
            >


            <button
                type="submit"
                class="btn btn-primary auth-submit"
            >
                Create account
            </button>

        </form>


        <p class="auth-footer">
            Already have an account?
            <a href="/auth/login.php">Sign in</a>
        </p>

    </section>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>