// assets/js/map.js - Leaflet.js Interactive Canal Map for HUA

class HuaCanalMap {
    constructor(containerId, onSelectCallback) {
        this.containerId = containerId;
        this.onSelectCallback = onSelectCallback;
        this.map = null;
        this.markers = {};
        this.pointsData = [];
        this.boatMarker = null;
        this.canalPolyline = null;
        this.routeCoords = [];
        this.baseLayers = {};
    }

    init(points) {
        this.pointsData = points;

        // Center on the Oudegracht between Gaardbrug and Stadhuisbrug
        this.map = L.map(this.containerId, {
            center: [52.0912, 5.1200],
            zoom: 16.5,
            minZoom: 15,
            maxZoom: 19,
            zoomControl: false
        });

        // Add zoom control at bottom-right for clean digibord look
        L.control.zoom({ position: 'bottomright' }).addTo(this.map);

        // Tile layer 1: Clear, crisp CartoDB Positron (perfect for historical museum contrast)
        const streetLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
            maxZoom: 19
        }).addTo(this.map);

        // Tile layer 2: High-resolution Aerial/Satellite (3D / Aerial perspective)
        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri, Maxar, Earthstar Geographics'
        });

        this.baseLayers = {
            'street': streetLayer,
            'satellite': satelliteLayer
        };

        // Canal route coordinates
        this.routeCoords = this.pointsData.map(p => [p.latitude, p.longitude]);

        // Draw canal route line (Utrecht water path)
        this.canalPolyline = L.polyline(this.routeCoords, {
            color: '#008899', // HUA teal
            weight: 6,
            opacity: 0.75,
            dashArray: '4, 8',
            lineCap: 'round'
        }).addTo(this.map);

        // Add markers for all route points
        this.pointsData.forEach(p => {
            const isBridge = p.is_bridge;
            const markerHtml = `
                <div class="hua-map-marker ${isBridge ? 'bridge-marker' : ''}" id="map-marker-${p.id}" title="${p.title_nl}">
                    ${p.code}
                </div>
            `;

            const icon = L.divIcon({
                className: 'hua-marker-wrapper',
                html: markerHtml,
                iconSize: [32, 32],
                iconAnchor: [16, 16],
                popupAnchor: [0, -18]
            });

            const marker = L.marker([p.latitude, p.longitude], { icon: icon }).addTo(this.map);

            marker.bindPopup(`
                <div style="font-family: Arial; padding: 4px;">
                    <strong style="color: #010044; font-size: 14px;">${p.title_nl}</strong><br>
                    <span style="font-size: 12px; color: #666;">Tijd in video: ${this.formatTime(p.video_start_sec)}</span><br>
                    <button style="margin-top: 6px; background: #B2910A; color: #fff; border:none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%;" onclick="window.huaApp.seekToPoint(${p.id})">
                        ⚓ Vaar hierheen
                    </button>
                </div>
            `);

            marker.on('click', () => {
                if (this.onSelectCallback) {
                    this.onSelectCallback(p);
                }
            });

            this.markers[p.id] = marker;
        });

        // Sailing boat icon representing current video position
        const boatHtml = `<div class="boat-marker" role="img" aria-label="Huidige vaarpositie">⛵</div>`;
        const boatIcon = L.divIcon({
            className: 'hua-boat-wrapper',
            html: boatHtml,
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        if (this.routeCoords.length > 0) {
            this.boatMarker = L.marker(this.routeCoords[0], { icon: boatIcon, zIndexOffset: 1000 }).addTo(this.map);
        }
    }

    setLayer(layerKey) {
        if (!this.baseLayers[layerKey]) return;
        Object.values(this.baseLayers).forEach(layer => {
            if (this.map.hasLayer(layer)) {
                this.map.removeLayer(layer);
            }
        });
        this.baseLayers[layerKey].addTo(this.map);
    }

    highlightPoint(pointId) {
        document.querySelectorAll('.hua-map-marker').forEach(el => {
            el.classList.remove('active-marker');
        });

        const activeEl = document.getElementById(`map-marker-${pointId}`);
        if (activeEl) {
            activeEl.classList.add('active-marker');
        }

        const point = this.pointsData.find(p => p.id === pointId);
        if (point && this.map) {
            // Smoothly pan without disorienting
            this.map.panTo([point.latitude, point.longitude], { animate: true, duration: 0.8 });
        }
    }

    updateBoatProgress(currentTime, totalDuration) {
        if (!this.boatMarker || this.pointsData.length < 2) return;
        if (!totalDuration || totalDuration <= 0) totalDuration = 105;

        // Find which segment we are currently in
        let curPointIndex = 0;
        for (let i = 0; i < this.pointsData.length; i++) {
            if (currentTime >= this.pointsData[i].video_start_sec) {
                curPointIndex = i;
            }
        }

        const p1 = this.pointsData[curPointIndex];
        const p2 = this.pointsData[Math.min(curPointIndex + 1, this.pointsData.length - 1)];

        if (p1 && p2 && p1 !== p2) {
            const segDuration = Math.max(1, p2.video_start_sec - p1.video_start_sec);
            const segProgress = Math.min(1, Math.max(0, (currentTime - p1.video_start_sec) / segDuration));
            const lat = p1.latitude + (p2.latitude - p1.latitude) * segProgress;
            const lng = p1.longitude + (p2.longitude - p1.longitude) * segProgress;
            this.boatMarker.setLatLng([lat, lng]);
        } else if (p1) {
            this.boatMarker.setLatLng([p1.latitude, p1.longitude]);
        }
    }

    formatTime(sec) {
        const m = Math.floor(sec / 60);
        const s = Math.floor(sec % 60);
        return `${m}:${s < 10 ? '0' : ''}${s}`;
    }
}
