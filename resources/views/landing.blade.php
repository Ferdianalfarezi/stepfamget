<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HRIS — Sistem Informasi Karyawan</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --green-dark:  #1a3320;
    --green-mid:   #2e5c3a;
    --green-main:  #3d7a47;
    --green-light: #d4edda;
    --accent:      #f59e0b;
    --white:       #ffffff;
    --gray-100:    #f7f9f7;
    --text-dark:   #111;
    --text-mid:    #555;
    --text-light:  #999;
  }

  html, body {
    height: 100%;
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--gray-100);
    overflow: hidden;
  }

  /* ── Background ── */
  .bg-wrap {
    position: fixed; inset: 0; z-index: 0;
    background: var(--green-dark);
    overflow: hidden;
  }
  .bg-wrap::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(61,122,71,0.45) 0%, transparent 70%);
  }
  .bg-dots {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px);
    background-size: 28px 28px;
  }
  .bg-orb {
    position: absolute; border-radius: 50%; filter: blur(70px); opacity: 0.18;
  }
  .bg-orb.o1 { width:420px; height:420px; background:#3d7a47; top:-100px; right:-80px; }
  .bg-orb.o2 { width:300px; height:300px; background:#f59e0b; bottom:-60px; left:-60px; }

  /* ── Card ── */
  .card-wrap {
    position: relative; z-index: 1;
    min-height: 100dvh;
    display: flex; align-items: center; justify-content: center;
    padding: 24px 16px;
  }

  .card {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(24px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 24px;
    width: 100%; max-width: 420px;
    padding: 40px 36px;
    text-align: center;
    animation: fadeUp 0.55s cubic-bezier(.22,.61,.36,1) both;
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(32px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* ── Logo ── */
  .logo-ring {
    display: inline-flex; align-items: center; justify-content: center;
    width: 72px; height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--green-main), var(--green-dark));
    box-shadow: 0 8px 32px rgba(61,122,71,0.45);
    margin-bottom: 20px;
  }
  .logo-ring i { font-size: 28px; color: #fff; }

  .brand { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
  .tagline {
    font-size: 12.5px; color: rgba(255,255,255,0.45);
    margin-top: 4px; margin-bottom: 32px;
    letter-spacing: 0.3px;
  }

  /* ── Divider ── */
  .divider {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 20px;
  }
  .divider span { font-size: 11px; color: rgba(255,255,255,0.3); white-space: nowrap; }
  .divider::before, .divider::after {
    content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.12);
  }

  /* ── Buttons ── */
  .btn-role {
    display: flex; align-items: center; gap: 14px;
    width: 100%;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 14px;
    padding: 16px 18px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.22s ease;
    margin-bottom: 12px;
    text-align: left;
  }
  .btn-role:hover {
    background: rgba(255,255,255,0.12);
    border-color: rgba(255,255,255,0.25);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
  }
  .btn-role:last-child { margin-bottom: 0; }

  .btn-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 18px;
  }
  .btn-icon.admin  { background: rgba(245,158,11,0.18); color: var(--accent); }
  .btn-icon.guest  { background: rgba(61,122,71,0.25);  color: #7ec88a; }

  .btn-info { flex: 1; }
  .btn-title { font-size: 14px; font-weight: 700; color: #fff; display: block; }
  .btn-sub   { font-size: 11px; color: rgba(255,255,255,0.4); margin-top: 2px; display: block; }

  .btn-arrow { color: rgba(255,255,255,0.25); font-size: 13px; }

  /* ── Footer ── */
  .footer {
    margin-top: 28px;
    font-size: 10.5px; color: rgba(255,255,255,0.2);
    line-height: 1.6;
  }

  /* ── Mobile ── */
  @media (max-width: 400px) {
    .card { padding: 32px 24px; }
  }
</style>
</head>
<body>

<div class="bg-wrap">
  <div class="bg-dots"></div>
  <div class="bg-orb o1"></div>
  <div class="bg-orb o2"></div>
</div>

<div class="card-wrap">
  <div class="card">

    <div class="logo-ring">
      <i class="fa-solid fa-building-user"></i>
    </div>

    <div class="brand">HRIS</div>
    <div class="tagline">Sistem Informasi Karyawan</div>

    <div class="divider"><span>Masuk sebagai</span></div>

    {{-- Login Admin --}}
    <a href="{{ route('login') }}" class="btn-role">
      <div class="btn-icon admin">
        <i class="fa-solid fa-shield-halved"></i>
      </div>
      <div class="btn-info">
        <span class="btn-title">Administrator</span>
        <span class="btn-sub">Kelola data karyawan & sistem</span>
      </div>
      <i class="fa-solid fa-chevron-right btn-arrow"></i>
    </a>

    {{-- Login Guest / Karyawan --}}
    <a href="{{ route('login.guest') }}" class="btn-role">
      <div class="btn-icon guest">
        <i class="fa-solid fa-id-card"></i>
      </div>
      <div class="btn-info">
        <span class="btn-title">Karyawan</span>
        <span class="btn-sub">Lihat profil & konfirmasi kehadiran</span>
      </div>
      <i class="fa-solid fa-chevron-right btn-arrow"></i>
    </a>

    <div class="footer">
      &copy; {{ date('Y') }} PT STEP &mdash; step.co.id<br>
      Semua hak dilindungi
    </div>

  </div>
</div>

</body>
</html>