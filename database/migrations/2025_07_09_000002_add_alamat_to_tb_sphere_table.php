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
            $table->text('alamat')->nullable()->after('nama_lokasi')->comment('Alamat lokasi sphere');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_sphere', function (Blueprint $table) {
            $table->dropColumn('alamat');
        });
    }
};
