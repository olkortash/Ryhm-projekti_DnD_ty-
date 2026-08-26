<?php

declare(strict_types=1);


require __DIR__ . '/partials/head.php';

?>

<main class="auth-page">

    <section class="auth-card">

        <p class="eyebrow">THE GAMEMASTER'S SANCTUM</p>

        <h1>Create your account</h1>

        <p class="auth-intro">
            Create your GM account and start building your campaign.
        </p>



        <form action="index.php?action=register" method="post" class="auth-form">



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
            <a href="index.php?action=login">Sign in</a>
        </p>

    </section>

</main>

<?php require __DIR__ . '/partials/footer.php'; ?>