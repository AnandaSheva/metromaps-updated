<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $sphere->nama_lokasi }} - Virtual Tour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Pannellum CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            background: #000; 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow: hidden;
        }
        
        #panorama { 
            width: 100vw; 
            height: 100vh; 
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            color: white;
            font-size: 18px;
        }
        
        .loading-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
            max-width: 600px;
            padding: 20px;
            text-align: center;
        }
        
        #loading-status {
            font-size: 16px;
            margin-top: 10px;
        }
        
        #loading-status.error {
            color: #ff6b6b;
            font-weight: bold;
        }
        
        #loading-status.success {
            color: #51cf66;
        }
        
        #loading-status.warning {
            color: #fcc419;
        }
        
        /* Info Panel - Clean & Minimal - Moved to Bottom Left */
        .info-panel {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 15px;
            border-radius: 8px;
            max-width: 280px;
            z-index: 1000;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: opacity 0.3s ease;
        }
        
        .info-panel:hover {
            opacity: 1;
        }
        
        /* Slightly transparent when not hovering for less obstruction */
        .info-panel {
            opacity: 0.8;
        }

        .info-panel h3 {
            margin: 0 0 8px 0;
            font-size: 16px;
            color: #00bfff;
            font-weight: 600;
        }

        .info-panel p {
            margin: 3px 0;
            font-size: 13px;
            opacity: 0.9;
        }

        /* Navigation Hotspots - Enhanced Interactive Design */
        .pnlm-hotspot {
            cursor: pointer !important;
        }
        
        .pnlm-hotspot-base.hotspot-navigate {
            background: linear-gradient(135deg, #007bff, #0056b3) !important;
            border: 3px solid rgba(255, 255, 255, 0.9) !important;
            border-radius: 50% !important;
            width: 60px !important;
            height: 60px !important;
            cursor: pointer !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: absolute !important;
            transform-origin: center center !important;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4), 
                        0 0 0 0 rgba(0, 123, 255, 0.7) !important;
            z-index: 100 !important;
            pointer-events: auto !important;
            animation: hotspotPulse 2s infinite !important;
        }

        .pnlm-hotspot-base.hotspot-navigate:hover {
            background: linear-gradient(135deg, #0056b3, #003d82) !important;
            transform: scale(1.2) !important;
            box-shadow: 0 6px 25px rgba(0, 123, 255, 0.6), 
                        0 0 0 10px rgba(0, 123, 255, 0.2) !important;
            border-color: #ffd700 !important;
            border-width: 4px !important;
            animation: none !important;
        }

        .pnlm-hotspot-base.hotspot-navigate:active {
            transform: scale(1.1) !important;
            transition: transform 0.1s ease !important;
        }

        .pnlm-hotspot-base.hotspot-navigate::before {
            content: '→' !important;
            color: white !important;
            font-size: 24px !important;
            font-weight: bold !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
        }

        .pnlm-hotspot-base.hotspot-navigate:hover::before {
            font-size: 26px !important;
            color: #ffd700 !important;
        }

        /* Pulse animation for navigation hotspots */
        @keyframes hotspotPulse {
            0% {
                box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4), 
                           0 0 0 0 rgba(0, 123, 255, 0.7);
            }
            50% {
                box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4), 
                           0 0 0 8px rgba(0, 123, 255, 0.2);
            }
            100% {
                box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4), 
                           0 0 0 0 rgba(0, 123, 255, 0);
            }
        }

        /* Ripple effect animation */
        @keyframes ripple {
            0% {
                transform: scale(0);
                opacity: 1;
            }
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        /* Prevent hotspot movement and ensure stable positioning */
        .pnlm-hotspot-base {
            will-change: transform !important;
            backface-visibility: hidden !important;
            transform-style: preserve-3d !important;
        }

        /* Enhanced tooltip styling */
        .hotspot-tooltip {
            position: absolute;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.95));
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            z-index: 1001;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hotspot-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: rgba(0, 0, 0, 0.9);
        }

        .custom-hotspot:hover .hotspot-tooltip {
            opacity: 1;
            transform: translateX(-50%) translateY(-5px);
        }
        }

        /* Make hotspots more prominent */
        .custom-hotspot {
            z-index: 100 !important;
            position: relative !important;
        }

        /* Info Hotspots - Clean White Cards */
        .pnlm-hotspot-base.hotspot-info {
            background: rgba(255, 255, 255, 0.95) !important;
            color: #333 !important;
            padding: 12px 16px !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            max-width: 250px !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
            backdrop-filter: blur(5px) !important;
        }

        /* Quick Actions - Minimal */
        .quick-actions {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .quick-btn {
            width: 45px;
            height: 45px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            backdrop-filter: blur(5px);
        }

        .quick-btn:hover {
            background: rgba(0, 123, 255, 0.8);
            transform: scale(1.1);
        }

        /* Error panel styles */
        .error-panel {
            background: rgba(255, 0, 0, 0.2);
            border: 1px solid rgba(255, 0, 0, 0.4);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            max-width: 600px;
            text-align: left;
        }

        .error-panel h3 {
            color: #ff6b6b;
            margin-bottom: 10px;
        }

        .tech-details {
            background: rgba(0, 0, 0, 0.5);
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            font-family: monospace;
            font-size: 14px;
            overflow-wrap: break-word;
        }
        
        details {
            margin-top: 15px;
            cursor: pointer;
        }
        
        details summary {
            font-weight: bold;
            padding: 5px 0;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .info-panel {
                bottom: 10px;
                left: 10px;
                right: auto;
                max-width: calc(100% - 20px);
                padding: 12px;
            }

            .quick-actions {
                bottom: 15px;
                right: 15px;
            }

            .quick-btn {
                width: 40px;
                height: 40px;
                font-size: 14px;
            }
        }

        /* Spinner animation for loading */
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Tombol Kembali */
        .back-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            left: auto;
            z-index: 2000;
            background: rgba(0,0,0,0.7);
            color: #fff;
            border: none;
            border-radius: 24px;
            padding: 10px 18px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: background 0.2s, transform 0.2s;
        }
        .back-btn:hover {
            background: #007bff;
            color: #fff;
            transform: scale(1.05);
        }
        .back-text {
            display: inline;
        }
        @media (max-width: 600px) {
            .back-btn {
                padding: 8px 12px;
                font-size: 14px;
                top: 10px;
                right: 10px;
                left: auto;
            }
            .back-text {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Tombol Kembali -->
    <button class="back-btn" onclick="handleBackButton()" title="Kembali">
        <span style="font-size:20px;vertical-align:middle;">&#8592;</span> <span class="back-text">Kembali</span>
    </button>
    <script>
    function handleBackButton() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/'; // Ganti '/' ke route utama/daftar sphere jika perlu
        }
    }
    </script>
    <div class="sphere-container">
        @if(isset($fileInfo) && !$fileInfo['exists'])
        <div class="error-panel">
            <h3>⚠️ Panorama file not found!</h3>
            <p>The system cannot find the panorama image file.</p>
            <details>
                <summary>Technical Details</summary>
                <div class="tech-details">
                    <p><strong>Filename:</strong> {{ $filename }}.jpg</p>
                    <p><strong>Path:</strong> {{ $fileInfo['path'] }}</p>
                    <p><strong>Full Path:</strong> {{ $fileInfo['fullPath'] }}</p>
                    @if(!empty($fileInfo['similarFiles']))
                    <p><strong>Similar Files Found:</strong></p>
                    <ul>
                        @foreach($fileInfo['similarFiles'] as $file)
                        <li>{{ $file }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </details>
        </div>
        @else
        <div id="panorama"></div>
        <div class="info-panel">
            <h3>{{ $sphere->nama_lokasi }}</h3>
            @if(isset($sphere->alamat) && !empty($sphere->alamat))
                <p>{{ $sphere->alamat }}</p>
            @endif
            <p>📍 {{ $sphere->lat }}, {{ $sphere->lang }}</p>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var panoramaUrl = @json($fileInfo['exists'] ? asset($fileInfo['path']) : null);
            if (!panoramaUrl) return;
            try {
                pannellum.viewer('panorama', {
                    type: 'equirectangular',
                    panorama: panoramaUrl,
                    autoLoad: true,
                    showControls: true,
                    showZoomCtrl: true,
                    showFullscreenCtrl: true,
                    mouseZoom: true,
                    draggable: true,
                    compass: true,
                    northOffset: {{ $sphere->north_offset ?? 0 }},
                    pitch: 0,
                    yaw: 0,
                    hfov: 100,
                    minHfov: 50,
                    maxHfov: 120
                });
            } catch (e) {
                alert('Gagal memuat panorama: ' + e.message);
            }
        });
        </script>
        @endif
    </div>
</body>
</html>