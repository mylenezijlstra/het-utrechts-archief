# Testrapport – *Ontmoet de Oudegracht* (Het Utrechts Archief)

| Projectgegevens | Details |
| :--- | :--- |
| **Applicatie** | Interactieve Webapplicatie & CMS *Ontmoet de Oudegracht* |
| **Opdrachtgever** | Het Utrechts Archief (HUA) |
| **Contactpersoon** | Ellen Blom (Educator) |
| **Testomgeving** | PHP 8.x / MySQL 8.0, Chrome, Firefox, Edge, Safari, Touch-digibord (1920×1080) |
| **Testperiode** | Week 3 (eerste functionele ronde) en Week 4/5 (eindacceptatietest vóór jurypresentatie op 7 december 2026) |
| **Tester(s)** | Projectteam Webdevelopment |

---

## 1. Doel en Testomvang

Dit testrapport documenteert de resultaten van alle uitgevoerde testen op de bezoekersapplicatie en het CMS vóór de definitieve oplevering op **7 december 2026**. De testen zijn onderverdeeld in:
1. **Functionele testen (Bezoekersapplicatie)**
2. **Functionele testen (CMS)**
3. **Touch, Digibord & Responsiviteit**
4. **Browsercompatibiliteit**
5. **Toegankelijkheid (WCAG 2.1 AA / rijksoverheid.nl/toegankelijkheid)**
6. **Beveiligingstesten**

**Legenda status:**
- `[ ]` Nog uit te voeren
- `GESLAAGD` Voldoet volledig aan de verwachting
- `GEFAALD` Afwijking geconstateerd (zie bevindingenlog)

---

## 2. Testgevallen: Functioneel – Bezoekersapplicatie

| ID | Testgeval | Teststappen | Verwacht resultaat | Status |
| :--- | :--- | :--- | :--- | :---: |
| **TC-F01** | Start-pop-up & Attract-mode | Open de applicatie; wacht 90 sec zonder interactie; tik daarna op het scherm. | Start-pop-up verschijnt met korte uitleg en is weg te klikken. Na inactiviteit start de attract-mode; een tik brengt direct de interactieve modus terug. | `[ ]` |
| **TC-F02** | Video afspelen & live kaartsync | Start de 1929-video en laat deze doorlopen van Gaardbrug (1) tot Stadhuisbrug (5). | De actieve marker op de kaart en de mini-map verspringt synchroon met het huidige videosegment. | `[ ]` |
| **TC-F03** | Kaartnavigatie (vooruit/achteruit) | Klik op punt *3. Kalisbrug* en daarna terug op *1. De Gaardbrug*. | De video springt direct naar de juiste starttijd van het aangeklikte punt en toont bijbehorende info en foto's. | `[ ]` |
| **TC-F04** | Video-bediening (slepen/knoppen) | Gebruik de vorige/volgende-knoppen en sleep over de voortgangsbalk/mini-map. | De video reageert vloeiend zonder vastlopen en werkt de getoonde archiefinformatie direct bij. | `[ ]` |
| **TC-F05** | HUA-logo & Colofon-pop-up | Controleer aanwezigheid HUA-logo en klik op de colofon-knop. | Logo is duidelijk zichtbaar in de huisstijl; de pop-up toont info over HUA, de applicatie en de makers en is eenvoudig te sluiten. | `[ ]` |
| **TC-F06** | Geluidseffecten & Mute | Activeer een routepunt met geluid aan; klik daarna op de mute-knop. | Subtiele interactie-/omgevingsgeluiden (geen muziek) zijn hoorbaar en dempen direct bij klikken op mute. | `[ ]` |
| **TC-F07** | Taalinstelling (wens) | Schakel in de header tussen Nederlands (NL) en Engels (EN). | Interface-labels, routetitels en beschrijvingen wijzigen direct naar de gekozen taal. | `[ ]` |

---

## 3. Testgevallen: Functioneel – CMS

| ID | Testgeval | Teststappen | Verwacht resultaat | Status |
| :--- | :--- | :--- | :--- | :---: |
| **TC-C01** | Inloggen & Uitloggen | Log in op `/admin/` met geldig account; test ook een foutief wachtwoord. | Geldige login opent het dashboard; foutief wachtwoord toont een generieke foutmelding. Uitloggen vernietigt de sessie. | `[ ]` |
| **TC-C02** | Routepunt toevoegen/bewerken | Bewerk de tekst en tijdcode van een routepunt en sla op. Ververs de bezoekersapplicatie. | De wijziging wordt opgeslagen in MySQL en is direct zichtbaar in de bezoekersapplicatie. | `[ ]` |
| **TC-C03** | Afbeelding uploaden/koppelen | Upload een nieuwe foto (`JPG`/`PNG`) met alt-tekst bij een routepunt. | Afbeelding verschijnt in het CMS én op de juiste plek in de bezoekersapplicatie. | `[ ]` |
| **TC-C04** | Algemene teksten (Start/Colofon) | Pas de colofon-tekst aan in het CMS en sla op. | De bijgewerkte tekst is direct zichtbaar in de colofon-pop-up aan de voorzijde. | `[ ]` |

---

## 4. Testgevallen: Digibord, Responsiviteit & Browsers

| ID | Testgeval | Testomgeving | Verwacht resultaat | Status |
| :--- | :--- | :--- | :--- | :---: |
| **TC-R01** | Groot touch-digibord | 1920×1080 & 4K touch-scherm | Alle knoppen zijn minimaal 48×48 px, goed bereikbaar en zonder hover-problemen te bedienen. Geen ongewenste scrollbalken. | `[ ]` |
| **TC-R02** | Tablet & Mobiel | iPad / Smartphone viewport | Lay-out schaalt netjes naar een verticale weergave; video, kaart en tekst blijven volledig bruikbaar. | `[ ]` |
| **TC-B01** | Cross-browser check | Chrome, Firefox, Edge, Safari | Video, kaart, CSS-huisstijl en pop-ups werken consistent zonder console-errors in alle vier browsers. | `[ ]` |

---

## 5. Testgevallen: Toegankelijkheid (WCAG 2.1 AA)

| ID | Testgeval | Controlewijze | Verwacht resultaat | Status |
| :--- | :--- | :--- | :--- | :---: |
| **TC-A01** | Kleurcontrast | Contrastchecker op HUA-paars (`#010044`), groenbeige (`#B2910A`) en tekstvlakken. | Tekstcontrast voldoet aan minimaal 4.5:1 (normale tekst) en 3:1 (grote tekst/UI-elementen). | `[ ]` |
| **TC-A02** | Toetsenbordnavigatie | Navigeer door de hele app met alleen `Tab`, `Shift+Tab`, `Enter` en `Escape`. | Elke knop, kaartmarker en modal is bereikbaar en bedienbaar met zichtbare focus-indicator. | `[ ]` |
| **TC-A03** | Schermlezer & Alt-teksten | Inspecteer HTML op `<img alt="...">`, `aria-label` en `aria-live`. | Alle archieffoto's hebben beschrijvende alt-teksten; routewisselingen worden netjes aangekondigd. | `[ ]` |

---

## 6. Testgevallen: Beveiliging

| ID | Testgeval | Teststappen | Verwacht resultaat | Status |
| :--- | :--- | :--- | :--- | :---: |
| **TC-S01** | Ongeautoriseerde CMS-toegang | Open `/admin/dashboard.php` zonder ingelogde sessie. | Directe redirect naar inlogpagina; geen data zichtbaar. | `[ ]` |
| **TC-S02** | SQL-injectie & XSS | Voer `' OR '1'='1` en `<script>alert(1)</script>` in bij login en CMS-velden. | Queries falen veilig (PDO prepared statements); scripts worden als platte tekst getoond en niet uitgevoerd. | `[ ]` |
| **TC-S03** | Bestandsupload-validatie | Probeer een `.php` of `.exe`-bestand te uploaden als afbeelding in het CMS. | Upload wordt server-side geweigerd met een duidelijke foutmelding. | `[ ]` |

---

## 7. Bevindingenlog & Conclusie

### Gevonden bugs / aandachtspunten
| ID | Omschrijving bevinding | Ernst (Hoog/Middel/Laag) | Oplossing / Status |
| :--- | :--- | :---: | :--- |
| *B-01* | *Voorbeeld: ...* | *—* | *—* |

### Eindoordeel
*In te vullen na afronding van alle testen in Sprint 5 (vóór 7 december 2026):*  
- [ ] Goedgekeurd voor oplevering en jurypresentatie bij Het Utrechts Archief.
