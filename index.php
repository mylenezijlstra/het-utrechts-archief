<?php
// index.php - Bezoekersapplicatie: Ontmoet de Oudegracht (1929)
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

// Ensure DB is initialized
$db = getDB();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ontmoet de Oudegracht (1929) – Het Utrechts Archief</title>
    
    <!-- Meta tags voor toegankelijkheid en kiosk display -->
    <meta name="description" content="Interactieve ontdekking van de Utrechtse Oudegracht in 1929 via historische filmopnames en archiefbronnen van Het Utrechts Archief.">
    <meta name="theme-color" content="#010044">

    <!-- Leaflet CSS & JS for Interactive Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Custom HUA Huisstijl Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Header met HUA Branding & Toegankelijke Knoppen -->
    <header class="hua-header" role="banner">
        <a href="index.php" class="hua-brand" aria-label="Het Utrechts Archief Home">
            <?= renderHuaLogoSvg('white') ?>
            <div class="hua-title-group">
                <h1 data-i18n-nl="Ontmoet de Oudegracht (1929)" data-i18n-en="Meet the Oudegracht (1929)">Ontmoet de Oudegracht (1929)</h1>
                <p data-i18n-nl="900 jaar Oudegracht • Het Utrechts Archief" data-i18n-en="900 years Oudegracht • Utrecht Archives">900 jaar Oudegracht • Het Utrechts Archief</p>
            </div>
        </a>

        <div class="hua-header-actions" role="toolbar" aria-label="Applicatie instellingen en informatie">
            <button id="btn-colofon" class="btn-touch btn-header" aria-haspopup="dialog" aria-label="Informatie en makers van deze applicatie">
                ℹ️ <span data-i18n-nl="Over & Colofon" data-i18n-en="About & Colophon">Over & Colofon</span>
            </button>

            <button id="btn-quiz" class="btn-touch btn-header" aria-haspopup="dialog" aria-label="Familie Quiz over de Oudegracht">
                🎯 <span data-i18n-nl="Familie Quiz" data-i18n-en="Family Quiz">Familie Quiz</span>
            </button>

            <button id="btn-audio" class="btn-touch btn-header" aria-pressed="false" aria-label="Schakel omgevingsgeluid in of uit">
                🔇 <span data-i18n-nl="Geluid: Uit" data-i18n-en="Sound: Off">Geluid: Uit</span>
            </button>

            <button id="btn-lang" class="btn-touch btn-header" aria-label="Wissel van taal tussen Nederlands en Engels">
                🌐 <span>Taal: NL</span>
            </button>

            <button id="btn-contrast" class="btn-touch btn-header" aria-pressed="false" aria-label="Schakel hoog contrast modus in voor betere leesbaarheid">
                👁️ <span data-i18n-nl="Contrast" data-i18n-en="Contrast">Contrast</span>
            </button>

            <a href="admin/" class="btn-touch btn-header" title="Beheeromgeving voor medewerkers">
                ⚙️ <span data-i18n-nl="Beheer (CMS)" data-i18n-en="CMS Admin">Beheer (CMS)</span>
            </a>
        </div>
    </header>

    <!-- Authentieke HUA Diagonale Strepen Strook -->
    <?= renderDiagonalDivider() ?>

    <!-- Hoofd Applicatie Container (Split-Screen) -->
    <main class="app-container" role="main">
        
        <!-- Linker Paneel: 1929 Videospeler & Live Archiefinfo -->
        <section class="left-panel" aria-label="Historische video en archiefbronnen">
            
            <!-- Videokaart met Touch Scrubber -->
            <div class="video-card" id="video-card">
                <div class="video-overlay-badge">
                    <span class="pulse-dot" aria-hidden="true"></span>
                    <span data-i18n-nl="Boottocht Oudegracht 1929" data-i18n-en="Canal Boat Tour 1929">Boottocht Oudegracht 1929</span>
                </div>

                <video id="hua-video" class="video-element" playsinline preload="auto">
                    <source src="assets/videos/videowebsite.mp4" type="video/mp4">
                    <p>Uw browser ondersteunt deze HTML5 video niet. Bezoek Het Utrechts Archief om de filmopname te bekijken.</p>
                </video>

                <!-- Touch & Bedieningsbalk -->
                <div class="video-controls-bar">
                    <div class="scrubber-container" id="scrubber-container" title="Klik of sleep om door de film te spoelen">
                        <div class="scrubber-track" id="scrubber-track">
                            <div class="scrubber-fill" id="scrubber-fill"></div>
                            <div class="scrubber-markers" id="scrubber-markers"></div>
                        </div>
                    </div>

                    <div class="video-buttons-row">
                        <div class="controls-left">
                            <button id="btn-prev" class="btn-video-ctrl" aria-label="Vaar naar vorig punt" title="Vorig punt">⏮</button>
                            <button id="btn-play" class="btn-video-ctrl" aria-label="Afspelen of pauzeren" title="Afspelen / Pauze">
                                <span id="play-icon">▶</span>
                            </button>
                            <button id="btn-next" class="btn-video-ctrl" aria-label="Vaar naar volgend punt" title="Volgend punt">⏭</button>
                        </div>

                        <div class="controls-right">
                            <span class="time-display" id="time-display">0:00 / 1:45</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Routepunt Details & Archief Foto's -->
            <article class="info-card" id="info-card" aria-live="polite">
                <div class="info-card-header">
                    <h2 id="info-title">1. De Gaardbrug</h2>
                    <span class="point-badge" id="info-badge">Historische Brug</span>
                </div>

                <div class="info-card-description" id="info-desc">
                    Laden van historische informatie...
                </div>

                <div class="gallery-section">
                    <h3>
                        <span>🖼️</span> 
                        <span data-i18n-nl="Foto's uit Het Utrechts Archief (klik voor vergroting):" data-i18n-en="Photos from Utrecht Archives (click to enlarge):">Foto's uit Het Utrechts Archief (klik voor vergroting):</span>
                    </h3>
                    <div class="gallery-grid" id="gallery-grid">
                        <!-- Dynamisch gevuld met foto's uit het archief -->
                    </div>
                </div>
            </article>

        </section>

        <!-- Rechter Paneel: Live Meelopende Interactieve Kaart -->
        <section class="right-panel" aria-label="Interactieve kaart van de Oudegracht">
            <div class="map-card">
                <div class="map-header">
                    <div class="map-title">
                        <span>🗺️</span> 
                        <span data-i18n-nl="Vaarroute Oudegracht (Gaardbrug ➔ Stadhuisbrug)" data-i18n-en="Boat Route Oudegracht (Gaardbrug ➔ Stadhuisbrug)">Vaarroute Oudegracht (Gaardbrug ➔ Stadhuisbrug)</span>
                    </div>
                    <div class="map-mode-toggle" role="group" aria-label="Kaartstijl selecteren">
                        <button class="btn-map-mode active" data-mode="street" data-i18n-nl="Kaart" data-i18n-en="Map">Kaart</button>
                        <button class="btn-map-mode" data-mode="satellite" data-i18n-nl="Luchtfoto / 3D" data-i18n-en="Aerial / 3D">Luchtfoto / 3D</button>
                    </div>
                </div>

                <div id="canal-map" role="application" aria-label="Interactieve kaart met aanklikbare locaties langs de Oudegracht"></div>
            </div>
        </section>

    </main>

    <!-- Onderste Mini-Map / Vaarroute Tijdlijn Indicator -->
    <nav class="bottom-minimap-bar" aria-label="Mini-map navigatie langs de Oudegracht">
        <div class="minimap-title">
            <span data-i18n-nl="⚓ Route langs de Oudegracht • Tik op een locatie om er direct naartoe te varen" data-i18n-en="⚓ Oudegracht Route • Tap a location to sail directly to it">
                ⚓ Route langs de Oudegracht • Tik op een locatie om er direct naartoe te varen
            </span>
            <span style="font-size: 0.75rem; opacity: 0.85;">1929 Filmroute</span>
        </div>

        <div class="minimap-track-container">
            <div class="minimap-line">
                <div class="minimap-line-progress" id="minimap-line-progress"></div>
            </div>
            <div class="minimap-nodes" id="minimap-nodes">
                <!-- Dynamisch gevuld met routeknoppen -->
            </div>
        </div>
    </nav>

    <!-- Attract Mode Banner (voor museumpasanten) -->
    <div class="attract-banner" id="attract-banner" role="status" aria-live="assertive">
        <span>👋</span>
        <span data-i18n-nl="Raak het scherm aan om zelf over de Oudegracht te varen!" data-i18n-en="Touch the screen to explore Utrecht's Oudegracht!">
            Raak het scherm aan om zelf over de Oudegracht te varen!
        </span>
    </div>

    <!-- MODAL 1: Start & Uitleg Pop-up -->
    <div class="modal-backdrop" id="modal-start" role="dialog" aria-modal="true" aria-labelledby="start-title">
        <div class="modal-box">
            <div class="modal-header">
                <h2 id="start-title">⛵ <span data-i18n-nl="Welkom bij Ontmoet de Oudegracht" data-i18n-en="Welcome to Meet the Oudegracht">Welkom bij Ontmoet de Oudegracht</span></h2>
                <button class="btn-close-modal" data-close-modal aria-label="Sluit uitleg venster">&times;</button>
            </div>
            <div class="modal-body">
                <p><strong>In 2027 bestaat de wereldberoemde Utrechtse Oudegracht 900 jaar!</strong></p>
                <p data-i18n-nl="Stap aan boord van deze unieke filmopname uit 1929. Terwijl de boot over de gracht vaart, ontdek je hoe de bruggen, werven en werfkelders er bijna een eeuw geleden uitzagen." data-i18n-en="Step aboard this unique 1929 film recording. As the boat sails along the canal, discover what the bridges, wharves and cellars looked like nearly a century ago.">
                    Stap aan boord van deze unieke filmopname uit 1929. Terwijl de boot over de gracht vaart, ontdek je hoe de bruggen, werven en werfkelders er bijna een eeuw geleden uitzagen.
                </p>
                <div style="background: rgba(178, 145, 10, 0.15); border-left: 4px solid var(--hua-gold); padding: 12px; border-radius: 4px; margin-bottom: 14px;">
                    <strong>💡 <span data-i18n-nl="Hoe werkt het?" data-i18n-en="How does it work?">Hoe werkt het?</span></strong>
                    <ul style="margin-left: 20px; margin-top: 6px;">
                        <li data-i18n-nl="Tik op de kaart of de mini-map onderaan om direct naar een brug of markt te varen." data-i18n-en="Tap on the map or bottom mini-map to sail directly to a bridge or market.">Tik op de kaart of de mini-map onderaan om direct naar een brug of markt te varen.</li>
                        <li data-i18n-nl="Sleep over de video met je vinger om door de tijd te spoelen." data-i18n-en="Drag horizontally across the video with your finger to scrub through time.">Sleep over de video met je vinger om door de tijd te spoelen.</li>
                        <li data-i18n-nl="Tik op een archieffoto om deze in groot formaat te bewonderen." data-i18n-en="Tap any archival photo to view it in high resolution.">Tik op een archieffoto om deze in groot formaat te bewonderen.</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-touch btn-primary" data-close-modal>
                    🚢 <span data-i18n-nl="Start de Rondvaart" data-i18n-en="Start Boat Tour">Start de Rondvaart</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Colofon Pop-up (Over Het Utrechts Archief, Tentoonstelling & Makers) -->
    <div class="modal-backdrop" id="modal-colofon" role="dialog" aria-modal="true" aria-labelledby="colofon-title">
        <div class="modal-box modal-large">
            <div class="modal-header">
                <h2 id="colofon-title">ℹ️ <span data-i18n-nl="Colofon & Over Het Utrechts Archief" data-i18n-en="Colophon & About Utrecht Archives">Colofon & Over Het Utrechts Archief</span></h2>
                <button class="btn-close-modal" data-close-modal aria-label="Sluit colofon venster">&times;</button>
            </div>
            <div class="modal-body">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px; border-bottom: 1px solid var(--hua-border); padding-bottom: 16px;">
                    <div style="width: 140px; flex-shrink: 0;">
                        <?= renderHuaLogoSvg('positive') ?>
                    </div>
                    <div>
                        <h3 style="color: var(--hua-purple); font-family: var(--font-heading); font-size: 1.2rem;">Het Utrechts Archief (HUA)</h3>
                        <p style="font-size: 0.9rem; color: #555; margin: 0;">Hét geheugen van de stad en provincie Utrecht. Bewaarplaats van documenten, beelden en verhalen van de middeleeuwen tot vandaag.</p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h4 style="color: var(--hua-gold); font-size: 1.05rem; margin-bottom: 6px;">Tentoonstelling 900 Jaar Oudegracht</h4>
                        <p style="font-size: 0.9rem;">
                            In maart 2027 opent de tentoonstelling <em>'Ontmoet de Oudegracht'</em> ter gelegenheid van 900 jaar Utrechtse werven en grachten (1127–2027). Deze applicatie brengt de historische filmopname uit 1929 tot leven voor museumbezoekers en families.
                        </p>

                        <h4 style="color: var(--hua-gold); font-size: 1.05rem; margin-top: 14px; margin-bottom: 6px;">Opdrachtgever & Begeleiding</h4>
                        <p style="font-size: 0.9rem;">
                            <strong>Het Utrechts Archief</strong><br>
                            Educator & Begeleider: <strong>Ellen Blom</strong> (<a href="mailto:e.blom@hetutrechtsarchief.nl" style="color: var(--hua-purple);">e.blom@hetutrechtsarchief.nl</a>)<br>
                            Locatie: Hamburgerstraat 28, 3512 NS Utrecht<br>
                            Website: <a href="https://www.hetutrechtsarchief.nl" target="_blank" rel="noopener" style="color: var(--hua-purple);">www.hetutrechtsarchief.nl</a>
                        </p>
                    </div>

                    <div>
                        <h4 style="color: var(--hua-gold); font-size: 1.05rem; margin-bottom: 6px;">Makers van de Applicatie</h4>
                        <p style="font-size: 0.9rem;">
                            Ontwikkeld en ontworpen door:<br>
                            <strong>Mylène Zijlstra</strong> & <strong>Iris Govaard</strong><br>
                            Opleiding: <strong>Grafisch Lyceum Utrecht</strong><br>
                            Projectperiode: Oktober – December 2026
                        </p>

                        <h4 style="color: var(--hua-gold); font-size: 1.05rem; margin-top: 14px; margin-bottom: 6px;">Toegankelijkheid & Technologie</h4>
                        <p style="font-size: 0.9rem;">
                            Deze applicatie is gebouwd met oog voor digitale toegankelijkheid conform de overheidsrichtlijnen (<a href="https://www.rijksoverheid.nl/toegankelijkheid" target="_blank" rel="noopener" style="color: var(--hua-purple);">WCAG 2.1 AA</a>). De backend draait op PHP en relationele database-architectuur met een touch-vriendelijk Content Management Systeem (CMS).
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-touch btn-secondary" data-close-modal>
                    <span data-i18n-nl="Sluiten" data-i18n-en="Close">Sluiten</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 3: Familie Quiz (Wens voor families) -->
    <div class="modal-backdrop" id="modal-quiz" role="dialog" aria-modal="true" aria-labelledby="quiz-title">
        <div class="modal-box">
            <div class="modal-header">
                <h2 id="quiz-title">🎯 <span data-i18n-nl="Oudegracht Familie Quiz!" data-i18n-en="Oudegracht Family Quiz!">Oudegracht Familie Quiz!</span></h2>
                <button class="btn-close-modal" data-close-modal aria-label="Sluit quiz venster">&times;</button>
            </div>
            <div class="modal-body" id="quiz-body">
                <div id="quiz-q1" class="quiz-question-block">
                    <p><strong>Vraag 1 van 3:</strong> Waar ontleent de Gaardbrug haar historische naam aan?</p>
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 10px;">
                        <button class="btn-touch btn-header" style="color: var(--hua-purple); background: #eee; text-align: left;" onclick="handleQuizAnswer(1, false, this)">A) Aan een vroegere gevangenisbewaker ('de gaard')</button>
                        <button class="btn-touch btn-header" style="color: var(--hua-purple); background: #eee; text-align: left;" onclick="handleQuizAnswer(1, true, this)">B) Aan de vroegere bisschoppelijke tuin ('de gaard')</button>
                        <button class="btn-touch btn-header" style="color: var(--hua-purple); background: #eee; text-align: left;" onclick="handleQuizAnswer(1, false, this)">C) Aan een scheepswerf waar boten werden bewaard</button>
                    </div>
                    <div id="quiz-feedback-1" style="margin-top: 10px; font-weight: bold; display: none;"></div>
                </div>

                <div id="quiz-q2" class="quiz-question-block" style="display: none; margin-top: 16px;">
                    <p><strong>Vraag 2 van 3:</strong> Hoe bleef de vis op de historische Vismarkt vers voordat er koelkasten waren?</p>
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 10px;">
                        <button class="btn-touch btn-header" style="color: var(--hua-purple); background: #eee; text-align: left;" onclick="handleQuizAnswer(2, false, this)">A) Door de vis in zoutbergen te begraven</button>
                        <button class="btn-touch btn-header" style="color: var(--hua-purple); background: #eee; text-align: left;" onclick="handleQuizAnswer(2, true, this)">B) In viskaren (doorboorde houten bakken) die in de gracht hingen</button>
                        <button class="btn-touch btn-header" style="color: var(--hua-purple); background: #eee; text-align: left;" onclick="handleQuizAnswer(2, false, this)">C) Door het ijs uit de Domtoren te halen</button>
                    </div>
                    <div id="quiz-feedback-2" style="margin-top: 10px; font-weight: bold; display: none;"></div>
                </div>

                <div id="quiz-q3" class="quiz-question-block" style="display: none; margin-top: 16px;">
                    <p><strong>Vraag 3 van 3:</strong> Welke twee bruggen werden samengevoegd om de Stadhuisbrug te vormen?</p>
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 10px;">
                        <button class="btn-touch btn-header" style="color: var(--hua-purple); background: #eee; text-align: left;" onclick="handleQuizAnswer(3, true, this)">A) De Broodbrug en de Huidenbrug</button>
                        <button class="btn-touch btn-header" style="color: var(--hua-purple); background: #eee; text-align: left;" onclick="handleQuizAnswer(3, false, this)">B) De Visbrug en de Kalisbrug</button>
                        <button class="btn-touch btn-header" style="color: var(--hua-purple); background: #eee; text-align: left;" onclick="handleQuizAnswer(3, false, this)">C) De Borchbrug en de Gaardbrug</button>
                    </div>
                    <div id="quiz-feedback-3" style="margin-top: 10px; font-weight: bold; display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-touch btn-primary" data-close-modal>
                    <span data-i18n-nl="Klaar" data-i18n-en="Done">Klaar</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 4: Image Lightbox Modal -->
    <div class="modal-backdrop" id="modal-lightbox" role="dialog" aria-modal="true" aria-labelledby="lightbox-caption">
        <div class="modal-box modal-large" style="background: #111; color: #fff;">
            <div class="modal-header" style="background: #000; border-bottom: 1px solid #333;">
                <h2>🖼️ <span data-i18n-nl="Historische Beeldbank Utrechts Archief" data-i18n-en="Utrecht Archives Historical Image">Historische Beeldbank</span></h2>
                <button class="btn-close-modal" data-close-modal aria-label="Sluit afbeelding">&times;</button>
            </div>
            <div class="modal-body" style="padding: 16px;">
                <div class="lightbox-img-wrap">
                    <img id="lightbox-img" src="" alt="Archiefafbeelding uit Het Utrechts Archief">
                </div>
                <div class="lightbox-meta">
                    <p id="lightbox-caption" style="font-weight: bold; font-size: 1.05rem; margin-bottom: 8px;"></p>
                    <a id="lightbox-archivelink" href="#" target="_blank" rel="noopener" class="btn-touch btn-header" style="font-size: 0.85rem; padding: 6px 14px; background: var(--hua-gold); color: var(--hua-purple);">
                        🔗 <span data-i18n-nl="Bekijk origineel in beeldbank HUA" data-i18n-en="View original in Utrecht Archives">Bekijk origineel in beeldbank HUA</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Simple inline interactive quiz handler for families
    function handleQuizAnswer(qNum, isCorrect, btn) {
        window.huaSound.init();
        const feedback = document.getElementById(`quiz-feedback-${qNum}`);
        feedback.style.display = 'block';
        if (isCorrect) {
            window.huaSound.playArrival();
            feedback.style.color = '#008899';
            feedback.innerHTML = '🎉 <strong>Helemaal goed!</strong> Uitstekend speurwerk!';
            btn.style.background = '#d4edda';
            btn.style.borderColor = '#28a745';
            setTimeout(() => {
                const nextQ = document.getElementById(`quiz-q${qNum + 1}`);
                if (nextQ) {
                    nextQ.style.display = 'block';
                    nextQ.scrollIntoView({ behavior: 'smooth' });
                }
            }, 900);
        } else {
            window.huaSound.playClick();
            feedback.style.color = '#EE2D27';
            feedback.innerHTML = '❌ <em>Helaas, probeer het nog eens!</em>';
            btn.style.background = '#f8d7da';
        }
    }
    </script>

    <!-- App Scripts -->
    <script src="assets/js/audio.js"></script>
    <script src="assets/js/map.js"></script>
    <script src="assets/js/app.js"></script>

</body>
</html>
