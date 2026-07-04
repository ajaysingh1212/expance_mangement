<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify PIN | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4c1d95 100%);
            overflow: hidden;
        }

        /* ── Animated background ── */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            animation: drift 20s infinite ease-in-out;
        }
        .bg-orb-1 {
            width: 500px; height: 500px;
            top: -150px; left: -150px;
            background: radial-gradient(circle, rgba(99,102,241,.25) 0%, transparent 70%);
            animation-duration: 24s;
        }
        .bg-orb-2 {
            width: 400px; height: 400px;
            bottom: -100px; right: -100px;
            background: radial-gradient(circle, rgba(139,92,246,.2) 0%, transparent 70%);
            animation-duration: 18s; animation-delay: -9s;
        }
        .bg-orb-3 {
            width: 200px; height: 200px;
            top: 40%; left: 40%;
            background: radial-gradient(circle, rgba(79,70,229,.15) 0%, transparent 70%);
            animation-duration: 15s; animation-delay: -5s;
        }
        @keyframes drift {
            0%,100% { transform: translate(0,0) scale(1); }
            33%      { transform: translate(20px,-30px) scale(1.05); }
            66%      { transform: translate(-15px,20px) scale(.96); }
        }

        /* ── Card ── */
        .pin-card {
            background: rgba(255,255,255,.97);
            border-radius: 28px;
            padding: 44px 40px 36px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 30px 80px rgba(0,0,0,.35), 0 0 0 1px rgba(255,255,255,.12);
            position: relative;
            z-index: 1;
            animation: slideUp .4s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px) scale(.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Avatar ── */
        .avatar-wrap {
            display: flex; flex-direction: column;
            align-items: center; margin-bottom: 24px;
        }
        .avatar-ring {
            width: 80px; height: 80px; border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #fff;
            box-shadow: 0 8px 24px rgba(79,70,229,.35);
            margin-bottom: 14px; position: relative;
            overflow: hidden;
        }
        .avatar-ring img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .lock-badge {
            position: absolute; bottom: -2px; right: -2px;
            width: 26px; height: 26px; background: #fff;
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,.15);
        }
        .lock-badge i { font-size: .7rem; color: #4f46e5; }
        .user-name { font-family: 'Poppins', sans-serif; font-size: 1.1rem; font-weight: 700; color: #1e293b; }
        .user-hint { font-size: .78rem; color: #64748b; margin-top: 2px; }

        /* ── Heading ── */
        .pin-title { text-align: center; font-family: 'Poppins', sans-serif; font-size: 1.3rem; font-weight: 800; color: #1e293b; margin-bottom: 4px; }
        .pin-sub   { text-align: center; font-size: .82rem; color: #64748b; margin-bottom: 30px; }

        /* ── PIN Dots ── */
        .pin-dots {
            display: flex; justify-content: center; gap: 14px; margin-bottom: 28px;
        }
        .pin-dot {
            width: 16px; height: 16px; border-radius: 50%;
            background: #e2e8f0;
            transition: background .2s, transform .2s;
        }
        .pin-dot.filled {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            transform: scale(1.1);
            box-shadow: 0 0 0 3px rgba(79,70,229,.2);
        }
        .pin-dot.error {
            background: #ef4444;
            animation: shake .35s ease;
        }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%      { transform: translateX(-6px); }
            40%      { transform: translateX(6px); }
            60%      { transform: translateX(-4px); }
            80%      { transform: translateX(4px); }
        }

        /* ── Numpad ── */
        .numpad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }
        .num-btn {
            height: 62px;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            background: #f8fafc;
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e293b;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column;
            transition: all .15s ease;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }
        .num-btn sub { font-size: .5rem; font-weight: 500; color: #94a3b8; letter-spacing: .08em; margin-top: 1px; }
        .num-btn:hover  { background: #eff6ff; border-color: #c7d2fe; color: #4f46e5; }
        .num-btn:active { background: #e0e7ff; transform: scale(.94); }
        .num-btn.del-btn { font-size: 1.1rem; color: #64748b; }
        .num-btn.del-btn:hover { color: #dc2626; border-color: #fca5a5; background: #fef2f2; }
        .num-btn.enter-btn {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff; border: none; font-size: 1.1rem;
            box-shadow: 0 4px 14px rgba(79,70,229,.3);
        }
        .num-btn.enter-btn:hover  { box-shadow: 0 6px 20px rgba(79,70,229,.4); transform: translateY(-1px); }
        .num-btn.enter-btn:active { transform: scale(.94); box-shadow: none; }
        .num-btn.enter-btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }

        /* ── Error ── */
        .error-strip {
            background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px;
            padding: 10px 14px; font-size: .82rem; color: #dc2626;
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 16px; animation: fadeIn .25s ease;
        }
        @keyframes fadeIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }

        /* ── Attempts bar ── */
        .attempts-bar {
            display: flex; justify-content: center; gap: 6px; margin-bottom: 20px;
        }
        .attempt-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #fca5a5;
            transition: background .2s;
        }
        .attempt-dot.used { background: #ef4444; }

        /* ── Footer actions ── */
        .pin-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; }
        .link-btn {
            background: none; border: none; cursor: pointer;
            font-size: .8rem; color: #64748b; display: flex; align-items: center; gap: 5px;
            padding: 6px 10px; border-radius: 8px; transition: all .15s;
            text-decoration: none;
        }
        .link-btn:hover { color: #4f46e5; background: #eff6ff; }

        /* ── Hidden real input ── */
        #realPin { position: absolute; opacity: 0; pointer-events: none; width: 1px; height: 1px; }
    </style>
</head>
<body>
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>
    <div class="bg-orb bg-orb-3"></div>

    <div class="pin-card">

        {{-- Avatar + Name --}}
        <div class="avatar-wrap">
            <div class="avatar-ring">
                @if($userAvatar ?? null)
                    <img src="{{ asset('storage/' . $userAvatar) }}" alt="{{ $userName }}">
                @else
                    <i class="fas fa-user"></i>
                @endif
                <div class="lock-badge"><i class="fas fa-lock"></i></div>
            </div>
            <div class="user-name">{{ $userName }}</div>
            <div class="user-hint">Enter your 4-digit PIN to continue</div>
        </div>

        {{-- PIN Dots --}}
        <div class="pin-dots" id="pinDots">
            <div class="pin-dot" id="dot-0"></div>
            <div class="pin-dot" id="dot-1"></div>
            <div class="pin-dot" id="dot-2"></div>
            <div class="pin-dot" id="dot-3"></div>
        </div>

        {{-- Error Strip --}}
        @if($errors->any())
        <div class="error-strip" id="errorStrip">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
        @endif

        {{-- Attempts visual --}}
        @php $attempts = session('pin_attempts', 0); @endphp
        @if($attempts > 0)
        <div class="attempts-bar">
            @for($i = 0; $i < 5; $i++)
            <div class="attempt-dot {{ $i < $attempts ? 'used' : '' }}"></div>
            @endfor
        </div>
        @endif

        {{-- Numpad --}}
        <div class="numpad" id="numpad">
            <button class="num-btn" data-digit="1">1<sub></sub></button>
            <button class="num-btn" data-digit="2">2<sub>ABC</sub></button>
            <button class="num-btn" data-digit="3">3<sub>DEF</sub></button>
            <button class="num-btn" data-digit="4">4<sub>GHI</sub></button>
            <button class="num-btn" data-digit="5">5<sub>JKL</sub></button>
            <button class="num-btn" data-digit="6">6<sub>MNO</sub></button>
            <button class="num-btn" data-digit="7">7<sub>PQRS</sub></button>
            <button class="num-btn" data-digit="8">8<sub>TUV</sub></button>
            <button class="num-btn" data-digit="9">9<sub>WXYZ</sub></button>
            <button class="num-btn del-btn" id="delBtn"><i class="fas fa-delete-left"></i></button>
            <button class="num-btn" data-digit="0">0</button>
            <button class="num-btn enter-btn" id="enterBtn" disabled>
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>

        {{-- Footer --}}
        <div class="pin-actions">
            <form action="{{ route('pin.switch') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="link-btn">
                    <i class="fas fa-right-left"></i> Switch Account
                </button>
            </form>
            <a href="{{ route('logout') }}" class="link-btn"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Sign Out
            </a>
        </div>

        {{-- Hidden form --}}
        <form id="pinForm" method="POST" action="{{ route('pin.verify') }}">
            @csrf
            <input type="hidden" name="pin" id="pinInput">
        </form>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    </div>

    <script>
    (function () {
        let pin = '';
        const MAX = 4;

        const dots     = [0,1,2,3].map(i => document.getElementById('dot-' + i));
        const enterBtn = document.getElementById('enterBtn');
        const delBtn   = document.getElementById('delBtn');

        // ── Render dots ──────────────────────────────────────────────
        function renderDots(error = false) {
            dots.forEach((dot, i) => {
                dot.classList.toggle('filled', i < pin.length);
                dot.classList.toggle('error', error);
            });
        }

        // ── Add digit ────────────────────────────────────────────────
        function addDigit(d) {
            if (pin.length >= MAX) return;
            pin += d;
            renderDots();
            enterBtn.disabled = pin.length < MAX;

            // Auto-submit when 4 digits entered
            if (pin.length === MAX) {
                setTimeout(submit, 180);
            }
        }

        // ── Delete ───────────────────────────────────────────────────
        function deleteDigit() {
            if (!pin.length) return;
            pin = pin.slice(0, -1);
            renderDots();
            enterBtn.disabled = true;
        }

        // ── Submit ───────────────────────────────────────────────────
        function submit() {
            document.getElementById('pinInput').value = pin;
            document.getElementById('pinForm').submit();
        }

        // ── Numpad clicks ─────────────────────────────────────────────
        document.getElementById('numpad').addEventListener('click', e => {
            const btn = e.target.closest('.num-btn');
            if (!btn) return;

            if (btn.id === 'delBtn')   { deleteDigit(); return; }
            if (btn.id === 'enterBtn') { if (pin.length === MAX) submit(); return; }

            const d = btn.dataset.digit;
            if (d !== undefined) addDigit(d);
        });

        // ── Keyboard support ─────────────────────────────────────────
        document.addEventListener('keydown', e => {
            if (e.key >= '0' && e.key <= '9')   addDigit(e.key);
            if (e.key === 'Backspace')           deleteDigit();
            if (e.key === 'Enter' && pin.length === MAX) submit();
        });

        // ── Shake on error (if errors from server) ───────────────────
        @if($errors->any())
        renderDots(true);
        setTimeout(() => renderDots(false), 400);
        @endif
    })();
    </script>
</body>
</html>
