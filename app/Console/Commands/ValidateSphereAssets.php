<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ValidateSphereAssets extends Command
{
    protected $signature = 'sphere:validate';
    protected $description = 'Validate all sphere assets and hotspot configurations';

    public function handle()
    {
        $this->info('🔍 Validating sphere assets and configurations...');

        $spheres = DB::table('tb_sphere')->get();
        $errors = 0;
        $warnings = 0;
        $success = 0;

        foreach ($spheres as $sphere) {
            $this->line("📍 Checking: {$sphere->nama_lokasi}");

            // Check image file exists
            $linkParts = explode('/', $sphere->link);
            $imageFile = end($linkParts);
            $imagePath = public_path("assets/sphere/{$imageFile}.jpg");
            
            if (!File::exists($imagePath)) {
                $this->error("   ❌ Image not found: {$imageFile}.jpg");
                $errors++;
                continue;
            }

            // Check image file size
            $fileSize = File::size($imagePath);
            if ($fileSize < 100000) { // Less than 100KB might be corrupted
                $this->warn("   ⚠️  Image file very small ({$fileSize} bytes): {$imageFile}.jpg");
                $warnings++;
            }

            // Check hotspot data
            if ($sphere->hotspot_data) {
                $hotspotData = json_decode($sphere->hotspot_data, true);
                $navHotspots = 0;
                $infoHotspots = 0;
                $invalidTargets = 0;

                foreach ($hotspotData as $hotspot) {
                    if ($hotspot['type'] === 'navigate') {
                        $navHotspots++;
                        if (isset($hotspot['target_id'])) {
                            $targetExists = DB::table('tb_sphere')
                                ->where('id_sphere', $hotspot['target_id'])
                                ->exists();
                            if (!$targetExists) {
                                $invalidTargets++;
                                $this->error("   ❌ Invalid target ID: {$hotspot['target_id']}");
                            }
                        }
                    } elseif ($hotspot['type'] === 'info') {
                        $infoHotspots++;
                    }
                }

                if ($invalidTargets > 0) {
                    $errors += $invalidTargets;
                } else {
                    $this->info("   ✅ {$navHotspots} nav + {$infoHotspots} info hotspots");
                    $success++;
                }
            } else {
                $this->warn("   ⚠️  No hotspot data");
                $warnings++;
            }
        }

        $this->newLine();
        $this->info("📊 Validation Results:");
        $this->info("   ✅ Valid spheres: {$success}");
        if ($warnings > 0) {
            $this->warn("   ⚠️  Warnings: {$warnings}");
        }
        if ($errors > 0) {
            $this->error("   ❌ Errors: {$errors}");
        } else {
            $this->info("   🎉 All sphere assets and configurations are valid!");
        }

        return $errors > 0 ? 1 : 0;
    }
}
