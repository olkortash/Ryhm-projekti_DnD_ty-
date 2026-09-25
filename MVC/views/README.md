# Näkymien ylläpito

Näkymät muodostavat HTML:n controllerien välittämistä muuttujista. Reitit valitaan
`../public/index.php`-tiedoston `action`-arvolla. Tietokantahaut ja lomakkeiden
tallennus kuuluvat controllereihin ja malleihin; näkymissä valitaan näytettävät
osiot ja tulostetaan tiedot.

## Tiedostojen vastuut

| Tiedosto | Sisältö ja tietojen lähde |
| --- | --- |
| `mainpage.php` | Etusivu ja julkiset kampanjat; `AuthController`. |
| `login.php`, `register.php` | Kirjautumis- ja rekisteröitymislomakkeet; `AuthController`. |
| `dashboard.php` | Käyttäjän hahmot ja luomat kampanjat; `DashboardController`. |
| `profile.php` | Tilitiedot ja yhteenvetoluvut; `ProfileController`. |
| `character_create.php` | Hahmon luonti, kuva ja pistejako; `CharacterController`. |
| `character_view.php` | Hahmon tiedot ja omistajan muokkauslomakkeet; `CharacterController`. |
| `campaign_view.php` | Kampanjan liittyminen, hallinta, jäsenet, tiedotteet ja sessiot; `CampaignController`. |
| `notifications.php` | Käyttäjän ilmoitukset ja lukukuittaukset; `CampaignController`. |
| `help.php`, `sitemap.php` | Staattiset ohjeet ja sivukartta; reititin sisällyttää suoraan. |
| `404_view.html` | Itsenäinen virhesivu ja `404-sivu.js`-tekstianimaatio. |
| `partials/head.php` | Dokumentin alku, sivun otsikko, `e()`-apufunktio, navigaatio ja flash-palaute. |
| `partials/footer.php` | Alatunniste, yhteinen JavaScript ja dokumentin sulkevat tagit. |
| `campaign_join.php` | Tyhjä tiedosto; nykyinen reititin ei käytä sitä. Liittymislomakkeet ovat kampanja- ja hahmosivuilla. |

## Yhteiset käytännöt

- Käytä neljän välilyönnin sisennystä. Sisennä sekä HTML-elementtien lapset että
  PHP:n `if`- ja `foreach`-lohkojen sisältö. Pidä `else` ja `endif` samalla tasolla
  kuin lohkon aloittava `if`.
- Erota kokonaisuudet yhdellä tyhjällä rivillä. Lyhyet elementit voivat olla yhdellä
  rivillä; jaa pitkän aloitustagin attribuutit omille riveilleen.
- Kerro sivun alussa sen tarvitsemat tiedot. Kommentoi erityisesti ehtojen tarkoitus,
  toimintojen väliset riippuvuudet ja poikkeukset. Päivitä kommentti, jos toiminta muuttuu.
- Aseta mahdollinen `$pageTitle` ennen `partials/head.php`-tiedoston sisällyttämistä.
  Sivun lopussa sisällytetään `partials/footer.php`. 404-sivulla on oma dokumenttirunko.
- Käytä dynaamisen tekstin ja attribuuttiarvojen tulostamiseen HTML-escapointia,
  esimerkiksi `e($value)`. Rivinvaihdot voi näyttää muodossa `nl2br(e($value))`.
  `e()` ei ole JavaScript- tai URL-koodausfunktio.
- Muuttaessasi lomakkeen `action`- tai `name`-arvoja tarkista myös niitä käsittelevä
  controller. Piilotetut tunnistekentät yhdistävät tallennuksen oikeaan tietueeseen.
- `$isOwner`, `$isGm` ja `$canViewPrivate` ohjaavat näkymien näkyvyyttä. Tarkista
  toiminnallisten muutosten yhteydessä myös controllerin käyttöoikeuskäsittely.
  Kampanjasivun `$isGm` tarkoittaa nykyisessä controllerissa kampanjan luojaa.
- Hahmonluonnin `id`-, `name`-, `class`- ja `data-*`-arvoja käytetään myös
  `../public/js/character-creator.js`-tiedostossa. Kuvakkeiden `use`-viittaukset
  puolestaan tarvitsevat vastaavat SVG-symbolien tunnisteet.
- Älä muuta `textarea`-, `pre`- tai `script`-elementtien sisältöä pelkän sisennyksen
  vuoksi: välilyönnit ja rivinvaihdot voivat olla osa arvoa tai ohjelmakoodia.

Muotoilumuutoksessa tarkista PHP-syntaksi (`php -l tiedosto.php`) ja säilytä
ehtojen, kenttien, linkkien sekä JavaScript-kytkentöjen toiminta. Tarkista muuttuneelta
sivulta myös tyhjä lista, virheilmoitus ja eri käyttäjäroolien näkymät soveltuvin osin.
