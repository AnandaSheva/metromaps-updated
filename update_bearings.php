<?php

use Illuminate\Support\Facades\DB;

// Function untuk menghitung bearing
function calculateBearing($lat1, $lng1, $lat2, $lng2) {
    $lat1 = deg2rad($lat1);
    $lat2 = deg2rad($lat2);
    $deltaLng = deg2rad($lng2 - $lng1);

    $y = sin($deltaLng) * cos($lat2);
    $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($deltaLng);

    $bearing = atan2($y, $x);
    $bearing = rad2deg($bearing);
    
    return ($bearing + 360) % 360;
}

// Ambil semua sphere yang memiliki hotspot data
$spheres = DB::table('tb_sphere')
    ->whereNotNull('hotspot_data')
    ->get();

$updated_count = 0;

foreach ($spheres as $sphere) {
    $hotspotData = json_decode($sphere->hotspot_data, true);
    if (!$hotspotData) continue;
    
    $hasUpdates = false;
    
    foreach ($hotspotData as &$hotspot) {
        if ($hotspot['type'] === 'navigate' && isset($hotspot['target_id'])) {
            $targetSphere = DB::table('tb_sphere')
                ->where('id_sphere', $hotspot['target_id'])
                ->first();
                
            if ($targetSphere) {
                $accurate_bearing = calculateBearing(
                    $sphere->lat, $sphere->lang,
                    $targetSphere->lat, $targetSphere->lang
                );
                
                // Update yaw dengan bearing yang akurat
                $hotspot['yaw'] = round($accurate_bearing);
                $hasUpdates = true;
                
                echo "Updated: {$sphere->nama_lokasi} -> {$targetSphere->nama_lokasi}: bearing {$accurate_bearing}°\n";
            }
        }
    }
    
    if ($hasUpdates) {
        DB::table('tb_sphere')
            ->where('id_sphere', $sphere->id_sphere)
            ->update(['hotspot_data' => json_encode($hotspotData)]);
        $updated_count++;
    }
}

echo "\nTotal updated: {$updated_count} spheres\n";
