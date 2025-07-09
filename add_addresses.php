<?php
require_once 'vendor/autoload.php';

// Properly bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Adding addresses to sphere locations...\n";

// Array of addresses for the spheres
$sphereAddresses = [
    1 => 'Jl. Masjid Taqwa No. 1, Metro Pusat, Kota Metro, Lampung', // Taqwa Depan
    2 => 'Jl. Masjid Taqwa No. 2, Metro Pusat, Kota Metro, Lampung', // Taqwa Dalam  
    3 => 'Jl. Taman Merdeka, Metro Pusat, Kota Metro, Lampung', // Taman Merdeka
    4 => 'Jl. Tugu Metrem, Metro Pusat, Kota Metro, Lampung', // Tugu Metrem
    5 => 'Jl. Tugu Pena, Metro Pusat, Kota Metro, Lampung', // Tugu Pena
];

foreach($sphereAddresses as $sphereId => $address) {
    try {
        $result = DB::table('tb_sphere')
            ->where('id_sphere', $sphereId)
            ->update(['alamat' => $address]);
            
        if($result) {
            echo "✅ Updated sphere ID $sphereId with address: $address\n";
        } else {
            echo "⚠️ No sphere found with ID $sphereId\n";
        }
    } catch(Exception $e) {
        echo "❌ Error updating sphere ID $sphereId: " . $e->getMessage() . "\n";
    }
}

echo "\nChecking updated spheres with addresses:\n";
$spheres = DB::table('tb_sphere')->limit(10)->get(['id_sphere', 'nama_lokasi', 'alamat']);

foreach($spheres as $sphere) {
    $address = $sphere->alamat ?: '(No address)';
    echo "ID: {$sphere->id_sphere} | Name: {$sphere->nama_lokasi} | Address: {$address}\n";
}

echo "\nDone!\n";
