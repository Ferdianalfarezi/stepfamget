@extends('guest.layouts.app')
@section('title', 'Data Baju')

@section('content')

@php
  $sudahKonfirmasi = $karyawan->isBajuConfirmedThisYear();
  $isExpired       = $isExpired ?? false;
  $deadline        = isset($menu) ? $menu->berlaku_hingga : null;
  // Cek apakah semua member sudah punya ukuran (untuk init allSaved)
  $semuaSudahIsi   = $sudahKonfirmasi && $members->every(fn($m) => !empty($m->ukuran_kaos));
@endphp

{{-- ── Hero Banner ─────────────────────────────────────────────────────────── --}}
<div style="background:#1a3320;border-radius:18px;padding:18px;margin-bottom:16px;position:relative;overflow:hidden;">
  <div style="position:absolute;top:-20px;right:-30px;width:110px;height:110px;border-radius:50%;
              background:rgba(255,255,255,.05);pointer-events:none;"></div>
  <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,.4);letter-spacing:1px;margin-bottom:6px;">
    UKURAN BAJU
  </div>
  <div style="font-size:20px;font-weight:800;color:#fff;margin-bottom:8px;">
    Pilih ukuran kaos kamu &amp; keluarga
  </div>

  @if($isExpired)
    <div style="display:inline-flex;align-items:center;gap:6px;
                background:rgba(229,57,53,.25);border:1px solid rgba(229,57,53,.4);
                border-radius:20px;padding:5px 12px;">
      <i class="fa-solid fa-lock" style="color:#ff8a80;font-size:11px;"></i>
      <span style="font-size:12px;font-weight:700;color:#ff8a80;">Pendaftaran Ditutup</span>
    </div>
  @elseif($deadline)
    <div style="display:inline-flex;align-items:center;gap:6px;
                background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);
                border-radius:20px;padding:5px 12px;">
      <i class="fa-solid fa-hourglass-half" style="color:rgba(255,255,255,.6);font-size:11px;" id="heroIcon"></i>
      <span style="font-size:12px;font-weight:700;color:rgba(255,255,255,.8);">
        Ditutup dalam <span id="heroCountdown" style="font-variant-numeric:tabular-nums;">--:--:--</span>
      </span>
    </div>
  @else
    <div style="font-size:12px;color:rgba(255,255,255,.4);">Isi ukuran sebelum batas waktu</div>
  @endif
</div>

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
  <div class="section-title" style="margin-bottom:0;">ANGGOTA</div>
  <span style="background:#e8f5e9;color:#2e7d32;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">
    {{ $members->count() }} orang
  </span>
</div>

{{-- ── Progress bar ─────────────────────────────────────────────────────────── --}}
<div style="margin-bottom:16px;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
    <span style="font-size:11.5px;color:#999;">Sudah diisi</span>
    <span id="progressLabel" style="font-size:11.5px;font-weight:700;color:#2e7d32;">
      0 / {{ $members->count() }}
    </span>
  </div>
  <div style="height:6px;background:#f0f0f0;border-radius:10px;overflow:hidden;">
    <div id="progressBar"
      style="height:100%;width:0%;background:linear-gradient(90deg,#43a047,#2e7d32);
             border-radius:10px;transition:width .3s ease;"></div>
  </div>
</div>

{{-- ── List Member ─────────────────────────────────────────────────────────── --}}
<div id="memberList">
  @foreach($members as $d)
  @php
    $adaDataLama = !empty($d->ukuran_kaos);
    $cardFilled  = $sudahKonfirmasi && !empty($d->ukuran_kaos);
  @endphp
  <div class="member-card card" data-id="{{ $d->id }}"
       data-ukuran="{{ $d->ukuran_kaos ?? '' }}"
       data-jenis="{{ $d->jenis_kaos ?? '' }}"
       data-lengan="{{ $d->lengan_kaos ?? '' }}"
       data-hubungan="{{ $d->hubungan }}"
       data-filled="{{ $cardFilled ? '1' : '0' }}"
       data-confirmed="{{ $cardFilled ? '1' : '0' }}"
       style="padding:14px;margin-bottom:10px;transition:border .2s;">

    {{-- ── Top row ── --}}
    <div style="display:flex;align-items:center;gap:12px;">

      {{-- Avatar --}}
      <div style="width:44px;height:44px;border-radius:12px;background:#e8f5e9;color:#2e7d32;
                  display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:800;flex-shrink:0;">
        {{ strtoupper(substr($d->nama_keluarga, 0, 1)) }}
      </div>

      {{-- Info --}}
      <div style="flex:1;min-width:0;">
        <div style="font-size:14px;font-weight:700;color:#111;">{{ $d->nama_keluarga }}</div>
        <div style="font-size:11.5px;color:#999;margin-top:2px;">{{ $d->hubungan }}</div>

        {{-- Badge tahun lalu --}}
        @if($adaDataLama && !$sudahKonfirmasi)
        <div class="ref-tahun-lalu" style="margin-top:5px;">
          <span style="background:#fff8e1;color:#f57f17;border-radius:6px;padding:2px 8px;
                       font-size:10.5px;font-weight:700;border:1px solid #ffe082;display:inline-flex;
                       align-items:center;gap:4px;">
            <i class="fa-solid fa-clock-rotate-left" style="font-size:9px;"></i>
            Thn lalu: {{ $d->ukuran_kaos }}
            @if($d->jenis_kaos || $d->lengan_kaos)
              · {{ $d->jenis_kaos ?? 'Dewasa' }}{{ $d->lengan_kaos ? ' · '.$d->lengan_kaos : '' }}
            @endif
          </span>
        </div>
        @endif

        {{-- Summary tersimpan --}}
        @if($sudahKonfirmasi && !empty($d->ukuran_kaos))
        <div class="saved-summary" style="margin-top:4px;">
          <span style="background:#e8f5e9;color:#2e7d32;border-radius:6px;padding:3px 10px;
                       font-size:12px;font-weight:800;display:inline-block;margin-top:4px;">
            {{ $d->ukuran_kaos }}
          </span>
          <span style="font-size:11px;color:#999;margin-left:6px;">
            {{ $d->jenis_kaos }}{{ $d->lengan_kaos ? ' · '.$d->lengan_kaos : '' }}
          </span>
        </div>
        @endif
      </div>

      {{-- Kanan: tombol ubah + status check --}}
      <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
        @if($sudahKonfirmasi && !empty($d->ukuran_kaos) && !$isExpired)
        <button type="button"
          onclick="editCard({{ $d->id }}, this)"
          class="btn-ubah"
          style="padding:5px 10px;border-radius:20px;border:1.5px solid #e0e0e0;
                 background:#fff;font-size:11px;font-weight:700;color:#777;
                 cursor:pointer;transition:all .15s;white-space:nowrap;">
          <i class="fa-solid fa-pen" style="font-size:10px;margin-right:3px;"></i>Ubah
        </button>
        @endif

        <div class="status-check" style="width:24px;height:24px;border-radius:50%;
             background:#f0f0f0;display:flex;align-items:center;justify-content:center;
             flex-shrink:0;transition:all .2s;">
          <i class="fa-solid fa-check" style="font-size:10px;color:#ccc;"></i>
        </div>
      </div>
    </div>

    {{-- ── Form pilih ── --}}
    <div class="edit-mode" style="margin-top:12px;border-top:1px solid #f0f4f0;padding-top:12px;
         {{ $cardFilled ? 'display:none;' : '' }}">

      <div class="auto-info" style="display:none;margin-bottom:14px;">
        <div style="background:#f0f4f0;border-radius:8px;padding:8px 12px;
                    font-size:11.5px;color:#555;display:flex;align-items:center;gap:6px;">
          <i class="fa-solid fa-circle-info" style="color:#2e7d32;font-size:12px;"></i>
          <span class="auto-info-text"></span>
        </div>
      </div>

      <div class="jenis-section" style="display:none;">
        <div style="font-size:11px;font-weight:700;color:#999;margin-bottom:8px;letter-spacing:.3px;">JENIS KAOS</div>
        <div style="display:flex;gap:8px;margin-bottom:14px;">
          @foreach(['Dewasa','Anak'] as $jenis)
          <button type="button"
            onclick="selectJenis({{ $d->id }}, this, '{{ $jenis }}')"
            class="btn-jenis" data-jenis="{{ $jenis }}"
            style="flex:1;padding:8px;border-radius:8px;border:1.5px solid #e0e0e0;
                   background:#fff;font-size:12px;font-weight:700;color:#777;cursor:pointer;transition:all .15s;">
            @if($jenis === 'Dewasa') 🧑 @else 👶 @endif {{ $jenis }}
          </button>
          @endforeach
        </div>
      </div>

      @if($d->hubungan !== 'Anak')
      <div class="lengan-section" style="display:none;">
        <div style="font-size:11px;font-weight:700;color:#999;margin-bottom:8px;letter-spacing:.3px;">TIPE LENGAN</div>
        <div style="display:flex;gap:8px;margin-bottom:14px;">
          @foreach(['Lengan Pendek','Lengan Panjang'] as $lengan)
          <button type="button"
            onclick="selectLengan({{ $d->id }}, this, '{{ $lengan }}')"
            class="btn-lengan" data-lengan="{{ $lengan }}"
            style="flex:1;padding:8px;border-radius:8px;border:1.5px solid #e0e0e0;
                   background:#fff;font-size:12px;font-weight:700;color:#777;cursor:pointer;transition:all .15s;">
            @if($lengan === 'Lengan Pendek') 👕 @else 🧥 @endif {{ $lengan }}
          </button>
          @endforeach
        </div>
      </div>
      @endif

      <div style="font-size:11px;font-weight:700;color:#999;margin-bottom:8px;letter-spacing:.3px;">PILIH UKURAN</div>
      <div class="sz-grid" style="display:flex;gap:8px;flex-wrap:wrap;">
        @foreach(['S','M','L','XL','XXL','XXXL','XXXXL','XXXXXL'] as $sz)
        <button type="button"
          onclick="selectSize({{ $d->id }}, this, '{{ $sz }}')"
          class="btn-sz" data-sz="{{ $sz }}"
          style="padding:7px 14px;border-radius:8px;border:1.5px solid #e0e0e0;
                 background:#fff;font-size:12px;font-weight:700;color:#777;cursor:pointer;transition:all .15s;">
          {{ $sz }}
        </button>
        @endforeach
      </div>

      <div class="btn-batal-ubah-wrap" style="display:none;margin-top:12px;">
        <button type="button" onclick="batalEdit({{ $d->id }})"
          style="width:100%;padding:9px;border-radius:8px;border:1.5px solid #e0e0e0;
                 background:#fff;font-size:12px;font-weight:700;color:#999;cursor:pointer;">
          <i class="fa-solid fa-xmark" style="margin-right:4px;"></i>Batal Ubah
        </button>
      </div>

    </div>
  </div>
  @endforeach
</div>

@if($members->isEmpty())
<div style="text-align:center;padding:40px 20px;color:#ccc;">
  <i class="fa-solid fa-shirt" style="font-size:36px;display:block;margin-bottom:12px;"></i>
  <div style="font-size:13px;">Belum ada data anggota</div>
</div>
@endif

{{-- ── Global Save Button ───────────────────────────────────────────────────── --}}
<div style="margin-top:8px;margin-bottom:24px;">
  <button id="btnSaveAll" onclick="saveAll()" disabled
    style="width:100%;padding:14px;border-radius:12px;border:none;
           background:#e0e0e0;color:#aaa;font-size:14px;font-weight:800;
           cursor:not-allowed;transition:all .3s;">
    <span id="btnSaveLabel">Isi semua ukuran dulu</span>
  </button>
</div>

{{-- ── CSS ─────────────────────────────────────────────────────────────────── --}}
<style>
@keyframes fadeInUp {
  from { transform:translateX(-50%) translateY(10px); opacity:0; }
  to   { transform:translateX(-50%) translateY(0);    opacity:1; }
}
.btn-sz.active       { background:#e8f5e9 !important; border-color:#3d7a47 !important; color:#2e7d32 !important; }
.btn-sz.active-anak  { background:#fff8e1 !important; border-color:#f9a825 !important; color:#e65100 !important; }
.btn-jenis.active, .btn-lengan.active { background:#e8f5e9 !important; border-color:#3d7a47 !important; color:#2e7d32 !important; }
.member-card.is-filled  { border-color:#a5d6a7 !important; }
.member-card.is-editing { border-color:#ffa726 !important; background:#fffde7 !important; }
.btn-ubah:hover { background:#f5f5f5 !important; border-color:#bbb !important; }
</style>

{{-- ── JS ─────────────────────────────────────────────────────────────────── --}}
<script>
const BAJU_UPDATE_URL = "{{ route('guest.baju.update') }}";
const CSRF            = "{{ csrf_token() }}";
const TOTAL           = {{ $members->count() }};
const IS_EXPIRED      = @json($isExpired);
const DEADLINE        = @json($deadline ? $deadline->toIso8601String() : null);

// Batas ukuran kaos: kalau HUBUNGAN-nya "Anak" (bukan cuma jenis kaosnya),
// atau jenis kaos yang dipilih "Anak", ukuran dibatasi max XXL
const KID_SIZES = ['S','M','L','XL','XXL'];

const selectedSize   = {};
const selectedJenis  = {};
const selectedLengan = {};
const editingCards   = {};

// ── allSaved: true dari awal kalau semua member sudah confirmed di DB ─────────
let allSaved = @json($semuaSudahIsi);

function resolveAuto(hubungan) {
  switch (hubungan) {
    case 'Karyawan':  return { jenis: 'Dewasa', lengan: 'Lengan Pendek',  manual: false };
    case 'Karyawati': return { jenis: 'Dewasa', lengan: 'Lengan Panjang', manual: false };
    case 'Suami':     return { jenis: 'Dewasa', lengan: 'Lengan Pendek',  manual: false };
    case 'Istri':     return { jenis: 'Dewasa', lengan: 'Lengan Panjang', manual: false };
    default:          return { jenis: null,     lengan: null,             manual: true  };
  }
}

function getCard(id) { return document.querySelector('.member-card[data-id="' + id + '"]'); }

// ── Filter ukuran: restrict kalau HUBUNGAN card = Anak ATAU jenis kaos = Anak ──
function applySizeFilter(id, jenis) {
  var card = getCard(id);
  if (!card) return;

  var hubungan = card.dataset.hubungan || '';
  var restrict = hubungan === 'Anak' || jenis === 'Anak';

  card.querySelectorAll('.btn-sz').forEach(function(b) {
    var allowed = !restrict || KID_SIZES.includes(b.dataset.sz);
    b.style.display = allowed ? '' : 'none';
  });

  // kalau ukuran yg udah kepilih ternyata size besar & baru masuk mode restrict, reset
  if (restrict && selectedSize[id] && !KID_SIZES.includes(selectedSize[id])) {
    selectedSize[id] = null;
    card.querySelectorAll('.btn-sz').forEach(function(b) { b.classList.remove('active', 'active-anak'); });
  }
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg, bg) {
  bg = bg || '#2e7d32';
  var t = document.createElement('div');
  t.textContent   = msg;
  t.style.cssText = 'position:fixed;bottom:30px;left:50%;transform:translateX(-50%);background:' + bg +
    ';color:#fff;padding:12px 24px;border-radius:100px;font-size:13px;font-weight:600;' +
    'z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,.2);white-space:nowrap;animation:fadeInUp .3s ease;';
  document.body.appendChild(t);
  setTimeout(function() { t.remove(); }, 2000);
}

// ── Cek card lengkap ──────────────────────────────────────────────────────────
function isCardComplete(id) {
  var card     = getCard(id);
  var hubungan = card.dataset.hubungan || '';
  var auto     = resolveAuto(hubungan);
  var sz       = selectedSize[id];
  var jenis    = selectedJenis[id];
  var lengan   = selectedLengan[id];

  if (!sz) return false;
  if (auto.manual) {
    if (!jenis) return false;
    if (jenis === 'Dewasa' && !lengan) return false;
  }
  return true;
}

// ── Update progress & tombol save ─────────────────────────────────────────────
function updateProgress() {
  var filled = 0;
  document.querySelectorAll('.member-card').forEach(function(card) {
    if (card.dataset.filled === '1') filled++;
  });

  var pct = TOTAL > 0 ? Math.round(filled / TOTAL * 100) : 0;
  document.getElementById('progressBar').style.width   = pct + '%';
  document.getElementById('progressLabel').textContent = filled + ' / ' + TOTAL;

  var btn        = document.getElementById('btnSaveAll');
  var label      = document.getElementById('btnSaveLabel');
  var anyEditing = Object.values(editingCards).some(Boolean);

  if (IS_EXPIRED) {
    btn.disabled         = true;
    btn.style.cssText   += ';background:#e0e0e0;color:#aaa;cursor:not-allowed;border:none;';
    label.textContent    = 'Pendaftaran sudah ditutup';
    return;
  }

  if (anyEditing && filled === TOTAL) {
    // Ada card editing & semua filled → Simpan Perubahan (oranye)
    btn.disabled       = false;
    btn.style.background = 'linear-gradient(135deg,#fb8c00,#e65100)';
    btn.style.color      = '#fff';
    btn.style.cursor     = 'pointer';
    btn.style.border     = 'none';
    label.textContent    = 'Simpan Perubahan';

  } else if (filled === TOTAL && !anyEditing && allSaved) {
    // Semua filled, tidak ada editing, sudah pernah save → status tersimpan
    btn.disabled       = true;
    btn.style.background = '#e8f5e9';
    btn.style.color      = '#2e7d32';
    btn.style.cursor     = 'not-allowed';
    btn.style.border     = '1.5px solid #a5d6a7';
    label.innerHTML      = '<i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>Data telah tersimpan';

  } else if (filled === TOTAL && !anyEditing && !allSaved) {
    // Semua filled, belum pernah save → Simpan Semua Ukuran (hijau)
    btn.disabled       = false;
    btn.style.background = 'linear-gradient(135deg,#43a047,#2e7d32)';
    btn.style.color      = '#fff';
    btn.style.cursor     = 'pointer';
    btn.style.border     = 'none';
    label.textContent    = 'Simpan Semua Ukuran';

  } else {
    // Belum semua filled
    btn.disabled       = true;
    btn.style.background = '#e0e0e0';
    btn.style.color      = '#aaa';
    btn.style.cursor     = 'not-allowed';
    btn.style.border     = 'none';
    label.textContent    = filled === TOTAL
      ? 'Isi semua ukuran dulu'
      : 'Isi semua ukuran dulu (' + (TOTAL - filled) + ' belum)';
  }
}

// ── Tandai card selesai/belum ─────────────────────────────────────────────────
function markCard(id) {
  var card      = getCard(id);
  var done      = isCardComplete(id);
  var check     = card.querySelector('.status-check');
  var checkIcon = check.querySelector('i');

  card.dataset.filled = done ? '1' : '0';

  if (done) {
    card.classList.add('is-filled');
    check.style.background = '#2e7d32';
    checkIcon.style.color  = '#fff';
  } else {
    card.classList.remove('is-filled');
    check.style.background = '#f0f0f0';
    checkIcon.style.color  = '#ccc';
  }

  updateProgress();
}

// ── Warna tombol ukuran ───────────────────────────────────────────────────────
function applyColorMode(id, jenis) {
  var sz = selectedSize[id];
  getCard(id).querySelectorAll('.btn-sz').forEach(function(b) {
    b.classList.remove('active', 'active-anak');
  });
  if (sz) {
    var btn = getCard(id).querySelector('.btn-sz[data-sz="' + sz + '"]');
    if (btn) btn.classList.add(jenis === 'Anak' ? 'active-anak' : 'active');
  }
}

// ── Init card ─────────────────────────────────────────────────────────────────
function initCard(id) {
  var card     = getCard(id);
  var hubungan = card.dataset.hubungan || '';
  var auto     = resolveAuto(hubungan);

  if (card.dataset.confirmed === '1') {
    selectedSize[id]    = card.dataset.ukuran;
    selectedJenis[id]   = card.dataset.jenis;
    selectedLengan[id]  = card.dataset.lengan || null;
    card.dataset.filled = '1';
    var check     = card.querySelector('.status-check');
    var checkIcon = check.querySelector('i');
    card.classList.add('is-filled');
    check.style.background = '#2e7d32';
    checkIcon.style.color  = '#fff';
    updateProgress();
    return;
  }

  selectedSize[id]   = null;
  selectedJenis[id]  = null;
  selectedLengan[id] = null;

  var jenisSec = card.querySelector('.jenis-section');
  var lenganSec = card.querySelector('.lengan-section');
  var autoInfo  = card.querySelector('.auto-info');
  var autoText  = card.querySelector('.auto-info-text');

  if (!auto.manual) {
    if (jenisSec)  jenisSec.style.display  = 'none';
    if (lenganSec) lenganSec.style.display = 'none';
    autoInfo.style.display = 'block';
    selectedJenis[id]      = auto.jenis;
    selectedLengan[id]     = auto.lengan;
    autoText.textContent   = 'Jenis & lengan otomatis: ' + auto.jenis + (auto.lengan ? ' · ' + auto.lengan : '');
  } else {
    autoInfo.style.display = 'none';
    if (jenisSec) jenisSec.style.display = 'block';
  }

  // langsung batasi ukuran kalau hubungan-nya Anak, walau jenis kaos belum dipilih
  applySizeFilter(id, selectedJenis[id]);

  markCard(id);
}

// ── Edit card ─────────────────────────────────────────────────────────────────
function editCard(id, btnEl) {
  if (IS_EXPIRED) { showToast('Pendaftaran sudah ditutup', '#e53935'); return; }

  var card      = getCard(id);
  var auto      = resolveAuto(card.dataset.hubungan || '');
  var editMode  = card.querySelector('.edit-mode');
  var summary   = card.querySelector('.saved-summary');
  var batalWrap = card.querySelector('.btn-batal-ubah-wrap');

  if (summary) summary.style.display = 'none';
  if (btnEl)   btnEl.style.display   = 'none';

  editMode.style.display = 'block';
  if (batalWrap) batalWrap.style.display = 'block';

  // ── Tandai editing & reset allSaved ──────────────────────────────────────
  editingCards[id] = true;
  allSaved         = false;   // ← reset flag biar progres bisa ke "Simpan Perubahan"
  card.classList.add('is-editing');
  card.classList.remove('is-filled');
  card.dataset.filled = '0';

  var prevUkuran = card.dataset.ukuran;
  var prevJenis  = card.dataset.jenis;
  var prevLengan = card.dataset.lengan || null;

  selectedSize[id]   = prevUkuran || null;
  selectedJenis[id]  = prevJenis  || null;
  selectedLengan[id] = prevLengan;

  var jenisSec  = card.querySelector('.jenis-section');
  var lenganSec = card.querySelector('.lengan-section');
  var autoInfo  = card.querySelector('.auto-info');
  var autoText  = card.querySelector('.auto-info-text');

  if (!auto.manual) {
    if (jenisSec)  jenisSec.style.display  = 'none';
    if (lenganSec) lenganSec.style.display = 'none';
    autoInfo.style.display = 'block';
    autoText.textContent   = 'Jenis & lengan otomatis: ' + auto.jenis + (auto.lengan ? ' · ' + auto.lengan : '');
    selectedJenis[id]      = auto.jenis;
    selectedLengan[id]     = auto.lengan;
  } else {
    autoInfo.style.display = 'none';
    if (jenisSec) jenisSec.style.display = 'block';

    if (prevJenis) {
      card.querySelectorAll('.btn-jenis').forEach(function(b) {
        b.classList.toggle('active', b.dataset.jenis === prevJenis);
      });
      if (prevJenis === 'Dewasa' && lenganSec) {
        lenganSec.style.display = 'block';
        if (prevLengan) {
          card.querySelectorAll('.btn-lengan').forEach(function(b) {
            b.classList.toggle('active', b.dataset.lengan === prevLengan);
          });
        }
      } else if (lenganSec) {
        lenganSec.style.display = 'none';
      }
    }
  }

  // filter ukuran berdasar hubungan card & jenis kaos yang aktif
  applySizeFilter(id, selectedJenis[id]);

  if (prevUkuran) {
    var jenis = selectedJenis[id] || 'Dewasa';
    card.querySelectorAll('.btn-sz').forEach(function(b) {
      b.classList.remove('active', 'active-anak');
      if (b.dataset.sz === prevUkuran) b.classList.add(jenis === 'Anak' ? 'active-anak' : 'active');
    });
  }

  updateProgress();
  card.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// ── Batal Edit card ───────────────────────────────────────────────────────────
function batalEdit(id) {
  var card      = getCard(id);
  var editMode  = card.querySelector('.edit-mode');
  var summary   = card.querySelector('.saved-summary');
  var batalWrap = card.querySelector('.btn-batal-ubah-wrap');
  var btnUbah   = card.querySelector('.btn-ubah');

  editingCards[id] = false;
  card.classList.remove('is-editing');
  card.classList.add('is-filled');
  card.dataset.filled = '1';

  selectedSize[id]   = card.dataset.ukuran;
  selectedJenis[id]  = card.dataset.jenis;
  selectedLengan[id] = card.dataset.lengan || null;

  if (summary)   summary.style.display   = '';
  if (btnUbah)   btnUbah.style.display   = '';
  if (batalWrap) batalWrap.style.display = 'none';
  editMode.style.display = 'none';

  // Kalau tidak ada card lain yang masih editing, kembalikan allSaved = true
  var anyStillEditing = Object.values(editingCards).some(Boolean);
  if (!anyStillEditing) allSaved = true;

  updateProgress();
}

// ── Pilih jenis ───────────────────────────────────────────────────────────────
function selectJenis(id, btn, jenis) {
  selectedJenis[id] = jenis;
  getCard(id).querySelectorAll('.btn-jenis').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');

  var card      = getCard(id);
  var lenganSec = card.querySelector('.lengan-section');

  if (jenis === 'Anak' || card.dataset.hubungan === 'Anak') {
    if (lenganSec) lenganSec.style.display = 'none';
    selectedLengan[id] = 'Lengan Pendek';
    card.querySelectorAll('.btn-lengan').forEach(function(b) { b.classList.remove('active'); });
  } else {
    if (lenganSec) lenganSec.style.display = 'block';
    selectedLengan[id] = null;
    card.querySelectorAll('.btn-lengan').forEach(function(b) { b.classList.remove('active'); });
  }

  // filter ukuran max XXL kalau hubungan Anak atau jenis kaosnya Anak
  applySizeFilter(id, jenis);

  applyColorMode(id, jenis);
  markCard(id);
}

// ── Pilih lengan ──────────────────────────────────────────────────────────────
function selectLengan(id, btn, lengan) {
  selectedLengan[id] = lengan;
  getCard(id).querySelectorAll('.btn-lengan').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');
  markCard(id);
}

// ── Pilih ukuran ──────────────────────────────────────────────────────────────
function selectSize(id, btn, sz) {
  selectedSize[id] = sz;
  var jenis = selectedJenis[id] || 'Dewasa';
  getCard(id).querySelectorAll('.btn-sz').forEach(function(b) {
    b.classList.remove('active', 'active-anak');
  });
  btn.classList.add(jenis === 'Anak' ? 'active-anak' : 'active');
  markCard(id);
}

// ── Save All / Save Perubahan ─────────────────────────────────────────────────
async function saveAll() {
  if (IS_EXPIRED) { showToast('Pendaftaran sudah ditutup', '#e53935'); return; }

  var btn        = document.getElementById('btnSaveAll');
  var label      = document.getElementById('btnSaveLabel');
  var anyEditing = Object.values(editingCards).some(Boolean);
  var cardsToSave = [];

  document.querySelectorAll('.member-card').forEach(function(card) {
    var id = card.dataset.id;
    if (anyEditing ? editingCards[id] : true) cardsToSave.push(card);
  });

  btn.disabled         = true;
  btn.style.cursor     = 'not-allowed';
  btn.style.background = '#a5d6a7';
  btn.style.border     = 'none';
  label.innerHTML      = '<i class="fa-solid fa-spinner fa-spin" style="margin-right:6px;"></i>Menyimpan...';

  try {
    for (var i = 0; i < cardsToSave.length; i++) {
      var card   = cardsToSave[i];
      var id     = card.dataset.id;

      var res = await fetch(BAJU_UPDATE_URL, {
        method : 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
        body: JSON.stringify({
          detail_id   : id,
          ukuran_kaos : selectedSize[id],
          jenis_kaos  : selectedJenis[id],
          lengan_kaos : selectedLengan[id],
        }),
      });

      var data = await res.json();

      if (!res.ok) {
        var nama = card.querySelector('div > div > div')?.textContent?.trim() || 'Anggota';
        showToast((data.message || 'Gagal menyimpan') + ' (' + nama + ')', '#e53935');
        btn.disabled         = false;
        btn.style.background = anyEditing ? 'linear-gradient(135deg,#fb8c00,#e65100)' : 'linear-gradient(135deg,#43a047,#2e7d32)';
        btn.style.color      = '#fff';
        btn.style.cursor     = 'pointer';
        label.textContent    = anyEditing ? 'Simpan Perubahan' : 'Simpan Semua Ukuran';
        return;
      }
    }

    // ── Berhasil semua — update UI ────────────────────────────────────────────
    cardsToSave.forEach(function(card) {
      var id     = card.dataset.id;
      var sz     = selectedSize[id];
      var jenis  = selectedJenis[id];
      var lengan = selectedLengan[id];

      card.dataset.ukuran    = sz;
      card.dataset.jenis     = jenis;
      card.dataset.lengan    = lengan || '';
      card.dataset.confirmed = '1';
      card.dataset.filled    = '1';

      editingCards[id] = false;
      card.classList.remove('is-editing');
      card.classList.add('is-filled');

      card.querySelector('.edit-mode').style.display = 'none';

      var oldSummary = card.querySelector('.saved-summary');
      if (oldSummary) oldSummary.remove();

      var refBadge = card.querySelector('.ref-tahun-lalu');
      if (refBadge) refBadge.style.display = 'none';

      var infoEl = document.createElement('div');
      infoEl.className     = 'saved-summary';
      infoEl.style.cssText = 'margin-top:4px;';
      infoEl.innerHTML =
        '<span style="background:#e8f5e9;color:#2e7d32;border-radius:6px;padding:3px 10px;' +
        'font-size:12px;font-weight:800;display:inline-block;margin-top:4px;">' + sz + '</span>' +
        '<span style="font-size:11px;color:#999;margin-left:6px;">' +
        jenis + (lengan ? ' · ' + lengan : '') + '</span>';
      card.querySelector('.edit-mode').before(infoEl);

      var batalWrap = card.querySelector('.btn-batal-ubah-wrap');
      if (batalWrap) batalWrap.style.display = 'none';

      var check     = card.querySelector('.status-check');
      var checkIcon = check.querySelector('i');
      check.style.background = '#2e7d32';
      checkIcon.style.color  = '#fff';

      var btnUbah = card.querySelector('.btn-ubah');
      if (!btnUbah) {
        var newBtn = document.createElement('button');
        newBtn.type          = 'button';
        newBtn.className     = 'btn-ubah';
        newBtn.style.cssText = 'padding:5px 10px;border-radius:20px;border:1.5px solid #e0e0e0;' +
          'background:#fff;font-size:11px;font-weight:700;color:#777;cursor:pointer;transition:all .15s;white-space:nowrap;';
        newBtn.innerHTML     = '<i class="fa-solid fa-pen" style="font-size:10px;margin-right:3px;"></i>Ubah';
        newBtn.setAttribute('onclick', 'editCard(' + id + ', this)');
        check.parentNode.insertBefore(newBtn, check);
      } else {
        btnUbah.style.display = '';
      }
    });

    // ── Set allSaved = true SEBELUM updateProgress ────────────────────────────
    allSaved = true;

    showToast(anyEditing ? 'Perubahan berhasil disimpan!' : 'Semua ukuran berhasil disimpan!');
    updateProgress(); // akan render "Data telah tersimpan"

  } catch (e) {
    showToast('Gagal terhubung ke server.', '#e53935');
    btn.disabled         = false;
    btn.style.background = anyEditing ? 'linear-gradient(135deg,#fb8c00,#e65100)' : 'linear-gradient(135deg,#43a047,#2e7d32)';
    btn.style.color      = '#fff';
    btn.style.cursor     = 'pointer';
    label.textContent    = anyEditing ? 'Simpan Perubahan' : 'Simpan Semua Ukuran';
  }
}

// ── Countdown (hero) ──────────────────────────────────────────────────────────
(function() {
  if (!DEADLINE || IS_EXPIRED) return;
  var heroEl   = document.getElementById('heroCountdown');
  var heroIcon = document.getElementById('heroIcon');
  if (!heroEl) return;

  var deadlineMs = new Date(DEADLINE).getTime();

  function tick() {
    var diff = deadlineMs - Date.now();
    if (diff <= 0) { heroEl.textContent = '00:00:00'; setTimeout(function() { location.reload(); }, 1000); return; }

    var d = Math.floor(diff / 86400000);
    var h = Math.floor((diff % 86400000) / 3600000);
    var m = Math.floor((diff % 3600000)  / 60000);
    var s = Math.floor((diff % 60000)    / 1000);

    heroEl.textContent = d > 0
      ? d + 'h ' + String(h).padStart(2,'0') + 'j ' + String(m).padStart(2,'0') + 'm'
      : String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');

    if (diff < 3600000 && heroIcon)  { heroIcon.style.color = '#ff8a80'; heroIcon.className = 'fa-solid fa-hourglass-end'; }
    else if (diff < 86400000 && heroIcon) { heroIcon.style.color = '#ffd54f'; }
  }

  tick();
  setInterval(tick, 1000);
})();

// ── Lock kalau expired ────────────────────────────────────────────────────────
if (IS_EXPIRED) {
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-sz, .btn-jenis, .btn-lengan').forEach(function(el) {
      el.disabled = true; el.style.opacity = '0.5'; el.style.pointerEvents = 'none';
    });
    document.querySelectorAll('.btn-ubah').forEach(function(el) { el.style.display = 'none'; });
    document.getElementById('btnSaveAll').style.display = 'none';
  });
}

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.member-card').forEach(function(card) { initCard(card.dataset.id); });
  updateProgress();
});
</script>

@endsection