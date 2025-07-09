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
        Schema::create('tb_sphere_history', function (Blueprint $table) {
            $table->id('id_sphere_history');
            $table->unsignedBigInteger('id_sphere');
            $table->dateTime('datetime');
            $table->timestamps();
            
            $table->foreign('id_sphere')->references('id_sphere')->on('tb_sphere');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_sphere_history');
    }
};
