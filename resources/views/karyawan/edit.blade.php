{{-- resources/views/karyawan/edit.blade.php --}}
{{-- Partial: di-@include ke modalEdit di index.blade.php --}}

<form id="formEdit" onsubmit="submitEdit(event)">
    @csrf
    @method('PUT')
    <input type="hidden" id="edit_id" name="id">

    {{-- ── DATA KARYAWAN ── --}}
    <div class="k-section-label">
        <i class="fa-solid fa-id-card"></i> Data Karyawan
    </div>

    <div class="k-form-grid">
        <div class="k-form-group">
            <label class="k-form-label">NIK <span class="required">*</span></label>
            <input type="text" name="nik" id="edit_nik" class="k-form-input"
                   placeholder="Contoh: 001234" required maxlength="20">
            <div class="k-form-error" id="edit_kerr_nik"></div>
        </div>
        <div class="k-form-group">
            <label class="k-form-label">NIK Login</label>
            <input type="text" name="nik_login" id="edit_nik_login" class="k-form-input"
                   placeholder="NIK untuk login">
            <div class="k-form-error" id="edit_kerr_nik_login"></div>
        </div>
        <div class="k-form-group k-form-col-2">
            <label class="k-form-label">Nama Karyawan <span class="required">*</span></label>
            <input type="text" name="nama" id="edit_nama" class="k-form-input"
                   placeholder="Nama lengkap karyawan" required>
            <div class="k-form-error" id="edit_kerr_nama"></div>
        </div>
        <div class="k-form-group">
            <label class="k-form-label">Departemen <span class="required">*</span></label>
            <input type="text" name="departemen" id="edit_departemen" class="k-form-input"
                   placeholder="Contoh: HRD, IT, Finance" required list="dept-list-edit">
            <datalist id="dept-list-edit">
                @foreach($departemenList as $dept)
                    <option value="{{ $dept }}">
                @endforeach
            </datalist>
            <div class="k-form-error" id="edit_kerr_departemen"></div>
        </div>
        <div class="k-form-group">
            <label class="k-form-label">Status Karyawan <span class="required">*</span></label>
            <select name="keterangan" id="edit_keterangan" class="k-form-input" required>
                <option value="Aktif">Aktif</option>
                <option value="Non-Aktif">Non-Aktif</option>
            </select>
            <div class="k-form-error" id="edit_kerr_keterangan"></div>
        </div>
        <div class="k-form-group">
            <label class="k-form-label">Status Kehadiran</label>
            <select name="status_kehadiran" id="edit_status_kehadiran" class="k-form-input">
                <option value="0">Tidak</option>
                <option value="1">Ya</option>
            </select>
        </div>
        <input type="hidden" name="jumlah_keluarga" id="edit_jumlah_keluarga" value="0">
    </div>

    {{-- ── DATA KELUARGA ── --}}
    <div class="k-section-label" style="margin-top:4px;">
        <i class="fa-solid fa-people-group"></i> Anggota Keluarga
        <span id="editFamilyCount" style="margin-left:4px;font-size:11px;color:#64748b;font-weight:500;text-transform:none;letter-spacing:0;">
            (0 anggota)
        </span>
    </div>

    <div id="editFamilyTableWrap" class="k-family-wrap">
        <table class="k-family-table">
            <thead>
                <tr>
                    <th class="k-fth" style="width:32px;">#</th>
                    <th class="k-fth">Nama <span class="required">*</span></th>
                    <th class="k-fth">Hubungan <span class="required">*</span></th>
                    <th class="k-fth">Jenis Kelamin <span class="required">*</span></th>
                    <th class="k-fth">Tgl. Lahir</th>
                    <th class="k-fth">Ukuran Kaos</th>
                    <th class="k-fth" style="width:40px;"></th>
                </tr>
            </thead>
            <tbody id="editFamilyRows"></tbody>
        </table>
    </div>

    <div id="editFamilyEmpty" class="k-family-empty">
        <i class="fa-solid fa-user-plus" style="font-size:20px;display:block;margin-bottom:6px;opacity:.35;"></i>
        Belum ada anggota keluarga
    </div>

    <button type="button" class="k-btn-add-family" onclick="addEditFamilyRow()">
        <i class="fa-solid fa-plus"></i> Tambah Anggota
    </button>

    {{-- ── ACTIONS ── --}}
    <div class="k-form-actions">
        <button type="button" class="btn btn-outline" onclick="closeModalEdit()">
            <i class="fa-solid fa-xmark"></i> Batal
        </button>
        <button type="submit" class="btn btn-primary" id="btnSubmitEdit">
            <i class="fa-solid fa-floppy-disk"></i> Update
        </button>
    </div>
</form>

<script>
(function () {
    let editRowIdx = 0;

    // ── Build satu row (bisa prefill dari data existing) ──
    function buildEditRow(i, data = {}) {
        const hubunganOpts = ['Karyawan','Karyawati','Istri','Suami','Anak','Saudara']
            .map(v => `<option value="${v}" ${data.hubungan === v ? 'selected' : ''}>${v}</option>`)
            .join('');

        const genderOpts = ['Laki-laki','Perempuan']
            .map(v => `<option value="${v}" ${data.jenis_kelamin === v ? 'selected' : ''}>${v}</option>`)
            .join('');

        const kaosOpts = ['','S','M','L','XL','XXL','XXXL']
            .map(v => `<option value="${v}" ${data.ukuran_kaos === v ? 'selected' : ''}>${v || '–'}</option>`)
            .join('');

        // tanggal lahir: strip jam kalau ada (format ISO)
        const tglVal = data.tanggal_lahir ? data.tanggal_lahir.substring(0, 10) : '';
        const umurVal = data.umur ?? 0;

        // hidden id untuk existing detail (0 = baru)
        const detailId = data.id ?? '';

        return `
            <td class="k-ftd" style="text-align:center;color:#94a3b8;font-size:12px;"></td>
            <td class="k-ftd" style="min-width:155px;">
                <input type="hidden" name="details[${i}][id]" value="${detailId}">
                <input type="text" name="details[${i}][nama_keluarga]" class="k-fc"
                       placeholder="Nama lengkap" value="${escHtml(data.nama_keluarga ?? '')}" required>
            </td>
            <td class="k-ftd" style="min-width:125px;">
                <select name="details[${i}][hubungan]" class="k-fc" required>
                    <option value="">– Pilih –</option>
                    ${hubunganOpts}
                </select>
            </td>
            <td class="k-ftd" style="min-width:115px;">
                <select name="details[${i}][jenis_kelamin]" class="k-fc" required>
                    <option value="">– Pilih –</option>
                    ${genderOpts}
                </select>
            </td>
            <td class="k-ftd" style="min-width:140px;">
                <input type="date" name="details[${i}][tanggal_lahir]" class="k-fc"
                       value="${tglVal}" onchange="calcEditUmur(this, ${i})">
                <input type="hidden" name="details[${i}][umur]" id="edit_umur_${i}" value="${umurVal}">
            </td>
            <td class="k-ftd" style="min-width:95px;">
                <select name="details[${i}][ukuran_kaos]" class="k-fc">
                    ${kaosOpts}
                </select>
            </td>
            <td class="k-ftd" style="text-align:center;">
                <button type="button" class="k-btn-rm" onclick="removeEditFamilyRow(this)">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </td>`;
    }

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Tambah row kosong ──
    window.addEditFamilyRow = function () {
        const tbody = document.getElementById('editFamilyRows');
        const i     = editRowIdx++;
        const tr    = document.createElement('tr');
        tr.innerHTML = buildEditRow(i);
        tbody.appendChild(tr);
        syncEditFamilyUI();
    };

    // ── Load rows dari data existing (dipanggil saat openModalEdit) ──
    window.loadEditFamilyRows = function (details) {
        const tbody = document.getElementById('editFamilyRows');
        tbody.innerHTML = '';
        editRowIdx = 0;

        details.forEach(d => {
            const i  = editRowIdx++;
            const tr = document.createElement('tr');
            tr.innerHTML = buildEditRow(i, d);
            tbody.appendChild(tr);
        });

        syncEditFamilyUI();
    };

    window.removeEditFamilyRow = function (btn) {
        btn.closest('tr').remove();
        reindexEdit();
        syncEditFamilyUI();
    };

    window.calcEditUmur = function (input, idx) {
        const el = document.getElementById(`edit_umur_${idx}`);
        if (!el) return;
        if (!input.value) { el.value = 0; return; }
        const diff = Date.now() - new Date(input.value).getTime();
        el.value = Math.max(0, Math.floor(diff / (365.25 * 24 * 3600 * 1000)));
    };

    function reindexEdit() {
        document.querySelectorAll('#editFamilyRows tr').forEach((tr, i) => {
            tr.cells[0].textContent = i + 1;
        });
    }

    function syncEditFamilyUI() {
        const count = document.getElementById('editFamilyRows').rows.length;
        // reindex nomor urut
        document.querySelectorAll('#editFamilyRows tr').forEach((tr, i) => {
            tr.cells[0].textContent = i + 1;
        });
        document.getElementById('editFamilyCount').textContent       = `(${count} anggota)`;
        document.getElementById('editFamilyTableWrap').style.display = count > 0 ? 'block' : 'none';
        document.getElementById('editFamilyEmpty').style.display     = count > 0 ? 'none'  : 'block';
        const jk = document.getElementById('edit_jumlah_keluarga');
        if (jk) jk.value = count;
    }
})();
</script>