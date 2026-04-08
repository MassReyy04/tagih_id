<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_penagihan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat', 128);
            $table->string('nama_mitra');
            $table->string('nama_usaha');
            $table->string('nomor_induk');
            $table->text('alamat');
            $table->string('no_hp', 32);
            $table->decimal('nilai_pinjaman', 18, 2)->default(0);
            $table->decimal('sisa_pinjaman', 18, 2)->default(0);
            $table->text('alasan')->nullable();
            $table->text('janji')->nullable();
            $table->text('catatan')->nullable();
            $table->text('kebutuhan')->nullable();
            $table->date('tanggal');
            $table->string('signature_mitra')->nullable();
            $table->string('signature_petugas')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['tanggal', 'nomor_surat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_penagihan');
    }
};
