@extends('guest.layouts.app')
@section('title', 'Data Baju')

@section('content')

@php $adaDataLama = $members->whereNotNull('ukuran_kaos')->where('ukuran_kaos','!=','')->isNotEmpty(); @endphp

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
  <div class="section-title" style="margin-bottom:0;">UKURAN BAJU</div>
  <div style="display:flex;align-items:center;gap:8px;">
    <span style="background:#e8f5e9;color:#2e7d32;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">
      {{ $members->count() }} orang
    </span>
    @if($adaDataLama)
    <button onclick="bukaSheetTahunLalu()"
      style="width:30px;height:30px;border-radius:8px;border:1.5px solid #e0e0e0;
             background:#fff;color:#999;cursor:pointer;display:flex;align-items:center;
             justify-content:center;" title="Lihat data tahun lalu">
      <i class="fa-solid fa-rotate-left" style="font-size:12px;"></i>
    </button>
    @endif
  </div>
</div>

{{-- ── List Member ─────────────────────────────────────────────────────────── --}}
<div id="memberList">
  @foreach($members as $d)
  <div class="member-card card" data-id="{{ $d->id }}"
       data-ukuran="{{ $d->ukuran_kaos ?? '' }}"
       data-jenis="{{ $d->jenis_kaos ?? 'Dewasa' }}"
       data-lengan="{{ $d->lengan_kaos ?? '' }}"
       data-hubungan="{{ $d->hubungan }}"
       style="padding:14px;margin-bottom:10px;">
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
      </div>

      {{-- Badge ukuran (view mode) --}}
      <div class="view-mode" style="display:flex;align-items:center;gap:8px;">
        @if($d->ukuran_kaos)
          <div style="text-align:right;">
            <span class="badge-ukuran"
              style="background:#e8f5e9;color:#2e7d32;border-radius:8px;padding:5px 12px;
                    font-size:13px;font-weight:800;display:inline-block;">
              {{ $d->ukuran_kaos }}
            </span>
            <span class="badge-detail"
              style="font-size:10px;color:#999;margin-top:3px;display:block;">
              {{ $d->jenis_kaos ?? 'Dewasa' }}{{ $d->lengan_kaos ? ' · '.$d->lengan_kaos : '' }}
            </span>
          </div>
        @else
          <span class="empty-label" style="font-size:11.5px;color:#ccc;font-style:italic;">Belum diisi</span>
        @endif
        <button onclick="startEdit({{ $d->id }})"
          style="width:32px;height:32px;border-radius:8px;border:1.5px solid #e0e0e0;
                 background:#fff;color:#999;cursor:pointer;display:flex;align-items:center;
                 justify-content:center;flex-shrink:0;">
          <i class="fa-solid fa-pen" style="font-size:11px;"></i>
        </button>
      </div>

      {{-- Saving spinner --}}
      <div class="saving-mode" style="display:none;align-items:center;gap:6px;">
        <div style="width:16px;height:16px;border:2px solid #e0e0e0;border-top-color:#2e7d32;
                    border-radius:50%;animation:spin .6s linear infinite;"></div>
        <span style="font-size:11.5px;color:#999;">Menyimpan...</span>
      </div>

    </div>

    {{-- Edit mode --}}
    <div class="edit-mode" style="display:none;margin-top:12px;border-top:1px solid #f0f4f0;padding-top:12px;">

      {{-- ── Info otomatis (non-Saudara) ── --}}
      <div class="auto-info" style="display:none;margin-bottom:14px;">
        <div style="background:#f0f4f0;border-radius:8px;padding:8px 12px;
                    font-size:11.5px;color:#555;display:flex;align-items:center;gap:6px;">
          <i class="fa-solid fa-circle-info" style="color:#2e7d32;font-size:12px;"></i>
          <span class="auto-info-text"></span>
        </div>
      </div>

      {{-- ── Jenis Kaos (hanya Saudara) ── --}}
      <div class="jenis-section" style="display:none;">
        <div style="font-size:11px;font-weight:700;color:#999;margin-bottom:8px;letter-spacing:.3px;">
          JENIS KAOS
        </div>
        <div style="display:flex;gap:8px;margin-bottom:14px;">
          @foreach(['Dewasa','Anak'] as $jenis)
          <button type="button"
            onclick="selectJenis({{ $d->id }}, this, '{{ $jenis }}')"
            class="btn-jenis"
            data-jenis="{{ $jenis }}"
            style="flex:1;padding:8px;border-radius:8px;border:1.5px solid #e0e0e0;
                   background:#fff;font-size:12px;font-weight:700;color:#777;cursor:pointer;
                   transition:all .15s;">
            @if($jenis === 'Dewasa') 🧑 @else 👶 @endif {{ $jenis }}
          </button>
          @endforeach
        </div>
      </div>

      {{-- ── Lengan (hanya Saudara + Dewasa) ── --}}
      <div class="lengan-section" style="display:none;">
        <div style="font-size:11px;font-weight:700;color:#999;margin-bottom:8px;letter-spacing:.3px;">
          TIPE LENGAN
        </div>
        <div style="display:flex;gap:8px;margin-bottom:14px;">
          @foreach(['Lengan Pendek','Lengan Panjang'] as $lengan)
          <button type="button"
            onclick="selectLengan({{ $d->id }}, this, '{{ $lengan }}')"
            class="btn-lengan"
            data-lengan="{{ $lengan }}"
            style="flex:1;padding:8px;border-radius:8px;border:1.5px solid #e0e0e0;
                   background:#fff;font-size:12px;font-weight:700;color:#777;cursor:pointer;
                   transition:all .15s;">
            @if($lengan === 'Lengan Pendek') 👕 @else 🧥 @endif {{ $lengan }}
          </button>
          @endforeach
        </div>
      </div>

      {{-- ── Ukuran ── --}}
      <div style="font-size:11px;font-weight:700;color:#999;margin-bottom:8px;letter-spacing:.3px;">
        PILIH UKURAN
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
        @foreach(['XS','S','M','L','XL','XXL','XXXL'] as $sz)
        <button type="button"
          onclick="selectSize({{ $d->id }}, this, '{{ $sz }}')"
          class="btn-sz"
          data-sz="{{ $sz }}"
          style="padding:7px 14px;border-radius:8px;border:1.5px solid #e0e0e0;
                 background:#fff;font-size:12px;font-weight:700;color:#777;cursor:pointer;
                 transition:all .15s;">
          {{ $sz }}
        </button>
        @endforeach
      </div>

      {{-- ── Actions ── --}}
      <div style="display:flex;gap:8px;">
        <button onclick="saveSize({{ $d->id }})"
          style="flex:1;padding:10px;border-radius:10px;border:none;
                 background:linear-gradient(135deg,#43a047,#2e7d32);
                 color:#fff;font-size:13px;font-weight:700;cursor:pointer;">
          Simpan
        </button>
        <button onclick="cancelEdit({{ $d->id }})"
          style="padding:10px 16px;border-radius:10px;border:1.5px solid #e0e0e0;
                 background:#fff;color:#999;font-size:13px;font-weight:600;cursor:pointer;">
          Batal
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


{{-- ═══════════════════════════════════════════════════════════════════════════
     BOTTOM SHEET — Gunakan data tahun lalu?
     ═══════════════════════════════════════════════════════════════════════════ --}}
@if($adaDataLama)
<div id="sheetTahunLalu"
  style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);
         align-items:flex-end;justify-content:center;">
  <div style="background:#fff;border-radius:24px 24px 0 0;width:100%;max-width:480px;
              padding:24px 20px 36px;animation:slideUp .3s ease;">

    <div style="width:40px;height:4px;background:#e0e0e0;border-radius:2px;margin:0 auto 20px;"></div>

    <div style="text-align:center;margin-bottom:6px;">
      <div style="width:52px;height:52px;border-radius:16px;background:#e8f5e9;
                  display:flex;align-items:center;justify-content:center;
                  margin:0 auto 12px;font-size:24px;">
        👕
      </div>
      <div style="font-size:16px;font-weight:800;color:#111;margin-bottom:6px;">
        Gunakan ukuran tahun lalu?
      </div>
      <div style="font-size:12px;color:#999;line-height:1.6;">
        Kami menemukan data ukuran sebelumnya.<br>
        Gunakan sebagai acuan atau sesuaikan langsung.
      </div>
    </div>

    {{-- Preview --}}
    <div style="background:#f9fef9;border-radius:14px;padding:12px 14px;margin:16px 0;
                border:1.5px solid #e8f5e9;">
      @foreach($members as $d)
      <div style="display:flex;align-items:center;justify-content:space-between;
                  padding:6px 0;border-bottom:1px solid #f0f0f0;">
        <div style="font-size:13px;color:#333;font-weight:600;">{{ $d->nama_keluarga }}</div>
        <div style="display:flex;align-items:center;gap:6px;">
          <span style="font-size:11px;color:#999;">{{ $d->hubungan }}</span>
          @if($d->ukuran_kaos)
            <span style="background:#e8f5e9;color:#2e7d32;border-radius:6px;padding:3px 10px;
                         font-size:12px;font-weight:800;">{{ $d->ukuran_kaos }}</span>
            <span style="font-size:10px;color:#999;">
              {{ $d->jenis_kaos ?? 'Dewasa' }}{{ $d->lengan_kaos ? ' · '.$d->lengan_kaos : '' }}
            </span>
          @else
            <span style="font-size:11.5px;color:#ccc;font-style:italic;">Belum diisi</span>
          @endif
        </div>
      </div>
      @endforeach
    </div>

    <div style="display:flex;flex-direction:column;gap:10px;">
      <button onclick="pakaiTahunLalu()"
        style="width:100%;padding:14px;border-radius:12px;border:none;
               background:linear-gradient(135deg,#43a047,#2e7d32);
               color:#fff;font-size:14px;font-weight:800;cursor:pointer;">
        <i class="fa-solid fa-check" style="margin-right:6px;"></i>
        Oke, Pakai Data Ini
      </button>
      <button onclick="sesuaikan()"
        style="width:100%;padding:13px;border-radius:12px;
               border:1.5px solid #3d7a47;background:#fff;
               color:#2e7d32;font-size:13px;font-weight:700;cursor:pointer;">
        <i class="fa-solid fa-pen" style="margin-right:6px;"></i>
        Sesuaikan Ulang
      </button>
    </div>
  </div>
</div>
@endif


{{-- ── CSS ─────────────────────────────────────────────────────────────────── --}}
<style>
@keyframes slideUp {
  from { transform:translateY(100%); opacity:0; }
  to   { transform:translateY(0);    opacity:1; }
}
@keyframes spin {
  to { transform:rotate(360deg); }
}
@keyframes fadeInUp {
  from { transform:translateX(-50%) translateY(10px); opacity:0; }
  to   { transform:translateX(-50%) translateY(0);    opacity:1; }
}
.btn-sz.active,
.btn-jenis.active,
.btn-lengan.active {
  background:#e8f5e9 !important;
  border-color:#3d7a47 !important;
  color:#2e7d32 !important;
}
</style>


{{-- ── JS ─────────────────────────────────────────────────────────────────── --}}
<script>
const BAJU_UPDATE_URL = "{{ route('guest.baju.update') }}";
const CSRF            = "{{ csrf_token() }}";
const STORAGE_KEY     = 'baju_sheet_seen_{{ $karyawan->nik }}';

const selectedSize   = {};
const selectedJenis  = {};
const selectedLengan = {};

// ── Resolve jenis & lengan otomatis dari hubungan ─────────────────────────────
// Return: { jenis, lengan, manual }
// manual = true  → Saudara, user pilih sendiri
// manual = false → sudah fix, tidak perlu pilih
function resolveAuto(hubungan) {
  switch (hubungan) {
    case 'Karyawan': return { jenis: 'Dewasa', lengan: 'Lengan Pendek', manual: false };
    case 'Karyawati': return { jenis: 'Dewasa', lengan: 'Lengan Panjang', manual: false };
    case 'Suami':    return { jenis: 'Dewasa', lengan: 'Lengan Pendek', manual: false };
    case 'Istri':    return { jenis: 'Dewasa', lengan: 'Lengan Panjang', manual: false };
    case 'Anak':     return { jenis: 'Anak',   lengan: null,             manual: false };
    default:         return { jenis: null,     lengan: null,             manual: true  }; // Saudara
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function getCard(id)   { return document.querySelector('.member-card[data-id="' + id + '"]'); }
function getView(id)   { return getCard(id).querySelector('.view-mode'); }
function getEdit(id)   { return getCard(id).querySelector('.edit-mode'); }
function getSaving(id) { return getCard(id).querySelector('.saving-mode'); }

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg, bg) {
  bg = bg || '#2e7d32';
  var t = document.createElement('div');
  t.textContent   = msg;
  t.style.cssText = [
    'position:fixed','bottom:30px','left:50%',
    'transform:translateX(-50%)',
    'background:' + bg,'color:#fff',
    'padding:12px 24px','border-radius:100px',
    'font-size:13px','font-weight:600',
    'z-index:9999','box-shadow:0 4px 20px rgba(0,0,0,.2)',
    'white-space:nowrap','animation:fadeInUp .3s ease',
  ].join(';');
  document.body.appendChild(t);
  setTimeout(function() { t.remove(); }, 1800);
}

// ── Start edit ────────────────────────────────────────────────────────────────
function startEdit(id) {
  var card     = getCard(id);
  var hubungan = card.dataset.hubungan || '';
  var auto     = resolveAuto(hubungan);

  getView(id).style.display   = 'none';
  getEdit(id).style.display   = 'block';
  getSaving(id).style.display = 'none';

  var existingUkuran = card.dataset.ukuran || '';
  selectedSize[id]   = existingUkuran || null;

  var jenisSec  = card.querySelector('.jenis-section');
  var lenganSec = card.querySelector('.lengan-section');
  var autoInfo  = card.querySelector('.auto-info');
  var autoText  = card.querySelector('.auto-info-text');

  if (!auto.manual) {
    // ── Non-Saudara: semua fixed, tampil info saja ──
    jenisSec.style.display  = 'none';
    lenganSec.style.display = 'none';
    autoInfo.style.display  = 'block';

    selectedJenis[id]  = auto.jenis;
    selectedLengan[id] = auto.lengan;

    // Susun teks info
    var infoText = auto.jenis;
    if (auto.lengan) infoText += ' · ' + auto.lengan;
    autoText.textContent = 'Jenis & lengan otomatis: ' + infoText;

  } else {
    // ── Saudara: tampil pilihan jenis & lengan ──
    autoInfo.style.display = 'none';
    jenisSec.style.display = 'block';

    // Prefill jenis dari data lama (default Dewasa)
    var existingJenis  = card.dataset.jenis  || 'Dewasa';
    var existingLengan = card.dataset.lengan || '';
    selectedJenis[id]  = existingJenis;
    selectedLengan[id] = existingLengan || null;

    card.querySelectorAll('.btn-jenis').forEach(function(btn) {
      btn.classList.toggle('active', btn.dataset.jenis === existingJenis);
    });

    // Tampilkan lengan kalau Dewasa
    toggleLenganSaudara(id, existingJenis, existingLengan);
  }

  // Highlight ukuran
  card.querySelectorAll('.btn-sz').forEach(function(btn) {
    btn.classList.toggle('active', btn.dataset.sz === existingUkuran);
  });
}

// ── Toggle lengan section (khusus Saudara) ────────────────────────────────────
function toggleLenganSaudara(id, jenis, prefillLengan) {
  var card      = getCard(id);
  var lenganSec = card.querySelector('.lengan-section');

  if (jenis === 'Anak') {
    lenganSec.style.display = 'none';
    selectedLengan[id] = null;
    card.querySelectorAll('.btn-lengan').forEach(function(b) { b.classList.remove('active'); });
  } else {
    lenganSec.style.display = 'block';
    var fill = prefillLengan || selectedLengan[id] || '';
    card.querySelectorAll('.btn-lengan').forEach(function(b) {
      b.classList.toggle('active', b.dataset.lengan === fill);
    });
  }
}

// ── Pilih jenis (hanya Saudara) ───────────────────────────────────────────────
function selectJenis(id, btn, jenis) {
  selectedJenis[id] = jenis;
  getCard(id).querySelectorAll('.btn-jenis').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');
  toggleLenganSaudara(id, jenis, '');
}

// ── Pilih lengan (hanya Saudara Dewasa) ───────────────────────────────────────
function selectLengan(id, btn, lengan) {
  selectedLengan[id] = lengan;
  getCard(id).querySelectorAll('.btn-lengan').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');
}

// ── Pilih ukuran ──────────────────────────────────────────────────────────────
function selectSize(id, btn, sz) {
  selectedSize[id] = sz;
  getCard(id).querySelectorAll('.btn-sz').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');
}

// ── Cancel edit ───────────────────────────────────────────────────────────────
function cancelEdit(id) {
  var card = getCard(id);
  card.querySelectorAll('.btn-sz,.btn-jenis,.btn-lengan').forEach(function(b) { b.classList.remove('active'); });
  delete selectedSize[id];
  delete selectedJenis[id];
  delete selectedLengan[id];

  getView(id).style.display   = 'flex';
  getEdit(id).style.display   = 'none';
  getSaving(id).style.display = 'none';
}

// ── Save ──────────────────────────────────────────────────────────────────────
async function saveSize(id) {
  var sz       = selectedSize[id];
  var jenis    = selectedJenis[id];
  var lengan   = selectedLengan[id];
  var card     = getCard(id);
  var hubungan = card.dataset.hubungan || '';
  var auto     = resolveAuto(hubungan);

  if (!sz) {
    showToast('Pilih ukuran dulu ya.', '#e53935');
    return;
  }

  // Validasi manual hanya untuk Saudara Dewasa
  if (auto.manual && jenis === 'Dewasa' && !lengan) {
    showToast('Pilih tipe lengan dulu ya.', '#e53935');
    return;
  }

  getEdit(id).style.display   = 'none';
  getView(id).style.display   = 'none';
  getSaving(id).style.display = 'flex';

  try {
    var res = await fetch(BAJU_UPDATE_URL, {
      method : 'POST',
      headers: {
        'Content-Type' : 'application/json',
        'X-CSRF-TOKEN' : CSRF,
        'Accept'       : 'application/json',
      },
      body: JSON.stringify({
        detail_id   : id,
        ukuran_kaos : sz,
        jenis_kaos  : jenis,
        lengan_kaos : lengan,
      }),
    });

    var data = await res.json();

    if (!res.ok) {
      showToast(data.message || 'Gagal menyimpan.', '#e53935');
      cancelEdit(id);
      return;
    }

    // Update data-attribute
    card.dataset.ukuran = sz;
    card.dataset.jenis  = jenis;
    card.dataset.lengan = lengan || '';

    // Update tampilan view-mode
    var viewEl      = getView(id);
    var badge       = viewEl.querySelector('.badge-ukuran');
    var badgeDetail = viewEl.querySelector('.badge-detail');
    var editBtn     = viewEl.querySelector('button');

    if (!badge) {
      var emptyLabel = viewEl.querySelector('.empty-label');
      var wrapper    = document.createElement('div');
      wrapper.style.textAlign = 'right';

      badge = document.createElement('span');
      badge.className  = 'badge-ukuran';
      badge.style.cssText = 'background:#e8f5e9;color:#2e7d32;border-radius:8px;padding:5px 12px;font-size:13px;font-weight:800;display:inline-block;';

      badgeDetail = document.createElement('span');
      badgeDetail.className   = 'badge-detail';
      badgeDetail.style.cssText = 'font-size:10px;color:#999;margin-top:3px;display:block;';

      wrapper.appendChild(badge);
      wrapper.appendChild(badgeDetail);

      if (emptyLabel) {
        emptyLabel.replaceWith(wrapper);
      } else {
        editBtn.before(wrapper);
      }
    }

    badge.textContent       = sz;
    badgeDetail.textContent = jenis + (lengan ? ' · ' + lengan : '');

    getSaving(id).style.display = 'none';
    getView(id).style.display   = 'flex';
    delete selectedSize[id];
    delete selectedJenis[id];
    delete selectedLengan[id];
    showToast('Ukuran disimpan!');

  } catch (e) {
    showToast('Gagal terhubung ke server.', '#e53935');
    cancelEdit(id);
  }
}

// ── Sheet: buka manual ────────────────────────────────────────────────────────
function bukaSheetTahunLalu() {
  var sheet = document.getElementById('sheetTahunLalu');
  if (!sheet) return;
  sheet.style.display          = 'flex';
  document.body.style.overflow = 'hidden';
}

// ── Sheet: Oke pakai data lama ────────────────────────────────────────────────
function pakaiTahunLalu() {
  localStorage.setItem(STORAGE_KEY, '1');
  document.getElementById('sheetTahunLalu').style.display = 'none';
  document.body.style.overflow = '';
}

// ── Sheet: Sesuaikan ──────────────────────────────────────────────────────────
function sesuaikan() {
  localStorage.setItem(STORAGE_KEY, '1');
  document.getElementById('sheetTahunLalu').style.display = 'none';
  document.body.style.overflow = '';

  document.querySelectorAll('.member-card').forEach(function(card) {
    var id = card.dataset.id;

    card.dataset.ukuran = '';
    card.dataset.jenis  = 'Dewasa';
    card.dataset.lengan = '';

    var viewEl  = getView(id);
    var wrapper = viewEl.querySelector('div');
    var badge   = viewEl.querySelector('.badge-ukuran');

    if (badge || wrapper) {
      var emptyLabel = document.createElement('span');
      emptyLabel.className     = 'empty-label';
      emptyLabel.style.cssText = 'font-size:11.5px;color:#ccc;font-style:italic;';
      emptyLabel.textContent   = 'Belum diisi';
      (wrapper || badge).replaceWith(emptyLabel);
    }

    startEdit(id);
  });
}

// ── Auto buka sheet hanya kalau belum pernah ──────────────────────────────────
@if($adaDataLama)
if (!localStorage.getItem(STORAGE_KEY)) {
  document.body.style.overflow = 'hidden';
  document.getElementById('sheetTahunLalu').style.display = 'flex';
}
@endif
</script>

@endsection