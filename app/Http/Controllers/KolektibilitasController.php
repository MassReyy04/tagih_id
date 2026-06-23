<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateKolektibilitasBermasalahRequest;
use App\Http\Requests\UpdateKolektibilitasMitraRequest;
use App\Http\Requests\UpdateKolektibilitasSaldoRequest;
use App\Models\KolektibilitasSaldoInput;
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
        $this->middleware('auth');
        $this->middleware('admin')->only([
            'updateSaldo',
            'updateBermasalah',
            'updateMitra',
            'destroy',
        ]);
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            return redirect()->to(route('home').'#kolektibilitas-dashboard');
        }

        $tanggal = $request->date('tanggal') ?? now()->startOfDay();
        $manualSaldo = $this->kolektibilitasService->getSaldoInput($tanggal);

        $perPage = $request->input('per_page');
        if ($perPage === 'all') {
            $perPage = KolektibilitasSaldoInput::count() ?: 10;
        } else {
            $perPage = (int) ($perPage ?: 10);
        }

        $riwayat = KolektibilitasSaldoInput::query()
            ->orderByDesc('tanggal')
            ->paginate($perPage)
            ->withQueryString();

        return view('kolektibilitas.index', [
            'tanggal' => $tanggal,
            'saldoForForm' => [
                'lancar' => (int) ($manualSaldo['lancar'] ?? 0),
                'kurang_lancar' => (int) ($manualSaldo['kurang_lancar'] ?? 0),
                'diragukan' => (int) ($manualSaldo['diragukan'] ?? 0),
                'macet' => (int) ($manualSaldo['macet'] ?? 0),
                'bermasalah' => (int) ($manualSaldo['bermasalah'] ?? 0),
            ],
            'riwayat' => $riwayat,
        ]);
    }

    public function destroy(KolektibilitasSaldoInput $kolektibilitas): RedirectResponse
    {
        $tanggal = $kolektibilitas->tanggal;
        $kolektibilitas->delete();

        // Refresh snapshot untuk tanggal tersebut jika ada
        $this->kolektibilitasService->saveDailySnapshot($tanggal);

        return redirect()
            ->route('kolektibilitas.index')
            ->with('status', 'Data kolektibilitas tanggal '.$tanggal->translatedFormat('d F Y').' berhasil dihapus.');
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

    public function updateSaldo(UpdateKolektibilitasSaldoRequest $request): RedirectResponse
    {
        $tanggal = Carbon::parse($request->input('tanggal'))->startOfDay();

        $this->kolektibilitasService->setSaldoInput($tanggal, [
            'lancar' => (float) $request->input('saldo_lancar'),
            'kurang_lancar' => (float) $request->input('saldo_kurang_lancar'),
            'diragukan' => (float) $request->input('saldo_diragukan'),
            'macet' => (float) $request->input('saldo_macet'),
            'bermasalah' => (float) $request->input('saldo_bermasalah'),
        ]);

        $this->kolektibilitasService->saveDailySnapshot($tanggal);

        $status = $tanggal->isToday()
            ? 'Saldo kolektibilitas berhasil disimpan. Angka akan tampil di dashboard depan.'
            : 'Saldo kolektibilitas untuk '.$tanggal->translatedFormat('d F Y').' berhasil disimpan.';

        return redirect()
            ->route('kolektibilitas.index', ['tanggal' => $tanggal->toDateString()])
            ->with('status', $status);
    }

    public function updateMitra(UpdateKolektibilitasMitraRequest $request): RedirectResponse
    {
        $tanggal = Carbon::parse($request->input('tanggal'))->startOfDay();

        $this->kolektibilitasService->setMitraHariTunggakan(
            $request->input('nomor_induk'),
            (int) $request->input('hari_tunggakan')
        );

        $this->kolektibilitasService->saveDailySnapshot($tanggal);

        return redirect()
            ->route('kolektibilitas.index', ['tanggal' => $tanggal->toDateString()])
            ->with('status', 'Hari tunggakan untuk NIM '.$request->input('nomor_induk').' berhasil disimpan.');
    }
}
