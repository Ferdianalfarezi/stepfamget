@extends('guest.layouts.app')
@section('title', 'Hadiah Saya')

@section('content')

{{-- ── Header ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div class="section-title" style="margin-bottom:0;">HADIAH SAYA</div>
    <span style="background:#e8f5e9;color:#2e7d32;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">
        {{ $hadiahku->count() }} hadiah
    </span>
</div>

@if($hadiahku->isEmpty())
<div style="text-align:center;padding:60px 20px;color:#ccc;">
    <i class="fa-solid fa-gift" style="font-size:48px;display:block;margin-bottom:12px;"></i>
    <div style="font-size:13px;font-weight:500;">Kamu belum mendapatkan hadiah</div>
</div>

@else

@foreach($hadiahku as $h)
<div class="card" style="padding:20px;margin-bottom:12px;border-radius:18px;">

    {{-- Badge pojok kanan + Nama tengah --}}
    <div style="position:relative;margin-bottom:16px;">

        

        {{-- Nama barang tengah --}}
        <div style="text-align:center;padding:16px 0 4px;">
            <i class="fa-solid fa-gift" style="color:#2e7d32;font-size:28px;display:block;margin-bottom:10px;"></i>
            <div style="font-size:22px;font-weight:800;color:#111;line-height:1.3;">{{ $h->barang }}</div>
        </div>
    </div>

    {{-- Waktu diambil --}}
    @if($h->scanned_at)
    <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:10px;">
        <div style="font-size:11px;font-weight:700;color:#999;letter-spacing:.5px;">DIAMBIL</div>
        <div style="font-size:13px;font-weight:600;color:#1e293b;">
            {{ $h->scanned_at->format('d M Y, H:i') }}
        </div>
    </div>
    @endif

    {{-- QR Code section — hanya kalau siap diambil --}}
    @if($h->status === 'siap_diambil' && $h->qr_code)
    <div style="margin-top:16px;padding:20px 16px;background:#f0fdf4;
                border:1.5px solid #bbf7d0;border-radius:16px;text-align:center;">

        {{-- Label --}}
        <div style="font-size:11px;font-weight:700;color:#16a34a;letter-spacing:1px;margin-bottom:14px;
                    display:flex;align-items:center;justify-content:center;gap:6px;">
            <i class="fa-solid fa-qrcode"></i> KODE PENGAMBILAN
        </div>

        {{-- QR Image --}}
        <div style="display:flex;justify-content:center;margin-bottom:14px;">
            <div style="background:#fff;padding:12px;border-radius:14px;
                        box-shadow:0 2px 12px rgba(0,0,0,.08);display:inline-block;">
                <canvas id="qrCanvas{{ $h->id }}" style="display:block;"></canvas>
            </div>
        </div>

        {{-- Kode teks --}}
        <div style="font-family:monospace;font-size:16px;font-weight:800;
                    color:#1e293b;letter-spacing:3px;margin-bottom:4px;word-break:break-all;">
            {{ $h->qr_code }}
        </div>
        <div style="font-size:11px;color:#16a34a;margin-bottom:16px;line-height:1.5;">
            <i class="fa-solid fa-circle-info" style="margin-right:3px;"></i>
            Tunjukkan kode ini kepada panitia untuk mengambil hadiah
        </div>

        {{-- Tombol --}}
        <div style="display:flex;gap:8px;justify-content:center;">
            <button onclick="salinKode('{{ $h->qr_code }}', this)"
                style="flex:1;max-width:160px;padding:10px 14px;border-radius:10px;
                       border:1.5px solid #4ade80;background:#fff;
                       color:#16a34a;font-size:12px;font-weight:700;cursor:pointer;
                       display:flex;align-items:center;justify-content:center;gap:6px;
                       font-family:inherit;transition:all .2s;">
                <i class="fa-solid fa-copy"></i> Salin Kode
            </button>
            <button onclick="unduhQR('qrCanvas{{ $h->id }}', '{{ $h->qr_code }}')"
                style="flex:1;max-width:160px;padding:10px 14px;border-radius:10px;
                       border:none;background:linear-gradient(135deg,#43a047,#2e7d32);
                       color:#fff;font-size:12px;font-weight:700;cursor:pointer;
                       display:flex;align-items:center;justify-content:center;gap:6px;
                       font-family:inherit;transition:all .2s;">
                <i class="fa-solid fa-download"></i> Unduh QR
            </button>
        </div>

        {{-- Tips --}}
        <div style="margin-top:12px;padding:10px 12px;background:rgba(34,197,94,.08);
                    border-radius:10px;font-size:11px;color:#166534;line-height:1.6;text-align:left;">
            <strong>💡 Tips:</strong> Simpan screenshot halaman ini atau unduh gambar QR code
            sebagai cadangan. Tunjukkan kepada panitia saat pengambilan hadiah.
        </div>
    </div>
    @endif

    {{-- Sudah diambil --}}
    @if($h->status === 'sudah_diambil')
    <div style="margin-top:14px;padding:14px;background:#f0fdf4;
                border:1.5px solid #bbf7d0;border-radius:14px;text-align:center;">
        <i class="fa-solid fa-circle-check" style="font-size:28px;color:#22c55e;display:block;margin-bottom:6px;"></i>
        <div style="font-size:13px;font-weight:700;color:#16a34a;">Hadiah sudah berhasil diambil</div>
        @if($h->scanned_at)
        <div style="font-size:11px;color:#4ade80;margin-top:3px;">
            {{ $h->scanned_at->format('d M Y, H:i') }}
        </div>
        @endif
    </div>
    @endif

</div>
@endforeach

@endif

@endsection

@section('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @foreach($hadiahku as $h)
    @if($h->status === 'siap_diambil' && $h->qr_code)
    (function () {
        const canvas = document.getElementById('qrCanvas{{ $h->id }}');
        if (!canvas) return;

        const tmp = document.createElement('div');
        new QRCode(tmp, {
            text        : '{{ $h->qr_code }}',
            width       : 180,
            height      : 180,
            colorDark   : '#1e293b',
            colorLight  : '#ffffff',
            correctLevel: QRCode.CorrectLevel.H,
        });

        setTimeout(function () {
            const img = tmp.querySelector('img');
            if (!img) return;
            const draw = function () {
                canvas.width  = img.naturalWidth  || 180;
                canvas.height = img.naturalHeight || 180;
                canvas.getContext('2d').drawImage(img, 0, 0);
            };
            img.complete ? draw() : (img.onload = draw);
        }, 100);
    })();
    @endif
    @endforeach
});

function salinKode(kode, btn) {
    const copy = function () {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin!';
        btn.style.background  = '#f0fdf4';
        btn.style.borderColor = '#4ade80';
        btn.style.color       = '#16a34a';
        setTimeout(function () {
            btn.innerHTML         = orig;
            btn.style.background  = '#fff';
            btn.style.borderColor = '#4ade80';
            btn.style.color       = '#16a34a';
        }, 2000);
    };

    if (navigator.clipboard) {
        navigator.clipboard.writeText(kode).then(copy).catch(function () {
            fallbackCopy(kode); copy();
        });
    } else {
        fallbackCopy(kode); copy();
    }
}

function fallbackCopy(kode) {
    const el = document.createElement('textarea');
    el.value = kode;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
}

function unduhQR(canvasId, kode) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const padding = 24;
    const labelH  = 50;
    const nc      = document.createElement('canvas');
    nc.width      = canvas.width  + padding * 2;
    nc.height     = canvas.height + padding * 2 + labelH;
    const ctx     = nc.getContext('2d');

    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, nc.width, nc.height);
    ctx.drawImage(canvas, padding, padding);

    ctx.fillStyle = '#1e293b';
    ctx.font      = 'bold 14px monospace';
    ctx.textAlign = 'center';
    ctx.fillText(kode, nc.width / 2, canvas.height + padding + 26);

    ctx.fillStyle = '#94a3b8';
    ctx.font      = '11px sans-serif';
    ctx.fillText('Kode Pengambilan Hadiah', nc.width / 2, canvas.height + padding + 44);

    const link    = document.createElement('a');
    link.download = 'hadiah-' + kode + '.png';
    link.href     = nc.toDataURL('image/png');
    link.click();
}
</script>
@endsection