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
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Map CSS -->
    <link rel="stylesheet" href="{{ asset('css/map.css') }}" />
    
    <!-- Custom CSS for navbar transitions and pseudo-elements -->
    <style>
        /* Custom CSS untuk efek yang tidak bisa diimplementasi dengan Tailwind */
        .nav-item.active {
            color: #000 !important;
        }
        
        .nav-item.active::before {
            transform: translateX(-50%) scaleX(1) !important;
            background: #000000 !important;
        }
        
        .nav-item-audio.active {
            color: #000 !important;
        }
        
        .nav-item-audio.active::before {
            transform: translateX(-50%) scaleX(1) !important;
            background: #000000 !important;
        }
        
        /* Hamburger menu active states */
        .hamburger-menu.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
            background: #3b82f6;
        }
        
        .hamburger-menu.active span:nth-child(2) {
            opacity: 0;
            transform: scale(0.2);
        }
        
        .hamburger-menu.active span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
            background: #3b82f6;
        }
        
        .hamburger-menu.active {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        /* Mobile nav menu styles - konversi ke Tailwind dengan CSS custom untuk pseudo-elements kompleks */
        .nav-menu.open {
            right: 0 !important;
        }
        
        /* Backdrop overlay untuk mobile sidebar */
        .nav-menu::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: -1;
        }
        
        .nav-menu.open::before {
            opacity: 1;
            visibility: visible;
        }
        
        @media (min-width: 1024px) {
            .nav-menu::before {
                display: none;
            }
        }
        
        /* Safe area dan overflow fixes */
        @media (max-width: 640px) {
            .logo-section {
                max-width: 60vw;
                overflow: hidden;
            }
            
            .logo-section .flex-col div {
                font-size: clamp(8px, 2vw, 10px) !important;
                line-height: 1.1 !important;
            }
        }
    </style>

</head>

<body>
    
    <!-- Header with Tailwind CSS classes -->
    <div class="relative w-full h-20 md:h-[70px] overflow-hidden z-[1000] shadow-[0px_1px_4px_rgba(0,0,0,0.25)]" 
         style="background: url('{{ asset('images/navbar-pattern.png') }}') center/cover no-repeat;">
        <!-- Background Pattern 1 -->
        <div class="absolute w-[960px] h-[135px] -left-[400px] -top-[18px] opacity-5 -rotate-90 origin-top-left z-0 lg:hidden" 
             style="background: url('{{ asset('images/header.jpg') }}') center/cover no-repeat;"></div>
        <!-- Background Pattern 2 -->
        <div class="absolute w-[953px] h-[134px] left-[560px] -top-[18px] opacity-5 -rotate-90 origin-top-left z-0 lg:hidden" 
             style="background: url('{{ asset('images/header.jpg') }}') center/cover no-repeat;"></div>

        <!-- Header Content -->
        <div class="relative w-full h-full mx-auto flex items-center justify-between px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-16 z-[1]">
            
            <!-- Logo Section -->
            <div class="logo-section flex items-center gap-2 sm:gap-3 lg:gap-4 flex-shrink-0 min-w-0">
                <img src="{{ asset('images/komdigi.png') }}" alt="Logo Kiri" 
                     class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8 xl:w-[30px] xl:h-[30px] object-contain flex-shrink-0">
                <div class="w-[2px] lg:w-[3px] h-3 sm:h-4 lg:h-5 bg-[#D9D9D9] rounded-full flex-shrink-0"></div>
                <img src="{{ asset('images/logokotametro.png') }}" alt="Logo Kota Metro" 
                     class="w-6 h-7 sm:w-7 sm:h-8 lg:w-8 lg:h-9 xl:w-[30px] xl:h-10 object-contain flex-shrink-0">
                <div class="flex flex-col justify-center ml-1 sm:ml-2 lg:ml-3 min-w-0">
                    <div class="font-[Montserrat] font-extrabold text-[10px] sm:text-xs lg:text-sm xl:text-base leading-tight tracking-wide text-[#464646] whitespace-nowrap">
                        PEMERINTAH KOTA METRO
                    </div>
                    <div class="font-[Inter] font-extrabold text-[6px] sm:text-[7px] lg:text-[8px] xl:text-[9px] leading-tight tracking-wide text-[#464646] whitespace-nowrap">
                        CCTV PINTAR KOTA BERBASIS KEWILAYAHAN
                    </div>
                </div>
            </div>

            <!-- Desktop Navigation Menu -->
            <nav class="nav-menu hidden lg:flex lg:items-center lg:gap-3 xl:gap-4 2xl:gap-5 lg:flex-shrink-0 
                        fixed lg:static top-0 right-[-100%] w-72 sm:w-80 lg:w-auto max-w-[85vw] lg:max-w-none 
                        h-screen lg:h-auto bg-white/95 lg:bg-transparent 
                        backdrop-blur-lg lg:backdrop-blur-none 
                        flex-col lg:flex-row pt-0 lg:pt-0 px-0 lg:px-0 pb-0 lg:pb-0 
                        gap-0 lg:gap-3 xl:gap-4 2xl:gap-5 
                        shadow-[-8px_0_32px_rgba(0,0,0,0.12)] lg:shadow-none 
                        border-l border-gray-200 lg:border-l-0 
                        z-[2010] lg:z-auto 
                        transition-[right] duration-300 ease-out lg:transition-none 
                        overflow-y-auto lg:overflow-visible 
                        justify-start lg:justify-center 
                        items-stretch lg:items-center" id="nav-menu">
                
                <!-- Mobile Header with Close Button -->
                <div class="mobile-nav-header flex lg:hidden items-center justify-between p-4 border-b border-gray-200 bg-white">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/komdigi.png') }}" alt="Logo Komdigi" class="w-7 h-7 object-contain">
                        <h3 class="font-[Montserrat] font-bold text-base text-gray-800 m-0">Metro Maps</h3>
                    </div>
                    <button class="close-sidebar-btn p-1.5 rounded-lg hover:bg-gray-100 transition-colors duration-200" id="close-sidebar">
                        <i data-lucide="x" class="w-5 h-5 text-gray-600"></i>
                    </button>
                </div>
                
                <!-- Mobile Menu Items Container -->
                <div class="mobile-menu-container flex flex-col p-4 gap-2 lg:hidden">
                    <!-- Menu items akan diisi di sini -->
                </div>
                
                
                <a href="#" class="nav-item group no-underline text-gray-600 font-[Inter] font-medium text-sm xl:text-base 
                           leading-relaxed flex items-center gap-2 whitespace-nowrap relative pb-1 
                           transition-all duration-200 ease-out hover:text-gray-900
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-full before:h-0.5 before:bg-blue-500 before:rounded-full before:transition-transform before:duration-300 before:ease-out before:origin-center
                           hover:before:scale-x-100
                           lg:w-auto lg:p-0 lg:m-0 lg:rounded-none lg:bg-transparent lg:border-0 lg:shadow-none lg:justify-start
                           w-full py-3 px-4 mb-1 rounded-lg bg-white border border-gray-100 text-gray-700 shadow-sm justify-between
                           hover:lg:text-gray-900 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 hover:shadow-md" 
                           id="cctv-nav" onclick="showOnly('cctv')">
                    <span class="nav-item-text flex-1 lg:flex-none text-left lg:text-center">CCTV</span>
                    <i data-lucide="cctv" class="nav-item-icon w-4 h-4 lg:w-4 lg:h-4 xl:w-5 xl:h-5 flex-shrink-0"></i>
                </a>
                
                <a href="#" class="nav-item group no-underline text-gray-600 font-[Inter] font-medium text-sm xl:text-base 
                           leading-relaxed flex items-center gap-2 whitespace-nowrap relative pb-1 
                           transition-all duration-200 ease-out hover:text-gray-900
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-full before:h-0.5 before:bg-blue-500 before:rounded-full before:transition-transform before:duration-300 before:ease-out before:origin-center
                           hover:before:scale-x-100
                           lg:w-auto lg:p-0 lg:m-0 lg:rounded-none lg:bg-transparent lg:border-0 lg:shadow-none lg:justify-start
                           w-full py-3 px-4 mb-1 rounded-lg bg-white border border-gray-100 text-gray-700 shadow-sm justify-between
                           hover:lg:text-gray-900 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 hover:shadow-md" 
                           id="virtual-tour-nav" onclick="showOnly('virtual')">
                    <span class="nav-item-text flex-1 lg:flex-none text-left lg:text-center">Virtual Tour</span>
                    <i data-lucide="binoculars" class="nav-item-icon w-4 h-4 lg:w-4 lg:h-4 xl:w-5 xl:h-5 flex-shrink-0"></i>
                </a>
                
                <a href="#" class="nav-item group no-underline text-gray-600 font-[Inter] font-medium text-sm xl:text-base 
                           leading-relaxed flex items-center gap-2 whitespace-nowrap relative pb-1 
                           transition-all duration-200 ease-out hover:text-gray-900
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-full before:h-0.5 before:bg-blue-500 before:rounded-full before:transition-transform before:duration-300 before:ease-out before:origin-center
                           hover:before:scale-x-100
                           lg:w-auto lg:p-0 lg:m-0 lg:rounded-none lg:bg-transparent lg:border-0 lg:shadow-none lg:justify-start
                           w-full py-3 px-4 mb-1 rounded-lg bg-white border border-gray-100 text-gray-700 shadow-sm justify-between
                           hover:lg:text-gray-900 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 hover:shadow-md" 
                           id="pemerintahan-nav" onclick="showOnly('pemerintahan')">
                    <span class="nav-item-text flex-1 lg:flex-none text-left lg:text-center">Pemerintahan</span>
                    <i data-lucide="building-2" class="nav-item-icon w-4 h-4 lg:w-4 lg:h-4 xl:w-5 xl:h-5 flex-shrink-0"></i>
                </a>
                
                <a href="#" class="nav-item group no-underline text-gray-600 font-[Inter] font-medium text-sm xl:text-base 
                           leading-relaxed flex items-center gap-2 whitespace-nowrap relative pb-1 
                           transition-all duration-200 ease-out hover:text-gray-900
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-full before:h-0.5 before:bg-blue-500 before:rounded-full before:transition-transform before:duration-300 before:ease-out before:origin-center
                           hover:before:scale-x-100
                           lg:w-auto lg:p-0 lg:m-0 lg:rounded-none lg:bg-transparent lg:border-0 lg:shadow-none lg:justify-start
                           w-full py-3 px-4 mb-1 rounded-lg bg-white border border-gray-100 text-gray-700 shadow-sm justify-between
                           hover:lg:text-gray-900 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 hover:shadow-md" 
                           id="pendidikan-nav" onclick="showOnly('pendidikan')">
                    <span class="nav-item-text flex-1 lg:flex-none text-left lg:text-center">Pendidikan</span>
                    <i data-lucide="university" class="nav-item-icon w-4 h-4 lg:w-4 lg:h-4 xl:w-5 xl:h-5 flex-shrink-0"></i>
                </a>
                
                <a href="#" class="nav-item group no-underline text-gray-600 font-[Inter] font-medium text-sm xl:text-base 
                           leading-relaxed flex items-center gap-2 whitespace-nowrap relative pb-1 
                           transition-all duration-200 ease-out hover:text-gray-900
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-full before:h-0.5 before:bg-blue-500 before:rounded-full before:transition-transform before:duration-300 before:ease-out before:origin-center
                           hover:before:scale-x-100
                           lg:w-auto lg:p-0 lg:m-0 lg:rounded-none lg:bg-transparent lg:border-0 lg:shadow-none lg:justify-start
                           w-full py-3 px-4 mb-1 rounded-lg bg-white border border-gray-100 text-gray-700 shadow-sm justify-between
                           hover:lg:text-gray-900 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 hover:shadow-md" 
                           id="kesehatan-nav" onclick="showOnly('kesehatan')">
                    <span class="nav-item-text flex-1 lg:flex-none text-left lg:text-center">Kesehatan</span>
                    <i data-lucide="hospital" class="nav-item-icon w-4 h-4 lg:w-4 lg:h-4 xl:w-5 xl:h-5 flex-shrink-0"></i>
                </a>
                
                <a href="#" class="nav-item group no-underline text-gray-600 font-[Inter] font-medium text-sm xl:text-base 
                           leading-relaxed flex items-center gap-2 whitespace-nowrap relative pb-1 
                           transition-all duration-200 ease-out hover:text-gray-900
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-full before:h-0.5 before:bg-blue-500 before:rounded-full before:transition-transform before:duration-300 before:ease-out before:origin-center
                           hover:before:scale-x-100
                           lg:w-auto lg:p-0 lg:m-0 lg:rounded-none lg:bg-transparent lg:border-0 lg:shadow-none lg:justify-start
                           w-full py-3 px-4 mb-1 rounded-lg bg-white border border-gray-100 text-gray-700 shadow-sm justify-between
                           hover:lg:text-gray-900 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 hover:shadow-md" 
                           id="layanan-nav" onclick="showOnly('layanan')">
                    <span class="nav-item-text flex-1 lg:flex-none text-left lg:text-center">Layanan Publik</span>
                    <i data-lucide="users" class="nav-item-icon w-4 h-4 lg:w-4 lg:h-4 xl:w-5 xl:h-5 flex-shrink-0"></i>
                </a>
                
                <a href="#" class="nav-item group no-underline text-gray-600 font-[Inter] font-medium text-sm xl:text-base 
                           leading-relaxed flex items-center gap-2 whitespace-nowrap relative pb-1 
                           transition-all duration-200 ease-out hover:text-gray-900
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-full before:h-0.5 before:bg-blue-500 before:rounded-full before:transition-transform before:duration-300 before:ease-out before:origin-center
                           hover:before:scale-x-100
                           lg:w-auto lg:p-0 lg:m-0 lg:rounded-none lg:bg-transparent lg:border-0 lg:shadow-none lg:justify-start
                           w-full py-3 px-4 mb-1 rounded-lg bg-white border border-gray-100 text-gray-700 shadow-sm justify-between
                           hover:lg:text-gray-900 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 hover:shadow-md" 
                           id="pangan-nav" onclick="showOnly('pangan')">
                    <span class="nav-item-text flex-1 lg:flex-none text-left lg:text-center">Pangan</span>
                    <i data-lucide="wheat" class="nav-item-icon w-4 h-4 lg:w-4 lg:h-4 xl:w-5 xl:h-5 flex-shrink-0"></i>
                </a>
                
                <a href="#" class="nav-item-audio group no-underline text-gray-600 font-[Inter] font-medium text-sm xl:text-base 
                           leading-relaxed flex items-center gap-2 whitespace-nowrap relative pb-1 
                           transition-all duration-200 ease-out hover:text-gray-900
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-full before:h-0.5 before:bg-blue-500 before:rounded-full before:transition-transform before:duration-300 before:ease-out before:origin-center
                           hover:before:scale-x-100
                           lg:w-auto lg:p-0 lg:m-0 lg:rounded-none lg:bg-transparent lg:border-0 lg:shadow-none lg:justify-start
                           w-full py-3 px-4 mb-1 rounded-lg bg-white border border-gray-100 text-gray-700 shadow-sm justify-between
                           hover:lg:text-gray-900 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 hover:shadow-md" 
                           id="audio-toggle-link">
                    <span class="nav-item-text sr-only lg:not-sr-only flex-1 lg:flex-none text-left lg:text-center">Audio</span>
                    <i data-lucide="volume-off" class="nav-item-icon w-4 h-4 lg:w-4 lg:h-4 xl:w-5 xl:h-5 flex-shrink-0" id="audio-toggle-icon"></i>
                </a>
            </nav>
            
            <!-- Hamburger Menu Button -->
            <button class="hamburger-menu lg:hidden flex flex-col justify-center items-center w-9 h-9 sm:w-10 sm:h-10 cursor-pointer z-[2015] 
                           bg-white/95 border border-black/10 rounded-lg transition-all duration-200 ease-in-out 
                           shadow-[0_2px_8px_rgba(0,0,0,0.1)] relative flex-shrink-0
                           hover:bg-blue-500/15 hover:border-blue-500/30 hover:scale-105 hover:shadow-[0_4px_12px_rgba(59,130,246,0.2)]
                           active:bg-blue-500/10 active:border-blue-500/30" 
                           id="hamburger-menu" aria-label="Toggle navigation">
                <span class="block w-4 h-[2px] sm:w-5 sm:h-[2px] bg-gray-700 rounded-[1px] transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] my-[2px] origin-center"></span>
                <span class="block w-4 h-[2px] sm:w-5 sm:h-[2px] bg-gray-700 rounded-[1px] transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] my-[2px] origin-center"></span>
                <span class="block w-4 h-[2px] sm:w-5 sm:h-[2px] bg-gray-700 rounded-[1px] transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] my-[2px] origin-center"></span>
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