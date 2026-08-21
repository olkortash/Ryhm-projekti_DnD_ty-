<?php

/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
|
| Tämä tiedosto toimii keskitettynä autentikointikerroksena.
|
| Jos olemassa olevassa projektissa on jo kirjautumisjärjestelmä,
| muuta nämä funktiot käyttämään sitä.
|--------------------------------------------------------------------------
*/

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| Onko käyttäjä kirjautunut?
|--------------------------------------------------------------------------
*/

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}


/*
|--------------------------------------------------------------------------
| Nykyisen käyttäjän ID
|--------------------------------------------------------------------------
*/

function current_user_id(): ?int
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    return (int) $_SESSION['user_id'];
}


/*
|--------------------------------------------------------------------------
| Vaadi kirjautuminen
|--------------------------------------------------------------------------
|
| Käytä tätä sivuilla, jotka eivät saa näkyä kirjautumattomille.
|--------------------------------------------------------------------------
*/

function require_login(): void
{
    if (!is_logged_in()) {

        header('Location: /auth/login.php');
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Kirjaudu käyttäjä sisään
|--------------------------------------------------------------------------
|
| TODO:
| Yhdistä tämä olemassa olevaan login-järjestelmään.
|--------------------------------------------------------------------------
*/

function login_user(int $userId): void
{
    session_regenerate_id(true);

    $_SESSION['user_id'] = $userId;
}


/*
|--------------------------------------------------------------------------
| Kirjaudu ulos
|--------------------------------------------------------------------------
*/

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}