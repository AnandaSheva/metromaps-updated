<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotspot Test - {{ $sphere->nama_lokasi }}</title>
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
            height: 70vh; 
        }

        .test-panel {
            background: #2d2d2d;
            padding: 20px;
            height: 30vh;
            overflow-y: auto;
            border-top: 2px solid #00ff00;
        }

        .test-section {
            margin-bottom: 15px;
            padding: 10px;
            background: #3d3d3d;
            border-radius: 5px;
            border-left: 3px solid #00ff00;
        }

        .test-section h4 {
            color: #00ff00;
            margin-bottom: 8px;
        }

        .hotspot-info {
            background: #1a1a1a;
            padding: 8px;
            margin: 5px 0;
            border-radius: 3px;
            font-size: 12px;
        }

        .status-good { border-left: 3px solid #00ff00; }
        .status-warning { border-left: 3px solid #ffaa00; }
        .status-error { border-left: 3px solid #ff3333; }

        /* Enhanced Hotspot Styles for Testing */
        .pnlm-hotspot-base.hotspot-navigate {
            background: #00ff00 !important;
            border: 3px solid #ffffff !important;
            border-radius: 50% !important;
            width: 60px !important;
            height: 60px !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
            position: absolute !important;
            z-index: 100 !important;
            pointer-events: auto !important;
            box-shadow: 0 4px 15px rgba(0, 255, 0, 0.5) !important;
        }

        .pnlm-hotspot-base.hotspot-navigate:hover {
            background: #ffaa00 !important;
            transform: scale(1.2) !important;
            box-shadow: 0 6px 25px rgba(255, 170, 0, 0.7) !important;
        }

        .pnlm-hotspot-base.hotspot-navigate::before {
            content: '🎯' !important;
            font-size: 24px !important;
        }

        .info-panel {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 15px;
            border-radius: 8px;
            max-width: 350px;
            z-index: 1000;
            border: 2px solid #00ff00;
        }
    </style>
</head>
<body>
    <!-- Info Panel -->
    <div class="info-panel">
        <h3>🧪 HOTSPOT TEST - {{ $sphere->nama_lokasi }}</h3>
        <p><strong>Sphere ID:</strong> {{ $sphere->id_sphere }}</p>
        <p><strong>Total Hotspots:</strong> {{ count($hotspotData) }}</p>
        <p><strong>Navigation Hotspots:</strong> {{ count(array_filter($hotspotData, fn($h) => $h['type'] === 'navigate')) }}</p>
        <p><strong>Click Test:</strong> <span id="click-status">Not tested</span></p>
    </div>

    <!-- Viewer Container -->
    <div id="panorama"></div>

    <!-- Test Panel -->
    <div class="test-panel">
        <div class="test-section">
            <h4>🎯 Hotspot Analysis</h4>
            <div id="hotspot-analysis">Loading analysis...</div>
        </div>

        <div class="test-section">
            <h4>🔍 Click Test Results</h4>
            <div id="click-results">Click on hotspots to test functionality</div>
        </div>

        <div class="test-section">
            <h4>📊 Real-time Status</h4>
            <div id="realtime-status">
                <div>⏱️ Loading panorama...</div>
            </div>
        </div>
    </div>

    <!-- Pannellum JS -->
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <script>
        // Test data
        const sphere = @json($sphere);
        const hotspotData = @json($hotspotData);
        const navigationSpheres = @json($navigationSpheres);

        let viewer = null;
        let clickTestResults = [];
        let loadStartTime = Date.now();

        // Update status function
        function updateStatus(message, type = 'info') {
            const statusDiv = document.getElementById('realtime-status');
            const timestamp = new Date().toLocaleTimeString();
            const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : '📊';
            statusDiv.innerHTML += `<div>[${timestamp}] ${icon} ${message}</div>`;
            statusDiv.scrollTop = statusDiv.scrollHeight;
            console.log(`[TEST] ${message}`);
        }

        // Analyze hotspots
        function analyzeHotspots() {
            const analysisDiv = document.getElementById('hotspot-analysis');
            let analysisHTML = '';

            hotspotData.forEach((hotspot, index) => {
                if (hotspot.type === 'navigate' && navigationSpheres[hotspot.target_id]) {
                    const target = navigationSpheres[hotspot.target_id];
                    const yaw = hotspot.calculated_yaw || hotspot.yaw || 0;
                    const pitch = hotspot.pitch || 0;
                    
                    let status = 'status-good';
                    let statusText = 'Good';
                    
                    if (yaw < 0 || yaw > 360) {
                        status = 'status-error';
                        statusText = 'Invalid Yaw';
                    } else if (pitch < -90 || pitch > 90) {
                        status = 'status-error';
                        statusText = 'Invalid Pitch';
                    } else if (!target) {
                        status = 'status-error';
                        statusText = 'Target Missing';
                    }

                    analysisHTML += `
                        <div class="hotspot-info ${status}">
                            <strong>Hotspot ${index + 1}: ${target.nama_lokasi}</strong><br>
                            Yaw: ${yaw.toFixed(1)}° | Pitch: ${pitch}° | Status: ${statusText}<br>
                            Distance: ${hotspot.distance_km || 'N/A'} km
                        </div>
                    `;
                }
            });

            analysisDiv.innerHTML = analysisHTML || '<div>No navigation hotspots found</div>';
        }

        // Process hotspots for viewer
        const processedHotSpots = [];
        const scenes = {};

        hotspotData.forEach(hotspot => {
            if (hotspot.type === 'navigate' && navigationSpheres[hotspot.target_id]) {
                const targetSphere = navigationSpheres[hotspot.target_id];
                const yaw = hotspot.calculated_yaw || hotspot.yaw || 0;
                
                processedHotSpots.push({
                    "pitch": hotspot.pitch || -10,
                    "yaw": yaw,
                    "type": "scene",
                    "text": `TEST: ${targetSphere.nama_lokasi}`,
                    "sceneId": `sphere_${targetSphere.id_sphere}`,
                    "cssClass": "hotspot-navigate",
                    "hotspotIndex": processedHotSpots.length
                });

                // Create target scene
                const returnBearing = (yaw + 180) % 360;
                const targetFilename = targetSphere.link.split('/').pop();
                scenes[`sphere_${targetSphere.id_sphere}`] = {
                    "type": "equirectangular",
                    "panorama": `{{ asset('assets/sphere/') }}/${targetFilename}.jpg`,
                    "hotSpots": [
                        {
                            "pitch": -10,
                            "yaw": returnBearing,
                            "type": "scene",
                            "text": `RETURN: ${sphere.nama_lokasi}`,
                            "sceneId": `sphere_${sphere.id_sphere}`,
                            "cssClass": "hotspot-navigate"
                        }
                    ]
                };
            }
        });

        // Main scene
        const currentFilename = sphere.link.split('/').pop();
        scenes[`sphere_${sphere.id_sphere}`] = {
            "type": "equirectangular",
            "panorama": `{{ asset('assets/sphere/') }}/${currentFilename}.jpg`,
            "autoLoad": true,
            "hotSpots": processedHotSpots
        };

        updateStatus(`Processed ${processedHotSpots.length} hotspots for testing`);
        analyzeHotspots();

        // Initialize viewer
        viewer = pannellum.viewer('panorama', {
            "default": {
                "firstScene": `sphere_${sphere.id_sphere}`,
                "sceneFadeDuration": 500,
                "autoLoad": true,
                "compass": true,
                "showZoomCtrl": true,
                "showFullscreenCtrl": true,
                "showControls": true,
                "autoRotate": 0,
                "mouseZoom": true,
                "draggable": true,
                "hotSpotDebug": true,
                "hfov": 90
            },
            "scenes": scenes
        });

        viewer.on('load', function() {
            const loadTime = Date.now() - loadStartTime;
            updateStatus(`Panorama loaded in ${loadTime}ms`, 'success');
            
            // Add click handlers with enhanced testing
            setTimeout(() => {
                addTestClickHandlers();
            }, 500);
        });

        viewer.on('scenechange', function(sceneId) {
            updateStatus(`Scene changed to: ${sceneId}`, 'success');
        });

        viewer.on('error', function(error) {
            updateStatus(`Pannellum error: ${JSON.stringify(error)}`, 'error');
        });

        function addTestClickHandlers() {
            const hotspotElements = document.querySelectorAll('.pnlm-hotspot-base.hotspot-navigate');
            updateStatus(`Found ${hotspotElements.length} hotspot elements for testing`);

            hotspotElements.forEach((element, index) => {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const testResult = {
                        index: index + 1,
                        timestamp: new Date().toLocaleTimeString(),
                        element: element,
                        success: false
                    };

                    try {
                        // Visual feedback
                        element.style.background = '#ff3333';
                        element.innerHTML = '⚡';
                        
                        // Find corresponding hotspot
                        const correspondingHotspot = processedHotSpots[index];
                        if (correspondingHotspot && correspondingHotspot.sceneId) {
                            updateStatus(`Hotspot ${index + 1} clicked - navigating to ${correspondingHotspot.sceneId}`, 'success');
                            viewer.loadScene(correspondingHotspot.sceneId);
                            testResult.success = true;
                            testResult.targetScene = correspondingHotspot.sceneId;
                        } else {
                            updateStatus(`Hotspot ${index + 1} clicked but no target scene found`, 'error');
                        }

                        // Reset visual
                        setTimeout(() => {
                            element.style.background = '';
                            element.innerHTML = '';
                        }, 1000);

                    } catch (error) {
                        updateStatus(`Error testing hotspot ${index + 1}: ${error.message}`, 'error');
                        testResult.error = error.message;
                    }

                    clickTestResults.push(testResult);
                    updateClickResults();
                });

                // Enhanced hover for testing
                element.addEventListener('mouseenter', function() {
                    updateStatus(`Hovering over hotspot ${index + 1}`);
                    element.style.borderColor = '#ffaa00';
                });

                element.addEventListener('mouseleave', function() {
                    element.style.borderColor = '#ffffff';
                });
            });

            document.getElementById('click-status').textContent = `Ready - ${hotspotElements.length} hotspots`;
            updateStatus(`Enhanced ${hotspotElements.length} hotspots with test handlers`, 'success');
        }

        function updateClickResults() {
            const resultsDiv = document.getElementById('click-results');
            let resultsHTML = '';

            clickTestResults.forEach(result => {
                const status = result.success ? 'status-good' : 'status-error';
                const statusText = result.success ? '✅ Success' : '❌ Failed';
                resultsHTML += `
                    <div class="hotspot-info ${status}">
                        [${result.timestamp}] Hotspot ${result.index}: ${statusText}
                        ${result.targetScene ? `<br>→ ${result.targetScene}` : ''}
                        ${result.error ? `<br>Error: ${result.error}` : ''}
                    </div>
                `;
            });

            resultsDiv.innerHTML = resultsHTML || 'No tests performed yet';
        }

        updateStatus('Hotspot test environment initialized', 'success');
    </script>
</body>
</html>
