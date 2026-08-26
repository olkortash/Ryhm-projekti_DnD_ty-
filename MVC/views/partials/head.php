<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Roolipelisovellus'; ?></title>
    <!-- CSS-tiedosto tähän -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php?action=dashboard">Etusivu</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php?action=logout">Kirjaudu ulos</a>
        <?php endif; ?>
    </nav>
</header>
<main>