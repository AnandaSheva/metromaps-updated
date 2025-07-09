<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanganApiController extends Controller
{
    public function getData(Request $request)
    {
        $indikator = $request->query('indikator');
        $tahun = $request->query('tahun');

        // Validation
        if (!$indikator || !$tahun) {
            return response()->json([
                'error' => 'Parameter indikator dan tahun diperlukan',
                'data' => []
            ], 400);
        }

        // Mapping indikator ke nama kolom
        $indikatorMap = [
            'indeks' => 'nilai_ikkkp',
            'sarana' => 'nilai_spp',
            'rasio_10' => 'nilai_rpt',
            'akses' => 'nilai_tap',
            'air_bersih' => 'nilai_prttab',
            'rasio_nakes' => 'nilai_rppkttk'
        ];

        if (!array_key_exists($indikator, $indikatorMap)) {
            return response()->json([
                'error' => 'Indikator tidak valid',
                'valid_indicators' => array_keys($indikatorMap),
                'data' => []
            ], 400);
        }

        $column = $indikatorMap[$indikator];

        try {
            $data = DB::table('tb_pangan')
                ->join('tb_desa', 'tb_pangan.id_desa', '=', 'tb_desa.id_desa')
                ->select('tb_desa.nama_desa', 'tb_desa.koordinat', "tb_pangan.$column as nilai")
                ->where('tb_pangan.tahun', $tahun)
                ->whereNotNull('tb_desa.koordinat')
                ->whereNotNull("tb_pangan.$column")
                ->get()
                ->map(function ($item) use ($indikator) {
                    $nilai = floatval($item->nilai);
                    $kategori = $this->getKategori($nilai, $indikator);

                    return [
                        'nama_desa' => $item->nama_desa,
                        'koordinat' => $item->koordinat,
                        'nilai' => $nilai,
                        'kategori' => $kategori
                    ];
                });

            return response()->json($data);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching pangan data: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Terjadi kesalahan internal server',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get kategori based on nilai and indikator
     */
    private function getKategori($nilai, $indikator)
    {
        switch ($indikator) {
            case 'indeks':
                // Nilai sudah dalam bentuk persentase (0-100)
                if ($nilai < 20) return 'sangat_rentan';
                elseif ($nilai < 40) return 'rentan';
                elseif ($nilai < 60) return 'agak_rentan';
                elseif ($nilai < 70) return 'agak_tahan';
                elseif ($nilai < 85) return 'tahan';
                else return 'sangat_tahan';

            case 'sarana':
            case 'rasio_10':
            case 'akses':
                // Untuk nilai desimal, kita perlu mengkonversi atau menggunakan range yang berbeda
                // Asumsikan nilai 0-1 dikonversi ke priority 1-6
                if ($nilai <= 0.16) return 'prioritas_1';
                elseif ($nilai <= 0.33) return 'prioritas_2';
                elseif ($nilai <= 0.50) return 'prioritas_3';
                elseif ($nilai <= 0.66) return 'prioritas_4';
                elseif ($nilai <= 0.83) return 'prioritas_5';
                else return 'prioritas_6';

            case 'air_bersih':
                if ($nilai >= 70) return '>= 70';
                elseif ($nilai >= 60) return '60 -< 70';
                elseif ($nilai >= 50) return '50 -< 60';
                elseif ($nilai >= 40) return '40 -< 50';
                elseif ($nilai >= 30) return '30 -< 40';
                else return '< 30';

            case 'rasio_nakes':
                if ($nilai >= 30) return '>= 30';
                elseif ($nilai >= 20) return '20 -< 30';
                elseif ($nilai >= 15) return '15 -< 20';
                elseif ($nilai >= 10) return '10 -< 15';
                elseif ($nilai >= 5) return '5 -< 10';
                else return '< 5';

            default:
                return 'tidak_ada_data';
        }
    }
}