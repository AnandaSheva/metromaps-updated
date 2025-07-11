<!DOCTYPE html>
<html>
<head>
    <title>Metro Maps | Pemerintah Kota Metro</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <!-- Add Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- Map CSS -->
    <link rel="stylesheet" href="{{ asset('css/map.css') }}" />

</head>

<body>
    
    <div class="header">
        <div class="bg-pattern-1"></div>
        <div class="bg-pattern-2"></div>

        <div class="header-content">
            <div class="logo-section">
                <img src="{{ asset('images/komdigi.png') }}" alt="Logo Kiri" class="logo-image-5">
                <div class="vertical-separator"></div>
                <img src="{{ asset('images/logokotametro.png') }}" alt="Logo Kota Metro" class="logo-kotametro">
                <div class="logo-text-group">
                    <div class="main-title-header">PEMERINTAH KOTA METRO</div>
                    <div class="sub-title-header">CCTV PINTAR KOTA BERBASIS KEWILAYAHAN</div>
                </div>
            </div>

            <div class="nav-menu" id="nav-menu">
                <!-- Mobile Header - only visible in responsive mode -->
                <div class="mobile-nav-header">
                    <img src="{{ asset('images/komdigi.png') }}" alt="Logo Komdigi" class="mobile-nav-logo">
                    <h3 class="mobile-nav-title">Metro Maps</h3>
                </div>
                
                <a href="#" class="nav-item" id="cctv-nav" onclick="showOnly('cctv')">
                    <span class="nav-item-text">CCTV</span>
                    <i data-lucide="cctv" class="nav-item-icon"></i>
                </a>
                <a href="#" class="nav-item" id="virtual-tour-nav" onclick="showOnly('virtual')">
                    <span class="nav-item-text">Virtual Tour</span>
                    <i data-lucide="binoculars" class="nav-item-icon"></i>
                </a>
                <a href="#" class="nav-item" id="pemerintahan-nav" onclick="showOnly('pemerintahan')">
                    <span class="nav-item-text">Pemerintahan</span>
                    <i data-lucide="building-2" class="nav-item-icon"></i>
                </a>
                <a href="#" class="nav-item" id="pendidikan-nav" onclick="showOnly('pendidikan')">
                    <span class="nav-item-text">Pendidikan</span>
                    <i data-lucide="university" class="nav-item-icon"></i>
                </a>
                <a href="#" class="nav-item" id="kesehatan-nav" onclick="showOnly('kesehatan')">
                    <span class="nav-item-text">Kesehatan</span>
                    <i data-lucide="hospital" class="nav-item-icon"></i>
                </a>
                <a href="#" class="nav-item" id="layanan-nav" onclick="showOnly('layanan')">
                    <span class="nav-item-text">Layanan Publik</span>
                    <i data-lucide="users" class="nav-item-icon"></i>
                </a>
                <a href="#" class="nav-item" id="pangan-nav" onclick="showOnly('pangan')">
                    <span class="nav-item-text">Pangan</span>
                    <i data-lucide="wheat" class="nav-item-icon"></i>
                </a>
                <a href="#" class="nav-item-audio" id="audio-toggle-link">
                    <span class="nav-item-text"></span>
                    <i data-lucide="volume-off" class="nav-item-icon" id="audio-toggle-icon"></i>
                </a>
            </nav>
            
            <button class="hamburger-menu" id="hamburger-menu" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>

    <div id="map"></div>
    <div class="map-overlay">
        <img src="{{ asset('images/logodiskominfo.png') }}" alt="Logo Diskominfo" class="diskominfo-logo-bottom-right">
    </div>

    <!-- CCTV List Container using Component -->
    @php
        // Pisahkan CCTV yang memiliki nama_lokasi dan yang tidak
        $cctvWithNames = $cctvs->filter(function($cctv) {
            return !is_null($cctv->nama_lokasi) && $cctv->nama_lokasi !== '';
        })->sortBy('nama_lokasi');
        
        $cctvWithoutNames = $cctvs->filter(function($cctv) {
            return is_null($cctv->nama_lokasi) || $cctv->nama_lokasi === '';
        });
        
        // Gabungkan: CCTV tanpa nama di atas, kemudian CCTV dengan nama (terurut abjad)
        $sortedCctvs = $cctvWithoutNames->concat($cctvWithNames);
    @endphp

    <x-list-container
        id="cctv-list-container"
        position="primary"
        theme="red"
        title="CCTV Tersedia"
        icon="cctv"
        :count="count($cctvs)"
        :items="$sortedCctvs"
        item-type="cctv"
    />

    <div id="pangan-filter" style="display: none; flex-direction: column; gap: 5px; margin-top: 10px;">
        <select id="indikator-select" class="service-btn pangan-btn">
            <option value="" disabled selected>Pilih Indikator</option>
            <option value="indeks">Indeks Komposit Ketahanan dan Kerentanan Pangan</option>
            <option value="sarana">Sarana Penyedia Pangan</option>
            <option value="rasio_10">Rasio Penduduk 10% Terendah</option>
            <option value="akses">Tanpa Akses Penghubung</option>
            <option value="air_bersih">Persentase Rumah Tangga Tanpa Air Bersih</option>
            <option value="rasio_nakes">Rasio Penduduk per Tenaga Kesehatan vs Kepadatan</option>
        </select>

        <select id="tahun-select" class="service-btn pangan-btn">
            <option value="" disabled selected>Pilih Tahun</option>
            <option value="2024">2024</option>
            <option value="2023">2023</option>
            <option value="2022">2022</option>
            <option value="2021">2021</option>
        </select>
    </div>

    <div id="legend"></div>

    <!-- Virtual Tour Container using Component -->
    @php
        // Urutkan virtual tour berdasarkan nama lokasi
        $sortedSpheres = $spheres->sortBy('nama_lokasi');
    @endphp

    <x-list-container
        id="virtual-tour-container"
        position="primary"
        theme="blue"
        title="Virtual Tour Tersedia"
        icon="binoculars"
        :count="count($spheres)"
        :items="$sortedSpheres"
        item-type="virtual-tour"
    />

    <!-- CCTV Preview Overlay -->
    <div id="cctv-preview-overlay">
        <div class="cctv-preview-container">
            <div class="cctv-preview-header">
                <div class="cctv-preview-title-section">
                    <img src="{{ asset('images/logokotametro.png') }}" alt="Logo Kota Metro" class="cctv-preview-logo">
                    <h3 id="cctv-preview-title" class="cctv-preview-title">CCTV Preview</h3>
                </div>
                <div class="cctv-preview-controls">
                    <button class="cctv-close-btn" onclick="closePreview()">
                        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>
            </div>
            <div class="cctv-preview-content">
                <iframe id="cctv-preview-iframe" class="cctv-preview-iframe" src="" allowfullscreen></iframe>
            </div>
            <div class="cctv-preview-stats">
                <div class="cctv-view-count">
                    <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                    <span id="cctv-view-counter">0</span> views
                </div>
                <div class="cctv-status-indicator">
                    <div class="status-dot"></div>
                    Online
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="{{ asset('js/map.js') }}"></script>
    <script>
        // Initialize markers after map.js loads
        document.addEventListener('DOMContentLoaded', function() {
            // Hamburger menu functionality
            const hamburgerMenu = document.getElementById('hamburger-menu');
            const navMenu = document.getElementById('nav-menu');
            const closeSidebar = document.getElementById('close-sidebar');
            
            function toggleNavMenu() {
                hamburgerMenu.classList.toggle('active');
                navMenu.classList.toggle('open');
                document.body.classList.toggle('menu-open');
            }
            
            function closeNavMenu() {
                hamburgerMenu.classList.remove('active');
                navMenu.classList.remove('open');
                document.body.classList.remove('menu-open');
            }
            
            hamburgerMenu.addEventListener('click', toggleNavMenu);
            closeSidebar.addEventListener('click', closeNavMenu);
            
            // Close menu when clicking on backdrop
            navMenu.addEventListener('click', function(e) {
                if (e.target === navMenu) {
                    closeNavMenu();
                }
            });
            
            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && navMenu.classList.contains('open')) {
                    closeNavMenu();
                }
            });
            
            // Wait for map to be initialized
            setTimeout(() => {
                // Add CCTV markers
                @foreach($cctvs as $index => $cctv)
                    @if(strtolower($cctv->nama_lokasi ?? '') !== 'semua cctv')
                        addCCTVMarker({{ $index }}, {{ $cctv->lat }}, {{ $cctv->lang }}, '{{ $cctv->nama_lokasi ?? 'CCTV' }}', '{{ $cctv->link }}', {{ $cctv->id_cctv }}, {{ $cctv->count_seen ?? 0 }});
                    @endif
                @endforeach

                // Add Pemerintahan markers
                @foreach($pemerintahan as $unit)
                    addPemerintahanMarker({{ $loop->index }}, {{ $unit->latitude }}, {{ $unit->longitude }}, '{{ $unit->unit }}');
                @endforeach

                // Add Pendidikan markers
                @foreach($pendidikan as $edu)
                    addPendidikanMarker({{ $loop->index }}, {{ $edu->lat }}, {{ $edu->lang }}, '{{ $edu->nama_lokasi }}', '{{ $edu->alamat }}');
                @endforeach

                // Add Virtual Tour markers
                @foreach($spheres as $sphere)
                    addVirtualTourMarker({{ $loop->index }}, {{ $sphere->lat }}, {{ $sphere->lang }}, '{{ $sphere->nama_lokasi }}', '{{ $sphere->link }}');
                @endforeach

                // Add Layanan markers
                @foreach($layanan as $item)
                    addLayananMarker({{ $loop->index }}, {{ $item->lat }}, {{ $item->lang }}, '{{ $item->nama_lokasi }}', '{{ $item->alamat }}');
                @endforeach

                // Add Kesehatan markers
                @foreach($kesehatan as $item)
                    addKesehatanMarker({{ $loop->index }}, {{ $item->posyandu_latitude }}, {{ $item->posyandu_longitude }}, '{{ $item->nama_posyandu }}', '{{ $item->posisi }}', '{{ $item->balita ?? 'N/A' }}', '{{ $item->remaja ?? 'N/A' }}', '{{ $item->ibu_hamil ?? 'N/A' }}', '{{ $item->lansia ?? 'N/A' }}');
                @endforeach

                // Initialize Lucide icons for all markers
                setTimeout(() => {
                    lucide.createIcons();
                }, 200);
            }, 500);

            // Initialize audio with Laravel asset path
            if (window.backgroundAudio) {
                window.backgroundAudio.src = '{{ asset('audio/audi_kota.mp3') }}';
            }
        });
    </script>

</body>
</html>