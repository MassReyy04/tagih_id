<?php

namespace App\Services;

use App\Models\MonitoringPenagihan;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class NomorSuratService
{
    /**
     * Nomor BAM: BAM n/tanggal/bulan/tahun
     *
     * - n: urutan kunjungan pada tanggal yang sama (reset setiap hari)
     * - tanggal: dd
     * - bulan: mm dalam Romawi (I, II, III, ...)
     * - tahun: yyyy
     */
    public function generateNomorSurat(CarbonInterface $tanggal): string
    {
        $date = $tanggal->copy()->startOfDay();

        return DB::transaction(function () use ($date) {
            $dateStr = $date->toDateString();

            // Ambil semua nomor surat yang ada pada tanggal tersebut
            $existingNumbers = MonitoringPenagihan::query()
                ->whereDate('tanggal', $dateStr)
                ->lockForUpdate()
                ->pluck('nomor_surat')
                ->toArray();

            // Ekstrak angka urutan dari string 'BAM n/dd/MM/yyyy'
            $usedSequences = [];
            foreach ($existingNumbers as $no) {
                // Format: BAM n/dd/MM/yyyy -> ambil bagian 'n'
                if (preg_match('/BAM\s+(\d+)\//i', $no, $matches)) {
                    $usedSequences[] = (int) $matches[1];
                }
            }

            // Cari angka terkecil (mulai dari 1) yang belum digunakan (gap filling)
            $sequence = 1;
            while (in_array($sequence, $usedSequences)) {
                $sequence++;
            }

            return self::formatNomorSurat($date, $sequence);
        });
    }

    /**
     * Mengurutkan ulang (re-index) semua nomor surat pada tanggal tertentu.
     * Berguna setelah ada penghapusan data agar nomor tidak ada yang bolong (gap).
     */
    public function reindexNomorSurat(CarbonInterface $tanggal): void
    {
        $dateStr = $tanggal->toDateString();

        DB::transaction(function () use ($dateStr, $tanggal) {
            $items = MonitoringPenagihan::query()
                ->whereDate('tanggal', $dateStr)
                ->orderBy('id') // Urutkan berdasarkan waktu input asli (ID)
                ->lockForUpdate()
                ->get();

            foreach ($items as $index => $m) {
                $newNumber = self::formatNomorSurat($tanggal, $index + 1);
                if ($m->nomor_surat !== $newNumber) {
                    $m->update(['nomor_surat' => $newNumber]);
                }
            }
        });
    }

    /**
     * Satu string nomor surat untuk tanggal + urutan harian (satu basis dengan generate).
     */
    public static function formatNomorSurat(CarbonInterface $tanggal, int $sequence): string
    {
        $d = $tanggal->copy()->startOfDay();
        $n = max(1, $sequence);
        $romanMonth = self::getRomanNumeral((int) $d->format('m'));

        return sprintf(
            'BAM %02d/%s/%s/%s',
            $n,
            $d->format('d'),
            $romanMonth,
            $d->format('Y')
        );
    }

    /**
     * Konversi angka bulan ke Romawi.
     */
    private static function getRomanNumeral(int $month): string
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        return $romans[$month] ?? '';
    }
}
