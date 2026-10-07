# Masters – roolipelikampanjoiden hallintasovellus

Masters on neljän opiskelijan toteuttama PHP- ja MySQL-pohjainen kouluprojekti. Pelaajat voivat luoda hahmoja ja liittyä kampanjoihin. Kampanjan luonut Game Master voi hallita kampanjaa, pelaajia, hahmojen tasoja, tiedotteita ja pelikertojen muistiinpanoja.

Projekti on toteutettu ilman PHP-kehystä MVC-rakennetta noudattaen.

## Keskeiset ominaisuudet

### Käyttäjät

- rekisteröityminen, kirjautuminen ja uloskirjautuminen
- tunnin käyttämättömyyden jälkeen vanheneva istunto
- käyttäjäprofiili ja yhteenveto omista hahmoista ja kampanjoista
- käyttäjien ja kampanjoiden haku
- kampanjailmoitukset ja niiden kuittaaminen luetuiksi

### Hahmot

- hahmon luonti: nimi, kuva, rotu, luokka, ammatti, HP ja ability scoret
- HP:n ja ability scorejen päivittäminen
- varusteiden ja lisätaitojen ylläpito
- profiilikuvan lisääminen, vaihtaminen ja poistaminen
- kampanjaan liittyminen kutsukoodilla tai julkiselta kampanjasivulta
- kampanjasta poistuminen

Maksimi-HP ja seitsemän ability scorea jakavat hahmon luonnissa 47 pisteen budjetin. Ability scoren sallittu arvo on 1–25 ja maksimi-HP:n 15–25. Uusi hahmo aloittaa tasolta 1.

### Kampanjat

- kampanjan luonti, muokkaaminen ja poistaminen
- julkisten kampanjoiden selaaminen
- pelaajahahmojen lisääminen ja poistaminen
- kampanjaan kuuluvien hahmojen nykyisen HP:n muuttaminen GM:nä
- hahmojen nostaminen tasolle 20 asti
- d20-nopanheitto kampanjan hahmolistassa
- kampanjatiedotteiden luonti, muokkaaminen ja poistaminen
- pelikertojen päivämäärät, otsikot, muistiinpanot ja osallistujat
- jäsenille lähetettävät ilmoitukset

Kampanjan luoja on kampanjan ainoa Game Master. Muut jäsenet ovat pelaajia; erillisiä co-GM-oikeuksia ei ole.

## Teknologiat

- PHP 8.0 tai uudempi
- MySQL/MariaDB ja PDO
- HTML5, CSS ja tavallinen JavaScript
- PHP-istunnot käyttäjän tunnistamiseen
- SQLite-pohjainen erillinen hakutesti

Tarvittavia PHP-laajennuksia ovat vähintään `pdo_mysql` ja `mbstring`. Testiä varten tarvitaan `pdo_sqlite`.

## Projektin rakenne

```text
.
├── README.md
└── MVC
    ├── controllers        # pyyntöjen käsittely, validointi ja käyttöoikeudet
    ├── database
    │   ├── connection.php # ympäristömuuttujat ja PDO-yhteys
    │   ├── migrations     # tietokannan jatkomigraatiot
    │   └── models         # SQL-kyselyt ja tietokantaoperaatiot
    ├── public
    │   ├── css            # yhteiset tyylit
    │   ├── js             # lomakkeet ja käyttöliittymätoiminnot
    │   └── index.php      # front controller ja reititys
    ├── tests              # automatisoidut testit
    └── views              # sivupohjat ja yhteiset partialit
```

Kaikki pyynnöt kulkevat `MVC/public/index.php`-tiedoston kautta. `action`-parametri valitsee toiminnon, esimerkiksi `index.php?action=dashboard`.

## Asennus

### 1. Vaatimukset

Asenna PHP 8+, MySQL tai MariaDB sekä yllä mainitut PHP-laajennukset. Sovellus toimii esimerkiksi Apache/PHP-ympäristössä tai PHP:n sisäisellä kehityspalvelimella.

### 2. Tietokanta

Luo sovellukselle tietokanta ja tietokantakäyttäjä. Projekti tarvitsee perustaulut ainakin käyttäjille, hahmoille, kampanjoille, roduille, luokille ja ammateille.

> **Huomio:** repossa ei tällä hetkellä ole alkuperäistä `001`-perusskeemaa tai siementietoja. Tyhjän tietokannan asennus vaatii projektissa käytetyn alkuperäisen tietokantadumpin. `MVC/database/migrations` sisältää vain myöhemmät hahmo-, tiedote- ja ilmoitusmuutokset. Ennen projektin jakelua kannattaa lisätä versionhallittu perusskeema sekä rotujen, luokkien ja ammattien alkutiedot.

Kun perusskeema on tuotu, suorita hakemiston `MVC/database/migrations` SQL-tiedostot numerojärjestyksessä. Migraatiot on tarkoitettu ajettaviksi kerran.

### 3. Ympäristöasetukset

Luo tiedosto `MVC/.env`. Se on rajattu pois versionhallinnasta `.gitignore`-asetuksella.

```dotenv
DB_HOST=localhost
DB_PORT=3306
DB_NAME=masters
DB_USERNAME=masters_user
DB_PASSWORD=vaihda_turvallinen_salasana
```

Älä tallenna oikeita tietokantatunnuksia Git-repositorioon.

### 4. Käynnistäminen

Käynnistä kehityspalvelin projektin juuresta:

```bash
php -S localhost:8000 -t MVC/public
```

Avaa `http://localhost:8000/index.php`.

Apachea käytettäessä document root tulee osoittaa hakemistoon `MVC/public`, jotta controller-, malli- ja `.env`-tiedostoja ei voi ladata suoraan verkosta.

## Tyypillinen demopolku

1. Rekisteröi ensimmäinen käyttäjä ja luo hänellä kampanja.
2. Rekisteröi toisessa selainprofiilissa pelaaja ja luo hänelle hahmo.
3. Liitä hahmo kampanjaan julkiselta kampanjasivulta tai kutsukoodilla.
4. Avaa kampanja GM-käyttäjänä ja hallitse rosteria, tasoja ja tiedotteita.
5. Tallenna pelikerta osallistujineen ja tarkista pelaajan ilmoitukset.
6. Päivitä pelaajana hahmon HP, ability scoret, varusteet ja lisätaidot.

Kahden erillisen selainprofiilin käyttäminen tekee käyttöoikeuksien demonstroinnista selkeää.

## Tietoturva ja validointi

Sovelluksessa on toteutettu:

- salasanojen `password_hash`- ja `password_verify`-käsittely
- istuntotunnisteen uusiminen kirjautumisen yhteydessä
- `HttpOnly`-, `SameSite=Lax`- ja HTTPS-ympäristössä `Secure`-session cookie
- CSRF-token kaikissa tilaa muuttavissa POST-pyynnöissä
- parametrisoidut PDO-kyselyt ja HTML-tulosteen enkoodaus
- controller- ja mallitasojen omistajuus- ja GM-tarkistukset
- palvelinpuolinen tyyppi-, pituus- ja arvorajojen validointi
- kuvatiedoston koon ja MIME-tyypin tarkistus

Tuotannossa tulee lisäksi käyttää HTTPS-yhteyttä, rajata kirjautumisyrityksiä, suojata tietokantatunnukset ja järjestää varmuuskopiointi.

## Testaus

Suorita nykyinen testi projektin juuresta:

```bash
php MVC/tests/search_test.php
```

Tarkista PHP-tiedoston syntaksi esimerkiksi näin:

```bash
php -l MVC/public/index.php
```

Nykyinen testi kattaa haun osumat, jokerimerkkien käsittelyn, syötteen validoinnin, HTML-enkoodauksen, käyttölinkit ja tulosrajan. Jatkossa kannattaa lisätä integraatiotestit ainakin kirjautumiselle, CSRF-suojaukselle, käyttöoikeuksille, hahmon luonnille sekä kampanjaan liittymiselle ja poistumiselle.

## Jatkokehitysideoita

- sessiomuistiinpanojen muokkaus ja poistaminen
- kampanjakohtainen nopanhistoria ja aloitejärjestys
- saavutettavuus- ja responsiivisuustestaus eri selaimilla

## Tekijät

Projektin ovat toteuttaneet neljän hengen opiskelijaryhmänä:

- Rico
- Jyri
- Taika
- Pasi

