# Debriefing – Het Utrechts Archief: *Ontmoet de Oudegracht*

## 1. Algemene informatie

| Veld | Gegevens |
| :--- | :--- |
| **Naam opdrachtgever** | Het Utrechts Archief (HUA) |
| **Contactpersoon opdrachtgever** | Ellen Blom (Educator) — `e.blom@hetutrechtsarchief.nl` |
| **Bezoekadres** | Hamburgerstraat 28, 3512 NS Utrecht |
| **Postadres** | Postbus 131, 3500 AC Utrecht |
| **Depot & studiezaal** | Alexander Numankade 199–201, 3572 KW Utrecht |
| **Telefoon / Website** | 030 286 66 11 / [www.hetutrechtsarchief.nl](https://www.hetutrechtsarchief.nl) |
| **Datum briefing** | 28-10-2026 (Week 1) |
| **Datum en tijd van het gesprek** | Woensdag in Week 1 (klantbezoek Ellen Blom in de les) |
| **Deelnemers & toelichting** | Ellen Blom (HUA), docent en aanwezige studenten/projectleden. *Let op: de auteur van deze debriefing was zelf niet fysiek aanwezig bij het gesprek; de verwerkte informatie en antwoorden zijn gebaseerd op de aantekeningen en uitwisseling van aanwezige project-/studiegenoten.* |
| **Opleverdatum** | Maandag 7 december 2026 (Week 5: presentatie in het auditorium, Hamburgerstraat 28, Utrecht) |

---

## 2. De opdrachtgever & doel van het gesprek

**Het Utrechts Archief (HUA)** is hét geheugen van de stad en provincie Utrecht. De organisatie bewaart historische documenten, foto's, films, kaarten en archiefstukken over de geschiedenis van Utrecht en maakt deze bronnen voor iedereen toegankelijk en beschikbaar.

**Doel van het gesprek / de briefing:** Het afstemmen van de wensen, eisen, doelgroep, technische randvoorwaarden en het beschikbare bronmateriaal voor de ontwikkeling van een interactieve webapplicatie (en bijbehorend CMS) voor de tijdelijke tentoonstelling *'Ontmoet de Oudegracht'*.

---

## 3. Achtergrond & huidige situatie

In **maart 2027** opent bij Het Utrechts Archief de tijdelijke tentoonstelling **'Ontmoet de Oudegracht'**, naar aanleiding van het **900-jarig bestaan van de Oudegracht** in 2027. De tentoonstelling laat zien hoe de Oudegracht door de eeuwen heen is veranderd en wat deze plek betekent voor Utrecht en haar inwoners.

Het Utrechts Archief beschikt over een **historische filmopname uit 1929** vanaf een varende boot over de Oudegracht (traject Gaardbrug – Stadhuisbrug/Bezembrug) en over bijbehorende historische foto's en teksten uit het archief. Bezoekers moeten deze film op een aantrekkelijke, interactieve manier kunnen beleven in de tentoonstellingsruimte.

---

## 4. Opdrachtformulering (vraag van de klant)

### Kern van de opdracht
Ontwikkel een **informatieve, interactieve webapplicatie** waarin de historische bootvideo uit 1929 centraal staat en wordt verrijkt met gekoppeld bronmateriaal (foto's en teksten) uit Het Utrechts Archief, **inclusief een gebruiksvriendelijk CMS** waarmee medewerkers zonder technische kennis de inhoud kunnen beheren.

### Gewenste media-uiting / eindproduct
1. **Bezoekersapplicatie (Frontend):**
   - De 1929-video vormt de visuele basis van de applicatie.
   - Een **interactieve kaart** naast de video die live meeloopt met de vaarroute (optioneel Google Maps in 3D).
   - **Locatiegebonden informatie:** tijdens het varen verschijnen automatisch bijbehorende archiefteksten en historische foto's op basis van de actuele positie in de video.
   - **Interactieve navigatie:** bezoekers kunnen punten op de kaart aanklikken om direct naar dat punt in de video te "varen" (vooruit- of achteruitspoelen), of de video bedienen via touch-interactie (slepen/swipen, knoppen/pijltjes).
   - **Startuitleg & attract-mode:** een weg te klikken start-pop-up met korte uitleg, plus een aantrekkelijke weergave op afstand wanneer er geen interactie plaatsvindt.
   - **Huisstijl, logo & colofon:** verwerking van het HUA-logo en een knop die een colofon-pop-up opent met algemene informatie over HUA, de applicatie en de makers.
   - **Geluid:** ondersteunende omgevings-/interactiegeluiden (uitdrukkelijk géén muziek).
   - **Optioneel / wensen:** taalwisselaar (meertaligheid), een mini-map onder in beeld als voortgangsindicator, ondersteunende functionele animaties en eventueel een quiz of mini-game.

2. **Beheeromgeving (CMS):**
   - Eenvoudig en zonder technische kennis te bedienen door medewerkers van Het Utrechts Archief.
   - Beheren van routepunten (tijdcode in video, kaartcoördinaten, titel, beschrijving, gekoppelde archiefafbeeldingen en eventuele vertalingen).

### Technische en functionele specificaties
- **Weergavehardware:** Groot digibord met **touch-functionaliteit** in de tentoonstellingsruimte (schaalbaarheid, grote touch-targets, geen hover-afhankelijkheid).
- **Serveromgeving:** PHP en MySQL (geen Node.js of JavaScript-backend).
- **Toegankelijkheid:** Voldoen aan overheidsrichtlijnen ([rijksoverheid.nl/toegankelijkheid](https://www.rijksoverheid.nl/toegankelijkheid) / WCAG 2.1 niveau AA).
- **Bronmateriaal:** Aangeleverd in `Bronmateriaal_en_info.pdf` (8 routepunten met historische teksten en directe links naar HUA-beeldbank).

---

## 5. Doelgroepomschrijving

De doelgroep van de tentoonstelling is **'families'**, breed opgevat als bezoekers in verschillende samenstellingen van **leeftijd en achtergrond** (kinderen, ouders, grootouders, scholieren en geïnteresseerde stadsgenoten/toeristen).
- **Implicaties voor ontwerp:** Laagdrempelige, intuïtieve bediening op oog- en handhoogte; heldere en beknopte teksten; visueel aantrekkelijk voor jong en oud; toegankelijk voor mensen met een visuele, auditieve of motorische beperking.

---

## 6. Doelstellingen & KPI's

- **Communicatiedoelstelling:** Bezoekers met nieuwe ogen naar de Oudegracht laten kijken, hen de bijzondere geschiedenis en waarde van de werven en werfkelders laten begrijpen en laten ontdekken hoe deze plek door de eeuwen heen is veranderd.
- **Productdoelstelling:** Een stabiele, aantrekkelijke en volledig beheerbare interactieve applicatie opleveren vóór de jury-presentatie op **7 december 2026**, klaar als basis voor de tentoonstelling in **maart 2027**.
- **Succescriteria (KPI's):**
  - Naadloze synchronisatie tussen video, kaart en archiefbronnen.
  - Zelfstandig beheerbaar door HUA-medewerkers via het CMS zonder technische hulp.
  - Positieve beoordeling op gebruiksvriendelijkheid (touch/digibord), huisstijlgetrouwheid en toegankelijkheid (WCAG 2.1 AA).

---

## 7. Mediakeuze / middelenmix

Gezien de fysieke tentoonstellingsomgeving (groot touch-digibord) en de rijke historische bronnen adviseren wij een **responsive webapplicatie op PHP/MySQL-basis** met een **split-screen interface**:
- **Links/hoofdvlak:** De gerestaureerde 1929-bootvideo met subtiele bedieningsknoppen, een mini-map/voortgangsbalk onderaan en een vloeiende overlay/zijpaneel voor de archiefafbeeldingen en verhalen.
- **Rechts:** De interactieve kaart (Leaflet / Google Maps 3D-optie) met genummerde en aanklikbare routepunten langs de Oudegracht.
- **Audio & Animatie:** Subtiele water-/boot- en klikgeluiden (met mute-knop en respect voor toegankelijkheid) en functionele transities die de aandacht trekken van voorbijlopende museumbezoekers (*attract-mode*).

---

## 8. Randvoorwaarden en bijzonderheden

- **Huisstijl (`HUA_huisstijlmanual digitaal_2.pdf`):**
  - Primaire externe kleuren: **Paars/Donkerblauw** (Pantone 5255, RGB `1/0/68`, `#010044`) en **Groenbeige** (Pantone 457, RGB `178/145/10`, `#B2910A`). Mengkleuren en kleurvlakpercentages toegestaan; witte vlakken toegestaan.
  - Interne/accentkleuren: **Rood** (Pantone 485, RGB `238/45/39`, `#EE2D27`) en **Groen** (Pantone 3145, RGB `0/136/153`, `#008899`) — mengkleuren niet gebruiken voor het logo.
  - **Typografie:** *Berthold Akzidenz Grotesk* (centrale opmaak) en *Arial* / *Helvetica* (documenten/webfallback).
  - **Grafische elementen:** Schuine streepjes (`/ / / /` met 1 spatie ertussen), vierkante kleur-/beeldvlakken en officieel HUA-logo (positief of diapositief).
- **Materialen:** Video-opname uit 1929 en teksten/afbeeldingslinks uit `Bronmateriaal_en_info.pdf`.
- **Geografisch bereik:** Lokaal (Utrecht, Oudegracht), met optionele meertaligheid (NL/EN) voor internationale bezoekers.
- **Budget & Tijdlijn:** Studentenproject binnen de periode **28-10-2026 t/m 07-12-2026**.

---

## 9. Aanvullingen, aandachtspunten en openstaande vragen

Tijdens het analyseren van de briefing en het bronmateriaal zijn enkele concrete aandachtspunten naar voren gekomen om met Ellen Blom te bespreken:
1. **Eindpunt van de video:** In de briefing wordt gesproken over *"van de Gaardbrug tot de Bezembrug"*, terwijl in `Bronmateriaal_en_info.pdf` staat: *"start net voor de Gaardbrug en eindigt bij het stadhuis onder de Stadhuisbrug"*. Wij vragen bevestiging welk eindpunt leidend is in het videobestand.
2. **Nummering routepunten:** In het bronmateriaal springt de nummering van punt **3 (Kalisbrug)** direct naar punt **5 (De Stadhuisbrug)**. Punt 4 ontbreekt, en bij punt 3 staat één afbeeldings-URL dubbel (`452C4EF2...`). Is er een punt 4 weggevallen of mogen wij de nummering opschonen?
3. **Selectie afbeeldingen (>3 stuks):** Bij o.a. *1c Lijnmarkt / Lichte Gaard* en *2 Maartensbrug* staan meer dan drie afbeeldingen. Conform instructie maken wij zelf een passende selectie (en maken het via het CMS achteraf eenvoudig aanpasbaar).
4. **Aanlevering videobestand & rechten:** Wij verzoeken het exacte MP4/WebM-videobestand (liefst geoptimaliseerd voor lokaal/kiosk afspelen) tijdig aan te leveren, evenals eventuele hoge-resolutie versies van het HUA-logo (SVG/PNG).
5. **Google Maps 3D vs. historische/open kaart:** Google Maps 3D vereist een API-key (met mogelijke kosten bij hoog verbruik) en internetverbinding op het digibord. Als robuust en kosteloos alternatief kunnen wij ook een interactieve Leaflet/MapLibre-kaart of een historische kaartlaag inrichten, of Google Maps 3D als configureerbare optie opnemen.
6. **Geluid op het digibord:** Wij adviseren een duidelijke volumeregeling/mute-knop en ondertiteling/visuele feedback zodat het geluid niet stoort in de tentoonstellingsruimte en voldoet aan toegankelijkheidsrichtlijnen.

---

## 10. Actiepunten en Follow-up plan (Rooster Week 1 t/m 5)

De werkzaamheden en contactmomenten verlopen strikt volgens het 5-wekenrooster van het project:

| Fase / Periode | Planning & Activiteiten | Betrokkenen |
| :--- | :--- | :--- |
| **Week 1** | **Voorbereiden van de opdracht**<br>• Maandag: Kick-off van het project.<br>• Filmopname 1929 in de les bekijken en gezamenlijk brainstormen over mogelijke uitwerkingen.<br>• Woensdag: Ellen Blom komt langs in de les voor toelichting en Q&A (aantekeningen ontvangen via medestudenten).<br>• Opstellen en afronden Debriefing, Plan van Aanpak, Technisch Ontwerp en Offerte. | Projectteam, medestudenten & Ellen Blom (HUA) |
| **Week 2** | **Werken aan de opdracht**<br>• Realisatie van de technische basis: video-integratie, Leaflet-kaart, database-opzet (MySQL) en CMS-fundament (PHP). | Projectteam |
| **Week 3** | **Werken aan de opdracht & testen**<br>• Contentintegratie (8 routepunten en archiefbeelden), audio/interactie, colofon/start-pop-up en eerste testronde (functioneel en touch). | Projectteam |
| **Week 4** | **Afronden van de opdracht & voorbereiden eindpresentatie**<br>• Laatste bugfixes, responsive en WCAG-toegankelijkheidscontroles, CMS-gebruikershandleiding en voorbereiden presentatiestructuur. | Projectteam |
| **Week 5** | **Presentatie van de eindproducten bij Het Utrechts Archief**<br>• Maandag 7 december 2026: Presentatie van de eindproducten voor een jury in het auditorium (Hamburgerstraat 28, Utrecht).<br>• Beoordelen van de producten door de jury, evaluatie en reflectie met alle studenten en HUA. | Projectteam, alle studenten, jury & Ellen Blom |
