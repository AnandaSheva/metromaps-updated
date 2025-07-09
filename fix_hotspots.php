<?php

// Simple script to fix hotspot issues
// Run this from the root of your Laravel project: php update_bearings.php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function calculateBearing($lat1, $lng1, $lat2, $lng2)
{
    $lat1Rad = deg2rad($lat1);
    $lat2Rad = deg2rad($lat2);
    $deltaLngRad = deg2rad($lng2 - $lng1);

    $y = sin($deltaLngRad) * cos($lat2Rad);
    $x = cos($lat1Rad) * sin($lat2Rad) - sin($lat1Rad) * cos($lat2Rad) * cos($deltaLngRad);

    $bearingRad = atan2($y, $x);
    $bearingDeg = rad2deg($bearingRad);
    
    return round(($bearingDeg + 360) % 360, 1);
}

echo "🔧 Starting hotspot bearing correction...\n";

// Get all spheres with hotspot data
$spheres = DB::table('tb_sphere')
    ->whereNotNull('hotspot_data')
    ->where('hotspot_data', '!=', '')
    ->get();

$totalUpdated = 0;
$totalHotspots = 0;

foreach ($spheres as $sphere) {
    $hotspotData = json_decode($sphere->hotspot_data, true);
    
    if (!$hotspotData) {
        continue;
    }
    
    $updated = false;
    $sphereHotspots = 0;
    
    foreach ($hotspotData as &$hotspot) {
        if ($hotspot['type'] === 'navigate' && isset($hotspot['target_id'])) {
            $targetSphere = DB::table('tb_sphere')
                ->where('id_sphere', $hotspot['target_id'])
                ->first();
                
            if ($targetSphere) {
                $oldYaw = $hotspot['yaw'] ?? 0;
                $newYaw = calculateBearing(
                    $sphere->lat, $sphere->lang,
                    $targetSphere->lat, $targetSphere->lang
                );
                
                $difference = abs($newYaw - $oldYaw);
                if ($difference > 180) {
                    $difference = 360 - $difference;
                }
                
                // Update if difference is significant (>5 degrees)
                if ($difference > 5) {
                    echo "📍 {$sphere->nama_lokasi} -> {$targetSphere->nama_lokasi}\n";
                    echo "   Old bearing: {$oldYaw}° | New bearing: {$newYaw}° | Difference: {$difference}°\n";
                    
                    $hotspot['yaw'] = $newYaw;
                    $updated = true;
                }
                
                $sphereHotspots++;
                $totalHotspots++;
            }
        }
    }
    
    if ($updated) {
        DB::table('tb_sphere')
            ->where('id_sphere', $sphere->id_sphere)
            ->update([
                'hotspot_data' => json_encode($hotspotData)
            ]);
        
        $totalUpdated++;
        echo "✅ Updated {$sphere->nama_lokasi} ({$sphereHotspots} hotspots)\n";
    }
}

echo "\n🎯 Summary:\n";
echo "   Total spheres checked: " . count($spheres) . "\n";
echo "   Total hotspots found: {$totalHotspots}\n";
echo "   Spheres updated: {$totalUpdated}\n";
echo "✅ Hotspot bearing correction complete!\n";
