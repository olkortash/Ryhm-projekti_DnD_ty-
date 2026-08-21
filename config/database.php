<?php

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
|
| MUUTA NÄMÄ vastaamaan olemassa olevaa projektia.
|
| Esimerkiksi:
| $host = 'localhost';
| $dbname = 'oma_tietokanta';
| $username = 'oma_kayttaja';
| $password = 'salasana';
|
| Tuotannossa tunnuksia ei kannata pitää suoraan tässä tiedostossa.
| Ne kannattaa lukea .env/environment muuttujista.
|--------------------------------------------------------------------------
*/

declare(strict_types=1);

$host = "ricsaa25.treok.io";
$dbname = "ricsaa25_dnd_projekti";
$username = "ricsaa25_dnd_projekti";
$password = "K&psOqm#o*Mh3+k#";

$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {

    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        $options
    );

} catch (PDOException $e) {

    /*
    |--------------------------------------------------------------------------
    | TUOTANTO
    |--------------------------------------------------------------------------
    |
    | Tuotannossa älä näytä oikeaa tietokantavirhettä käyttäjälle.
    | Kirjaa virhe palvelimen lokiin.
    |
    */

    error_log($e->getMessage());

    die('Tietokantayhteydessä tapahtui virhe.');
}