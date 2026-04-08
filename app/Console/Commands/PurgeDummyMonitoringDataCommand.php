<?php

namespace App\Console\Commands;

use App\Models\MonitoringPenagihan;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeDummyMonitoringDataCommand extends Command
{
    protected $signature = 'data:purge-dummy-monitoring
                            {--users : Hapus juga akun admin@tagih.test & petugas@tagih.test}';

    protected $description = 'Hapus baris monitoring dummy (nomor_induk NIM dari MonitoringPenagihanDummySeeder) dan berkas storage terkait';

    /** @var list<string> */
    private array $dummyNomorInduk = [
        'NIM-0001', 'NIM-0002', 'NIM-0003', 'NIM-0004',
        'NIM-0005', 'NIM-0006', 'NIM-0007', 'NIM-0008',
        'NIM-1001', 'NIM-1002', 'NIM-1003', 'NIM-1004',
    ];

    /** @var list<string> */
    private array $dummyUserEmails = [
        'admin@tagih.test',
        'petugas@tagih.test',
    ];

    public function handle(): int
    {
        $query = MonitoringPenagihan::query()->whereIn('nomor_induk', $this->dummyNomorInduk);
        $count = $query->count();

        foreach ($query->cursor() as $monitoring) {
            foreach (['signature_mitra', 'signature_petugas', 'foto'] as $field) {
                $path = $monitoring->{$field};
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            $monitoring->delete();
        }

        $this->info("Monitoring dummy dihapus: {$count} baris.");

        if ($this->option('users')) {
            $n = User::query()->whereIn('email', $this->dummyUserEmails)->delete();
            $this->info("Pengguna uji dihapus: {$n} baris.");
        }

        return self::SUCCESS;
    }
}
