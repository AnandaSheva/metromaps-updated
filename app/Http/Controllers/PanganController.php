<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanganController extends Controller
{
    public function getData(Request $request)
    {
        $indikator = $request->query('indikator');
        $tahun = $request->query('tahun');

        // Debug mode - jika parameter debug=1, return info database
        if ($request->query('debug') == '1') {
            try {
                $panganCount = DB::table('tb_pangan')->count();
                $desaCount = DB::table('tb_desa')->count();
                
                // Cek kolom yang tersedia
                $panganColumns = DB::getSchemaBuilder()->getColumnListing('tb_pangan');
                $desaColumns = DB::getSchemaBuilder()->getColumnListing('tb_desa');
                
                // Sample data
                $panganSample = DB::table('tb_pangan')->limit(3)->get();
                $desaSample = DB::table('tb_desa')->limit(3)->get();
                
                return response()->json([
                    'debug_info' => [
                        'pangan_count' => $panganCount,
                        'desa_count' => $desaCount,
                        'pangan_columns' => $panganColumns,
                        'desa_columns' => $desaColumns,
                        'pangan_sample' => $panganSample,
                        'desa_sample' => $desaSample
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'debug_error' => $e->getMessage()
                ], 500);
            }
        }

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
            // Query dengan join ke tb_desa untuk mendapatkan nama desa
            $query = DB::table('tb_pangan')
                ->join('tb_desa', 'tb_pangan.id_desa', '=', 'tb_desa.id_desa')
                ->where('tb_pangan.tahun', $tahun)
                ->whereNotNull($column); // FIX: Use the correct variable $column

            // Select fields: id_desa, nama_desa, kecamatan, nilai, and the correct village code
            $query->select(
                'tb_desa.id_desa',
                'tb_desa.desa as nama_desa',
                'tb_desa.kecamatan',
                'tb_desa.kode_desa as kode_desa',
                "$column as nilai" // FIX: Use the correct variable $column
            );

            $data = $query->get()
                ->map(function ($item) use ($indikator, $tahun) {
                    $nilai = floatval($item->nilai);
                    $kategori = $this->getKategori($nilai, $indikator);

                    return [
                        'id_desa' => $item->id_desa,
                        'kode_desa' => $item->kode_desa,
                        'nama_desa' => $item->nama_desa,
                        'kecamatan' => $item->kecamatan,
                        'tahun' => $tahun,
                        'indikator' => $indikator,
                        'nilai' => $nilai,
                        'kategori' => $kategori
                    ];
                });

            return response()->json($data);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching pangan data: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Terjadi kesalahan internal server: ' . $e->getMessage(),
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

    public function debug(Request $request)
    {
        try {
            // Test basic database connection
            $panganCount = DB::table('tb_pangan')->count();
            $desaCount = DB::table('tb_desa')->count();
            
            // Get all desa names
            $allDesa = DB::table('tb_desa')
                ->select('id_desa', 'desa', 'kecamatan')
                ->orderBy('desa')
                ->get();
            
            // Get available years in tb_pangan
            $availableYears = DB::table('tb_pangan')
                ->select('tahun')
                ->distinct()
                ->orderBy('tahun')
                ->get();
            
            // Test join query with correct column name
            $joinTest = DB::table('tb_pangan')
                ->join('tb_desa', 'tb_pangan.id_desa', '=', 'tb_desa.id_desa')
                ->select('tb_desa.desa', 'tb_desa.kecamatan', 'tb_pangan.tahun', 'tb_pangan.nilai_ikkkp')
                ->limit(10)
                ->get();
            
            // Check missing data - desa that don't have pangan data
            $desaWithoutPangan = DB::table('tb_desa')
                ->leftJoin('tb_pangan', 'tb_desa.id_desa', '=', 'tb_pangan.id_desa')
                ->whereNull('tb_pangan.id_desa')
                ->select('tb_desa.desa', 'tb_desa.kecamatan')
                ->get();
            
            // Check for Hadimulyo specifically
            $hadimulyo = DB::table('tb_desa')
                ->where('desa', 'like', '%Hadimulyo%')
                ->get();
            
            // Check pangan data for Hadimulyo
            $hadimulyo_pangan = DB::table('tb_pangan')
                ->join('tb_desa', 'tb_pangan.id_desa', '=', 'tb_desa.id_desa')
                ->where('tb_desa.desa', 'like', '%Hadimulyo%')
                ->select('tb_desa.desa', 'tb_pangan.*')
                ->get();
                
            return response()->json([
                'status' => 'success',
                'pangan_count' => $panganCount,
                'desa_count' => $desaCount,
                'all_desa' => $allDesa,
                'available_years' => $availableYears,
                'join_sample' => $joinTest,
                'desa_without_pangan' => $desaWithoutPangan,
                'hadimulyo_desa' => $hadimulyo,
                'hadimulyo_pangan' => $hadimulyo_pangan
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}