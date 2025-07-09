<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SphereHotspotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data hotspot untuk sphere yang berdekatan
        $hotspotData = [
            1 => [ // Taqwa Depan
                ['type' => 'navigate', 'target_id' => 2, 'pitch' => -10, 'yaw' => 180, 'text' => 'Masuk ke Dalam Masjid'],
                ['type' => 'navigate', 'target_id' => 3, 'pitch' => -5, 'yaw' => 90, 'text' => 'Ke Taman Merdeka'],
                ['type' => 'info', 'pitch' => 10, 'yaw' => 0, 'text' => '<h4>Masjid Taqwa Kota Metro</h4>Tempat ibadah utama di pusat kota']
            ],
            2 => [ // Taqwa Dalam
                ['type' => 'navigate', 'target_id' => 1, 'pitch' => -10, 'yaw' => 0, 'text' => 'Keluar Masjid'],
                ['type' => 'info', 'pitch' => 15, 'yaw' => 180, 'text' => '<h4>Interior Masjid Taqwa</h4>Ruang sholat utama yang megah']
            ],
            3 => [ // Taman Merdeka
                ['type' => 'navigate', 'target_id' => 1, 'pitch' => -8, 'yaw' => 270, 'text' => 'Ke Masjid Taqwa'],
                ['type' => 'navigate', 'target_id' => 5, 'pitch' => -5, 'yaw' => 180, 'text' => 'Ke Tugu Pena'],
                ['type' => 'navigate', 'target_id' => 4, 'pitch' => -5, 'yaw' => 90, 'text' => 'Ke Tugu Metrem'],
                ['type' => 'info', 'pitch' => 5, 'yaw' => 0, 'text' => '<h4>Taman Merdeka</h4>Ruang terbuka hijau di pusat kota']
            ],
            4 => [ // Tugu Metrem
                ['type' => 'navigate', 'target_id' => 3, 'pitch' => -8, 'yaw' => 270, 'text' => 'Ke Taman Merdeka'],
                ['type' => 'navigate', 'target_id' => 6, 'pitch' => -5, 'yaw' => 90, 'text' => 'Ke Mall Pelayanan Publik'],
                ['type' => 'info', 'pitch' => 20, 'yaw' => 0, 'text' => '<h4>Tugu Metrem</h4>Ikon Kota Metro yang terkenal']
            ],
            5 => [ // Tugu Pena
                ['type' => 'navigate', 'target_id' => 3, 'pitch' => -8, 'yaw' => 0, 'text' => 'Ke Taman Merdeka'],
                ['type' => 'navigate', 'target_id' => 1, 'pitch' => -5, 'yaw' => 45, 'text' => 'Ke Masjid Taqwa'],
                ['type' => 'info', 'pitch' => 25, 'yaw' => 180, 'text' => '<h4>Tugu Pena Metro</h4>Monumen pendidikan dan literasi']
            ],
            6 => [ // Mall Pelayanan Publik Depan
                ['type' => 'navigate', 'target_id' => 7, 'pitch' => -5, 'yaw' => 45, 'text' => 'Ke Area Pendaftaran'],
                ['type' => 'navigate', 'target_id' => 8, 'pitch' => -5, 'yaw' => 90, 'text' => 'Ke Area Pelayanan'],
                ['type' => 'navigate', 'target_id' => 4, 'pitch' => -8, 'yaw' => 270, 'text' => 'Ke Tugu Metrem'],
                ['type' => 'info', 'pitch' => 10, 'yaw' => 0, 'text' => '<h4>Mall Pelayanan Publik</h4>Pusat pelayanan pemerintahan']
            ],
            7 => [ // Mall Pelayanan Publik Pendaftaran
                ['type' => 'navigate', 'target_id' => 6, 'pitch' => -8, 'yaw' => 225, 'text' => 'Ke Area Depan'],
                ['type' => 'navigate', 'target_id' => 8, 'pitch' => -5, 'yaw' => 135, 'text' => 'Ke Area Pelayanan'],
                ['type' => 'info', 'pitch' => 5, 'yaw' => 0, 'text' => '<h4>Area Pendaftaran</h4>Tempat registrasi layanan publik']
            ],
            8 => [ // Mall Pelayanan Publik Pelayanan
                ['type' => 'navigate', 'target_id' => 6, 'pitch' => -8, 'yaw' => 270, 'text' => 'Ke Area Depan'],
                ['type' => 'navigate', 'target_id' => 7, 'pitch' => -5, 'yaw' => 315, 'text' => 'Ke Area Pendaftaran'],
                ['type' => 'info', 'pitch' => 8, 'yaw' => 90, 'text' => '<h4>Area Pelayanan</h4>Counter layanan administrasi']
            ],
            9 => [ // Taman Metro Indonesia Indah Kolam Renang
                ['type' => 'navigate', 'target_id' => 10, 'pitch' => -5, 'yaw' => 180, 'text' => 'Ke Area Taman'],
                ['type' => 'info', 'pitch' => 0, 'yaw' => 0, 'text' => '<h4>Kolam Renang Metro Indonesia Indah</h4>Fasilitas olahraga air keluarga']
            ],
            10 => [ // Taman Metro Indonesia Indah Taman
                ['type' => 'navigate', 'target_id' => 9, 'pitch' => -5, 'yaw' => 0, 'text' => 'Ke Kolam Renang'],
                ['type' => 'info', 'pitch' => 5, 'yaw' => 180, 'text' => '<h4>Taman Rekreasi Metro Indonesia Indah</h4>Tempat wisata keluarga populer']
            ],
            11 => [ // Rumah Informasi Sejarah Swoning Dalam 1
                ['type' => 'navigate', 'target_id' => 13, 'pitch' => -8, 'yaw' => 180, 'text' => 'Keluar ke Area Luar'],
                ['type' => 'navigate', 'target_id' => 14, 'pitch' => -5, 'yaw' => 90, 'text' => 'Ke Area Luar 2'],
                ['type' => 'info', 'pitch' => 10, 'yaw' => 0, 'text' => '<h4>Interior Rumah Bersejarah</h4>Peninggalan arsitektur kolonial']
            ],
            13 => [ // Rumah Informasi Sejarah Swoning Luar 1
                ['type' => 'navigate', 'target_id' => 11, 'pitch' => -8, 'yaw' => 0, 'text' => 'Masuk ke Dalam Rumah'],
                ['type' => 'navigate', 'target_id' => 14, 'pitch' => -5, 'yaw' => 135, 'text' => 'Ke Area Luar 2'],
                ['type' => 'info', 'pitch' => 8, 'yaw' => 270, 'text' => '<h4>Halaman Rumah Bersejarah</h4>Bangunan cagar budaya Kota Metro']
            ],
            14 => [ // Rumah Informasi Sejarah Swoning Luar 2
                ['type' => 'navigate', 'target_id' => 11, 'pitch' => -8, 'yaw' => 270, 'text' => 'Masuk ke Dalam Rumah'],
                ['type' => 'navigate', 'target_id' => 13, 'pitch' => -5, 'yaw' => 315, 'text' => 'Ke Area Luar 1'],
                ['type' => 'info', 'pitch' => 12, 'yaw' => 90, 'text' => '<h4>Sisi Samping Rumah</h4>Detail arsitektur tradisional']
            ],
            15 => [ // Jembatan Gantung Pelita Sumbersari
                ['type' => 'info', 'pitch' => 0, 'yaw' => 0, 'text' => '<h4>Jembatan Gantung Pelita</h4>Jembatan penyeberangan bersejarah'],
                ['type' => 'info', 'pitch' => -15, 'yaw' => 180, 'text' => '<h4>Sungai Way Sekampung</h4>Aliran sungai utama Kota Metro']
            ],
            16 => [ // Kantor Pemda Kota Metro
                ['type' => 'info', 'pitch' => 10, 'yaw' => 0, 'text' => '<h4>Kantor Pemerintah Daerah</h4>Pusat administrasi Kota Metro'],
                ['type' => 'info', 'pitch' => -5, 'yaw' => 180, 'text' => '<h4>Halaman Kantor Pemda</h4>Area parkir dan taman']
            ]
        ];

        // Update data hotspot untuk setiap sphere
        foreach ($hotspotData as $sphereId => $hotspots) {
            DB::table('tb_sphere')
                ->where('id_sphere', $sphereId)
                ->update([
                    'hotspot_data' => json_encode($hotspots)
                ]);
        }

        $this->command->info('Sphere hotspot data seeded successfully!');
    }
}
