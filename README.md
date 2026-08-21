# Roolipeliprojekti

## Projektin rakenne

```text
/
├── index.php
│
├── config/
│   └── database.php
│
├── includes/
│   ├── bootstrap.php
│   ├── auth.php
│   └── functions.php
│
├── views/
│   └── style.css
│
├── partials/
│   ├── header.php
│   └── footer.php
│
├── campaigns/
│   ├── create.php
│   ├── edit.php
│   └── manage.php
│
├── tools/
│   └── index.php
│
├── resources/
│   └── index.php
│
└── auth/
    ├── login.php
    └── logout.php
```

## Tiedostojen tarkoitus

| Tiedosto                 | Tarkoitus                                                       |
| ------------------------ | --------------------------------------------------------------- |
| `index.php`              | Etusivu / dashboard                                             |
| `config/database.php`    | Tietokantayhteyden asetukset                                    |
| `includes/bootstrap.php` | Kaikki yhteiset yhteydet ja alustukset                          |
| `includes/auth.php`      | Kirjautumiseen ja käyttäjien tunnistamiseen liittyvät toiminnot |
| `includes/functions.php` | Yhteiset PHP-funktiot                                           |
| `views/style.css`        | Projektin yhteiset tyylit                                       |
| `partials/header.php`    | Sivujen yhteinen yläosa                                         |
| `partials/footer.php`    | Sivujen yhteinen alaosa                                         |
| `campaigns/create.php`   | Uuden kampanjan luominen                                        |
| `campaigns/edit.php`     | Kampanjan muokkaaminen                                          |
| `campaigns/manage.php`   | Kampanjoiden hallinta                                           |
| `tools/index.php`        | Työkalut                                                        |
| `resources/index.php`    | Resurssit                                                       |
| `auth/login.php`         | Kirjautumissivu                                                 |
| `auth/logout.php`        | Uloskirjautuminen                                               |

## Perusrakenne

* **`index.php`** toimii projektin etusivuna ja dashboardina.
* **`partials/header.php`** sisältää sivujen yhteisen yläosan, kuten navigaation.
* **`partials/footer.php`** sisältää sivujen yhteisen alaosan.
* **`includes/bootstrap.php`** ladataan sivuille, jotka tarvitsevat projektin yhteisiä riippuvuuksia ja alustuksia.
* **`views/style.css`** sisältää koko projektin yhteiset CSS-tyylit.
* **`config/database.php`** sisältää tietokantayhteyden asetukset.
