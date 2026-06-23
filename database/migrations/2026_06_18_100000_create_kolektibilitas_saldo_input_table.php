<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kolektibilitas_saldo_input', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->decimal('saldo_lancar', 18, 2)->default(0);
            $table->decimal('saldo_kurang_lancar', 18, 2)->default(0);
            $table->decimal('saldo_diragukan', 18, 2)->default(0);
            $table->decimal('saldo_macet', 18, 2)->default(0);
            $table->decimal('saldo_bermasalah', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kolektibilitas_saldo_input');
    }
};
