<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'admin@politeknikjambi.ac.id' => 'admin@ptpn.ac.id',
            'petugas@politeknikjambi.ac.id' => 'petugas@ptpn.ac.id',
        ];

        foreach ($map as $old => $new) {
            DB::table('users')->where('email', $old)->update(['email' => $new, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        $map = [
            'admin@ptpn.ac.id' => 'admin@politeknikjambi.ac.id',
            'petugas@ptpn.ac.id' => 'petugas@politeknikjambi.ac.id',
        ];

        foreach ($map as $new => $old) {
            DB::table('users')->where('email', $new)->update(['email' => $old, 'updated_at' => now()]);
        }
    }
};
