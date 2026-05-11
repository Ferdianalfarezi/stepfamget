@extends('layouts.app')
@section('title', 'Voting Tempat')
@section('page-title', 'Voting Tempat Gathering')

@section('content')

{{-- Stats --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
  <div style="background:#1a3320;border-radius:14px;padding:16px;display:flex;align-items:center;gap:14px;">
    <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;font-size:20px;color:#7ec88a;flex-shrink:0;">
      <i class="fa-solid fa-box-ballot"></i>
    </div>
    <div>
      <div style="font-size:11px;color:rgba(255,255,255,.4);letter-spacing:1px;">TOTAL SUARA</div>
      <div style="font-size:28px;font-weight:800;color:#fff;line-height:1.1;">{{ $totalVotes }}</div>
    </div>
  </div>
  <div style="background:#fff;border:1px solid #e8ede8;border-radius:14px;padding:16px;display:flex;align-items:center;gap:14px;">
    <div style="width:48px;height:48px;border-radius:12px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;font-size:20px;color:#3d7a47;flex-shrink:0;">
      <i class="fa-solid fa-trophy"></i>
    </div>
    <div>
      <div style="font-size:11px;color:#999;letter-spacing:1px;">TERBANYAK</div>
      <div style="font-size:14px;font-weight:800;color:#111;line-height:1.2;">{{ $tempats->first()?->nama ?? '-' }}</div>
    </div>
  </div>
</div>

{{-- Kandidat --}}
<div class="card">
  <div class="card-header">
    <div class="card-title">
      <i class="fa-solid fa-map-location-dot"></i>
      Kandidat Tempat
    </div>
    <div style="display:flex;gap:8px;">
      <button onclick="confirmReset()" class="btn btn-sm" style="background:#ffebee;color:#c62828;border:1px solid #ffcdd2;">
        <i class="fa-solid fa-rotate-left"></i> Reset Votes
      </button>
      <button onclick="openModal()" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-plus"></i> Tambah
      </button>
    </div>
  </div>

  <div style="padding:16px;display:flex;flex-direction:column;gap:8px;">
    @forelse($tempats as $t)
    @php $pct = $totalVotes > 0 ? round($t->votes_count / $totalVotes * 100) : 0; @endphp
    <div style="background:#fafafa;border:1px solid #e8ede8;border-radius:12px;padding:14px 16px;">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
        <div style="width:32px;height:32px;border-radius:8px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#3d7a47;flex-shrink:0;">
          {{ $loop->iteration }}
        </div>
        <div style="flex:1;">
          <div style="font-size:14px;font-weight:700;color:#111;">{{ $t->nama }}</div>
          @if($t->lokasi)
          <div style="font-size:11px;color:#999;margin-top:1px;"><i class="fa-solid fa-location-dot" style="color:#3d7a47;margin-right:3px;font-size:10px;"></i>{{ $t->lokasi }}</div>
          @endif
          @if($t->deskripsi)
          <div style="font-size:11px;color:#777;margin-top:2px;">{{ $t->deskripsi }}</div>
          @endif
        </div>
        <div style="text-align:right;flex-shrink:0;">
          <div style="font-size:20px;font-weight:800;color:#111;">{{ $t->votes_count }}</div>
          <div style="font-size:10px;color:#999;">suara · {{ $pct }}%</div>
        </div>
        <div style="display:flex;gap:6px;flex-shrink:0;">
          <button onclick="openModal({{ $t->id }}, '{{ addslashes($t->nama) }}', '{{ addslashes($t->lokasi) }}', '{{ addslashes($t->deskripsi) }}')"
                  style="background:#e3f2fd;color:#1565c0;border:none;border-radius:8px;padding:7px 10px;cursor:pointer;font-size:12px;">
            <i class="fa-solid fa-pen"></i>
          </button>
          <button onclick="hapusTempat({{ $t->id }})"
                  style="background:#ffebee;color:#c62828;border:none;border-radius:8px;padding:7px 10px;cursor:pointer;font-size:12px;">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </div>
      <div style="background:#e8ede8;border-radius:6px;height:6px;overflow:hidden;">
        <div style="height:100%;border-radius:6px;transition:width .5s;
             background:{{ $loop->first ? '#3d7a47' : ($loop->iteration == 2 ? '#7ec88a' : '#bbb') }};
             width:{{ $pct }}%;"></div>
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:40px;color:#ccc;font-size:12px;">
      <i class="fa-solid fa-map-location-dot" style="font-size:36px;display:block;margin-bottom:10px;"></i>
      Belum ada kandidat tempat. Klik "Tambah" untuk memulai.
    </div>
    @endforelse
  </div>
</div>

{{-- MODAL --}}
<div id="modal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:20px;">
  <div style="background:#fff;border-radius:20px;width:100%;max-width:440px;padding:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
      <div style="font-size:16px;font-weight:800;color:#111;" id="modalTitle">Tambah Tempat</div>
      <button onclick="closeModal()" style="background:#f5f5f5;border:none;border-radius:8px;width:32px;height:32px;cursor:pointer;font-size:14px;">✕</button>
    </div>
    <div style="margin-bottom:14px;">
      <label style="font-size:11.5px;font-weight:600;color:#555;display:block;margin-bottom:6px;">NAMA TEMPAT *</label>
      <input type="text" id="inputNama" placeholder="cth. Villa Puncak Indah"
             style="width:100%;border:1px solid #e0e0e0;border-radius:10px;padding:11px 14px;font-size:14px;font-family:inherit;outline:none;">
    </div>
    <div style="margin-bottom:14px;">
      <label style="font-size:11.5px;font-weight:600;color:#555;display:block;margin-bottom:6px;">LOKASI</label>
      <input type="text" id="inputLokasi" placeholder="cth. Puncak, Bogor"
             style="width:100%;border:1px solid #e0e0e0;border-radius:10px;padding:11px 14px;font-size:14px;font-family:inherit;outline:none;">
    </div>
    <div style="margin-bottom:20px;">
      <label style="font-size:11.5px;font-weight:600;color:#555;display:block;margin-bottom:6px;">DESKRIPSI</label>
      <textarea id="inputDeskripsi" placeholder="Fasilitas, kapasitas, dll..." rows="3"
                style="width:100%;border:1px solid #e0e0e0;border-radius:10px;padding:11px 14px;font-size:14px;font-family:inherit;outline:none;resize:none;"></textarea>
    </div>
    <div style="display:flex;gap:10px;">
      <button onclick="closeModal()" style="flex:1;padding:12px;border:1px solid #e0e0e0;border-radius:10px;background:#fff;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;">Batal</button>
      <button onclick="saveTempat()" style="flex:1;padding:12px;border:none;border-radius:10px;background:#1a3320;color:#fff;font-family:inherit;font-size:14px;font-weight:700;cursor:pointer;">Simpan</button>
    </div>
  </div>
</div>

<script>
let editId = null;

function openModal(id = null, nama = '', lokasi = '', deskripsi = '') {
  editId = id;
  document.getElementById('modalTitle').textContent = id ? 'Edit Tempat' : 'Tambah Tempat';
  document.getElementById('inputNama').value      = nama;
  document.getElementById('inputLokasi').value    = lokasi;
  document.getElementById('inputDeskripsi').value = deskripsi;
  document.getElementById('modal').style.display  = 'flex';
}

function closeModal() {
  document.getElementById('modal').style.display = 'none';
  editId = null;
}

function saveTempat() {
  const nama = document.getElementById('inputNama').value.trim();
  if (!nama) { alert('Nama tempat wajib diisi'); return; }

  const url    = editId ? `/voting/${editId}` : `/voting`;
  const method = editId ? 'PUT' : 'POST';

  fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({
      nama,
      lokasi:    document.getElementById('inputLokasi').value,
      deskripsi: document.getElementById('inputDeskripsi').value,
    }),
  })
  .then(r => r.json())
  .then(() => { closeModal(); location.reload(); })
  .catch(() => alert('Gagal menyimpan'));
}

function hapusTempat(id) {
  if (!confirm('Hapus tempat ini? Semua votes juga akan terhapus.')) return;
  fetch(`/voting/${id}`, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
  })
  .then(r => r.json())
  .then(() => location.reload())
  .catch(() => alert('Gagal menghapus'));
}

function confirmReset() {
  if (!confirm('Reset semua votes? Tindakan ini tidak bisa dibatalkan.')) return;
  fetch('/voting/reset', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
  })
  .then(r => r.json())
  .then(() => location.reload())
  .catch(() => alert('Gagal reset'));
}

document.getElementById('modal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
</script>

@endsection