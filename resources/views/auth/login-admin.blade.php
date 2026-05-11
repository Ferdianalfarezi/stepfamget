<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin — HRIS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --green-dark: #1a3320; --green-main: #3d7a47;
    --accent: #f59e0b;
  }
  html, body { height: 100%; font-family: 'Plus Jakarta Sans', sans-serif; background: var(--green-dark); overflow: hidden; }

  .bg-wrap { position:fixed; inset:0; z-index:0; background:var(--green-dark); }
  .bg-wrap::before { content:''; position:absolute; inset:0; background:radial-gradient(ellipse 80% 60% at 50% -10%, rgba(61,122,71,0.4) 0%, transparent 70%); }
  .bg-dots  { position:absolute; inset:0; background-image:radial-gradient(circle,rgba(255,255,255,0.04) 1px,transparent 1px); background-size:28px 28px; }
  .bg-orb   { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.15; }
  .bg-orb.o1 { width:400px;height:400px;background:#3d7a47;top:-80px;right:-80px; }
  .bg-orb.o2 { width:280px;height:280px;background:#f59e0b;bottom:-50px;left:-50px; }

  .wrap { position:relative;z-index:1;min-height:100dvh;display:flex;align-items:center;justify-content:center;padding:24px 16px; }

  .card {
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(24px);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:24px;
    width:100%;max-width:400px;
    padding:40px 36px;
    animation:fadeUp .5s cubic-bezier(.22,.61,.36,1) both;
  }
  @keyframes fadeUp { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }

  .back-link { display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,0.4);font-size:12px;text-decoration:none;margin-bottom:24px;transition:color .2s; }
  .back-link:hover { color:rgba(255,255,255,0.8); }

  .icon-wrap {
    width:58px;height:58px;border-radius:14px;
    background:linear-gradient(135deg,rgba(245,158,11,0.3),rgba(245,158,11,0.1));
    border:1px solid rgba(245,158,11,0.3);
    display:flex;align-items:center;justify-content:center;
    font-size:22px;color:var(--accent);
    margin-bottom:18px;
  }
  .title { font-size:20px;font-weight:800;color:#fff;letter-spacing:-.4px; }
  .sub   { font-size:12px;color:rgba(255,255,255,.4);margin-top:4px;margin-bottom:28px; }

  .form-group { margin-bottom:16px; }
  label { display:block;font-size:11.5px;font-weight:600;color:rgba(255,255,255,.6);margin-bottom:7px;letter-spacing:.3px; }
  .input-wrap { position:relative; }
  .input-wrap i { position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.25);font-size:13px; }
  input[type=email], input[type=password] {
    width:100%;
    background:rgba(255,255,255,.07);
    border:1px solid rgba(255,255,255,.12);
    border-radius:10px;
    padding:12px 14px 12px 40px;
    color:#fff; font-size:13.5px;
    font-family:inherit;
    outline:none;
    transition:border-color .2s,background .2s;
  }
  input::placeholder { color:rgba(255,255,255,.25); }
  input:focus { border-color:rgba(61,122,71,.6);background:rgba(255,255,255,.1); }

  .remember { display:flex;align-items:center;gap:8px;margin-bottom:22px;font-size:12px;color:rgba(255,255,255,.45);cursor:pointer; }
  .remember input[type=checkbox] { accent-color:var(--green-main); }

  .btn-submit {
    width:100%;padding:13px;border:none;cursor:pointer;
    background:linear-gradient(135deg,var(--green-main),#2a5c33);
    border-radius:11px;color:#fff;font-size:14px;font-weight:700;
    font-family:inherit;letter-spacing:.2px;
    transition:transform .2s,box-shadow .2s;
    box-shadow:0 4px 18px rgba(61,122,71,.35);
  }
  .btn-submit:hover { transform:translateY(-2px);box-shadow:0 8px 28px rgba(61,122,71,.45); }
  .btn-submit:active { transform:translateY(0); }

  .error-msg { background:rgba(198,40,40,.18);border:1px solid rgba(198,40,40,.35);border-radius:10px;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#ff8a80; }
</style>
</head>
<body>
<div class="bg-wrap"><div class="bg-dots"></div><div class="bg-orb o1"></div><div class="bg-orb o2"></div></div>

<div class="wrap">
  <div class="card">

    <a href="{{ route('landing') }}" class="back-link">
      <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>

    <div class="icon-wrap"><i class="fa-solid fa-shield-halved"></i></div>
    <div class="title">Login Admin</div>
    <div class="sub">Masuk dengan akun administrator</div>

    @if($errors->any())
    <div class="error-msg">
      <i class="fa-solid fa-triangle-exclamation" style="margin-right:6px;"></i>
      {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
      @csrf

      <div class="form-group">
        <label>USERNAME</label>
        <div class="input-wrap">
          <i class="fa-solid fa-user"></i>
          <input type="text" name="username" placeholder="username admin" value="{{ old('username') }}" required autofocus>
        </div>
      </div>

      <div class="form-group">
        <label>PASSWORD</label>
        <div class="input-wrap">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="password" placeholder="••••••••" required>
        </div>
      </div>

      <label class="remember">
        <input type="checkbox" name="remember"> Ingat saya
      </label>

      <button type="submit" class="btn-submit">
        <i class="fa-solid fa-right-to-bracket" style="margin-right:8px;"></i>
        Masuk sebagai Admin
      </button>
    </form>

  </div>
</div>
</body>
</html>