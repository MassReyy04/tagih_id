<?php

use App\Models\MonitoringPenagihan;
use App\Services\NomorSuratService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

MonitoringPenagihan::all()->groupBy(function($m) {
    return $m->tanggal->toDateString();
})->each(function($group) {
    $group->values()->each(function($m, $index) {
        $newNo = NomorSuratService::formatNomorSurat($m->tanggal, $index + 1);
        $m->update(['nomor_surat' => $newNo]);
        echo "Updated record ID {$m->id} to {$newNo}\n";
    });
});
