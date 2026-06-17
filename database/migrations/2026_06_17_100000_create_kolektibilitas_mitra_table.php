<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kolektibilitas_mitra', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_induk')->unique();
            $table->string('nama_mitra')->nullable();
            $table->unsignedInteger('hari_tunggakan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kolektibilitas_mitra');
    }
};
