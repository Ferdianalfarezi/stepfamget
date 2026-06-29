<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#1a3320">
<title>@yield('title', 'HRIS') — {{ $karyawan->nama }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" type="image/png" href="{{ asset('images/logostep31.png') }}">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --green-dark: #1a3320; --green-main: #3d7a47; --green-light: #e8f5e9;
    --bg: #f0f4f0; --white: #fff; --border: #e0e8e0;
    --text: #1a1a1a; --text-mid: #555; --text-light: #999;
    --safe-top: env(safe-area-inset-top, 0px);
    --safe-bottom: env(safe-area-inset-bottom, 0px);
    --accent-color: {{ isset($menu) ? $menu->color : '#3d7a47' }};
    --accent-bg:    {{ isset($menu) ? $menu->bg_color : '#e8f5e9' }};
  }
  html { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); }
  body { min-height: 100dvh; padding-bottom: calc(72px + var(--safe-bottom)); }

  /* HEADER */
  .page-header {
    background: var(--green-dark);
    padding: calc(14px + var(--safe-top)) 20px 18px;
    display: flex; align-items: center; gap: 14px;
  }
  .btn-back {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 14px; text-decoration: none;
    transition: background .2s;
  }
  .btn-back:active { background: rgba(255,255,255,.2); }

  .page-icon {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    background: rgba(255,255,255,.12);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: #fff;
  }
  .page-title { font-size: 17px; font-weight: 800; color: #fff; }
  .page-sub   { font-size: 11px; color: rgba(255,255,255,.4); margin-top: 2px; }

  /* CONTENT */
  .page-content { padding: 16px; max-width: 520px; margin: 0 auto; }

  /* CARD */
  .card {
    background: var(--white); border: 1px solid var(--border);
    border-radius: 16px; padding: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
    margin-bottom: 12px;
  }
  .card-title {
    font-size: 13px; font-weight: 700; color: var(--text);
    margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
  }
  .card-title i { color: var(--accent-color); }

  /* INFO ROW */
  .info-row { display:flex; align-items:center; justify-content:space-between; padding:8px 0; }
  .info-row:not(:last-child) { border-bottom: 1px solid var(--bg); }
  .info-key { font-size:12px; color:var(--text-light); display:flex; align-items:center; gap:8px; }
  .info-key i { width:16px; text-align:center; color:var(--green-main); font-size:12px; }
  .info-val { font-size:13px; font-weight:600; color:var(--text); }

  /* BOTTOM NAV */
  .bottom-nav {
    position:fixed; bottom:0; left:0; right:0; z-index:100;
    background:rgba(255,255,255,.96); backdrop-filter:blur(16px);
    border-top:1px solid var(--border);
    display:flex; justify-content:space-around; align-items:center;
    padding:10px 0 calc(10px + var(--safe-bottom));
  }
  .nav-item { display:flex; flex-direction:column; align-items:center; gap:3px; text-decoration:none; }
  .nav-item i    { font-size:18px; color:var(--text-light); }
  .nav-item span { font-size:10px; color:var(--text-light); font-weight:600; }
  .nav-item.active i, .nav-item.active span { color:var(--green-main); }

  /* SECTION TITLE */
  .section-title { font-size:11px; font-weight:700; color:var(--text-light); letter-spacing:1px; margin-bottom:10px; }

  /* BADGE */
  .badge {
    display:inline-flex; align-items:center; gap:5px;
    border-radius:20px; padding:3px 10px;
    font-size:11px; font-weight:700;
  }
  .badge-success { background:#e8f5e9; color:#2e7d32; }
  .badge-danger  { background:#ffebee; color:#c62828; }

  @yield('style')
</style>
@yield('head')
</head>
<body>

<!-- HEADER -->
<div class="page-header">
  <a href="{{ route('guest.dashboard') }}" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i>
  </a>
  @if(isset($menu))
  <div class="page-icon">
  <i class="fa-solid {{ $menu->icon }}"></i>
</div>
  <div>
    <div class="page-title">{{ $menu->label }}</div>
    <div class="page-sub">{{ $karyawan->nama }}</div>
  </div>
  @else
  <div>
    <div class="page-title">@yield('title')</div>
    <div class="page-sub">{{ $karyawan->nama }}</div>
  </div>
  @endif
</div>

<!-- PAGE CONTENT -->
<div class="page-content">
  @yield('content')
</div>

<!-- BOTTOM NAV -->
<div class="bottom-nav">
  <a href="{{ route('guest.dashboard') }}" class="nav-item">
    <i class="fa-solid fa-house"></i>
    <span>Beranda</span>
  </a>

  <form method="POST" action="{{ route('logout') }}" style="margin:0;">
    @csrf
    <button type="submit" class="nav-item" style="background:none;border:none;cursor:pointer;">
      <i class="fa-solid fa-right-from-bracket"></i>
      <span>Keluar</span>
    </button>
  </form>
</div>

@yield('scripts')
</body>
</html>