{{--
    Dua canvas Signature Pad + hidden input untuk POST.
    Canvas id: canvas_signature_mitra, canvas_signature_petugas
    Hidden name: signature_mitra, signature_petugas (base64 PNG saat submit / tombol Simpan)
    Opsional: $showSignatureIntro (default true)
--}}
@php($showSignatureIntro = $showSignatureIntro ?? true)
@if ($showSignatureIntro)
    <h2 class="h6">Tanda tangan digital</h2>
    <p class="small text-muted mb-3">
        Gambar di canvas, lalu klik <strong>Simpan</strong> untuk memasukkan ke form, atau biarkan — saat kirim form, tanda tangan akan diambil otomatis.
    </p>
@endif
@error('signature_mitra')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
@error('signature_petugas')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="canvas_signature_mitra">Tanda tangan mitra binaan</label>
        <div class="border rounded bg-white position-relative shadow-sm" style="touch-action: none;">
            <canvas id="canvas_signature_mitra" class="w-100 rounded" style="height:220px;" aria-label="Area tanda tangan mitra"></canvas>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="sigClearMitra">Clear</button>
            <button type="button" class="btn btn-sm btn-success" id="sigSaveMitra">Simpan</button>
        </div>
        <div class="small text-success mt-1 d-none" id="sigStatusMitra">Sudah disalin ke form (hidden).</div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="canvas_signature_petugas">Tanda tangan petugas penagih</label>
        <div class="border rounded bg-white position-relative shadow-sm" style="touch-action: none;">
            <canvas id="canvas_signature_petugas" class="w-100 rounded" style="height:220px;" aria-label="Area tanda tangan petugas"></canvas>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="sigClearPetugas">Clear</button>
            <button type="button" class="btn btn-sm btn-success" id="sigSavePetugas">Simpan</button>
        </div>
        <div class="small text-success mt-1 d-none" id="sigStatusPetugas">Sudah disalin ke form (hidden).</div>
    </div>
</div>
<input type="hidden" name="signature_mitra" id="signature_mitra" value="{{ old('signature_mitra') }}">
<input type="hidden" name="signature_petugas" id="signature_petugas" value="{{ old('signature_petugas') }}">
