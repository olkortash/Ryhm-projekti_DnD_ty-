<!DOCTYPE html>
<html lang="fi">
<head>
    <title>Roolipelisovellus</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="" type="text/css">
</head>
<body>
    <header>
        <h1>Roolipelisovellus</h1>
    </header>
<nav>
    <ul class="navbar">
        <li class="navbutton"><a href="/">Etusivu</a></li>
        <li class="navbutton"><a href="/public_campaigns">Julkiset kampanjat</a></li>
        <?php if(!isLoggedIn()): ?>
           <li class="navbutton"><a href="/login">Kirjaudu sisään</a></li> 
           <li class="navbutton"><a href="/register">Rekisteröidy</a></li>
        <?php else: ?>
            <li class="navbutton dropdown">
                <a href="/profile" class="dropbtn">Oma profiili</a>
                <ul class="dropdown-content">
                    <li><a href="/my_campaigns">Omat kampanjat</a></li>
                    <li><a href="/new_campaign">Luo kampanja</a></li>
                    <li><a href="/my_characters">Omat hahmot</a></li>
                    <li><a href="/add_character">Luo hahmo</a></li>
                </ul>
            </li>

           <li class="navbutton"><a href="/logout">Kirjaudu ulos</a></li>
        <?php endif ?>

    </ul>
</nav>