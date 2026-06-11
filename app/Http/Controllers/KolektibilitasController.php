<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateKolektibilitasBermasalahRequest;
use App\Models\KolektibilitasSnapshot;
use App\Services\KolektibilitasService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KolektibilitasController extends Controller
{
    public function __construct(
        private readonly KolektibilitasService $kolektibilitasService
    ) {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request): View
    {
        $tanggal = $request->date('tanggal') ?? now()->startOfDay();

        if ($tanggal->isToday()) {
            $this->kolektibilitasService->saveDailySnapshot($tanggal);
        }

        $report = $this->kolektibilitasService->buildReport($tanggal);

        $snapshots = KolektibilitasSnapshot::query()
            ->orderByDesc('tanggal')
            ->limit(30)
            ->get();

        return view('kolektibilitas.index', [
            'tanggal' => $tanggal,
            'report' => $report,
            'snapshots' => $snapshots,
        ]);
    }

    public function updateBermasalah(UpdateKolektibilitasBermasalahRequest $request): RedirectResponse
    {
        $tanggal = Carbon::parse($request->input('tanggal'))->startOfDay();

        $this->kolektibilitasService->setSaldoBermasalah(
            $tanggal,
            (float) $request->input('saldo_bermasalah')
        );

        $this->kolektibilitasService->saveDailySnapshot($tanggal);

        return redirect()
            ->route('kolektibilitas.index', ['tanggal' => $tanggal->toDateString()])
            ->with('status', 'Saldo bermasalah berhasil diperbarui.');
    }
}
