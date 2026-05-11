@extends('guest.layouts.app')
@section('title', 'Keluarga')

@section('content')

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
  <div class="section-title" style="margin-bottom:0;">ANGGOTA KELUARGA</div>
  <span style="background:#e8f5e9;color:#2e7d32;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">
    {{ $karyawan->details->count() }} orang
  </span>
</div>

{{-- ── Banner: sedang ada pengajuan pending ───────────────────────────────── --}}
@if($pengajuanPending)
<div style="background:#fff8e1;border:1.5px solid #ffe082;border-radius:14px;padding:14px 16px;margin-bottom:14px;display:flex;align-items:flex-start;gap:12px;">
  <div style="font-size:20px;margin-top:1px;">⏳</div>
  <div style="flex:1;">
    <div style="font-size:13px;font-weight:700;color:#e65100;margin-bottom:3px;">Pengajuan Sedang Ditinjau</div>
    <div style="font-size:12px;color:#795548;line-height:1.5;">
      Pengajuan untuk <strong>{{ $pengajuanPending->nama_keluarga }}</strong>
      ({{ $pengajuanPending->hubungan }}) sedang ditinjau oleh panitia.
      Tombol tambah anggota akan tersedia setelah pengajuan ini selesai diproses.
    </div>
    <div style="font-size:10.5px;color:#bbb;margin-top:5px;">
      Diajukan {{ $pengajuanPending->created_at->diffForHumans() }}
    </div>
  </div>
</div>
@endif

{{-- ── Riwayat pengajuan (approved/rejected) ──────────────────────────────── --}}
@if($riwayatPengajuan->isNotEmpty())
<div style="margin-bottom:16px;">
  <div style="font-size:11px;font-weight:700;color:#999;letter-spacing:.5px;text-transform:uppercase;margin-bottom:8px;">
    Riwayat Pengajuan
  </div>
  @foreach($riwayatPengajuan as $rw)
  <div style="background:#fff;border-radius:12px;padding:12px 14px;margin-bottom:8px;border:1px solid #f0f0f0;display:flex;align-items:center;gap:12px;">
    <div style="width:36px;height:36px;border-radius:10px;
      background:{{ $rw->isApproved() ? '#e8f5e9' : '#fce4ec' }};
      color:{{ $rw->isApproved() ? '#2e7d32' : '#c62828' }};
      display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">
      {{ $rw->isApproved() ? '✓' : '✕' }}
    </div>
    <div style="flex:1;min-width:0;">
      <div style="font-size:13px;font-weight:700;color:#111;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
        {{ $rw->nama_keluarga }}
      </div>
      <div style="font-size:11px;color:#999;">
        {{ $rw->hubungan }}
        &middot;
        <span style="color:{{ $rw->isApproved() ? '#2e7d32' : '#c62828' }};font-weight:600;">
          {{ $rw->labelStatus() }}
        </span>
      </div>
      @if($rw->isRejected() && $rw->alasan_tolak)
      <div style="font-size:11px;color:#e53935;margin-top:3px;font-style:italic;">
        "{{ $rw->alasan_tolak }}"
      </div>
      @endif
    </div>
    <div style="font-size:10.5px;color:#ccc;flex-shrink:0;text-align:right;">
      {{ $rw->reviewed_at?->format('d M') ?? '-' }}
    </div>
  </div>
  @endforeach
</div>
@endif

{{-- ── Tombol Tambah Anggota ───────────────────────────────────────────────── --}}
@if(!$pengajuanPending)
<button onclick="openModalPengajuan()"
  style="width:100%;padding:13px;border-radius:14px;border:2px dashed #81c784;background:#f9fef9;
         color:#2e7d32;font-size:13px;font-weight:700;cursor:pointer;margin-bottom:16px;
         display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;"
  onmouseover="this.style.background='#e8f5e9'" onmouseout="this.style.background='#f9fef9'">
  <i class="fa-solid fa-plus" style="font-size:12px;"></i>
  Tambah Anggota Keluarga
</button>
@else
<button disabled
  style="width:100%;padding:13px;border-radius:14px;border:2px dashed #e0e0e0;background:#fafafa;
         color:#bdbdbd;font-size:13px;font-weight:700;cursor:not-allowed;margin-bottom:16px;
         display:flex;align-items:center;justify-content:center;gap:8px;">
  <i class="fa-solid fa-clock" style="font-size:12px;"></i>
  Pengajuan Sedang Diproses
</button>
@endif

{{-- ── List Anggota Keluarga ───────────────────────────────────────────────── --}}
@forelse($karyawan->details as $d)
<div class="card" style="padding:14px;margin-bottom:10px;">
  <div style="display:flex;align-items:center;gap:12px;">
    <div style="width:44px;height:44px;border-radius:12px;background:#e8f5e9;color:#2e7d32;
                display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:800;flex-shrink:0;">
      {{ strtoupper(substr($d->nama_keluarga, 0, 1)) }}
    </div>
    <div style="flex:1;">
      <div style="font-size:14px;font-weight:700;color:#111;">{{ $d->nama_keluarga }}</div>
      <div style="font-size:11.5px;color:#999;margin-top:2px;">
        {{ $d->hubungan }} &middot; {{ $d->jenis_kelamin }}
      </div>
    </div>
    @if($d->umur)
    <div style="font-size:13px;font-weight:700;color:#555;flex-shrink:0;">
      {{ $d->umur }} th
    </div>
    @endif
  </div>

  @if($d->tanggal_lahir || $d->ukuran_kaos)
  <div style="border-top:1px solid #f0f4f0;margin-top:12px;padding-top:10px;display:flex;gap:16px;">
    @if($d->tanggal_lahir)
    <div style="font-size:11.5px;color:#999;">
      <i class="fa-solid fa-cake-candles" style="color:#3d7a47;margin-right:5px;"></i>
      {{ \Carbon\Carbon::parse($d->tanggal_lahir)->translatedFormat('d F Y') }}
    </div>
    @endif
    @if($d->ukuran_kaos)
    <div style="font-size:11.5px;color:#999;">
      <i class="fa-solid fa-shirt" style="color:#3d7a47;margin-right:5px;"></i>
      Kaos {{ $d->ukuran_kaos }}
    </div>
    @endif
  </div>
  @endif
</div>
@empty
<div style="text-align:center;padding:40px 20px;color:#ccc;">
  <i class="fa-solid fa-user-slash" style="font-size:36px;display:block;margin-bottom:12px;"></i>
  <div style="font-size:13px;">Belum ada data anggota keluarga</div>
</div>
@endforelse


{{-- ═══════════════════════════════════════════════════════════════════════════
     MODAL — Form Pengajuan Anggota Keluarga
     ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modalPengajuan"
  style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);
         align-items:flex-end;justify-content:center;">
  <div style="background:#fff;border-radius:24px 24px 0 0;width:100%;max-width:480px;
              padding:24px 20px 36px;animation:slideUp .3s ease;">

    {{-- Handle --}}
    <div style="width:40px;height:4px;background:#e0e0e0;border-radius:2px;margin:0 auto 20px;"></div>

    {{-- Title --}}
    <div style="font-size:16px;font-weight:800;color:#111;margin-bottom:4px;">Tambah Anggota Keluarga</div>
    <div style="font-size:12px;color:#999;margin-bottom:20px;">Pengajuan akan ditinjau oleh panitia terlebih dahulu</div>

    {{-- Form --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

      {{-- Nama --}}
      <div>
        <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">
          Nama Lengkap <span style="color:#e53935;">*</span>
        </label>
        <input type="text" id="pNama" placeholder="Masukkan nama lengkap"
          style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid #e0e0e0;
                 font-size:13px;outline:none;box-sizing:border-box;transition:border-color .2s;"
          onfocus="this.style.borderColor='#3d7a47'" onblur="this.style.borderColor='#e0e0e0'">
      </div>

      {{-- Hubungan + Jenis Kelamin --}}
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
          <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">
            Hubungan <span style="color:#e53935;">*</span>
          </label>
          <select id="pHubungan"
            onchange="autoJenisKelamin(this.value)"
            style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid #e0e0e0;
                   font-size:13px;outline:none;box-sizing:border-box;background:#fff;appearance:none;
                   background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%23999' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E\");
                   background-repeat:no-repeat;background-position:right 12px center;">
            <option value="">Pilih</option>
            <option value="Suami">Suami</option>
            <option value="Istri">Istri</option>
            <option value="Anak">Anak</option>
          </select>
        </div>
        <div>
          <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">
            Jenis Kelamin <span style="color:#e53935;">*</span>
          </label>
          <select id="pJenisKelamin"
            style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid #e0e0e0;
                   font-size:13px;outline:none;box-sizing:border-box;background:#fff;appearance:none;
                   background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%23999' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E\");
                   background-repeat:no-repeat;background-position:right 12px center;">
            <option value="">Pilih</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
        </div>
      </div>

      {{-- Tanggal Lahir + Umur --}}
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
          <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">Tanggal Lahir</label>
          <input type="date" id="pTanggalLahir"
            onchange="hitungUmur()"
            style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid #e0e0e0;
                   font-size:13px;outline:none;box-sizing:border-box;"
            onfocus="this.style.borderColor='#3d7a47'" onblur="this.style.borderColor='#e0e0e0'">
        </div>
        <div>
          <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">Umur (tahun)</label>
          <input type="number" id="pUmur" placeholder="0" min="0" max="150"
            style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid #e0e0e0;
                   font-size:13px;outline:none;box-sizing:border-box;"
            onfocus="this.style.borderColor='#3d7a47'" onblur="this.style.borderColor='#e0e0e0'">
        </div>
      </div>

      {{-- Ukuran Kaos --}}
      <div>
        <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">Ukuran Kaos</label>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          @foreach(['XS','S','M','L','XL','XXL','XXXL'] as $sz)
          <button type="button" onclick="selectUkuran(this, '{{ $sz }}')"
            class="btn-ukuran"
            style="padding:7px 14px;border-radius:8px;border:1.5px solid #e0e0e0;
                   background:#fff;font-size:12px;font-weight:700;color:#777;cursor:pointer;
                   transition:all .15s;">
            {{ $sz }}
          </button>
          @endforeach
        </div>
        <input type="hidden" id="pUkuranKaos">
      </div>

      {{-- Error alert --}}
      <div id="pErrorAlert" style="display:none;background:#fce4ec;border-radius:10px;
           padding:10px 14px;font-size:12px;color:#c62828;"></div>

      {{-- Submit --}}
      <button onclick="submitPengajuan()" id="btnSubmitPengajuan"
        style="width:100%;padding:14px;border-radius:12px;border:none;
               background:linear-gradient(135deg,#43a047,#2e7d32);
               color:#fff;font-size:14px;font-weight:800;cursor:pointer;
               transition:opacity .2s;margin-top:4px;">
        Kirim Pengajuan
      </button>

      <button onclick="closeModalPengajuan()"
        style="width:100%;padding:12px;border-radius:12px;border:1.5px solid #e0e0e0;
               background:#fff;color:#999;font-size:13px;font-weight:600;cursor:pointer;">
        Batal
      </button>
    </div>
  </div>
</div>

{{-- CSS tambahan --}}
<style>
@keyframes slideUp {
  from { transform: translateY(100%); opacity: 0; }
  to   { transform: translateY(0);    opacity: 1; }
}
.btn-ukuran.active {
  background: #e8f5e9 !important;
  border-color: #3d7a47 !important;
  color: #2e7d32 !important;
}
</style>

{{-- JS --}}
<script>
const PENGAJUAN_URL = "{{ route('guest.pengajuan.store') }}";
const CSRF_TOKEN    = "{{ csrf_token() }}";

// ── Modal open/close ─────────────────────────────────────────────────────────
function openModalPengajuan() {
  resetFormPengajuan();
  const modal = document.getElementById('modalPengajuan');
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeModalPengajuan() {
  document.getElementById('modalPengajuan').style.display = 'none';
  document.body.style.overflow = '';
}

// Tutup modal kalau klik di luar
document.getElementById('modalPengajuan').addEventListener('click', function(e) {
  if (e.target === this) closeModalPengajuan();
});

// ── Auto jenis kelamin berdasarkan hubungan ──────────────────────────────────
function autoJenisKelamin(hubungan) {
  const jkEl = document.getElementById('pJenisKelamin');
  if (hubungan === 'Suami')    jkEl.value = 'Laki-laki';
  else if (hubungan === 'Istri') jkEl.value = 'Perempuan';
  else jkEl.value = '';
}

// ── Hitung umur otomatis dari tanggal lahir ──────────────────────────────────
function hitungUmur() {
  const tgl = document.getElementById('pTanggalLahir').value;
  if (!tgl) return;
  const today = new Date();
  const birth  = new Date(tgl);
  let age = today.getFullYear() - birth.getFullYear();
  const m = today.getMonth() - birth.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
  document.getElementById('pUmur').value = age >= 0 ? age : 0;
}

// ── Pilih ukuran kaos ────────────────────────────────────────────────────────
function selectUkuran(btn, sz) {
  document.querySelectorAll('.btn-ukuran').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('pUkuranKaos').value = sz;
}

// ── Reset form ───────────────────────────────────────────────────────────────
function resetFormPengajuan() {
  document.getElementById('pNama').value         = '';
  document.getElementById('pHubungan').value     = '';
  document.getElementById('pJenisKelamin').value = '';
  document.getElementById('pTanggalLahir').value = '';
  document.getElementById('pUmur').value         = '';
  document.getElementById('pUkuranKaos').value   = '';
  document.querySelectorAll('.btn-ukuran').forEach(b => b.classList.remove('active'));
  hideError();
}

// ── Error helper ─────────────────────────────────────────────────────────────
function showError(msg) {
  const el = document.getElementById('pErrorAlert');
  el.textContent = msg;
  el.style.display = 'block';
}
function hideError() {
  document.getElementById('pErrorAlert').style.display = 'none';
}

// ── Submit ───────────────────────────────────────────────────────────────────
async function submitPengajuan() {
  hideError();

  const nama       = document.getElementById('pNama').value.trim();
  const hubungan   = document.getElementById('pHubungan').value;
  const jk         = document.getElementById('pJenisKelamin').value;
  const tglLahir   = document.getElementById('pTanggalLahir').value;
  const umur       = document.getElementById('pUmur').value;
  const ukuran     = document.getElementById('pUkuranKaos').value;

  // Validasi client-side
  if (!nama)     return showError('Nama anggota keluarga wajib diisi.');
  if (!hubungan) return showError('Hubungan wajib dipilih.');
  if (!jk)       return showError('Jenis kelamin wajib dipilih.');

  const btn = document.getElementById('btnSubmitPengajuan');
  btn.disabled = true;
  btn.textContent = 'Mengirim...';

  try {
    const res = await fetch(PENGAJUAN_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        nama_keluarga : nama,
        hubungan      : hubungan,
        jenis_kelamin : jk,
        tanggal_lahir : tglLahir || null,
        umur          : umur     || null,
        ukuran_kaos   : ukuran   || null,
      }),
    });

    const data = await res.json();

    if (!res.ok) {
      // Laravel validation errors → ambil pesan pertama
      if (data.errors) {
        const firstErr = Object.values(data.errors)[0];
        return showError(Array.isArray(firstErr) ? firstErr[0] : firstErr);
      }
      return showError(data.message || 'Terjadi kesalahan. Coba lagi.');
    }

    // Sukses → reload halaman supaya banner pending muncul
    closeModalPengajuan();
    // Tampilkan toast sukses sebelum reload
    showToastSuccess(data.message || 'Pengajuan berhasil dikirim!');
    setTimeout(() => location.reload(), 1800);

  } catch (err) {
    showError('Gagal terhubung ke server. Periksa koneksi internet.');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Kirim Pengajuan';
  }
}

// ── Toast sukses sederhana ───────────────────────────────────────────────────
function showToastSuccess(msg) {
  const toast = document.createElement('div');
  toast.textContent = msg;
  toast.style.cssText = `
    position:fixed;bottom:30px;left:50%;transform:translateX(-50%);
    background:#2e7d32;color:#fff;padding:12px 24px;border-radius:100px;
    font-size:13px;font-weight:600;z-index:9999;
    animation:fadeInUp .3s ease;box-shadow:0 4px 20px rgba(0,0,0,.2);
    white-space:nowrap;
  `;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 1800);
}
</script>

@endsection