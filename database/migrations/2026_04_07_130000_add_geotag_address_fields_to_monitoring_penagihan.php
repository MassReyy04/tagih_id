<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_penagihan', function (Blueprint $table) {
            $table->string('geo_jalan')->nullable()->after('longitude');
            $table->string('geo_kelurahan')->nullable()->after('geo_jalan');
            $table->string('geo_kecamatan')->nullable()->after('geo_kelurahan');
            $table->string('geo_kota')->nullable()->after('geo_kecamatan');
            $table->string('geo_provinsi')->nullable()->after('geo_kota');
            $table->string('geo_kode_pos', 16)->nullable()->after('geo_provinsi');
            $table->string('geo_negara')->nullable()->after('geo_kode_pos');
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_penagihan', function (Blueprint $table) {
            $table->dropColumn([
                'geo_jalan',
                'geo_kelurahan',
                'geo_kecamatan',
                'geo_kota',
                'geo_provinsi',
                'geo_kode_pos',
                'geo_negara',
            ]);
        });
    }
};
