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
        Schema::create('tb_pangan', function (Blueprint $table) {
            $table->id('id_pangan');
            $table->unsignedBigInteger('id_desa');
            $table->integer('tahun');
            $table->integer('point_ikkkp');
            $table->integer('point_spp');
            $table->integer('point_rpt');
            $table->integer('point_tap');
            $table->integer('point_prttab');
            $table->integer('point_rppkttk');
            $table->float('nilai_ikkkp');
            $table->float('nilai_spp');
            $table->float('nilai_rpt');
            $table->float('nilai_tap');
            $table->float('nilai_prttab');
            $table->float('nilai_rppkttk');
            $table->timestamps();
            
            $table->foreign('id_desa')->references('id_desa')->on('tb_desa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pangan');
    }
};
