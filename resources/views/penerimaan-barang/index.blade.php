@extends('layouts.app')
@section('title', 'Penerimaan Barang')
@section('page-title', 'Penerimaan Barang')

@section('content')
{{-- ── FILTER BAR ── --}}
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('penerimaan-barang.index') }}">
            <div class="filters">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari barang, supplier, atau PIC..."
                           value="{{ request('search') }}" style="width:300px;">
                </div>
                <select name="supplier_id" class="form-control" style="width:200px;">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}"
                            {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                            {{ $sup->nama }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->filled('search') || request()->filled('supplier_id'))
                    <a href="{{ route('penerimaan-barang.index') }}" class="btn btn-outline">
                        <i class="fa-solid fa-xmark"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── TABLE CARD ── --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-boxes-stacked" style="color:#0b4614;margin-right:6px;"></i>Data Penerimaan Barang
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total {{ $items->total() }} record ditemukan
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <button class="btn btn-primary" onclick="openModalCreate()">
                <i class="fa-solid fa-plus"></i> Tambah
            </button>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Supplier</th>
                    <th>Nama Barang</th>
                    <th style="text-align:right;">Harga</th>
                    <th style="text-align:right;">Qty</th>
                    <th style="text-align:right;">Total</th>
                    <th>PIC</th>
                    <th>Tanggal Masuk</th>
                    <th style="width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $item->supplier->nama ?? '-' }}</div>
                    </td>
                    <td>
                        <div style="font-size:13.5px;font-weight:500;">{{ $item->barang }}</div>
                    </td>
                    <td style="text-align:right;font-size:13px;color:#475569;">
                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                    </td>
                    <td style="text-align:right;font-size:13px;color:#475569;">
                        {{ number_format($item->qty, 0, ',', '.') }}
                    </td>
                    <td style="text-align:right;font-size:13px;font-weight:600;color:#0b4614;">
                        Rp {{ number_format($item->total, 0, ',', '.') }}
                    </td>
                    <td style="font-size:13px;color:#475569;">{{ $item->pic }}</td>
                    <td style="font-size:12px;color:#64748b;white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="action-btn action-btn-warning"
                                    onclick="openModalEdit({{ $item->id }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="action-btn action-btn-danger"
                                    onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->barang) }}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-boxes-stacked" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Tidak ada data penerimaan barang
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($items->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }}
            dari {{ $items->total() }} data
        </div>
        <div class="pagination">
            @if($items->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $items->previousPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            @foreach($items->getUrlRange(
                max(1, $items->currentPage()-2),
                min($items->lastPage(), $items->currentPage()+2)
            ) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $items->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($items->hasMorePages())
                <a href="{{ $items->nextPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ============================= --}}
{{-- MODAL: CREATE (MULTI ITEM)    --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalCreate">
    <div class="modal-box" style="max-width:720px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-boxes-stacked" style="color:#0b4614;margin-right:8px;"></i>Tambah Penerimaan Barang
                </div>
                <div class="modal-subtitle">Bisa input beberapa item sekaligus</div>
            </div>
            <button class="modal-close" onclick="closeModalCreate()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formCreate" onsubmit="submitCreate(event)">
                @csrf

                {{-- SUPPLIER & PIC --}}
                <div class="k-form-grid" style="grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                    <div class="k-form-group" style="margin-bottom:0;">
                        <label class="k-form-label">Supplier <span class="required">*</span></label>
                        <div class="supplier-select-wrap" id="cSupplierWrap">
                            <div class="supplier-select-display" id="cSupplierDisplay"
                                 onclick="toggleSupplierDropdown('c')" tabindex="0">
                                <span id="cSupplierLabel" style="color:#94a3b8;">Pilih supplier...</span>
                                <i class="fa-solid fa-chevron-down" style="font-size:11px;color:#94a3b8;"></i>
                            </div>
                            <div class="supplier-dropdown" id="cSupplierDropdown" style="display:none;">
                                <div class="supplier-search-wrap">
                                    <i class="fa-solid fa-magnifying-glass" style="font-size:11px;color:#94a3b8;"></i>
                                    <input type="text" class="supplier-search-input" id="cSupplierSearch"
                                           placeholder="Cari supplier..." oninput="filterSupplier('c', this.value)">
                                </div>
                                <div class="supplier-options" id="cSupplierOptions">
                                    @foreach($suppliers as $sup)
                                    <div class="supplier-option" data-id="{{ $sup->id }}" data-nama="{{ $sup->nama }}"
                                         onclick="selectSupplier('c', {{ $sup->id }}, '{{ addslashes($sup->nama) }}')">
                                        {{ $sup->nama }}
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="supplier_id" id="create_supplier_id">
                        <div class="k-form-error" id="cerr_supplier_id"></div>
                    </div>
                    <div class="k-form-group" style="margin-bottom:0;">
                        <label class="k-form-label">PIC <span class="required">*</span></label>
                        <input type="text" name="pic" id="create_pic" class="k-form-input"
                               placeholder="Nama penanggung jawab" maxlength="150">
                        <div class="k-form-error" id="cerr_pic"></div>
                    </div>
                </div>

                {{-- HEADER KOLOM --}}
                <div style="display:grid;grid-template-columns:1fr 150px 90px 120px 32px;
                            gap:8px;padding:0 0 6px;border-bottom:1.5px solid #e2e8f0;
                            font-size:10.5px;font-weight:700;color:#94a3b8;letter-spacing:.4px;">
                    <div>NAMA BARANG</div>
                    <div style="text-align:right;">HARGA (Rp)</div>
                    <div style="text-align:right;">QTY</div>
                    <div style="text-align:right;">TOTAL</div>
                    <div></div>
                </div>

                {{-- ITEM ROWS --}}
                <div id="itemRows" style="max-height:320px;overflow-y:auto;"></div>

                {{-- GRAND TOTAL + TAMBAH --}}
                <div style="display:flex;align-items:center;justify-content:space-between;
                            padding:10px 0 0;border-top:1.5px solid #e2e8f0;margin-top:4px;">
                    <button type="button" onclick="addItemRow()"
                        style="display:flex;align-items:center;gap:6px;padding:7px 14px;
                               border-radius:8px;border:1.5px dashed #3d7a47;background:#f0fdf4;
                               color:#0b4614;font-size:12px;font-weight:700;cursor:pointer;">
                        <i class="fa-solid fa-plus"></i> Tambah Item
                    </button>
                    <div style="text-align:right;">
                        <div style="font-size:10px;color:#94a3b8;margin-bottom:2px;letter-spacing:.3px;">GRAND TOTAL</div>
                        <div id="grandTotal" style="font-size:17px;font-weight:800;color:#0b4614;">Rp 0</div>
                    </div>
                </div>

                <div class="k-form-actions" style="margin-top:16px;">
                    <button type="button" class="btn btn-outline" onclick="closeModalCreate()">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitCreate">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL: EDIT                   --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color:#f59e0b;margin-right:8px;"></i>Edit Penerimaan Barang
                </div>
                <div class="modal-subtitle" id="editModalSub">Memuat data...</div>
            </div>
            <button class="modal-close" onclick="closeModalEdit()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formEdit" onsubmit="submitEdit(event)">
                @csrf
                <input type="hidden" id="edit_id">
                <div class="k-form-grid" style="grid-template-columns:1fr;">

                    <div class="k-form-group">
                        <label class="k-form-label">Supplier <span class="required">*</span></label>
                        <div class="supplier-select-wrap" id="eSupplierWrap">
                            <div class="supplier-select-display" id="eSupplierDisplay"
                                 onclick="toggleSupplierDropdown('e')" tabindex="0">
                                <span id="eSupplierLabel" style="color:#94a3b8;">Pilih supplier...</span>
                                <i class="fa-solid fa-chevron-down" style="font-size:11px;color:#94a3b8;"></i>
                            </div>
                            <div class="supplier-dropdown" id="eSupplierDropdown" style="display:none;">
                                <div class="supplier-search-wrap">
                                    <i class="fa-solid fa-magnifying-glass" style="font-size:11px;color:#94a3b8;"></i>
                                    <input type="text" class="supplier-search-input" id="eSupplierSearch"
                                           placeholder="Cari supplier..." oninput="filterSupplier('e', this.value)">
                                </div>
                                <div class="supplier-options" id="eSupplierOptions">
                                    @foreach($suppliers as $sup)
                                    <div class="supplier-option" data-id="{{ $sup->id }}" data-nama="{{ $sup->nama }}"
                                         onclick="selectSupplier('e', {{ $sup->id }}, '{{ addslashes($sup->nama) }}')">
                                        {{ $sup->nama }}
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="supplier_id" id="edit_supplier_id">
                        <div class="k-form-error" id="eerr_supplier_id"></div>
                    </div>

                    <div class="k-form-group">
                        <label class="k-form-label">Nama Barang <span class="required">*</span></label>
                        <input type="text" name="barang" id="edit_barang" class="k-form-input"
                               required maxlength="200">
                        <div class="k-form-error" id="eerr_barang"></div>
                    </div>

                    <div class="k-form-grid" style="grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="k-form-group">
                            <label class="k-form-label">Harga (Rp) <span class="required">*</span></label>
                            <input type="text" id="edit_harga_display" class="k-form-input"
                                   placeholder="0" autocomplete="off"
                                   oninput="formatHargaInput(this, 'edit_harga')">
                            <input type="hidden" name="harga" id="edit_harga">
                            <div class="k-form-error" id="eerr_harga"></div>
                        </div>
                        <div class="k-form-group">
                            <label class="k-form-label">Qty <span class="required">*</span></label>
                            <input type="number" name="qty" id="edit_qty" class="k-form-input"
                                   min="0.01" step="0.01" required
                                   oninput="calcTotal('e')">
                            <div class="k-form-error" id="eerr_qty"></div>
                        </div>
                    </div>

                    <div class="k-form-group">
                        <label class="k-form-label">Total</label>
                        <div id="eTotalDisplay" style="padding:9px 14px;border-radius:8px;background:#f1f5f9;
                            border:1px solid #e2e8f0;font-size:14px;font-weight:700;color:#0b4614;">
                            Rp 0
                        </div>
                    </div>

                    <div class="k-form-group">
                        <label class="k-form-label">PIC <span class="required">*</span></label>
                        <input type="text" name="pic" id="edit_pic" class="k-form-input"
                               required maxlength="150">
                        <div class="k-form-error" id="eerr_pic"></div>
                    </div>

                </div>
                <div class="k-form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModalEdit()">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEdit">
                        <i class="fa-solid fa-floppy-disk"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL: DELETE CONFIRM         --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalDelete">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;margin-right:8px;"></i>Hapus Data
                </div>
                <div class="modal-subtitle">Tindakan ini tidak bisa dibatalkan</div>
            </div>
            <button class="modal-close" onclick="closeModalDelete()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <p style="color:#475569;font-size:14px;margin-bottom:20px;">
                Yakin ingin menghapus data barang
                <strong id="deleteItemName" style="color:#1e293b;"></strong>?
                Data yang sudah dihapus tidak dapat dikembalikan.
            </p>
            <div class="k-form-actions">
                <button type="button" class="btn btn-outline" onclick="closeModalDelete()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button type="button" class="btn k-btn-danger" id="btnConfirmDelete" onclick="doDelete()">
                    <i class="fa-solid fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.supplier-select-wrap { position:relative; }
.supplier-select-display {
    display:flex;align-items:center;justify-content:space-between;
    padding:9px 14px;border:1px solid #e2e8f0;border-radius:8px;
    background:#fff;cursor:pointer;font-size:13.5px;color:#1e293b;
    transition:border-color .15s,box-shadow .15s;user-select:none;
}
.supplier-select-display:focus,
.supplier-select-display.open {
    border-color:#0b4614;box-shadow:0 0 0 3px rgba(11,70,20,.08);outline:none;
}
.supplier-dropdown {
    position:absolute;top:calc(100% + 4px);left:0;right:0;
    background:#fff;border:1px solid #e2e8f0;border-radius:10px;
    box-shadow:0 8px 24px rgba(0,0,0,.10);z-index:999;overflow:hidden;
}
.supplier-search-wrap {
    display:flex;align-items:center;gap:8px;
    padding:10px 12px;border-bottom:1px solid #f1f5f9;
}
.supplier-search-input {
    border:none;outline:none;font-size:13px;width:100%;
    color:#1e293b;background:transparent;
}
.supplier-options { max-height:200px;overflow-y:auto; }
.supplier-option {
    padding:9px 14px;font-size:13px;color:#1e293b;
    cursor:pointer;transition:background .1s;
}
.supplier-option:hover  { background:#f0fdf4;color:#0b4614; }
.supplier-option.selected { background:#dcfce7;color:#0b4614;font-weight:600; }
.supplier-option.hidden { display:none; }

/* item row inputs */
.item-row input.k-form-input {
    margin-bottom:0;
    padding:7px 10px;
    font-size:13px;
}
.item-row { transition:background .2s; }
</style>
@endpush

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
let rowIndex = 0;

// ── FORMAT HARGA (edit modal single) ──────
function formatHargaInput(el, hiddenId) {
    const cursorPos = el.selectionStart;
    const prevLen   = el.value.length;
    const raw       = el.value.replace(/\D/g, '');
    const formatted = raw ? parseInt(raw).toLocaleString('id-ID') : '';
    el.value = formatted;
    const newCursor = cursorPos + (el.value.length - prevLen);
    el.setSelectionRange(newCursor, newCursor);
    document.getElementById(hiddenId).value = raw || '';
    calcTotal('e');
}

// ── FORMAT RUPIAH ──────────────────────────
function formatRupiah(num) {
    if (isNaN(num) || num === '') return 'Rp 0';
    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
}

// ── CALC TOTAL (edit modal) ────────────────
function calcTotal(prefix) {
    const harga = parseFloat(document.getElementById('edit_harga').value) || 0;
    const qty   = parseFloat(document.getElementById('edit_qty').value)   || 0;
    document.getElementById('eTotalDisplay').textContent = formatRupiah(harga * qty);
}

// ── TOAST ──────────────────────────────────
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

// ── VALIDATION HELPERS ─────────────────────
function clearErrors(prefix = '') {
    document.querySelectorAll(`[id^="${prefix}err_"]`).forEach(el => el.textContent = '');
    document.querySelectorAll('.k-form-input.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}
function showErrors(errors, prefix = '') {
    Object.keys(errors).forEach(field => {
        const errEl = document.getElementById(`${prefix}err_${field}`);
        const formId = prefix === 'e' ? 'formEdit' : 'formCreate';
        const input  = document.querySelector(`#${formId} [name="${field}"]`);
        if (errEl) errEl.textContent = errors[field][0];
        if (input) input.classList.add('is-invalid');
    });
}

// ── SUPPLIER DROPDOWN ──────────────────────
function toggleSupplierDropdown(prefix) {
    const dropdown = document.getElementById(prefix + 'SupplierDropdown');
    const display  = document.getElementById(prefix + 'SupplierDisplay');
    const isOpen   = dropdown.style.display !== 'none';
    ['c','e'].forEach(p => {
        document.getElementById(p + 'SupplierDropdown').style.display = 'none';
        document.getElementById(p + 'SupplierDisplay').classList.remove('open');
    });
    if (!isOpen) {
        dropdown.style.display = 'block';
        display.classList.add('open');
        const searchInput = document.getElementById(prefix + 'SupplierSearch');
        searchInput.value = '';
        filterSupplier(prefix, '');
        setTimeout(() => searchInput.focus(), 50);
    }
}
function filterSupplier(prefix, q) {
    document.querySelectorAll(`#${prefix}SupplierOptions .supplier-option`).forEach(opt => {
        opt.classList.toggle('hidden', !opt.dataset.nama.toLowerCase().includes(q.toLowerCase()));
    });
}
function selectSupplier(prefix, id, nama) {
    document.getElementById(prefix === 'c' ? 'create_supplier_id' : 'edit_supplier_id').value = id;
    const lbl = document.getElementById(prefix + 'SupplierLabel');
    lbl.textContent = nama;
    lbl.style.color = '#1e293b';
    document.querySelectorAll(`#${prefix}SupplierOptions .supplier-option`).forEach(opt => {
        opt.classList.toggle('selected', opt.dataset.id == id);
    });
    document.getElementById(prefix + 'SupplierDropdown').style.display = 'none';
    document.getElementById(prefix + 'SupplierDisplay').classList.remove('open');
}
document.addEventListener('click', function(e) {
    ['c','e'].forEach(prefix => {
        const wrap = document.getElementById(prefix + 'SupplierWrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById(prefix + 'SupplierDropdown').style.display = 'none';
            document.getElementById(prefix + 'SupplierDisplay').classList.remove('open');
        }
    });
});

// ── ITEM ROWS (create multi) ───────────────
function addItemRow() {
    const idx = rowIndex++;
    const row = document.createElement('div');
    row.className   = 'item-row';
    row.dataset.idx = idx;
    row.style.cssText = 'display:grid;grid-template-columns:1fr 150px 90px 120px 32px;gap:8px;align-items:center;padding:7px 0;border-bottom:1px solid #f1f5f9;';
    row.innerHTML = `
        <input type="text" name="items[${idx}][barang]"
               class="k-form-input" placeholder="Nama barang..." maxlength="200">
        <input type="text" id="harga_display_${idx}"
               class="k-form-input" placeholder="0" autocomplete="off"
               oninput="formatRowHarga(this,${idx})"
               style="text-align:right;">
        <input type="hidden" name="items[${idx}][harga]" id="harga_${idx}">
        <input type="number" name="items[${idx}][qty]" id="qty_${idx}"
               class="k-form-input" placeholder="0" min="0.01" step="0.01"
               oninput="calcRowTotal(${idx})"
               style="text-align:right;">
        <div id="total_${idx}"
             style="text-align:right;font-size:12px;font-weight:700;color:#0b4614;white-space:nowrap;padding-right:2px;">
            Rp 0
        </div>
        <button type="button" onclick="removeItemRow(${idx})"
            style="width:28px;height:28px;border-radius:7px;border:1.5px solid #fee2e2;
                   background:#fff5f5;color:#ef4444;cursor:pointer;
                   display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-xmark" style="font-size:11px;"></i>
        </button>
    `;
    document.getElementById('itemRows').appendChild(row);
    row.querySelector('input[type="text"]').focus();
}

function removeItemRow(idx) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 1) {
        showToast('Minimal 1 item harus ada.', 'error');
        return;
    }
    document.querySelector(`.item-row[data-idx="${idx}"]`)?.remove();
    updateGrandTotal();
}

function formatRowHarga(el, idx) {
    const raw = el.value.replace(/\D/g, '');
    el.value  = raw ? parseInt(raw).toLocaleString('id-ID') : '';
    document.getElementById('harga_' + idx).value = raw || '';
    calcRowTotal(idx);
}

function calcRowTotal(idx) {
    const harga = parseFloat(document.getElementById('harga_' + idx)?.value) || 0;
    const qty   = parseFloat(document.getElementById('qty_'   + idx)?.value) || 0;
    const total = document.getElementById('total_' + idx);
    if (total) total.textContent = formatRupiah(harga * qty);
    updateGrandTotal();
}

function updateGrandTotal() {
    let grand = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const idx   = row.dataset.idx;
        const harga = parseFloat(document.getElementById('harga_' + idx)?.value) || 0;
        const qty   = parseFloat(document.getElementById('qty_'   + idx)?.value) || 0;
        grand += harga * qty;
    });
    document.getElementById('grandTotal').textContent = formatRupiah(grand);
}

// ── MODAL CREATE ───────────────────────────
function openModalCreate() {
    rowIndex = 0;
    document.getElementById('itemRows').innerHTML = '';
    document.getElementById('create_pic').value   = '';
    document.getElementById('grandTotal').textContent = 'Rp 0';
    clearErrors('c');

    const lbl = document.getElementById('cSupplierLabel');
    lbl.textContent = 'Pilih supplier...';
    lbl.style.color = '#94a3b8';
    document.getElementById('create_supplier_id').value = '';
    document.querySelectorAll('#cSupplierOptions .supplier-option').forEach(o => o.classList.remove('selected'));

    document.getElementById('modalCreate').classList.add('show');
    addItemRow();
}
function closeModalCreate() {
    document.getElementById('modalCreate').classList.remove('show');
}
function submitCreate(e) {
    e.preventDefault();
    clearErrors('c');

    const supplierId = document.getElementById('create_supplier_id').value;
    const pic        = document.getElementById('create_pic').value.trim();
    let hasError     = false;

    if (!supplierId) {
        document.getElementById('cerr_supplier_id').textContent = 'Supplier wajib dipilih.';
        hasError = true;
    }
    if (!pic) {
        document.getElementById('cerr_pic').textContent = 'PIC wajib diisi.';
        document.getElementById('create_pic').classList.add('is-invalid');
        hasError = true;
    }

    let rowError = false;
    document.querySelectorAll('.item-row').forEach(row => {
        const idx    = row.dataset.idx;
        const barang = row.querySelector(`[name="items[${idx}][barang]"]`).value.trim();
        const harga  = document.getElementById('harga_' + idx).value;
        const qty    = document.getElementById('qty_'   + idx).value;
        if (!barang || !harga || !qty) {
            rowError = true;
            row.style.background    = '#fff5f5';
            row.style.borderRadius  = '8px';
            setTimeout(() => {
                row.style.background   = '';
                row.style.borderRadius = '';
            }, 1500);
        }
    });

    if (hasError || rowError) {
        if (rowError) showToast('Lengkapi semua kolom pada setiap item.', 'error');
        return;
    }

    const btn = document.getElementById('btnSubmitCreate');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

    fetch('{{ route('penerimaan-barang.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: new FormData(document.getElementById('formCreate')),
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 422) {
            showErrors(body.errors, 'c');
        } else if (status === 200 || status === 201) {
            closeModalCreate();
            showToast(`${body.count} item berhasil ditambahkan!`);
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(body.message ?? 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan Semua';
    });
}

// ── MODAL EDIT ─────────────────────────────
function openModalEdit(id) {
    clearErrors('e');
    document.getElementById('editModalSub').textContent = 'Memuat data...';
    document.getElementById('modalEdit').classList.add('show');

    fetch(`/penerimaan-barang/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const s = data.item ?? data;
        document.getElementById('edit_id').value     = s.id;
        document.getElementById('edit_barang').value = s.barang ?? '';
        document.getElementById('edit_qty').value    = s.qty    ?? '';
        document.getElementById('edit_pic').value    = s.pic    ?? '';
        document.getElementById('editModalSub').textContent = `ID: ${s.id}`;

        const hargaRaw = parseFloat(s.harga) || 0;
        document.getElementById('edit_harga').value         = hargaRaw || '';
        document.getElementById('edit_harga_display').value = hargaRaw
            ? parseInt(hargaRaw).toLocaleString('id-ID') : '';

        const supId   = s.supplier_id;
        const supNama = s.supplier?.nama ?? '';
        document.getElementById('edit_supplier_id').value = supId;
        const lbl = document.getElementById('eSupplierLabel');
        lbl.textContent = supNama || 'Pilih supplier...';
        lbl.style.color = supNama ? '#1e293b' : '#94a3b8';
        document.querySelectorAll('#eSupplierOptions .supplier-option').forEach(opt => {
            opt.classList.toggle('selected', opt.dataset.id == supId);
        });

        calcTotal('e');
    })
    .catch(() => { closeModalEdit(); showToast('Gagal memuat data', 'error'); });
}
function closeModalEdit() {
    document.getElementById('modalEdit').classList.remove('show');
}
function submitEdit(e) {
    e.preventDefault();
    if (!document.getElementById('edit_harga').value) {
        document.getElementById('eerr_harga').textContent = 'Harga wajib diisi.';
        document.getElementById('edit_harga_display').classList.add('is-invalid');
        return;
    }

    const btn = document.getElementById('btnSubmitEdit');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    clearErrors('e');

    const id   = document.getElementById('edit_id').value;
    const data = new FormData(document.getElementById('formEdit'));
    data.append('_method', 'PUT');

    fetch(`/penerimaan-barang/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: data,
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 422) {
            showErrors(body.errors, 'e');
        } else if (status === 200) {
            closeModalEdit();
            showToast('Data berhasil diupdate!');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(body.message ?? 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Update';
    });
}

// ── MODAL DELETE ───────────────────────────
let deleteTargetId = null;
function confirmDelete(id, nama) {
    deleteTargetId = id;
    document.getElementById('deleteItemName').textContent = nama;
    document.getElementById('modalDelete').classList.add('show');
}
function closeModalDelete() {
    deleteTargetId = null;
    document.getElementById('modalDelete').classList.remove('show');
}
function doDelete() {
    if (!deleteTargetId) return;
    const btn = document.getElementById('btnConfirmDelete');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menghapus...';

    fetch(`/penerimaan-barang/${deleteTargetId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 200) {
            closeModalDelete();
            showToast('Data berhasil dihapus!');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(body.message ?? 'Gagal menghapus', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fa-solid fa-trash"></i> Hapus';
    });
}

// ── CLOSE ON OVERLAY / ESC ─────────────────
['modalCreate','modalEdit','modalDelete'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['modalCreate','modalEdit','modalDelete'].forEach(id => {
            document.getElementById(id).classList.remove('show');
        });
    }
});
</script>
@endpush