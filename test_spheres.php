<?php
require_once 'vendor/autoload.php';

// Properly bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "First 5 spheres in database:\n";
$spheres = DB::table('tb_sphere')->limit(5)->get();

foreach($spheres as $sphere) {
    echo "ID: {$sphere->id_sphere} | Name: {$sphere->nama_lokasi} | Link: {$sphere->link}\n";
}

echo "\nLooking for sphere with link containing 'banksampah':\n";
$bankSampahSphere = DB::table('tb_sphere')->where('link', 'like', '%banksampah%')->first();
if($bankSampahSphere) {
    echo "Found: ID: {$bankSampahSphere->id_sphere} | Name: {$bankSampahSphere->nama_lokasi} | Link: {$bankSampahSphere->link}\n";
} else {
    echo "No sphere found with 'banksampah' in link\n";
}

echo "\nLooking for sphere with link containing 'PXL_20240517_012748972':\n";
$pxlSphere = DB::table('tb_sphere')->where('link', 'like', '%PXL_20240517_012748972%')->first();
if($pxlSphere) {
    echo "Found: ID: {$pxlSphere->id_sphere} | Name: {$pxlSphere->nama_lokasi} | Link: {$pxlSphere->link}\n";
} else {
    echo "No sphere found with 'PXL_20240517_012748972' in link\n";
}
