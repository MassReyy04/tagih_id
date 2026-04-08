<?php

namespace App\Console\Commands;

use App\Models\MonitoringPenagihan;
use App\Services\NomorSuratService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RenumberBamSuratCommand extends Command
{
    protected $signature = 'app:renumber-bam-surat {--dry-run}';

    protected $description = 'Normalisasi semua nomor_surat ke format BAM nn/tanggal/bulan(Romawi)/tahun';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $total = MonitoringPenagihan::query()->count();
        if ($total === 0) {
            $this->info('Tidak ada data monitoring_penagihan.');

            return self::SUCCESS;
        }

        $rows = MonitoringPenagihan::query()
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        $preview = [];
        $byDate = $rows->groupBy(fn ($m) => $m->tanggal->toDateString());

        foreach ($byDate as $dateStr => $items) {
            $seq = 0;
            foreach ($items as $m) {
                $seq++;
                $new = NomorSuratService::formatNomorSurat($m->tanggal, $seq);
                if ($m->nomor_surat !== $new) {
                    $preview[] = [
                        'id' => $m->id,
                        'lama' => $m->nomor_surat,
                        'baru' => $new,
                    ];
                }
            }
        }

        if ($dryRun) {
            $this->info('[DRY RUN] Perubahan yang akan dilakukan:');
            $this->table(['id', 'nomor_surat (lama)', 'nomor_surat (baru)'], array_map(fn ($p) => [$p['id'], $p['lama'], $p['baru']], $preview));
            if (count($preview) === 0) {
                $this->info('Semua nomor sudah sesuai format baru.');
            }

            return self::SUCCESS;
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $m) {
                MonitoringPenagihan::query()->whereKey($m->id)->update([
                    'nomor_surat' => '__MIGRATE__'.$m->id,
                ]);
            }

            $byDate = $rows->groupBy(fn ($m) => $m->tanggal->toDateString());

            foreach ($byDate as $items) {
                $seq = 0;
                foreach ($items as $m) {
                    $seq++;
                    $new = NomorSuratService::formatNomorSurat($m->tanggal, $seq);
                    MonitoringPenagihan::query()->whereKey($m->id)->update([
                        'nomor_surat' => $new,
                    ]);
                }
            }
        });

        $this->info("Berhasil menormalisasi {$total} baris ke format BAM nn/tanggal/bulan(Romawi)/tahun.");

        return self::SUCCESS;
    }
}
