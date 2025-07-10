// Metro Maps JavaScript Functions

// Global variables
let mapInstance = null;
let layerGroups = {};
let panganLayer = null;
let backgroundAudio = null;
let isAudioPlaying = false;
let currentCCTVData = null;
let cctvMarkers = [];

// Initialize map after DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if Leaflet is loaded
    if (typeof L === 'undefined') {
        console.error('Leaflet library not loaded');
        return;
    }

    // Check if map container exists
    const mapContainer = document.getElementById('map');
    if (!mapContainer) {
        console.error('Map container not found');
        return;
    }

    try {
        // Initialize map
        mapInstance = L.map('map').setView([-5.114141, 105.307573], 16);
        
        L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{
            maxZoom: 20,
            subdomains:['mt0','mt1','mt2','mt3'],
            attribution: '&copy; <a href="https://www.google.com/maps">Google Maps</a>'
        }).addTo(mapInstance);

        console.log('Map initialized successfully');

        // Make map globally accessible
        window.mapInstance = mapInstance;

        layerGroups = {
            cctv: L.layerGroup().addTo(mapInstance),
            pemerintahan: L.layerGroup(),
            pendidikan: L.layerGroup(),
            virtual: L.layerGroup(),
            kesehatan: L.layerGroup(),
            layanan: L.layerGroup(),
            pangan: L.layerGroup()
        };

        panganLayer = L.layerGroup();

        // Make functions globally accessible
        window.layerGroups = layerGroups;
        window.panganLayer = panganLayer;
        window.cctvMarkers = cctvMarkers;
        window.currentCCTVData = currentCCTVData;

        // Initialize Lucide icons
        setTimeout(() => {
            lucide.createIcons();
        }, 100);

    } catch (error) {
        console.error('Error initializing map:', error);
        // Show user-friendly error message
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            mapContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f8f9fa; color: #666; font-size: 16px;">Gagal memuat peta. Silakan refresh halaman.</div>';
        }
    }
});

// Create marker icons with factory function
function createIcon(color, iconName) {
    return L.divIcon({
        html: `
            <div style="position: relative; width: 36px; height: 54px; background: transparent; border: none;">
                <!-- Shadow -->
                <div style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 12px; height: 3px; background: rgba(0,0,0,0.25); border-radius: 50%; filter: blur(1px);"></div>
                <!-- Tail (white outline) -->
                <div style="position: absolute; top: 28px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 9px solid transparent; border-right: 9px solid transparent; border-top: 18px solid white;"></div>
                <!-- Tail (colored) -->
                <div style="position: absolute; top: 26px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 7px solid transparent; border-right: 7px solid transparent; border-top: 16px solid ${color};"></div>
                <!-- Main body -->
                <div style="position: absolute; top: 2px; left: 50%; transform: translateX(-50%); width: 32px; height: 32px; background: ${color}; border: 3px solid white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.25);">
                    <i data-lucide="${iconName}" style="color: white; width: 16px; height: 16px;"></i>
                </div>
            </div>
        `,
        iconSize: [36, 54],
        iconAnchor: [18, 54],
        popupAnchor: [0, -54],
        className: 'custom-marker-icon'
    });
}

// Add CCTV marker function - to be called from Blade template
function addCCTVMarker(index, lat, lng, nama, link, id, views) {
    const cctvIcon = createIcon('#007bff', 'cctv');
    const marker = L.marker([lat, lng], {icon: cctvIcon})
        .addTo(layerGroups.cctv);
    
    marker.bindTooltip(nama, {
        permanent: false,
        direction: 'top',
        offset: [0, -45]
    });
    
    marker.on('click', function() {
        openCCTVPreview(link, nama, id, views);
    });
    
    cctvMarkers.push({
        marker: marker,
        id: id,
        name: nama,
        link: link,
        lat: lat,
        lng: lng,
        views: views
    });
}

// Add other marker types
function addPemerintahanMarker(index, lat, lng, unit) {
    const pemerintahanIcon = createIcon('#1e40af', 'building-2');
    const marker = L.marker([lat, lng], {icon: pemerintahanIcon})
        .addTo(layerGroups.pemerintahan);
    
    marker.bindTooltip(unit, {
        permanent: false,
        direction: 'top',
        offset: [0, -45]
    });
    
    marker.on('click', function() {
        mapInstance.setView([lat, lng], 18);
    });
}

function addPendidikanMarker(index, lat, lng, nama, alamat) {
    const pendidikanIcon = createIcon('#109c42', 'graduation-cap');
    const marker = L.marker([lat, lng], {icon: pendidikanIcon})
        .addTo(layerGroups.pendidikan);
    
    marker.bindTooltip(nama, {
        permanent: false,
        direction: 'top',
        offset: [0, -45]
    });
    
    marker.on('click', function() {
        mapInstance.setView([lat, lng], 18);
        
        marker.bindPopup(`
            <div style="font-family: 'Inter', sans-serif; min-width: 200px;">
                <h3 style="margin: 0 0 8px 0; color: #109c42; font-size: 16px; font-weight: 600;">${nama}</h3>
                <p style="margin: 0; color: #666; font-size: 14px; line-height: 1.4;">
                    <i data-lucide="map-pin" style="width: 14px; height: 14px; margin-right: 4px; vertical-align: middle;"></i>
                    ${alamat}
                </p>
            </div>
        `).openPopup();
        
        setTimeout(() => {
            lucide.createIcons();
        }, 10);
    });
}

function addVirtualTourMarker(index, lat, lng, nama, link) {
    const virtualTourIcon = createIcon('#00BFFF', 'binoculars');
    const marker = L.marker([lat, lng], {icon: virtualTourIcon})
        .addTo(layerGroups.virtual);
    
    marker.bindTooltip(nama, {
        permanent: false,
        direction: 'top',
        offset: [0, -45]
    });
    
    marker.on('click', function() {
        mapInstance.setView([lat, lng], 18);
        
        // Tampilkan popup dengan preview dan informasi hotspot
        marker.bindPopup(`
            <div style="font-family: 'Inter', sans-serif; min-width: 280px;">
                <h3 style="margin: 0 0 12px 0; color: #00BFFF; font-size: 16px; font-weight: 600; display: flex; align-items: center;">
                    <i data-lucide="binoculars" style="width: 20px; height: 20px; margin-right: 8px;"></i>
                    ${nama}
                </h3>
            
                <div style="text-align: center; margin: 15px 0;">
                    <button onclick="openVirtualTour('${link}', '${nama}', ${lat}, ${lng})" 
                            style="background: #00BFFF; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 2px 8px rgba(0, 191, 255, 0.3); transition: all 0.2s;">
                        Buka Virtual Tour
                    </button>
                </div>
            </div>
        `).openPopup();
        
        setTimeout(() => {
            lucide.createIcons();
        }, 10);
    });
}

function addLayananMarker(index, lat, lng, nama, alamat) {
    const layananIcon = createIcon('#e36334', 'users');
    const marker = L.marker([lat, lng], {icon: layananIcon})
        .addTo(layerGroups.layanan);
    
    marker.bindTooltip(nama, {
        permanent: false,
        direction: 'top',
        offset: [0, -45]
    });
    
    marker.on('click', function() {
        mapInstance.setView([lat, lng], 18);
        
        marker.bindPopup(`
            <div style="font-family: 'Inter', sans-serif; min-width: 220px;">
                <h3 style="margin: 0 0 10px 0; color: #e36334; font-size: 16px; font-weight: 600;">${nama}</h3>
                <div style="background: #fff5f2; padding: 8px 12px; border-radius: 6px; border-left: 4px solid #e36334; margin-top: 8px;">
                    <span style="color: #666; font-size: 13px; font-weight: 500; display: flex; align-items: center;">
                        <i data-lucide="map-pin" style="width: 14px; height: 14px; margin-right: 6px; vertical-align: middle; color: #e36334;"></i>
                        ${alamat}
                    </span>
                </div>
                <div style="margin-top: 8px; padding: 6px 0; border-top: 1px solid #f0f0f0;">
                    <span style="color: #e36334; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i data-lucide="users" style="width: 12px; height: 12px; margin-right: 4px; vertical-align: middle;"></i>
                        Layanan Publik
                    </span>
                </div>
            </div>
        `).openPopup();
        
        setTimeout(() => {
            lucide.createIcons();
        }, 10);
    });
}

function addKesehatanMarker(index, lat, lng, nama, posisi, balita, remaja, ibuHamil, lansia) {
    const kesehatanIcon = createIcon('#E91E63', 'hospital');
    const marker = L.marker([lat, lng], {icon: kesehatanIcon})
        .addTo(layerGroups.kesehatan);
    
    marker.bindTooltip(nama, {
        permanent: false,
        direction: 'top',
        offset: [0, -45]
    });
    
    marker.on('click', function() {
        mapInstance.setView([lat, lng], 18);
        
        marker.bindPopup(`
            <div style="font-family: 'Inter', sans-serif; min-width: 250px;">
                <h3 style="margin: 0 0 10px 0; color: #E91E63; font-size: 16px; font-weight: 600;">${nama}</h3>
                <div style="margin-bottom: 8px;">
                    <span style="color: #666; font-size: 13px; font-weight: 500;">
                        <i data-lucide="map-pin" style="width: 12px; height: 12px; margin-right: 4px; vertical-align: middle;"></i>
                        Posisi: ${posisi}
                    </span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 10px; font-size: 13px;">
                    <div style="background: #f8f9fa; padding: 6px 8px; border-radius: 4px; border-left: 3px solid #E91E63;">
                        <span style="color: #666; font-weight: 500;">Balita:</span><br>
                        <span style="color: #333; font-weight: 600;">${balita || 'N/A'}</span>
                    </div>
                    <div style="background: #f8f9fa; padding: 6px 8px; border-radius: 4px; border-left: 3px solid #E91E63;">
                        <span style="color: #666; font-weight: 500;">Remaja:</span><br>
                        <span style="color: #333; font-weight: 600;">${remaja || 'N/A'}</span>
                    </div>
                    <div style="background: #f8f9fa; padding: 6px 8px; border-radius: 4px; border-left: 3px solid #E91E63;">
                        <span style="color: #666; font-weight: 500;">Ibu Hamil:</span><br>
                        <span style="color: #333; font-weight: 600;">${ibuHamil || 'N/A'}</span>
                    </div>
                    <div style="background: #f8f9fa; padding: 6px 8px; border-radius: 4px; border-left: 3px solid #E91E63;">
                        <span style="color: #666; font-weight: 500;">Lansia:</span><br>
                        <span style="color: #333; font-weight: 600;">${lansia || 'N/A'}</span>
                    </div>
                </div>
            </div>
        `).openPopup();
        
        setTimeout(() => {
            lucide.createIcons();
        }, 10);
    });
}

// Global functions (outside DOMContentLoaded to be accessible from HTML onclick)
function showOnly(target) {
    const map = window.mapInstance || mapInstance;
    const layers = window.layerGroups || layerGroups;
    const pangan = window.panganLayer || panganLayer;
    
    if (!map || !layers) {
        console.error('Map not initialized');
        return;
    }
    
    // Remove 'active' class from all navigation items (except audio-toggle-link)
    document.querySelectorAll('.nav-menu .nav-item, .nav-menu .nav-item-audio').forEach(item => {
        if (item.id !== 'audio-toggle-link') {
            item.classList.remove('active');
        }
    });

    // Hide all layers and related panels
    for (const key in layers) {
        map.removeLayer(layers[key]);
    }
    document.getElementById('pangan-filter').style.display = 'none';
    document.getElementById('legend').style.display = 'none';
    document.getElementById('cctv-list-container').style.display = 'none';
    map.removeLayer(pangan);
    pangan.clearLayers();

    // Virtual tour container handling
    const virtualTourContainer = document.getElementById('virtual-tour-container');
    if (virtualTourContainer) {
        virtualTourContainer.style.display = 'none';
    }

    // Show target layer
    if (target === 'pangan') {
        document.getElementById('pangan-filter').style.display = 'flex';
        document.getElementById('legend').style.display = 'block';
        map.addLayer(pangan);
        loadPanganData();
    } else if (target === 'cctv') {
        map.addLayer(layers[target]);
        document.getElementById('cctv-list-container').style.display = 'block';
        
        setTimeout(() => {
            lucide.createIcons();
        }, 100);
    } else if (target === 'virtual') {
        map.addLayer(layers[target]);
        if (virtualTourContainer) {
            virtualTourContainer.style.display = 'block';
        }
        
        setTimeout(() => {
            lucide.createIcons();
        }, 100);
    } else if (layers[target]) {
        map.addLayer(layers[target]);
        
        if (target === 'pemerintahan' || target === 'pendidikan' || target === 'kesehatan' || target === 'layanan') {
            setTimeout(() => {
                lucide.createIcons();
            }, 100);
        }
    }

    // Add 'active' class to clicked navigation item
    const clickedNavItem = document.getElementById(`${target}-nav`);
    if (clickedNavItem) {
        clickedNavItem.classList.add('active');
    }
}

// Function to focus on CCTV marker from list
function focusOnMarker(index, cctvId, cctvName, cctvLink, lat, lng, views) {
    const map = window.mapInstance || mapInstance;
    if (!map) {
        console.error('Map not initialized');
        return;
    }
    
    map.setView([lat, lng], 18);
    openCCTVPreview(cctvLink, cctvName, cctvId, views);
}

// Function to open CCTV preview
function openCCTVPreview(cctvLink, cctvName, cctvId, currentViews = 0) {
    window.currentCCTVData = { 
        link: cctvLink, 
        name: cctvName, 
        id: cctvId,
        views: currentViews 
    };
    
    // Update UI preview
    document.getElementById('cctv-preview-title').textContent = cctvName;
    document.getElementById('cctv-preview-iframe').src = cctvLink;
    document.getElementById('cctv-view-counter').textContent = formatNumber(currentViews);
    
    // Show overlay with animation
    const overlay = document.getElementById('cctv-preview-overlay');
    overlay.style.display = 'flex';
    setTimeout(() => {
        overlay.classList.add('show');
        lucide.createIcons();
    }, 10);
    
    // Update view counter
    updateViewCount(cctvId);
}

// Function to close preview with improved animation
function closePreview() {
    const overlay = document.getElementById('cctv-preview-overlay');
    const container = overlay.querySelector('.cctv-preview-container');
    
    // Fadeout animation with transform
    overlay.style.opacity = '0';
    container.style.transform = 'translateY(-50px)';
    
    setTimeout(() => {
        overlay.style.display = 'none';
        overlay.classList.remove('show');
        // Reset styles for next use
        overlay.style.opacity = '';
        container.style.transform = '';
        // Clear iframe
        document.getElementById('cctv-preview-iframe').src = '';
        window.currentCCTVData = null;
    }, 300);
}

// Function to update view counter
function updateViewCount(cctvId) {
    fetch(`/cctv/view/${cctvId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update counter in preview if currently open
            if (window.currentCCTVData && window.currentCCTVData.id === cctvId) {
                window.currentCCTVData.views += 1;
                document.getElementById('cctv-view-counter').textContent = formatNumber(window.currentCCTVData.views);
            }
            
            // Update counter in CCTV list
            const listElement = document.getElementById(`view-count-${cctvId}`);
            if (listElement) {
                const currentCount = parseInt(listElement.textContent.replace(/[^\d]/g, '')) || 0;
                listElement.textContent = formatNumber(currentCount + 1) + ' views';
            }
        }
    })
    .catch(error => {
        console.error('Error updating view count:', error);
    });
}

// Helper function to format numbers
function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

// Function to focus on CCTV (backward compatibility)
function focusCCTV(lat, lng, nama, link) {
    const map = window.mapInstance || mapInstance;
    if (!map) {
        console.error('Map not initialized');
        return;
    }
    
    map.setView([lat, lng], 18);
    
    // Find matching CCTV data and open preview
    const cctvData = cctvMarkers.find(cctv => 
        Math.abs(cctv.lat - lat) < 0.0001 && Math.abs(cctv.lng - lng) < 0.0001
    );
    
    if (cctvData) {
        openCCTVPreview(cctvData.link, cctvData.name, cctvData.id, cctvData.views);
    }
}

// Function to open virtual tour
function openVirtualTour(link, nama, lat, lng) {
    const map = window.mapInstance || mapInstance;
    if (!map) {
        console.error('Map not initialized');
        return;
    }
    
    map.setView([lat, lng], 18);
    window.open(link, '_blank');
}

// Function to focus on Virtual Tour marker from list
function focusOnVirtualTour(index, tourName, tourLink, lat, lng) {
    const map = window.mapInstance || mapInstance;
    if (!map) {
        console.error('Map not initialized');
        return;
    }
    
    map.setView([lat, lng], 18);
    window.open(tourLink, '_blank');
}

// Audio toggle function
function toggleAudio() {
    const audioIcon = document.getElementById('audio-toggle-icon');
    const audioLink = document.getElementById('audio-toggle-link');

    if (window.isAudioPlaying || isAudioPlaying) {
        (window.backgroundAudio || backgroundAudio).pause();
        audioIcon.setAttribute('data-lucide', 'volume-off');
        audioLink.classList.remove('active');
        window.isAudioPlaying = false;
        isAudioPlaying = false;
    } else {
        (window.backgroundAudio || backgroundAudio).play();
        audioIcon.setAttribute('data-lucide', 'volume-2');
        audioLink.classList.add('active');
        window.isAudioPlaying = true;
        isAudioPlaying = true;
    }
    
    lucide.createIcons();
}

// Additional DOMContentLoaded handlers
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Initialize audio object
    window.backgroundAudio = new Audio('/audio/audi_kota.mp3');
    window.backgroundAudio.loop = true;
    window.isAudioPlaying = false;
    backgroundAudio = window.backgroundAudio;
    isAudioPlaying = window.isAudioPlaying;

    // Set default active navigation
    const navItems = document.querySelectorAll('.nav-menu .nav-item');
    if (navItems.length > 0) {
        showOnly('cctv');
    }

    // Audio toggle event listener
    const audioToggleLink = document.getElementById('audio-toggle-link');
    if (audioToggleLink) {
        audioToggleLink.addEventListener('click', function(event) {
            event.preventDefault();
            toggleAudio();
        });
    }

    // Event listeners for Pangan filters
    const indikatorSelect = document.getElementById('indikator-select');
    const tahunSelect = document.getElementById('tahun-select');
    
    if (indikatorSelect) {
        indikatorSelect.addEventListener('change', loadPanganData);
    }
    if (tahunSelect) {
        tahunSelect.addEventListener('change', loadPanganData);
    }

    // CCTV overlay event listeners
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const overlay = document.getElementById('cctv-preview-overlay');
            if (overlay && overlay.classList.contains('show')) {
                closePreview();
            }
        }
    });

    const cctvOverlay = document.getElementById('cctv-preview-overlay');
    if (cctvOverlay) {
        cctvOverlay.addEventListener('click', function(event) {
            if (event.target === this) {
                closePreview();
            }
        });
    }
});

// Pangan data functions
function updateLegend(indikator) {
    const legend = document.getElementById('legend');
    legend.innerHTML = '';
    legend.style.display = 'block';

    let html = '';

    const htmlPrioritas = (title) => `
        <h4>${title}</h4>
        <div><span style="background:#800026 !important;"></span> Prioritas 1</div>
        <div><span style="background:#e31a1c !important;"></span> Prioritas 2</div>
        <div><span style="background:#fc9272 !important;"></span> Prioritas 3</div>
        <div><span style="background:#a1d99b !important;"></span> Prioritas 4</div>
        <div><span style="background:#31a354 !important;"></span> Prioritas 5</div>
        <div><span style="background:#006837 !important;"></span> Prioritas 6</div>
        <div><span style="background:#ffffff !important; border: 1px solid #ccc;"></span> Tidak ada data</div>
    `;

    if (indikator === 'indeks') {
        html = `
            <h4>Indeks Komposit Ketahanan & Kerentanan Pangan</h4>
            <div><span style="background:#800026 !important;"></span> Sangat Rentan</div>
            <div><span style="background:#e31a1c !important;"></span> Rentan</div>
            <div><span style="background:#fc9272 !important;"></span> Agak Rentan</div>
            <div><span style="background:#a1d99b !important;"></span> Agak Tahan</div>
            <div><span style="background:#31a354 !important;"></span> Tahan</div>
            <div><span style="background:#006837 !important;"></span> Sangat Tahan</div>
            <div><span style="background:#ffffff !important; border: 1px solid #ccc;"></span> Tidak ada data</div>
        `;
    } else if (indikator === 'sarana') {
        html = htmlPrioritas('Sarana Penyedia Pangan');
    } else if (indikator === 'rasio_10') {
        html = htmlPrioritas('Rasio Penduduk 10% Terendah');
    } else if (indikator === 'akses') {
        html = htmlPrioritas('Tanpa Akses Penghubung');
    } else if (indikator === 'air_bersih') {
        html = `
            <h4>Persentase Rumah Tangga tanpa Air Bersih</h4>
            <div><span style="background:#800026 !important;"></span> ≥ 70%</div>
            <div><span style="background:#e31a1c !important;"></span> 60 – &lt; 70%</div>
            <div><span style="background:#fc9272 !important;"></span> 50 – &lt; 60%</div>
            <div><span style="background:#a1d99b !important;"></span> 40 – &lt; 50%</div>
            <div><span style="background:#31a354 !important;"></span> 30 – &lt; 40%</div>
            <div><span style="background:#006837 !important;"></span> &lt; 30%</div>
            <div><span style="background:#ffffff !important; border: 1px solid #ccc;"></span> Tidak ada data</div>
        `;
    } else if (indikator === 'rasio_nakes') {
        html = `
            <h4>Rasio Penduduk per Tenaga Kesehatan vs Kepadatan</h4>
            <div><span style="background:#800026 !important;"></span> ≥ 30</div>
            <div><span style="background:#e31a1c !important;"></span> 20 – &lt; 30</div>
            <div><span style="background:#fc9272 !important;"></span> 15 – &lt; 20</div>
            <div><span style="background:#a1d99b !important;"></span> 10 – &lt; 15</div>
            <div><span style="background:#31a354 !important;"></span> 5 – &lt; 10</div>
            <div><span style="background:#006837 !important;"></span> &lt; 5</div>
            <div><span style="background:#ffffff !important; border: 1px solid #ccc;"></span> Tidak ada data</div>
        `;
    }

    legend.innerHTML = html;
}

function getColor(value, indikator, kategori = null) {
    // If we have a kategori from the controller, use it directly
    if (kategori) {
        switch (kategori) {
            // Indeks categories
            case 'sangat_rentan': return '#800026';
            case 'rentan': return '#e31a1c';
            case 'agak_rentan': return '#fc9272';
            case 'agak_tahan': return '#a1d99b';
            case 'tahan': return '#31a354';
            case 'sangat_tahan': return '#006837';
            
            // Priority categories
            case 'prioritas_1': return '#800026';
            case 'prioritas_2': return '#e31a1c';
            case 'prioritas_3': return '#fc9272';
            case 'prioritas_4': return '#a1d99b';
            case 'prioritas_5': return '#31a354';
            case 'prioritas_6': return '#006837';
            
            // Air bersih categories
            case '>= 70': return '#800026';
            case '60 -< 70': return '#e31a1c';
            case '50 -< 60': return '#fc9272';
            case '40 -< 50': return '#a1d99b';
            case '30 -< 40': return '#31a354';
            case '< 30': return '#006837';
            
            // Rasio nakes categories
            case '>= 30': return '#800026';
            case '20 -< 30': return '#e31a1c';
            case '15 -< 20': return '#fc9272';
            case '10 -< 15': return '#a1d99b';
            case '5 -< 10': return '#31a354';
            case '< 5': return '#006837';
            
            default: return '#ffffff'; // No data color
        }
    }
    
    // Fallback to numeric value if no category provided
    const numValue = typeof value === 'string' ? parseFloat(value) : value;
    
    if (numValue === null || numValue === undefined || isNaN(numValue)) {
        return '#ffffff'; // White color for no data
    }

    switch (indikator) {
        case 'indeks':
            if (numValue >= 0 && numValue < 20) return '#800026';
            if (numValue >= 20 && numValue < 40) return '#e31a1c';
            if (numValue >= 40 && numValue < 60) return '#fc9272';
            if (numValue >= 60 && numValue < 70) return '#a1d99b';
            if (numValue >= 70 && numValue < 85) return '#31a354';
            if (numValue >= 85) return '#006837';
            break;
        case 'sarana':
        case 'rasio_10':
        case 'akses':
            if (numValue <= 0.16) return '#800026';
            if (numValue <= 0.33) return '#e31a1c';
            if (numValue <= 0.50) return '#fc9272';
            if (numValue <= 0.66) return '#a1d99b';
            if (numValue <= 0.83) return '#31a354';
            if (numValue > 0.83) return '#006837';
            break;
        case 'air_bersih':
            if (numValue >= 70) return '#800026';
            if (numValue >= 60 && numValue < 70) return '#e31a1c';
            if (numValue >= 50 && numValue < 60) return '#fc9272';
            if (numValue >= 40 && numValue < 50) return '#a1d99b';
            if (numValue >= 30 && numValue < 40) return '#31a354';
            if (numValue < 30) return '#006837';
            break;
        case 'rasio_nakes':
            if (numValue >= 30) return '#800026';
            if (numValue >= 20 && numValue < 30) return '#e31a1c';
            if (numValue >= 15 && numValue < 20) return '#fc9272';
            if (numValue >= 10 && numValue < 15) return '#a1d99b';
            if (numValue >= 5 && numValue < 10) return '#31a354';
            if (numValue < 5) return '#006837';
            break;
    }
    return '#ccc';
}

function stylePangan(feature, indikator) {
    let value = null;
    let kategori = null;
    
    if (feature.properties && feature.properties.data) {
        value = feature.properties.data.nilai;
        kategori = feature.properties.data.kategori;
    }

    return {
        fillColor: getColor(value, indikator, kategori),
        weight: 2,
        opacity: 1,
        color: 'white',
        dashArray: '3',
        fillOpacity: 0.7
    };
}

function loadPanganData() {
    const indikatorSelect = document.getElementById('indikator-select');
    const tahunSelect = document.getElementById('tahun-select');
    const legend = document.getElementById('legend');
    const pangan = window.panganLayer || panganLayer;

    if (!indikatorSelect || !tahunSelect || !pangan) {
        console.error('Required elements not found');
        return;
    }

    const indikator = indikatorSelect.value;
    const tahun = tahunSelect.value;

    pangan.clearLayers();
    legend.innerHTML = '';
    legend.style.display = 'none';

    if (!indikator || !tahun) {
        return;
    }

    // Show loading state
    legend.style.display = 'block';
    legend.innerHTML = '<div style="padding: 10px; text-align: center;"><i>Memuat data...</i></div>';

    fetch(`/api/pangan?indikator=${indikator}&tahun=${tahun}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('DATA API (first 5):', data.slice(0, 5));
            if (data.length === 0) {
                legend.innerHTML = '<div style="padding: 10px; text-align: center; color: #666;"><i>Tidak ada data untuk kombinasi indikator dan tahun ini</i></div>';
                return;
            }

            // Create data mapping by id_desa or kode_desa for faster lookup
            const dataMap = {};
            data.forEach(item => {
                if (item.id_desa) dataMap[String(item.id_desa).trim()] = item;
                if (item.kode_desa) dataMap[String(item.kode_desa).trim()] = item;
            });

            // Load GeoJSON for administrative boundaries
            fetch('/metro_desa.geojson')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal memuat data geografis');
                    }
                    return response.json();
                })
                .then(geoJson => {
                    console.log('SAMPLE GEOJSON (first 5):', geoJson.features.slice(0, 5).map(f => f.properties));
                    let matchedFeatures = 0;
                    geoJson.features.forEach(feature => {
                        const kdKelurahan = String(feature.properties.kd_kelurahan).trim();
                        const matchingData = dataMap[kdKelurahan];
                        if (matchingData) {
                            feature.properties.data = matchingData;
                            matchedFeatures++;
                        }
                    });
                    console.log(`Matched ${matchedFeatures} features out of ${geoJson.features.length} total features`);

                    L.geoJson(geoJson, {
                        style: function(feature) {
                            return stylePangan(feature, indikator);
                        },
                        onEachFeature: function(feature, layer) {
                            const desaName = feature.properties.nm_kelurahan || 'Kelurahan tidak diketahui';
                            let popupContent = `<div style="font-family: 'Inter', sans-serif; min-width: 200px;">
                                <h3 style="margin: 0 0 8px 0; color: #333; font-size: 16px; font-weight: 600;">${desaName}</h3>`;
                            
                            if (feature.properties.data) {
                                const data = feature.properties.data;
                                const indikatorLabels = {
                                    'indeks': 'Indeks Komposit Ketahanan & Kerentanan Pangan',
                                    'sarana': 'Sarana Penyedia Pangan',
                                    'rasio_10': 'Rasio Penduduk 10% Terendah',
                                    'akses': 'Tanpa Akses Penghubung',
                                    'air_bersih': 'Persentase Rumah Tangga Tanpa Air Bersih',
                                    'rasio_nakes': 'Rasio Penduduk per Tenaga Kesehatan vs Kepadatan'
                                };
                                
                                popupContent += `
                                    <div style="background: #f8f9fa; padding: 8px 12px; border-radius: 6px; margin-top: 8px;">
                                        <div style="margin-bottom: 4px;"><strong>Kecamatan:</strong> ${data.kecamatan}</div>
                                        <div style="margin-bottom: 4px;"><strong>Indikator:</strong> ${indikatorLabels[indikator] || indikator}</div>
                                        <div style="margin-bottom: 4px;"><strong>Nilai:</strong> ${data.nilai}</div>
                                        <div style="margin-bottom: 4px;"><strong>Kategori:</strong> ${data.kategori}</div>
                                        <div><strong>Tahun:</strong> ${tahun}</div>
                                    </div>
                                `;
                            } else {
                                popupContent += `<div style="color: #666; font-style: italic; margin-top: 8px;">Data tidak tersedia untuk indikator ini</div>`;
                            }
                            
                            popupContent += '</div>';
                            layer.bindPopup(popupContent);
                        }
                    }).addTo(pangan);

                    updateLegend(indikator);
                })
                .catch(error => {
                    console.error('Error loading GeoJSON:', error);
                    legend.innerHTML = '<div style="padding: 10px; text-align: center; color: #d32f2f;"><i>Gagal memuat data geografis</i></div>';
                });
        })
        .catch(error => {
            console.error('Error fetching pangan data:', error);
            legend.innerHTML = '<div style="padding: 10px; text-align: center; color: #d32f2f;"><i>Gagal memuat data. Silakan coba lagi.</i></div>';
        });
}
