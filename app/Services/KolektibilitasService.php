<?php

namespace App\Services;

use App\Models\KolektibilitasBermasalah;
use App\Models\KolektibilitasMitra;
use App\Models\KolektibilitasSaldoInput;
use App\Models\KolektibilitasSnapshot;
use App\Models\MonitoringPenagihan;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class KolektibilitasService
{
    /** @var list<array{skor: int, label: string}> */
    public const SKOR_THRESHOLDS = [
        ['skor' => 3, 'label' => '> 70 = 3'],
        ['skor' => 2, 'label' => '40 s.d. 70 = 2'],
        ['skor' => 1, 'label' => '10 s.d. 40 = 1'],
        ['skor' => 0, 'label' => '< 10 = 0'],
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
     * @return array{lancar: float, kurang_lancar: float, diragukan: float, macet: float, bermasalah: float}|null
     */
    public function getSaldoInput(CarbonInterface $tanggal): ?array
    {
        $row = KolektibilitasSaldoInput::query()
            ->whereDate('tanggal', $tanggal->toDateString())
            ->first();

        if ($row === null) {
            return null;
        }

        return [
            'lancar' => (float) $row->saldo_lancar,
            'kurang_lancar' => (float) $row->saldo_kurang_lancar,
            'diragukan' => (float) $row->saldo_diragukan,
            'macet' => (float) $row->saldo_macet,
            'bermasalah' => (float) $row->saldo_bermasalah,
        ];
    }

    /**
     * @param array{lancar: float, kurang_lancar: float, diragukan: float, macet: float, bermasalah: float} $saldos
     */
    public function setSaldoInput(CarbonInterface $tanggal, array $saldos): void
    {
        KolektibilitasSaldoInput::query()->updateOrCreate(
            ['tanggal' => $tanggal->toDateString()],
            [
                'saldo_lancar' => $saldos['lancar'],
                'saldo_kurang_lancar' => $saldos['kurang_lancar'],
                'saldo_diragukan' => $saldos['diragukan'],
                'saldo_macet' => $saldos['macet'],
                'saldo_bermasalah' => $saldos['bermasalah'],
            ]
        );

        KolektibilitasBermasalah::query()->updateOrCreate(
            ['tanggal' => $tanggal->toDateString()],
            ['saldo_bermasalah' => $saldos['bermasalah']]
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
        if ($nilai > 70) {
            return ['skor' => 3, 'label' => '> 70 = 3'];
        }

        if ($nilai >= 40) {
            return ['skor' => 2, 'label' => '40 s.d. 70 = 2'];
        }

        if ($nilai >= 10) {
            return ['skor' => 1, 'label' => '10 s.d. 40 = 1'];
        }

        return ['skor' => 0, 'label' => '< 10 = 0'];
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
        $manualInput = $this->getSaldoInput($tanggal);

        if ($manualInput !== null) {
            $saldos = [
                'lancar' => $manualInput['lancar'],
                'kurang_lancar' => $manualInput['kurang_lancar'],
                'diragukan' => $manualInput['diragukan'],
                'macet' => $manualInput['macet'],
            ];
            $saldoBermasalah = $manualInput['bermasalah'];
        } else {
            $saldos = $this->aggregateSaldos($tanggal);
            $saldoBermasalah = $this->getSaldoBermasalah($tanggal);
        }

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

    /**
     * Grafik kolektibilitas tahunan:
     * - monthly.labels: Jan..(bulan sekarang / Des)
     * - monthly.values: persentase nilai terakhir tiap bulan (snapshot terakhir pada bulan tsb)
     * - monthly.keys: kunci bulan (YYYY-MM) untuk drilldown
     * - dailyByMonth[YYYY-MM]: labels/value harian dalam bulan tersebut (berdasarkan snapshot yang tersimpan)
     *
     * @return array{
     *   year: int,
     *   monthly: array{labels: list<string>, values: list<float|null>, keys: list<string>},
     *   dailyByMonth: array<string, array{
     *     labels: list<string>,
     *     values: list<float|null>,
     *     entries: list<array{tanggal: string, label: string, nilai: float|null}>
     *   }>
     * }
     */
    public function buildKolektibilitasChart(int $year): array
    {
        $today = now();
        $endMonth = ($today->year === $year) ? (int) $today->month : 12;

        $rows = KolektibilitasSnapshot::query()
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal')
            ->get();

        $saldoInputs = KolektibilitasSaldoInput::query()
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal')
            ->get();

        $inputsByMonth = $saldoInputs->groupBy(fn (KolektibilitasSaldoInput $input) => $input->tanggal->format('Y-m'));
        $snapshotsByMonth = $rows->groupBy(fn (KolektibilitasSnapshot $s) => $s->tanggal->format('Y-m'));

        $monthlyLabels = [];
        $monthlyValues = [];
        $monthlyKeys = [];
        $dailyByMonth = [];

        for ($month = 1; $month <= $endMonth; $month++) {
            $dt = Carbon::create($year, $month, 1)->startOfMonth();
            $key = $dt->format('Y-m');

            $monthlyLabels[] = $dt->translatedFormat('M Y');
            $monthlyKeys[] = $key;

            /** @var \Illuminate\Support\Collection<int, KolektibilitasSaldoInput> $monthInputs */
            $monthInputs = $inputsByMonth->get($key, collect());

            /** @var \Illuminate\Support\Collection<int, KolektibilitasSnapshot> $monthSnapshots */
            $monthSnapshots = $snapshotsByMonth->get($key, collect());

            $lastInput = $monthInputs->last();
            $lastSnapshot = $monthSnapshots->last();
            $monthlyValues[] = $lastInput
                ? $this->calculateNilaiFromSaldoInput($lastInput)
                : ($lastSnapshot ? $this->calculateNilaiFromSnapshot($lastSnapshot) : null);

            $dailyLabels = [];
            $dailyValues = [];
            $entries = [];
            foreach ($monthInputs as $input) {
                $nilai = $this->calculateNilaiFromSaldoInput($input);
                $dailyLabels[] = $input->tanggal->translatedFormat('d M');
                $dailyValues[] = $nilai;
                $entries[] = [
                    'tanggal' => $input->tanggal->toDateString(),
                    'label' => $input->tanggal->translatedFormat('d M Y'),
                    'nilai' => $nilai,
                ];
            }

            if ($entries === [] && $monthSnapshots->isNotEmpty()) {
                foreach ($monthSnapshots as $snap) {
                    $nilai = $this->calculateNilaiFromSnapshot($snap);
                    $dailyLabels[] = $snap->tanggal->translatedFormat('d M');
                    $dailyValues[] = $nilai;
                    $entries[] = [
                        'tanggal' => $snap->tanggal->toDateString(),
                        'label' => $snap->tanggal->translatedFormat('d M Y'),
                        'nilai' => $nilai,
                    ];
                }
            }

            $dailyByMonth[$key] = [
                'labels' => $dailyLabels,
                'values' => $dailyValues,
                'entries' => $entries,
            ];
        }

        return [
            'year' => $year,
            'monthly' => [
                'labels' => $monthlyLabels,
                'values' => $monthlyValues,
                'keys' => $monthlyKeys,
            ],
            'dailyByMonth' => $dailyByMonth,
        ];
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

    private function calculateNilaiFromSnapshot(KolektibilitasSnapshot $snapshot): ?float
    {
        $jumlahSaldo = (float) $snapshot->saldo_lancar
            + (float) $snapshot->saldo_kurang_lancar
            + (float) $snapshot->saldo_diragukan
            + (float) $snapshot->saldo_macet;

        $jumlahPerkalian = (float) $snapshot->nilai_perkalian_total;

        return $this->calculateNilai($jumlahPerkalian, $jumlahSaldo);
    }

    private function calculateNilaiFromSaldoInput(KolektibilitasSaldoInput $input): ?float
    {
        $jumlahSaldo = (float) $input->saldo_lancar
            + (float) $input->saldo_kurang_lancar
            + (float) $input->saldo_diragukan
            + (float) $input->saldo_macet;

        $jumlahPerkalian = ((float) $input->saldo_lancar * self::CATEGORIES['lancar']['bobot'])
            + ((float) $input->saldo_kurang_lancar * self::CATEGORIES['kurang_lancar']['bobot'])
            + ((float) $input->saldo_diragukan * self::CATEGORIES['diragukan']['bobot'])
            + ((float) $input->saldo_macet * self::CATEGORIES['macet']['bobot']);

        return $this->calculateNilai($jumlahPerkalian, $jumlahSaldo);
    }
}
