@extends('layouts.app')
@section('title', 'Data Konsumsi')
@section('page-title', 'Konsumsi')

@section('content')

{{-- FILTER BAR --}}
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('konsumsis.index') }}">
            <div class="filters">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama atau satuan..."
                           value="{{ $search ?? '' }}" style="width:280px;">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if($search)
                    <a href="{{ route('konsumsis.index') }}" class="btn btn-outline">
                        <i class="fa-solid fa-xmark"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- TABLE CARD --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-utensils" style="color:#0b4614;margin-right:6px;"></i>Data Konsumsi
            </div>
            
        </div>
        @if(auth()->user()->nama !== 'Hitz')
            <div style="display:flex;gap:8px;align-items:center;">
                <button onclick="openModalAdd()"
                        class="btn"
                        style="background:#0b4614;color:#fff;border:none;display:inline-flex;align-items:center;gap:6px;">
                    <i class="fa-solid fa-plus"></i> Tambah Konsumsi
                </button>
            </div>
        @endif
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:45px;">#</th>
                    <th>Nama</th>
                    <th>Satuan</th>
                    <th style="text-align:center;">Spare</th>
                    <th style="text-align:center;background:#eff6ff;">Qty</th>
                    <th style="text-align:center;background:#eff6ff;">Total</th>
                    @if(auth()->user()->nama !== 'Hitz')
                        <th style="width:100px;text-align:center;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($konsumsis as $k)
                @php
                    $rowQty = match($k->qty_source) {
                        'karyawan_saja' => $qtyKaryawanSemua,
                        'anak'          => $qtyAnakSemua,
                        'pasangan'      => $qtyPasanganSemua,
                        'vip'           => $qtyVip,
                        'vvip'          => $qtyVvip,
                        default         => $qtySemua,
                    };
                    $qtySourceLabel = match($k->qty_source) {
                        'karyawan_saja' => 'Karyawan Saja',
                        'anak'          => 'Anak Karyawan',
                        'pasangan'      => 'Pasangan (Suami/Istri)',
                        'vip'           => 'Kendaraan VIP',
                        'vvip'          => 'Kendaraan VVIP',
                        default         => 'Semua Orang',
                    };
                @endphp
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($konsumsis->currentPage() - 1) * $konsumsis->perPage() }}
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:10px;
                                        background:#e8f5e9;color:#2e7d32;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:12px;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($k->nama, 0, 2)) }}
                            </div>
                            <div style="font-weight:600;font-size:13.5px;">{{ $k->nama }}</div>
                        </div>
                    </td>
                    <td>
                        <span style="background:#f1f5f9;color:#475569;border-radius:6px;
                                     padding:3px 10px;font-size:12px;font-weight:600;">
                            {{ $k->satuan }}
                        </span>
                    </td>
                   
                    <td style="text-align:center;">
                        <span style="font-size:14px;font-weight:600;color:#0369a1;">
                            {{ number_format($k->spare) }}
                        </span>
                    </td>
                    <td style="text-align:center;background:#f8fbff;">
                        <span style="font-size:15px;font-weight:700;color:#0369a1;">
                            {{ number_format($rowQty) }}
                        </span>
                    </td>
                    <td style="text-align:center;background:#f8fbff;">
                        <span style="background:#0369a1;color:#fff;border-radius:8px;
                                     padding:4px 14px;font-size:14px;font-weight:800;
                                     letter-spacing:.5px;">
                            {{ number_format($rowQty + $k->spare) }}
                        </span>
                    </td>
                    @if(auth()->user()->nama !== 'Hitz')
                        <td style="text-align:center;">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <button onclick="openModalEdit({{ $k->id }})"
                                        class="btn btn-outline"
                                        style="padding:5px 10px;font-size:12px;">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button onclick="hapusKonsumsi({{ $k->id }}, '{{ addslashes($k->nama) }}')"
                                        class="btn"
                                        style="padding:5px 10px;font-size:12px;background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-utensils" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Belum ada data konsumsi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($konsumsis->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $konsumsis->firstItem() }}–{{ $konsumsis->lastItem() }} dari {{ $konsumsis->total() }} data
        </div>
        <div class="pagination">
            @if($konsumsis->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $konsumsis->previousPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif
            @foreach($konsumsis->getUrlRange(max(1,$konsumsis->currentPage()-2), min($konsumsis->lastPage(),$konsumsis->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $konsumsis->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($konsumsis->hasMorePages())
                <a href="{{ $konsumsis->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- MODAL TAMBAH --}}
<div class="modal-overlay" id="modalAdd">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-plus" style="color:#0b4614;margin-right:8px;"></i>Tambah Konsumsi
                </div>
                <div class="modal-subtitle">Isi data item konsumsi baru</div>
            </div>
            <button class="modal-close" onclick="closeModalAdd()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="k-form-group">
                <label class="k-form-label">Nama <span class="required">*</span></label>
                <input type="text" id="addNama" class="k-form-input"
                       placeholder="e.g. Makan Siang, Gorengan, Souvenir Anak">
                <div class="k-form-error" id="addNamaError"></div>
            </div>
            <div class="k-form-group">
                <label class="k-form-label">Satuan <span class="required">*</span></label>
                <input type="text" id="addSatuan" class="k-form-input"
                       placeholder="e.g. porsi, pcs, kotak, botol">
                <div class="k-form-error" id="addSatuanError"></div>
            </div>
            <div class="k-form-group">
                <label class="k-form-label">Sumber Qty <span class="required">*</span></label>
                <select id="addQtySource" class="k-form-input">
                    <option value="semua_orang">Semua Orang (Karyawan + Keluarga)</option>
                    <option value="karyawan_saja">Karyawan Saja (Tanpa Keluarga)</option>
                    <option value="anak">Anak Karyawan Saja</option>
                    <option value="pasangan">Pasangan (Suami/Istri) Saja</option>
                    <option value="vip">Kendaraan VIP</option>
                    <option value="vvip">Kendaraan VVIP</option>
                </select>
                <div class="k-form-error" id="addQtySourceError"></div>
            </div>
            <div class="k-form-group">
                <label class="k-form-label">Spare <span class="required">*</span></label>
                <input type="number" id="addSpare" class="k-form-input"
                    placeholder="0" value="0">
                <div class="k-form-error" id="addSpareError"></div>
            </div>

            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;
                        padding:10px 14px;font-size:13px;color:#1d4ed8;margin-top:4px;">
                <i class="fa-solid fa-calculator" style="margin-right:6px;"></i>
                Estimasi Total:
                <strong id="previewTotal">–</strong>
            </div>

            <div class="k-form-actions" style="margin-top:20px;">
                <button type="button" class="btn btn-outline" onclick="closeModalAdd()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button type="button" id="btnDoAdd" onclick="doAdd()"
                        class="btn" style="background:#0b4614;color:#fff;">
                    <i class="fa-solid fa-plus"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-pen" style="color:#0369a1;margin-right:8px;"></i>Edit Konsumsi
                </div>
                <div class="modal-subtitle">Perbarui data item konsumsi</div>
            </div>
            <button class="modal-close" onclick="closeModalEdit()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="editId">
            <div class="k-form-group">
                <label class="k-form-label">Nama <span class="required">*</span></label>
                <input type="text" id="editNama" class="k-form-input">
                <div class="k-form-error" id="editNamaError"></div>
            </div>
            <div class="k-form-group">
                <label class="k-form-label">Satuan <span class="required">*</span></label>
                <input type="text" id="editSatuan" class="k-form-input">
                <div class="k-form-error" id="editSatuanError"></div>
            </div>
            <div class="k-form-group">
                <label class="k-form-label">Sumber Qty <span class="required">*</span></label>
                <select id="editQtySource" class="k-form-input">
                    <option value="semua_orang">Semua Orang (Karyawan + Keluarga)</option>
                    <option value="karyawan_saja">Karyawan Saja (Tanpa Keluarga)</option>
                    <option value="anak">Anak Karyawan Saja</option>
                    <option value="pasangan">Pasangan (Suami/Istri) Saja</option>
                    <option value="vip">Kendaraan VIP</option>
                    <option value="vvip">Kendaraan VVIP</option>
                </select>
                <div class="k-form-error" id="editQtySourceError"></div>
            </div>
            <div class="k-form-group">
                <label class="k-form-label">Spare <span class="required">*</span></label>
                <input type="number" id="editSpare" class="k-form-input">
                <div class="k-form-error" id="editSpareError"></div>
            </div>

            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;
                        padding:10px 14px;font-size:13px;color:#1d4ed8;margin-top:4px;">
                <i class="fa-solid fa-calculator" style="margin-right:6px;"></i>
                Estimasi Total:
                <strong id="editPreviewTotal">–</strong>
            </div>

            <div class="k-form-actions" style="margin-top:20px;">
                <button type="button" class="btn btn-outline" onclick="closeModalEdit()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button type="button" id="btnDoEdit" onclick="doEdit()"
                        class="btn" style="background:#0369a1;color:#fff;">
                    <i class="fa-solid fa-floppy-disk"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// Qty per kategori — dikirim dari controller
const QTY_SEMUA_ORANG = {{ $qtySemua }};
const QTY_KARYAWAN     = {{ $qtyKaryawanSemua }};
const QTY_ANAK         = {{ $qtyAnakSemua }};
const QTY_PASANGAN     = {{ $qtyPasanganSemua }};
const QTY_VIP          = {{ $qtyVip }};
const QTY_VVIP         = {{ $qtyVvip }};

function getQtyBySource(source) {
    switch (source) {
        case 'karyawan_saja': return QTY_KARYAWAN;
        case 'anak':           return QTY_ANAK;
        case 'pasangan':       return QTY_PASANGAN;
        case 'vip':             return QTY_VIP;
        case 'vvip':            return QTY_VVIP;
        default:                 return QTY_SEMUA_ORANG;
    }
}

/* ─── Toast ─── */
function showToast(msg, type = 'success') {
    let wrap = document.getElementById('toastContainer');
    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id = 'toastContainer';
        wrap.className = 'toast-container';
        document.body.appendChild(wrap);
    }
    const icon  = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<i class="fa-solid ${icon}"></i>${msg}`;
    wrap.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

/* ─── Modal Add ─── */
function openModalAdd() {
    document.getElementById('addNama').value      = '';
    document.getElementById('addSatuan').value    = '';
    document.getElementById('addQtySource').value = 'semua_orang';
    document.getElementById('addSpare').value     = '0';
    document.getElementById('addNamaError').textContent      = '';
    document.getElementById('addSatuanError').textContent    = '';
    document.getElementById('addQtySourceError').textContent = '';
    document.getElementById('addSpareError').textContent     = '';
    updatePreviewAdd();
    document.getElementById('modalAdd').classList.add('show');
    document.getElementById('addNama').focus();
}
function closeModalAdd() {
    document.getElementById('modalAdd').classList.remove('show');
}

function updatePreviewAdd() {
    const source = document.getElementById('addQtySource').value;
    const qty    = getQtyBySource(source);
    const spare  = parseInt(document.getElementById('addSpare').value) || 0;
    const total  = qty + spare;
    document.getElementById('previewTotal').textContent =
        `${qty.toLocaleString('id-ID')} + ${spare.toLocaleString('id-ID')} = ${total.toLocaleString('id-ID')}`;
}
document.getElementById('addQtySource').addEventListener('change', updatePreviewAdd);
document.getElementById('addSpare').addEventListener('input', updatePreviewAdd);

async function doAdd() {
    const nama      = document.getElementById('addNama').value.trim();
    const satuan    = document.getElementById('addSatuan').value.trim();
    const qtySource = document.getElementById('addQtySource').value;
    const spare     = document.getElementById('addSpare').value;
    let valid = true;

    document.getElementById('addNamaError').textContent      = '';
    document.getElementById('addSatuanError').textContent    = '';
    document.getElementById('addQtySourceError').textContent = '';
    document.getElementById('addSpareError').textContent     = '';

    if (!nama)   { document.getElementById('addNamaError').textContent   = 'Nama wajib diisi.'; valid = false; }
    if (!satuan) { document.getElementById('addSatuanError').textContent = 'Satuan wajib diisi.'; valid = false; }
    if (spare === '' || isNaN(parseInt(spare))) { document.getElementById('addSpareError').textContent = 'Spare wajib diisi angka.'; valid = false; }
    if (!valid) return;

    const btn = document.getElementById('btnDoAdd');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res  = await fetch('{{ route("konsumsis.store") }}', {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : CSRF,
                'Accept'       : 'application/json',
            },
            body: JSON.stringify({ nama, satuan, spare: parseInt(spare), qty_source: qtySource }),
        });
        const data = await res.json();
        if (res.ok) {
            showToast(data.message ?? 'Konsumsi berhasil ditambahkan.');
            closeModalAdd();
            setTimeout(() => location.reload(), 800);
        } else {
            if (data.errors) {
                if (data.errors.nama)       document.getElementById('addNamaError').textContent      = data.errors.nama[0];
                if (data.errors.satuan)     document.getElementById('addSatuanError').textContent    = data.errors.satuan[0];
                if (data.errors.spare)      document.getElementById('addSpareError').textContent     = data.errors.spare[0];
                if (data.errors.qty_source) document.getElementById('addQtySourceError').textContent = data.errors.qty_source[0];
            } else {
                showToast(data.message ?? 'Terjadi kesalahan.', 'error');
            }
        }
    } catch {
        showToast('Gagal menghubungi server.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-plus"></i> Simpan';
    }
}

/* ─── Modal Edit ─── */
async function openModalEdit(id) {
    document.getElementById('editNamaError').textContent      = '';
    document.getElementById('editSatuanError').textContent    = '';
    document.getElementById('editQtySourceError').textContent = '';
    document.getElementById('editSpareError').textContent     = '';
    document.getElementById('editPreviewTotal').textContent   = '...';

    try {
        const res  = await fetch(`/konsumsis/${id}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await res.json();
        document.getElementById('editId').value        = data.id;
        document.getElementById('editNama').value      = data.nama;
        document.getElementById('editSatuan').value    = data.satuan;
        document.getElementById('editQtySource').value = data.qty_source;
        document.getElementById('editSpare').value     = data.spare;
        updatePreviewEdit();
        document.getElementById('modalEdit').classList.add('show');
        document.getElementById('editNama').focus();
    } catch {
        showToast('Gagal memuat data.', 'error');
    }
}
function closeModalEdit() {
    document.getElementById('modalEdit').classList.remove('show');
}

function updatePreviewEdit() {
    const source = document.getElementById('editQtySource').value;
    const qty    = getQtyBySource(source);
    const spare  = parseInt(document.getElementById('editSpare').value) || 0;
    const total  = qty + spare;
    document.getElementById('editPreviewTotal').textContent =
        `${qty.toLocaleString('id-ID')} + ${spare.toLocaleString('id-ID')} = ${total.toLocaleString('id-ID')}`;
}
document.getElementById('editQtySource').addEventListener('change', updatePreviewEdit);
document.getElementById('editSpare').addEventListener('input', updatePreviewEdit);

async function doEdit() {
    const id        = document.getElementById('editId').value;
    const nama      = document.getElementById('editNama').value.trim();
    const satuan    = document.getElementById('editSatuan').value.trim();
    const qtySource = document.getElementById('editQtySource').value;
    const spare     = document.getElementById('editSpare').value;
    let valid = true;

    document.getElementById('editNamaError').textContent      = '';
    document.getElementById('editSatuanError').textContent    = '';
    document.getElementById('editQtySourceError').textContent = '';
    document.getElementById('editSpareError').textContent     = '';

    if (!nama)   { document.getElementById('editNamaError').textContent   = 'Nama wajib diisi.'; valid = false; }
    if (!satuan) { document.getElementById('editSatuanError').textContent = 'Satuan wajib diisi.'; valid = false; }
    if (spare === '' || isNaN(parseInt(spare))) { document.getElementById('editSpareError').textContent = 'Spare wajib diisi angka.'; valid = false; }
    if (!valid) return;

    const btn = document.getElementById('btnDoEdit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res  = await fetch(`/konsumsis/${id}`, {
            method : 'PUT',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : CSRF,
                'Accept'       : 'application/json',
            },
            body: JSON.stringify({ nama, satuan, spare: parseInt(spare), qty_source: qtySource }),
        });
        const data = await res.json();
        if (res.ok) {
            showToast(data.message ?? 'Konsumsi berhasil diperbarui.');
            closeModalEdit();
            setTimeout(() => location.reload(), 800);
        } else {
            if (data.errors) {
                if (data.errors.nama)       document.getElementById('editNamaError').textContent      = data.errors.nama[0];
                if (data.errors.satuan)     document.getElementById('editSatuanError').textContent    = data.errors.satuan[0];
                if (data.errors.spare)      document.getElementById('editSpareError').textContent     = data.errors.spare[0];
                if (data.errors.qty_source) document.getElementById('editQtySourceError').textContent = data.errors.qty_source[0];
            } else {
                showToast(data.message ?? 'Terjadi kesalahan.', 'error');
            }
        }
    } catch {
        showToast('Gagal menghubungi server.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Update';
    }
}

/* ─── Hapus ─── */
async function hapusKonsumsi(id, nama) {
    if (!confirm(`Hapus konsumsi "${nama}"? Tindakan ini tidak bisa dibatalkan.`)) return;

    try {
        const res  = await fetch(`/konsumsis/${id}`, {
            method : 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok) {
            showToast(data.message ?? 'Konsumsi berhasil dihapus.');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message ?? 'Gagal menghapus.', 'error');
        }
    } catch {
        showToast('Gagal menghubungi server.', 'error');
    }
}

/* ─── Close on backdrop/Esc ─── */
['modalAdd', 'modalEdit'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('modalAdd').classList.remove('show');
        document.getElementById('modalEdit').classList.remove('show');
    }
});
</script>
@endpush