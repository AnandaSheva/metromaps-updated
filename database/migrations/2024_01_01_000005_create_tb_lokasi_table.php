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
        Schema::create('tb_lokasi', function (Blueprint $table) {
            $table->id('id_lokasi');
            $table->string('nama_lokasi', 200);
            $table->text('alamat');
            $table->string('lat', 200);
            $table->string('lang', 200);
            $table->unsignedBigInteger('id_kategori');
            $table->text('keterangan');
            $table->timestamps();
            
            $table->foreign('id_kategori')->references('id_kategori')->on('tb_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_lokasi');
    }
};
