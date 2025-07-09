<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_kesehatan_posyandu', function (Blueprint $table) {
            $table->id('id_kesehatan_posyandu');
            $table->string('nama_surveyor', 50);
            $table->string('posisi', 50);
            $table->unsignedBigInteger('id_desa');
            $table->float('posyandu_latitude');
            $table->float('posyandu_longitude');
            $table->float('posyandu_altitude');
            $table->float('posyandu_precision');
            $table->string('nama_posyandu', 50);
            $table->string('nama_ketua', 50);
            $table->integer('rw');
            $table->integer('rt');
            $table->string('alamat', 100);
            $table->integer('jumlah_kader');
            $table->string('gedung_posyandu', 50);
            $table->string('kondisi_posyandu', 50);
            $table->string('papan_nama_posyandu', 50);
            $table->string('kondisi_papan_nama', 50);
            $table->string('meja_posyandu', 50);
            $table->integer('jumlah_meja');
            $table->string('timbangan_dacin', 50);
            $table->string('timbangan_bayi', 50);
            $table->string('timbangan_dewasa', 50);
            $table->string('pengukur_tinggi_badan', 50);
            $table->string('meja_periksa', 50);
            $table->integer('jumlah_meja2');
            $table->string('kursi_perika', 50);
            $table->integer('jumlah_kursi');
            $table->string('kursi_petugas', 50);
            $table->integer('jumlah_kursi3');
            $table->string('kursi_tunggu', 50);
            $table->integer('jumlah_kursi4');
            $table->string('tensimeter', 50);
            $table->string('glukotest', 50);
            $table->string('alat_ukur_lingkar_perut', 50);
            $table->string('alat_ukur_fungsi_paru', 50);
            $table->string('alat_detektor_alkohol', 50);
            $table->string('kolesterol_test', 50);
            $table->string('asam_urat_test', 50);
            $table->string('foto_kondisi_posyandu_url', 1000);
            $table->integer('balita');
            $table->integer('remaja');
            $table->integer('ibu_hamil');
            $table->integer('lansia');
            $table->integer('balita5');
            $table->integer('remaja6');
            $table->integer('ibu_hamil7');
            $table->integer('lansia8');
            $table->integer('status_aktif');
            $table->timestamps();
            
            $table->foreign('id_desa')->references('id_desa')->on('tb_desa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_kesehatan_posyandu');
    }
};
