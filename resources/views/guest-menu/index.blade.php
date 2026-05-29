@extends('layouts.app')
@section('title', 'Kelola Menu Guest')
@section('page-title', 'Kelola Menu Guest')

@section('content')

<div class="card">
  <div class="card-header">
    <div class="card-title">
      <i class="fa-solid fa-grid-2"></i>
      Menu Tampilan Karyawan
    </div>
    <span style="font-size:11px;color:#999;">Drag untuk ubah urutan · Toggle untuk aktifkan/nonaktifkan</span>
  </div>

  <div style="padding:16px;">

    <div style="background:#fff8e1;border:1px solid #ffe082;border-radius:10px;padding:12px 14px;margin-bottom:16px;font-size:12px;color:#795548;display:flex;gap:10px;">
      <i class="fa-solid fa-circle-info" style="color:#f59e0b;margin-top:1px;flex-shrink:0;"></i>
      <span>Menu yang diaktifkan akan muncul di dashboard karyawan. Drag & drop untuk mengatur urutan tampilan.</span>
    </div>

    <div id="menuList" style="display:flex;flex-direction:column;gap:8px;">
      @foreach($menus as $menu)
      <div class="menu-row" data-id="{{ $menu->id }}"
           style="background:#fff;border:1px solid #e8ede8;border-radius:12px;padding:14px 16px;
                  display:flex;align-items:center;gap:14px;cursor:grab;
                  transition:box-shadow .2s,opacity .2s;
                  {{ !$menu->is_active ? 'opacity:0.55;' : '' }}">

        {{-- Drag handle --}}
        <div class="drag-handle" style="color:#ccc;font-size:14px;cursor:grab;flex-shrink:0;">
          <i class="fa-solid fa-grip-vertical"></i>
        </div>

        {{-- Icon --}}
        <div style="width:42px;height:42px;border-radius:11px;background:{{ $menu->bg_color }};
                    display:flex;align-items:center;justify-content:center;
                    font-size:18px;color:{{ $menu->color }};flex-shrink:0;">
          <i class="fa-solid {{ $menu->icon }}"></i>
        </div>

        {{-- Info --}}
        <div style="flex:1;">
          <div style="font-size:14px;font-weight:700;color:#111;">{{ $menu->label }}</div>
          <div style="font-size:11px;color:#999;margin-top:2px;">key: <code>{{ $menu->key }}</code></div>
        </div>

        {{-- Urutan badge --}}
        <div style="font-size:11px;font-weight:700;color:#ccc;min-width:24px;text-align:center;">
          #{{ $menu->urutan }}
        </div>

        {{-- Deadline input --}}
        <div style="display:flex;flex-direction:column;gap:4px;min-width:180px;">
          <label style="font-size:10px;font-weight:700;color:#aaa;letter-spacing:.5px;">BERLAKU HINGGA</label>
          <div style="display:flex;align-items:center;gap:6px;">
            <div style="position:relative;flex:1;">
              <input type="datetime-local"
                     class="deadline-input"
                     data-id="{{ $menu->id }}"
                     data-label="{{ $menu->label }}"
                     value="{{ $menu->berlaku_hingga ? $menu->berlaku_hingga->format('Y-m-d\TH:i') : '' }}"
                     style="border:1.5px solid #e0e8e0;border-radius:8px;padding:6px 8px;
                            font-size:12px;font-family:inherit;color:#333;outline:none;
                            background:#f8faf8;width:100%;transition:border-color .2s,box-shadow .2s;">
              {{-- Spinner overlay --}}
              <div class="deadline-spinner" data-id="{{ $menu->id }}"
                   style="display:none;position:absolute;right:8px;top:50%;transform:translateY(-50%);">
                <i class="fa-solid fa-circle-notch fa-spin" style="color:#3d7a47;font-size:12px;"></i>
              </div>
            </div>
            <span class="deadline-badge" data-id="{{ $menu->id }}"
                  style="font-size:10px;font-weight:700;padding:3px 8px;border-radius:20px;white-space:nowrap;
                         background:{{ $menu->berlaku_hingga ? ($menu->berlaku_hingga->isPast() ? '#fce8e8' : '#e8f5e9') : '#f5f5f5' }};
                         color:{{ $menu->berlaku_hingga ? ($menu->berlaku_hingga->isPast() ? '#c62828' : '#2e7d32') : '#aaa' }};">
              {{ $menu->berlaku_hingga ? ($menu->berlaku_hingga->isPast() ? 'Expired' : 'Aktif') : 'Selamanya' }}
            </span>
          </div>
          {{-- Feedback row --}}
          <div class="deadline-feedback" data-id="{{ $menu->id }}"
               style="display:none;font-size:11px;font-weight:600;margin-top:2px;
                      display:flex;align-items:center;gap:4px;">
          </div>
        </div>

        {{-- Toggle switch --}}
        <label class="toggle-wrap" style="flex-shrink:0;cursor:pointer;display:flex;align-items:center;gap:8px;">
          <div class="toggle {{ $menu->is_active ? 'on' : '' }}" data-id="{{ $menu->id }}"
               onclick="toggleMenu({{ $menu->id }}, this)"
               style="width:44px;height:24px;border-radius:12px;position:relative;transition:background .2s;
                      background:{{ $menu->is_active ? '#3d7a47' : '#ddd' }};cursor:pointer;">
            <div style="position:absolute;top:3px;transition:left .2s;
                        left:{{ $menu->is_active ? '23px' : '3px' }};
                        width:18px;height:18px;border-radius:50%;background:#fff;
                        box-shadow:0 1px 4px rgba(0,0,0,.2);">
            </div>
          </div>
          <span style="font-size:12px;font-weight:600;color:{{ $menu->is_active ? '#3d7a47' : '#999' }};" class="toggle-label">
            {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
          </span>
        </label>

      </div>
      @endforeach
    </div>

  </div>
</div>

{{-- SortableJS CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
// ── Drag & Drop reorder ──
const list = document.getElementById('menuList');
Sortable.create(list, {
  handle: '.drag-handle',
  animation: 150,
  ghostClass: 'sortable-ghost',
  onEnd: function() {
    const order = [...list.querySelectorAll('.menu-row')].map(el => el.dataset.id);
    fetch('{{ route('guest-menu.reorder') }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ order }),
    })
    .then(r => r.json())
    .then(() => showToast('Urutan disimpan', 'success'))
    .catch(() => showToast('Gagal menyimpan urutan', 'error'));
  }
});

// ── Toggle aktif ──
function toggleMenu(id, el) {
  fetch(`/guest-menu/${id}/toggle`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({}),
  })
  .then(r => r.json())
  .then(data => {
    const row   = el.closest('.menu-row');
    const thumb = el.querySelector('div');
    const label = el.nextElementSibling;

    if (data.is_active) {
      el.style.background = '#3d7a47';
      thumb.style.left    = '23px';
      label.textContent   = 'Aktif';
      label.style.color   = '#3d7a47';
      row.style.opacity   = '1';
    } else {
      el.style.background = '#ddd';
      thumb.style.left    = '3px';
      label.textContent   = 'Nonaktif';
      label.style.color   = '#999';
      row.style.opacity   = '0.55';
    }
    showToast(data.message, 'success');
  })
  .catch(() => showToast('Gagal mengubah status', 'error'));
}

// ── Deadline save on change ──
document.querySelectorAll('.deadline-input').forEach(input => {
  input.addEventListener('change', function () {
    const id      = this.dataset.id;
    const label   = this.dataset.label;
    const val     = this.value;
    const inputEl = this;

    // UI: loading state
    inputEl.style.borderColor  = '#3d7a47';
    inputEl.style.boxShadow    = '0 0 0 3px rgba(61,122,71,.12)';
    inputEl.disabled           = true;
    const spinner = document.querySelector(`.deadline-spinner[data-id="${id}"]`);
    if (spinner) spinner.style.display = 'block';

    // Sembunyiin feedback lama
    const feedback = document.querySelector(`.deadline-feedback[data-id="${id}"]`);
    if (feedback) feedback.style.display = 'none';

    fetch(`/guest-menu/${id}/deadline`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ berlaku_hingga: val || null }),
    })
    .then(r => r.json())
    .then(data => {
      // Update badge
      const badge = document.querySelector(`.deadline-badge[data-id="${id}"]`);
      if (badge) {
        if (!val) {
          badge.textContent      = 'Selamanya';
          badge.style.background = '#f5f5f5';
          badge.style.color      = '#aaa';
        } else {
          const expired          = new Date(val) < new Date();
          badge.textContent      = expired ? 'Expired' : 'Aktif';
          badge.style.background = expired ? '#fce8e8' : '#e8f5e9';
          badge.style.color      = expired ? '#c62828' : '#2e7d32';
        }
      }

      // Feedback inline — berhasil
      if (feedback) {
        feedback.style.display = 'flex';
        feedback.style.color   = '#2e7d32';
        feedback.innerHTML     = val
          ? `<i class="fa-solid fa-circle-check"></i> Batas waktu <strong>${label}</strong> berhasil disimpan`
          : `<i class="fa-solid fa-circle-check"></i> Batas waktu <strong>${label}</strong> dihapus (selamanya)`;
        clearTimeout(feedback._timer);
        feedback._timer = setTimeout(() => feedback.style.display = 'none', 3500);
      }

      // Toast global
      showToast(
        val ? `Deadline "${label}" berhasil disimpan` : `Deadline "${label}" dihapus`,
        'success'
      );

      // Border success sebentar lalu balik normal
      inputEl.style.borderColor = '#3d7a47';
      setTimeout(() => {
        inputEl.style.borderColor = '#e0e8e0';
        inputEl.style.boxShadow   = 'none';
      }, 1500);
    })
    .catch(() => {
      // Feedback inline — gagal
      if (feedback) {
        feedback.style.display = 'flex';
        feedback.style.color   = '#c62828';
        feedback.innerHTML     = `<i class="fa-solid fa-circle-xmark"></i> Gagal menyimpan batas waktu`;
        clearTimeout(feedback._timer);
        feedback._timer = setTimeout(() => feedback.style.display = 'none', 3500);
      }

      inputEl.style.borderColor = '#e53935';
      inputEl.style.boxShadow   = '0 0 0 3px rgba(229,57,53,.12)';
      setTimeout(() => {
        inputEl.style.borderColor = '#e0e8e0';
        inputEl.style.boxShadow   = 'none';
      }, 2000);

      showToast(`Gagal menyimpan deadline "${label}"`, 'error');
    })
    .finally(() => {
      inputEl.disabled        = false;
      if (spinner) spinner.style.display = 'none';
    });
  });
});

// ── Toast ──
function showToast(msg, type) {
  let t = document.getElementById('adminToast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'adminToast';
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;background:#1a3320;color:#fff;' +
                      'border-radius:12px;padding:12px 18px;font-size:13px;font-weight:600;' +
                      'box-shadow:0 4px 20px rgba(0,0,0,.2);display:flex;align-items:center;' +
                      'gap:8px;transition:opacity .3s,transform .3s;transform:translateY(0);';
    document.body.appendChild(t);
  }
  t.style.background = type === 'success' ? '#2e7d32' : '#c62828';
  t.style.opacity    = '1';
  t.style.transform  = 'translateY(0)';
  t.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'}"></i> ${msg}`;
  clearTimeout(t._timer);
  t._timer = setTimeout(() => {
    t.style.opacity   = '0';
    t.style.transform = 'translateY(8px)';
  }, 2800);
}
</script>

<style>
.sortable-ghost { opacity:.4; background:#e8f5e9 !important; }
.deadline-input:focus {
  border-color: #3d7a47 !important;
  box-shadow: 0 0 0 3px rgba(61,122,71,.12) !important;
  background: #fff !important;
}
@keyframes fa-spin {
  0%   { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

@endsection