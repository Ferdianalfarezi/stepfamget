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
    <div style="font-size:13px;font-weight:700;color:#555;flex-shrink:0;margin-right:4px;">
      {{ $d->umur }} th
    </div>
    @endif
    <button type="button"
      onclick="openModalEdit({{ $d->id }}, {{ Js::from($d->nama_keluarga) }}, {{ Js::from($d->hubungan) }}, {{ Js::from($d->jenis_kelamin) }}, {{ Js::from(optional($d->tanggal_lahir)->format('Y-m-d')) }}, {{ Js::from($d->umur) }}, {{ Js::from($d->ukuran_kaos) }}, {{ Js::from($d->jenis_kaos) }}, {{ Js::from($d->lengan_kaos) }})"
      style="width:32px;height:32px;border-radius:9px;border:1.5px solid #e0e0e0;background:#fff;
             color:#3d7a47;flex-shrink:0;cursor:pointer;display:flex;align-items:center;justify-content:center;">
      <i class="fa-solid fa-pen" style="font-size:12px;"></i>
    </button>
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
     MODAL — Form Pengajuan Anggota Keluarga (TAMBAH)
     ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modalPengajuan"
  style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);
         align-items:flex-end;justify-content:center;">
  <div style="background:#fff;border-radius:24px 24px 0 0;width:100%;max-width:480px;
              padding:24px 20px 36px;animation:slideUp .3s ease;max-height:90vh;overflow-y:auto;">

    <div style="width:40px;height:4px;background:#e0e0e0;border-radius:2px;margin:0 auto 20px;"></div>

    <div style="font-size:16px;font-weight:800;color:#111;margin-bottom:4px;">Tambah Anggota Keluarga</div>
    <div style="font-size:12px;color:#999;margin-bottom:20px;">Pengajuan akan ditinjau oleh panitia terlebih dahulu</div>

    <div style="display:flex;flex-direction:column;gap:14px;">

      <div>
        <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">
          Nama Lengkap <span style="color:#e53935;">*</span>
        </label>
        <input type="text" id="pNama" placeholder="Masukkan nama lengkap"
          style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid #e0e0e0;
                 font-size:13px;outline:none;box-sizing:border-box;transition:border-color .2s;"
          onfocus="this.style.borderColor='#3d7a47'" onblur="this.style.borderColor='#e0e0e0'">
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
          <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">
            Hubungan <span style="color:#e53935;">*</span>
          </label>
          <select id="pHubungan"
            onchange="onHubunganChange(this.value)"
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
          <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">Umur (tahun) <span style="color:#bbb;font-weight:500;">— otomatis</span></label>
          <input type="number" id="pUmur" placeholder="0" readonly tabindex="-1"
            style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid #e0e0e0;
                   font-size:13px;outline:none;box-sizing:border-box;background:#f5f5f5;color:#777;cursor:not-allowed;">
        </div>
      </div>

      <div>
        <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:8px;">Ukuran Kaos</label>

        <div id="pAutoInfo" style="display:none;margin-bottom:10px;">
          <div style="background:#f0f4f0;border-radius:8px;padding:8px 12px;
                      font-size:11.5px;color:#555;display:flex;align-items:center;gap:6px;">
            <i class="fa-solid fa-circle-info" style="color:#2e7d32;font-size:12px;"></i>
            <span id="pAutoInfoText"></span>
          </div>
        </div>

        <div id="pJenisSection" style="display:none;margin-bottom:14px;">
          <div style="font-size:11px;font-weight:700;color:#999;margin-bottom:8px;letter-spacing:.3px;">JENIS KAOS</div>
          <div style="display:flex;gap:8px;">
            @foreach(['Dewasa','Anak'] as $jenis)
            <button type="button" onclick="selectPJenis(this, '{{ $jenis }}')"
              class="btn-p-jenis" data-jenis="{{ $jenis }}"
              style="flex:1;padding:8px;border-radius:8px;border:1.5px solid #e0e0e0;
                     background:#fff;font-size:12px;font-weight:700;color:#777;cursor:pointer;transition:all .15s;">
              @if($jenis === 'Dewasa') 🧑 @else 👶 @endif {{ $jenis }}
            </button>
            @endforeach
          </div>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          @foreach(['S','M','L','XL','XXL','XXXL','XXXXL','XXXXXL'] as $sz)
          <button type="button" onclick="selectPUkuran(this, '{{ $sz }}')"
            class="btn-p-ukuran" data-sz="{{ $sz }}"
            style="padding:7px 14px;border-radius:8px;border:1.5px solid #e0e0e0;
                   background:#fff;font-size:12px;font-weight:700;color:#777;cursor:pointer;
                   transition:all .15s;">
            {{ $sz }}
          </button>
          @endforeach
        </div>

        <input type="hidden" id="pUkuranKaos">
        <input type="hidden" id="pJenisKaos">
        <input type="hidden" id="pLenganKaos">
      </div>

      <div id="pErrorAlert" style="display:none;background:#fce4ec;border-radius:10px;
           padding:10px 14px;font-size:12px;color:#c62828;"></div>

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

{{-- ═══════════════════════════════════════════════════════════════════════════
     MODAL — Form Edit Anggota Keluarga
     ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modalEdit"
  style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);
         align-items:flex-end;justify-content:center;">
  <div style="background:#fff;border-radius:24px 24px 0 0;width:100%;max-width:480px;
              padding:24px 20px 36px;animation:slideUp .3s ease;max-height:90vh;overflow-y:auto;">

    <div style="width:40px;height:4px;background:#e0e0e0;border-radius:2px;margin:0 auto 20px;"></div>

    <div style="font-size:16px;font-weight:800;color:#111;margin-bottom:4px;">Edit Anggota Keluarga</div>
    <div style="font-size:12px;color:#999;margin-bottom:20px;">Perubahan langsung tersimpan tanpa perlu ditinjau panitia</div>

    <input type="hidden" id="eDetailId">

    <div style="display:flex;flex-direction:column;gap:14px;">

      <div>
        <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">
          Nama Lengkap <span style="color:#e53935;">*</span>
        </label>
        <input type="text" id="eNama" placeholder="Masukkan nama lengkap"
          style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid #e0e0e0;
                 font-size:13px;outline:none;box-sizing:border-box;transition:border-color .2s;"
          onfocus="this.style.borderColor='#3d7a47'" onblur="this.style.borderColor='#e0e0e0'">
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
          <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">
            Hubungan <span style="color:#e53935;">*</span>
          </label>
          <select id="eHubungan"
            onchange="onHubunganChangeEdit(this.value)"
            style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid #e0e0e0;
                   font-size:13px;outline:none;box-sizing:border-box;background:#fff;appearance:none;
                   background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%23999' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E\");
                   background-repeat:no-repeat;background-position:right 12px center;">
            <option value="">Pilih</option>
            <option value="Suami">Suami</option>
            <option value="Istri">Istri</option>
            <option value="Anak">Anak</option>
            {{-- opsi ini disembunyikan dari dropdown, cuma dipakai internal buat nyimpen value
                 waktu yang diedit adalah record karyawan/karyawati itu sendiri --}}
            <option value="Karyawan" style="display:none;">Karyawan</option>
            <option value="Karyawati" style="display:none;">Karyawati</option>
          </select>
          <div id="eHubunganLabel" style="display:none;width:100%;padding:11px 14px;border-radius:10px;
               border:1.5px solid #e0e0e0;font-size:13px;box-sizing:border-box;background:#f5f5f5;
               color:#777;"></div>
        </div>
        <div>
          <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">
            Jenis Kelamin <span style="color:#e53935;">*</span>
          </label>
          <select id="eJenisKelamin"
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

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
          <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">Tanggal Lahir</label>
          <input type="date" id="eTanggalLahir"
            onchange="hitungUmurEdit()"
            style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid #e0e0e0;
                   font-size:13px;outline:none;box-sizing:border-box;"
            onfocus="this.style.borderColor='#3d7a47'" onblur="this.style.borderColor='#e0e0e0'">
        </div>
        <div>
          <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:5px;">Umur (tahun) <span style="color:#bbb;font-weight:500;">— otomatis</span></label>
          <input type="number" id="eUmur" placeholder="0" readonly tabindex="-1"
            style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid #e0e0e0;
                   font-size:13px;outline:none;box-sizing:border-box;background:#f5f5f5;color:#777;cursor:not-allowed;">
        </div>
      </div>

      <div>
        <label style="font-size:11.5px;font-weight:700;color:#555;display:block;margin-bottom:8px;">Ukuran Kaos</label>

        <div id="eAutoInfo" style="display:none;margin-bottom:10px;">
          <div style="background:#f0f4f0;border-radius:8px;padding:8px 12px;
                      font-size:11.5px;color:#555;display:flex;align-items:center;gap:6px;">
            <i class="fa-solid fa-circle-info" style="color:#2e7d32;font-size:12px;"></i>
            <span id="eAutoInfoText"></span>
          </div>
        </div>

        <div id="eJenisSection" style="display:none;margin-bottom:14px;">
          <div style="font-size:11px;font-weight:700;color:#999;margin-bottom:8px;letter-spacing:.3px;">JENIS KAOS</div>
          <div style="display:flex;gap:8px;">
            @foreach(['Dewasa','Anak'] as $jenis)
            <button type="button" onclick="selectEJenis(this, '{{ $jenis }}')"
              class="btn-e-jenis" data-jenis="{{ $jenis }}"
              style="flex:1;padding:8px;border-radius:8px;border:1.5px solid #e0e0e0;
                     background:#fff;font-size:12px;font-weight:700;color:#777;cursor:pointer;transition:all .15s;">
              @if($jenis === 'Dewasa') 🧑 @else 👶 @endif {{ $jenis }}
            </button>
            @endforeach
          </div>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          @foreach(['S','M','L','XL','XXL','XXXL','XXXXL','XXXXXL'] as $sz)
          <button type="button" onclick="selectEUkuran(this, '{{ $sz }}')"
            class="btn-e-ukuran" data-sz="{{ $sz }}"
            style="padding:7px 14px;border-radius:8px;border:1.5px solid #e0e0e0;
                   background:#fff;font-size:12px;font-weight:700;color:#777;cursor:pointer;
                   transition:all .15s;">
            {{ $sz }}
          </button>
          @endforeach
        </div>

        <input type="hidden" id="eUkuranKaos">
        <input type="hidden" id="eJenisKaos">
        <input type="hidden" id="eLenganKaos">
      </div>

      <div id="eErrorAlert" style="display:none;background:#fce4ec;border-radius:10px;
           padding:10px 14px;font-size:12px;color:#c62828;"></div>

      <button onclick="submitEdit()" id="btnSubmitEdit"
        style="width:100%;padding:14px;border-radius:12px;border:none;
               background:linear-gradient(135deg,#43a047,#2e7d32);
               color:#fff;font-size:14px;font-weight:800;cursor:pointer;
               transition:opacity .2s;margin-top:4px;">
        Simpan Perubahan
      </button>

      <button onclick="closeModalEdit()"
        style="width:100%;padding:12px;border-radius:12px;border:1.5px solid #e0e0e0;
               background:#fff;color:#999;font-size:13px;font-weight:600;cursor:pointer;">
        Batal
      </button>
    </div>
  </div>
</div>

{{-- CSS --}}
<style>
@keyframes slideUp {
  from { transform: translateY(100%); opacity: 0; }
  to   { transform: translateY(0);    opacity: 1; }
}
@keyframes fadeInUp {
  from { transform:translateX(-50%) translateY(10px); opacity:0; }
  to   { transform:translateX(-50%) translateY(0);    opacity:1; }
}
.btn-p-jenis.active, .btn-e-jenis.active {
  background: #e8f5e9 !important;
  border-color: #3d7a47 !important;
  color: #2e7d32 !important;
}
.btn-p-ukuran.active, .btn-e-ukuran.active {
  background: #e8f5e9 !important;
  border-color: #3d7a47 !important;
  color: #2e7d32 !important;
}
.btn-p-ukuran.active-anak, .btn-e-ukuran.active-anak {
  background: #fff8e1 !important;
  border-color: #f9a825 !important;
  color: #e65100 !important;
}
</style>

{{-- JS --}}
<script>
const PENGAJUAN_URL = "{{ route('guest.pengajuan.store') }}";
const KELUARGA_UPDATE_URL = "{{ route('guest.keluarga.update') }}";
const CSRF_TOKEN    = "{{ csrf_token() }}";

// Batas ukuran kaos: kalau HUBUNGAN-nya "Anak" (bukan cuma jenis kaosnya),
// atau jenis kaos yang dipilih "Anak", ukuran dibatasi max XXL
const KID_SIZES = ['S','M','L','XL','XXL'];

// ═══════════════════════════════════════════════════════════════════════
// TAMBAH ANGGOTA (pengajuan)
// ═══════════════════════════════════════════════════════════════════════
let pSelectedJenis  = null;
let pSelectedLengan = null;
let pSelectedUkuran = null;

function resolvePAuto(hubungan) {
  // Toggle "Jenis Kaos" (Dewasa/Anak) HANYA untuk hubungan === 'Anak'.
  // Selain itu (Suami, Istri, atau nilai tak terduga lain) selalu Dewasa & otomatis.
  if (hubungan === 'Anak') {
    return { jenis: null, lengan: null, manual: true };
  }
  switch (hubungan) {
    case 'Istri': return { jenis: 'Dewasa', lengan: 'Lengan Panjang', manual: false };
    default:      return { jenis: 'Dewasa', lengan: 'Lengan Pendek',  manual: false }; // Suami / lainnya
  }
}

// filter ukuran: restrict kalau HUBUNGAN = Anak ATAU jenis kaos = Anak
function applyPUkuranFilter() {
  const hubungan = document.getElementById('pHubungan').value;
  const restrict = hubungan === 'Anak' || pSelectedJenis === 'Anak';

  document.querySelectorAll('.btn-p-ukuran').forEach(b => {
    const allowed = !restrict || KID_SIZES.includes(b.dataset.sz);
    b.style.display = allowed ? '' : 'none';
  });

  if (restrict && pSelectedUkuran && !KID_SIZES.includes(pSelectedUkuran)) {
    pSelectedUkuran = null;
    document.getElementById('pUkuranKaos').value = '';
    document.querySelectorAll('.btn-p-ukuran').forEach(b => b.classList.remove('active', 'active-anak'));
  }
}

function onHubunganChange(hubungan) {
  const jkEl = document.getElementById('pJenisKelamin');
  if      (hubungan === 'Suami') jkEl.value = 'Laki-laki';
  else if (hubungan === 'Istri') jkEl.value = 'Perempuan';
  else                           jkEl.value = '';

  resetBajuState();

  const auto         = resolvePAuto(hubungan);
  const jenisSection = document.getElementById('pJenisSection');
  const autoInfo     = document.getElementById('pAutoInfo');
  const autoInfoText = document.getElementById('pAutoInfoText');

  if (!hubungan) {
    jenisSection.style.display = 'none';
    autoInfo.style.display     = 'none';
    applyPUkuranFilter();
    return;
  }

  if (!auto.manual) {
    jenisSection.style.display = 'none';
    autoInfo.style.display     = 'block';
    autoInfoText.textContent   = 'Jenis & lengan otomatis: ' + auto.jenis + ' · ' + auto.lengan;
    pSelectedJenis  = auto.jenis;
    pSelectedLengan = auto.lengan;
    document.getElementById('pJenisKaos').value  = auto.jenis;
    document.getElementById('pLenganKaos').value = auto.lengan;
  } else {
    autoInfo.style.display     = 'none';
    jenisSection.style.display = 'block';
  }

  // hubungan "Anak" langsung membatasi ukuran, walau jenis kaos belum dipilih
  applyPUkuranFilter();
}

function selectPJenis(btn, jenis) {
  pSelectedJenis  = jenis;
  pSelectedLengan = 'Lengan Pendek';
  document.querySelectorAll('.btn-p-jenis').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('pJenisKaos').value  = jenis;
  document.getElementById('pLenganKaos').value = 'Lengan Pendek';

  applyPUkuranFilter();

  document.querySelectorAll('.btn-p-ukuran').forEach(b => {
    b.classList.remove('active', 'active-anak');
    if (b.dataset.sz === pSelectedUkuran)
      b.classList.add(jenis === 'Anak' ? 'active-anak' : 'active');
  });
}

function selectPUkuran(btn, sz) {
  pSelectedUkuran = sz;
  document.getElementById('pUkuranKaos').value = sz;
  document.querySelectorAll('.btn-p-ukuran').forEach(b => b.classList.remove('active', 'active-anak'));
  btn.classList.add(pSelectedJenis === 'Anak' ? 'active-anak' : 'active');
}

function resetBajuState() {
  pSelectedJenis  = null;
  pSelectedLengan = null;
  pSelectedUkuran = null;
  document.getElementById('pUkuranKaos').value = '';
  document.getElementById('pJenisKaos').value  = '';
  document.getElementById('pLenganKaos').value = '';
  document.querySelectorAll('.btn-p-jenis, .btn-p-ukuran')
    .forEach(b => {
      b.classList.remove('active', 'active-anak');
      b.style.display = ''; // tampilin lagi semua ukuran, nanti difilter ulang
    });
  document.getElementById('pJenisSection').style.display = 'none';
  document.getElementById('pAutoInfo').style.display     = 'none';
}

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

document.getElementById('modalPengajuan').addEventListener('click', function(e) {
  if (e.target === this) closeModalPengajuan();
});

function hitungUmur() {
  const tgl    = document.getElementById('pTanggalLahir').value;
  const umurEl = document.getElementById('pUmur');
  if (!tgl) { umurEl.value = ''; return; }
  const today = new Date();
  const birth  = new Date(tgl);
  let age = today.getFullYear() - birth.getFullYear();
  const m = today.getMonth() - birth.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
  umurEl.value = age >= 0 ? age : 0;
}

function resetFormPengajuan() {
  document.getElementById('pNama').value         = '';
  document.getElementById('pHubungan').value     = '';
  document.getElementById('pJenisKelamin').value = '';
  document.getElementById('pTanggalLahir').value = '';
  document.getElementById('pUmur').value         = '';
  resetBajuState();
  hideError();
}

function showError(msg) {
  const el = document.getElementById('pErrorAlert');
  el.textContent   = msg;
  el.style.display = 'block';
}
function hideError() {
  document.getElementById('pErrorAlert').style.display = 'none';
}

async function submitPengajuan() {
  hideError();

  const nama     = document.getElementById('pNama').value.trim();
  const hubungan = document.getElementById('pHubungan').value;
  const jk       = document.getElementById('pJenisKelamin').value;
  const tglLahir = document.getElementById('pTanggalLahir').value;
  const umur     = document.getElementById('pUmur').value;

  if (!nama)     return showError('Nama anggota keluarga wajib diisi.');
  if (!hubungan) return showError('Hubungan wajib dipilih.');
  if (!jk)       return showError('Jenis kelamin wajib dipilih.');

  const btn = document.getElementById('btnSubmitPengajuan');
  btn.disabled    = true;
  btn.textContent = 'Mengirim...';

  try {
    const res = await fetch(PENGAJUAN_URL, {
      method : 'POST',
      headers: {
        'Content-Type' : 'application/json',
        'X-CSRF-TOKEN' : CSRF_TOKEN,
        'Accept'       : 'application/json',
      },
      body: JSON.stringify({
        nama_keluarga : nama,
        hubungan      : hubungan,
        jenis_kelamin : jk,
        tanggal_lahir : tglLahir        || null,
        umur          : umur !== '' ? umur : 0,
        ukuran_kaos   : pSelectedUkuran || null,
        jenis_kaos    : pSelectedJenis  || null,
        lengan_kaos   : pSelectedLengan || null,
      }),
    });

    const data = await res.json();

    if (!res.ok) {
      if (data.errors) {
        const firstErr = Object.values(data.errors)[0];
        return showError(Array.isArray(firstErr) ? firstErr[0] : firstErr);
      }
      return showError(data.message || 'Terjadi kesalahan. Coba lagi.');
    }

    closeModalPengajuan();
    showToastSuccess(data.message || 'Pengajuan berhasil dikirim!');
    setTimeout(() => location.reload(), 1800);

  } catch (err) {
    showError('Gagal terhubung ke server. Periksa koneksi internet.');
  } finally {
    btn.disabled    = false;
    btn.textContent = 'Kirim Pengajuan';
  }
}

// ═══════════════════════════════════════════════════════════════════════
// EDIT ANGGOTA (baru) — direct update, no approval
// ═══════════════════════════════════════════════════════════════════════
let eSelectedJenis  = null;
let eSelectedLengan = null;
let eSelectedUkuran = null;

function resolveEAuto(hubungan) {
  // Toggle "Jenis Kaos" (Dewasa/Anak) HANYA untuk hubungan === 'Anak'.
  // Selain itu (Suami, Istri, Karyawan, Karyawati, atau nilai tak terduga lain) selalu Dewasa & otomatis.
  if (hubungan === 'Anak') {
    return { jenis: null, lengan: null, manual: true };
  }
  switch (hubungan) {
    case 'Istri':
    case 'Karyawati':
      return { jenis: 'Dewasa', lengan: 'Lengan Panjang', manual: false };
    default:
      return { jenis: 'Dewasa', lengan: 'Lengan Pendek', manual: false }; // Suami / Karyawan / lainnya
  }
}

// filter ukuran: restrict kalau HUBUNGAN = Anak ATAU jenis kaos = Anak
function applyEUkuranFilter() {
  const hubungan = document.getElementById('eHubungan').value;
  const restrict = hubungan === 'Anak' || eSelectedJenis === 'Anak';

  document.querySelectorAll('.btn-e-ukuran').forEach(b => {
    const allowed = !restrict || KID_SIZES.includes(b.dataset.sz);
    b.style.display = allowed ? '' : 'none';
  });

  if (restrict && eSelectedUkuran && !KID_SIZES.includes(eSelectedUkuran)) {
    eSelectedUkuran = null;
    document.getElementById('eUkuranKaos').value = '';
    document.querySelectorAll('.btn-e-ukuran').forEach(b => b.classList.remove('active', 'active-anak'));
  }
}

function onHubunganChangeEdit(hubungan, skipAutoGender = false) {
  if (!skipAutoGender) {
    const jkEl = document.getElementById('eJenisKelamin');
    if      (hubungan === 'Suami') jkEl.value = 'Laki-laki';
    else if (hubungan === 'Istri') jkEl.value = 'Perempuan';
  }

  const auto         = resolveEAuto(hubungan);
  const jenisSection = document.getElementById('eJenisSection');
  const autoInfo      = document.getElementById('eAutoInfo');
  const autoInfoText  = document.getElementById('eAutoInfoText');

  if (!hubungan) {
    jenisSection.style.display = 'none';
    autoInfo.style.display     = 'none';
    applyEUkuranFilter();
    return;
  }

  if (!auto.manual) {
    jenisSection.style.display = 'none';
    autoInfo.style.display     = 'block';
    autoInfoText.textContent   = 'Jenis & lengan otomatis: ' + auto.jenis + ' · ' + auto.lengan;
    eSelectedJenis  = auto.jenis;
    eSelectedLengan = auto.lengan;
    document.getElementById('eJenisKaos').value  = auto.jenis;
    document.getElementById('eLenganKaos').value = auto.lengan;
  } else {
    autoInfo.style.display     = 'none';
    jenisSection.style.display = 'block';
  }

  // hubungan "Anak" langsung membatasi ukuran, walau jenis kaos belum dipilih
  applyEUkuranFilter();
}

function selectEJenis(btn, jenis) {
  eSelectedJenis  = jenis;
  eSelectedLengan = 'Lengan Pendek';
  document.querySelectorAll('.btn-e-jenis').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('eJenisKaos').value  = jenis;
  document.getElementById('eLenganKaos').value = 'Lengan Pendek';

  applyEUkuranFilter();

  document.querySelectorAll('.btn-e-ukuran').forEach(b => {
    b.classList.remove('active', 'active-anak');
    if (b.dataset.sz === eSelectedUkuran)
      b.classList.add(jenis === 'Anak' ? 'active-anak' : 'active');
  });
}

function selectEUkuran(btn, sz) {
  eSelectedUkuran = sz;
  document.getElementById('eUkuranKaos').value = sz;
  document.querySelectorAll('.btn-e-ukuran').forEach(b => b.classList.remove('active', 'active-anak'));
  btn.classList.add(eSelectedJenis === 'Anak' ? 'active-anak' : 'active');
}

function hitungUmurEdit() {
  const tgl    = document.getElementById('eTanggalLahir').value;
  const umurEl = document.getElementById('eUmur');
  if (!tgl) { umurEl.value = ''; return; }
  const today = new Date();
  const birth  = new Date(tgl);
  let age = today.getFullYear() - birth.getFullYear();
  const m = today.getMonth() - birth.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
  umurEl.value = age >= 0 ? age : 0;
}

function showErrorEdit(msg) {
  const el = document.getElementById('eErrorAlert');
  el.textContent   = msg;
  el.style.display = 'block';
}
function hideErrorEdit() {
  document.getElementById('eErrorAlert').style.display = 'none';
}

// dipanggil dari tombol pensil tiap card
function openModalEdit(id, nama, hubungan, jenisKelamin, tanggalLahir, umur, ukuranKaos, jenisKaos, lenganKaos) {
  hideErrorEdit();

  document.getElementById('eDetailId').value      = id;
  document.getElementById('eNama').value           = nama || '';
  document.getElementById('eHubungan').value       = hubungan || '';
  document.getElementById('eTanggalLahir').value   = tanggalLahir || '';
  document.getElementById('eUmur').value           = umur || '';
  hitungUmurEdit(); // recompute otomatis dari tanggal lahir (jangan andalkan value lama dari DB)

  // Kalau yang diedit adalah record karyawan/karyawati itu sendiri, field Hubungan
  // diganti jadi label statis (bukan dropdown) — dropdown cuma punya opsi Suami/Istri/Anak.
  const selectHubEl = document.getElementById('eHubungan');
  const labelHubEl  = document.getElementById('eHubunganLabel');
  const isKaryawanSelf = (hubungan === 'Karyawan' || hubungan === 'Karyawati');

  if (isKaryawanSelf) {
    selectHubEl.value       = hubungan; // tetep diisi (option-nya ada, cuma disembunyikan dari list)
    selectHubEl.style.display = 'none';
    labelHubEl.textContent    = hubungan + ' (tidak bisa diubah)';
    labelHubEl.style.display  = 'block';
  } else {
    selectHubEl.value       = hubungan || '';
    selectHubEl.style.display = '';
    labelHubEl.style.display  = 'none';
  }

  // reset tombol jenis & ukuran dulu (termasuk tampilin lagi semua ukuran, nanti difilter ulang)
  document.querySelectorAll('.btn-e-jenis, .btn-e-ukuran').forEach(b => {
    b.classList.remove('active', 'active-anak');
    b.style.display = '';
  });

  eSelectedJenis  = jenisKaos  || null;
  eSelectedLengan = lenganKaos || null;
  eSelectedUkuran = ukuranKaos || null;
  document.getElementById('eJenisKaos').value  = jenisKaos  || '';
  document.getElementById('eLenganKaos').value = lenganKaos || '';
  document.getElementById('eUkuranKaos').value = ukuranKaos || '';

  // tampilkan section jenis/auto-info sesuai hubungan, tanpa override gender yg sudah diisi
  // (fungsi ini juga otomatis memanggil applyEUkuranFilter → batasi max XXL kalau hubungan Anak)
  onHubunganChangeEdit(hubungan, true);
  document.getElementById('eJenisKelamin').value = jenisKelamin || '';

  // highlight ukuran yang sudah tersimpan (kalau masih terlihat setelah difilter)
  if (eSelectedUkuran) {
    document.querySelectorAll('.btn-e-ukuran').forEach(b => {
      if (b.dataset.sz === eSelectedUkuran) b.classList.add(eSelectedJenis === 'Anak' ? 'active-anak' : 'active');
    });
  }
  // highlight jenis kaos (hanya relevan utk Anak)
  if (hubungan === 'Anak' && jenisKaos) {
    document.querySelectorAll('.btn-e-jenis').forEach(b => {
      if (b.dataset.jenis === jenisKaos) b.classList.add('active');
    });
  }

  const modal = document.getElementById('modalEdit');
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeModalEdit() {
  document.getElementById('modalEdit').style.display = 'none';
  document.body.style.overflow = '';
}

document.getElementById('modalEdit').addEventListener('click', function(e) {
  if (e.target === this) closeModalEdit();
});

async function submitEdit() {
  hideErrorEdit();

  const id       = document.getElementById('eDetailId').value;
  const nama     = document.getElementById('eNama').value.trim();
  const hubungan = document.getElementById('eHubungan').value;
  const jk       = document.getElementById('eJenisKelamin').value;
  const tglLahir = document.getElementById('eTanggalLahir').value;
  const umur     = document.getElementById('eUmur').value;

  if (!nama)     return showErrorEdit('Nama anggota keluarga wajib diisi.');
  if (!hubungan) return showErrorEdit('Hubungan wajib dipilih.');
  if (!jk)       return showErrorEdit('Jenis kelamin wajib dipilih.');

  const btn = document.getElementById('btnSubmitEdit');
  btn.disabled    = true;
  btn.textContent = 'Menyimpan...';

  try {
    const res = await fetch(KELUARGA_UPDATE_URL, {
      method : 'POST',
      headers: {
        'Content-Type' : 'application/json',
        'X-CSRF-TOKEN' : CSRF_TOKEN,
        'Accept'       : 'application/json',
      },
      body: JSON.stringify({
        detail_id     : id,
        nama_keluarga : nama,
        hubungan      : hubungan,
        jenis_kelamin : jk,
        tanggal_lahir : tglLahir        || null,
        umur          : umur !== '' ? umur : 0,
        ukuran_kaos   : eSelectedUkuran || null,
        jenis_kaos    : eSelectedJenis  || null,
        lengan_kaos   : eSelectedLengan || null,
      }),
    });

    const data = await res.json();

    if (!res.ok) {
      if (data.errors) {
        const firstErr = Object.values(data.errors)[0];
        return showErrorEdit(Array.isArray(firstErr) ? firstErr[0] : firstErr);
      }
      return showErrorEdit(data.message || 'Terjadi kesalahan. Coba lagi.');
    }

    closeModalEdit();
    showToastSuccess(data.message || 'Data berhasil diperbarui!');
    setTimeout(() => location.reload(), 1200);

  } catch (err) {
    showErrorEdit('Gagal terhubung ke server. Periksa koneksi internet.');
  } finally {
    btn.disabled    = false;
    btn.textContent = 'Simpan Perubahan';
  }
}

// ── Toast sukses (shared) ──────────────────────────────────────────────
function showToastSuccess(msg) {
  const toast = document.createElement('div');
  toast.textContent   = msg;
  toast.style.cssText = 'position:fixed;bottom:30px;left:50%;transform:translateX(-50%);' +
    'background:#2e7d32;color:#fff;padding:12px 24px;border-radius:100px;' +
    'font-size:13px;font-weight:600;z-index:9999;animation:fadeInUp .3s ease;' +
    'box-shadow:0 4px 20px rgba(0,0,0,.2);white-space:nowrap;';
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 1800);
}
</script>

@endsection