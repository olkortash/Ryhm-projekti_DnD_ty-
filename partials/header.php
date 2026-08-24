<?php

$pageTitle = $pageTitle ?? 'Masters';
$activeNav = $activeNav ?? '';

?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masters campaign dashboard">

    <title>
        <?= e($pageTitle) ?> — Masters
    </title>

    <link rel="stylesheet" href="/views/style.css">
</head>


<body>


<header class="topbar">

    <a class="brand" href="/index.php" aria-label="Masters home">MASTERS</a>


    <nav class="main-nav" aria-label="Main navigation">

        <a class="nav-link <?= $activeNav === 'campaigns' ? 'active' : '' ?>" href="/index.php">Campaigns</a>

        <a class="nav-link <?= $activeNav === 'tools' ? 'active' : '' ?>" href="/tools/">Tools</a>

        <a class="nav-link <?= $activeNav === 'resources' ? 'active' : '' ?>" href="/resources/">Resources</a>

    </nav>


    <div class="account-actions">

        <?php if (is_logged_in()): ?>

            <!--
            TODO:
            Hae käyttäjän nimi/avatar tietokannasta.
            -->

            <a href="/auth/logout.php" class="text-link" >Logout</a>

            <a href="/account/" class="avatar" aria-label="Account">GM</a>

        <?php else: ?>

            <a href="/auth/login.php" class="text-link">Sign in</a>


            <a href="/auth/login.php" class="avatar" aria-label="Sign in">GM</a>

        <?php endif; ?>

    </div>

</header>