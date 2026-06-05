@extends('guest.layouts.app')
@section('title', 'Hadiah Saya')

@php
    $nikKey  = 'gacha_opened_' . $karyawan->nik;
    $menang  = !is_null($hadiah);
    $barang  = $hadiah?->barang ?? '';
    $qrCode  = $hadiah?->qr_code ?? '';
    $status  = $hadiah?->status ?? '';
@endphp

@section('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
.gacha-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 32px 0 24px;
}

/* ── Kotak hadiah ── */
.gift-box-wrap {
    position: relative;
    width: 200px;
    height: 200px;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    user-select: none;
    display: flex;
    align-items: center;
    justify-content: center;
}
.gift-img {
    width: 200px;
    height: 200px;
    object-fit: contain;
    filter: drop-shadow(0 12px 24px rgba(0,0,0,.18));
    transition: transform .15s;
}
.gift-box-wrap:active .gift-img { transform: scale(.93); }

/* Glow di bawah kotak */
.gift-glow {
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 120px;
    height: 20px;
    background: radial-gradient(ellipse, rgba(0,0,0,.15) 0%, transparent 70%);
    border-radius: 50%;
}

@keyframes float {
    0%,100% { transform: translateY(0); }
    50%      { transform: translateY(-12px); }
}
.gift-box-wrap.idle .gift-img {
    animation: float 2.4s ease-in-out infinite;
}
.gift-box-wrap.idle .gift-glow {
    animation: float-shadow 2.4s ease-in-out infinite;
}
@keyframes float-shadow {
    0%,100% { transform: translateX(-50%) scaleX(1); opacity: .6; }
    50%      { transform: translateX(-50%) scaleX(.7); opacity: .3; }
}

@keyframes shake {
    0%,100% { transform: translateX(0) rotate(0); }
    15%      { transform: translateX(-8px) rotate(-5deg); }
    30%      { transform: translateX(8px) rotate(5deg); }
    45%      { transform: translateX(-6px) rotate(-3deg); }
    60%      { transform: translateX(6px) rotate(3deg); }
    75%      { transform: translateX(-3px) rotate(-1deg); }
}
.gift-box-wrap.shaking .gift-img {
    animation: shake .55s ease-in-out forwards;
}

@keyframes pop-out {
    0%   { transform: scale(1); opacity: 1; }
    50%  { transform: scale(1.4); opacity: .7; }
    100% { transform: scale(0); opacity: 0; }
}
.gift-box-wrap.popping .gift-img {
    animation: pop-out .4s ease-in forwards;
}

/* Particles */
.particles {
    position: absolute;
    inset: 0;
    pointer-events: none;
}
.particle {
    position: absolute;
    width: 9px; height: 9px;
    border-radius: 50%;
    opacity: 0;
}
@keyframes burst {
    0%   { transform: translate(0,0) scale(1); opacity: 1; }
    100% { transform: var(--tx,60px) var(--ty,-60px) scale(0); opacity: 0; }
}

/* Tombol buka */
.btn-buka {
    margin-top: 28px;
    padding: 14px 40px;
    border-radius: 14px;
    border: none;
    background: linear-gradient(135deg, #43a047, #2e7d32);
    color: #fff;
    font-family: inherit;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    letter-spacing: .3px;
    box-shadow: 0 4px 18px rgba(43,122,49,.3);
    transition: transform .15s, box-shadow .15s;
    -webkit-tap-highlight-color: transparent;
}
.btn-buka:active { transform: scale(.95); box-shadow: none; }
.btn-buka:disabled { opacity: .5; cursor: not-allowed; transform: none; }

.hint {
    margin-top: 14px;
    font-size: 12px;
    font-weight: 600;
    color: #999;
    letter-spacing: .5px;
    text-align: center;
}

/* ── Reveal ── */
.reveal-wrap {
    display: none;
    flex-direction: column;
    align-items: center;
    width: 100%;
}
@keyframes slide-up {
    from { opacity:0; transform: translateY(30px); }
    to   { opacity:1; transform: translateY(0); }
}
.reveal-wrap.visible {
    display: flex;
    animation: slide-up .45s cubic-bezier(.34,1.3,.64,1) forwards;
}
.reveal-wrap.instant {
    display: flex;
}

/* ── Card menang ── */
.reveal-menang {
    text-align: center;
    padding: 28px 20px 24px;
    background: #f0fdf4;
    border: 1.5px solid #bbf7d0;
    border-radius: 20px;
    width: 100%;
}

/* ── QR section ── */
.qr-section {
    margin-top: 20px;
    padding: 20px 16px;
    background: #fff;
    border: 1.5px solid #e0e0e0;
    border-radius: 16px;
    text-align: center;
    width: 100%;
}
.qr-label {
    font-size: 11px;
    font-weight: 700;
    color: #2e7d32;
    letter-spacing: 1px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.qr-canvas-wrap { display:flex; justify-content:center; margin-bottom:14px; }
.qr-canvas-inner {
    background: #fff;
    padding: 12px;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    display: inline-block;
}
.qr-code-text {
    font-family: monospace;
    font-size: 16px;
    font-weight: 800;
    color: #1e293b;
    letter-spacing: 3px;
    margin-bottom: 4px;
    word-break: break-all;
}
.qr-hint {
    font-size: 11px;
    color: #666;
    margin-bottom: 16px;
    line-height: 1.5;
}
.qr-btn-row { display:flex; gap:8px; justify-content:center; }
.btn-salin {
    flex:1; max-width:160px;
    padding: 10px 14px; border-radius: 10px;
    border: 1.5px solid #4ade80; background: #fff;
    color: #16a34a; font-size: 12px; font-weight: 700;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    font-family: inherit; transition: all .2s;
}
.btn-unduh {
    flex:1; max-width:160px;
    padding: 10px 14px; border-radius: 10px;
    border: none; background: linear-gradient(135deg,#43a047,#2e7d32);
    color: #fff; font-size: 12px; font-weight: 700;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    font-family: inherit; transition: all .2s;
}
.badge-diambil {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: 20px; color: #16a34a;
    font-size: 12px; font-weight: 700; margin-bottom: 16px;
}

/* ── Card kalah ── */
.reveal-kalah {
    text-align: center;
    padding: 40px 24px;
    background: #f5f5f5;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    width: 100%;
}

/* ── Confetti ── */
.confetti-wrap {
    position: fixed; inset: 0;
    pointer-events: none; z-index: 999; overflow: hidden;
}
.confetti-piece {
    position: absolute; width: 10px; height: 10px;
    top: -14px; opacity: 0;
}
@keyframes confetti-fall {
    0%   { transform: translateY(0) rotate(0deg); opacity: 1; }
    100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
}

@keyframes spin { to { transform: rotate(360deg); } }
.spinner { animation: spin .7s linear infinite; }
</style>
@endsection

@section('content')

{{-- Reset jika ?reset=1 --}}
@if(request('reset') === '1')
<script>
    localStorage.removeItem('{{ $nikKey }}');
    window.location.href = '{{ url()->current() }}';
</script>
@endif

{{-- Data PHP → JS --}}
<script>
const _gacha = {
    nikKey : '{{ $nikKey }}',
    menang : {{ $menang ? 'true' : 'false' }},
    barang : '{{ addslashes($barang) }}',
    qrCode : '{{ addslashes($qrCode) }}',
    status : '{{ $status }}',
};
</script>

<div class="gacha-wrap" id="gachaWrap">

    {{-- ── KOTAK HADIAH ── --}}
    <div class="gift-box-wrap idle" id="giftBox" onclick="mulaiGacha()">
        <img class="gift-img" src="{{ asset('images/gift-box.png') }}" alt="Kotak Hadiah">
        <div class="gift-glow"></div>
        <div class="particles" id="particles"></div>
    </div>

    <div class="hint" id="hintText">Ketuk kotak untuk membuka hadiah</div>
    <button class="btn-buka" id="btnBuka" onclick="mulaiGacha()">
        <i class="fa-solid fa-gift" style="margin-right:8px;"></i>Buka Hadiah
    </button>

    {{-- ── REVEAL ── --}}
    <div class="reveal-wrap" id="revealWrap">

        @if($menang)
        {{-- ✅ MENANG --}}
        <div class="reveal-menang">
            <div style="font-size:11px;font-weight:700;color:#2e7d32;letter-spacing:1px;margin-bottom:6px;">
                🎊 SELAMAT!
            </div>
            <div style="font-size:11px;font-weight:700;color:#666;letter-spacing:1px;margin-bottom:8px;">
                KAMU MENDAPATKAN
            </div>
            <div style="font-size:26px;font-weight:800;color:#1a3320;line-height:1.25;margin-bottom:16px;">
                {{ $barang }}
            </div>

            @if($status === 'sudah_diambil')
            <div class="badge-diambil">
                <i class="fa-solid fa-circle-check"></i> Hadiah sudah diambil
                @if($hadiah->scanned_at)
                &nbsp;· {{ $hadiah->scanned_at->format('d M Y, H:i') }}
                @endif
            </div>
            @endif

            @if($qrCode && $status !== 'sudah_diambil')
            <div class="qr-section">
                <div class="qr-label">
                    <i class="fa-solid fa-qrcode"></i> KODE PENGAMBILAN
                </div>
                <div class="qr-canvas-wrap">
                    <div class="qr-canvas-inner">
                        <canvas id="qrCanvas" style="display:block;"></canvas>
                    </div>
                </div>
                <div class="qr-code-text">{{ $qrCode }}</div>
                <div class="qr-hint">
                    <i class="fa-solid fa-circle-info" style="margin-right:3px;"></i>
                    Tunjukkan kode ini kepada panitia untuk mengambil hadiah
                </div>
                <div class="qr-btn-row">
                    <button class="btn-salin" onclick="salinKode('{{ $qrCode }}', this)">
                        <i class="fa-solid fa-copy"></i> Salin Kode
                    </button>
                    <button class="btn-unduh" onclick="unduhQR()">
                        <i class="fa-solid fa-download"></i> Unduh QR
                    </button>
                </div>
                <div style="margin-top:12px;padding:10px 12px;background:rgba(34,197,94,.08);
                            border-radius:10px;font-size:11px;color:#166534;line-height:1.6;text-align:left;">
                    <strong>💡 Tips:</strong>
                    Simpan screenshot atau unduh QR code sebagai cadangan.
                    Tunjukkan kepada panitia saat pengambilan hadiah.
                </div>
            </div>
            @endif

            @if($status === 'sudah_diambil' && $qrCode)
            <div class="qr-section" style="opacity:.5;margin-top:16px;">
                <div class="qr-label"><i class="fa-solid fa-qrcode"></i> QR SUDAH DIGUNAKAN</div>
                <div class="qr-canvas-wrap">
                    <div class="qr-canvas-inner" style="filter:grayscale(1);">
                        <canvas id="qrCanvasUsed" style="display:block;"></canvas>
                    </div>
                </div>
                <div class="qr-code-text" style="color:#999;">{{ $qrCode }}</div>
            </div>
            @endif
        </div>

        @else
        {{-- ❌ BELUM BERUNTUNG --}}
        <div class="reveal-kalah">
            <div style="font-size:52px;margin-bottom:12px;">😔</div>
            <div style="font-size:18px;font-weight:800;color:#1a3320;margin-bottom:8px;">
                Belum Beruntung
            </div>
            <div style="font-size:13px;color:#666;line-height:1.6;max-width:260px;margin:0 auto;">
                Kamu belum mendapatkan hadiah kali ini. Tetap semangat dan nikmati acara gathering!
            </div>
        </div>
        @endif

    </div>{{-- /reveal-wrap --}}

</div>{{-- /gacha-wrap --}}

<div class="confetti-wrap" id="confettiWrap"></div>

@endsection

@section('scripts')
<script>
let opened = false;

/* ── Init ── */
(function init() {
    if (localStorage.getItem(_gacha.nikKey)) {
        tampilHasil(true);
    }
})();

/* ── Mulai gacha ── */
function mulaiGacha() {
    if (opened) return;
    opened = true;

    const box     = document.getElementById('giftBox');
    const btnBuka = document.getElementById('btnBuka');
    const hint    = document.getElementById('hintText');

    btnBuka.disabled = true;
    hint.style.display = 'none';
    btnBuka.innerHTML  = '<i class="fa-solid fa-circle-notch spinner" style="margin-right:8px;"></i>Membuka...';

    /* Fase 1: shake */
    box.classList.remove('idle');
    box.classList.add('shaking');

    setTimeout(function () {
        burstParticles();

        /* Fase 2: pop */
        box.classList.remove('shaking');
        box.classList.add('popping');

        setTimeout(function () {
            box.style.display     = 'none';
            btnBuka.style.display = 'none';

            localStorage.setItem(_gacha.nikKey, '1');

            if (_gacha.menang) launchConfetti();
            tampilHasil(false);
        }, 420);

    }, 580);
}

/* ── Tampilkan hasil ── */
function tampilHasil(instant) {
    if (instant) {
        document.getElementById('giftBox').style.display     = 'none';
        document.getElementById('btnBuka').style.display     = 'none';
        document.getElementById('hintText').style.display    = 'none';
    }

    const reveal = document.getElementById('revealWrap');
    reveal.classList.add(instant ? 'instant' : 'visible');

    if (_gacha.qrCode) {
        setTimeout(renderQR, instant ? 80 : 520);
    }
}

/* ── Render QR ── */
function renderQR() {
    ['qrCanvas','qrCanvasUsed'].forEach(function(id) {
        const canvas = document.getElementById(id);
        if (!canvas) return;

        const tmp = document.createElement('div');
        new QRCode(tmp, {
            text        : _gacha.qrCode,
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
    });
}

/* ── Particles ── */
function burstParticles() {
    const container = document.getElementById('particles');
    const colors    = ['#fdd835','#ef5350','#42a5f5','#ab47bc','#ff7043','#26c6da','#66bb6a'];
    const count     = 20;
    const cx = 100, cy = 100;

    for (let i = 0; i < count; i++) {
        const p     = document.createElement('div');
        p.className = 'particle';
        const angle = (i / count) * Math.PI * 2;
        const dist  = 55 + Math.random() * 55;
        const tx    = Math.cos(angle) * dist + 'px';
        const ty    = Math.sin(angle) * dist + 'px';

        p.style.cssText = `
            left:${cx-4}px;top:${cy-4}px;
            background:${colors[i % colors.length]};
            --tx:translateX(${tx});--ty:translateY(${ty});
            animation:burst .65s ease-out ${i*.025}s forwards;
        `;
        container.appendChild(p);
    }
}

/* ── Confetti ── */
function launchConfetti() {
    const wrap   = document.getElementById('confettiWrap');
    const colors = ['#fdd835','#43a047','#ef5350','#42a5f5','#ab47bc','#ff7043','#26c6da'];
    const shapes = ['circle','square'];

    for (let i = 0; i < 90; i++) {
        const p      = document.createElement('div');
        p.className  = 'confetti-piece';
        const color  = colors[Math.floor(Math.random() * colors.length)];
        const shape  = shapes[Math.floor(Math.random() * shapes.length)];
        const size   = 6 + Math.random() * 8;

        p.style.cssText = `
            left:${Math.random()*100}%;
            width:${size}px;height:${size}px;
            background:${color};
            border-radius:${shape==='circle'?'50%':'2px'};
            transform:rotate(${Math.random()*360}deg);
            animation:confetti-fall ${2.2+Math.random()*1.5}s ease-in ${Math.random()*1.2}s forwards;
        `;
        wrap.appendChild(p);
    }

    setTimeout(function () { wrap.innerHTML = ''; }, 5000);
}

/* ── Salin kode ── */
function salinKode(kode, btn) {
    const orig = btn.innerHTML;
    const done = function() {
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin!';
        setTimeout(function() { btn.innerHTML = orig; }, 2000);
    };
    navigator.clipboard
        ? navigator.clipboard.writeText(kode).then(done).catch(function(){ fallbackCopy(kode); done(); })
        : (fallbackCopy(kode), done());
}
function fallbackCopy(kode) {
    const el = document.createElement('textarea');
    el.value = kode; document.body.appendChild(el); el.select();
    document.execCommand('copy'); document.body.removeChild(el);
}

/* ── Unduh QR ── */
function unduhQR() {
    const canvas = document.getElementById('qrCanvas');
    if (!canvas) return;

    const padding = 24, labelH = 50;
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
    ctx.fillText(_gacha.qrCode, nc.width/2, canvas.height + padding + 26);

    ctx.fillStyle = '#94a3b8';
    ctx.font      = '11px sans-serif';
    ctx.fillText('Kode Pengambilan Hadiah', nc.width/2, canvas.height + padding + 44);

    const link    = document.createElement('a');
    link.download = 'hadiah-' + _gacha.qrCode + '.png';
    link.href     = nc.toDataURL('image/png');
    link.click();
}
</script>
@endsection