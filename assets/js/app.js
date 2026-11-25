// assets/js/app.js - Main Application Controller for Het Utrechts Archief

class HuaApp {
    constructor() {
        this.points = [];
        this.currentPoint = null;
        this.currentLang = 'nl';
        this.video = null;
        this.map = null;
        this.idleTimer = null;
        this.isAttractMode = false;
        this.totalDuration = 105; // 01:45
    }

    async init() {
        this.video = document.getElementById('hua-video');
        
        // 1. Fetch points from API
        try {
            const resp = await fetch('api/points.php');
            const data = await resp.json();
            if (data.status === 'success') {
                this.points = data.data;
            }
        } catch (e) {
            console.error("Fout bij laden van routepunten:", e);
        }

        // 2. Initialize Map
        this.map = new HuaCanalMap('canal-map', (point) => {
            this.seekToPoint(point.id);
        });
        this.map.init(this.points);

        // 3. Render Mini-Map / Canal Route bar
        this.renderMinimap();

        // 4. Setup Scrubber & Markers
        this.setupScrubberMarkers();

        // 5. Setup Video Events & Touch Gestures
        this.setupVideoEvents();
        this.setupTouchGestures();

        // 6. Setup UI Event Listeners (Audio, Lang, Modals, Quiz)
        this.setupEventListeners();

        // 7. Setup Attract Mode
        this.resetIdleTimer();

        // Show start modal by default
        this.openModal('modal-start');

        // Set initial point
        if (this.points.length > 0) {
            this.updateActivePoint(this.points[0], false);
        }
    }

    renderMinimap() {
        const container = document.getElementById('minimap-nodes');
        if (!container) return;
        container.innerHTML = '';

        this.points.forEach(p => {
            const btn = document.createElement('button');
            btn.className = 'minimap-node';
            btn.id = `minimap-node-${p.id}`;
            btn.setAttribute('aria-label', p.title_nl);
            btn.innerHTML = `
                <div class="minimap-node-circle">${p.code}</div>
                <div class="minimap-node-label">${this.currentLang === 'nl' ? p.title_nl : p.title_en}</div>
            `;
            btn.addEventListener('click', () => {
                window.huaSound.playClick();
                this.seekToPoint(p.id);
            });
            container.appendChild(btn);
        });
    }

    setupScrubberMarkers() {
        const markersContainer = document.getElementById('scrubber-markers');
        if (!markersContainer) return;
        markersContainer.innerHTML = '';

        this.points.forEach(p => {
            const pct = (p.video_start_sec / this.totalDuration) * 100;
            const el = document.createElement('div');
            el.className = 'scrubber-marker';
            el.style.left = `${pct}%`;
            el.title = `${p.code}. ${p.title_nl}`;
            markersContainer.appendChild(el);
        });
    }

    setupVideoEvents() {
        if (!this.video) return;

        this.video.addEventListener('loadedmetadata', () => {
            if (this.video.duration && !isNaN(this.video.duration)) {
                this.totalDuration = this.video.duration;
                this.setupScrubberMarkers();
            }
        });

        this.video.addEventListener('timeupdate', () => {
            const curTime = this.video.currentTime;
            this.updateScrubber(curTime);
            this.map.updateBoatProgress(curTime, this.totalDuration);

            // Find current active point based on start & end time
            const activePt = this.points.find(p => curTime >= p.video_start_sec && curTime < p.video_end_sec);
            if (activePt && (!this.currentPoint || activePt.id !== this.currentPoint.id)) {
                this.updateActivePoint(activePt, true);
            }
        });

        this.video.addEventListener('ended', () => {
            document.getElementById('play-icon').textContent = '▶';
        });

        const scrubberTrack = document.getElementById('scrubber-track');
        if (scrubberTrack) {
            scrubberTrack.addEventListener('click', (e) => {
                const rect = scrubberTrack.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const ratio = Math.max(0, Math.min(1, clickX / rect.width));
                this.video.currentTime = ratio * this.totalDuration;
                window.huaSound.playClick();
            });
        }
    }

    updateScrubber(curTime) {
        const pct = (curTime / this.totalDuration) * 100;
        const fill = document.getElementById('scrubber-fill');
        const miniFill = document.getElementById('minimap-line-progress');
        const timeDisplay = document.getElementById('time-display');

        if (fill) fill.style.width = `${pct}%`;
        if (miniFill) miniFill.style.width = `${pct}%`;
        if (timeDisplay) {
            timeDisplay.textContent = `${this.formatTime(curTime)} / ${this.formatTime(this.totalDuration)}`;
        }
    }

    updateActivePoint(point, playChime = false) {
        this.currentPoint = point;

        // Sound chime on new landmark
        if (playChime) {
            window.huaSound.playArrival();
        }

        // Update Info Card
        const titleEl = document.getElementById('info-title');
        const badgeEl = document.getElementById('info-badge');
        const descEl = document.getElementById('info-desc');

        const title = this.currentLang === 'nl' ? point.title_nl : point.title_en;
        const desc = this.currentLang === 'nl' ? point.description_nl : point.description_en;

        if (titleEl) titleEl.textContent = title;
        if (descEl) descEl.textContent = desc;
        if (badgeEl) {
            badgeEl.textContent = point.is_bridge ? (this.currentLang === 'nl' ? 'Historische Brug' : 'Historic Bridge') : (this.currentLang === 'nl' ? 'Werven & Handel' : 'Wharf & Trade');
            badgeEl.style.backgroundColor = point.is_bridge ? 'var(--hua-gold)' : 'var(--hua-purple)';
            badgeEl.style.color = point.is_bridge ? 'var(--hua-purple)' : '#ffffff';
        }

        // Update Photos Gallery
        this.renderGallery(point.images);

        // Update Map Marker Highlight
        this.map.highlightPoint(point.id);

        // Update Minimap Active State
        document.querySelectorAll('.minimap-node').forEach(node => node.classList.remove('active'));
        const activeNode = document.getElementById(`minimap-node-${point.id}`);
        if (activeNode) {
            activeNode.classList.add('active');
        }
    }

    renderGallery(images) {
        const grid = document.getElementById('gallery-grid');
        if (!grid) return;
        grid.innerHTML = '';

        if (!images || images.length === 0) {
            grid.innerHTML = `<p style="color: #888; font-size: 0.9rem;">${this.currentLang === 'nl' ? 'Geen afbeeldingen beschikbaar' : 'No images available'}</p>`;
            return;
        }

        images.forEach(img => {
            const thumb = document.createElement('div');
            thumb.className = 'gallery-thumb';
            thumb.tabIndex = 0;
            thumb.setAttribute('role', 'button');
            const alt = this.currentLang === 'nl' ? (img.alt_text_nl || img.caption_nl) : (img.alt_text_en || img.caption_en);
            const caption = this.currentLang === 'nl' ? img.caption_nl : img.caption_en;

            thumb.innerHTML = `
                <img src="${img.image_path}" alt="${alt}" loading="lazy">
                <div class="gallery-thumb-overlay">${caption || alt}</div>
            `;

            const openLight = () => this.openLightbox(img);
            thumb.addEventListener('click', openLight);
            thumb.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openLight();
                }
            });

            grid.appendChild(thumb);
        });
    }

    openLightbox(img) {
        const modalImg = document.getElementById('lightbox-img');
        const modalCaption = document.getElementById('lightbox-caption');
        const modalArchiveLink = document.getElementById('lightbox-archivelink');

        const caption = this.currentLang === 'nl' ? img.caption_nl : img.caption_en;
        const alt = this.currentLang === 'nl' ? img.alt_text_nl : img.alt_text_en;

        if (modalImg) {
            modalImg.src = img.image_path;
            modalImg.alt = alt || caption;
        }
        if (modalCaption) {
            modalCaption.textContent = caption || alt;
        }
        if (modalArchiveLink) {
            if (img.archive_url) {
                modalArchiveLink.href = img.archive_url;
                modalArchiveLink.style.display = 'inline-block';
            } else {
                modalArchiveLink.style.display = 'none';
            }
        }

        this.openModal('modal-lightbox');
    }

    seekToPoint(pointId) {
        const pt = this.points.find(p => p.id === pointId);
        if (pt && this.video) {
            this.video.currentTime = pt.video_start_sec;
            if (this.video.paused) {
                this.togglePlay();
            }
        }
    }

    skipPoint(direction) {
        if (!this.currentPoint || this.points.length === 0) return;
        const curIdx = this.points.findIndex(p => p.id === this.currentPoint.id);
        const nextIdx = Math.max(0, Math.min(this.points.length - 1, curIdx + direction));
        this.seekToPoint(this.points[nextIdx].id);
        window.huaSound.playClick();
    }

    togglePlay() {
        if (!this.video) return;
        window.huaSound.init();
        const icon = document.getElementById('play-icon');
        if (this.video.paused) {
            this.video.play();
            if (icon) icon.textContent = '⏸';
        } else {
            this.video.pause();
            if (icon) icon.textContent = '▶';
        }
    }

    setupTouchGestures() {
        const card = document.getElementById('video-card');
        if (!card) return;

        let touchStartX = 0;
        let touchStartTime = 0;

        card.addEventListener('touchstart', (e) => {
            this.resetIdleTimer();
            if (e.touches.length === 1) {
                touchStartX = e.touches[0].clientX;
                touchStartTime = this.video.currentTime;
            }
        }, { passive: true });

        card.addEventListener('touchmove', (e) => {
            this.resetIdleTimer();
            if (e.touches.length === 1) {
                const diffX = e.touches[0].clientX - touchStartX;
                // 10px swipe = 1 second scrub
                const seekTo = Math.max(0, Math.min(this.totalDuration, touchStartTime + (diffX / 10)));
                this.video.currentTime = seekTo;
            }
        }, { passive: true });
    }

    setupEventListeners() {
        // Play / Pause Button
        const btnPlay = document.getElementById('btn-play');
        if (btnPlay) {
            btnPlay.addEventListener('click', () => {
                this.togglePlay();
            });
        }

        // Previous / Next Buttons
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');
        if (btnPrev) btnPrev.addEventListener('click', () => this.skipPoint(-1));
        if (btnNext) btnNext.addEventListener('click', () => this.skipPoint(1));

        // Audio Toggle
        const btnAudio = document.getElementById('btn-audio');
        if (btnAudio) {
            btnAudio.addEventListener('click', () => {
                const unmuted = window.huaSound.toggleMute();
                btnAudio.textContent = unmuted ? '🔊 Geluid: Aan' : '🔇 Geluid: Uit';
                btnAudio.setAttribute('aria-pressed', unmuted ? 'true' : 'false');
            });
        }

        // Language Switcher
        const btnLang = document.getElementById('btn-lang');
        if (btnLang) {
            btnLang.addEventListener('click', () => {
                window.huaSound.playClick();
                this.setLanguage(this.currentLang === 'nl' ? 'en' : 'nl');
            });
        }

        // Contrast Switcher
        const btnContrast = document.getElementById('btn-contrast');
        if (btnContrast) {
            btnContrast.addEventListener('click', () => {
                window.huaSound.playClick();
                document.body.classList.toggle('high-contrast');
                const isHigh = document.body.classList.contains('high-contrast');
                btnContrast.setAttribute('aria-pressed', isHigh ? 'true' : 'false');
            });
        }

        // Map Mode Switcher (Street / Satellite 3D)
        document.querySelectorAll('.btn-map-mode').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.btn-map-mode').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const mode = btn.dataset.mode;
                this.map.setLayer(mode);
                window.huaSound.playClick();
            });
        });

        // Colofon Modal Button
        const btnColofon = document.getElementById('btn-colofon');
        if (btnColofon) {
            btnColofon.addEventListener('click', () => {
                window.huaSound.playClick();
                this.openModal('modal-colofon');
            });
        }

        // Quiz Modal Button
        const btnQuiz = document.getElementById('btn-quiz');
        if (btnQuiz) {
            btnQuiz.addEventListener('click', () => {
                window.huaSound.playClick();
                this.openModal('modal-quiz');
            });
        }

        // Close Modal Handlers
        document.querySelectorAll('[data-close-modal]').forEach(el => {
            el.addEventListener('click', () => {
                const modalId = el.closest('.modal-backdrop').id;
                this.closeModal(modalId);
            });
        });

        // Close on Escape Key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-backdrop.open').forEach(m => m.classList.remove('open'));
            }
        });

        // Attract Banner Click
        const attractBanner = document.getElementById('attract-banner');
        if (attractBanner) {
            attractBanner.addEventListener('click', () => {
                this.exitAttractMode();
            });
        }

        // User activity resets idle timer
        ['touchstart', 'mousedown', 'keydown', 'scroll'].forEach(evt => {
            window.addEventListener(evt, () => this.resetIdleTimer(), { passive: true });
        });
    }

    setLanguage(lang) {
        this.currentLang = lang;
        const btnLang = document.getElementById('btn-lang');
        if (btnLang) {
            btnLang.textContent = lang === 'nl' ? '🌐 Taal: NL' : '🌐 Lang: EN';
        }

        // Update labels
        document.querySelectorAll('[data-i18n-nl]').forEach(el => {
            el.textContent = lang === 'nl' ? el.dataset.i18nNl : el.dataset.i18nEn;
        });

        // Re-render current active point & minimap
        if (this.currentPoint) {
            this.updateActivePoint(this.currentPoint, false);
        }
        this.renderMinimap();
    }

    openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('open');
            const focusTarget = modal.querySelector('button, [tabindex="0"]');
            if (focusTarget) focusTarget.focus();
        }
    }

    closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('open');
        }
    }

    resetIdleTimer() {
        if (this.isAttractMode) {
            this.exitAttractMode();
        }
        clearTimeout(this.idleTimer);
        // After 75 seconds of no activity, switch to Attract Mode
        this.idleTimer = setTimeout(() => {
            this.enterAttractMode();
        }, 75000);
    }

    enterAttractMode() {
        this.isAttractMode = true;
        const banner = document.getElementById('attract-banner');
        if (banner) banner.classList.add('visible');

        // Loop the video silently
        if (this.video && this.video.paused) {
            this.video.muted = true;
            this.video.play().catch(() => {});
        }
    }

    exitAttractMode() {
        this.isAttractMode = false;
        const banner = document.getElementById('attract-banner');
        if (banner) banner.classList.remove('visible');
    }

    formatTime(sec) {
        const m = Math.floor(sec / 60);
        const s = Math.floor(sec % 60);
        return `${m}:${s < 10 ? '0' : ''}${s}`;
    }
}

// Global initialization
document.addEventListener('DOMContentLoaded', () => {
    window.huaApp = new HuaApp();
    window.huaApp.init();
});
