# BästaLaddboxen.se – Installationsguide

## 1. Ladda upp filerna
Ladda upp HELA mappen till din webbserver via FTP.
Rekommenderat: Lägg filerna i public_html eller en undermapp.

## 2. Skapa databasen
1. Öppna phpMyAdmin på ditt webbhotell
2. Skapa en ny databas (t.ex. "laddboxen")
3. Importera filen `database.sql`

## 3. Konfigurera anslutningen
Öppna `includes/config.php` och ändra:
```php
define('DB_HOST', 'localhost');        // Vanligtvis localhost
define('DB_NAME', 'laddboxen');        // Namnet på din databas
define('DB_USER', 'ditt_användarnamn'); // Din MySQL-användare
define('DB_PASS', 'ditt_lösenord');     // Ditt MySQL-lösenord
define('SITE_URL', 'https://dindomän.se'); // Din URL
```

## 4. Säkra admin-panelen
Öppna `admin/index.php` och ändra lösenordet:
```php
$admin_password = 'ditt-hemliga-lösenord';
```

Eller skydda admin-mappen med .htpasswd via cPanel.

## 5. Skaffa affiliate-konton
- **Adtraction.com** – Stor nordisk affiliate-plattform (Elgiganten, m.fl.)
- **Amazon Associates** – amazon.se
- **Tradedoubler.com** – Alternativ till Adtraction

Byt ut `https://example.com/affiliate/...` i databasen mot dina riktiga affiliate-länkar.

## 6. Lägg till egna produkter
Gå till `/admin/` på din sajt och logga in.
Därifrån kan du:
- Lägga till nya produkter
- Framhäva produkter på startsidan
- Radera produkter

## Filstruktur
```
/
├── index.php          ← Startsida
├── kategori.php       ← Kategorisida
├── produkt.php        ← Produktsida
├── 404.php            ← Felsida
├── .htaccess          ← Apache-konfiguration
├── database.sql       ← Databasstruktur
├── css/
│   └── style.css      ← All styling
├── js/
│   └── main.js        ← JavaScript
├── includes/
│   ├── config.php     ← Databasconfig (ändra detta!)
│   ├── header.php     ← Sidhuvud
│   └── footer.php     ← Sidfot
└── admin/
    └── index.php      ← Admin-panel
```

## Tips för att tjäna pengar snabbt
1. Lägg till 10–20 riktiga produkter med riktiga affiliate-länkar
2. Skriv SEO-optimerade recensioner i "lång beskrivning"
3. Dela sajten i elbilsgrupper på Facebook
4. Posta på Reddit (r/elbilar, r/Sweden)
5. Registrera sajten i Google Search Console

## SEO-tips
- Produktsidor har automatisk schema.org-markup (hjälper Google)
- Använd nyckelord som "bästa laddbox 2026", "laddbox hemma" etc.
- Skriv minst 300 ord i varje recension

Lycka till! 🚀⚡
