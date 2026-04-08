<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Akun demo (tampil di halaman login untuk pengujian)
    |--------------------------------------------------------------------------
    */
    'demo_accounts' => [
        [
            'role' => 'Admin',
            'label' => 'Akun admin (dari DatabaseSeeder)',
            'email' => 'admin@ptpn.ac.id',
            'password' => 'password',
            'accent' => 'orange',
        ],
        [
            'role' => 'Petugas Penagih',
            'label' => 'Input berita acara (bukan admin)',
            'email' => 'petugas@ptpn.ac.id',
            'password' => 'password',
            'accent' => 'green',
        ],
    ],

];
