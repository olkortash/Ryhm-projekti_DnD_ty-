<?php

/*
|--------------------------------------------------------------------------
| APPLICATION BOOTSTRAP
|--------------------------------------------------------------------------
|
| Jokainen normaali PHP-sivu lataa tämän.
|
| Tämän kautta tulevat:
|
| - tietokanta
| - sessio
| - autentikointi
| - yleiset funktiot
|--------------------------------------------------------------------------
*/

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/auth.php';

require_once __DIR__ . '/functions.php';