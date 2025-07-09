<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\CCTVController;
use App\Http\Controllers\SphereController;
use App\Http\Controllers\PanganController;
use App\Http\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::post('/set-session', [WelcomeController::class, 'setSession'])->name('welcome.setSession');
Route::get('/clear-session', function() {
    session()->forget('visited_welcome');
    return redirect('/');
})->name('clear.session');

Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::post('/cctv/view/{id}', [CCTVController::class, 'view'])->name('cctv.view');
Route::get('/sphere/view/{slug}', [SphereController::class, 'view']);
Route::post('/api/sphere/plus/{id}', [SphereController::class, 'plus']);
Route::get('/api/pangan', [PanganController::class, 'getData']);

// Debug routes untuk sphere
Route::get('/debug/sphere/{id?}', [App\Http\Controllers\SphereDebugController::class, 'debug']);
Route::post('/debug/sphere/{id}/update-bearing', [App\Http\Controllers\SphereDebugController::class, 'updateHotspotBearing']);

// Test route for hotspot functionality
Route::get('/sphere/test/{id}', function($id) {
    $sphere = DB::table('tb_sphere')
        ->where('id_sphere', $id)
        ->first();

    if (!$sphere) {
        abort(404, 'Virtual tour tidak ditemukan');
    }

    $hotspotData = [];
    if ($sphere->hotspot_data) {
        $hotspotData = json_decode($sphere->hotspot_data, true);
    }

    $navigationSpheres = [];
    foreach ($hotspotData as &$hotspot) {
        if ($hotspot['type'] === 'navigate' && isset($hotspot['target_id'])) {
            $targetSphere = DB::table('tb_sphere')
                ->where('id_sphere', $hotspot['target_id'])
                ->first();
                
            if ($targetSphere) {
                $navigationSpheres[$hotspot['target_id']] = $targetSphere;
                
                // Calculate bearing
                $lat1 = deg2rad($sphere->lat);
                $lat2 = deg2rad($targetSphere->lat);
                $deltaLng = deg2rad($targetSphere->lang - $sphere->lang);

                $y = sin($deltaLng) * cos($lat2);
                $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($deltaLng);

                $bearing = atan2($y, $x);
                $bearing = rad2deg($bearing);
                $bearing = ($bearing + 360) % 360;
                
                $hotspot['calculated_yaw'] = round($bearing, 1);
                
                // Calculate distance
                $earthRadius = 6371;
                $deltaLatRad = deg2rad($targetSphere->lat - $sphere->lat);
                $deltaLngRad = deg2rad($targetSphere->lang - $sphere->lang);
                $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
                     cos($lat1) * cos($lat2) *
                     sin($deltaLngRad / 2) * sin($deltaLngRad / 2);
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earthRadius * $c;
                $hotspot['distance_km'] = round($distance, 3);
            }
        }
    }

    return view('sphere.test', [
        'sphere' => $sphere,
        'hotspotData' => $hotspotData,
        'navigationSpheres' => $navigationSpheres
    ]);
});

Route::get('/sphere/debug/{slug}', function($slug) {
    $sphere = DB::table('tb_sphere')
        ->where('link', 'like', '%' . $slug)
        ->first();

    if (!$sphere) {
        abort(404, 'Virtual tour tidak ditemukan');
    }

    $hotspotData = [];
    if ($sphere->hotspot_data) {
        $hotspotData = json_decode($sphere->hotspot_data, true);
    }

    $navigationSpheres = [];
    foreach ($hotspotData as &$hotspot) {
        if ($hotspot['type'] === 'navigate' && isset($hotspot['target_id'])) {
            $targetSphere = DB::table('tb_sphere')
                ->where('id_sphere', $hotspot['target_id'])
                ->first();
                
            if ($targetSphere) {
                $navigationSpheres[$hotspot['target_id']] = $targetSphere;
                
                $lat1 = deg2rad($sphere->lat);
                $lat2 = deg2rad($targetSphere->lat);
                $deltaLng = deg2rad($targetSphere->lang - $sphere->lang);

                $y = sin($deltaLng) * cos($lat2);
                $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($deltaLng);

                $bearing = atan2($y, $x);
                $bearing = rad2deg($bearing);
                $bearing = ($bearing + 360) % 360;
                
                $hotspot['calculated_yaw'] = $bearing;
            }
        }
    }

    return view('sphere.debug', [
        'sphere' => $sphere,
        'hotspotData' => $hotspotData,
        'navigationSpheres' => $navigationSpheres
    ]);
});