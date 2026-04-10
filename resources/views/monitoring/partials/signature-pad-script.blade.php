{{-- Parameter: $formId (string), $requireBothSignatures (bool) --}}
@php
    $formId = $formId ?? 'formBam';
    $requireBothSignatures = $requireBothSignatures ?? true;
@endphp
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
(function () {
    const form = document.getElementById(@json($formId));
    if (!form) return;

    const canvasMitra = document.getElementById('canvas_signature_mitra');
    const canvasPetugas = document.getElementById('canvas_signature_petugas');
    const hiddenMitra = document.getElementById('signature_mitra');
    const hiddenPetugas = document.getElementById('signature_petugas');
    const requireBoth = @json($requireBothSignatures);

    function resizeCanvas(canvas) {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const w = canvas.offsetWidth;
        const h = canvas.offsetHeight;
        canvas.width = w * ratio;
        canvas.height = h * ratio;
        const ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);
    }

    resizeCanvas(canvasMitra);
    resizeCanvas(canvasPetugas);

    const padMitra = new SignaturePad(canvasMitra, { backgroundColor: 'rgb(255,255,255)' });
    const padPetugas = new SignaturePad(canvasPetugas, { backgroundColor: 'rgb(255,255,255)' });

    window.addEventListener('resize', function () {
        const dM = padMitra.toData();
        const dP = padPetugas.toData();
        resizeCanvas(canvasMitra);
        resizeCanvas(canvasPetugas);
        padMitra.clear();
        padPetugas.clear();
        padMitra.fromData(dM);
        padPetugas.fromData(dP);
    });

    function showStatus(id, show) {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('d-none', !show);
    }

    document.getElementById('sigClearMitra').addEventListener('click', function () {
        padMitra.clear();
        hiddenMitra.value = '';
        showStatus('sigStatusMitra', false);
    });
    document.getElementById('sigClearPetugas').addEventListener('click', function () {
        padPetugas.clear();
        hiddenPetugas.value = '';
        showStatus('sigStatusPetugas', false);
    });

    document.getElementById('sigSaveMitra').addEventListener('click', function () {
        if (padMitra.isEmpty()) {
            alert('Tanda tangan mitra masih kosong.');
            return;
        }
        hiddenMitra.value = padMitra.toDataURL('image/png');
        showStatus('sigStatusMitra', true);
    });
    document.getElementById('sigSavePetugas').addEventListener('click', function () {
        if (padPetugas.isEmpty()) {
            alert('Tanda tangan petugas masih kosong.');
            return;
        }
        hiddenPetugas.value = padPetugas.toDataURL('image/png');
        showStatus('sigStatusPetugas', true);
    });

    form.addEventListener('submit', function (e) {
        hiddenMitra.value = padMitra.isEmpty() ? '' : padMitra.toDataURL('image/png');
        hiddenPetugas.value = padPetugas.isEmpty() ? '' : padPetugas.toDataURL('image/png');

        if (requireBoth && (!hiddenMitra.value || !hiddenPetugas.value)) {
            e.preventDefault();
            alert('Kedua tanda tangan (mitra & petugas) wajib diisi sebelum mengirim.');
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
        }
    });
})();
</script>
