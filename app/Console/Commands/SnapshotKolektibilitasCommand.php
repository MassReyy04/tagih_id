<?php

namespace App\Console\Commands;

use App\Services\KolektibilitasService;
use Illuminate\Console\Command;

class SnapshotKolektibilitasCommand extends Command
{
    protected $signature = 'kolektibilitas:snapshot {--date= : Tanggal snapshot (Y-m-d), default hari ini}';

    protected $description = 'Simpan snapshot harian kinerja kolektibilitas';

    public function handle(KolektibilitasService $service): int
    {
        $tanggal = $this->option('date')
            ? now()->parse($this->option('date'))->startOfDay()
            : now()->startOfDay();

        $snapshot = $service->saveDailySnapshot($tanggal);

        $this->info('Snapshot kolektibilitas tersimpan untuk '.$snapshot->tanggal->toDateString());

        return self::SUCCESS;
    }
}
