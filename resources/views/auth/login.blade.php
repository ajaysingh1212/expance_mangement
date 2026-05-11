<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | {{ $siteName ?? 'RBAC System' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0f0c29;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4c1d95 100%);
            overflow: hidden;
        }

        /* Animated background shapes */
        .bg-shapes { position: fixed; inset: 0; overflow: hidden; z-index: 0; }
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            animation: float 20s infinite ease-in-out;
        }
        .shape:nth-child(1) { width: 400px; height: 400px; top: -100px; left: -100px; animation-duration: 25s; }
        .shape:nth-child(2) { width: 300px; height: 300px; top: 60%; right: -80px; animation-duration: 18s; animation-delay: -8s; }
        .shape:nth-child(3) { width: 200px; height: 200px; bottom: 10%; left: 30%; animation-duration: 22s; animation-delay: -4s; }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(10deg); }
        }

        /* Left Panel */
        .left-panel {
            flex: 1;
            display: none;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
            z-index: 1;
        }
        @media(min-width:900px) { .left-panel { display: flex; } }

        .left-panel .tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #a5b4fc;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 30px;
            width: fit-content;
        }

        .left-panel h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 3rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 20px;
        }
        .left-panel h1 span { color: #818cf8; }
        .left-panel p { color: #a5b4fc; font-size: 1.05rem; line-height: 1.7; max-width: 420px; }

        .feature-list { margin-top: 40px; display: flex; flex-direction: column; gap: 16px; }
        .feature-item { display: flex; align-items: center; gap: 12px; color: #c7d2fe; font-size: 0.9rem; }
        .feature-icon {
            width: 36px; height: 36px;
            background: rgba(99, 102, 241, 0.3);
            border: 1px solid rgba(99, 102, 241, 0.5);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #818cf8; font-size: 0.9rem; flex-shrink: 0;
        }

        /* Right Panel - Login Form */
        .right-panel {
            width: 100%;
            max-width: 460px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            position: relative;
            z-index: 1;
        }
        @media(min-width:900px) { .right-panel { max-width: 480px; padding: 40px; } }

        .login-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px 36px;
            width: 100%;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.1);
        }

        .login-logo {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: white;
            margin-bottom: 24px;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }

        .login-card h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.6rem; font-weight: 700;
            color: #1e293b; margin-bottom: 6px;
        }
        .login-card p.subtitle { color: #64748b; font-size: 0.875rem; margin-bottom: 28px; }

        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-weight: 500; font-size: 0.85rem; color: #374151; margin-bottom: 6px; }
        .input-group-custom { position: relative; }
        .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; z-index: 2; }
        .form-input {
            width: 100%; padding: 12px 14px 12px 40px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.9rem;
            color: #1e293b;
            transition: all 0.2s;
            background: #fff;
            outline: none;
        }
        .form-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .form-input.is-invalid { border-color: #ef4444; }

        .toggle-password {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; cursor: pointer; background: none; border: none; font-size: 0.9rem; z-index: 2;
        }
        .toggle-password:hover { color: #4f46e5; }

        .remember-forgot { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: #64748b; cursor: pointer; }
        .checkbox-label input { width: 16px; height: 16px; accent-color: #4f46e5; }
        .forgot-link { font-size: 0.85rem; color: #4f46e5; text-decoration: none; font-weight: 500; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4); }
        .btn-login:active { transform: translateY(0); }

        .divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; color: #94a3b8; font-size: 0.8rem; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }

        .register-link { text-align: center; font-size: 0.875rem; color: #64748b; }
        .register-link a { color: #4f46e5; text-decoration: none; font-weight: 600; }

        .error-msg { background: #fee2e2; color: #991b1b; border-radius: 8px; padding: 10px 14px; font-size: 0.85rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

        /* Demo accounts pills */
        .demo-accounts { background: #f8fafc; border-radius: 10px; padding: 14px; margin-top: 20px; border: 1px dashed #e2e8f0; }
        .demo-accounts h6 { font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 10px; }
        .demo-pill {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 20px; padding: 4px 10px; font-size: 0.75rem; color: #374151;
            margin: 3px; cursor: pointer; transition: all 0.2s;
        }
        .demo-pill:hover { border-color: #4f46e5; color: #4f46e5; }
        .demo-dot { width: 8px; height: 8px; border-radius: 50%; }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Left Panel -->
    <div class="left-panel">
        <div class="tag">
            <i class="fas fa-shield-alt"></i>
            Enterprise RBAC Systemssc
        </div>
        <h1>Control Access.<br><span>Secure Your</span><br>Application.</h1>
        <p>A powerful Role-Based Access Control system with fine-grained permissions, user management, and real-time activity tracking.</p>

        <div class="feature-list">
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-users"></i></div>
                <span>Complete User Management with hierarchical access</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <span>Role-Based Permissions with Gate authorization</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-history"></i></div>
                <span>Detailed Activity Logging for all user actions</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-cog"></i></div>
                <span>Dynamic Site Settings with logo and contact info</span>
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2>Welcome Again</h2>
            <p class="subtitle">Sign in to your account to continue</p>

            @if($errors->any())
                <div class="error-msg">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group">
                    <label>Email or Username</label>
                    <div class="input-group-custom">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="email" value="{{ old('login') }}" class="form-input @error('login') is-invalid @enderror" placeholder="Enter email or username" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group-custom">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" class="form-input @error('password') is-invalid @enderror" placeholder="Enter your password" required>
                        <button type="button" class="toggle-password" onclick="togglePwd()">
                            <i class="fas fa-eye" id="pwd-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="remember-forgot">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                </button>
            </form>

            @if(\App\Models\SiteSetting::get('registration_enabled', '1'))
            <div class="divider">or</div>
            <p class="register-link">
                Don't have an account? <a href="{{ route('register') }}">Create one</a>
            </p>
            @endif

            <!-- Demo Accounts -->
            <div class="demo-accounts">
                <h6>Demo Accounts (Password: password)</h6>
                <div>
                    <span class="demo-pill" onclick="fillLogin('superadmin@rbac.com')">
                        <span class="demo-dot" style="background:#dc3545;"></span> Super Admin
                    </span>
                    <span class="demo-pill" onclick="fillLogin('admin@rbac.com')">
                        <span class="demo-dot" style="background:#007bff;"></span> Admin
                    </span>
                    <span class="demo-pill" onclick="fillLogin('manager@rbac.com')">
                        <span class="demo-dot" style="background:#28a745;"></span> Manager
                    </span>
                    <span class="demo-pill" onclick="fillLogin('editor@rbac.com')">
                        <span class="demo-dot" style="background:#fd7e14;"></span> Editor
                    </span>
                    <span class="demo-pill" onclick="fillLogin('viewer@rbac.com')">
                        <span class="demo-dot" style="background:#6c757d;"></span> Viewer
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script>
    function togglePwd() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('pwd-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    function fillLogin(email) {
        document.querySelector('[name=login]').value = email;
        document.querySelector('[name=password]').value = 'password';
    }
    </script>
</body>
</html>
