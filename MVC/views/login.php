<?php
$pageTitle = 'Sign in - Roolipelisovellus';
require __DIR__ . '/partials/head.php';
?>

<main class="auth-page">

    <section class="auth-card">

        <p class="eyebrow">THE GAMEMASTER'S SANCTUM</p>

        <h1>Sign in</h1>

        <p class="auth-intro">
            Sign in to continue building your campaign.
        </p>

        <?php if (isset($error)): ?>
            <p class="form-error"><?= e($error) ?></p>
        <?php endif; ?>

        <form action="index.php?action=login" method="post" class="auth-form">

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

            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
            >

            <button
                type="submit"
                class="btn btn-primary auth-submit"
            >
                Sign in
            </button>

        </form>

        <p class="auth-footer">
            Don't have an account?
            <a href="index.php?action=register">Register</a>
        </p>

    </section>

</main>

<?php require __DIR__ . '/partials/footer.php'; ?>