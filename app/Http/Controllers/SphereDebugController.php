<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SphereDebugController extends Controller
{
    public function debug($id = null)
    {
        if ($id) {
            $sphere = DB::table('tb_sphere')->where('id_sphere', $id)->first();
            if (!$sphere) {
                return response()->json(['error' => 'Sphere not found'], 404);
            }
            
            $hotspotData = json_decode($sphere->hotspot_data, true) ?? [];
            $navigationSpheres = [];
            
            foreach ($hotspotData as &$hotspot) {
                if ($hotspot['type'] === 'navigate' && isset($hotspot['target_id'])) {
                    $targetSphere = DB::table('tb_sphere')
                        ->where('id_sphere', $hotspot['target_id'])
                        ->first();
                        
                    if ($targetSphere) {
                        $navigationSpheres[$hotspot['target_id']] = $targetSphere;
                        
                        // Hitung bearing
                        $bearing = $this->calculateBearing(
                            $sphere->lat, $sphere->lang,
                            $targetSphere->lat, $targetSphere->lang
                        );
                        
                        $distance = $this->calculateDistance(
                            $sphere->lat, $sphere->lang,
                            $targetSphere->lat, $targetSphere->lang
                        );
                        
                        $hotspot['calculated_bearing'] = round($bearing, 2);
                        $hotspot['distance_meters'] = round($distance * 1000, 2);
                        $hotspot['original_yaw'] = $hotspot['yaw'];
                    }
                }
            }
            
            return response()->json([
                'sphere' => $sphere,
                'hotspot_data' => $hotspotData,
                'navigation_spheres' => $navigationSpheres
            ], 200, [], JSON_PRETTY_PRINT);
        }
        
        // List all spheres dengan hotspot count
        $spheres = DB::table('tb_sphere')
            ->select('id_sphere', 'nama_lokasi', 'lat', 'lang', 'hotspot_data')
            ->orderBy('nama_lokasi')
            ->get();
            
        $result = [];
        foreach ($spheres as $sphere) {
            $hotspotData = json_decode($sphere->hotspot_data, true) ?? [];
            $navigateCount = count(array_filter($hotspotData, fn($h) => $h['type'] === 'navigate'));
            $infoCount = count(array_filter($hotspotData, fn($h) => $h['type'] === 'info'));
            
            $result[] = [
                'id' => $sphere->id_sphere,
                'nama' => $sphere->nama_lokasi,
                'coordinates' => [
                    'lat' => $sphere->lat,
                    'lng' => $sphere->lang
                ],
                'hotspot_count' => [
                    'navigate' => $navigateCount,
                    'info' => $infoCount,
                    'total' => count($hotspotData)
                ]
            ];
        }
        
        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
    }
    
    public function updateHotspotBearing($id)
    {
        $sphere = DB::table('tb_sphere')->where('id_sphere', $id)->first();
        if (!$sphere) {
            return response()->json(['error' => 'Sphere not found'], 404);
        }
        
        $hotspotData = json_decode($sphere->hotspot_data, true) ?? [];
        $updated = [];
        
        foreach ($hotspotData as &$hotspot) {
            if ($hotspot['type'] === 'navigate' && isset($hotspot['target_id'])) {
                $targetSphere = DB::table('tb_sphere')
                    ->where('id_sphere', $hotspot['target_id'])
                    ->first();
                    
                if ($targetSphere) {
                    $bearing = $this->calculateBearing(
                        $sphere->lat, $sphere->lang,
                        $targetSphere->lat, $targetSphere->lang
                    );
                    
                    $hotspot['original_yaw'] = $hotspot['yaw'];
                    $hotspot['yaw'] = round($bearing);
                    $updated[] = [
                        'target' => $targetSphere->nama_lokasi,
                        'old_yaw' => $hotspot['original_yaw'],
                        'new_yaw' => $hotspot['yaw'],
                        'bearing' => round($bearing, 2)
                    ];
                }
            }
        }
        
        // Update database
        DB::table('tb_sphere')
            ->where('id_sphere', $id)
            ->update(['hotspot_data' => json_encode($hotspotData)]);
            
        return response()->json([
            'message' => 'Hotspot bearings updated',
            'sphere' => $sphere->nama_lokasi,
            'updates' => $updated
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    private function calculateBearing($lat1, $lng1, $lat2, $lng2)
    {
        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $deltaLng = deg2rad($lng2 - $lng1);

        $y = sin($deltaLng) * cos($lat2);
        $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($deltaLng);

        $bearing = atan2($y, $x);
        $bearing = rad2deg($bearing);
        
        return ($bearing + 360) % 360;
    }
    
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);
        
        $deltaLat = $lat2 - $lat1;
        $deltaLng = $lng2 - $lng1;
        
        $a = sin($deltaLat/2) * sin($deltaLat/2) + 
             cos($lat1) * cos($lat2) * 
             sin($deltaLng/2) * sin($deltaLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
}
