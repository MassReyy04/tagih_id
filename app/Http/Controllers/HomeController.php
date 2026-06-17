<?php

namespace App\Http\Controllers;

use App\Models\MonitoringPenagihan;

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

        // Jika bukan admin, hanya lihat data sendiri
        if (! $user->isAdmin()) {
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

        $chartData = $this->buildMonthlyChartData($user->isAdmin() ? null : $user->id);

        return view('home', compact('total', 'bulanIni', 'terbaru', 'chartData'));
    }

    private function buildMonthlyChartData(?int $userId): array
    {
        $labels = [];
        $values = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->copy()->subMonths($i)->startOfMonth();
            $labels[] = $month->translatedFormat('M Y');

            $values[] = MonitoringPenagihan::query()
                ->when($userId, fn ($q) => $q->where('user_id', $userId))
                ->whereYear('tanggal', $month->year)
                ->whereMonth('tanggal', $month->month)
                ->count();
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
