<?php

if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

/*
 * ============================================================
 * PAGE TITLE
 * ============================================================
 *
 * Sivukohtainen otsikko voidaan antaa ennen tämän tiedoston
 * lataamista:
 *
 * $pageTitle = "Main page - Roolipelisovellus";
 *
 * Jos otsikkoa ei anneta, käytetään oletusta.
 */

$pageTitle = $pageTitle ?? 'Masters';

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Masters campaign dashboard"
    >

    <title>
        <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>
    </title>

    <link rel="stylesheet" href="../public/css/style.css">

</head>


<body>

<header class="topbar">

    <a
        class="brand"
        href="index.php?action=landing"
        aria-label="Masters home"
    >
        MASTERS
    </a>


    <nav
        class="main-nav"
        aria-label="Main navigation"
    >

        <a
            class="nav-link"
            href="index.php?action=landing#campaigns"
        >
            Campaigns
        </a>

        <a
            class="nav-link"
            href="index.php?action=landing#features"
        >
            Tools
        </a>

        <a
            class="nav-link"
            href="index.php?action=landing#features"
        >
            Resources
        </a>

    </nav>


    <div class="account-actions">

        <!--
            TODO:
            Kun kirjautumisjärjestelmä tehdään, tähän lisätään
            tarkistus käyttäjän kirjautumistilasta.

            Kirjautunut:
                Logout
                Account / avatar

            Kirjautumaton:
                Register
                Sign in
        -->

        <a href="../views/register.php" class="text-link">Register</a>

        <a href="index.php?action=login" class="avatar" aria-label="Sign in">Sign in</a>

    </div>

</header>