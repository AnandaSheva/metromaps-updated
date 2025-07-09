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
        Schema::create('tb_cctv_history', function (Blueprint $table) {
            $table->id('id_cctv_history');
            $table->unsignedBigInteger('id_cctv');
            $table->dateTime('datetime');
            $table->timestamps();
            
            $table->foreign('id_cctv')->references('id_cctv')->on('tb_cctv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_cctv_history');
    }
};
