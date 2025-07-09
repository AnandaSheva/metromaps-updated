<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
   public function index()
{
    $cctvs = DB::table('tb_cctv')
        ->orderBy('nama_lokasi', 'asc')
        ->get();

    $pemerintahan = DB::table('unitkerja')
        ->where(function ($q) {
            $q->where('unit', 'like', '%DINAS%')
              ->orWhere('unit', 'like', '%KECAMATAN%')
              ->orWhere('unit', 'like', '%KELURAHAN%')
              ->orWhere('unit', 'like', '%BADAN%')
              ->orWhere('unit', 'like', '%SEKRETARIAT%')
              ->orWhere('unit', 'like', '%INSPEKTORAT%');
        })
        ->where(function ($q) {
            $q->where('unit', 'not like', '%SEKOLAH%')
              ->where('unit', 'not like', '%SMP%')
              ->where('unit', 'not like', '%SD%')
              ->where('unit', 'not like', '%TK%')
              ->where('unit', 'not like', '%PAUD%')
              ->where('unit', 'not like', '%UPTD%')
              ->where('unit', 'not like', '%PENDIDIKAN%')
              ->where('unit', 'not like', '%STIKES%')
              ->where('unit', 'not like', '%UNIV%')
              ->where('unit', 'not like', '%SANGGAR%')
              ->where('unit', 'not like', '%KOPERASI%')
              ->where('unit', 'not like', '%MIKRO%')
              ->where('unit', 'not like', '%KECIL%')
              ->where('unit', 'not like', '%MENENGAH%')
              ->where('unit', 'not like', '%PENANAMAN MODAL%')
              ->where('unit', 'not like', '%PELAYANAN TERPADU%')
              ->where('unit', 'not like', '%KETAHANAN PANGAN%')
              ->where('unit', 'not like', '%PERTANIAN%')
              ->where('unit', 'not like', '%PERIKANAN%')
              ->where('unit', 'not like', '%KESATUAN BANGSA%')
              ->where('unit', 'not like', '%POLITIK%')
              ->where('unit', 'not like', '%KEPENDUDUKAN%')
              ->where('unit', 'not like', '%PENCATATAN SIPIL%')
              ->where('unit', 'not like', '%KEPEGAWAIAN%')
              ->where('unit', 'not like', '%PENGEMBANGAN%')
              ->where('unit', 'not like', '%SUMBER DAYA MANUSIA%')
              ->where('unit', 'not like', '%LINGKUNGAN HIDUP%')
              ->where('unit', 'not like', '%PERDAGANGAN%')
              ->where('unit', 'not like', '%PENDIDIKAN%')
              ->where('unit', 'not like', '%KEBUDAYAAN%')
              ->where('unit', 'not like', '%PERENCANAAN%')
              ->where('unit', 'not like', '%PEMBANGUNAN%')
              ->where('unit', 'not like', '%SOSIAL%')
              ->where('unit', 'not like', '%KEPEMUDAAN%')
              ->where('unit', 'not like', '%OLAHRAGA%')
              ->where('unit', 'not like', '%PARIWISATA%')
              ->where('unit', 'not like', '%PURWOSARI%')
              ->where('unit', 'not like', '%PENANGGULANGAN%')
              ->where('unit', 'not like', '%BENCANA%')
              ->where('unit', 'not like', '%BANJARSARI%')
              ->where('unit', 'not like', '%KARANG REJO%');
        })
        ->get();

    $pendidikan = DB::table('tb_lokasi')->where('id_kategori', 8)->get();
    $spheres = DB::table('tb_sphere')->get();

    $layanan = DB::table('tb_lokasi')
    ->where(function ($q) {
        $q->where('nama_lokasi', 'like', '%MASJID%')
          ->orWhere('nama_lokasi', 'like', '%MUSHOLA%')
          ->orWhere('nama_lokasi', 'like', '%PURA%')
          ->orWhere('nama_lokasi', 'like', '%VIHARA%')
          ->orWhere('nama_lokasi', 'like', '%MALL PELAYANAN%')
          ->orWhere('nama_lokasi', 'like', '%TAMAN KOTA%')
          ->orWhere('nama_lokasi', 'like', '%GEREJA%');
    })
    ->get();

    $kesehatan = DB::table('tb_kesehatan_posyandu')
    ->where(function ($q) {
        $q->Where('nama_posyandu', 'like', '%POSYANDU%');
    })
    ->get();

    $pangan = DB::table('tb_pangan')->get();



return view('map', compact(
    'cctvs',
    'pemerintahan',
    'pendidikan',
    'spheres',
    'layanan',
    'kesehatan',
    'pangan'
));

}

}