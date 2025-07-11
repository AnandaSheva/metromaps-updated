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
            transform: rotate(45deg) translate(7px, 7px);
            background: #3b82f6;
        }
        
        .hamburger-menu.active span:nth-child(2) {
            opacity: 0;
            transform: scale(0.2);
        }
        
        .hamburger-menu.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
            background: #3b82f6;
        }
        
        .hamburger-menu.active {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        /* Mobile nav menu styles - tetap menggunakan CSS untuk kompleksitas yang tinggi */
        @media (max-width: 1024px) {
            .nav-menu:not(.header-content > .nav-menu) {
                position: fixed;
                top: 0;
                right: -100%;
                width: 320px;
                height: 100vh;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                flex-direction: column;
                padding: 80px 20px 20px;
                gap: 0;
                box-shadow: -5px 0 25px rgba(0, 0, 0, 0.15);
                border-left: 1px solid rgba(0, 0, 0, 0.1);
                z-index: 2010;
                transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                overflow-y: auto;
                justify-content: flex-start;
                align-items: stretch;
            }
            
            .nav-menu.open:not(.header-content > .nav-menu) {
                right: 0;
            }
            
            .mobile-nav-header {
                display: flex !important;
                align-items: center;
                gap: 12px;
                padding: 0 0 30px 0;
                margin-bottom: 20px;
                border-bottom: 2px solid #e5e7eb;
            }
            
            .mobile-nav-logo {
                width: 32px;
                height: 32px;
                object-fit: contain;
            }
            
            .mobile-nav-title {
                font-family: 'Montserrat', sans-serif;
                font-weight: 800;
                font-size: 18px;
                color: #374151;
                margin: 0;
            }
            
            .nav-menu:not(.header-content > .nav-menu) .nav-item,
            .nav-menu:not(.header-content > .nav-menu) .nav-item-audio {
                width: 100%;
                padding: 16px 20px;
                margin: 0 0 8px 0;
                border-radius: 12px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                transition: all 0.3s ease;
                font-size: 16px;
                font-weight: 600;
                color: #475569;
                justify-content: space-between;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }
            
            .nav-menu:not(.header-content > .nav-menu) .nav-item:hover,
            .nav-menu:not(.header-content > .nav-menu) .nav-item-audio:hover {
                background: #e0f2fe;
                border-color: #0891b2;
                color: #0c4a6e;
                transform: translateX(4px);
                box-shadow: 0 4px 12px rgba(8, 145, 178, 0.15);
            }
            
            .nav-menu:not(.header-content > .nav-menu) .nav-item .nav-item-text,
            .nav-menu:not(.header-content > .nav-menu) .nav-item-audio .nav-item-text {
                flex: 1;
                text-align: left;
            }
            
            .nav-menu:not(.header-content > .nav-menu) .nav-item .nav-item-icon,
            .nav-menu:not(.header-content > .nav-menu) .nav-item-audio .nav-item-icon {
                width: 20px;
                height: 20px;
                flex-shrink: 0;
            }
            
            .nav-menu .nav-item::before,
            .nav-menu .nav-item-audio::before {
                display: none;
            }
        }
        
        @media (min-width: 1025px) {
            .hamburger-menu {
                display: none !important;
            }
            
            .mobile-nav-header {
                display: none !important;
            }
        }
    </style>

</head>

<body>
    
    <!-- Header with Tailwind CSS classes -->
    <div class="relative w-full h-20 md:h-[70px] overflow-hidden z-[1000] shadow-[0px_1px_4px_rgba(0,0,0,0.25)]" style="background: url('{{ asset('images/navbar-pattern.png') }}') center/cover no-repeat;">
        <!-- Background Pattern 1 -->
        <div class="absolute w-[960px] h-[135px] -left-[400px] -top-[18px] opacity-5 rotate-90 transform-origin-top-left z-0 md:hidden" style="background: url('{{ asset('images/header.jpg') }}'); background-size: cover; background-position: center; transform: rotate(-90deg);"></div>
        <!-- Background Pattern 2 -->
        <div class="absolute w-[953px] h-[134px] left-[560px] -top-[18px] opacity-5 rotate-90 transform-origin-top-left z-0 md:hidden" style="background: url('{{ asset('images/header.jpg') }}'); background-size: cover; background-position: center; transform: rotate(-90deg);"></div>

        <!-- Header Content -->
        <div class="relative max-w-screen-2xl w-full h-full mx-auto flex items-center justify-between px-[45px] z-[1] 
                    2xl:px-5 xl:px-5 lg:px-5 md:px-[15px] md:justify-between sm:px-[15px] sm:justify-between">
            
            <!-- Logo Section -->
            <div class="flex items-center gap-[15px] 2xl:gap-[10px] xl:gap-[10px] lg:gap-[10px] md:gap-2 sm:gap-2">
                <img src="{{ asset('images/komdigi.png') }}" alt="Logo Kiri" 
                     class="w-[30px] h-[30px] object-contain 
                            2xl:w-[26px] 2xl:h-[26px] xl:w-[26px] xl:h-[26px] lg:w-[26px] lg:h-[26px] 
                            md:w-6 md:h-6 sm:w-6 sm:h-6">
                <div class="w-[3px] h-5 bg-[#D9D9D9] rounded-[32px]"></div>
                <img src="{{ asset('images/logokotametro.png') }}" alt="Logo Kota Metro" 
                     class="w-[30px] h-10 object-contain 
                            2xl:w-[26px] 2xl:h-[34px] xl:w-[26px] xl:h-[34px] lg:w-[26px] lg:h-[34px] 
                            md:w-6 md:h-[30px] sm:w-6 sm:h-[30px]">
                <div class="flex flex-col justify-center h-[60px] 2xl:h-11 xl:h-11 lg:h-11 md:h-10 sm:h-10">
                    <div class="font-[Montserrat] font-extrabold text-base leading-[1.2] tracking-[0.02em] text-[#464646] whitespace-nowrap
                               2xl:text-[11px] xl:text-[11px] lg:text-[11px] md:text-[10px] sm:text-[10px]">
                        PEMERINTAH KOTA METRO
                    </div>
                    <div class="font-[Inter] font-extrabold text-[9px] leading-[1.2] tracking-[0.01em] text-[#464646] whitespace-nowrap
                               2xl:text-[7px] xl:text-[7px] lg:text-[7px] md:text-[6px] sm:text-[6px]">
                        CCTV PINTAR KOTA BERBASIS KEWILAYAHAN
                    </div>
                </div>
            </div>

            <!-- Desktop Navigation Menu -->
            <div class="nav-menu flex items-center gap-5 h-[29px] 
                        static !important w-auto !important h-auto !important bg-transparent !important 
                        flex-row !important p-0 !important m-0 !important shadow-none !important 
                        border-none !important z-auto !important transform-none !important 
                        opacity-100 !important pointer-events-auto !important transition-none !important 
                        overflow-visible !important
                        2xl:gap-5 xl:gap-[15px] lg:gap-[15px] md:hidden sm:hidden" id="nav-menu">
                
                <!-- Mobile Header - only visible in responsive mode -->
                <div class="mobile-nav-header hidden">
                    <img src="{{ asset('images/komdigi.png') }}" alt="Logo Komdigi" class="mobile-nav-logo">
                    <h3 class="mobile-nav-title">Metro Maps</h3>
                </div>
                
                <a href="#" class="nav-item no-underline text-[#666666] font-[Inter] font-semibold text-sm leading-6 tracking-[-0.02em] 
                           flex items-center gap-[5px] whitespace-nowrap relative pb-[5px] transition-colors duration-300 ease-in-out
                           hover:text-black 2xl:text-[13px] xl:text-[13px] lg:text-[13px] md:text-xs sm:text-xs
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-[90%] before:h-0.5 before:bg-black before:rounded-[1px] before:transition-transform before:duration-[400ms] before:ease-[cubic-bezier(0.4,0,0.2,1)] before:origin-center
                           hover:before:scale-x-100 active:text-black active:before:scale-x-100 active:before:bg-black" 
                           id="cctv-nav" onclick="showOnly('cctv')">
                    <span class="nav-item-text">CCTV</span>
                    <i data-lucide="cctv" class="nav-item-icon w-4 h-4 2xl:w-[14px] 2xl:h-[14px] xl:w-[14px] xl:h-[14px] lg:w-[14px] lg:h-[14px] md:w-3 md:h-3 sm:w-3 sm:h-3"></i>
                </a>
                
                <a href="#" class="nav-item no-underline text-[#666666] font-[Inter] font-semibold text-sm leading-6 tracking-[-0.02em] 
                           flex items-center gap-[5px] whitespace-nowrap relative pb-[5px] transition-colors duration-300 ease-in-out
                           hover:text-black 2xl:text-[13px] xl:text-[13px] lg:text-[13px] md:text-xs sm:text-xs
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-[90%] before:h-0.5 before:bg-black before:rounded-[1px] before:transition-transform before:duration-[400ms] before:ease-[cubic-bezier(0.4,0,0.2,1)] before:origin-center
                           hover:before:scale-x-100 active:text-black active:before:scale-x-100 active:before:bg-black" 
                           id="virtual-tour-nav" onclick="showOnly('virtual')">
                    <span class="nav-item-text">Virtual Tour</span>
                    <i data-lucide="binoculars" class="nav-item-icon w-4 h-4 2xl:w-[14px] 2xl:h-[14px] xl:w-[14px] xl:h-[14px] lg:w-[14px] lg:h-[14px] md:w-3 md:h-3 sm:w-3 sm:h-3"></i>
                </a>
                
                <a href="#" class="nav-item no-underline text-[#666666] font-[Inter] font-semibold text-sm leading-6 tracking-[-0.02em] 
                           flex items-center gap-[5px] whitespace-nowrap relative pb-[5px] transition-colors duration-300 ease-in-out
                           hover:text-black 2xl:text-[13px] xl:text-[13px] lg:text-[13px] md:text-xs sm:text-xs
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-[90%] before:h-0.5 before:bg-black before:rounded-[1px] before:transition-transform before:duration-[400ms] before:ease-[cubic-bezier(0.4,0,0.2,1)] before:origin-center
                           hover:before:scale-x-100 active:text-black active:before:scale-x-100 active:before:bg-black" 
                           id="pemerintahan-nav" onclick="showOnly('pemerintahan')">
                    <span class="nav-item-text">Pemerintahan</span>
                    <i data-lucide="building-2" class="nav-item-icon w-4 h-4 2xl:w-[14px] 2xl:h-[14px] xl:w-[14px] xl:h-[14px] lg:w-[14px] lg:h-[14px] md:w-3 md:h-3 sm:w-3 sm:h-3"></i>
                </a>
                
                <a href="#" class="nav-item no-underline text-[#666666] font-[Inter] font-semibold text-sm leading-6 tracking-[-0.02em] 
                           flex items-center gap-[5px] whitespace-nowrap relative pb-[5px] transition-colors duration-300 ease-in-out
                           hover:text-black 2xl:text-[13px] xl:text-[13px] lg:text-[13px] md:text-xs sm:text-xs
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-[90%] before:h-0.5 before:bg-black before:rounded-[1px] before:transition-transform before:duration-[400ms] before:ease-[cubic-bezier(0.4,0,0.2,1)] before:origin-center
                           hover:before:scale-x-100 active:text-black active:before:scale-x-100 active:before:bg-black" 
                           id="pendidikan-nav" onclick="showOnly('pendidikan')">
                    <span class="nav-item-text">Pendidikan</span>
                    <i data-lucide="university" class="nav-item-icon w-4 h-4 2xl:w-[14px] 2xl:h-[14px] xl:w-[14px] xl:h-[14px] lg:w-[14px] lg:h-[14px] md:w-3 md:h-3 sm:w-3 sm:h-3"></i>
                </a>
                
                <a href="#" class="nav-item no-underline text-[#666666] font-[Inter] font-semibold text-sm leading-6 tracking-[-0.02em] 
                           flex items-center gap-[5px] whitespace-nowrap relative pb-[5px] transition-colors duration-300 ease-in-out
                           hover:text-black 2xl:text-[13px] xl:text-[13px] lg:text-[13px] md:text-xs sm:text-xs
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-[90%] before:h-0.5 before:bg-black before:rounded-[1px] before:transition-transform before:duration-[400ms] before:ease-[cubic-bezier(0.4,0,0.2,1)] before:origin-center
                           hover:before:scale-x-100 active:text-black active:before:scale-x-100 active:before:bg-black" 
                           id="kesehatan-nav" onclick="showOnly('kesehatan')">
                    <span class="nav-item-text">Kesehatan</span>
                    <i data-lucide="hospital" class="nav-item-icon w-4 h-4 2xl:w-[14px] 2xl:h-[14px] xl:w-[14px] xl:h-[14px] lg:w-[14px] lg:h-[14px] md:w-3 md:h-3 sm:w-3 sm:h-3"></i>
                </a>
                
                <a href="#" class="nav-item no-underline text-[#666666] font-[Inter] font-semibold text-sm leading-6 tracking-[-0.02em] 
                           flex items-center gap-[5px] whitespace-nowrap relative pb-[5px] transition-colors duration-300 ease-in-out
                           hover:text-black 2xl:text-[13px] xl:text-[13px] lg:text-[13px] md:text-xs sm:text-xs
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-[90%] before:h-0.5 before:bg-black before:rounded-[1px] before:transition-transform before:duration-[400ms] before:ease-[cubic-bezier(0.4,0,0.2,1)] before:origin-center
                           hover:before:scale-x-100 active:text-black active:before:scale-x-100 active:before:bg-black" 
                           id="layanan-nav" onclick="showOnly('layanan')">
                    <span class="nav-item-text">Layanan Publik</span>
                    <i data-lucide="users" class="nav-item-icon w-4 h-4 2xl:w-[14px] 2xl:h-[14px] xl:w-[14px] xl:h-[14px] lg:w-[14px] lg:h-[14px] md:w-3 md:h-3 sm:w-3 sm:h-3"></i>
                </a>
                
                <a href="#" class="nav-item no-underline text-[#666666] font-[Inter] font-semibold text-sm leading-6 tracking-[-0.02em] 
                           flex items-center gap-[5px] whitespace-nowrap relative pb-[5px] transition-colors duration-300 ease-in-out
                           hover:text-black 2xl:text-[13px] xl:text-[13px] lg:text-[13px] md:text-xs sm:text-xs
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-[90%] before:h-0.5 before:bg-black before:rounded-[1px] before:transition-transform before:duration-[400ms] before:ease-[cubic-bezier(0.4,0,0.2,1)] before:origin-center
                           hover:before:scale-x-100 active:text-black active:before:scale-x-100 active:before:bg-black" 
                           id="pangan-nav" onclick="showOnly('pangan')">
                    <span class="nav-item-text">Pangan</span>
                    <i data-lucide="wheat" class="nav-item-icon w-4 h-4 2xl:w-[14px] 2xl:h-[14px] xl:w-[14px] xl:h-[14px] lg:w-[14px] lg:h-[14px] md:w-3 md:h-3 sm:w-3 sm:h-3"></i>
                </a>
                
                <a href="#" class="nav-item-audio no-underline text-[#666666] font-[Inter] font-semibold text-sm leading-6 tracking-[-0.02em] 
                           flex items-center gap-[5px] whitespace-nowrap relative pb-[5px] transition-colors duration-300 ease-in-out
                           hover:text-black 2xl:text-[13px] xl:text-[13px] lg:text-[13px] md:text-xs sm:text-xs
                           before:content-[''] before:absolute before:bottom-0 before:left-1/2 before:transform before:-translate-x-1/2 before:scale-x-0 
                           before:w-[90%] before:h-0.5 before:bg-black before:rounded-[1px] before:transition-transform before:duration-[400ms] before:ease-[cubic-bezier(0.4,0,0.2,1)] before:origin-center
                           hover:before:scale-x-100 active:text-black active:before:scale-x-100 active:before:bg-black" 
                           id="audio-toggle-link">
                    <span class="nav-item-text"></span>
                    <i data-lucide="volume-off" class="nav-item-icon w-4 h-4 2xl:w-[14px] 2xl:h-[14px] xl:w-[14px] xl:h-[14px] lg:w-[14px] lg:h-[14px] md:w-3 md:h-3 sm:w-3 sm:h-3" id="audio-toggle-icon"></i>
                </a>
            </div>
            
            <!-- Hamburger Menu Button -->
            <button class="hamburger-menu hidden flex-col justify-center items-center w-[42px] h-[42px] cursor-pointer z-[2015] 
                           bg-white/95 border border-black/10 rounded-lg ml-5 transition-all duration-200 ease-in-out 
                           shadow-[0_2px_8px_rgba(0,0,0,0.1)] relative
                           hover:bg-blue-500/15 hover:border-blue-500/30 hover:scale-105 hover:shadow-[0_4px_12px_rgba(59,130,246,0.2)]
                           active:bg-blue-500/10 active:border-blue-500/30
                           2xl:flex xl:flex lg:flex md:flex sm:flex" 
                           id="hamburger-menu" aria-label="Toggle navigation">
                <span class="block w-6 h-[3px] bg-gray-700 rounded-[2px] transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] my-[3px] origin-center 
                             hamburger-menu.active:rotate-45 hamburger-menu.active:translate-x-[7px] hamburger-menu.active:translate-y-[7px] hamburger-menu.active:bg-blue-500"></span>
                <span class="block w-6 h-[3px] bg-gray-700 rounded-[2px] transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] my-[3px] origin-center 
                             hamburger-menu.active:opacity-0 hamburger-menu.active:scale-[0.2]"></span>
                <span class="block w-6 h-[3px] bg-gray-700 rounded-[2px] transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] my-[3px] origin-center 
                             hamburger-menu.active:-rotate-45 hamburger-menu.active:translate-x-[7px] hamburger-menu.active:-translate-y-[7px] hamburger-menu.active:bg-blue-500"></span>
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