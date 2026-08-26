<?php

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

```
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
    <?= e($pageTitle) ?>
</title>


<!--
    ========================================================
    CSS
    ========================================================

    TODO:
    Tarkista projektisi todellinen public-kansion sijainti.

    Jos public on web-rootissa:
        /css/style.css

    Jos public on projektin juuressa:
        /public/css/style.css
    ========================================================
-->

<link
    rel="stylesheet"
    href="/public/css/style.css"
>
```

</head>

<body>

<header class="topbar">

```
<!-- ========================================================
     BRAND
     ======================================================== -->

<a
    class="brand"
    href="/index.php"
    aria-label="Masters home"
>
    MASTERS
</a>


<!-- ========================================================
     MAIN NAVIGATION
     ======================================================== -->

<nav
    class="main-nav"
    aria-label="Main navigation"
>

    <a
        class="nav-link"
        href="/index.php"
    >
        Campaigns
    </a>

    <a
        class="nav-link"
        href="/tools/"
    >
        Tools
    </a>

    <a
        class="nav-link"
        href="/resources/"
    >
        Resources
    </a>

</nav>


<!-- ========================================================
     ACCOUNT
     ======================================================== -->

<div class="account-actions">


    <!--
        ====================================================
        TODO: KIRJAUTUMISEN TARKISTUS
        ====================================================

        Kun kirjautumisjärjestelmä tehdään, tähän tulee
        tarkistus esimerkiksi:

        if (isset($_SESSION['user_id']))

        Kirjautuneelle käyttäjälle näytetään:

            Logout
            Account / avatar

        Kirjautumattomalle käyttäjälle näytetään:

            Register
            Sign in
        ====================================================
    -->


    <!--
        TODO:
        Käyttäjän nimi ja avatar haetaan myöhemmin
        tietokannasta.
    -->

    <a
        href="/auth/register.php"
        class="text-link"
    >
        Register
    </a>


    <a
        href="/auth/login.php"
        class="avatar"
        aria-label="Sign in"
    >
        Sign in
    </a>


    <!--
        TODO: Kirjautuneen käyttäjän vaihtoehto:

        <a
            href="/index.php?action=logout"
            class="text-link"
        >
            Logout
        </a>

        <a
            href="/account/"
            class="avatar"
            aria-label="Account"
        >
            GM
        </a>
    -->


</div>
```

</header>
