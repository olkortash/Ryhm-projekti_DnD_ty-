<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';


if (is_logged_in()) {
    redirect('/index.php');
}


$error = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();


    $email = trim(
        (string) ($_POST['email'] ?? '')
    );


    $password = (string) (
        $_POST['password'] ?? ''
    );


    $stmt = $pdo->prepare("
    SELECT
        user_id,
        username,
        email,
        password_hash
    FROM users
    WHERE email = :email
    LIMIT 1
");

$stmt->execute([
    'email' => $email
]);

$user = $stmt->fetch();

if (
    $user &&
    password_verify($password, $user['password_hash'])
) {
    login_user((int) $user['user_id']);

    redirect('/index.php');
}

$error = 'Invalid email or password.';
    
    }



$pageTitle = 'Sign in';

$activeNav = '';


require __DIR__ . '/../partials/header.php';

?>


<main class="form-page">

    <section class="hero-content">

        <p class="eyebrow">THE GAMEMASTER'S SANCTUM</p>

        <h1>Sign in</h1>


        <?php if ($error): ?>

            <div class="alert">

                <?= e($error) ?>

            </div>

        <?php endif; ?>


        <form method="post" action="">

            <?= csrf_field() ?>

            <label>

                Email
            
                <input type="email" name="email" required autocomplete="email">

            </label>


            <label>

                Password

                <input type="password" name="password" required autocomplete="current-password">

            </label>


            <button type="submit" class="btn btn-primary">
                Sign in
            </button>

        </form>

    </section>

</main>


<?php

require __DIR__ . '/../partials/footer.php';

?>