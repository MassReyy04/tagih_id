<?php

namespace App\Http\Controllers;

use App\Models\MonitoringPenagihan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        [$dateFrom, $dateTo, $userId] = $this->resolveFilters($request);

        $scoped = fn ($q) => $q->when($userId, fn ($qq) => $qq->where('user_id', $userId));

        $totalPenagihan = MonitoringPenagihan::query()
            ->tap($scoped)
            ->whereBetween('tanggal', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->count();

        $totalMitra = (int) MonitoringPenagihan::query()
            ->tap($scoped)
            ->whereBetween('tanggal', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->whereNotNull('nomor_induk')
            ->where('nomor_induk', '!=', '')
            ->selectRaw('COUNT(DISTINCT nomor_induk) as aggregate')
            ->value('aggregate');

        if ($totalMitra === 0) {
            $totalMitra = (int) MonitoringPenagihan::query()
                ->tap($scoped)
                ->whereBetween('tanggal', [$dateFrom->toDateString(), $dateTo->toDateString()])
                ->selectRaw('COUNT(DISTINCT nama_mitra) as aggregate')
                ->value('aggregate');
        }

        $kunjunganHariIni = MonitoringPenagihan::query()
            ->tap($scoped)
            ->whereDate('tanggal', now()->toDateString())
            ->count();

        $penagihanBulanIni = MonitoringPenagihan::query()
            ->tap($scoped)
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->count();

        $rekapPetugas = $this->queryRekapPetugas($dateFrom, $dateTo, $userId);

        $avgKunjungan = $rekapPetugas->avg('total_kunjungan') ?? 0;
        $maxKunjungan = $rekapPetugas->max('total_kunjungan') ?? 0;
        $rank = 0;
        $rekapPetugas = $rekapPetugas->map(function ($row) use (&$rank, $avgKunjungan, $maxKunjungan) {
            $rank++;
            $row->rank = $rank;
            $row->activity = ($maxKunjungan > 0 && $row->total_kunjungan >= $avgKunjungan) ? 'active' : 'low';

            return $row;
        });

        $chartMonthly = $this->buildMonthlyChartData($userId);
        $chartDaily = $this->buildDailyChartData($dateFrom, $dateTo, $userId);

        $rekapDetail = MonitoringPenagihan::query()
            ->tap($scoped)
            ->whereBetween('tanggal', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $petugasList = User::query()
            ->where('role', 'petugas')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('dashboard.admin', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'userId' => $userId,
            'totalPenagihan' => $totalPenagihan,
            'totalMitra' => $totalMitra,
            'kunjunganHariIni' => $kunjunganHariIni,
            'penagihanBulanIni' => $penagihanBulanIni,
            'rekapPetugas' => $rekapPetugas,
            'rekapDetail' => $rekapDetail,
            'petugasList' => $petugasList,
            'chartMonthly' => $chartMonthly,
            'chartDaily' => $chartDaily,
        ]);
    }

    /**
     * @return array{0: \Carbon\Carbon, 1: \Carbon\Carbon, 2: int|null}
     */
    private function resolveFilters(Request $request): array
    {
        $userId = $request->filled('user_id') ? (int) $request->input('user_id') : null;

        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');

        if ($dateFrom && $dateTo) {
            if ($dateFrom->gt($dateTo)) {
                [$dateFrom, $dateTo] = [$dateTo->copy()->startOfDay(), $dateFrom->copy()->endOfDay()];
            }
        } elseif ($dateFrom && ! $dateTo) {
            $dateTo = now()->endOfDay();
        } elseif (! $dateFrom && $dateTo) {
            $dateFrom = $dateTo->copy()->startOfMonth()->startOfDay();
        } else {
            $dateFrom = now()->copy()->startOfMonth();
            $dateTo = now()->copy()->endOfDay();
        }

        return [$dateFrom, $dateTo, $userId];
    }

    private function queryRekapPetugas(Carbon $dateFrom, Carbon $dateTo, ?int $userId)
    {
        $janjiExpr = "SUM(CASE WHEN m.janji IS NOT NULL AND TRIM(m.janji) != '' THEN 1 ELSE 0 END)";

        $q = DB::table('monitoring_penagihan as m')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->whereBetween('m.tanggal', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->whereNotNull('m.user_id')
            ->groupBy('m.user_id', 'u.name')
            ->selectRaw('m.user_id, u.name as nama_petugas, COUNT(m.id) as total_kunjungan, COUNT(m.id) as total_input, '.$janjiExpr.' as penagihan_berhasil')
            ->orderByDesc('total_kunjungan');

        if ($userId) {
            $q->where('m.user_id', $userId);
        }

        return $q->get();
    }

    /**
     * @return array{labels: string[], values: int[]}
     */
    private function buildMonthlyChartData(?int $userId): array
    {
        $start = now()->copy()->subMonths(11)->startOfMonth();

        $rows = MonitoringPenagihan::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->where('tanggal', '>=', $start->toDateString())
            ->get(['tanggal']);

        $byMonth = $rows->groupBy(fn ($m) => $m->tanggal->format('Y-m'))
            ->map->count();

        $labels = [];
        $values = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->copy()->subMonths($i)->startOfMonth();
            $key = $d->format('Y-m');
            $labels[] = $d->translatedFormat('M Y');
            $values[] = (int) ($byMonth[$key] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array{labels: string[], values: int[]}
     */
    private function buildDailyChartData(Carbon $dateFrom, Carbon $dateTo, ?int $userId): array
    {
        $rows = MonitoringPenagihan::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->whereBetween('tanggal', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->get(['tanggal']);

        $byDay = $rows->groupBy(fn ($m) => $m->tanggal->format('Y-m-d'))
            ->map->count();

        $labels = [];
        $values = [];
        $cursor = $dateFrom->copy()->startOfDay();
        $end = $dateTo->copy()->startOfDay();
        while ($cursor->lte($end)) {
            $key = $cursor->format('Y-m-d');
            $labels[] = $cursor->translatedFormat('d M');
            $values[] = (int) ($byDay[$key] ?? 0);
            $cursor->addDay();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function mitraUnik(Request $request): JsonResponse
    {
        [$dateFrom, $dateTo, $userId] = $this->resolveFilters($request);

        $scoped = fn ($q) => $q->when($userId, fn ($qq) => $qq->where('user_id', $userId));

        $baseQuery = MonitoringPenagihan::query()
            ->tap($scoped)
            ->whereBetween('tanggal', [$dateFrom->toDateString(), $dateTo->toDateString()]);

        $hasNomorInduk = (clone $baseQuery)
            ->whereNotNull('nomor_induk')
            ->where('nomor_induk', '!=', '')
            ->exists();

        if ($hasNomorInduk) {
            $groupBy = 'nomor_induk';
            $rows = (clone $baseQuery)
                ->whereNotNull('nomor_induk')
                ->where('nomor_induk', '!=', '')
                ->select([
                    'nomor_induk',
                    DB::raw('MAX(nama_mitra) as nama_mitra'),
                    DB::raw('MAX(nama_usaha) as nama_usaha'),
                    DB::raw('MAX(geo_kecamatan) as geo_kecamatan'),
                    DB::raw('MAX(geo_kelurahan) as geo_kelurahan'),
                    DB::raw('COUNT(id) as total_kunjungan'),
                    DB::raw('MAX(tanggal) as terakhir_kunjungan'),
                ])
                ->groupBy('nomor_induk')
                ->orderBy('nama_mitra');
        } else {
            $groupBy = 'nama_mitra';
            $rows = (clone $baseQuery)
                ->select([
                    DB::raw('NULL as nomor_induk'),
                    DB::raw('nama_mitra as nama_mitra'),
                    DB::raw('MAX(nama_usaha) as nama_usaha'),
                    DB::raw('MAX(geo_kecamatan) as geo_kecamatan'),
                    DB::raw('MAX(geo_kelurahan) as geo_kelurahan'),
                    DB::raw('COUNT(id) as total_kunjungan'),
                    DB::raw('MAX(tanggal) as terakhir_kunjungan'),
                ])
                ->groupBy('nama_mitra')
                ->orderBy('nama_mitra');
        }

        if ($request->filled('q')) {
            $q = '%' . $request->string('q') . '%';
            $rows->where(function ($w) use ($q) {
                $w->where('nama_mitra', 'like', $q)
                    ->orWhere('nama_usaha', 'like', $q)
                    ->orWhere('nomor_induk', 'like', $q)
                    ->orWhere('geo_kecamatan', 'like', $q)
                    ->orWhere('geo_kelurahan', 'like', $q);
            });
        }

        $list = $rows->get()->map(function ($m) {
            return [
                'nomor_induk' => $m->nomor_induk ?? '-',
                'nama_mitra' => $m->nama_mitra,
                'nama_usaha' => $m->nama_usaha ?? '-',
                'kecamatan' => $m->geo_kecamatan ?: '-',
                'kelurahan' => $m->geo_kelurahan ?: '-',
                'total_kunjungan' => (int) $m->total_kunjungan,
                'terakhir_kunjungan' => $m->terakhir_kunjungan
                    ? Carbon::parse($m->terakhir_kunjungan)->translatedFormat('d M Y')
                    : '-',
            ];
        })->values();

        $html = '';
        if ($list->isEmpty()) {
            $html = '<tr><td colspan="6" class="text-center text-muted py-5"><i class="fa-solid fa-folder-open fa-2x mb-2 opacity-25"></i><p class="mb-0">Tidak ada data mitra untuk periode &amp; filter ini.</p></td></tr>';
        } else {
            foreach ($list as $i => $m) {
                $html .= '<tr>'
                    . '<td class="ps-4 text-muted small">' . ($i + 1) . '.</td>'
                    . '<td class="fw-medium">' . e($m['nama_mitra']) . '</td>'
                    . '<td><code class="text-success small">' . e($m['nomor_induk']) . '</code></td>'
                    . '<td class="small">' . e($m['nama_usaha']) . '</td>'
                    . '<td class="small">'
                        . ($m['kelurahan'] !== '-' ? '<span class="text-muted d-block small">' . e($m['kelurahan']) . '</span>' : '')
                        . e($m['kecamatan'])
                    . '</td>'
                    . '<td class="text-end pe-4 text-nowrap small">'
                        . '<span class="d-block">x' . $m['total_kunjungan'] . '</span>'
                        . '<span class="text-muted d-block fw-normal">terakhir: ' . e($m['terakhir_kunjungan']) . '</span>'
                    . '</td>'
                    . '</tr>';
            }
        }

        return response()->json([
            'total' => $list->count(),
            'periode' => $dateFrom->translatedFormat('d M Y') . ' — ' . $dateTo->translatedFormat('d M Y'),
            'html' => $html,
        ]);
    }
}
