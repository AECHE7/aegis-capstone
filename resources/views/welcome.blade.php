<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script>
        (function () {
            const savedTheme = localStorage.getItem('aegis-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} — {{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }} Scholarship Portal</title>
    <meta name="description" content="Apply for {{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }} scholarships online. {{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} is the official scholarship management portal with AI-powered grade verification, real-time application tracking, and secure document management.">
    <meta name="keywords" content="scholarship, {{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }}, scholarship portal, {{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }}, scholarship application, Philippines scholarship">
    <meta name="author" content="{{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }} — Office of Student Affairs">
    <meta name="robots" content="index, follow">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Favicon: WebP for modern browsers, PNG fallback --}}
    <link rel="icon" type="image/webp" href="{{ \App\Models\Setting::get('app_logo') ? route('system.logo') : asset('logo.webp') }}">
    <link rel="icon" type="image/png" href="{{ \App\Models\Setting::get('app_logo') ? route('system.logo') : asset('logo.png') }}">

    {{-- Open Graph (Facebook, LinkedIn) --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} — {{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }} Scholarship Portal">
    <meta property="og:description" content="Apply for {{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }} scholarships online. Official scholarship management portal with AI-powered grade verification and real-time application tracking.">
    <meta property="og:image" content="{{ \App\Models\Setting::get('app_logo') ? route('system.logo') : asset('logo.webp') }}">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">
    <meta property="og:locale" content="en_PH">
    <meta property="og:site_name" content="{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} {{ \App\Models\Setting::get('university_name', 'CLSU') }} Scholarship Portal">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} — {{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }} Scholarship Portal">
    <meta name="twitter:description" content="Apply for {{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }} scholarships online with AI-powered grade verification and real-time tracking.">
    <meta name="twitter:image" content="{{ \App\Models\Setting::get('app_logo') ? route('system.logo') : asset('logo.webp') }}">

    {{-- JSON-LD Structured Data --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "A.E.G.I.S. CLSU Scholarship Portal",
        "url": "{{ url('/') }}",
        "description": "Central Luzon State University's official scholarship management portal with AI-powered grade integrity verification.",
        "applicationCategory": "EducationApplication",
        "operatingSystem": "Web Browser",
        "publisher": {
            "@type": "Organization",
            "name": "Central Luzon State University",
            "url": "https://www.clsu.edu.ph",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('logo.webp') }}"
            },
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Science City of Muñoz",
                "addressRegion": "Nueva Ecija",
                "addressCountry": "PH"
            }
        }
    }
    </script>

    {{-- Preconnect to CDN origins --}}
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Non-blocking Google Fonts --}}
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet"></noscript>
    <style>
        :root {
            --bg-main: #f8fafc;
            --bg-section: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #475569;
            --text-title: #0f172a;
            --border-color: #e2e8f0;
            --clsu-green: #0C4E2D;
            --clsu-green-dark: #072F1B;
            --clsu-green-muted: rgba(12, 78, 45, 0.08);
            --clsu-gold: #D97706;
            --clsu-gold-light: #fcd34d;
            --transition: all 0.2s ease;
            --radius-lg: 16px;
            --radius-md: 10px;
            --radius-sm: 7px;
        }

        [data-theme="dark"] {
            --bg-main: #0b0f19;
            --bg-section: #0f172a;
            --card-bg: #111827;
            --text-main: #94a3b8;
            --text-title: #f1f5f9;
            --border-color: rgba(255,255,255,0.07);
            --clsu-green-muted: rgba(20, 83, 45, 0.15);
        }

        *, *::before, *::after { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            transition: background-color 0.2s, color 0.2s;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Poppins', sans-serif;
            color: var(--text-title);
        }

        .mono {
            font-family: 'SF Mono', 'Fira Code', 'Courier New', monospace;
            font-size: 0.78rem;
            letter-spacing: -0.2px;
        }

        /* ── NAVBAR ─────────────────────────────────────── */
        .site-nav {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 999;
            transition: background 0.2s, border-color 0.2s;
        }

        .site-nav .container {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .nav-brand-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--clsu-gold), #b45309);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-brand-icon i { color: #fff; font-size: 0.85rem; }

        .nav-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .nav-brand-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--text-title);
        }

        .nav-brand-sub {
            font-size: 0.6rem;
            color: var(--text-main);
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .nav-pill {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-main);
            text-decoration: none;
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }

        .nav-pill:hover {
            background: var(--clsu-green-muted);
            color: var(--clsu-green);
        }

        .nav-theme-btn {
            width: 34px;
            height: 34px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            background: none;
            color: var(--text-main);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            margin-left: 4px;
        }

        .nav-theme-btn:hover { border-color: var(--clsu-green); color: var(--clsu-green); }

        .btn-nav-primary {
            background: var(--clsu-green);
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            padding: 7px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: 8px;
        }

        .btn-nav-primary:hover {
            background: var(--clsu-green-dark);
            color: var(--clsu-gold-light);
        }

        .nav-hamburger {
            display: none;
            background: none;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            width: 34px;
            height: 34px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-title);
        }

        @media (max-width: 768px) {
            .nav-hamburger { display: flex; }
            .nav-links { display: none; position: absolute; top: 60px; left: 0; width: 100%; background: var(--card-bg); border-bottom: 1px solid var(--border-color); flex-direction: column; padding: 1rem; gap: 8px; }
            .nav-links.open { display: flex; }
            .btn-nav-primary { width: 100%; justify-content: center; }
        }

        /* ── HERO ──────────────────────────────────────── */
        .hero {
            padding: 140px 0 90px;
            border-bottom: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        /* Subtle dot grid background */
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(var(--border-color) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.5;
            pointer-events: none;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--border-color);
            border-radius: 50px;
            padding: 5px 14px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--clsu-green);
            background: var(--clsu-green-muted);
            margin-bottom: 22px;
        }

        .hero-eyebrow .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            animation: pulse-dot 2s ease infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.85); }
        }

        .hero-title {
            font-size: clamp(2.2rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -1.5px;
            margin-bottom: 20px;
            color: var(--text-title);
        }

        .hero-title .accent { color: var(--clsu-green); }

        .hero-desc {
            font-size: 1rem;
            line-height: 1.7;
            color: var(--text-main);
            max-width: 520px;
            margin-bottom: 36px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 44px;
        }

        .btn-hero-primary {
            background: var(--clsu-green);
            color: white;
            border: 1px solid var(--clsu-green);
            border-radius: var(--radius-sm);
            padding: 12px 26px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .btn-hero-primary:hover {
            background: var(--clsu-green-dark);
            border-color: var(--clsu-green-dark);
            color: var(--clsu-gold-light);
        }

        .btn-hero-outline {
            background: transparent;
            color: var(--text-title);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 12px 26px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .btn-hero-outline:hover {
            border-color: var(--text-title);
            background: rgba(0,0,0,0.03);
            color: var(--text-title);
        }

        [data-theme="dark"] .btn-hero-outline:hover { background: rgba(255,255,255,0.04); }

        /* Stats row */
        .hero-stats {
            display: flex;
            gap: 0;
            border-top: 1px solid var(--border-color);
            padding-top: 28px;
        }

        .hero-stat {
            flex: 1;
            padding-right: 20px;
        }

        .hero-stat + .hero-stat {
            padding-left: 20px;
            border-left: 1px solid var(--border-color);
        }

        .hero-stat-val {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--clsu-green);
            line-height: 1;
            margin-bottom: 4px;
        }

        .hero-stat-lbl {
            font-size: 0.77rem;
            color: var(--text-main);
        }

        /* ── FORENSIC PANEL (right side) ─────────────── */
        .forensic-panel {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 22px;
            position: relative;
            overflow: hidden;
        }

        .scan-sweep {
            position: absolute;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #22c55e, transparent);
            animation: sweep 3.5s linear infinite;
            z-index: 5;
        }

        @keyframes sweep {
            0% { top: 0; opacity: 0; }
            5% { opacity: 1; }
            90% { opacity: 0.7; }
            100% { top: 100%; opacity: 0; }
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border-color);
        }

        .panel-header-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-title);
        }

        .panel-badge {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 50px;
            background: rgba(34,197,94,0.1);
            color: #16a34a;
            border: 1px solid rgba(34,197,94,0.2);
        }

        .panel-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.8rem;
        }

        .panel-row:last-child { border-bottom: none; }

        .panel-row-label { color: var(--text-main); }

        .panel-row-val {
            font-weight: 600;
            color: var(--text-title);
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.77rem;
        }

        .panel-row-val.success { color: #16a34a; }

        .panel-terminal {
            margin-top: 14px;
            background: rgba(0,0,0,0.04);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 14px;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.77rem;
            line-height: 1.6;
        }

        [data-theme="dark"] .panel-terminal { background: rgba(0,0,0,0.35); }

        .t-cmd { color: var(--text-title); }
        .t-info { color: var(--clsu-gold); }
        .t-ok { color: #16a34a; font-weight: 700; }
        .t-label { color: var(--text-main); }

        /* Integrity ring */
        .integrity-ring {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 14px;
            padding: 12px 14px;
            background: rgba(34,197,94,0.05);
            border: 1px solid rgba(34,197,94,0.18);
            border-radius: var(--radius-sm);
        }

        .ring-circle {
            width: 42px;
            height: 42px;
            position: relative;
            flex-shrink: 0;
        }

        .ring-circle svg { transform: rotate(-90deg); }

        .ring-text {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 0.68rem;
            fill: #16a34a;
        }

        /* ── SECTION SHARED ──────────────────────────── */
        .section-eyebrow {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--clsu-green);
            display: block;
            margin-bottom: 8px;
        }

        .section-title {
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 700;
            color: var(--text-title);
            margin-bottom: 12px;
        }

        .section-desc {
            color: var(--text-main);
            font-size: 0.92rem;
            line-height: 1.65;
            max-width: 580px;
            margin: 0 auto;
        }

        /* ── PROGRAMS SECTION ────────────────────────── */
        .programs-section {
            padding: 80px 0;
            background: var(--bg-section);
            border-bottom: 1px solid var(--border-color);
        }

        .program-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 28px;
            height: 100%;
            transition: border-color 0.2s;
        }

        .program-card:hover { border-color: var(--clsu-green); }

        .program-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 18px;
            border: 1px solid var(--border-color);
        }

        .program-tag {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .program-card h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-title);
        }

        .program-card p {
            font-size: 0.82rem;
            color: var(--text-main);
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .program-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .program-list li {
            font-size: 0.8rem;
            color: var(--text-main);
            padding: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            border-top: 1px solid var(--border-color);
        }

        .program-list li:first-child { border-top: none; }

        /* ── PIPELINE SECTION ────────────────────────── */
        .pipeline-section {
            padding: 80px 0;
            background: var(--bg-main);
            border-bottom: 1px solid var(--border-color);
        }

        .step-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 24px;
            height: 100%;
            position: relative;
            transition: border-color 0.2s;
        }

        .step-card:hover { border-color: var(--clsu-green); }

        .step-num {
            position: absolute;
            top: 18px;
            right: 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--border-color);
            line-height: 1;
        }

        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            margin-bottom: 16px;
            color: var(--clsu-green);
        }

        .step-card h5 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-title);
        }

        .step-card p {
            font-size: 0.82rem;
            color: var(--text-main);
            line-height: 1.6;
            margin: 0;
        }

        /* ── CTA SECTION ─────────────────────────────── */
        .cta-section {
            padding: 80px 0;
            background: var(--bg-section);
            border-bottom: 1px solid var(--border-color);
        }

        .cta-box {
            background: var(--clsu-green);
            border-radius: var(--radius-lg);
            padding: 52px 48px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-box::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .cta-box h2 {
            color: white;
            font-size: clamp(1.4rem, 3vw, 2rem);
            font-weight: 700;
            margin-bottom: 12px;
        }

        .cta-box p {
            color: rgba(255,255,255,0.75);
            font-size: 0.92rem;
            margin-bottom: 28px;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-cta {
            background: white;
            color: var(--clsu-green);
            border: none;
            border-radius: var(--radius-sm);
            padding: 12px 28px;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .btn-cta:hover {
            background: var(--clsu-gold-light);
            color: var(--clsu-green-dark);
        }

        /* ── FOOTER ──────────────────────────────────── */
        .site-footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            padding: 36px 0;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .site-footer p {
            font-size: 0.8rem;
            color: var(--text-main);
            margin: 0;
        }

        .footer-link {
            font-size: 0.8rem;
            color: var(--clsu-green);
            text-decoration: none;
        }

        .footer-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<!-- ── NAVBAR ─────────────────────────────────────────── -->
<nav class="site-nav">
    <div class="container">
        <a href="{{ route('welcome') }}" class="nav-brand">
            <div class="nav-brand-icon d-flex align-items-center justify-content-center" style="overflow: hidden;">
                @if(\App\Models\Setting::get('app_logo'))
                    <img src="{{ route('system.logo') }}" style="width: 20px; height: 20px; object-fit: contain;">
                @else
                    <i class="fa-solid fa-shield-halved"></i>
                @endif
            </div>
            <div class="nav-brand-text">
                <span class="nav-brand-name">{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }}</span>
                <span class="nav-brand-sub">{{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }} Portal</span>
            </div>
        </a>

        <div class="nav-links" id="navLinks">
            <a href="#programs" class="nav-pill">Programs</a>
            <a href="#pipeline" class="nav-pill">How It Works</a>
            <button class="nav-theme-btn" onclick="toggleTheme()" title="Toggle theme">
                <i class="fa-solid fa-moon" id="themeIcon"></i>
            </button>
            @auth
                @php
                    $dashRoute = match(auth()->user()->role) {
                        'admin' => route('admin.dashboard'),
                        'superadmin' => route('superadmin.analytics'),
                        default => route('student.dashboard'),
                    };
                @endphp
                <a href="{{ $dashRoute }}" class="btn-nav-primary">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-nav-primary">
                    <i class="fa-solid fa-right-to-bracket"></i> Login to Apply
                </a>
            @endauth
        </div>

        <button class="nav-hamburger" id="navToggle" onclick="document.getElementById('navLinks').classList.toggle('open')">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</nav>

<!-- ── HERO SECTION ───────────────────────────────────── -->
<section class="hero">
    <div class="container position-relative">
        <div class="row align-items-center g-5">

            <!-- Left: Copy -->
            <div class="col-lg-6">
                <div class="hero-eyebrow">
                    <span class="dot"></span>
                    <span class="mono">A.E.G.I.S. FORENSICS — ACTIVE</span>
                </div>

                <h1 class="hero-title">
                    Merit-based Scholarships<br>at <span class="accent">CLSU,</span> Secured<br>by AI.
                </h1>

                <p class="hero-desc">
                    The OSA Scholarship Portal connects Central Luzon State University students to institutional, government, and private grants — protected end-to-end by automated forensic document verification.
                </p>

                <div class="hero-actions">
                    @auth
                        @php
                            $dashRoute = match(auth()->user()->role) {
                                'admin' => route('admin.dashboard'),
                                'superadmin' => route('superadmin.analytics'),
                                default => route('student.dashboard'),
                            };
                        @endphp
                        <a href="{{ $dashRoute }}" class="btn-hero-primary">
                            <i class="fa-solid fa-gauge-high"></i> Open My Dashboard
                        </a>
                        <a href="{{ route('logout') }}" class="btn-hero-outline"
                           onclick="event.preventDefault(); document.getElementById('hero-logout-form').submit();">
                            <i class="fa-solid fa-right-from-bracket"></i> Sign Out
                        </a>
                        <form id="hero-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    @else
                        <a href="{{ route('login') }}" class="btn-hero-primary">
                            Apply for a Scholarship <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <a href="#pipeline" class="btn-hero-outline">
                            <i class="fa-solid fa-play"></i> See How It Works
                        </a>
                    @endauth
                </div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-val mono">99.8%</div>
                        <div class="hero-stat-lbl">ELA Accuracy Rate</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-val mono">&lt;0.05ms</div>
                        <div class="hero-stat-lbl">AI Scan Latency</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-val mono">100%</div>
                        <div class="hero-stat-lbl">Encrypted Records</div>
                    </div>
                </div>
            </div>

            <!-- Right: Live Forensic Panel -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="forensic-panel">
                    <div class="scan-sweep"></div>

                    <div class="panel-header">
                        <div class="panel-header-title">
                            <i class="fa-solid fa-shield-halved text-success"></i>
                            <span class="mono">AEGIS-SHIELD / ACTIVE_SCAN</span>
                        </div>
                        <span class="panel-badge">LIVE</span>
                    </div>

                    <div class="mb-3">
                        <div class="panel-row">
                            <span class="panel-row-label mono">Target File</span>
                            <span class="panel-row-val">Certificate_of_Grades.pdf</span>
                        </div>
                        <div class="panel-row">
                            <span class="panel-row-label mono">Analysis Engine</span>
                            <span class="panel-row-val">ResNet-50 ELA-CNN</span>
                        </div>
                        <div class="panel-row">
                            <span class="panel-row-label mono">Pixel Hash</span>
                            <span class="panel-row-val">sha256:a3f8c1...</span>
                        </div>
                        <div class="panel-row">
                            <span class="panel-row-label mono">Integrity Score</span>
                            <span class="panel-row-val success">99.82% — Authentic</span>
                        </div>
                    </div>

                    <div class="panel-terminal">
                        <div class="t-cmd">$ aegis --scan "COG_STUDENT_2024.pdf"</div>
                        <div class="t-info">[INFO] Loading ELA forensic matrices...</div>
                        <div class="t-info">[INFO] Contrast normalization: COMPLETE</div>
                        <div class="t-ok">[PASS] Forgery probability: 0.00%</div>
                        <div class="t-label mt-1">→ Status: READY_FOR_OSA_REVIEW</div>
                    </div>

                    <div class="integrity-ring">
                        <div class="ring-circle">
                            <svg width="42" height="42" viewBox="0 0 42 42">
                                <circle cx="21" cy="21" r="17" fill="none" stroke="var(--border-color)" stroke-width="3"/>
                                <circle cx="21" cy="21" r="17" fill="none" stroke="#22c55e" stroke-width="3"
                                        stroke-dasharray="106.8" stroke-dashoffset="1"
                                        stroke-linecap="round"/>
                                <text x="21" y="26" text-anchor="middle" class="ring-text" transform="rotate(90, 21, 21)">✓</text>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size: 0.82rem; font-weight: 700; color: #16a34a;">Document Cleared</div>
                            <div class="mono" style="font-size: 0.72rem; color: var(--text-main);">No tampering detected • Ready for OSA review</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── PROGRAMS SECTION ───────────────────────────────── -->
<section id="programs" class="programs-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-eyebrow">Scholarship Programs</span>
            <h2 class="section-title">Available Grant Programs</h2>
            <p class="section-desc">Deserving CLSU students can access multiple funding options, all verified through our platform.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="program-card">
                    <div class="program-icon bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span class="program-tag" style="background: rgba(34,197,94,0.1); color: #16a34a; border: 1px solid rgba(34,197,94,0.2);">Institutional</span>
                    <h3>Academic Excellence Awards</h3>
                    <p>Direct university grants for undergraduate students maintaining outstanding GWA standings.</p>
                    <ul class="program-list">
                        <li><i class="fa-solid fa-check text-success" style="font-size: 0.7rem;"></i> University Scholar (GWA 1.00–1.45)</li>
                        <li><i class="fa-solid fa-check text-success" style="font-size: 0.7rem;"></i> College Scholar (GWA 1.46–1.75)</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-4">
                <div class="program-card">
                    <div class="program-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <span class="program-tag" style="background: rgba(59,130,246,0.1); color: #2563eb; border: 1px solid rgba(59,130,246,0.2);">External</span>
                    <h3>Government & Private Grants</h3>
                    <p>National grants and foundation awards integrated directly with the Office of Student Affairs.</p>
                    <ul class="program-list">
                        <li><i class="fa-solid fa-check text-primary" style="font-size: 0.7rem;"></i> DOST-SEI Merit & RA 7687</li>
                        <li><i class="fa-solid fa-check text-primary" style="font-size: 0.7rem;"></i> CHED Tulong Dunong Assistance</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-4">
                <div class="program-card">
                    <div class="program-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="program-tag" style="background: rgba(234,179,8,0.1); color: #b45309; border: 1px solid rgba(234,179,8,0.2);">Talent & Service</span>
                    <h3>Special Service Grants</h3>
                    <p>Financial assistance for cultural representatives, athletes, and student assistants at CLSU.</p>
                    <ul class="program-list">
                        <li><i class="fa-solid fa-check text-warning" style="font-size: 0.7rem;"></i> Varsity & Athletic Scholarship</li>
                        <li><i class="fa-solid fa-check text-warning" style="font-size: 0.7rem;"></i> Student Assistantship Support</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── PIPELINE SECTION ───────────────────────────────── -->
<section id="pipeline" class="pipeline-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-eyebrow">Security & Validation</span>
            <h2 class="section-title">The Evaluation Pipeline</h2>
            <p class="section-desc">A.E.G.I.S. provides an end-to-end digital audit trail protecting merit and transparency at every step.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-sm-6">
                <div class="step-card">
                    <span class="step-num mono">01</span>
                    <div class="step-icon"><i class="fa-solid fa-upload"></i></div>
                    <h3>Submit Application</h3>
                    <p>Students complete their profile, answer scholarship-specific questions, and securely upload required documents (COG, ITR, certifications).</p>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="step-card">
                    <span class="step-num mono">02</span>
                    <div class="step-icon"><i class="fa-solid fa-microchip"></i></div>
                    <h3>AI Document Scan</h3>
                    <p>The ResNet-50 ELA engine inspects every uploaded file for pixel inconsistencies, metadata tampering, or forged content signatures.</p>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="step-card">
                    <span class="step-num mono">03</span>
                    <div class="step-icon"><i class="fa-solid fa-eye"></i></div>
                    <h3>OSA Staff Review</h3>
                    <p>Administrators review GWA eligibility alongside Grad-CAM neural heatmaps and risk scores for each document submitted.</p>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="step-card">
                    <span class="step-num mono">04</span>
                    <div class="step-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
                    <h3>Clearance & Award</h3>
                    <p>Upon approval, an A.E.G.I.S. clearance report is embedded in a signed PDF and emailed directly to the student applicant.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA SECTION ────────────────────────────────────── -->
@guest
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <h2>Ready to Apply?</h2>
            <p>Create your verified applicant profile and submit your scholarship application today. It takes less than 10 minutes.</p>
            <a href="{{ route('login') }}" class="btn-cta">
                <i class="fa-solid fa-right-to-bracket"></i> Get Started — Log In
            </a>
        </div>
    </div>
</section>
@endguest

<!-- ── FOOTER ─────────────────────────────────────────── -->
<footer class="site-footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="footer-brand">
                    <div class="nav-brand-icon" style="width:28px;height:28px;">
                        <i class="fa-solid fa-shield-halved" style="font-size: 0.7rem;"></i>
                    </div>
                    <span class="nav-brand-name" style="font-size: 0.82rem;">A.E.G.I.S. Portal</span>
                </div>
                <p>&copy; {{ date('Y') }} Central Luzon State University — Office of Student Affairs. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p>
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <span class="mx-2" style="color: var(--border-color);">|</span>
                    <span class="mono" style="font-size: 0.75rem; color: var(--text-main);">AEGIS Grade Integrity System v2.0</span>
                </p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleTheme() {
        const html = document.documentElement;
        const current = html.getAttribute('data-theme') || 'light';
        const next = current === 'light' ? 'dark' : 'light';
        html.setAttribute('data-theme', next);
        localStorage.setItem('aegis-theme', next);
        updateIcon(next);
    }

    function updateIcon(theme) {
        const icon = document.getElementById('themeIcon');
        if (!icon) return;
        icon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const saved = localStorage.getItem('aegis-theme') || 'light';
        document.documentElement.setAttribute('data-theme', saved);
        updateIcon(saved);
    });
</script>
</body>
</html>