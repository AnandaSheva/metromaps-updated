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
        Schema::table('tb_sphere', function (Blueprint $table) {
            $table->json('hotspot_data')->nullable()->after('last_seen')->comment('Data hotspot untuk navigasi antar sphere');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_sphere', function (Blueprint $table) {
            $table->dropColumn('hotspot_data');
        });
    }
};
