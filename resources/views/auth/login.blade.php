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
            --clsu-green: #0C4E2D;
            --clsu-green-dark: #07331c;
            --clsu-green-light: #126b3f;
            --clsu-green-accent: #00754A;
            --clsu-gold: #D97706;
            --clsu-gold-light: #fcd34d;
            --clsu-bg: #f2f0eb;
            --card-bg: #ffffff;
            --text-main: rgba(0, 0, 0, 0.87);
            --border-color: #edebe9;

            /* Backward compatibility aliases */
            --green: var(--clsu-green);
            --green-dark: var(--clsu-green-dark);
            --green-accent: var(--clsu-green-accent);
            --gold: var(--clsu-gold);
            --gold-light: var(--clsu-gold-light);
            --bg-warm: var(--clsu-bg);
        }

        [data-theme="dark"] {
            --clsu-bg: #0b0f19;
            --card-bg: #111827;
            --text-main: #94a3b8;
            --border-color: rgba(255,255,255,0.07);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-warm);
            margin: 0;
            overflow-x: hidden;
            letter-spacing: -0.01em;
        }

        h1,h2,h3,h4,h5 {
            font-family: 'Poppins', sans-serif;
            letter-spacing: -0.02em;
        }

        /* ── SPLIT LAYOUT ────────────────────────────── */
        /* WCAG 1.4.10 Reflow: 100dvh accounts for iOS Safari URL bar offset */
        .split {
            min-height: 100dvh;
            display: flex;
            flex-wrap: wrap;
        }

        /* ── LEFT HERO PANEL ──────────────────────── */
        /* WCAG 1.4.10 Reflow: fluid flex sizing instead of fixed 55%
           Degrades gracefully from 55% on desktop to full-width below 991px */
        .hero-panel {
            flex: 1 1 min(55%, 560px);
            background: var(--green-dark);
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
            background: var(--bg-warm); /* Neutral Warm canvas */
        }

        .form-box {
            width: 100%;
            max-width: 400px;
            animation: fadeUp 0.6s ease-out both;
            opacity: 0;
            transform: translateY(16px);
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 0 0.5px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.18);
        }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Inputs */
        .form-floating > .form-control {
            border: 1.5px solid #d6dbde;
            border-radius: 8px; /* Standard input radius */
            background: #ffffff;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-floating > .form-control:focus {
            border-color: var(--green-accent);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0,117,74,0.12);
        }

        .form-floating > label { color: #475569 !important; opacity: 1 !important; font-weight: 600; font-size: 0.9rem; }
        .form-floating > .form-control:focus ~ label { color: var(--green-accent) !important; opacity: 1 !important; }

        /* Login button */
        .btn-login {
            background: var(--green-accent);
            color: white;
            border: none;
            border-radius: var(--radius-pill, 50px); /* Starbucks full pill standard */
            padding: 13px 28px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 117, 74, 0.15);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            background: var(--green);
            color: white;
            box-shadow: 0 8px 24px rgba(0, 117, 74, 0.25);
        }

        .btn-login:active {
            transform: scale(0.95) !important;
        }

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

        /* ── PHASE 2 WCAG ADDITIONS ────────────────────── */

        /* WCAG 1.4.4 Resize Text / 1.4.10 Reflow: fluid type scale via clamp()
           Text scales proportionally without horizontal scroll at any viewport width */
        .hero-title   { font-size: clamp(1.5rem, 4vw + 0.5rem, 2.8rem); }
        .hero-desc    { font-size: clamp(0.85rem, 1.5vw, 1rem); }
        .inst-badge   { font-size: clamp(0.7rem, 1.5vw, 0.8rem); }

        /* WCAG 1.4.11 Non-text Contrast: autofill override
           Prevents browser autofill white flash that breaks form aesthetics */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #fff inset !important;
            -webkit-text-fill-color: #1a1a1a !important;
            transition: background-color 5000s ease-in-out 0s !important;
        }

        /* WCAG 2.4.7 Focus Visible: keyboard ring on inputs (matches global system) */
        .form-control:focus-visible,
        .form-select:focus-visible {
            outline: 3px solid var(--green) !important;
            outline-offset: 2px !important;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-panel { flex: 1 1 100%; min-height: 44vh; padding: 2.5rem 1.5rem; }
            .form-panel { flex: 1 1 100%; padding: 2rem 1.5rem; overflow-y: auto; max-height: 100dvh; }
            .scan-terminal { display: none; }
        }

        @media (max-width: 480px) {
            .hero-panel { padding: 2rem 1.25rem; min-height: 38vh; }
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

            @if(isset($emergencyReadOnly) && $emergencyReadOnly)
                <div class="alert border-0 rounded-3 mb-4 p-3 animate-fade-in" style="background: #fffbeb; color: #b45309; border-left: 4px solid #d97706 !important;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <strong>Emergency Read-Only Mode</strong>
                    </div>
                    <p class="small mb-0">The application database is temporarily offline. Sign in is disabled, but you can review portal layouts and configurations.</p>
                </div>
            @endif

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

    <x-auth-modal :emergencyReadOnly="$emergencyReadOnly ?? false" />
</main>
</body>
</html>