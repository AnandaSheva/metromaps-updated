<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Debug Virtual Tour - {{ $sphere->nama_lokasi }}</title>
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
            background: #1a1a1a; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: white;
        }
        
        #panorama { 
            width: 100vw; 
            height: 60vh; 
        }

        .debug-panel {
            background: #2d2d2d;
            padding: 20px;
            height: 40vh;
            overflow-y: auto;
            border-top: 2px solid #007bff;
        }

        .debug-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #3d3d3d;
            border-radius: 5px;
        }

        .debug-section h3 {
            color: #00bfff;
            margin-bottom: 10px;
        }

        .debug-section pre {
            background: #1a1a1a;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
            font-size: 12px;
        }

        .info-panel {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 15px;
            border-radius: 8px;
            max-width: 300px;
            z-index: 1000;
            border: 1px solid #007bff;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60vh;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            color: white;
            font-size: 18px;
        }

        /* Hotspot styles */
        .pnlm-hotspot-base.hotspot-navigate {
            background: #007bff !important;
            border: 3px solid #fff !important;
            border-radius: 50% !important;
            width: 50px !important;
            height: 50px !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.3s ease !important;
        }

        .pnlm-hotspot-base.hotspot-navigate:hover {
            background: #0056b3 !important;
            transform: scale(1.2) !important;
        }

        .pnlm-hotspot-base.hotspot-navigate::before {
            content: '→' !important;
            color: white !important;
            font-size: 20px !important;
            font-weight: bold !important;
        }

        .pnlm-hotspot-base.hotspot-info {
            background: rgba(255, 255, 255, 0.95) !important;
            color: #333 !important;
            padding: 12px 16px !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            max-width: 280px !important;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.4) !important;
            border: 1px solid #ddd !important;
        }

        .error {
            color: #ff6b6b;
        }
        .success {
            color: #51cf66;
        }
        .warning {
            color: #ffd43b;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loading" class="loading-overlay">
        <div>🔄 Loading {{ $sphere->nama_lokasi }}...</div>
    </div>

    <!-- Info Panel -->
    <div class="info-panel">
        <h3>📍 {{ $sphere->nama_lokasi }}</h3>
        <p><strong>ID:</strong> {{ $sphere->id_sphere }}</p>
        <p><strong>Coordinates:</strong> {{ number_format($sphere->lat, 6) }}, {{ number_format($sphere->lang, 6) }}</p>
        <p><strong>Views:</strong> {{ number_format($sphere->count_seen) }}</p>
        <p><strong>Hotspots:</strong> {{ count($hotspotData) }}</p>
        <p><strong>Navigation:</strong> {{ count(array_filter($hotspotData, fn($h) => $h['type'] === 'navigate')) }}</p>
    </div>

    <!-- Viewer Container -->
    <div id="panorama"></div>

    <!-- Debug Panel -->
    <div class="debug-panel">
        <div class="debug-section">
            <h3>🔍 Sphere Data</h3>
            <pre>{{ json_encode($sphere, JSON_PRETTY_PRINT) }}</pre>
        </div>

        <div class="debug-section">
            <h3>🎯 Hotspot Data</h3>
            <pre>{{ json_encode($hotspotData, JSON_PRETTY_PRINT) }}</pre>
        </div>

        <div class="debug-section">
            <h3>🗺️ Navigation Spheres</h3>
            <pre>{{ json_encode($navigationSpheres, JSON_PRETTY_PRINT) }}</pre>
        </div>

        <div class="debug-section">
            <h3>📊 Console Log</h3>
            <div id="console-log" style="background: #1a1a1a; padding: 10px; border-radius: 3px; min-height: 100px;">
                <div class="success">✅ Debug mode initialized</div>
            </div>
        </div>
    </div>

    <!-- Pannellum JS -->
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <script>
        // Debug console logger
        function debugLog(message, type = 'info') {
            const logDiv = document.getElementById('console-log');
            const timestamp = new Date().toLocaleTimeString();
            const className = type === 'error' ? 'error' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : '';
            logDiv.innerHTML += `<div class="${className}">[${timestamp}] ${message}</div>`;
            logDiv.scrollTop = logDiv.scrollHeight;
            console.log(`[DEBUG] ${message}`);
        }

        // Data dari controller
        const currentSphere = @json($sphere);
        const hotspotData = @json($hotspotData);
        const navigationSpheres = @json($navigationSpheres);

        debugLog(`🎬 Initializing sphere: ${currentSphere.nama_lokasi}`, 'success');
        debugLog(`📍 Coordinates: ${currentSphere.lat}, ${currentSphere.lang}`);
        debugLog(`🎯 Total hotspots: ${hotspotData.length}`);

        // Global viewer variable
        let viewer = null;

        // Process hotspot data
        const processedHotSpots = [];
        const scenes = {};

        debugLog('🔧 Processing hotspots...', 'info');

        // Add navigation hotspots
        hotspotData.forEach((hotspot, index) => {
            if (hotspot.type === 'navigate' && navigationSpheres[hotspot.target_id]) {
                const targetSphere = navigationSpheres[hotspot.target_id];
                const yaw = hotspot.calculated_yaw || hotspot.yaw || 0;
                
                debugLog(`🧭 Navigation hotspot ${index + 1}: ${targetSphere.nama_lokasi} at yaw ${yaw}°`, 'success');
                
                processedHotSpots.push({
                    "pitch": hotspot.pitch || -10,
                    "yaw": yaw,
                    "type": "scene",
                    "text": hotspot.text || targetSphere.nama_lokasi,
                    "sceneId": `sphere_${targetSphere.id_sphere}`,
                    "cssClass": "hotspot-navigate"
                });

                // Create target scene with return hotspot
                const returnBearing = (yaw + 180) % 360;
                scenes[`sphere_${targetSphere.id_sphere}`] = {
                    "type": "equirectangular",
                    "panorama": `{{ asset('assets/sphere/') }}/${targetSphere.link.split('/').pop()}.jpg`,
                    "hotSpots": [
                        {
                            "pitch": -10,
                            "yaw": returnBearing,
                            "type": "scene",
                            "text": "🔙 Kembali ke " + currentSphere.nama_lokasi,
                            "sceneId": `sphere_${currentSphere.id_sphere}`,
                            "cssClass": "hotspot-navigate"
                        }
                    ]
                };

                debugLog(`🔄 Created return scene for ${targetSphere.nama_lokasi} with return bearing ${returnBearing}°`);
            } else if (hotspot.type === 'info') {
                debugLog(`ℹ️ Info hotspot ${index + 1}: ${hotspot.text}`, 'info');
                
                processedHotSpots.push({
                    "pitch": hotspot.pitch || 0,
                    "yaw": hotspot.yaw || 0,
                    "type": "info",
                    "text": hotspot.text || "Info",
                    "cssClass": "hotspot-info"
                });
            }
        });

        // Verify image path
        const imagePath = `{{ asset('assets/sphere/') }}/${currentSphere.link.split('/').pop()}.jpg`;
        debugLog(`🖼️ Image path: ${imagePath}`, 'info');

        // Main scene
        scenes[`sphere_${currentSphere.id_sphere}`] = {
            "type": "equirectangular",
            "panorama": imagePath,
            "autoLoad": true,
            "hotSpots": processedHotSpots
        };

        debugLog(`✅ Processed ${processedHotSpots.length} hotspots successfully`, 'success');

        // Initialize Pannellum with debug config
        try {
            debugLog('🚀 Initializing Pannellum viewer...', 'info');

            viewer = pannellum.viewer('panorama', {
                "default": {
                    "firstScene": `sphere_${currentSphere.id_sphere}`,
                    "sceneFadeDuration": 500,
                    "autoLoad": true,
                    "compass": true,
                    "showZoomCtrl": true,
                    "showFullscreenCtrl": true,
                    "showControls": true,
                    "autoRotate": 0, // STATIC CAMERA - NO AUTO ROTATION
                    "mouseZoom": true,
                    "doubleClickZoom": true,
                    "draggable": true,
                    "keyboardZoom": true,
                    "hotSpotDebug": true // Enable hotspot debug mode
                },
                "scenes": scenes
            });

            // Event listeners with debug logging
            viewer.on('load', function() {
                debugLog('✅ Sphere loaded successfully!', 'success');
                document.getElementById('loading').style.display = 'none';
                updateViewCounter();
                
                // Log current view position
                const yaw = viewer.getYaw();
                const pitch = viewer.getPitch();
                debugLog(`📷 Initial camera position: yaw=${yaw.toFixed(2)}°, pitch=${pitch.toFixed(2)}°`);
            });

            viewer.on('scenechange', function(sceneId) {
                debugLog(`🔄 Scene changed to: ${sceneId}`, 'success');
                const sphereId = sceneId.replace('sphere_', '');
                if (sphereId !== currentSphere.id_sphere.toString()) {
                    updateViewCounter(sphereId);
                }
            });

            viewer.on('error', function(error) {
                debugLog(`❌ Pannellum error: ${JSON.stringify(error)}`, 'error');
                showError('Gagal memuat panorama. Silakan periksa console untuk detail.');
            });

            viewer.on('mouseup', function() {
                const yaw = viewer.getYaw();
                const pitch = viewer.getPitch();
                debugLog(`📷 Camera moved to: yaw=${yaw.toFixed(2)}°, pitch=${pitch.toFixed(2)}°`);
            });

        } catch (error) {
            debugLog(`❌ Failed to initialize viewer: ${error.message}`, 'error');
            showError('Gagal menginisialisasi viewer: ' + error.message);
        }

        // Utility functions
        function updateViewCounter(sphereId = null) {
            const id = sphereId || currentSphere.id_sphere;
            debugLog(`📊 Updating view counter for sphere ${id}`, 'info');
            
            fetch(`/api/sphere/plus/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(response => {
                if (response.ok) {
                    debugLog('✅ View counter updated', 'success');
                } else {
                    debugLog('⚠️ Failed to update view counter', 'warning');
                }
            }).catch(error => {
                debugLog(`❌ View counter error: ${error.message}`, 'error');
            });
        }

        function showError(message) {
            debugLog(`❌ Error: ${message}`, 'error');
            document.getElementById('loading').innerHTML = `
                <div style="text-align: center; color: #ff6b6b;">
                    <div style="font-size: 24px; margin-bottom: 10px;">⚠️</div>
                    <div>${message}</div>
                    <div style="margin-top: 15px;">
                        <button onclick="location.reload()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
                            🔄 Reload
                        </button>
                    </div>
                </div>
            `;
        }

        // Image load test
        function testImageLoad() {
            const img = new Image();
            img.onload = function() {
                debugLog('✅ Sphere image loaded successfully', 'success');
            };
            img.onerror = function() {
                debugLog('❌ Failed to load sphere image', 'error');
            };
            img.src = imagePath;
        }

        // Test image loading
        testImageLoad();

        // Keyboard controls
        document.addEventListener('keydown', function(e) {
            switch(e.key) {
                case 'r':
                case 'R':
                    location.reload();
                    break;
                case 'd':
                case 'D':
                    debugLog('🔍 Manual debug triggered', 'info');
                    debugLog(`Current camera: yaw=${viewer.getYaw().toFixed(2)}°, pitch=${viewer.getPitch().toFixed(2)}°`);
                    break;
            }
        });

        debugLog('🎯 Debug mode ready! Press R to reload, D for manual debug', 'success');
    </script>
</body>
</html>
