<?php
/*
 * Rekisteröitymislomake lähettää tiedot AuthControllerille.
 * Käyttäjänimi ja sähköposti säilyvät epäonnistuneen lähetyksen jälkeen; salasanoja ei palauteta kenttiin.
 */
declare(strict_types=1);

require __DIR__ . '/partials/head.php';
?>

<main class="auth-page">
    <section class="auth-card">
        <p class="eyebrow">THE GAMEMASTER'S SANCTUM</p>

        <h1>Create your account</h1>

        <p class="auth-intro">Create your GM account and start building your campaign.</p>

        <form action="index.php?action=register" method="post" class="auth-form">
            <?= csrf_field(); ?>
            <?php if (isset($error)): ?>
                <p class="form-error"><?= e($error); ?></p>
            <?php endif; ?>
            <label for="username" class="field-label"><span>Username</span><span class="field-required">Required</span></label>

            <input
                type="text"
                id="username"
                name="username"
                maxlength="50"
                value="<?= e($_POST['username'] ?? '') ?>"
                autocomplete="username"
                required
            >

            <label for="email" class="field-label"><span>Email</span><span class="field-required">Required</span></label>

            <input
                type="email"
                id="email"
                name="email"
                maxlength="75"
                value="<?= e($_POST['email'] ?? '') ?>"
                autocomplete="email"
                pattern="^[A-Za-z0-9.!#$%&'*+/=?^_`{|}~-]+@(?:[A-Za-z0-9-]+\.)+[A-Za-z]{2,63}$"
                required
            >

            <label for="password" class="field-label"><span>Password</span><span class="field-required">Required</span></label>

            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                autocomplete="new-password"
                required
            >

            <label for="password_confirm" class="field-label"><span>Confirm password</span><span class="field-required">Required</span></label>

            <input
                type="password"
                id="password_confirm"
                name="password_confirm"
                minlength="8"
                autocomplete="new-password"
                required
            >

            <button type="submit" class="btn btn-primary auth-submit">
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
