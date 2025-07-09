<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SphereController extends Controller
{
    // Menampilkan semua data sphere (seperti index)
    public function index()
    {
        $spheres = DB::table('tb_sphere')->orderBy('nama_lokasi', 'ASC')->get();
        return response()->json($spheres, 201);
    }

    // Menampilkan virtual tour (seperti view di CodeIgniter)
    public function view($slug)
    {
        $sphere = DB::table('tb_sphere')
            ->where('link', 'like', '%' . $slug)
            ->first();

        if (!$sphere) {
            abort(404, 'Virtual tour tidak ditemukan');
        }

        // Extract filename from link URL with improved error handling
        $linkParts = explode('/', $sphere->link);
        $filename = end($linkParts);
        
        // Check for both jpg and JPG extensions (case sensitivity)
        $assetPathJpg = public_path("assets/sphere/{$filename}.jpg");
        $assetPathJPG = public_path("assets/sphere/{$filename}.JPG");
        $assetPathWithoutExt = public_path("assets/sphere/{$filename}");
        
        // Check if file exists in any format
        $fileExists = file_exists($assetPathJpg) || file_exists($assetPathJPG) || file_exists($assetPathWithoutExt);
        $actualPath = "";
        
        if (file_exists($assetPathJpg)) {
            $actualPath = "assets/sphere/{$filename}.jpg";
        } elseif (file_exists($assetPathJPG)) {
            $actualPath = "assets/sphere/{$filename}.JPG";
        } elseif (file_exists($assetPathWithoutExt)) {
            $actualPath = "assets/sphere/{$filename}";
        }
        
        // List all available files for troubleshooting
        $allFiles = glob(public_path("assets/sphere/*"));
        $availableFiles = [];
        foreach ($allFiles as $file) {
            $availableFiles[] = basename($file);
        }
        
        // Log detailed info for debugging
        \Log::info("Loading sphere: {$sphere->nama_lokasi}, File: {$filename}, Exists: " . ($fileExists ? 'Yes' : 'No'));
        \Log::info("Attempted paths: {$assetPathJpg}, {$assetPathJPG}, {$assetPathWithoutExt}");
        \Log::info("Actual path found: {$actualPath}");
        
        if (!$fileExists) {
            \Log::warning("Sphere file not found: {$assetPathJpg}");
            \Log::warning("Available files: " . implode(", ", $availableFiles));
            
            // Try to find similar filenames
            $similarFiles = [];
            foreach ($availableFiles as $file) {
                if (strpos($file, substr($filename, 0, 10)) !== false) {
                    $similarFiles[] = $file;
                }
            }
            
            if (!empty($similarFiles)) {
                \Log::info("Similar files found: " . implode(", ", $similarFiles));
            }
        }

        // Decode hotspot data if exists
        $hotspotData = [];
        if ($sphere->hotspot_data) {
            $hotspotData = json_decode($sphere->hotspot_data, true);
        }

        // Ambil data sphere untuk hotspot navigasi dan hitung bearing
        $navigationSpheres = [];
        foreach ($hotspotData as &$hotspot) {
            if ($hotspot['type'] === 'navigate' && isset($hotspot['target_id'])) {
                $targetSphere = DB::table('tb_sphere')
                    ->where('id_sphere', $hotspot['target_id'])
                    ->first();
                    
                if ($targetSphere) {
                    $navigationSpheres[$hotspot['target_id']] = $targetSphere;
                    
                    // Hitung bearing otomatis berdasarkan koordinat GPS dengan presisi tinggi
                    $bearing = $this->calculateBearing(
                        $sphere->lat, $sphere->lang,
                        $targetSphere->lat, $targetSphere->lang
                    );
                    
                    // Update yaw dengan bearing yang tepat
                    $hotspot['calculated_yaw'] = $bearing;
                    
                    // Add validation to ensure bearing is reasonable
                    if ($bearing < 0 || $bearing > 360) {
                        \Log::warning("Invalid bearing calculated for sphere {$sphere->id_sphere} to {$targetSphere->id_sphere}: {$bearing}");
                        $hotspot['calculated_yaw'] = $hotspot['yaw'] ?? 0; // Fallback to original yaw
                    }
                    
                    // Calculate distance for debugging
                    $distance = $this->calculateDistance(
                        $sphere->lat, $sphere->lang,
                        $targetSphere->lat, $targetSphere->lang
                    );
                    $hotspot['distance_km'] = $distance;
                    
                    // Check if the target sphere's file exists too
                    $targetLinkParts = explode('/', $targetSphere->link);
                    $targetFilename = end($targetLinkParts);
                    $targetFileExists = file_exists(public_path("assets/sphere/{$targetFilename}.jpg")) ||
                                       file_exists(public_path("assets/sphere/{$targetFilename}.JPG")) ||
                                       file_exists(public_path("assets/sphere/{$targetFilename}"));
                    
                    if (!$targetFileExists) {
                        \Log::warning("Target sphere file not found: {$targetFilename}");
                    }
                }
            }
        }

        // Add file information to the view data
        $fileInfo = [
            'exists' => $fileExists,
            'path' => $actualPath ?: "assets/sphere/{$filename}.jpg",
            'fullPath' => $assetPathJpg,
            'possiblePaths' => [
                'jpg' => $assetPathJpg,
                'JPG' => $assetPathJPG,
                'noExt' => $assetPathWithoutExt
            ],
            'similarFiles' => $similarFiles ?? [],
            'allFiles' => $availableFiles
        ];
        
        return view('sphere.show', [
            'sphere' => $sphere,
            'hotspotData' => $hotspotData,
            'navigationSpheres' => $navigationSpheres,
            'filename' => $filename,
            'fileInfo' => $fileInfo
        ]);
    }

    // Fungsi untuk menghitung bearing antara dua koordinat dengan presisi tinggi
    private function calculateBearing($lat1, $lng1, $lat2, $lng2)
    {
        // Convert degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLngRad = deg2rad($lng2 - $lng1);

        // Calculate bearing using the forward azimuth formula
        $y = sin($deltaLngRad) * cos($lat2Rad);
        $x = cos($lat1Rad) * sin($lat2Rad) - sin($lat1Rad) * cos($lat2Rad) * cos($deltaLngRad);

        $bearingRad = atan2($y, $x);
        $bearingDeg = rad2deg($bearingRad);
        
        // Normalize to 0-360 degrees
        $normalizedBearing = ($bearingDeg + 360) % 360;
        
        // Round to 1 decimal place for precision
        return round($normalizedBearing, 1);
    }

    // Fungsi untuk menghitung jarak antara dua koordinat (Haversine formula)
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Radius bumi dalam kilometer

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLngRad = deg2rad($lng2 - $lng1);

        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLngRad / 2) * sin($deltaLngRad / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return round($distance, 3); // Return distance in kilometers with 3 decimal places
    }

    // Fungsi tambah counter dilihat
    public function plus($id)
    {
        // Update count_seen dan last_seen
        DB::table('tb_sphere')
            ->where('id_sphere', $id)
            ->update([
                'count_seen' => DB::raw('count_seen + 1'),
                'last_seen' => Carbon::now()
            ]);

        // Simpan ke history
        DB::table('tb_sphere_history')->insert([
            'id_sphere' => $id,
            'datetime' => Carbon::now()
        ]);

        // Kembalikan semua data terbaru
        $data = DB::table('tb_sphere')->get();
        return response()->json($data, 201);
    }
}
