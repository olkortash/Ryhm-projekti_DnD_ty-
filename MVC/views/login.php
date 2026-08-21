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

    <h2>Rekisteröidy uutena käyttäjänä</h2>
    <form action="index.php?action=register" method="POST">
        <label>Käyttäjätunnus: <input type="text" name="username" required></label><br>
        <label>Sähköposti: <input type="email" name="email" required></label><br>
        <label>Salasana: <input type="password" name="password" required></label><br>
        <button type="submit">Rekisteröidy</button>
    </form>


<?php require __DIR__ . '/partials/footer.php'; ?>