<?php
$pageTitle = 'Profile - Roolipelisovellus';
require __DIR__ . '/partials/head.php';
?>

<main class="dashboard">
    <h1>Profile</h1>

    <section>
        <h2>Account details</h2>
        <p><strong>Username:</strong> <?= e($user['username']) ?></p>
        <p><strong>Email:</strong> <?= e($user['email']) ?></p>
        <p><strong>Member since:</strong> <?= e($user['created_at']) ?></p>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>