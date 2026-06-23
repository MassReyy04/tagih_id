<?php

namespace App\Http\Controllers;

use App\Models\MonitoringPenagihan;
use App\Services\KolektibilitasService;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $query = MonitoringPenagihan::query();

        // Jika bukan admin dan bukan pimpinan, hanya lihat data sendiri
        if (! $user->isAdmin() && ! $user->isRegional()) {
            $query->where('user_id', $user->id);
        }

        $total = (clone $query)->count();
        $bulanIni = (clone $query)
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->count();

        $terbaru = $query->with('user')
            ->latest('tanggal')
            ->latest('id')
            ->limit(5)
            ->get();

        $visitChart = $this->buildVisitChartData($user->isAdmin() || $user->isRegional() ? null : $user->id);

        $kolektibilitasService = app(KolektibilitasService::class);
        $today = now()->startOfDay();

        if ($user->isAdmin()) {
            $kolektibilitasService->saveDailySnapshot($today);
        }

        $kolektibilitasChart = $kolektibilitasService->buildKolektibilitasChart((int) now()->year);
        $kolektibilitasSummary = $kolektibilitasService->buildReport($today);

        return view('home', compact('total', 'bulanIni', 'terbaru', 'visitChart', 'kolektibilitasChart', 'kolektibilitasSummary'));
    }

    private function buildVisitChartData(?int $userId): array
    {
        $year = (int) now()->year;
        $endMonth = (int) now()->month;

        $rows = MonitoringPenagihan::query()
            ->with('user:id,name')
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal')
            ->get(['tanggal', 'user_id']);

        $monthlyLabels = [];
        $monthlyValues = [];
        $monthlyKeys = [];
        $dailyByMonth = [];

        for ($month = 1; $month <= $endMonth; $month++) {
            $dt = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
            $key = $dt->format('Y-m');

            $monthlyLabels[] = $dt->translatedFormat('M Y');
            $monthlyKeys[] = $key;

            /** @var \Illuminate\Support\Collection<int, MonitoringPenagihan> $monthRecords */
            $monthRecords = $rows->filter(
                fn ($row) => $row->tanggal->format('Y-m') === $key
            )->values();

            $countsByDate = $monthRecords
                ->groupBy(fn ($row) => $row->tanggal->toDateString())
                ->map(fn ($group) => $group->count())
                ->sortKeys();

            $monthlyValues[] = (int) $countsByDate->sum();

            $entries = [];
            $dailyLabels = [];
            $dailyValues = [];

            foreach ($countsByDate as $dateStr => $count) {
                $date = \Carbon\Carbon::parse($dateStr);
                $dailyLabels[] = $date->translatedFormat('d M');
                $dailyValues[] = (int) $count;

                $entryByPetugas = $monthRecords
                    ->filter(fn ($row) => $row->tanggal->toDateString() === $dateStr)
                    ->groupBy('user_id')
                    ->map(function ($group) {
                        $user = $group->first()->user;

                        return [
                            'name' => $user?->name ?? 'Tidak diketahui',
                            'jumlah' => $group->count(),
                        ];
                    })
                    ->sortByDesc('jumlah')
                    ->values()
                    ->all();

                $entries[] = [
                    'tanggal' => $dateStr,
                    'label' => $date->translatedFormat('d M Y'),
                    'jumlah' => (int) $count,
                    'byPetugas' => $entryByPetugas,
                ];
            }

            $byPetugas = $monthRecords
                ->groupBy('user_id')
                ->map(function ($group) {
                    $user = $group->first()->user;

                    return [
                        'name' => $user?->name ?? 'Tidak diketahui',
                        'jumlah' => $group->count(),
                    ];
                })
                ->sortByDesc('jumlah')
                ->values()
                ->all();

            $dailyByMonth[$key] = [
                'labels' => $dailyLabels,
                'values' => $dailyValues,
                'entries' => $entries,
                'byPetugas' => $byPetugas,
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
}
