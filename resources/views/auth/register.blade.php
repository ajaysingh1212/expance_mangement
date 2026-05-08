<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | {{ $siteName ?? 'RBAC System' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#1e1b4b 0%,#312e81 50%,#4c1d95 100%); padding:20px; }
        .card { background:rgba(255,255,255,0.95); border-radius:24px; padding:40px 36px; width:100%; max-width:500px; box-shadow:0 25px 50px rgba(0,0,0,0.3); }
        .logo { width:50px;height:50px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.3rem;margin-bottom:20px; }
        h2 { font-family:'Poppins',sans-serif;font-size:1.5rem;font-weight:700;color:#1e293b;margin-bottom:4px; }
        p.sub { color:#64748b;font-size:0.875rem;margin-bottom:24px; }
        .form-row { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
        .form-group { margin-bottom:16px; }
        label { display:block;font-size:0.82rem;font-weight:500;color:#374151;margin-bottom:5px; }
        .inp-wrap { position:relative; }
        .ico { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:0.85rem; }
        input { width:100%;padding:11px 12px 11px 36px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:0.875rem;color:#1e293b;outline:none;transition:all 0.2s;background:#fff; }
        input:focus { border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,0.1); }
        input.err { border-color:#ef4444; }
        .btn { width:100%;padding:13px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:10px;font-size:0.95rem;font-weight:600;cursor:pointer;transition:all 0.2s;margin-top:8px; }
        .btn:hover { transform:translateY(-1px);box-shadow:0 8px 20px rgba(79,70,229,0.4); }
        .login-link { text-align:center;font-size:0.875rem;color:#64748b;margin-top:16px; }
        .login-link a { color:#4f46e5;text-decoration:none;font-weight:600; }
        .error-box { background:#fee2e2;color:#991b1b;border-radius:8px;padding:10px 14px;font-size:0.85rem;margin-bottom:14px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo"><i class="fas fa-shield-alt"></i></div>
        <h2>Create Account</h2>
        <p class="sub">Join the RBAC System</p>

        @if($errors->any())
            <div class="error-box"><i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name</label>
                    <div class="inp-wrap">
                        <i class="fas fa-user ico"></i>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" class="{{ $errors->has('name') ? 'err' : '' }}" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <div class="inp-wrap">
                        <i class="fas fa-at ico"></i>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="john_doe" class="{{ $errors->has('username') ? 'err' : '' }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <div class="inp-wrap">
                    <i class="fas fa-envelope ico"></i>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" class="{{ $errors->has('email') ? 'err' : '' }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <div class="inp-wrap">
                        <i class="fas fa-lock ico"></i>
                        <input type="password" name="password" placeholder="Min 8 chars" class="{{ $errors->has('password') ? 'err' : '' }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="inp-wrap">
                        <i class="fas fa-lock ico"></i>
                        <input type="password" name="password_confirmation" placeholder="Repeat password" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn"><i class="fas fa-user-plus mr-2"></i> Create Account</button>
        </form>
        <p class="login-link">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
    </div>
</body>
</html>
