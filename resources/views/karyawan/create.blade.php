{{-- resources/views/karyawan/create.blade.php --}}
{{-- Partial: di-@include ke modalCreate di index.blade.php --}}

<form id="formCreate" onsubmit="submitCreate(event)">
    @csrf

    {{-- ── DATA KARYAWAN ── --}}
    <div class="k-section-label">
        <i class="fa-solid fa-id-card"></i> Data Karyawan
    </div>

    <div class="k-form-grid">
        <div class="k-form-group">
            <label class="k-form-label">NIK <span class="required">*</span></label>
            <input type="text" name="nik" id="create_nik" class="k-form-input"
                   placeholder="Contoh: 001234" required maxlength="20">
            <div class="k-form-error" id="kerr_nik"></div>
        </div>
        <div class="k-form-group">
            <label class="k-form-label">NIK Login</label>
            <input type="text" name="nik_login" id="create_nik_login" class="k-form-input"
                   placeholder="NIK untuk login">
            <div class="k-form-error" id="kerr_nik_login"></div>
        </div>
        <div class="k-form-group k-form-col-2">
            <label class="k-form-label">Nama Karyawan <span class="required">*</span></label>
            <input type="text" name="nama" id="create_nama" class="k-form-input"
                   placeholder="Nama lengkap karyawan" required>
            <div class="k-form-error" id="kerr_nama"></div>
        </div>
        <div class="k-form-group">
            <label class="k-form-label">Departemen <span class="required">*</span></label>
            <input type="text" name="departemen" id="create_departemen" class="k-form-input"
                   placeholder="Contoh: HRD, IT, Finance" required list="dept-list-create">
            <datalist id="dept-list-create">
                @foreach($departemenList as $dept)
                    <option value="{{ $dept }}">
                @endforeach
            </datalist>
            <div class="k-form-error" id="kerr_departemen"></div>
        </div>
        <div class="k-form-group">
            <label class="k-form-label">Status Karyawan <span class="required">*</span></label>
            <select name="keterangan" id="create_keterangan" class="k-form-input" required>
                <option value="Aktif" selected>Aktif</option>
                <option value="Non-Aktif">Non-Aktif</option>
            </select>
            <div class="k-form-error" id="kerr_keterangan"></div>
        </div>
        <div class="k-form-group">
            <label class="k-form-label">Status Kehadiran</label>
            <select name="status_kehadiran" id="create_status_kehadiran" class="k-form-input">
                <option value="0" selected>Tidak</option>
                <option value="1">Ya</option>
            </select>
        </div>
        {{-- hidden, dihitung otomatis dari jumlah row keluarga --}}
        <input type="hidden" name="jumlah_keluarga" id="create_jumlah_keluarga" value="0">
    </div>

    {{-- ── DATA KELUARGA ── --}}
    <div class="k-section-label" style="margin-top:4px;">
        <i class="fa-solid fa-people-group"></i> Anggota Keluarga
        <span id="familyCount" style="margin-left:4px;font-size:11px;color:#64748b;font-weight:500;text-transform:none;letter-spacing:0;">
            (0 anggota)
        </span>
    </div>

    <div id="familyTableWrap" class="k-family-wrap">
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
            <tbody id="familyRows"></tbody>
        </table>
    </div>

    <div id="familyEmpty" class="k-family-empty">
        <i class="fa-solid fa-user-plus" style="font-size:20px;display:block;margin-bottom:6px;opacity:.35;"></i>
        Belum ada anggota keluarga ditambahkan
    </div>

    <button type="button" class="k-btn-add-family" onclick="addFamilyRow()">
        <i class="fa-solid fa-plus"></i> Tambah Anggota
    </button>

    {{-- ── ACTIONS ── --}}
    <div class="k-form-actions">
        <button type="button" class="btn btn-outline" onclick="closeModalCreate()">
            <i class="fa-solid fa-xmark"></i> Batal
        </button>
        <button type="submit" class="btn btn-primary" id="btnSubmitCreate">
            <i class="fa-solid fa-floppy-disk"></i> Simpan
        </button>
    </div>
</form>

<script>
(function () {
    let rowIdx = 0;

    window.addFamilyRow = function () {
        const tbody = document.getElementById('familyRows');
        const i     = rowIdx++;
        const tr    = document.createElement('tr');

        tr.innerHTML = `
            <td class="k-ftd" style="text-align:center;color:#94a3b8;font-size:12px;">
                ${tbody.rows.length + 1}
            </td>
            <td class="k-ftd" style="min-width:155px;">
                <input type="text" name="details[${i}][nama_keluarga]" class="k-fc"
                       placeholder="Nama lengkap" required>
            </td>
            <td class="k-ftd" style="min-width:125px;">
                <select name="details[${i}][hubungan]" class="k-fc" required>
                    <option value="">– Pilih –</option>
                    <option value="Karyawan">Karyawan</option>
                    <option value="Karyawati">Karyawati</option>
                    <option value="Istri">Istri</option>
                    <option value="Suami">Suami</option>
                    <option value="Anak">Anak</option>
                    <option value="Saudara">Saudara</option>
                </select>
            </td>
            <td class="k-ftd" style="min-width:115px;">
                <select name="details[${i}][jenis_kelamin]" class="k-fc" required>
                    <option value="">– Pilih –</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </td>
            <td class="k-ftd" style="min-width:140px;">
                <input type="date" name="details[${i}][tanggal_lahir]" class="k-fc"
                       onchange="calcUmur(this, ${i})">
                <input type="hidden" name="details[${i}][umur]" id="umur_${i}" value="0">
            </td>
            <td class="k-ftd" style="min-width:95px;">
                <select name="details[${i}][ukuran_kaos]" class="k-fc">
                    <option value="">–</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="XXL">XXL</option>
                    <option value="XXXL">XXXL</option>
                </select>
            </td>
            <td class="k-ftd" style="text-align:center;">
                <button type="button" class="k-btn-rm" onclick="removeFamilyRow(this)">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </td>`;

        tbody.appendChild(tr);
        syncFamilyUI();
    };

    window.removeFamilyRow = function (btn) {
        btn.closest('tr').remove();
        reindex();
        syncFamilyUI();
    };

    window.calcUmur = function (input, idx) {
        const el = document.getElementById(`umur_${idx}`);
        if (!el) return;
        if (!input.value) { el.value = 0; return; }
        const diff = Date.now() - new Date(input.value).getTime();
        el.value = Math.max(0, Math.floor(diff / (365.25 * 24 * 3600 * 1000)));
    };

    function reindex() {
        document.querySelectorAll('#familyRows tr').forEach((tr, i) => {
            tr.cells[0].textContent = i + 1;
        });
    }

    function syncFamilyUI() {
        const count = document.getElementById('familyRows').rows.length;
        document.getElementById('familyCount').textContent       = `(${count} anggota)`;
        document.getElementById('familyTableWrap').style.display = count > 0 ? 'block' : 'none';
        document.getElementById('familyEmpty').style.display     = count > 0 ? 'none'  : 'block';
        const jk = document.getElementById('create_jumlah_keluarga');
        if (jk) jk.value = count;
    }

    // dipanggil oleh openModalCreate()
    window._resetFamilyRows = function () {
        document.getElementById('familyRows').innerHTML = '';
        rowIdx = 0;
        syncFamilyUI();
    };
})();
</script>