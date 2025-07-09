<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateSphereBearings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sphere:update-bearings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update hotspot bearings berdasarkan koordinat GPS yang akurat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai update bearing hotspot...');
        
        // Ambil semua sphere yang memiliki hotspot data
        $spheres = DB::table('tb_sphere')
            ->whereNotNull('hotspot_data')
            ->get();

        $updated_count = 0;
        $total_hotspots = 0;

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
                        $old_bearing = $hotspot['yaw'] ?? 0;
                        $accurate_bearing = $this->calculateBearing(
                            $sphere->lat, $sphere->lang,
                            $targetSphere->lat, $targetSphere->lang
                        );
                        
                        // Update yaw dengan bearing yang akurat
                        $hotspot['yaw'] = round($accurate_bearing);
                        $hasUpdates = true;
                        $total_hotspots++;
                        
                        $this->line("  {$sphere->nama_lokasi} -> {$targetSphere->nama_lokasi}");
                        $this->line("    Old bearing: {$old_bearing}° | New bearing: {$accurate_bearing}°");
                    }
                }
            }
            
            if ($hasUpdates) {
                DB::table('tb_sphere')
                    ->where('id_sphere', $sphere->id_sphere)
                    ->update(['hotspot_data' => json_encode($hotspotData)]);
                $updated_count++;
                $this->info("✓ Updated: {$sphere->nama_lokasi}");
            }
        }

        $this->info("\n🎯 Update selesai!");
        $this->info("📊 Statistik:");
        $this->info("   - Sphere diupdate: {$updated_count}");
        $this->info("   - Total hotspot navigate: {$total_hotspots}");
        
        return Command::SUCCESS;
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
}
