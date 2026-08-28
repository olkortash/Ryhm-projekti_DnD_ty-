<?php 
$pageTitle = "Kirjaudu sisään - Roolipelisovellus";
require __DIR__ . '/partials/head.php'; 
?>


    <h2>Kirjaudu sisään</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form action="index.php?action=login" method="POST">
        <label>Käyttäjätunnus: <input type="text" name="username" required></label><br>
        <label>Salasana: <input type="password" name="password" required></label><br>
        <button type="submit">Kirjaudu</button>
    </form>

    <hr>

<?php require __DIR__ . '/partials/footer.php'; ?>