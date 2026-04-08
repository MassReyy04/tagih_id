<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@ptpn.ac.id'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'petugas@ptpn.ac.id'],
            [
                'name' => 'Petugas Penagih',
                'password' => Hash::make('password'),
                'role' => 'petugas',
            ]
        );
    }
}
