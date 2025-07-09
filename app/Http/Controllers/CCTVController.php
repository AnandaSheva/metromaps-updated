<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CCTVController extends Controller
{
    public function index()
    {
        $cctvs = DB::table('tb_cctv')
            ->orderByRaw('COALESCE(nama_lokasi, "CCTV") ASC')
            ->get();
        return view('map', compact('cctvs'));
    }

    public function increment($id)
    {
        // Update count_seen di tb_cctv
        DB::table('tb_cctv')->where('id_cctv', $id)->increment('count_seen');

        // Insert history view
        DB::table('tb_cctv_history')->insert([
            'id_cctv' => $id,
            'datetime' => now()
        ]);

        // Ambil count terbaru untuk response
        $updatedCount = DB::table('tb_cctv')
            ->where('id_cctv', $id)
            ->value('count_seen');

        return response()->json([
            'success' => true,
            'new_count' => $updatedCount,
            'message' => 'View count updated successfully'
        ]);
    }

    public function view($id)
    {
        return $this->increment($id);
    }
}
