# Virtual Tour dengan Navigate Hotspot

## Fitur Utama

### 1. **Navigate Hotspot** 
- Hotspot yang dapat diklik untuk berpindah antar sphere yang berdekatan
- Menampilkan arah dan jarak ke lokasi tujuan
- Animasi transisi yang smooth antar scene

### 2. **Info Hotspot**
- Hotspot informasi yang menampilkan detail lokasi
- Konten dalam format HTML untuk fleksibilitas
- Styling yang konsisten dengan desain UI

### 3. **Enhanced Controls**
- **Keyboard Shortcuts:**
  - `F`: Toggle fullscreen
  - `R`: Toggle auto-rotate
  - `H`: Show help
  - `ESC`: Back/Exit
  - `←→`: Navigate between spheres
- **Touch Controls:**
  - Swipe left/right untuk navigasi
  - Pinch to zoom
  - Pan untuk melihat sekeliling

### 4. **Fitur Tambahan**
- Auto-rotate dengan pause saat interaksi
- Preload gambar terdekat untuk loading yang cepat
- Progress indicator saat loading
- Share location functionality
- Performance monitoring
- Device detection dan optimasi

## Struktur Database

### Tabel tb_sphere
```sql
CREATE TABLE `tb_sphere` (
  `id_sphere` int(11) NOT NULL,
  `nama_lokasi` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  `lat` double NOT NULL,
  `lang` double NOT NULL,
  `count_seen` int(11) NOT NULL,
  `last_seen` datetime NOT NULL,
  `hotspot_data` JSON NULL COMMENT 'Data hotspot untuk navigasi antar sphere'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Format hotspot_data JSON
```json
[
  {
    "type": "navigate",
    "target_id": 2,
    "pitch": -10,
    "yaw": 180,
    "text": "Masuk ke Dalam Masjid"
  },
  {
    "type": "info",
    "pitch": 10,
    "yaw": 0,
    "text": "<h4>Masjid Taqwa</h4>Tempat ibadah utama"
  }
]
```

## Implementasi

### 1. Controller (SphereController.php)
```php
public function view($slug)
{
    $sphere = DB::table('tb_sphere')
        ->where('link', 'like', '%' . $slug)
        ->first();

    // Decode hotspot data
    $hotspotData = json_decode($sphere->hotspot_data, true) ?? [];

    // Ambil data sphere untuk navigasi
    $navigationSpheres = [];
    foreach ($hotspotData as $hotspot) {
        if ($hotspot['type'] === 'navigate') {
            $targetSphere = DB::table('tb_sphere')
                ->where('id_sphere', $hotspot['target_id'])
                ->first();
            $navigationSpheres[$hotspot['target_id']] = $targetSphere;
        }
    }

    return view('sphere.show', [
        'sphere' => $sphere,
        'hotspotData' => $hotspotData,
        'navigationSpheres' => $navigationSpheres
    ]);
}
```

### 2. View (sphere/show.blade.php)
- Menggunakan **Pannellum** sebagai pengganti Photo Sphere Viewer
- Integrasi dengan VirtualTourManager untuk kontrol advanced
- Responsive design untuk mobile dan desktop
- Custom CSS untuk styling hotspot

### 3. JavaScript Utils (virtual-tour-utils.js)
```javascript
class VirtualTourManager {
    constructor() {
        this.viewer = null;
        this.currentSphere = null;
        this.navigationHistory = [];
    }

    init(sphereData, hotspotData, navigationSpheres) {
        this.setupKeyboardControls();
        this.setupTouchControls();
        this.preloadNearbyImages(navigationSpheres);
        return this;
    }
}
```

## Grup Sphere yang Terhubung

### 1. **Area Pusat Kota**
- Taqwa Depan ↔ Taqwa Dalam
- Taqwa Depan ↔ Taman Merdeka
- Taman Merdeka ↔ Tugu Pena
- Taman Merdeka ↔ Tugu Metrem

### 2. **Area Pemerintahan**
- Tugu Metrem ↔ Mall Pelayanan Publik Depan
- Mall Pelayanan Publik Depan ↔ Pendaftaran ↔ Pelayanan

### 3. **Area Rekreasi**
- Kolam Renang ↔ Taman Metro Indonesia Indah

### 4. **Area Bersejarah**
- Rumah Sejarah Dalam ↔ Luar 1 ↔ Luar 2

## Instalasi dan Setup

### 1. Jalankan Migration
```bash
php artisan migrate --path=database/migrations/2025_07_09_000001_add_hotspot_data_to_tb_sphere_table.php
```

### 2. Jalankan Seeder
```bash
php artisan db:seed --class=SphereHotspotSeeder
```

### 3. Update Routes
```php
Route::get('/sphere/view/{slug}', [SphereController::class, 'view']);
Route::post('/api/sphere/plus/{id}', [SphereController::class, 'plus']);
```

## Contoh Penggunaan

### Membuat Hotspot Navigate
```json
{
  "type": "navigate",
  "target_id": 2,
  "pitch": -10,
  "yaw": 180,
  "text": "Masuk ke Dalam Masjid"
}
```

### Membuat Hotspot Info
```json
{
  "type": "info",
  "pitch": 10,
  "yaw": 0,
  "text": "<h4>Masjid Taqwa</h4>Tempat ibadah utama di pusat kota"
}
```

## Optimasi Performance

### 1. **Image Preloading**
- Preload gambar sphere terdekat
- Progressive loading untuk mobile

### 2. **Caching**
- Browser cache untuk assets
- CDN untuk library eksternal

### 3. **Mobile Optimization**
- Touch-friendly controls
- Responsive hotspot size
- Optimized loading untuk koneksi lambat

## Browser Support

- **Modern Browsers:** Chrome, Firefox, Safari, Edge
- **Mobile:** iOS Safari, Android Chrome
- **Features:** WebGL, ES6 Modules, JSON support

## Troubleshooting

### 1. **Hotspot tidak muncul**
- Periksa format JSON hotspot_data
- Pastikan target_id ada di database
- Cek console browser untuk error

### 2. **Loading lambat**
- Kompres gambar sphere
- Gunakan CDN untuk assets
- Periksa koneksi internet

### 3. **Navigation tidak berfungsi**
- Pastikan route sphere/view/{slug} berfungsi
- Periksa JavaScript console errors
- Verifikasi CSRF token

## Future Enhancements

### 1. **Audio Integration**
- Ambient sound untuk setiap lokasi
- Audio guide narrator
- Spatial audio effects

### 2. **AR/VR Support**
- WebXR integration
- VR headset support
- AR overlay information

### 3. **Analytics**
- User journey tracking
- Popular hotspot analytics
- Performance metrics

### 4. **Advanced Features**
- Multi-language support
- Accessibility features
- Social sharing integration
