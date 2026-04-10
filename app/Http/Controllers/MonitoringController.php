<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMonitoringPenagihanRequest;
use App\Http\Requests\UpdateMonitoringPenagihanRequest;
use App\Models\MonitoringPenagihan;
use App\Services\NomorSuratService;
use App\Services\ReverseGeocodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function __construct(
        private readonly NomorSuratService $nomorSuratService
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = MonitoringPenagihan::query()
            ->with('user')
            ->latest('tanggal')
            ->latest('id');

        // Jika bukan admin, hanya lihat data milik sendiri
        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($w) use ($q) {
                $w->where('nama_mitra', 'like', '%'.$q.'%')
                    ->orWhere('nama_usaha', 'like', '%'.$q.'%')
                    ->orWhere('nomor_surat', 'like', '%'.$q.'%')
                    ->orWhere('nomor_induk', 'like', '%'.$q.'%');
            });
        }

        $items = $query->paginate(15)->withQueryString();

        return view('monitoring.index', compact('items'));
    }

    public function create(): View
    {
        return view('monitoring.create');
    }

    public function store(StoreMonitoringPenagihanRequest $request): RedirectResponse
    {
        \Illuminate\Support\Facades\Log::info('Monitoring Store Request Received', [
            'user_id' => auth()->id(),
            'data_count' => count($request->all()),
            'has_foto' => $request->hasFile('foto'),
            'has_sig_mitra' => $request->filled('signature_mitra'),
            'has_sig_petugas' => $request->filled('signature_petugas'),
        ]);

        $tanggal = $request->date('tanggal');
        $nomorSurat = $this->nomorSuratService->generateNomorSurat($tanggal);

        $signatureMitra = $this->storeDataUrlAsPng($request->input('signature_mitra'), 'mitra');
        $signaturePetugas = $this->storeDataUrlAsPng($request->input('signature_petugas'), 'petugas');

        if (! $signatureMitra || ! $signaturePetugas) {
            return back()->withInput()->withErrors([
                'signature_mitra' => 'Tanda tangan tidak valid atau gagal disimpan.',
            ]);
        }

        $fotoPath = $request->hasFile('foto')
            ? $request->file('foto')->store('fotos', 'public')
            : null;

        $geo = $this->resolveGeoFields($request);

        MonitoringPenagihan::query()->create([
            'nomor_surat' => $nomorSurat,
            'nama_mitra' => $request->input('nama_mitra'),
            'nama_usaha' => $request->input('nama_usaha'),
            'nomor_induk' => $request->input('nomor_induk'),
            'alamat' => $request->input('alamat'),
            'no_hp' => $request->input('no_hp'),
            'nilai_pinjaman' => $request->input('nilai_pinjaman'),
            'sisa_pinjaman' => $request->input('sisa_pinjaman'),
            'alasan' => $request->input('alasan'),
            'janji' => $request->input('janji'),
            'catatan' => $request->input('catatan'),
            'kebutuhan' => $request->input('kebutuhan'),
            'tanggal' => $tanggal,
            'signature_mitra' => $signatureMitra,
            'signature_petugas' => $signaturePetugas,
            'foto' => $fotoPath,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            ...$geo,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('monitoring.index')->with('status', 'Data berita acara berhasil disimpan.');
    }

    public function show(MonitoringPenagihan $monitoring): View
    {
        $user = auth()->user();
        if (! $user->isAdmin() && (int) $monitoring->user_id !== (int) $user->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $monitoring->load('user');

        return view('monitoring.show', ['m' => $monitoring]);
    }

    public function edit(MonitoringPenagihan $monitoring): View
    {
        $user = auth()->user();
        if (! $user->isAdmin() && (int) $monitoring->user_id !== (int) $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data ini.');
        }

        return view('monitoring.edit', ['m' => $monitoring]);
    }

    public function update(UpdateMonitoringPenagihanRequest $request, MonitoringPenagihan $monitoring): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isAdmin() && (int) $monitoring->user_id !== (int) $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data ini.');
        }

        $oldTanggal = $monitoring->tanggal;
        $newTanggal = $request->date('tanggal');

        $data = [
            'nama_mitra' => $request->input('nama_mitra'),
            'nama_usaha' => $request->input('nama_usaha'),
            'nomor_induk' => $request->input('nomor_induk'),
            'alamat' => $request->input('alamat'),
            'no_hp' => $request->input('no_hp'),
            'nilai_pinjaman' => $request->input('nilai_pinjaman'),
            'sisa_pinjaman' => $request->input('sisa_pinjaman'),
            'alasan' => $request->input('alasan'),
            'janji' => $request->input('janji'),
            'catatan' => $request->input('catatan'),
            'kebutuhan' => $request->input('kebutuhan'),
            'tanggal' => $newTanggal,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            ...$this->resolveGeoFields($request),
        ];

        // Jika tanggal berubah, generate nomor surat baru untuk tanggal tersebut
        if ($oldTanggal->toDateString() !== $newTanggal->toDateString()) {
            $data['nomor_surat'] = $this->nomorSuratService->generateNomorSurat($newTanggal);
        }

        if ($request->filled('signature_mitra')) {
            $path = $this->storeDataUrlAsPng($request->input('signature_mitra'), 'mitra');
            if ($path) {
                $this->deletePublicIfExists($monitoring->signature_mitra);
                $data['signature_mitra'] = $path;
            }
        }

        if ($request->filled('signature_petugas')) {
            $path = $this->storeDataUrlAsPng($request->input('signature_petugas'), 'petugas');
            if ($path) {
                $this->deletePublicIfExists($monitoring->signature_petugas);
                $data['signature_petugas'] = $path;
            }
        }

        if ($request->hasFile('foto')) {
            $this->deletePublicIfExists($monitoring->foto);
            $data['foto'] = $request->file('foto')->store('fotos', 'public');
        }

        $monitoring->update($data);

        // Jika tanggal berubah, urutkan ulang nomor surat pada tanggal lama agar tidak ada gap
        if ($oldTanggal->toDateString() !== $newTanggal->toDateString()) {
            $this->nomorSuratService->reindexNomorSurat($oldTanggal);
        }

        return redirect()->route('monitoring.show', $monitoring)->with('status', 'Data berhasil diperbarui.');
    }

    public function destroy(Request $request, MonitoringPenagihan $monitoring): RedirectResponse
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menghapus data.');
        }

        $tanggal = $monitoring->tanggal;

        $this->deletePublicIfExists($monitoring->signature_mitra);
        $this->deletePublicIfExists($monitoring->signature_petugas);
        $this->deletePublicIfExists($monitoring->foto);
        $monitoring->delete();

        // Urutkan ulang nomor surat pada tanggal yang sama agar tidak ada gap
        $this->nomorSuratService->reindexNomorSurat($tanggal);

        return redirect()->route('monitoring.index')->with('status', 'Data telah dihapus dan nomor surat telah diurutkan ulang.');
    }

    public function pdf(MonitoringPenagihan $monitoring)
    {
        $monitoring->load('user');

        $mapSig = null;
        if (! empty($monitoring->latitude) && ! empty($monitoring->longitude)) {
            $lat = (float) $monitoring->latitude;
            $lng = (float) $monitoring->longitude;
            $mapSig = $this->osmTileStaticMapToPngDataUri($lat, $lng, zoom: 17, width: 400, height: 220);
        }

        $pdf = Pdf::loadView('monitoring.pdf', [
            'm' => $monitoring,
            'mitraSig' => $this->fileToDataUri($monitoring->signature_mitra),
            'petugasSig' => $this->fileToDataUri($monitoring->signature_petugas),
            'fotoSig' => $this->fileToDataUri($monitoring->foto),
            'mapSig' => $mapSig,
        ])->setPaper('a4', 'portrait');

        $filename = 'BAM-'.$monitoring->id.'-'.preg_replace('/[^A-Za-z0-9_-]+/', '_', $monitoring->nomor_surat).'.pdf';

        return $pdf->stream($filename);
    }

    private function storeDataUrlAsPng(?string $dataUrl, string $prefix): ?string
    {
        if ($dataUrl === null || $dataUrl === '') {
            return null;
        }

        if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $m)) {
            $dataUrl = substr($dataUrl, strpos($dataUrl, ',') + 1);
        }

        $binary = base64_decode($dataUrl, true);
        if ($binary === false || $binary === '') {
            return null;
        }

        // Contoh: signatures/mitra_1730000000_a1b2c3d4.png
        $unique = time().'_'.Str::lower(Str::random(8));
        $name = sprintf('signatures/%s_%s.png', $prefix, $unique);
        Storage::disk('public')->put($name, $binary);

        return $name;
    }

    private function deletePublicIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function fileToDataUri(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };

        return 'data:'.$mime.';base64,'.base64_encode(Storage::disk('public')->get($path));
    }

    private function osmTileStaticMapToPngDataUri(float $lat, float $lng, int $zoom, int $width, int $height): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $zoom = max(0, min(19, $zoom));
        $width = max(64, min(1200, $width));
        $height = max(64, min(1200, $height));

        $tileSize = 256;
        $n = 2 ** $zoom;

        $latRad = deg2rad($lat);
        $x = ($lng + 180.0) / 360.0 * $n;
        $y = (1.0 - log(tan($latRad) + 1.0 / cos($latRad)) / M_PI) / 2.0 * $n;

        $centerPxX = $x * $tileSize;
        $centerPxY = $y * $tileSize;

        $topLeftPxX = (int) round($centerPxX - ($width / 2));
        $topLeftPxY = (int) round($centerPxY - ($height / 2));

        $minTileX = (int) floor($topLeftPxX / $tileSize);
        $minTileY = (int) floor($topLeftPxY / $tileSize);
        $maxTileX = (int) floor(($topLeftPxX + $width - 1) / $tileSize);
        $maxTileY = (int) floor(($topLeftPxY + $height - 1) / $tileSize);

        $img = imagecreatetruecolor($width, $height);
        if (! $img) {
            return null;
        }

        imagealphablending($img, true);
        imagesavealpha($img, true);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $width, $height, $white);

        // Sumber citra satelit (tanpa API key): Esri World Imagery
        // Format: /tile/{z}/{y}/{x}
        $hosts = [
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer',
        ];

        for ($tileY = $minTileY; $tileY <= $maxTileY; $tileY++) {
            if ($tileY < 0 || $tileY >= $n) {
                continue;
            }
            for ($tileX = $minTileX; $tileX <= $maxTileX; $tileX++) {
                $wrappedX = (($tileX % $n) + $n) % $n;

                $tileBin = null;
                foreach ($hosts as $host) {
                    try {
                        $url = sprintf('%s/tile/%d/%d/%d', $host, $zoom, $tileY, $wrappedX);
                        $res = Http::timeout(8)->retry(1, 250)->get($url);
                        if ($res->successful()) {
                            $tileBin = $res->body();
                            if (is_string($tileBin) && $tileBin !== '') {
                                break;
                            }
                        }
                    } catch (\Throwable) {
                        // coba host berikutnya
                    }
                }

                if (! is_string($tileBin) || $tileBin === '') {
                    continue;
                }

                $tileImg = @imagecreatefromstring($tileBin);
                if (! $tileImg) {
                    continue;
                }

                $dstX = ($tileX * $tileSize) - $topLeftPxX;
                $dstY = ($tileY * $tileSize) - $topLeftPxY;

                imagecopy($img, $tileImg, (int) $dstX, (int) $dstY, 0, 0, $tileSize, $tileSize);
                imagedestroy($tileImg);
            }
        }

        $markerX = (int) round($centerPxX - $topLeftPxX);
        $markerY = (int) round($centerPxY - $topLeftPxY);

        $red = imagecolorallocate($img, 220, 0, 0);
        $dark = imagecolorallocate($img, 140, 0, 0);
        $mSize = 12;
        imagefilledellipse($img, $markerX, $markerY, $mSize, $mSize, $red);
        imageellipse($img, $markerX, $markerY, $mSize, $mSize, $dark);
        imagefilledellipse($img, $markerX, $markerY, 4, 4, $white);

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        if (! is_string($png) || $png === '') {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode($png);
    }

    /**
     * @return array<string, string>
     */
    private function resolveGeoFields(Request $request): array
    {
        $geo = [
            'geo_jalan' => (string) $request->input('geo_jalan', ''),
            'geo_kelurahan' => (string) $request->input('geo_kelurahan', ''),
            'geo_kecamatan' => (string) $request->input('geo_kecamatan', ''),
            'geo_kota' => (string) $request->input('geo_kota', ''),
            'geo_provinsi' => (string) $request->input('geo_provinsi', ''),
            'geo_kode_pos' => (string) $request->input('geo_kode_pos', ''),
        ];

        if ($request->filled('latitude') && $request->filled('longitude') && trim($geo['geo_jalan']) === '') {
            return app(ReverseGeocodeService::class)->lookup(
                (float) $request->input('latitude'),
                (float) $request->input('longitude')
            );
        }

        return $geo;
    }
}
