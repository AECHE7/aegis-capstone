<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} | Secure Gateway</title>
    <meta name="description" content="Securely sign in to the {{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} {{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }} Scholarship Portal to apply for grants, check your application queue, and verify grades.">
    <link rel="icon" type="image/webp" href="{{ \App\Models\Setting::get('app_logo') ? route('system.logo') : asset('logo.webp') }}">
    <link rel="icon" type="image/png" href="{{ \App\Models\Setting::get('app_logo') ? route('system.logo') : asset('logo.png') }}">

    {{-- Preconnect hints --}}
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Bootstrap CSS: non-blocking preload --}}
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"></noscript>

    {{-- Font Awesome CSS: non-blocking preload --}}
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
    <style>
        @font-face { font-family: "Font Awesome 6 Free"; font-display: swap; }
        @font-face { font-family: "Font Awesome 6 Free Solid"; font-display: swap; }
        @font-face { font-family: "Font Awesome 6 Brands"; font-display: swap; }
    </style>


    {{-- Non-blocking Google Fonts --}}
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet"></noscript>

    <style>
        :root {
            --green: #0C4E2D;
            --green-dark: #07331c;
            --gold: #D97706;
            --gold-light: #fbbf24;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #fff;
            margin: 0;
            overflow-x: hidden;
        }

        h1,h2,h3,h4,h5 { font-family: 'Poppins', sans-serif; }

        /* ── SPLIT LAYOUT ─────────────────────────── */
        .split {
            min-height: 100vh;
            display: flex;
            flex-wrap: wrap;
        }

        /* ── LEFT HERO PANEL ──────────────────────── */
        .hero-panel {
            width: 55%;
            background: linear-gradient(135deg, var(--green-dark) 0%, var(--green) 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3.5rem;
            position: relative;
            overflow: hidden;
            color: white;
        }

        /* Dot grid background */
        .hero-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.08) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        /* Subtle diagonal accent line */
        .hero-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.06);
            pointer-events: none;
        }

        .hero-top { position: relative; z-index: 1; }

        /* Institution badge */
        .inst-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 28px;
        }

        /* Live dot */
        .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #4ade80;
            animation: pulse 2s ease infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.8); }
        }

        .hero-title {
            font-size: clamp(2rem, 4vw, 2.8rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.5px;
            margin-bottom: 16px;
        }

        .hero-title .gold { color: var(--gold-light); }

        .hero-desc {
            font-size: 0.9rem;
            line-height: 1.7;
            opacity: 0.88;
            max-width: 460px;
            margin-bottom: 32px;
        }

        /* Feature rows */
        .feature-list { display: flex; flex-direction: column; gap: 12px; }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.04);
            transition: background 0.2s, border-color 0.2s, transform 0.2s;
            cursor: default;
        }

        .feature-item:hover {
            background: rgba(255,255,255,0.09);
            border-color: rgba(255,255,255,0.2);
            transform: translateX(4px);
        }

        .feature-icon {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 9px;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: var(--gold-light);
        }

        .feature-title { font-size: 0.85rem; font-weight: 700; margin: 0; }
        .feature-sub { font-size: 0.75rem; opacity: 0.85; margin: 0; }

        /* ── STATS ROW ────────────────────────────── */
        .stats-row {
            display: flex;
            gap: 0;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.1);
            position: relative;
            z-index: 1;
        }

        .stat-item { flex: 1; text-align: center; }
        .stat-item + .stat-item { border-left: 1px solid rgba(255,255,255,0.1); }

        .stat-val {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--gold-light);
            font-variant-numeric: tabular-nums;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-lbl { font-size: 0.68rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px; }

        /* ── SCAN TERMINAL ────────────────────────── */
        .scan-terminal {
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 14px 16px;
            margin-top: 28px;
            position: relative;
            z-index: 1;
            font-family: 'SF Mono', 'Fira Code', 'Courier New', monospace;
            font-size: 0.73rem;
            line-height: 1.7;
            overflow: hidden;
        }

        .terminal-bar {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
        }

        .t-dot { width: 8px; height: 8px; border-radius: 50%; }
        .t-dot.r { background: #ef4444; }
        .t-dot.y { background: #eab308; }
        .t-dot.g { background: #22c55e; }

        .t-label { font-size: 0.65rem; color: rgba(255,255,255,0.4); margin-left: 6px; }

        .t-line { display: block; white-space: nowrap; overflow: hidden; }
        .t-cmd  { color: rgba(255,255,255,0.85); }
        .t-info { color: var(--gold-light); }
        .t-ok   { color: #4ade80; font-weight: 700; }
        .t-muted { color: rgba(255,255,255,0.4); }

        /* Blinking cursor */
        .cursor {
            display: inline-block;
            width: 7px;
            height: 12px;
            background: #4ade80;
            vertical-align: middle;
            animation: blink 1s step-start infinite;
        }

        @keyframes blink { 50% { opacity: 0; } }

        /* Scan sweep line */
        .sweep-line {
            position: absolute;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #4ade80, transparent);
            animation: sweep 4s linear infinite;
            top: 0;
        }

        @keyframes sweep {
            0%   { top: 0; opacity: 0; }
            5%   { opacity: 1; }
            95%  { opacity: 0.6; }
            100% { top: 100%; opacity: 0; }
        }

        /* ── HERO FOOTER ──────────────────────────── */
        .hero-footer {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .brand-circle {
            width: 42px;
            height: 42px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .brand-name { font-weight: 700; font-size: 0.9rem; }
        .brand-sub  { font-size: 0.65rem; opacity: 0.6; }

        /* ── RIGHT FORM PANEL ─────────────────────── */
        .form-panel {
            width: 45%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
            background: #fff;
        }

        .form-box {
            width: 100%;
            max-width: 400px;
            animation: fadeUp 0.6s ease-out both;
            opacity: 0;
            transform: translateY(16px);
        }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Inputs */
        .form-floating > .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-floating > .form-control:focus {
            border-color: var(--green);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(12,78,45,0.1);
        }

        .form-floating > label { color: #334155 !important; opacity: 1 !important; font-weight: 600; font-size: 0.9rem; }
        .form-floating > .form-control:focus ~ label { color: var(--green) !important; opacity: 1 !important; }

        /* Login button */
        .btn-login {
            background: var(--green);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 12px rgba(12, 78, 45, 0.2);
        }

        .btn-login:hover {
            background: var(--green-dark);
            color: var(--gold-light);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(12, 78, 45, 0.35);
        }

        .btn-login:active { transform: translateY(0); }

        /* Divider */
        .or-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #475569;
            font-weight: 600;
            font-size: 0.78rem;
            margin: 24px 0 16px;
        }

        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        /* Quick Access Chips */
        .quick-chip {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 8px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            transition: border-color 0.2s, background 0.2s, color 0.2s, transform 0.15s;
        }

        .quick-chip:hover {
            border-color: var(--green);
            background: rgba(12,78,45,0.04);
            color: var(--green);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-panel { width: 100%; min-height: 50vh; padding: 2.5rem 1.5rem; }
            .form-panel  { width: 100%; padding: 2rem 1.5rem; }
            .scan-terminal { display: none; }
        }

        @media (max-width: 480px) {
            .hero-panel { padding: 2rem 1.25rem; }
            .stats-row  { gap: 0; }
        }
    </style>
</head>
<body>

<main>
<div class="split">

    <!-- ── LEFT: HERO PANEL ─────────────────────────── -->
    <div class="hero-panel">

        <div class="hero-top">

            <!-- Institution badge -->
            <div class="inst-badge">
                <span class="live-dot"></span>
                <i class="fa-solid fa-building-columns me-1"></i>
                CLSU Office of Student Affairs
            </div>

            <h1 class="hero-title">
                Your Gateway to<br>
                <span class="gold">Scholarship Grants.</span>
            </h1>

            <p class="hero-desc">
                {{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} streamlines scholarship applications at {{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }}, providing a direct, paperless, and secure portal for students and the Office of Student Affairs.
            </p>

            <!-- Feature list -->
            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-award"></i></div>
                    <div>
                        <p class="feature-title">Institutional & Private Grants</p>
                        <p class="feature-sub">Access to University, CHED, and DOST-SEI scholarships.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-bolt"></i></div>
                    <div>
                        <p class="feature-title">Real-Time Tracking</p>
                        <p class="feature-sub">Follow your application status from submission to approval instantly.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-user-check"></i></div>
                    <div>
                        <p class="feature-title">Digital Profile Management</p>
                        <p class="feature-sub">Keep your verified student information up to date securely.</p>
                    </div>
                </div>
            </div>

            <!-- Welcome Info Block -->
            <div class="mt-4 p-4 border border-white-50 rounded-4 text-start" style="background: rgba(255,255,255,0.06); backdrop-filter: blur(10px); border-radius: 16px;">
                <p class="h6 fw-bold text-warning mb-2"><i class="fa-solid fa-circle-info me-2"></i> How to Get Started</p>
                <ul class="text-white-50 small ps-3 mb-0" style="line-height: 1.6;">
                    <li>Create your account using your verified CLSU email address.</li>
                    <li>Complete your digital student profile.</li>
                    <li>Apply directly to active scholarship programs.</li>
                    <li>Track updates and receive notifications from the OSA team in real time.</li>
                </ul>
            </div>

        </div>

        <!-- Hero footer -->
        <div class="hero-footer mt-4">
            <div class="brand-circle d-flex align-items-center justify-content-center" style="overflow: hidden;">
                @if(\App\Models\Setting::get('app_logo'))
                    <img src="{{ route('system.logo') }}" style="width: 24px; height: 24px; object-fit: contain;">
                @else
                    <i class="fa-solid fa-shield-halved text-white"></i>
                @endif
            </div>
            <div>
                <div class="brand-name">{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} Portal</div>
                <div class="brand-sub">{{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }}</div>
            </div>
        </div>

    </div>

    <!-- ── RIGHT: FORM PANEL ──────────────────────────── -->
    <div class="form-panel">
        <div class="form-box">

            @auth
                <div class="alert border-0 rounded-3 mb-4 p-3" style="background: #ecfdf5; color: #065f46;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <strong>Active Session</strong>
                    </div>
                    <p class="small mb-3">Logged in as <strong>{{ auth()->user()->name }}</strong> ({{ ucfirst(auth()->user()->role) }}).</p>
                    <div class="d-flex gap-2">
                        @if(auth()->user()->role === 'superadmin')
                            <a href="{{ route('superadmin.analytics') }}" class="btn btn-sm fw-bold px-3" style="background: var(--green); color: white; border-radius: 8px;">Go to Dashboard</a>
                        @elseif(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm fw-bold px-3" style="background: var(--green); color: white; border-radius: 8px;">Go to Dashboard</a>
                        @else
                            <a href="{{ route('student.dashboard') }}" class="btn btn-sm fw-bold px-3" style="background: var(--green); color: white; border-radius: 8px;">Go to Dashboard</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary fw-bold px-3" style="border-radius: 8px;">Logout</button>
                        </form>
                    </div>
                </div>
            @endauth

            <div class="mb-4">
                <h2 class="h4 fw-bold text-dark mb-1">Welcome Back</h2>
                <p class="text-muted" style="font-size: 0.88rem;">Enter your credentials to access the portal.</p>
            </div>

            @if($errors->any())
                <div class="alert py-2 px-3 mb-3 border-0 rounded-3" style="background: #fef2f2; color: #b91c1c; font-size: 0.84rem;">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf

                <div class="form-floating mb-3">
                    <input type="email" name="email" id="emailInput"
                           class="form-control" placeholder="name@clsu.edu.ph"
                           required value="{{ old('email') }}" autocomplete="username">
                    <label for="emailInput">
                        <i class="fa-solid fa-envelope me-2 text-muted" style="font-size: 0.8rem;"></i>Email Address
                    </label>
                </div>

                <div class="form-floating mb-2" style="position: relative;">
                    <input type="password" name="password" id="passwordInput"
                           class="form-control" placeholder="Password"
                           required style="padding-right: 48px;" autocomplete="current-password">
                    <label for="passwordInput">
                        <i class="fa-solid fa-lock me-2 text-muted" style="font-size: 0.8rem;"></i>Password
                    </label>
                    <button type="button" onclick="togglePwd()" aria-label="Toggle password visibility"
                            style="position:absolute;top:50%;right:14px;transform:translateY(-50%);background:none;border:none;color:#475569;cursor:pointer;z-index:10;">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </button>
                </div>

                <div class="d-flex justify-content-end mb-4">
                    <a href="{{ route('password.request') }}" class="text-decoration-none fw-semibold" style="font-size: 0.83rem; color: var(--green);">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-login w-100 d-flex justify-content-center align-items-center gap-2" id="loginBtn">
                    <span id="btnText">Secure Login</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
                    <i class="fa-solid fa-arrow-right-to-bracket" id="btnArrow"></i>
                </button>

                @if(app()->environment('local', 'testing') && ($demoStudent || $demoAdmin || $demoSuperAdmin))
                <div class="or-divider">QUICK DEMO ACCESS</div>
                <div class="row g-2 mb-3">
                    @if($demoStudent)
                    <div class="col-4">
                        <button type="button" class="quick-chip w-100" onclick="fillDemo('{{ $demoStudent->email }}')">
                            <i class="fa-solid fa-user-graduate text-success fs-5"></i>
                            <span class="small fw-semibold" style="font-size:0.72rem;">Student</span>
                        </button>
                    </div>
                    @endif
                    @if($demoAdmin)
                    <div class="col-4">
                        <button type="button" class="quick-chip w-100" onclick="fillDemo('{{ $demoAdmin->email }}')">
                            <i class="fa-solid fa-user-shield text-info fs-5"></i>
                            <span class="small fw-semibold" style="font-size:0.72rem;">OSA Admin</span>
                        </button>
                    </div>
                    @endif
                    @if($demoSuperAdmin)
                    <div class="col-4">
                        <button type="button" class="quick-chip w-100" onclick="fillDemo('{{ $demoSuperAdmin->email }}')">
                            <i class="fa-solid fa-crown text-warning fs-5"></i>
                            <span class="small fw-semibold" style="font-size:0.72rem;">Director</span>
                        </button>
                    </div>
                    @endif
                </div>
                @endif

                <p class="text-center text-muted mt-3 mb-0" style="font-size: 0.83rem;">
                    New student? <a href="{{ route('register') }}" class="fw-bold text-decoration-none" style="color: var(--green);">Create an account</a>
                </p>
            </form>

            @if(app()->environment('local', 'testing'))
            <div class="text-center mt-4 pt-3 border-top">
                <div class="small fw-semibold text-muted mb-2">
                    <i class="fa-solid fa-wand-magic-sparkles me-1 text-warning"></i> Developer Quick Links
                </div>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('register') }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 8px; font-size: 0.75rem;">
                        <i class="fa-solid fa-user-plus me-1"></i> Register Student
                    </a>
                    @if($latestInvitation)
                    <a href="{{ route('activate.form', ['token' => $latestInvitation->token]) }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 8px; font-size: 0.75rem;">
                        <i class="fa-solid fa-key me-1"></i> Activate Staff
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

</div>

<script>
    /* ── Password Toggle ────────────────────────── */
    function togglePwd() {
        const input = document.getElementById('passwordInput');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    /* ── Login Form Submit Spinner ──────────────── */
    document.querySelector('form').addEventListener('submit', function() {
        document.getElementById('btnText').textContent = 'Authenticating...';
        document.getElementById('btnSpinner').classList.remove('d-none');
        document.getElementById('btnArrow').style.display = 'none';
        document.getElementById('loginBtn').disabled = true;
    });

    /* ── Fill Demo Helper ───────────────────────── */
    function fillDemo(email) {
        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = 'password';
        
        // Trigger submit spinner manually and submit the form
        document.getElementById('btnText').textContent = 'Authenticating...';
        document.getElementById('btnSpinner').classList.remove('d-none');
        document.getElementById('btnArrow').style.display = 'none';
        document.getElementById('loginBtn').disabled = true;
        
        setTimeout(() => {
            document.querySelector('form').submit();
        }, 150);
    }
</script>

</main>
</body>
</html>