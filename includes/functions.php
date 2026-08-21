<?php

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| HTML ESCAPE
|--------------------------------------------------------------------------
|
| Käytä aina kun tulostat tietokannasta tulevaa tekstiä HTML:ään.
|--------------------------------------------------------------------------
*/

function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


/*
|--------------------------------------------------------------------------
| REDIRECT
|--------------------------------------------------------------------------
*/

function redirect(string $url): never
{
    header("Location: {$url}");
    exit;
}


/*
|--------------------------------------------------------------------------
| CSRF TOKEN
|--------------------------------------------------------------------------
|
| Käytetään POST-lomakkeiden suojaamiseen.
|--------------------------------------------------------------------------
*/

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {

        $_SESSION['csrf_token'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['csrf_token'];
}


/*
|--------------------------------------------------------------------------
| CSRF INPUT
|--------------------------------------------------------------------------
*/

function csrf_field(): string
{
    return sprintf(
        '<input type="hidden" name="csrf_token" value="%s">',
        e(csrf_token())
    );
}


/*
|--------------------------------------------------------------------------
| CSRF VALIDATION
|--------------------------------------------------------------------------
*/

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {

        http_response_code(419);

        exit('Invalid CSRF token.');
    }
}