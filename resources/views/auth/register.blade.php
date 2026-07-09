<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A.E.G.I.S. | Create Account</title>
    <link rel="icon" type="image/webp" href="{{ asset('logo.webp') }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
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
            background: var(--green);
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

        /* Circle accent */
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
            opacity: 0.75;
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
        .feature-sub   { font-size: 0.75rem; opacity: 0.65; margin: 0; }

        /* ── PROCESS STEPS ────────────────────────── */
        .process-steps {
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .process-step {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .step-num {
            width: 22px;
            height: 22px;
            min-width: 22px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.68rem;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
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
            overflow-y: auto;
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

        /* Password strength bar */
        .strength-bar {
            height: 4px;
            border-radius: 4px;
            background: #e2e8f0;
            margin-top: 6px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            border-radius: 4px;
            width: 0%;
            transition: width 0.3s, background 0.3s;
        }

        .strength-label {
            font-size: 0.72rem;
            margin-top: 4px;
            color: #475569;
            transition: color 0.2s;
        }

        /* Submit button */
        .btn-register {
            background: var(--green);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }

        .btn-register:hover {
            background: var(--green-dark);
            color: var(--gold-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(12,78,45,0.25);
        }

        .btn-register:active { transform: translateY(0); }

        /* Email hint */
        .email-hint {
            font-size: 0.75rem;
            color: #475569;
            margin-top: 5px;
            padding-left: 2px;
        }

        .email-hint strong { color: #475569; }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-panel { width: 100%; min-height: 45vh; padding: 2.5rem 1.5rem; }
            .form-panel  { width: 100%; padding: 2rem 1.5rem; }
        }

        @media (max-width: 480px) {
            .hero-panel { padding: 2rem 1.25rem; }
        }
    </style>
</head>
<body>

<main>
<div class="split">

    <!-- ── LEFT: HERO PANEL ─────────────────────────── -->
    <div class="hero-panel">

        <div class="hero-top">

            <!-- Badge -->
            <div class="inst-badge">
                <span class="live-dot"></span>
                <i class="fa-solid fa-building-columns me-1"></i>
                CLSU Office of Student Affairs
            </div>

            <h1 class="hero-title">
                Create Your Account.<br>
                <span class="gold">Start Your Application.</span>
            </h1>

            <p class="hero-desc">
                Registration is restricted to CLSU institutional email addresses. Once registered, verify your email to unlock scholarship applications.
            </p>

            <!-- Feature list -->
            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-envelope-circle-check"></i></div>
                    <div>
                        <p class="feature-title">Institutional Email Only</p>
                        <p class="feature-sub">Only @clsu.edu.ph and @clsu2.edu.ph domains accepted.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-shield-check"></i></div>
                    <div>
                        <p class="feature-title">Email Verification Required</p>
                        <p class="feature-sub">Confirms ownership to block unauthorized access.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-lock"></i></div>
                    <div>
                        <p class="feature-title">Encrypted Profile Storage</p>
                        <p class="feature-sub">Personal data encrypted at rest and in transit.</p>
                    </div>
                </div>
            </div>

            <!-- Application process steps -->
            <div class="process-steps">
                <div class="process-step">
                    <span class="step-num">1</span>
                    <span>Register with your CLSU email address</span>
                </div>
                <div class="process-step">
                    <span class="step-num">2</span>
                    <span>Verify your email via the confirmation link</span>
                </div>
                <div class="process-step">
                    <span class="step-num">3</span>
                    <span>Complete your profile and submit an application</span>
                </div>
            </div>

        </div>

        <!-- Hero footer -->
        <div class="hero-footer mt-4">
            <div class="brand-circle">
                <i class="fa-solid fa-shield-halved text-white"></i>
            </div>
            <div>
                <div class="brand-name">A.E.G.I.S. Portal</div>
                <div class="brand-sub">Automated Evaluation & Grading Intelligence System</div>
            </div>
        </div>

    </div>

    <!-- ── RIGHT: FORM PANEL ──────────────────────────── -->
    <div class="form-panel">
        <div class="form-box">

            <div class="mb-4">
                <h2 class="h4 fw-bold text-dark mb-1">Get Started</h2>
                <p class="text-muted" style="font-size: 0.88rem;">Enter your details to register as a student.</p>
            </div>

            @if($errors->any())
                <div class="alert py-2 px-3 mb-3 border-0 rounded-3" style="background: #fef2f2; color: #b91c1c; font-size: 0.84rem;">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Full Name -->
                <div class="form-floating mb-3">
                    <input type="text" name="name" id="nameInput"
                           class="form-control" placeholder="Juan Dela Cruz"
                           value="{{ old('name') }}" required autofocus autocomplete="name">
                    <label for="nameInput">
                        <i class="fa-solid fa-user me-2 text-muted" style="font-size: 0.8rem;"></i>Full Name
                    </label>
                </div>

                <!-- CLSU Email -->
                <div class="form-floating mb-1">
                    <input type="email" name="email" id="emailInput"
                           class="form-control" placeholder="student@clsu2.edu.ph"
                           value="{{ old('email') }}" required autocomplete="email"
                           oninput="validateEmailDomain(this)">
                    <label for="emailInput">
                        <i class="fa-solid fa-envelope me-2 text-muted" style="font-size: 0.8rem;"></i>CLSU Student Email
                    </label>
                </div>
                <p class="email-hint mb-3">
                    Must end in <strong>@clsu.edu.ph</strong> or <strong>@clsu2.edu.ph</strong>
                    <span id="emailOk" class="text-success ms-1 d-none"><i class="fa-solid fa-circle-check"></i></span>
                </p>

                <!-- Password -->
                <div class="form-floating mb-1" style="position: relative;">
                    <input type="password" name="password" id="passwordInput"
                           class="form-control" placeholder="Password"
                           required autocomplete="new-password"
                           oninput="checkStrength(this.value)"
                           style="padding-right: 48px;">
                    <label for="passwordInput">
                        <i class="fa-solid fa-lock me-2 text-muted" style="font-size: 0.8rem;"></i>Password
                    </label>
                    <button type="button" onclick="togglePwd('passwordInput', 'eyeIcon1')"
                            style="position:absolute;top:50%;right:14px;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;z-index:10;">
                        <i class="fa-solid fa-eye" id="eyeIcon1"></i>
                    </button>
                </div>
                <!-- Strength bar -->
                <div class="strength-bar mb-1">
                    <div class="strength-fill" id="strengthFill"></div>
                </div>
                <p class="strength-label mb-3" id="strengthLabel">Enter a password</p>

                <!-- Confirm Password -->
                <div class="form-floating mb-4" style="position: relative;">
                    <input type="password" name="password_confirmation" id="confirmInput"
                           class="form-control" placeholder="Confirm Password"
                           required autocomplete="new-password"
                           oninput="checkMatch()"
                           style="padding-right: 48px;">
                    <label for="confirmInput">
                        <i class="fa-solid fa-lock me-2 text-muted" style="font-size: 0.8rem;"></i>Confirm Password
                    </label>
                    <button type="button" onclick="togglePwd('confirmInput', 'eyeIcon2')"
                            style="position:absolute;top:50%;right:14px;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;z-index:10;">
                        <i class="fa-solid fa-eye" id="eyeIcon2"></i>
                    </button>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-register w-100 d-flex justify-content-center align-items-center gap-2"
                        onclick="showSpinner()">
                    <i class="fa-solid fa-user-plus" id="regIcon"></i>
                    <span id="regText">Register Account</span>
                    <span id="regSpinner" class="spinner-border spinner-border-sm d-none"></span>
                </button>

                <p class="text-center text-muted mt-3 mb-0" style="font-size: 0.83rem;">
                    Already registered?
                    <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color: var(--green);">Log in instead</a>
                </p>
            </form>

        </div>
    </div>

</div>

<script>
    /* ── Password Toggle ──────────────────────── */
    function togglePwd(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    /* ── Password Strength ────────────────────── */
    function checkStrength(val) {
        const fill  = document.getElementById('strengthFill');
        const label = document.getElementById('strengthLabel');

        let score = 0;
        if (val.length >= 8)              score++;
        if (/[A-Z]/.test(val))            score++;
        if (/[0-9]/.test(val))            score++;
        if (/[^A-Za-z0-9]/.test(val))     score++;

        const map = {
            0: { w: '0%',   bg: '#e2e8f0', txt: 'Enter a password',      col: '#94a3b8' },
            1: { w: '25%',  bg: '#ef4444', txt: 'Weak — too short',       col: '#ef4444' },
            2: { w: '50%',  bg: '#f59e0b', txt: 'Fair — add numbers',     col: '#b45309' },
            3: { w: '75%',  bg: '#3b82f6', txt: 'Good — almost there',    col: '#1d4ed8' },
            4: { w: '100%', bg: '#22c55e', txt: 'Strong ✓',               col: '#15803d' },
        };

        const s = map[Math.min(score, 4)];
        fill.style.width      = val.length === 0 ? '0%' : s.w;
        fill.style.background = s.bg;
        label.textContent     = val.length === 0 ? 'Enter a password' : s.txt;
        label.style.color     = val.length === 0 ? '#94a3b8' : s.col;
    }

    /* ── Confirm Password Match ───────────────── */
    function checkMatch() {
        const pw  = document.getElementById('passwordInput').value;
        const cfm = document.getElementById('confirmInput');
        if (cfm.value.length === 0) { cfm.style.borderColor = ''; return; }
        cfm.style.borderColor = pw === cfm.value ? '#22c55e' : '#ef4444';
    }

    /* ── Email Domain Check ───────────────────── */
    function validateEmailDomain(input) {
        const ok = document.getElementById('emailOk');
        const valid = /(@clsu\.edu\.ph|@clsu2\.edu\.ph)$/i.test(input.value);
        input.style.borderColor = input.value.length > 5 ? (valid ? '#22c55e' : '') : '';
        ok.classList.toggle('d-none', !valid);
    }

    /* ── Submit Spinner ───────────────────────── */
    function showSpinner() {
        setTimeout(() => {
            document.getElementById('regText').textContent = 'Creating account...';
            document.getElementById('regSpinner').classList.remove('d-none');
            document.getElementById('regIcon').style.display = 'none';
        }, 10);
    }
</script>

</main>
</body>
</html>
