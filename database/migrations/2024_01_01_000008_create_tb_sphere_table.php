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
        Schema::create('tb_sphere', function (Blueprint $table) {
            $table->id('id_sphere');
            $table->string('nama_lokasi', 100);
            $table->string('link', 100);
            $table->double('lat');
            $table->double('lang');
            $table->integer('count_seen')->default(0);
            $table->dateTime('last_seen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_sphere');
    }
};
