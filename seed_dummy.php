<?php

use App\Models\MonitoringPenagihan;
use App\Models\User;
use App\Services\NomorSuratService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::first();
if (!$user) {
    echo "Tidak ada user di database. Silakan registrasi dulu.\n";
    exit(1);
}

$nomorSuratService = app(NomorSuratService::class);
$tanggal = Carbon::now();

$names = [
    'Budi Santoso', 'Siti Aminah', 'Agus Prayitno', 'Dewi Lestari', 'Eko Saputro',
    'Rina Wijaya', 'Bambang Hermawan', 'Ani Maryani', 'Dedi Kusnadi', 'Yanti Susanti',
    'Heri Kurniawan', 'Luluk Handayani', 'Andi Setiawan', 'Maya Indah', 'Rahmat Hidayat'
];

$businesses = [
    'Warung Makan Berkah', 'Penjahit Rapi', 'Toko Sembako Jaya', 'Bengkel Motor Maju', 'Cucian Steam Kinclong',
    'Salon Cantik', 'Fotocopy Cepat', 'Konter Pulsa Amanah', 'Laundry Bersih', 'Toko Plastik Laris',
    'Percetakan Kilat', 'Toko Bangunan Kokoh', 'Toko Baju Trendy', 'Warung Kopi Santai', 'Toko Mainan Anak'
];

$districts = ['Pasar Jambi', 'Telanaipura', 'Jambi Selatan', 'Jambi Timur', 'Kotabaru'];
$villages = ['Kelurahan A', 'Kelurahan B', 'Kelurahan C', 'Kelurahan D', 'Kelurahan E'];

echo "Sedang membuat 15 data dummy...\n";

for ($i = 0; $i < 15; $i++) {
    $nomorSurat = $nomorSuratService->generateNomorSurat($tanggal);
    
    MonitoringPenagihan::create([
        'nomor_surat' => $nomorSurat,
        'nama_mitra' => $names[$i],
        'nama_usaha' => $businesses[$i],
        'nomor_induk' => 'MITRA-'.str_pad($i + 1, 5, '0', STR_PAD_LEFT),
        'alamat' => 'Jl. Dummy No. '.($i + 1),
        'no_hp' => '0812345678'.($i % 10),
        'nilai_pinjaman' => rand(5000000, 20000000),
        'sisa_pinjaman' => rand(1000000, 5000000),
        'alasan' => 'Dummy data untuk testing paginasi.',
        'janji' => 'Akan dibayar bulan depan.',
        'catatan' => 'Kunjungan rutin seeder.',
        'kebutuhan' => 'Bantuan pemasaran.',
        'tanggal' => $tanggal,
        'signature_mitra' => 'signatures/dummy_mitra.png', // dummy path
        'signature_petugas' => 'signatures/dummy_petugas.png', // dummy path
        'foto' => null,
        'latitude' => -1.613,
        'longitude' => 103.593,
        'geo_jalan' => 'Jl. Jambi',
        'geo_kecamatan' => $districts[array_rand($districts)],
        'geo_kelurahan' => $villages[array_rand($villages)],
        'geo_kota' => 'Jambi',
        'geo_provinsi' => 'Jambi',
        'geo_kode_pos' => '36123',
        'user_id' => $user->id,
    ]);
}

echo "Berhasil membuat 15 data dummy. Silakan cek halaman Monitoring.\n";
