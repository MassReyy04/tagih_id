<?php

namespace Database\Seeders;

use App\Models\KolektibilitasBermasalah;
use App\Models\MonitoringPenagihan;
use App\Models\User;
use App\Services\KolektibilitasService;
use Illuminate\Database\Seeder;

class KolektibilitasContohSeeder extends Seeder
{
    /** @var list<string> */
    public const NOMOR_INDUK = [
        'MITRA-001', 'MITRA-002', 'MITRA-003', 'MITRA-004', 'MITRA-005',
        'MITRA-006', 'MITRA-007', 'MITRA-008', 'MITRA-009', 'MITRA-010',
    ];

    public function run(): void
    {
        $petugas = User::query()->where('role', 'petugas')->first()
            ?? User::query()->where('role', 'admin')->first();

        if (! $petugas) {
            $this->command?->error('Tidak ada user petugas/admin. Jalankan php artisan db:seed terlebih dahulu.');

            return;
        }

        $tanggal = now()->toDateString();

        $rows = [
            ['Budi Santoso', 'Usaha Tani Budi', 'MITRA-001', 180_000_000, 120_000_000, 15],
            ['Siti Aminah', 'Kebun Siti', 'MITRA-002', 100_000_000, 85_000_000, 28],
            ['Ahmad Rizki', 'Koperasi Rizki', 'MITRA-003', 250_000_000, 200_000_000, 45],
            ['Dewi Lestari', 'Warung Dewi', 'MITRA-004', 80_000_000, 50_000_000, 90],
            ['Joko Widodo', 'Toko Joko', 'MITRA-005', 50_000_000, 30_000_000, 150],
            ['Rina Marlina', 'UD Rina', 'MITRA-006', 100_000_000, 75_000_000, 200],
            ['Agus Pratama', 'Bengkel Agus', 'MITRA-007', 60_000_000, 40_000_000, 250],
            ['Fitri Handayani', 'Perkebunan Fitri', 'MITRA-008', 400_000_000, 300_000_000, 320],
            ['Hendra Kusuma', 'CV Hendra', 'MITRA-009', 200_000_000, 150_000_000, 400],
            ['Maya Sari', 'Florist Maya', 'MITRA-010', 80_000_000, 60_000_000, 10],
        ];

        foreach ($rows as $index => [$nama, $usaha, $nim, $nilai, $sisa, $hari]) {
            $seq = $index + 1;

            MonitoringPenagihan::query()->updateOrCreate(
                ['nomor_induk' => $nim, 'tanggal' => $tanggal],
                [
                    'nomor_surat' => sprintf('BAM %d/10/VI/2026', $seq),
                    'nama_mitra' => $nama,
                    'nama_usaha' => $usaha,
                    'alamat' => 'Jl. Contoh No. '.$seq.', PTPN IV Regional 4',
                    'no_hp' => '0812345678'.str_pad((string) $seq, 2, '0', STR_PAD_LEFT),
                    'nilai_pinjaman' => $nilai,
                    'sisa_pinjaman' => $sisa,
                    'hari_tunggakan' => $hari,
                    'alasan' => 'Contoh data kolektibilitas',
                    'catatan' => 'Data demo — MITRA-00'.$seq,
                    'tanggal' => $tanggal,
                    'user_id' => $petugas->id,
                ]
            );
        }

        // Kunjungan lama Budi (tidak dipakai — hanya untuk demo "data terbaru yang dihitung")
        MonitoringPenagihan::query()->updateOrCreate(
            [
                'nomor_induk' => 'MITRA-001',
                'tanggal' => now()->subMonths(5)->toDateString(),
            ],
            [
                'nomor_surat' => 'BAM 99/10/I/2026',
                'nama_mitra' => 'Budi Santoso',
                'nama_usaha' => 'Usaha Tani Budi',
                'alamat' => 'Jl. Contoh No. 1, PTPN IV Regional 4',
                'no_hp' => '081234567801',
                'nilai_pinjaman' => 200_000_000,
                'sisa_pinjaman' => 150_000_000,
                'hari_tunggakan' => 40,
                'alasan' => 'Kunjungan lama (tidak dipakai di perhitungan)',
                'catatan' => 'Data demo lama — MITRA-001',
                'tanggal' => now()->subMonths(5)->toDateString(),
                'user_id' => $petugas->id,
            ]
        );

        KolektibilitasBermasalah::query()->updateOrCreate(
            ['tanggal' => $tanggal],
            ['saldo_bermasalah' => 500_000_000]
        );

        app(KolektibilitasService::class)->saveDailySnapshot(now()->startOfDay());

        $this->command?->info('10 data contoh kolektibilitas berhasil dimasukkan (+ 1 kunjungan lama Budi).');
        $this->command?->info('Saldo bermasalah: Rp 500.000.000');
        $this->command?->info('Buka menu Kolektibilitas sebagai admin untuk melihat hasilnya.');
    }
}
