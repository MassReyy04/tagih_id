<?php

namespace App\Console\Commands;

use App\Models\MonitoringPenagihan;
use Database\Seeders\KolektibilitasContohSeeder;
use Illuminate\Console\Command;

class SeedKolektibilitasContohCommand extends Command
{
    protected $signature = 'kolektibilitas:seed-contoh {--fresh : Hapus data contoh sebelumnya lalu isi ulang}';

    protected $description = 'Masukkan 10 data contoh mitra untuk demo kinerja kolektibilitas';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $deleted = MonitoringPenagihan::query()
                ->whereIn('nomor_induk', KolektibilitasContohSeeder::NOMOR_INDUK)
                ->delete();

            $this->info("Data contoh lama dihapus: {$deleted} baris monitoring.");
        }

        $this->call('db:seed', ['--class' => KolektibilitasContohSeeder::class, '--force' => true]);

        return self::SUCCESS;
    }
}
