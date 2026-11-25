# Plan van Aanpak (PvA) – *Ontmoet de Oudegracht* (Het Utrechts Archief)

## 1. Inleiding

### Beschrijving van de opdracht
Voor **Het Utrechts Archief (HUA)** ontwikkelt het projectteam een interactieve, informatieve webapplicatie voor de tijdelijke tentoonstelling *'Ontmoet de Oudegracht'*, aangevuld met een gebruiksvriendelijk **Content Management Systeem (CMS)**. De webapplicatie combineert een historische bootopname uit **1929** over de Utrechtse Oudegracht met een live meelopende interactieve kaart en historisch bronmateriaal (foto's en verhalen) uit het archief.

### Achtergrondinformatie
Het Utrechts Archief is hét geheugen van de stad en provincie Utrecht. In **maart 2027** opent de tentoonstelling *'Ontmoet de Oudegracht'* ter gelegenheid van het **900-jarig bestaan van de Oudegracht** in 2027. De applicatie wordt op een groot touch-digibord in de tentoonstelling getoond en moet op **7 december 2026** worden opgeleverd en gepresenteerd aan een jury in het auditorium aan de Hamburgerstraat 28 te Utrecht.

### Doelstelling
Het realiseren van een toegankelijke, touch-vriendelijke webapplicatie en een eenvoudig te bedienen CMS waarmee bezoekers (families) de geschiedenis van de Oudegracht interactief kunnen ontdekken en medewerkers van Het Utrechts Archief zelfstandig content kunnen beheren.

---

## 2. Doelgroep

De primaire doelgroep bestaat uit **families**: bezoekers in wisselende samenstellingen van leeftijd (kinderen, ouders, grootouders) en achtergrond.
- **Gebruikssituatie:** Staand of lopend in een tentoonstellingsruimte voor een groot touch-digibord.
- **Ontwerpkeuzes op basis van doelgroep:**
  - Grote, duidelijke touch-knoppen (minimaal 48×48 px, ruim opgezet voor digibord).
  - Korte, aansprekende teksten en visueel rijke historische foto's.
  - Drempelvrije bediening zonder technische voorkennis, inclusief korte weg te klikken startuitleg.
  - Aantrekkelijke *attract-mode* om voorbijgangers op afstand te trekken.
  - Volledige digitale toegankelijkheid conform overheidsrichtlijnen (WCAG 2.1 AA).

---

## 3. Projectdoelen en Randvoorwaarden

### SMART-projectdoelen
1. **Interactieve video- en kaartervaring:** Vóór 7 december 2026 is een werkende webapplicatie opgeleverd waarin de 1929-video synchroon loopt met een interactieve kaart en minimaal alle 8 aangeleverde routepunten uit `Bronmateriaal_en_info.pdf`.
2. **Gebruiksvriendelijk CMS:** Vóór 7 december 2026 kunnen HUA-medewerkers zonder technische kennis via een beveiligd CMS routepunten, teksten, tijdscodes en afbeeldingen toevoegen, wijzigen en verwijderen.
3. **Toegankelijkheid & Huisstijl:** De applicatie voldoet bij oplevering aantoonbaar aan de HUA-huisstijl (2008) en de belangrijkste eisen van WCAG 2.1 niveau AA (contrast, toetsenbord-/schermlezerondersteuning, ondertiteling/visuele alternatieven).
4. **Digibord-gereedheid:** De interface schaalt vloeiend op grote touch-schermen (1920×1080 en 4K) én op reguliere desktop/tablet-schermen zonder horizontale scrollbalken.

### Randvoorwaarden
- **Tijdsbudget:** De beschikbare ontwikkeltijd bedraagt **4 uur per week** per student gedurende het 5-wekenrooster (totaal **20 uur**). Dit vereist een strakke MoSCoW-prioritering en focus op de kernfunctionaliteiten (MVP).
- **Technische stack:** PHP (8.x) en MySQL/MariaDB voor backend en CMS; HTML5, CSS3 en Vanilla JavaScript voor frontend (géén Node.js of JS-backend).
- **Huisstijl:** Strikte toepassing van `HUA_huisstijlmanual digitaal_2.pdf` (Paars `#010044`, Groenbeige `#B2910A`, schuine streepjes `/ / /`, HUA-logo en typografie).
- **Overheidstoegankelijkheid:** Richtlijnen van [rijksoverheid.nl/toegankelijkheid](https://www.rijksoverheid.nl/toegankelijkheid).
- **Deadline:** Definitieve oplevering en jurypresentatie op **7 december 2026**.

---

## 4. Eisen en Wensen (MoSCoW)

### Eisen (Must-haves)
- Afspelen en bedienen van de 1929-bootvideo (slepen, knoppen/pijltjes, klikken op kaart om vooruit/achteruit te "varen").
- Live meelopende interactieve kaart naast de video met aanklikbare routepunten.
- Automatisch tonen van bijbehorende archiefteksten en historische foto's op basis van de videopositie.
- Eenvoudig, beveiligd CMS (PHP/MySQL) voor het beheren van routepunten, teksten en beelden.
- Verwerking van het HUA-logo en een knop met **colofon pop-up** (info over HUA, applicatie en makers).
- Korte, weg te klikken startuitleg en aantrekkelijke weergave op afstand (*attract-mode*).
- Touch-geoptimaliseerde UI voor een groot digibord.
- Toegankelijkheid conform overheidsrichtlijnen (WCAG 2.1 AA).

### Wensen (Should / Could-haves)
- **Taalinstelling** (bijv. Nederlands / Engels) via zowel frontend als CMS.
- **Geluidseffecten** (omgevings- en interactiegeluiden, géén muziek, met mute-optie).
- **Mini-map / voortgangsbalk** onder in beeld die direct toont waar de bezoeker zich op de gracht bevindt.
- **Ondersteunende, functionele animaties** voor betere gebruikerservaring.
- **Google Maps 3D-weergave** (of vergelijkbare 3D/satelliet-kaartlaag).
- **Quiz of mini-game** voor families/kinderen als verdiepende interactie.

---

## 5. Projectaanpak en Werkmethode

Wij werken volgens de **SCRUM-methode** binnen het vastgestelde 5-wekenrooster (Week 1 t/m Week 5):
- **Sprintduur:** 1 week per sprint/fase (5 weken in totaal).
- **Daily Stand-ups:** Dagelijkse korte afstemming (10–15 minuten) over voortgang, dagtaken en eventuele blokkades.
- **Sprint Planning & Review:** Wekelijkse planning conform het onderwijsrooster, met een duidelijke oplevering per week.
- **Samenwerking & Informatie:** Aantekeningen van het klantgesprek met Ellen Blom (woensdag in Week 1) zijn direct verwerkt via medestudenten en gedeeld in het projectteam.

---

## 6. Planning (Volgens het 5-Weken Rooster)

Het project verloopt volgens het officiële curriculumrooster in 5 overzichtelijke projectweken:

| Week | Fase | Activiteiten en Mijlpalen |
| :---: | :--- | :--- |
| **Week 1** | **Voorbereiden van de opdracht** | • **Maandag:** Kick-off van het project.<br>• Gezamenlijk bekijken van de 1929-film in de les en brainstormen over concepten en uitwerkingen.<br>• **Woensdag:** Ellen Blom (opdrachtgever) komt langs in de les voor toelichting en Q&A. Aantekeningen worden gedeeld met teamleden die niet aanwezig konden zijn.<br>• Opstellen van de projectdocumenten: Debriefing, Plan van Aanpak (PvA), Technisch Ontwerp (TO) en Offerte.<br>• Opzetten van versiebeheer (Git) en database-architectuur. |
| **Week 2** | **Werken aan de opdracht** | • Realisatie van de frontend-basis: HUA-huisstijl, lay-out voor groot touch-digibord.<br>• Implementatie van de HTML5-videospeler voor de filmopname uit 1929.<br>• Inrichten van de interactieve kaart (Leaflet/MapLibre) en de live video-kaart-synchronisatie.<br>• Opzet van de MySQL-database (`hua_oudegracht`) en de PHP-backend/JSON-API. |
| **Week 3** | **Werken aan de opdracht, testen** | • Bouw en beveiliging van het CMS voor HUA-medewerkers (inloggen, CRUD-beheer van routepunten).<br>• Koppeling en invoer van alle 8 routepunten en archiefbeelden uit `Bronmateriaal_en_info.pdf`.<br>• Implementatie van start-pop-up met uitleg, attract-mode, colofon-pop-up en subtiele audio-effecten.<br>• **Eerste testronde:** Functionele testen, cross-browser testen en touch-bediening. |
| **Week 4** | **Afronden van de opdracht, voorbereiden van eindpresentatie** | • Verfijnen van de gebruikerservaring, animaties en meertaligheid (NL/EN).<br>• Uitvoeren van de toegankelijkheidsaudit (WCAG 2.1 AA / rijksoverheid.nl/toegankelijkheid).<br>• Beveiligingstesten (SQLi, XSS, CSRF) en laatste bugfixing.<br>• Opstellen van de CMS-gebruikershandleiding.<br>• Voorbereiden en repeteren van de eindpresentatie voor de jury. |
| **Week 5** | **Presentatie van de eindproducten bij Het Utrechts Archief, beoordelen, evaluatie en reflectie** | • **Maandag 7 december 2026:** Presentatie van het eindproduct (webapplicatie + CMS) aan de jury in het auditorium van Het Utrechts Archief (Hamburgerstraat 28, Utrecht) in het bijzijn van alle studenten en Ellen Blom.<br>• Beoordeling van de applicaties door de jury.<br>• Evaluatie en projectreflectie binnen het team en met de opleiding. |

---

## 7. Taken en Rollen

| Rol | Verantwoordelijkheid |
| :--- | :--- |
| **Product Owner** | Bewaakt de visie van Het Utrechts Archief, prioriteert de backlog en onderhoudt contact met Ellen Blom. |
| **Scrum Master** | Faciliteert stand-ups, reviews en retrospectives, en lost blokkades binnen het team op. |
| **Frontend Developer(s)** | Bouwt de bezoekersapplicatie (HTML/CSS/JS), HUA-huisstijl, video-kaart-synchronisatie, mini-map, animaties, audio en touch/WCAG-optimalisatie. |
| **Backend / CMS Developer(s)** | Ontwerpt en bouwt de MySQL-database, PHP REST/JSON-endpoints, het beveiligde CMS en inputvalidatie/beveiliging. |
| **UX/UI Designer & Content/QA** | Ontwerpt wireframes, selecteert/optimaliseert archiefafbeeldingen, voert gebruikersonderzoek en het testplan uit. |

---

## 8. Risicoanalyse

| Risico | Kans | Impact | Beheersmaatregel |
| :--- | :--- | :--- | :--- |
| **Vertraging aanlevering videobestand of logo** | Gemiddeld | Hoog | Direct bij debriefing opvragen; intussen werken met een placeholder-video van gelijke lengte/aspectratio. |
| **Onduidelijkheid route (Stadhuisbrug vs. Bezembrug / punt 4)** | Hoog | Gemiddeld | Vroegtijdig verifiëren bij Ellen Blom en data flexibel via CMS instelbaar maken. |
| **Google Maps 3D API-limieten of offline digibord** | Gemiddeld | Hoog | Leaflet met OpenStreetMap/lokale tiles als stabiele fallback inrichten, zodat de kaart altijd werkt. |
| **Scope creep (te veel wensen zoals quiz + meertaligheid)** | Hoog | Hoog | Strikte MoSCoW-prioritering: eerst alle Must-haves afronden (Sprint 1–3), daarna wensen oppakken in Sprint 4. |
| **Touch-bediening op digibord wijkt af van desktop** | Gemiddeld | Hoog | Vroegtijdig testen op groot aanraakscherm, ruime touch-targets gebruiken en geen hover-afhankelijke UI bouwen. |

---

## 9. Test- en Opleverplan

### Testplan (samenvatting)
In Sprint 5 worden alle onderdelen systematisch getest en vastgelegd in het **Testrapport**:
- **Functionele testen:** Video-kaart-synchronisatie, klikken op routepunten (vooruit/achteruit), start- en colofon-pop-ups, mini-map, geluidseffecten, taalwissel en alle CMS-CRUD-acties.
- **Responsiviteit & Digibordtesten:** Weergave op 1920×1080 en 4K touch-schermen, tablets en smartphones; controle op touch-gestures (slepen/tikken).
- **Browsercompatibiliteit:** Chrome, Firefox, Edge en Safari (laatste versies).
- **Toegankelijkheidstesten:** Toetsenbordnavigatie, kleurcontrast (WCAG 2.1 AA), alt-teksten en ARIA-labels.
- **Beveiligingstesten:** Controle op SQL-injectie (PDO prepared statements), XSS (`htmlspecialchars`), CSRF-tokens en wachtwoordhashing (`password_hash`).

### Oplevering
Op **7 december 2026** presenteert het team het eindproduct live aan de jury in het auditorium van Het Utrechts Archief (Hamburgerstraat 28, Utrecht). Naast de werkende webapplicatie en het CMS wordt een beknopte **CMS-gebruikershandleiding** overhandigd zodat medewerkers direct zelfstandig aan de slag kunnen voor de tentoonstelling in maart 2027.
