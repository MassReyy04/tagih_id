<?php

namespace App\Services;

use App\Models\KolektibilitasBermasalah;
use App\Models\KolektibilitasMitra;
use App\Models\KolektibilitasSnapshot;
use App\Models\MonitoringPenagihan;
use Carbon\CarbonInterface;

class KolektibilitasService
{
    /** @var list<array{min: float, skor: int, label: string}> */
    public const SKOR_THRESHOLDS = [
        ['min' => 80, 'skor' => 4, 'label' => '> 80 = 4'],
        ['min' => 70, 'skor' => 3, 'label' => '> 70 = 3'],
        ['min' => 60, 'skor' => 2, 'label' => '> 60 = 2'],
    ];

    public const CATEGORIES = [
        'lancar' => [
            'label' => 'Lancar',
            'umur' => 's.d. 30 hari',
            'bobot' => 1.0,
            'bobot_label' => '100,00%',
        ],
        'kurang_lancar' => [
            'label' => 'Kurang Lancar',
            'umur' => '>30 - 180 hari',
            'bobot' => 0.75,
            'bobot_label' => '75,00%',
        ],
        'diragukan' => [
            'label' => 'Diragukan',
            'umur' => '>180 - 270 hari',
            'bobot' => 0.25,
            'bobot_label' => '25,00%',
        ],
        'macet' => [
            'label' => 'Macet',
            'umur' => '>270 hari',
            'bobot' => 0.0,
            'bobot_label' => '0%',
        ],
    ];

    public function classifyHariTunggakan(int $hari): string
    {
        if ($hari <= 30) {
            return 'lancar';
        }

        if ($hari <= 180) {
            return 'kurang_lancar';
        }

        if ($hari <= 270) {
            return 'diragukan';
        }

        return 'macet';
    }

    /**
     * @return array<string, float>
     */
    public function aggregateSaldos(CarbonInterface $asOf): array
    {
        $saldos = [
            'lancar' => 0.0,
            'kurang_lancar' => 0.0,
            'diragukan' => 0.0,
            'macet' => 0.0,
        ];

        $overrides = $this->getHariTunggakanMap();

        $records = MonitoringPenagihan::query()
            ->whereDate('tanggal', '<=', $asOf->toDateString())
            ->where('sisa_pinjaman', '>', 0)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get(['nomor_induk', 'nama_mitra', 'sisa_pinjaman']);

        $seen = [];

        foreach ($records as $row) {
            $key = trim((string) $row->nomor_induk) !== ''
                ? trim((string) $row->nomor_induk)
                : (string) $row->nama_mitra;

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $hari = $overrides[$key] ?? 0;
            $category = $this->classifyHariTunggakan($hari);
            $saldos[$category] += (float) $row->sisa_pinjaman;
        }

        return $saldos;
    }

    public function getSaldoBermasalah(CarbonInterface $tanggal): float
    {
        $row = KolektibilitasBermasalah::query()
            ->whereDate('tanggal', $tanggal->toDateString())
            ->first();

        return (float) ($row?->saldo_bermasalah ?? 0);
    }

    public function setSaldoBermasalah(CarbonInterface $tanggal, float $saldo): void
    {
        KolektibilitasBermasalah::query()->updateOrCreate(
            ['tanggal' => $tanggal->toDateString()],
            ['saldo_bermasalah' => $saldo]
        );
    }

    /**
     * @return array<string, int>
     */
    public function getHariTunggakanMap(): array
    {
        return KolektibilitasMitra::query()
            ->pluck('hari_tunggakan', 'nomor_induk')
            ->map(fn ($hari) => (int) $hari)
            ->all();
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{nomor_induk: string, nama_mitra: string|null, hari_tunggakan: int, klasifikasi: string}>
     */
    public function listMitraHariTunggakan()
    {
        return KolektibilitasMitra::query()
            ->orderBy('nomor_induk')
            ->get()
            ->map(function (KolektibilitasMitra $row) {
                $category = self::CATEGORIES[$this->classifyHariTunggakan($row->hari_tunggakan)];

                return [
                    'nomor_induk' => $row->nomor_induk,
                    'nama_mitra' => $row->nama_mitra,
                    'hari_tunggakan' => $row->hari_tunggakan,
                    'klasifikasi' => $category['label'],
                ];
            });
    }

    public function setMitraHariTunggakan(string $nomorInduk, int $hariTunggakan): KolektibilitasMitra
    {
        $nim = trim($nomorInduk);

        $latest = MonitoringPenagihan::query()
            ->where('nomor_induk', $nim)
            ->where('sisa_pinjaman', '>', 0)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first(['nama_mitra']);

        return KolektibilitasMitra::query()->updateOrCreate(
            ['nomor_induk' => $nim],
            [
                'nama_mitra' => $latest?->nama_mitra,
                'hari_tunggakan' => $hariTunggakan,
            ]
        );
    }

    public function calculateNilai(float $jumlahPerkalian, float $jumlahSaldo): ?float
    {
        if ($jumlahSaldo <= 0) {
            return null;
        }

        return ($jumlahPerkalian / $jumlahSaldo) * 100;
    }

    /**
     * @return array{skor: int, label: string}
     */
    public function calculateSkor(float $nilai): array
    {
        foreach (self::SKOR_THRESHOLDS as $threshold) {
            if ($nilai > $threshold['min']) {
                return [
                    'skor' => $threshold['skor'],
                    'label' => $threshold['label'],
                ];
            }
        }

        return [
            'skor' => 1,
            'label' => '≤ 60 = 1',
        ];
    }

    /**
     * @return array{
     *     tanggal: string,
     *     tahun: int,
     *     rows: list<array{key: string, label: string, umur: string, saldo: float, bobot: float, bobot_label: string, perkalian: float}>,
     *     jumlah_saldo: float,
     *     jumlah_perkalian: float,
     *     saldo_bermasalah: float,
     *     total_saldo: float,
     *     total_perkalian: float,
     *     nilai: ?float,
     *     skor: ?int,
     *     skor_label: ?string,
     *     mitra_count: int
     * }
     */
    public function buildReport(CarbonInterface $tanggal): array
    {
        $saldos = $this->aggregateSaldos($tanggal);
        $saldoBermasalah = $this->getSaldoBermasalah($tanggal);

        $rows = [];
        $jumlahSaldo = 0.0;
        $jumlahPerkalian = 0.0;

        foreach (self::CATEGORIES as $key => $meta) {
            $saldo = $saldos[$key];
            $perkalian = $saldo * $meta['bobot'];

            $rows[] = [
                'key' => $key,
                'label' => $meta['label'],
                'umur' => $meta['umur'],
                'saldo' => $saldo,
                'bobot' => $meta['bobot'],
                'bobot_label' => $meta['bobot_label'],
                'perkalian' => $perkalian,
            ];

            $jumlahSaldo += $saldo;
            $jumlahPerkalian += $perkalian;
        }

        $nilai = $this->calculateNilai($jumlahPerkalian, $jumlahSaldo);
        $skor = $nilai !== null ? $this->calculateSkor($nilai) : null;

        return [
            'tanggal' => $tanggal->toDateString(),
            'tahun' => (int) $tanggal->year,
            'rows' => $rows,
            'jumlah_saldo' => $jumlahSaldo,
            'jumlah_perkalian' => $jumlahPerkalian,
            'saldo_bermasalah' => $saldoBermasalah,
            'total_saldo' => $jumlahSaldo + $saldoBermasalah,
            'total_perkalian' => $jumlahPerkalian,
            'nilai' => $nilai,
            'skor' => $skor !== null ? $skor['skor'] : null,
            'skor_label' => $skor !== null ? $skor['label'] : null,
            'mitra_count' => $this->countActiveMitra($tanggal),
        ];
    }

    public function saveDailySnapshot(CarbonInterface $tanggal): KolektibilitasSnapshot
    {
        $report = $this->buildReport($tanggal);

        return KolektibilitasSnapshot::query()->updateOrCreate(
            ['tanggal' => $tanggal->toDateString()],
            [
                'saldo_lancar' => $report['rows'][0]['saldo'],
                'saldo_kurang_lancar' => $report['rows'][1]['saldo'],
                'saldo_diragukan' => $report['rows'][2]['saldo'],
                'saldo_macet' => $report['rows'][3]['saldo'],
                'saldo_bermasalah' => $report['saldo_bermasalah'],
                'nilai_perkalian_total' => $report['jumlah_perkalian'],
            ]
        );
    }

    private function countActiveMitra(CarbonInterface $asOf): int
    {
        $records = MonitoringPenagihan::query()
            ->whereDate('tanggal', '<=', $asOf->toDateString())
            ->where('sisa_pinjaman', '>', 0)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get(['nomor_induk', 'nama_mitra']);

        $seen = [];

        foreach ($records as $row) {
            $key = trim((string) $row->nomor_induk) !== ''
                ? trim((string) $row->nomor_induk)
                : (string) $row->nama_mitra;

            $seen[$key] = true;
        }

        return count($seen);
    }
}
