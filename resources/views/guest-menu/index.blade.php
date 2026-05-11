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

// ── Toast ──
function showToast(msg, type) {
  let t = document.getElementById('adminToast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'adminToast';
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;background:#1a3320;color:#fff;border-radius:12px;padding:12px 18px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.2);display:flex;align-items:center;gap:8px;transition:opacity .3s;';
    document.body.appendChild(t);
  }
  t.style.background = type === 'success' ? '#2e7d32' : '#c62828';
  t.innerHTML = `<i class="fa-solid ${type==='success'?'fa-circle-check':'fa-circle-xmark'}"></i> ${msg}`;
  t.style.opacity = '1';
  clearTimeout(t._timer);
  t._timer = setTimeout(() => t.style.opacity = '0', 2500);
}
</script>
<style>
.sortable-ghost { opacity:.4; background:#e8f5e9 !important; }
</style>

@endsection