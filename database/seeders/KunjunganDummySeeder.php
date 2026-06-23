<?php

namespace Database\Seeders;

use App\Models\MonitoringPenagihan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KunjunganDummySeeder extends Seeder
{
    /** @var list<string> */
    private const ROMAN_MONTHS = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    public function run(): void
    {
        $this->removePetugasPenagihData();
        $this->clearPreviousDummyVisits();

        $petugasList = $this->ensurePetugasUsers();

        if ($petugasList === []) {
            $this->command?->error('Tidak ada user petugas. Jalankan php artisan db:seed terlebih dahulu.');

            return;
        }

        $year = (int) now()->year;
        $endMonth = (int) now()->month;
        $mitraSamples = [
            ['Budi Santoso', 'Usaha Tani Budi', 'MITRA-001'],
            ['Siti Aminah', 'Kebun Siti', 'MITRA-002'],
            ['Ahmad Rizki', 'Koperasi Rizki', 'MITRA-003'],
            ['Dewi Lestari', 'Warung Dewi', 'MITRA-004'],
            ['Joko Widodo', 'Toko Joko', 'MITRA-005'],
            ['Rina Marlina', 'UD Rina', 'MITRA-006'],
            ['Agus Pratama', 'Bengkel Agus', 'MITRA-007'],
            ['Fitri Handayani', 'Perkebunan Fitri', 'MITRA-008'],
            ['Hendra Kusuma', 'CV Hendra', 'MITRA-009'],
            ['Maya Sari', 'Florist Maya', 'MITRA-010'],
        ];

        $petugasByName = collect($petugasList)->keyBy(fn (User $u) => strtolower($u->name));
        $created = 0;
        $seqBase = 0;

        for ($month = 1; $month <= $endMonth; $month++) {
            foreach ([5, 15] as $day) {
                $tanggal = Carbon::create($year, $month, $day);

                foreach ($petugasList as $petugasIndex => $petugas) {
                    $seqBase++;
                    $created += $this->seedVisit(
                        $petugas,
                        $tanggal,
                        $mitraSamples,
                        $month,
                        $seqBase,
                        $petugasIndex
                    );
                }
            }

            $tanggal20 = Carbon::create($year, $month, 20);
            $day20Counts = [
                'andre' => 5 + ($month % 3),
                'andi wijaya' => 3 + ($month % 2),
                'rini kartika' => 1 + (($month + 1) % 2),
            ];

            foreach ($day20Counts as $nameKey => $count) {
                $petugas = $petugasByName->get($nameKey);

                if (! $petugas) {
                    continue;
                }

                for ($i = 0; $i < $count; $i++) {
                    $seqBase++;
                    $created += $this->seedVisit(
                        $petugas,
                        $tanggal20,
                        $mitraSamples,
                        $month,
                        $seqBase,
                        $i
                    );
                }
            }
        }

        $this->command?->info($created.' data dummy kunjungan berhasil dimasukkan.');
        $this->command?->info('Tanggal 5 & 15: 1 input per petugas. Tanggal 20: andre terbanyak, Rini Kartika tersedikit (bervariasi per bulan).');
        $this->command?->info('Petugas: '.implode(', ', array_map(fn (User $u) => $u->name, $petugasList)));
        $this->command?->info('Data Petugas Penagih telah dihapus.');
        $this->command?->info('Refresh dashboard untuk melihat grafik Data Kunjungan.');
    }

    private function removePetugasPenagihData(): void
    {
        $petugasPenagih = User::query()
            ->where('email', 'petugas@ptpn.ac.id')
            ->orWhere('name', 'Petugas Penagih')
            ->first();

        if (! $petugasPenagih) {
            return;
        }

        $deleted = MonitoringPenagihan::query()
            ->where('user_id', $petugasPenagih->id)
            ->delete();

        $this->command?->info('Menghapus '.$deleted.' data kunjungan milik Petugas Penagih.');
    }

    private function clearPreviousDummyVisits(): void
    {
        $deleted = MonitoringPenagihan::query()
            ->where('alasan', 'Data dummy kunjungan demo')
            ->delete();

        $this->command?->info('Membersihkan '.$deleted.' data dummy kunjungan sebelumnya.');
    }

    /**
     * @param  list<array{0: string, 1: string, 2: string}>  $mitraSamples
     */
    private function seedVisit(
        User $petugas,
        Carbon $tanggal,
        array $mitraSamples,
        int $month,
        int $seq,
        int $mitraOffset
    ): int {
        $mitra = $mitraSamples[($month + $tanggal->day + $mitraOffset) % count($mitraSamples)];
        $nomorSurat = sprintf(
            'BAM %d/10/%s/%d',
            $seq,
            self::ROMAN_MONTHS[$month],
            (int) $tanggal->year
        );

        MonitoringPenagihan::query()->updateOrCreate(
            ['nomor_surat' => $nomorSurat],
            [
                'nama_mitra' => $mitra[0],
                'nama_usaha' => $mitra[1],
                'nomor_induk' => $mitra[2],
                'alamat' => 'Jl. Demo Kunjungan No. '.$seq.', PTPN IV Regional 4',
                'no_hp' => '08123'.str_pad((string) $seq, 7, '0', STR_PAD_LEFT),
                'nilai_pinjaman' => 100_000_000 + ($seq * 1_000_000),
                'sisa_pinjaman' => 50_000_000 + ($seq * 500_000),
                'alasan' => 'Data dummy kunjungan demo',
                'catatan' => 'Dummy — '.$tanggal->translatedFormat('d F Y').' · Petugas: '.$petugas->name,
                'tanggal' => $tanggal->toDateString(),
                'user_id' => $petugas->id,
            ]
        );

        return 1;
    }

    /**
     * @return list<User>
     */
    private function ensurePetugasUsers(): array
    {
        $defaults = [
            ['email' => 'andre@ptpn.ac.id', 'name' => 'andre'],
            ['email' => 'petugas2@ptpn.ac.id', 'name' => 'Andi Wijaya'],
            ['email' => 'petugas3@ptpn.ac.id', 'name' => 'Rini Kartika'],
        ];

        $users = [];

        foreach ($defaults as $data) {
            $users[] = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'petugas',
                ]
            );
        }

        return $users;
    }
}
