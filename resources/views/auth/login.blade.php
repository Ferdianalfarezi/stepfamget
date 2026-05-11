<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – STEPFAMGET</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#f0f4ff;min-height:100vh;display:flex;align-items:center;justify-content:center;}
        .login-wrap{display:flex;width:100%;max-width:900px;min-height:520px;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(59,130,246,.15);}

        .login-left{flex:1;background:linear-gradient(145deg,#1d4ed8,#6366f1);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px;color:white;text-align:center;}
        .login-left .icon-big{width:80px;height:80px;background:rgba(255,255,255,.15);border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:36px;margin:0 auto 20px;backdrop-filter:blur(10px);}
        .login-left h1{font-size:26px;font-weight:700;margin-bottom:10px;}
        .login-left p{font-size:13.5px;opacity:.8;line-height:1.6;}
        .dots{display:flex;gap:8px;justify-content:center;margin-top:30px;}
        .dot{width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,.4);}
        .dot.active{background:white;width:24px;border-radius:4px;}

        .login-right{width:380px;background:white;display:flex;flex-direction:column;justify-content:center;padding:40px;}
        .login-right h2{font-size:22px;font-weight:700;color:#1e293b;margin-bottom:4px;}
        .login-right p{font-size:13px;color:#64748b;margin-bottom:28px;}

        .form-group{margin-bottom:16px;}
        label{display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px;}
        .input-wrap{position:relative;}
        .input-wrap i{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:14px;}
        input[type=text],input[type=password]{width:100%;height:44px;padding:0 14px 0 40px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:13.5px;font-family:inherit;color:#1e293b;outline:none;transition:.2s;}
        input:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.12);}
        input.is-invalid{border-color:#ef4444;}
        .invalid-feedback{font-size:11.5px;color:#ef4444;margin-top:4px;}

        .toggle-pass{position:absolute;right:13px;top:50%;transform:translateY(-50%);color:#9ca3af;cursor:pointer;font-size:14px;background:none;border:none;}

        .remember{display:flex;align-items:center;gap:8px;font-size:12.5px;color:#374151;cursor:pointer;}
        .remember input{width:15px;height:15px;accent-color:#3b82f6;}

        .btn-login{width:100%;height:44px;background:linear-gradient(135deg,#3b82f6,#6366f1);color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;margin-top:20px;transition:.2s;font-family:inherit;}
        .btn-login:hover{opacity:.9;box-shadow:0 6px 20px rgba(59,130,246,.35);}

        .divider{text-align:center;font-size:11.5px;color:#9ca3af;margin:20px 0 0;padding-top:16px;border-top:1px solid #f1f5f9;}
        .divider span{color:#64748b;font-weight:500;}
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-left">
        <div class="icon-big"><i class="fa-solid fa-users-gear"></i></div>
        <h1>STEPFAMGET</h1>
        <p>Sistem Informasi Data Karyawan & Keluarga terpadu untuk pengelolaan SDM yang lebih efisien.</p>
        <div class="dots">
            <div class="dot active"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>
    <div class="login-right">
        <h2>Selamat Datang 👋</h2>
        <p>Masuk untuk mengakses sistem</p>

        @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:8px;padding:10px 14px;font-size:12.5px;color:#991b1b;margin-bottom:14px;">
            <i class="fa-solid fa-circle-exclamation" style="margin-right:6px;"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label>Username</label>
                <div class="input-wrap">
                    <i class="fa-regular fa-user"></i>
                    <input type="text" name="username" placeholder="Masukkan username" value="{{ old('username') }}"
                           class="{{ $errors->has('username') ? 'is-invalid' : '' }}" autofocus>
                </div>
                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Masukkan password"
                           class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
                    <button type="button" class="toggle-pass" onclick="togglePass()">
                        <i class="fa-regular fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <label class="remember">
                <input type="checkbox" name="remember"> Ingat saya
            </label>
            <button type="submit" class="btn-login">
                <i class="fa-solid fa-arrow-right-to-bracket" style="margin-right:6px;"></i> Masuk
            </button>
        </form>
        <div class="divider">Default: <span>admin</span> / <span>admin123</span></div>
    </div>
</div>
<script>
function togglePass(){
    const p = document.getElementById('password');
    const e = document.getElementById('eyeIcon');
    if(p.type==='password'){p.type='text';e.className='fa-regular fa-eye-slash';}
    else{p.type='password';e.className='fa-regular fa-eye';}
}
</script>
</body>
</html>
