/* Virtual Tour Utilities and Enhanced Functions */

class VirtualTourManager {
    constructor() {
        this.viewer = null;
        this.currentSphere = null;
        this.navigationHistory = [];
        this.isFullscreen = false;
        this.autoRotateEnabled = true;
    }

    // Initialize virtual tour with enhanced features
    init(sphereData, hotspotData, navigationSpheres) {
        this.currentSphere = sphereData;
        this.setupKeyboardControls();
        this.setupTouchControls();
        this.preloadNearbyImages(navigationSpheres);
        return this;
    }

    // Setup keyboard shortcuts
    setupKeyboardControls() {
        document.addEventListener('keydown', (e) => {
            switch(e.key.toLowerCase()) {
                case 'f':
                    this.toggleFullscreen();
                    break;
                case 'r':
                    this.toggleAutoRotate();
                    break;
                case 'h':
                    this.showHelp();
                    break;
                case 'escape':
                    if (this.isFullscreen) {
                        this.toggleFullscreen();
                    } else {
                        this.goBack();
                    }
                    break;
                case 'arrowleft':
                    this.navigateToPrevious();
                    break;
                case 'arrowright':
                    this.navigateToNext();
                    break;
            }
        });
    }

    // Setup touch controls for mobile
    setupTouchControls() {
        let touchStartX = 0;
        let touchStartY = 0;

        document.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        });

        document.addEventListener('touchend', (e) => {
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;
            
            // Swipe gestures
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
                if (deltaX > 0) {
                    this.navigateToPrevious();
                } else {
                    this.navigateToNext();
                }
            }
        });
    }

    // Preload nearby sphere images for faster navigation
    preloadNearbyImages(navigationSpheres) {
        Object.values(navigationSpheres).forEach(sphere => {
            const img = new Image();
            img.src = `/assets/sphere/${sphere.link.split('/').pop()}.jpg`;
        });
    }

    // Toggle fullscreen mode
    toggleFullscreen() {
        if (this.viewer) {
            this.viewer.toggleFullscreen();
            this.isFullscreen = !this.isFullscreen;
        }
    }

    // Toggle auto rotate
    toggleAutoRotate() {
        if (this.viewer) {
            this.autoRotateEnabled = !this.autoRotateEnabled;
            this.viewer.setAutoRotate(this.autoRotateEnabled ? -2 : 0);
            this.showNotification(
                this.autoRotateEnabled ? 'Auto-rotate enabled' : 'Auto-rotate disabled'
            );
        }
    }

    // Show help overlay
    showHelp() {
        const helpOverlay = document.createElement('div');
        helpOverlay.className = 'help-overlay';
        helpOverlay.innerHTML = `
            <div class="help-content">
                <h3>🎮 Kontrol Virtual Tour</h3>
                <div class="help-grid">
                    <div><kbd>F</kbd> Fullscreen</div>
                    <div><kbd>R</kbd> Auto-rotate</div>
                    <div><kbd>H</kbd> Help</div>
                    <div><kbd>ESC</kbd> Back/Exit</div>
                    <div><kbd>←→</kbd> Navigate</div>
                    <div><kbd>Mouse</kbd> Look around</div>
                    <div><kbd>Scroll</kbd> Zoom</div>
                    <div><kbd>Touch</kbd> Swipe navigate</div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()">Tutup</button>
            </div>
        `;
        document.body.appendChild(helpOverlay);

        // Auto close after 5 seconds
        setTimeout(() => {
            if (helpOverlay.parentElement) {
                helpOverlay.remove();
            }
        }, 5000);
    }

    // Navigate to previous sphere
    navigateToPrevious() {
        if (this.navigationHistory.length > 1) {
            this.navigationHistory.pop(); // Remove current
            const previous = this.navigationHistory[this.navigationHistory.length - 1];
            this.navigateToSphere(previous, false);
        }
    }

    // Navigate to next sphere (if available)
    navigateToNext() {
        // This would need to be implemented based on your navigation logic
        console.log('Navigate to next sphere');
    }

    // Navigate to specific sphere
    navigateToSphere(sphereId, addToHistory = true) {
        if (addToHistory) {
            this.navigationHistory.push(sphereId);
        }
        
        if (this.viewer) {
            this.viewer.loadScene(`sphere_${sphereId}`);
        }
    }

    // Go back to previous sphere or close
    goBack() {
        if (this.navigationHistory.length > 1) {
            this.navigateToPrevious();
        } else {
            window.close();
        }
    }

    // Show notification message
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        notification.style.cssText = `
            position: fixed;
            top: 70px;
            right: 20px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            z-index: 9999;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;

        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => notification.style.opacity = '1', 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Get sphere statistics
    getStats() {
        return {
            currentSphere: this.currentSphere?.nama_lokasi || 'Unknown',
            navigationHistory: this.navigationHistory.length,
            isFullscreen: this.isFullscreen,
            autoRotateEnabled: this.autoRotateEnabled
        };
    }

    // Share current location
    shareLocation() {
        if (navigator.share && this.currentSphere) {
            navigator.share({
                title: `Virtual Tour: ${this.currentSphere.nama_lokasi}`,
                text: `Jelajahi ${this.currentSphere.nama_lokasi} dalam Virtual Tour 360°`,
                url: window.location.href
            }).catch(console.error);
        } else {
            // Fallback to copy URL
            navigator.clipboard.writeText(window.location.href).then(() => {
                this.showNotification('Link copied to clipboard!');
            }).catch(() => {
                this.showNotification('Unable to copy link', 'error');
            });
        }
    }
}

// Utility functions
const VirtualTourUtils = {
    // Calculate distance between two points
    calculateDistance(lat1, lng1, lat2, lng2) {
        const R = 6371; // Earth's radius in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    },

    // Format distance for display
    formatDistance(distance) {
        if (distance < 1) {
            return `${Math.round(distance * 1000)}m`;
        }
        return `${distance.toFixed(1)}km`;
    },

    // Get compass direction
    getCompassDirection(bearing) {
        const directions = ['Utara', 'Timur Laut', 'Timur', 'Tenggara', 'Selatan', 'Barat Daya', 'Barat', 'Barat Laut'];
        const index = Math.round(bearing / 45) % 8;
        return directions[index];
    },

    // Performance monitoring
    startPerformanceMonitoring() {
        if ('performance' in window) {
            const observer = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (entry.entryType === 'navigation') {
                        console.log('Page load time:', entry.loadEventEnd - entry.loadEventStart, 'ms');
                    }
                }
            });
            observer.observe({entryTypes: ['navigation']});
        }
    },

    // Device detection
    getDeviceInfo() {
        const ua = navigator.userAgent;
        return {
            isMobile: /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua),
            isTablet: /iPad|Android(?=.*Tablet)/i.test(ua),
            isDesktop: !/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua),
            supportsWebGL: !!window.WebGLRenderingContext,
            supportsVR: 'xr' in navigator || 'getVRDisplays' in navigator
        };
    }
};

// Auto-initialize performance monitoring
VirtualTourUtils.startPerformanceMonitoring();

// Global instance
window.VirtualTourManager = VirtualTourManager;
window.VirtualTourUtils = VirtualTourUtils;
