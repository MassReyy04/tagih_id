<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_penagihan', function (Blueprint $table) {
            $table->unsignedInteger('hari_tunggakan')->default(0)->after('sisa_pinjaman');
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_penagihan', function (Blueprint $table) {
            $table->dropColumn('hari_tunggakan');
        });
    }
};
