@extends('layouts.app')
@section('title', 'Import Data')
@section('page-title', 'Import Data Excel')

@section('content')

<div style="max-width:860px;">

    {{-- PANDUAN --}}
    <div class="card" style="margin-bottom:20px;border-left:4px solid #3b82f6;">
        <div class="card-body" style="padding:16px 20px;">
            <div style="display:flex;gap:12px;align-items:flex-start;">
                <div style="width:36px;height:36px;background:#dbeafe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid fa-circle-info" style="color:#3b82f6;font-size:16px;"></i>
                </div>
                <div>
                    <div style="font-weight:600;font-size:14px;margin-bottom:6px;">Panduan Import</div>
                    <div style="font-size:13px;color:#475569;line-height:1.8;">
                        <strong>File Karyawan</strong> harus memiliki kolom:
                        <code style="background:#f1f5f9;padding:1px 6px;border-radius:4px;font-size:12px;">NIK_Login, NIK, Nama, Departemen, Jumlah Keluarga, Keterangan, Status Kehadiran</code><br>
                        <strong>File Detail/Keluarga</strong> harus memiliki kolom:
                        <code style="background:#f1f5f9;padding:1px 6px;border-radius:4px;font-size:12px;">NIK, Nama Karyawan, Departemen, Nama Keluarga, Jenis Kelamin, Hubungan, Tanggal Lahir, Umur (per 30 Aug 2025), Ukuran Kaos</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS CURRENT --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="stat-value">{{ number_format($totalKaryawan) }}</div>
                <div class="stat-label">Data Karyawan saat ini</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fa-solid fa-people-group"></i></div>
            <div>
                <div class="stat-value">{{ number_format($totalDetail) }}</div>
                <div class="stat-label">Data Detail/Keluarga saat ini</div>
            </div>
        </div>
    </div>

    {{-- FORM KARYAWAN --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-file-excel" style="color:#10b981;margin-right:8px;"></i>Import Data Karyawan</div>
        </div>
        <div class="card-body">

            @if(session('success_karyawan'))
            <div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:12px 16px;font-size:13px;color:#065f46;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <i class="fa-solid fa-circle-check"></i> {{ session('success_karyawan') }}
            </div>
            @endif
            @if(session('error_karyawan'))
            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;font-size:13px;color:#991b1b;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error_karyawan') }}
            </div>
            @endif

            <form method="POST" action="{{ route('import.karyawan') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px;">Pilih File Excel (.xlsx)</label>
                        <div class="drop-zone" id="dropKaryawan" onclick="document.getElementById('fileKaryawan').click()">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size:28px;color:#94a3b8;margin-bottom:8px;display:block;"></i>
                            <div style="font-size:13px;font-weight:500;color:#475569;" id="labelKaryawan">Klik atau drag & drop file di sini</div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:4px;">Format: .xlsx, .xls · Maks 10MB</div>
                        </div>
                        <input type="file" id="fileKaryawan" name="file_karyawan" accept=".xlsx,.xls" style="display:none;" onchange="updateLabel(this,'labelKaryawan','dropKaryawan')">
                        @error('file_karyawan')<div style="font-size:12px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px;">Mode Import</label>
                        <div style="display:flex;flex-direction:column;gap:10px;margin-top:4px;">
                            <label class="radio-card">
                                <input type="radio" name="mode_karyawan" value="append" checked>
                                <div class="radio-content">
                                    <div style="font-weight:600;font-size:13px;">Tambahkan (Append)</div>
                                    <div style="font-size:11.5px;color:#64748b;">Data baru ditambahkan ke data yang sudah ada</div>
                                </div>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="mode_karyawan" value="replace">
                                <div class="radio-content">
                                    <div style="font-weight:600;font-size:13px;">Timpa (Replace)</div>
                                    <div style="font-size:11.5px;color:#ef4444;">⚠️ Hapus semua data lama, ganti dengan data baru</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;height:42px;">
                    <i class="fa-solid fa-file-import"></i> Import Data Karyawan
                </button>
            </form>
        </div>
    </div>

    {{-- FORM DETAIL --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-file-excel" style="color:#10b981;margin-right:8px;"></i>Import Data Detail Keluarga</div>
        </div>
        <div class="card-body">

            @if(session('success_detail'))
            <div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:12px 16px;font-size:13px;color:#065f46;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <i class="fa-solid fa-circle-check"></i> {{ session('success_detail') }}
            </div>
            @endif
            @if(session('error_detail'))
            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;font-size:13px;color:#991b1b;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error_detail') }}
            </div>
            @endif

            <form method="POST" action="{{ route('import.detail') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px;">Pilih File Excel (.xlsx)</label>
                        <div class="drop-zone" id="dropDetail" onclick="document.getElementById('fileDetail').click()">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size:28px;color:#94a3b8;margin-bottom:8px;display:block;"></i>
                            <div style="font-size:13px;font-weight:500;color:#475569;" id="labelDetail">Klik atau drag & drop file di sini</div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:4px;">Format: .xlsx, .xls · Maks 10MB</div>
                        </div>
                        <input type="file" id="fileDetail" name="file_detail" accept=".xlsx,.xls" style="display:none;" onchange="updateLabel(this,'labelDetail','dropDetail')">
                        @error('file_detail')<div style="font-size:12px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px;">Mode Import</label>
                        <div style="display:flex;flex-direction:column;gap:10px;margin-top:4px;">
                            <label class="radio-card">
                                <input type="radio" name="mode_detail" value="append" checked>
                                <div class="radio-content">
                                    <div style="font-weight:600;font-size:13px;">Tambahkan (Append)</div>
                                    <div style="font-size:11.5px;color:#64748b;">Data baru ditambahkan ke data yang sudah ada</div>
                                </div>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="mode_detail" value="replace">
                                <div class="radio-content">
                                    <div style="font-weight:600;font-size:13px;">Timpa (Replace)</div>
                                    <div style="font-size:11.5px;color:#ef4444;">⚠️ Hapus semua data lama, ganti dengan data baru</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;height:42px;">
                    <i class="fa-solid fa-file-import"></i> Import Data Detail Keluarga
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
.drop-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    background: #f8fafc;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.drop-zone:hover, .drop-zone.dragover {
    border-color: #3b82f6;
    background: #eff6ff;
}
.drop-zone.has-file {
    border-color: #10b981;
    background: #f0fdf4;
}
.radio-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    transition: .2s;
}
.radio-card:hover { border-color: #3b82f6; background: #eff6ff; }
.radio-card input[type=radio] { accent-color: #3b82f6; width: 16px; height: 16px; flex-shrink: 0; }
.radio-card input[type=radio]:checked + .radio-content { color: #1e3a8a; }
.radio-card:has(input:checked) { border-color: #3b82f6; background: #eff6ff; }
</style>
@endpush

@push('scripts')
<script>
function updateLabel(input, labelId, zoneId) {
    const label = document.getElementById(labelId);
    const zone  = document.getElementById(zoneId);
    if (input.files.length > 0) {
        const name = input.files[0].name;
        const size = (input.files[0].size / 1024).toFixed(1);
        label.innerHTML = `<i class="fa-solid fa-file-excel" style="color:#10b981;margin-right:6px;"></i>${name} <span style="color:#94a3b8;">(${size} KB)</span>`;
        zone.classList.add('has-file');
    }
}

// Drag & drop
['dropKaryawan','dropDetail'].forEach(id => {
    const zone = document.getElementById(id);
    const inputId = id === 'dropKaryawan' ? 'fileKaryawan' : 'fileDetail';
    const labelId = id === 'dropKaryawan' ? 'labelKaryawan' : 'labelDetail';

    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('dragover');
        const input = document.getElementById(inputId);
        const dt = new DataTransfer();
        dt.items.add(e.dataTransfer.files[0]);
        input.files = dt.files;
        updateLabel(input, labelId, id);
    });
});

// Confirm replace mode
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const mode = this.querySelector('input[name^="mode"]:checked');
        if (mode && mode.value === 'replace') {
            if (!confirm('⚠️ Mode REPLACE akan menghapus SEMUA data lama!\n\nLanjutkan?')) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endpush
