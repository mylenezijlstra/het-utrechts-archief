# Ontmoet de Oudegracht (1929) – Het Utrechts Archief (HUA)

Interactieve webapplicatie en Content Management Systeem (CMS) voor **Het Utrechts Archief (HUA)** ter gelegenheid van het 900-jarig bestaan van de Utrechtse Oudegracht (1127–2027) en de tijdelijke tentoonstelling *'Ontmoet de Oudegracht'* (opening maart 2027, jurypresentatie 7 december 2026).

---

## 🏛️ Inhoud & Functies

### 1. Bezoekersapplicatie (`index.php`)
- **Historische bootopname 1929:** De filmopname vanaf een varende boot over de Oudegracht (traject Gaardbrug t/m Stadhuisbrug) vormt het kloppend hart van de ervaring.
- **Interactieve kaart (Leaflet.js):** Loopt live mee met de video. Een bewegend bootje toont realtime de positie op de gracht.
- **Klikken om te varen:** Tik op een marker op de kaart of op de mini-map onderaan om direct naar dat punt te spoelen (ffwd/rewind).
- **Touch- & Digibord-optimalisatie:** Grote aanraakvlakken (minimaal 48×48 px), horizontale swipe-gesture over de video om door de tijd te scrubben.
- **Archiefbronnen & Beeldbank:** Bij elk routepunt verschijnt historische toelichting en een fotogalerij met beelden uit Het Utrechts Archief inclusief modal-vergroting en directe link naar de HUA-beeldbank.
- **Start-uitleg & Attract Mode:** Duidelijke introductiepop-up bij het openen en een aantrekkelijke kiosk-modus wanneer er 75 seconden geen interactie plaatsvindt.
- **HUA Huisstijl & Colofon:** Authentieke typografie, HUA-logo, schuine streepjes (`/ / / /`) en officiële huisstijlkleuren (Paars `#010044`, Groenbeige `#B2910A`, Rood `#EE2D27`, Groen `#008899`). Colofon-pop-up met vermelding van HUA, Ellen Blom en de makers.
- **Omgevingsgeluid:** Zachte klotsende watergeluiden en interactieklikken via de Web Audio API (geen muziek) met mute-knop.
- **Meertaligheid (NL / EN):** Eenvoudig wisselen tussen Nederlands en Engels.
- **Toegankelijkheid:** Voldoet aan [rijksoverheid.nl/toegankelijkheid](https://www.rijksoverheid.nl/toegankelijkheid) (WCAG 2.1 AA) met hoog-contrastmodus, schermlezerlabels en volledige toetsenbordnavigatie.
- **Familie Quiz:** Interactieve mini-quiz voor families en kinderen met directe speelse feedback.

### 2. Content Management Systeem (CMS) (`/admin/`)
- Eenvoudig en zonder technische voorkennis te bedienen door medewerkers van Het Utrechts Archief.
- **Dashboard:** Overzicht van alle routepunten langs de gracht.
- **Routepunt beheer:** Bewerken van code, volgorde, videotijd (start/eind in seconden), coördinaten en meertalige teksten (NL/EN).
- **Archiefbeelden beheer:** Foto's koppelen, alt-teksten, onderschriften en beeldbanklinks toevoegen of verwijderen.
- **Teksten & Colofon:** Aanpassen van de start-pop-up uitleg en colofontekst zonder code aan te raken.
- **Beveiliging:** Inlogsysteem met `password_hash` (bcrypt), sessiebeveiliging en CSRF-tokens.

---

## 🚀 Installatie & Starten

De applicatie draait op standaard **PHP (8.0+)** en vereist **geen Node.js**. De SQLite-database wordt bij de eerste start automatisch aangemaakt en direct gevuld met alle 8 historische locaties, teksten en archieffoto's.

### Direct starten met de ingebouwde PHP-webserver:
Open een terminal in de projectmap en voer uit:

```bash
php -S localhost:8000
```

Open vervolgens uw browser op:
- **Bezoekersapplicatie:** [http://localhost:8000/](http://localhost:8000/)
- **CMS Beheer:** [http://localhost:8000/admin/](http://localhost:8000/admin/)

### Standaard Inloggegevens CMS:
- **Gebruikersnaam:** `admin`
- **Wachtwoord:** `admin123`

---

## 📁 Projectstructuur

```
het-utrechts-archief/
├── index.php                 # Publieke bezoekersapplicatie (digibord)
├── assets/
│   ├── css/
│   │   ├── style.css         # HUA-huisstijl & touch-layout
│   │   └── admin.css         # CMS dashboard styling
│   ├── js/
│   │   ├── app.js            # Video- & state-controller
│   │   ├── map.js            # Leaflet interactieve kaart & vaarroute
│   │   └── audio.js          # Procedurale Web Audio effecten
│   ├── images/               # Hoge-resolutie archieffoto's HUA
│   └── videos/
│       └── videowebsite.mp4  # Historische filmopname uit 1929 (01:45)
├── admin/                    # CMS voor medewerkers
│   ├── index.php             # Routepunten overzicht
│   ├── login.php             # Beveiligde inlogpagina
│   ├── logout.php            # Sessie beëindigen
│   ├── point-edit.php        # Routepunt & foto's bewerken/toevoegen
│   ├── point-delete.php      # Routepunt verwijderen
│   └── settings.php          # Colofon & intro-teksten bewerken
├── api/
│   ├── points.php            # JSON API voor routepunten & media
│   └── settings.php          # JSON API voor algemene instellingen
├── includes/
│   ├── db.php                # PDO databasekoppeling & auto-seeding
│   ├── helpers.php           # HUA-logo SVG generator & CSRF functies
│   └── auth.php              # Sessie- en autorisatiebeveiliging
├── data/
│   ├── hua_oudegracht.sqlite # SQLite database (automatisch gegenereerd)
│   └── schema.sql            # Volledig MySQL / MariaDB exportscript
└── docs/                     # Projectdocumentatie
    ├── 01-debriefing.md      # Debriefing klantgesprek
    ├── 02-plan-van-aanpak.md # Plan van Aanpak (PvA, 5 weken)
    ├── 03-technisch-ontwerp.md # Technisch Ontwerp (TO, architectuur & ERD)
    ├── 04-offerte.md         # Offerte (20 uur begroting)
    ├── 05-logboek.md         # Logboek per week (4 uur/week)
    └── 06-testrapport.md     # Testrapport (WCAG 2.1 AA & digibord)
```

---

## 👥 Colofon & Credits

- **Opdrachtgever:** Het Utrechts Archief (HUA)
- **Begeleider / Educator:** Ellen Blom (`e.blom@hetutrechtsarchief.nl`)
- **Ontwerp & Realisatie:** Mylène Zijlstra & Iris Govaard (Grafisch Lyceum Utrecht)
- **Aanleiding:** 900-jarig bestaan van de Utrechtse Oudegracht (1127–2027)
- **Jurypresentatie:** 7 december 2026, Auditorium Het Utrechts Archief, Hamburgerstraat 28, Utrecht.
