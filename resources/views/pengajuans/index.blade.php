@extends('layouts.app')
@section('title', 'Pengajuan Anggota Keluarga')
@section('page-title', 'Pengajuan Anggota')

@section('content')

{{-- ── STAT CARDS ─────────────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px;">
    @php
        $statsConfig = [
            ['label'=>'Total',     'key'=>'all',      'icon'=>'fa-list',           'bg'=>'#f1f5f9','color'=>'#475569'],
            ['label'=>'Menunggu',  'key'=>'pending',  'icon'=>'fa-hourglass-half', 'bg'=>'#fff8e1','color'=>'#f59e0b'],
            ['label'=>'Disetujui','key'=>'approved', 'icon'=>'fa-circle-check',   'bg'=>'#e8f5e9','color'=>'#16a34a'],
            ['label'=>'Ditolak',  'key'=>'rejected', 'icon'=>'fa-circle-xmark',   'bg'=>'#fce4ec','color'=>'#ef4444'],
        ];
    @endphp
    @foreach($statsConfig as $sc)
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:16px 20px;display:flex;align-items:center;gap:14px;">
            <div style="width:44px;height:44px;border-radius:12px;background:{{ $sc['bg'] }};
                        color:{{ $sc['color'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid {{ $sc['icon'] }}" style="font-size:16px;"></i>
            </div>
            <div>
                {{-- ✅ id ditambah supaya bisa diupdate JS tanpa refresh --}}
                <div id="count-{{ $sc['key'] }}" style="font-size:24px;font-weight:800;color:#111;line-height:1;">{{ $counts[$sc['key']] }}</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">{{ $sc['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── FILTER BAR ──────────────────────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('pengajuan.index') }}">
            <div class="filters">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama karyawan atau anggota..."
                           value="{{ request('search') }}" style="width:280px;">
                </div>

                <select name="status" class="form-control"
                        style="width:160px;border-radius:10px;border:1.5px solid #e2e8f0;padding:8px 12px;font-size:13px;">
                    <option value="">Semua Status</option>
                    <option value="pending"  {{ request('status')==='pending'  ? 'selected':'' }}>⏳ Menunggu</option>
                    <option value="approved" {{ request('status')==='approved' ? 'selected':'' }}>✅ Disetujui</option>
                    <option value="rejected" {{ request('status')==='rejected' ? 'selected':'' }}>❌ Ditolak</option>
                </select>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search','status']))
                    <a href="{{ route('pengajuan.index') }}" class="btn btn-outline">
                        <i class="fa-solid fa-xmark"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── TABLE CARD ──────────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-user-plus" style="color:#0b4614;margin-right:6px;"></i>
                Pengajuan Anggota Keluarga
            </div>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:45px;">#</th>
                    <th>Karyawan</th>
                    <th>Anggota Diajukan</th>
                    <th>Detail</th>
                    <th>Status</th>
                    <th>Diajukan</th>
                    <th>Di Setujui</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $p)
                <tr id="row-{{ $p->id }}">

                    {{-- No --}}
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($pengajuans->currentPage() - 1) * $pengajuans->perPage() }}
                    </td>

                    {{-- Karyawan --}}
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:10px;
                                        background:#e8f5e9;color:#2e7d32;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:12px;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($p->karyawan->nama ?? '-', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13.5px;">{{ $p->karyawan->nama ?? '-' }}</div>
                                <div style="font-size:12px;color:#94a3b8;">{{ $p->nik }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Anggota diajukan --}}
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:10px;
                                        background:#fff8e1;color:#f57f17;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:12px;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($p->nama_keluarga, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13.5px;">{{ $p->nama_keluarga }}</div>
                                <div style="font-size:12px;color:#94a3b8;">{{ $p->hubungan }} · {{ $p->jenis_kelamin }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Detail --}}
                    <td>
                        <div style="font-size:12px;color:#64748b;display:flex;flex-direction:column;gap:3px;">
                            @if($p->umur)
                                <span><i class="fa-solid fa-user" style="color:#3d7a47;width:14px;"></i> {{ $p->umur }} tahun</span>
                            @endif
                            @if($p->tanggal_lahir)
                                <span><i class="fa-solid fa-cake-candles" style="color:#3d7a47;width:14px;"></i> {{ $p->tanggal_lahir->format('d M Y') }}</span>
                            @endif
                            @if($p->ukuran_kaos)
                                <span><i class="fa-solid fa-shirt" style="color:#3d7a47;width:14px;"></i> Kaos {{ $p->ukuran_kaos }}</span>
                            @endif
                            @if(!$p->umur && !$p->tanggal_lahir && !$p->ukuran_kaos)
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </div>
                    </td>

                    {{-- Status --}}
                    <td>
                        @php
                            $statusCfg = [
                                'pending'  => ['bg'=>'#fff8e1','color'=>'#f59e0b','label'=>'⏳ Menunggu'],
                                'approved' => ['bg'=>'#e8f5e9','color'=>'#16a34a','label'=>'✅ Disetujui'],
                                'rejected' => ['bg'=>'#fce4ec','color'=>'#ef4444','label'=>'❌ Ditolak'],
                            ];
                            $sc = $statusCfg[$p->status] ?? ['bg'=>'#f1f5f9','color'=>'#64748b','label'=>$p->status];
                        @endphp
                        <span style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};
                                     border-radius:20px;padding:4px 10px;
                                     font-size:11.5px;font-weight:700;white-space:nowrap;">
                            {{ $sc['label'] }}
                        </span>
                        @if($p->isRejected() && $p->alasan_tolak)
                            <div style="font-size:11px;color:#ef4444;margin-top:4px;font-style:italic;max-width:160px;">
                                "{{ Str::limit($p->alasan_tolak, 40) }}"
                            </div>
                        @endif
                    </td>

                    {{-- Diajukan --}}
                    <td style="font-size:13px;color:#64748b;">
                        <i class="fa-regular fa-clock" style="margin-right:4px;"></i>
                        {{ $p->created_at->format('d M Y, H:i') }}
                    </td>

                    {{-- Aksi --}}
                    <td>
                        @if($p->isPending())
                            <div style="display:flex;gap:6px;">
                                <button onclick="approveAction({{ $p->id }}, '{{ addslashes($p->nama_keluarga) }}')"
                                    class="btn"
                                    style="background:#e8f5e9;color:#16a34a;border:none;
                                           font-size:12px;font-weight:700;padding:6px 12px;border-radius:8px;">
                                    <i class="fa-solid fa-check"></i> Setujui
                                </button>
                                <button onclick="openRejectModal({{ $p->id }}, '{{ addslashes($p->nama_keluarga) }}')"
                                    class="btn"
                                    style="background:#fce4ec;color:#ef4444;border:none;
                                           font-size:12px;font-weight:700;padding:6px 12px;border-radius:8px;">
                                    <i class="fa-solid fa-times"></i> Tolak
                                </button>
                            </div>
                        @else
                            <span style="font-size:11.5px;color:#343434;">
                                {{ $p->reviewed_at?->format('d M Y') ?? '—' }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:#94a3b8;">
                        <i class="fa-solid fa-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:.4;"></i>
                        Belum ada pengajuan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($pengajuans->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $pengajuans->firstItem() }}–{{ $pengajuans->lastItem() }} dari {{ $pengajuans->total() }} data
        </div>
        <div class="pagination">
            @if($pengajuans->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $pengajuans->previousPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            @foreach($pengajuans->getUrlRange(max(1, $pengajuans->currentPage()-2), min($pengajuans->lastPage(), $pengajuans->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $pengajuans->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($pengajuans->hasMorePages())
                <a href="{{ $pengajuans->nextPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ── MODAL APPROVE ───────────────────────────────────────────────────────── --}}
<div id="modalApprove"
     style="display:none;position:fixed;inset:0;z-index:1050;
            background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:400px;
                margin:16px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2);text-align:center;">

        <div style="width:56px;height:56px;border-radius:16px;background:#e8f5e9;
                    color:#16a34a;display:flex;align-items:center;justify-content:center;
                    margin:0 auto 16px;font-size:22px;">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <div style="font-size:16px;font-weight:800;color:#111;margin-bottom:6px;">Setujui Pengajuan?</div>
        <p style="font-size:13px;color:#64748b;margin-bottom:24px;line-height:1.6;">
            Pengajuan untuk <strong id="approveNama"></strong> akan disetujui.<br>
            Data otomatis ditambahkan ke daftar anggota keluarga.
        </p>

        <div style="display:flex;gap:8px;">
            <button onclick="closeApproveModal()" class="btn btn-outline" style="flex:1;">
                Batal
            </button>
            <button onclick="confirmApprove()" id="btnConfirmApprove"
                class="btn"
                style="flex:1;background:#16a34a;color:#fff;border:none;font-weight:700;">
                <i class="fa-solid fa-check"></i> Setujui
            </button>
        </div>
    </div>
</div>

{{-- ── MODAL REJECT ────────────────────────────────────────────────────────── --}}
<div id="modalReject"
     style="display:none;position:fixed;inset:0;z-index:1050;
            background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:440px;
                margin:16px;padding:24px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="font-size:16px;font-weight:800;color:#111;margin-bottom:4px;">Tolak Pengajuan</div>
        <p style="font-size:13px;color:#64748b;margin-bottom:16px;">
            Pengajuan untuk <strong id="rejectNama"></strong> akan ditolak.
        </p>

        <label style="font-size:12px;font-weight:700;color:#475569;display:block;margin-bottom:6px;">
            Alasan Penolakan <span style="font-weight:400;color:#94a3b8;">(opsional)</span>
        </label>
        <textarea id="rejectAlasan" rows="3"
            placeholder="Contoh: Data tidak lengkap, mohon dilengkapi terlebih dahulu."
            style="width:100%;padding:10px 14px;border-radius:10px;border:1.5px solid #e2e8f0;
                   font-size:13px;resize:none;box-sizing:border-box;outline:none;font-family:inherit;"
            onfocus="this.style.borderColor='#ef4444'" onblur="this.style.borderColor='#e2e8f0'"></textarea>

        <div style="display:flex;gap:8px;margin-top:16px;">
            <button onclick="closeRejectModal()" class="btn btn-outline" style="flex:1;">
                Batal
            </button>
            <button onclick="confirmReject()" id="btnConfirmReject"
                class="btn"
                style="flex:1;background:#ef4444;color:#fff;border:none;font-weight:700;">
                <i class="fa-solid fa-times"></i> Tolak Pengajuan
            </button>
        </div>
    </div>
</div>

<script>
const PENGAJUAN_BASE = "{{ url('/pengajuan') }}";
const CSRF           = "{{ csrf_token() }}";
let activeRejectId   = null;
let activeApproveId  = null;

// ── Update summary cards tanpa refresh ───────────────────────────────────────
function updateCounts(type) {
    const elPending  = document.getElementById('count-pending');
    const elApproved = document.getElementById('count-approved');
    const elRejected = document.getElementById('count-rejected');

    // pending selalu -1 setiap ada aksi approve / reject
    if (elPending) {
        elPending.textContent = Math.max(0, parseInt(elPending.textContent) - 1);
    }

    if (type === 'approve' && elApproved) {
        elApproved.textContent = parseInt(elApproved.textContent) + 1;
    }

    if (type === 'reject' && elRejected) {
        elRejected.textContent = parseInt(elRejected.textContent) + 1;
    }

    // count-all tidak berubah (total tetap sama)
}

// ── Approve modal ─────────────────────────────────────────────────────────────
function approveAction(id, nama) {
    activeApproveId = id;
    document.getElementById('approveNama').textContent = nama;
    document.getElementById('modalApprove').style.display = 'flex';
}

function closeApproveModal() {
    document.getElementById('modalApprove').style.display = 'none';
    activeApproveId = null;
}

document.getElementById('modalApprove').addEventListener('click', function(e) {
    if (e.target === this) closeApproveModal();
});

async function confirmApprove() {
    if (!activeApproveId) return;
    const btn = document.getElementById('btnConfirmApprove');
    btn.disabled = true;

    try {
        const res  = await fetch(`${PENGAJUAN_BASE}/${activeApproveId}/approve`, {
            method : 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (!res.ok) {
            closeApproveModal();
            return showToast(data.message || 'Terjadi kesalahan.', 'error');
        }

        // Update baris tabel
        const row = document.getElementById(`row-${activeApproveId}`);
        if (row) {
            const tds = row.querySelectorAll('td');
            tds[4].innerHTML =
                `<span style="background:#e8f5e9;color:#16a34a;border-radius:20px;padding:4px 10px;font-size:11.5px;font-weight:700;">✅ Disetujui</span>`;
            tds[6].innerHTML =
                `<span style="font-size:11.5px;color:#cbd5e1;">Baru saja</span>`;
        }

        // ✅ Update summary cards
        updateCounts('approve');

        closeApproveModal();
        showToast(data.message, 'success');

    } catch (e) {
        showToast('Gagal terhubung ke server.', 'error');
    } finally {
        btn.disabled = false;
    }
}

// ── Reject modal ─────────────────────────────────────────────────────────────
function openRejectModal(id, nama) {
    activeRejectId = id;
    document.getElementById('rejectNama').textContent = nama;
    document.getElementById('rejectAlasan').value     = '';
    document.getElementById('modalReject').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('modalReject').style.display = 'none';
    activeRejectId = null;
}

document.getElementById('modalReject').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

async function confirmReject() {
    if (!activeRejectId) return;
    const alasan = document.getElementById('rejectAlasan').value.trim();
    const btn    = document.getElementById('btnConfirmReject');
    btn.disabled = true;

    try {
        const res  = await fetch(`${PENGAJUAN_BASE}/${activeRejectId}/reject`, {
            method : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN' : CSRF,
                'Accept'       : 'application/json',
            },
            body: JSON.stringify({ alasan_tolak: alasan || null }),
        });
        const data = await res.json();

        if (!res.ok) return showToast(data.message || 'Terjadi kesalahan.', 'error');

        // Update baris tabel
        const row = document.getElementById(`row-${activeRejectId}`);
        if (row) {
            const tds = row.querySelectorAll('td');
            let html = `<span style="background:#fce4ec;color:#ef4444;border-radius:20px;padding:4px 10px;font-size:11.5px;font-weight:700;">❌ Ditolak</span>`;
            if (alasan) html += `<div style="font-size:11px;color:#ef4444;margin-top:4px;font-style:italic;">"${alasan.substring(0,40)}${alasan.length>40?'...':''}"</div>`;
            tds[4].innerHTML = html;
            tds[6].innerHTML = `<span style="font-size:11.5px;color:#cbd5e1;">Baru saja</span>`;
        }

        // ✅ Update summary cards
        updateCounts('reject');

        closeRejectModal();
        showToast(data.message, 'success');

    } catch (e) {
        showToast('Gagal terhubung ke server.', 'error');
    } finally {
        btn.disabled = false;
    }
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const bg = type === 'success' ? '#16a34a' : '#ef4444';
    const t  = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = `
        position:fixed;bottom:28px;right:28px;z-index:9999;
        background:${bg};color:#fff;padding:12px 20px;
        border-radius:12px;font-size:13px;font-weight:600;
        box-shadow:0 4px 20px rgba(0,0,0,.2);
    `;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}
</script>

@endsection