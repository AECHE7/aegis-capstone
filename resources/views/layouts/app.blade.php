<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Anti-flash theme script --}}
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0C4E2D">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="description" content="@yield('meta_description', 'A.E.G.I.S. is Central Luzon State University\'s official scholarship management portal. Apply for scholarships, track your application status, and receive real-time updates.')">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', \App\Models\Setting::get('app_name', 'A.E.G.I.S.') . ' Portal') — {{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }}</title>
    <link rel="icon" type="image/webp" href="{{ \App\Models\Setting::get('app_logo') ? route('system.logo') : asset('logo.webp') }}">
    <link rel="icon" type="image/png" href="{{ \App\Models\Setting::get('app_logo') ? route('system.logo') : asset('logo.png') }}">

    {{-- Open Graph meta --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} Scholarship Portal">
    <meta property="og:title" content="@yield('title', \App\Models\Setting::get('app_name', 'A.E.G.I.S.') . ' Portal')">
    <meta property="og:description" content="@yield('meta_description', \App\Models\Setting::get('university_name', 'Central Luzon State University') . '\'s official scholarship management portal.')">
    <meta property="og:image" content="{{ \App\Models\Setting::get('app_logo') ? route('system.logo') : asset('logo.webp') }}">
    <meta property="og:locale" content="en_PH">

    {{-- Preconnect to CDN origins (reduces DNS + TLS overhead) --}}
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Bootstrap CSS: non-blocking preload (eliminates 1,210ms render-block) --}}
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"></noscript>

    {{-- Font Awesome CSS: non-blocking preload (eliminates 900ms render-block) --}}
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
    {{-- font-display:swap override — prevents FOIT on Font Awesome webfonts --}}
    <style>
        @font-face { font-family: "Font Awesome 6 Free"; font-display: swap; }
        @font-face { font-family: "Font Awesome 6 Free Solid"; font-display: swap; }
        @font-face { font-family: "Font Awesome 6 Brands"; font-display: swap; }
    </style>

    {{-- Google Fonts: non-blocking load via media='print' trick --}}
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet"></noscript>

    {{-- SweetAlert2: loaded async (moved to end of body) --}}

    <style>
        /* ══════════════════════════════════════════
           DESIGN SYSTEM TOKENS
        ══════════════════════════════════════════ */
        :root {
            --clsu-green: #0C4E2D;
            --clsu-green-dark: #07331c;
            --clsu-green-light: #126b3f;
            --clsu-green-muted: rgba(12, 78, 45, 0.08);
            --clsu-gold: #D97706;
            --clsu-gold-light: #fcd34d;
            --clsu-dark: #0f172a;
            --clsu-bg: #f2f0eb; /* Neutral Warm canvas */
            --card-bg: #ffffff;
            --text-main: rgba(0, 0, 0, 0.87); /* Text Black Soft */
            --text-title: #0C4E2D; /* Starbucks/CLSU Green primary title */
            --border-color: #edebe9; /* Ceramic alternate */
            --sidebar-width: 260px;
            --sidebar-collapsed: 72px;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-pill: 50px;
            --shadow-card: 0 0 0.5px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.18);
            --shadow-nav: 0 1px 3px rgba(0,0,0,0.08), 0 2px 2px rgba(0,0,0,0.05), 0 0 2px rgba(0,0,0,0.06);
            --shadow-elevated: 0 0 6px rgba(0,0,0,0.18), 0 8px 16px rgba(0,0,0,0.12);
            --transition: border-color 0.15s ease, background-color 0.15s ease, opacity 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        }

        [data-theme="dark"] {
            --clsu-bg: #0b0f19;
            --card-bg: #111827;
            --text-main: #94a3b8;
            --text-title: #f1f5f9;
            --border-color: rgba(255,255,255,0.07);
            --clsu-green-muted: rgba(20, 83, 45, 0.15);
            --shadow-card: 0 0 0.5px rgba(0,0,0,0.35), 0 1px 2px rgba(0,0,0,0.45);
            --shadow-nav: 0 1px 3px rgba(0,0,0,0.25), 0 2px 2px rgba(0,0,0,0.15), 0 0 2px rgba(0,0,0,0.18);
        }

        [data-theme="high-contrast"] {
            --clsu-bg: #000000;
            --card-bg: #000000;
            --text-main: #ffffff;
            --text-title: #ffffff;
            --border-color: #ffffff;
            --clsu-green: #ffffff;
            --clsu-green-dark: #000000;
            --clsu-green-light: #fbbf24;
            --clsu-green-muted: rgba(255, 255, 255, 0.2);
            --clsu-gold: #fbbf24;
            --shadow-card: none;
            --shadow-nav: none;
            --shadow-elevated: none;
        }

        [data-theme="high-contrast"] .card, 
        [data-theme="high-contrast"] .sidebar, 
        [data-theme="high-contrast"] .topbar, 
        [data-theme="high-contrast"] .modal-content, 
        [data-theme="high-contrast"] .dropdown-menu {
            border: 2px solid #ffffff !important;
        }
        [data-theme="high-contrast"] .btn {
            border: 2px solid #ffffff !important;
            background-color: #000000 !important;
            color: #ffffff !important;
        }
        [data-theme="high-contrast"] .btn:hover {
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        [data-theme="high-contrast"] *:focus {
            outline: 3px solid #fbbf24 !important;
            outline-offset: 2px;
        }

        /* ══════════════════════════════════════════
           SKELETON LOADER & MICRO-INTERACTIONS
        ══════════════════════════════════════════ */
        .skeleton {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: var(--radius-sm);
            display: inline-block;
            height: 1rem;
            width: 100%;
        }

        [data-theme="dark"] .skeleton {
            background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
            background-size: 200% 100%;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .btn-glow {
            position: relative;
            transition: var(--transition);
        }

        .btn-glow:hover {
            box-shadow: 0 0 12px rgba(15, 89, 52, 0.4);
            transform: translateY(-1px);
        }

        /* Glassmorphism elements */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        [data-theme="dark"] .glass-card {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .topbar-icon-btn {
            color: var(--text-main) !important;
            transition: var(--transition);
        }
        .topbar-icon-btn:hover {
            color: var(--clsu-green) !important;
        }

        /* Theme adjustments for general elements */
        .dropdown-menu {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            box-shadow: var(--shadow-elevated) !important;
        }
        .dropdown-item {
            color: var(--text-main) !important;
        }
        .dropdown-item:hover {
            background-color: var(--clsu-bg) !important;
            color: var(--text-title) !important;
        }
        .dropdown-menu span, .dropdown-menu li, .dropdown-menu div {
            color: var(--text-main);
        }
        .dropdown-menu .fw-bold {
            color: var(--text-title) !important;
        }

        /* Form elements inside theme */
        .form-control, .form-select {
            background-color: var(--card-bg) !important;
            color: var(--text-main) !important;
            border-color: var(--border-color) !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--clsu-green-light) !important;
            box-shadow: 0 0 0 3px rgba(18, 107, 63, 0.15) !important;
        }

        /* Modals inside theme */
        .modal-content {
            background-color: var(--card-bg) !important;
            color: var(--text-main) !important;
            border: none !important;
            box-shadow: var(--shadow-elevated) !important;
        }
        .modal-header, .modal-footer {
            border-color: var(--border-color) !important;
        }
        .modal-body {
            color: var(--text-main) !important;
        }

        /* Tables inside theme */
        .table {
            color: var(--text-main) !important;
        }
        .table th {
            color: var(--text-title) !important;
            border-color: var(--border-color) !important;
        }
        .table td {
            border-color: var(--border-color) !important;
        }

        /* Alert elements overrides */
        [data-theme="dark"] .alert-success {
            background-color: rgba(22, 101, 52, 0.25) !important;
            color: #86efac !important;
            border-color: rgba(34, 197, 94, 0.4) !important;
        }
        [data-theme="dark"] .alert-danger {
            background-color: rgba(127, 29, 29, 0.25) !important;
            color: #fca5a5 !important;
            border-color: rgba(239, 68, 68, 0.4) !important;
        }

        /* ══════════════════════════════════════════
           GLOBAL BASE & TYPOGRAPHY
        ══════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            background-color: var(--clsu-bg);
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: var(--text-main);
            margin: 0;
            overflow-x: hidden;
            letter-spacing: -0.01em; /* SoDoSans tight layout tracking */
            transition: background-color 0.25s, color 0.25s;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: var(--text-title);
            letter-spacing: -0.02em; /* Heading tracking */
        }

        /* ── Phase 2: Fluid Type Scale (WCAG 1.4.10 Reflow + Awwwards Caliber) ──
           clamp(min, preferred-vw, max) prevents font scaling from causing
           horizontal overflow at narrow (320px) viewports while enabling
           responsive sizing without media queries. */
        h1 { font-size: clamp(1.6rem,  4.5vw, 2.25rem); }
        h2 { font-size: clamp(1.35rem, 3.5vw, 1.875rem); }
        h3 { font-size: clamp(1.15rem, 2.8vw, 1.5rem);   }
        h4 { font-size: clamp(1.05rem, 2.2vw, 1.25rem);  }
        h5 { font-size: clamp(0.95rem, 1.8vw, 1.125rem); }
        h6 { font-size: clamp(0.85rem, 1.5vw, 1rem);     }

        /* ══════════════════════════════════════════
           GLOBAL PILL BUTTONS SYSTEM
        ══════════════════════════════════════════ */
        .btn {
            border-radius: var(--radius-pill) !important;
            font-weight: 600 !important;
            padding: 0.55rem 1.6rem !important;
            letter-spacing: -0.01em !important;
            transition: var(--transition) !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        /* Mobile Touch-Target scale and padding boost */
        @media (max-width: 767.98px) {
            .btn {
                padding: 0.7rem 1.8rem !important; /* WCAG 48px touch target height */
            }
        }
        .btn:active, .btn:focus:active {
            transform: scale(0.95) !important;
        }
        .btn-success, .btn-primary {
            background-color: var(--clsu-green-cta, #00754A) !important;
            border-color: var(--clsu-green-cta, #00754A) !important;
            color: #ffffff !important;
        }
        .btn-success:hover, .btn-primary:hover {
            background-color: var(--clsu-green, #0C4E2D) !important;
            border-color: var(--clsu-green, #0C4E2D) !important;
        }
        .btn-outline-success, .btn-outline-primary {
            color: var(--clsu-green-cta, #00754A) !important;
            border-color: var(--clsu-green-cta, #00754A) !important;
            background: transparent !important;
        }
        .btn-outline-success:hover, .btn-outline-primary:hover {
            background-color: var(--clsu-green-cta, #00754A) !important;
            color: #ffffff !important;
        }

        /* ══════════════════════════════════════════
           CARD SYSTEM
        ══════════════════════════════════════════ */
        .card {
            border: none !important; /* No hard borders, elevated with shadows */
            border-radius: var(--radius-md) !important;
            box-shadow: var(--shadow-card) !important;
            transition: var(--transition) !important;
            background: var(--card-bg) !important;
        }

        .card-hover:hover {
            box-shadow: var(--shadow-elevated) !important;
            transform: translateY(-2px);
        }

        .monospace-data {
            font-family: 'SF Mono', 'Fira Code', 'Courier New', monospace;
            font-size: 0.82em;
            letter-spacing: -0.2px;
        }

        /* ══════════════════════════════════════════
           FORM CONTROLS
        ══════════════════════════════════════════ */
        .form-control, .form-select {
            border-radius: var(--radius-sm);
            border: 1.5px solid #d6dbde; /* Input border */
            padding: 0.65rem 1rem;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--clsu-green-light);
            box-shadow: 0 0 0 3px rgba(18, 107, 63, 0.12);
            outline: none;
        }


        /* ══════════════════════════════════════════
           SIDEBAR — ADMIN & SUPERADMIN
        ══════════════════════════════════════════ */
        @auth
        @if(auth()->check())

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--clsu-green-dark); /* Solid House Green, no gradient */
            z-index: 1030;
            display: flex;
            flex-direction: column;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-brand {
            padding: 1.5rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            min-height: 72px;
            text-decoration: none;
            overflow: hidden;
            white-space: nowrap;
        }

        .sidebar-brand-icon {
            width: 38px;
            height: 38px;
            min-width: 38px;
            background: var(--clsu-green-light); /* Solid brand color */
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(18, 107, 63, 0.25);
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            transition: opacity 0.2s;
        }

        .sidebar.collapsed .sidebar-brand-text { opacity: 0; pointer-events: none; }

        .sidebar-brand-name { color: white; font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1rem; letter-spacing: 0.5px; line-height: 1.2; }
        .sidebar-brand-sub { color: rgba(255,255,255,0.5); font-size: 0.65rem; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; }

        /* Role badge in sidebar */
        .sidebar-role {
            padding: 0.75rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            overflow: hidden;
            white-space: nowrap;
        }

        .sidebar.collapsed .sidebar-role { padding: 0.75rem; }

        .sidebar-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.7);
            border: 1px solid rgba(255,255,255,0.1);
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar.collapsed .sidebar-role-badge .role-text { display: none; }

        /* Navigation */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1rem 0.75rem;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

        .sidebar-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.3);
            padding: 0.5rem 0.75rem 0.3rem;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s;
        }

        .sidebar.collapsed .sidebar-label { opacity: 0; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: var(--transition);
            white-space: nowrap;
            overflow: hidden;
            margin-bottom: 3px;
            position: relative;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-link.active {
            background: rgba(0, 117, 74, 0.2); /* Soft green backdrop */
            color: #86efac;
            border: 1px solid rgba(0, 117, 74, 0.3);
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--clsu-green-cta, #00754A); /* Bright green indicator */
            border-radius: 0 3px 3px 0;
        }

        .sidebar-icon {
            width: 20px;
            min-width: 20px;
            text-align: center;
            font-size: 0.95rem;
        }

        .sidebar-text {
            transition: opacity 0.2s;
            flex: 1;
        }

        .sidebar.collapsed .sidebar-text { opacity: 0; width: 0; }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 0.75rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar-logout-btn:hover {
            background: rgba(239, 68, 68, 0.2);
            color: white;
        }

        /* Toggle button */
        .sidebar-toggle {
            position: fixed;
            top: 0.95rem;
            left: calc(var(--sidebar-width) - 16px);
            width: 32px;
            height: 32px;
            background: var(--card-bg);
            border: 1.5px solid var(--border-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1035;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.25s, border-color 0.25s, box-shadow 0.25s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
            color: var(--text-main);
        }

        .sidebar-toggle:hover { 
            background: var(--clsu-green-cta, #00754A); 
            color: white; 
            border-color: var(--clsu-green-cta, #00754A); 
            box-shadow: 0 4px 12px rgba(0, 117, 74, 0.3);
        }
        .sidebar-toggle.collapsed { left: calc(var(--sidebar-collapsed) - 16px); }

        /* Main content with sidebar offset */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-wrapper.collapsed { margin-left: var(--sidebar-collapsed); }

        /* Top header bar inside main area */
        .topbar {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(12, 78, 45, 0.08);
            padding: 0.85rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.02);
            transition: var(--transition);
        }

        .topbar-title { font-family: 'Poppins', sans-serif; font-size: 1rem; font-weight: 600; color: var(--text-title); margin: 0; }
        .topbar-subtitle { font-size: 0.75rem; color: #94a3b8; margin: 0; }

        .page-content {
            padding: 1.75rem 2rem 3rem;
            animation: fadeInUp 0.4s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive sidebar & backdrop for mobile/tablet */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width) !important;
                z-index: 1040;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .sidebar.mobile-show {
                transform: translateX(0);
            }

            /* Mobile Sidebar: force show all text, branding and badges even if collapsed class exists (Image 2 fix) */
            .sidebar.collapsed .sidebar-brand-text {
                opacity: 1 !important;
                pointer-events: auto !important;
            }
            .sidebar.collapsed .sidebar-role {
                padding: 0.75rem 1.2rem !important;
            }
            .sidebar.collapsed .sidebar-role-badge .role-text {
                display: inline-block !important;
            }
            .sidebar.collapsed .sidebar-label {
                opacity: 1 !important;
            }
            .sidebar.collapsed .sidebar-text {
                opacity: 1 !important;
                width: auto !important;
                display: inline-block !important;
            }

            /* Mobile navigation is served via the dedicated fixed bottom navigation drawer */
            #mobileSidebarToggle {
                display: none !important;
            }
            
            .sidebar-toggle {
                display: none !important;
            }
            
            .main-wrapper, .main-wrapper.collapsed {
                margin-left: 0 !important;
            }
            
            .topbar {
                padding: 0.75rem 1rem;
            }
            
            .page-content {
                padding: 1.25rem 1rem calc(88px + env(safe-area-inset-bottom, 16px)) !important;
            }
            
            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(4px);
                z-index: 1039;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.25s ease;
            }
            
            .sidebar-backdrop.show {
                opacity: 1;
                pointer-events: auto;
            }
        }

        @media (max-width: 575.98px) {
            .topbar-subtitle {
                display: none !important;
            }
            .topbar-title {
                font-size: 0.88rem !important;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 150px;
            }
            .topbar {
                padding: 0.5rem 0.75rem !important;
            }
            .page-content {
                padding: 0.85rem 0.65rem calc(88px + env(safe-area-inset-bottom, 16px)) !important;
            }
            /* iOS Safari auto-zoom prevention: ensure form controls are at least 16px */
            input, select, textarea {
                font-size: 16px !important;
            }
        }

        @endif
        @endauth

        /* ══════════════════════════════════════════
           TOPBAR ICON BUTTONS (all roles)
        ══════════════════════════════════════════ */
        .topbar-icon-btn {
            color: var(--text-main);
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            text-decoration: none;
        }

        .topbar-icon-btn:hover {
            background: var(--clsu-green-muted, rgba(12,78,45,0.08));
            color: var(--clsu-green);
        }

        /* ══════════════════════════════════════════
           STAT CARDS
        ══════════════════════════════════════════ */
        .stat-card {
            border-radius: var(--radius-md);
            padding: 1.25rem 1.5rem;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: 0 0 var(--radius-md) var(--radius-md);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.08) !important;
        }

        .stat-card.warning:hover {
            border-color: rgba(217, 119, 6, 0.3);
            background: linear-gradient(180deg, var(--card-bg) 0%, rgba(217, 119, 6, 0.02) 100%) !important;
        }
        .stat-card.info:hover {
            border-color: rgba(2, 132, 199, 0.3);
            background: linear-gradient(180deg, var(--card-bg) 0%, rgba(2, 132, 199, 0.02) 100%) !important;
        }
        .stat-card.success:hover {
            border-color: rgba(12, 78, 45, 0.3);
            background: linear-gradient(180deg, var(--card-bg) 0%, rgba(12, 78, 45, 0.02) 100%) !important;
        }
        .stat-card.danger:hover {
            border-color: rgba(239, 68, 68, 0.3);
            background: linear-gradient(180deg, var(--card-bg) 0%, rgba(239, 68, 68, 0.02) 100%) !important;
        }

        .stat-card.warning::after  { background: var(--clsu-gold); }
        .stat-card.info::after     { background: #0284c7; }
        .stat-card.success::after  { background: var(--clsu-green); }
        .stat-card.danger::after   { background: #ef4444; }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        /* ══════════════════════════════════════════
           BADGES
        ══════════════════════════════════════════ */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .status-badge.pending  { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
        .status-badge.review   { background: #e0f2fe; color: #0c4a6e; border: 1px solid #7dd3fc; }
        .status-badge.approved { background: #dcfce7; color: #14532d; border: 1px solid #86efac; }
        .status-badge.rejected { background: #fee2e2; color: #7f1d1d; border: 1px solid #fca5a5; }

        /* ══════════════════════════════════════════
           TOOLTIPS (sidebar collapsed state)
        ══════════════════════════════════════════ */
        .sidebar.collapsed .sidebar-link {
            position: relative;
            justify-content: center;
        }

        .sidebar.collapsed .sidebar-link::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%);
            background: #1e293b;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
            z-index: 2000;
        }

        .sidebar.collapsed .sidebar-link:hover::after { opacity: 1; }



        /* ══════════════════════════════════════════
           PAGE ANIMATIONS
        ══════════════════════════════════════════ */
        .fade-in {
            animation: fadeInUp 0.4s ease-out;
        }

        /* ══════════════════════════════════════════
           ALERT IMPROVEMENTS
        ══════════════════════════════════════════ */
        .alert {
            border-radius: var(--radius-md);
            border: none;
        }

        /* ══════════════════════════════════════════
           TABLE IMPROVEMENTS
        ══════════════════════════════════════════ */
        .table > :not(caption) > * > * {
            padding: 0.85rem 1rem;
            vertical-align: middle;
        }

        .table thead th {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-main);
            background: var(--clsu-bg);
            border-bottom: 2px solid var(--border-color);
        }

        .table tbody tr {
            border-color: var(--border-color);
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: var(--clsu-bg);
        }

        /* ══════════════════════════════════════════
           PHASE 1 — WCAG 2.2 / WSG / AWARD-CALIBER
        ══════════════════════════════════════════ */

        /* --- WCAG 2.4.1 Bypass Blocks (Level A) ---
           Skip-to-content link: visually hidden until keyboard focus */
        .skip-link {
            position: absolute;
            top: -200%;
            left: 1rem;
            background: var(--clsu-green);
            color: #ffffff;
            padding: 0.6rem 1.25rem;
            border-radius: 0 0 var(--radius-md) var(--radius-md);
            z-index: 99999;
            font-weight: 700;
            font-size: 0.875rem;
            text-decoration: none;
            transition: top 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-elevated);
        }
        .skip-link:focus {
            top: 0;
        }

        /* --- WCAG 2.4.7 Focus Visible / 2.4.11 Focus Not Obscured (Level AA) ---
           Restore keyboard focus ring suppressed by Bootstrap;
           uses CLSU gold for guaranteed contrast on green backgrounds. */
        *:focus {
            outline: none;
        }
        *:focus-visible {
            outline: 3px solid var(--clsu-gold) !important;
            outline-offset: 3px !important;
            border-radius: var(--radius-sm) !important;
        }

        /* --- WCAG 2.5.8 Target Size Minimum (Level AA, new in WCAG 2.2) ---
           All topbar icon buttons get a 44x44px minimum touch target. */
        .topbar-icon-btn {
            min-width: 44px;
            min-height: 44px;
        }

        /* --- WCAG 2.3.3 Animation from Interactions + WSG Performance ---
           Disable ALL motion for users who prefer reduced motion.
           Satisfies Level AAA and W3C Web Sustainability Guidelines. */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        /* --- WCAG 1.4.10 Reflow / UX: smooth scrolling + scroll margin ---
           Prevents sticky topbar from obscuring anchor targets. */
        @media (prefers-reduced-motion: no-preference) {
            html { scroll-behavior: smooth; }
        }
        [id] { scroll-margin-top: 80px; }

        /* --- W3C Web Sustainability Guidelines (WSG): touch-action ---
           Eliminates 300ms tap delay on mobile — doubles perceived responsiveness. */
        a, button, .btn, [role="button"] {
            touch-action: manipulation;
        }

        /* --- WSG: content-visibility for heavy non-above-fold sections ---
           Tells the browser to skip painting off-screen card grids,
           reducing CPU layout/paint work significantly. */
        .content-lazy {
            content-visibility: auto;
            contain-intrinsic-size: 0 300px;
        }

        /* --- WCAG 1.4.11 Non-text Contrast: autofill color override ---
           Prevents browser autofill from painting white backgrounds
           that clash in dark mode or themed input fields. */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px var(--clsu-bg, #f2f0eb) inset !important;
            -webkit-text-fill-color: var(--text-main, rgba(0,0,0,0.87)) !important;
            transition: background-color 5000s ease-in-out 0s !important;
        }
        [data-theme="dark"] input:-webkit-autofill,
        [data-theme="dark"] input:-webkit-autofill:hover,
        [data-theme="dark"] input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #111827 inset !important;
            -webkit-text-fill-color: #94a3b8 !important;
        }

        /* --- AWWWARDS-CALIBER MOTION DESIGN & INTERACTIVE FEEDBACK --- */

        /* Smooth scroll-reveal effect for dashboard cards */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(24px) scale(0.98);
            transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal-on-scroll.revealed {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        /* Glowing interactive outline on active sidebar items */
        .sidebar-link.active {
            position: relative;
            background: linear-gradient(90deg, rgba(12, 78, 45, 0.1) 0%, rgba(12, 78, 45, 0.02) 100%) !important;
            box-shadow: inset 3px 0 0 var(--clsu-green);
        }

        /* Tactical micro-interaction: Button click active state feedback */
        .btn-animate-click {
            transition: transform 0.12s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.12s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .btn-animate-click:active {
            transform: scale(0.96) !important;
        }

        /* ── Standardized Institutional Button System (WCAG 2.2 AA / ISO 9241-210) ── */
        .btn-clsu-primary {
            background: linear-gradient(135deg, #00754A 0%, #0C4E2D 100%) !important;
            color: #ffffff !important;
            border: none !important;
            font-weight: 600 !important;
            border-radius: 10px !important;
            box-shadow: 0 2px 8px rgba(12, 78, 45, 0.2) !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            padding: 8px 18px !important;
            font-size: 0.85rem !important;
            min-height: 40px !important;
        }
        .btn-clsu-primary:hover {
            background: linear-gradient(135deg, #098757 0%, #0F5E38 100%) !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 14px rgba(12, 78, 45, 0.3) !important;
        }
        .btn-clsu-primary:active {
            transform: scale(0.97) !important;
        }

        .btn-clsu-secondary {
            background: var(--card-bg, #ffffff) !important;
            color: var(--text-main, #1e293b) !important;
            border: 1.5px solid var(--border-color, #e2e8f0) !important;
            font-weight: 600 !important;
            border-radius: 10px !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            padding: 8px 16px !important;
            font-size: 0.85rem !important;
            min-height: 40px !important;
        }
        .btn-clsu-secondary:hover {
            background: var(--clsu-bg, #f8fafc) !important;
            color: var(--text-title, #0f172a) !important;
            border-color: var(--clsu-green-light, #16a34a) !important;
            transform: translateY(-1px) !important;
        }

        .btn-clsu-danger {
            background: rgba(220, 38, 38, 0.08) !important;
            color: #dc2626 !important;
            border: 1.5px solid rgba(220, 38, 38, 0.3) !important;
            font-weight: 600 !important;
            border-radius: 10px !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            padding: 8px 16px !important;
            font-size: 0.85rem !important;
            min-height: 40px !important;
        }
        .btn-clsu-danger:hover {
            background: #dc2626 !important;
            color: #ffffff !important;
            border-color: #dc2626 !important;
            transform: translateY(-1px) !important;
        }

        /* Unified Control Bar & Quick-Filter Pill Ribbon */
        .unified-control-bar {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 14px;
            padding: 8px 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.025);
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        [data-theme="dark"] .unified-control-bar {
            background: rgba(17, 24, 39, 0.65);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .filter-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--border-color, #e2e8f0);
            background: var(--clsu-bg, #f1f5f9);
            color: var(--text-muted, #64748b);
            transition: all 0.15s ease;
            user-select: none;
            white-space: nowrap;
            text-decoration: none;
        }
        .filter-status-pill:hover {
            color: var(--text-main, #1e293b);
            background: rgba(12, 78, 45, 0.08);
            border-color: rgba(12, 78, 45, 0.2);
        }
        .filter-status-pill.active {
            background: var(--clsu-green, #0C4E2D) !important;
            color: #ffffff !important;
            border-color: var(--clsu-green, #0C4E2D) !important;
            box-shadow: 0 2px 8px rgba(12, 78, 45, 0.25);
        }
        .filter-status-pill .pill-count {
            background: rgba(0, 0, 0, 0.1);
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 0.7rem;
        }
        .filter-status-pill.active .pill-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* --- Slide-in Floating Toast Notifications (WCAG 4.1.3 / Awwwards Polish) --- */
        .toast-container-custom {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            max-width: 360px;
            width: calc(100% - 48px);
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }
        .toast-custom {
            animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            border-radius: 12px !important;
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.05) !important;
            pointer-events: auto;
        }
        @keyframes slideInRight {
            from { transform: translateX(110%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* --- WCAG 1.4.10 Reflow: Badge label text wrap override --- */
        .badge, .status-badge, .fraud-chip {
            white-space: normal !important;
            word-break: break-word;
            text-align: left;
        }

        /* ── Phase 5: Awwwards-Caliber Button Ripple Effect ──────────────────────
           Pure CSS ripple: a pseudo-element is triggered by JS adding
           .ripple-active class, then auto-removed. Wraps all .btn elements.
           Guarded by prefers-reduced-motion for WCAG 2.3.3 AAA compliance. */
        .btn { overflow: hidden; position: relative; }
        .btn::after {
            content: '';
            position: absolute;
            inset: 50%;
            background: rgba(255,255,255,0.35);
            border-radius: 50%;
            transform: scale(0);
            opacity: 0;
            pointer-events: none;
        }
        @media (prefers-reduced-motion: no-preference) {
            .btn.ripple-active::after {
                animation: btn-ripple 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }
        }
        @keyframes btn-ripple {
            0%   { inset: 50%; transform: scale(0); opacity: 1; }
            100% { inset: -150%; transform: scale(1); opacity: 0; }
        }

        /* ── Phase 5: Floating Label Design System Utility ────────────────────────
           Use .form-floating-custom wrapper for elegant floating label fields.
           Labels translate upward on :focus or when the input has a value (.has-value).
           Compatible with Bootstrap form-control inputs. */
        .form-floating-custom {
            position: relative;
        }
        .form-floating-custom label {
            position: absolute;
            top: 0.65rem;
            left: 1rem;
            font-size: 0.875rem;
            color: #94a3b8;
            pointer-events: none;
            transform-origin: left top;
            transition: transform 0.18s cubic-bezier(0.4, 0, 0.2, 1),
                        font-size 0.18s cubic-bezier(0.4, 0, 0.2, 1),
                        color 0.18s;
            background: transparent;
            padding: 0 0.25rem;
        }
        .form-floating-custom input:focus ~ label,
        .form-floating-custom input.has-value ~ label,
        .form-floating-custom select:focus ~ label,
        .form-floating-custom select.has-value ~ label {
            transform: translateY(-1.1rem) scale(0.78);
            color: var(--clsu-green-light);
            background: var(--card-bg);
            font-weight: 600;
        }
        .form-floating-custom input,
        .form-floating-custom select {
            padding-top: 1.1rem !important;
        }
        @media (prefers-reduced-motion: reduce) {
            .form-floating-custom label { transition: none; }
        }

        /* ── Phase 4: Mobile Grid Stacking ─────────────────────────────────────────
           Ensures stat card rows and detail panels fully collapse into a single
           column at xs breakpoint (≤575.98px), preventing truncation or overflow. */
        @media (max-width: 575.98px) {
            .row.g-4 > [class*='col-']:not(.col-12),
            .row.g-3 > [class*='col-']:not(.col-12) {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }
            /* Prevent horizontal scroll on data tables on small screens */
            .table-responsive {
                -webkit-overflow-scrolling: touch;
            }
            /* Stack action buttons in review/detail cards vertically */
            .action-btn-group {
                flex-direction: column !important;
                width: 100%;
            }
            .action-btn-group .btn {
                width: 100% !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>

{{-- WCAG Screen Reader Polite Announcer --}}
<div id="aegis-a11y-announcer" class="visually-hidden" aria-live="polite" aria-atomic="true"></div>

{{-- WCAG 2.4.1 Bypass Blocks (Level A): Skip-to-content link --}}
<a href="#main-content" class="skip-link">Skip to main content</a>

@auth
    @if(auth()->check())
    
    {{-- ═══════════════════════════════════════════
         SIDEBAR LAYOUT (ADMIN / SUPERADMIN)
    ═══════════════════════════════════════════ --}}
    
    @include('layouts.sidebar')

    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
        <i class="fa-solid fa-chevron-left" id="toggleIcon" style="font-size: 0.7rem;"></i>
    </button>

    <!-- Main Wrapper -->
    <div class="main-wrapper" id="mainWrapper">
        <!-- Top bar -->
        <div class="topbar">
            <div class="d-flex align-items-center overflow-hidden me-2" style="min-width: 0;">
                <!-- Mobile Hamburger Toggle (Disabled on mobile view as dedicated mobile bottom navbar provides complete navigation) -->
                <button class="btn btn-link topbar-icon-btn p-0 me-2 me-sm-3 d-none flex-shrink-0" id="mobileSidebarToggle"
                        aria-label="Toggle Navigation"
                        aria-controls="mainSidebar"
                        aria-expanded="false"
                        style="box-shadow: none;">
                    <i class="fa-solid fa-bars fs-5 fs-sm-4"></i>
                </button>
                <div class="overflow-hidden" style="min-width: 0;">
                    <p class="topbar-title text-truncate">@yield('page-title', 'Dashboard')</p>
                    <p class="topbar-subtitle text-truncate">@yield('page-subtitle', 'A.E.G.I.S. Portal')</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1.5 gap-sm-2 flex-shrink-0 ms-auto">
                <!-- Official Philippine Standard Time (PST / PHT) Live Institutional Clock -->
                <div class="d-none d-md-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-light border text-muted shadow-xs text-nowrap" 
                     title="Official Philippine Standard Time (UTC+8) - CLSU Institutional Clock" style="font-size: 0.76rem;">
                    <i class="fa-regular fa-clock text-success" aria-hidden="true"></i>
                    <span id="pstLiveClock" class="fw-semibold font-monospace" style="color: var(--text-title, #1e293b);">--:--:--</span>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-1.5 py-0.5" style="font-size:0.62rem; font-weight: 700;">PHT</span>
                </div>

                <!-- Interactive Demo Guide Trigger Button in Topbar -->
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 px-sm-3 py-1 text-muted d-none d-sm-inline-flex align-items-center gap-1.5 shadow-xs text-nowrap" 
                        onclick="openSystemTourModal('{{ auth()->user()->role ?? 'student' }}')" 
                        title="Interactive System Demo & Role Walkthrough">
                    <i class="fa-solid fa-graduation-cap text-success"></i>
                    <span class="small fw-semibold" style="font-size: 0.76rem;">Demo Guide</span>
                </button>

                <!-- Stakeholder Data Management Guide Trigger Button in Topbar -->
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 px-sm-3 py-1 text-muted d-none d-md-inline-flex align-items-center gap-1.5 shadow-xs text-nowrap" 
                        data-bs-toggle="modal" data-bs-target="#dataManagementGuideModal"
                        title="Institutional Data Management & Privacy Guide (R.A. 10173 & ISO/IEC 25010)">
                    <i class="fa-solid fa-shield-halved text-info"></i>
                    <span class="small fw-semibold" style="font-size: 0.76rem;">Data Guide</span>
                </button>

                <!-- UAT System Evaluation Feedback Trigger Button in Topbar -->
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 px-sm-3 py-1 text-muted d-none d-lg-inline-flex align-items-center gap-1.5 shadow-xs text-nowrap" 
                        data-bs-toggle="modal" data-bs-target="#uatFeedbackModal"
                        title="Submit ISO/IEC 25010 System Evaluation Feedback">
                    <i class="fa-solid fa-star text-warning" aria-hidden="true"></i>
                    <span class="small fw-semibold" style="font-size: 0.76rem;">Feedback</span>
                </button>

                <!-- Notification Bell Dropdown -->
                <div class="dropdown me-0 me-sm-1">
                    {{-- WCAG 4.1.2: Name/Role/Value — accessible name on icon-only button --}}
                    <button class="btn btn-link position-relative p-1 topbar-icon-btn" type="button" 
                            id="@if(auth()->user()->role === 'student') notifBellStudent @else notifBellAdmin @endif" 
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            aria-label="View notifications"
                            style="box-shadow: none;">
                        <i class="fa-regular fa-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white d-none" 
                              id="@if(auth()->user()->role === 'student') notifBadgeStudent @else notifBadgeAdmin @endif" 
                              style="font-size: 0.6rem; padding: 3px 6px;">
                            0
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2 text-start" 
                        aria-labelledby="@if(auth()->user()->role === 'student') notifBellStudent @else notifBellAdmin @endif" 
                        style="width: 320px; max-width: calc(100vw - 32px); border-radius: 16px; font-size: 0.85rem; max-height: 420px; overflow-y: auto;">
                        <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Notifications</span>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 small text-success fw-semibold" onclick="clearAllNotifications(event)">Mark all as read</button>
                        </li>
                        <ul class="list-unstyled mb-0" id="@if(auth()->user()->role === 'student') notifListStudent @else notifListAdmin @endif">
                            <li class="px-3 py-4 text-center text-muted small">
                                <i class="fa-solid fa-bell-slash mb-2 d-block opacity-40 fs-4"></i>
                                No new notifications
                            </li>
                        </ul>
                    </ul>
                </div>

                @if(auth()->user()->role === 'admin')
                    <span class="badge px-3 py-2 rounded-pill fw-semibold text-nowrap d-none d-sm-inline-flex align-items-center" style="background: #dcfce7; color: #14532d; font-size: 0.75rem; white-space: nowrap;">
                        <i class="fa-solid fa-user-shield me-1"></i> OSA Administrator
                    </span>
                    <span class="badge rounded-circle fw-semibold d-inline-flex d-sm-none align-items-center justify-content-center" style="background: #dcfce7; color: #14532d; width: 34px; height: 34px; font-size: 0.85rem;" title="OSA Administrator">
                        <i class="fa-solid fa-user-shield"></i>
                    </span>
                @elseif(auth()->user()->role === 'superadmin')
                    <span class="badge px-3 py-2 rounded-pill fw-semibold text-nowrap d-none d-sm-inline-flex align-items-center" style="background: #fef9c3; color: #854d0e; font-size: 0.75rem; white-space: nowrap;">
                        <i class="fa-solid fa-crown me-1"></i> Director
                    </span>
                    <span class="badge rounded-circle fw-semibold d-inline-flex d-sm-none align-items-center justify-content-center" style="background: #fef9c3; color: #854d0e; width: 34px; height: 34px; font-size: 0.85rem;" title="Director">
                        <i class="fa-solid fa-crown"></i>
                    </span>
                @else
                    <span class="badge px-3 py-2 rounded-pill fw-semibold text-success text-nowrap d-none d-sm-inline-flex align-items-center" style="background: rgba(25,135,84,0.1); font-size: 0.75rem; border: 1px solid rgba(25,135,84,0.25); white-space: nowrap;">
                        <i class="fa-solid fa-user-graduate me-1"></i> Student Applicant
                    </span>
                    <span class="badge rounded-circle fw-semibold text-success d-inline-flex d-sm-none align-items-center justify-content-center" style="background: rgba(25,135,84,0.1); border: 1px solid rgba(25,135,84,0.25); width: 34px; height: 34px; font-size: 0.85rem;" title="Student Applicant">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>
                @endif
            </div>
        </div>

        {{-- W3C Semantic Landmark: <main> (WCAG 1.3.1 Info and Relationships, Level A) --}}
        <main id="main-content" class="page-content" role="main" tabindex="-1">
            {{-- Toast Container for Slide-in Notifications (WCAG 4.1.3 Status Messages) --}}
            <div class="toast-container-custom">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 toast-custom mb-0" role="status" aria-live="polite" aria-atomic="true"
                         style="background: #dcfce7; color: #14532d; border-left: 4px solid #22c55e !important;">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-circle-check me-2 fs-5" aria-hidden="true"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Dismiss success message"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 toast-custom mb-0" role="alert" aria-live="assertive" aria-atomic="true"
                         style="background: #fee2e2; color: #7f1d1d; border-left: 4px solid #ef4444 !important;">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-circle-exclamation me-2 fs-5" aria-hidden="true"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Dismiss error message"></button>
                    </div>
                @endif
            </div>

            @yield('content')
        </main>
    </div>

    @endif

    <!-- UAT Feedback Modal (ISO/IEC 25010 6-Dimension Evaluation) -->
    <div class="modal fade" id="uatFeedbackModal" tabindex="-1" aria-labelledby="uatFeedbackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #0f1f12, #0F5934); padding: 1.5rem 1.75rem 1.25rem;">
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="uatFeedbackModalLabel">
                            <i class="fa-solid fa-star text-warning me-2"></i> System Evaluation (ISO/IEC 25010)
                        </h5>
                        <small class="text-white-50">Rate each institutional software quality metric from 1 (Strongly Disagree) to 5 (Strongly Agree)</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('uat.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            @foreach([
                                ['name' => 'functional_suitability', 'label' => '1. Functional Suitability', 'desc' => 'The system accurately processes eligibility scoring, document uploads, and application submissions without data loss.'],
                                ['name' => 'performance_efficiency', 'label' => '2. Performance Efficiency', 'desc' => 'Page load speeds, queue queries, and status updates render responsively without persistent bottlenecks.'],
                                ['name' => 'compatibility', 'label' => '3. Compatibility', 'desc' => 'The portal operates seamlessly across standard mobile viewports, modern desktop browsers, and network variations.'],
                                ['name' => 'usability', 'label' => '4. Usability', 'desc' => 'The navigation flow from scholarship selection to multi-step submission is intuitive and straightforward.'],
                                ['name' => 'reliability', 'label' => '5. Reliability', 'desc' => 'Automated background fraud scans, security verification, and institutional audit trails operate dependably.'],
                                ['name' => 'security', 'label' => '6. Security & Confidentiality', 'desc' => 'Student PII is encrypted at rest (AES-256-GCM), role boundaries are maintained, and audit logs track all access.'],
                            ] as $q)
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="form-label fw-semibold text-dark mb-1 small">{{ $q['label'] }}</div>
                                        <p class="text-muted mb-3" style="font-size: 0.76rem; line-height: 1.35;">{{ $q['desc'] }}</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                        <span class="text-muted small" style="font-size: 0.7rem;">Poor (1)</span>
                                        <div class="d-flex gap-2">
                                            @for($i = 1; $i <= 5; $i++)
                                            <label class="d-flex flex-column align-items-center gap-0 cursor-pointer mb-0" style="cursor:pointer;" title="Score: {{ $i }}">
                                                <input type="radio" name="{{ $q['name'] }}" value="{{ $i }}" 
                                                       class="d-none" {{ $i === 5 ? 'checked' : '' }}
                                                       onchange="highlightStars(this, '{{ $q['name'] }}', {{ $i }})">
                                                <span class="uat-star" data-group="{{ $q['name'] }}" data-val="{{ $i }}"
                                                      style="font-size:1.35rem;color:#F2A900;transition:color 0.15s;cursor:pointer;line-height:1;"
                                                      onclick="this.previousElementSibling.click()">★</span>
                                                <span style="font-size:0.65rem;color:#94a3b8;font-weight:600;">{{ $i }}</span>
                                            </label>
                                            @endfor
                                        </div>
                                        <span class="text-success small fw-semibold" style="font-size: 0.7rem;">Excellent (5)</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <label class="form-label fw-semibold text-dark small" for="commentsTextarea">Institutional Comments & Improvement Suggestions</label>
                            <textarea class="form-control" name="comments" id="commentsTextarea" rows="3" 
                                      placeholder="Provide qualitative feedback regarding UI/UX ergonomics, system speed, or scholarship workflow..." 
                                      style="font-size:0.875rem;resize:none;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light fw-semibold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn fw-bold rounded-pill px-5" 
                                style="background: linear-gradient(135deg, var(--clsu-gold), #e09500); color: #1a1a00;">
                            <i class="fa-solid fa-paper-plane me-1"></i> Submit Evaluation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Session Idle Warning Modal (NIST SP 800-63B Compliance) -->
    <div class="modal fade" id="sessionIdleModal" tabindex="-1" aria-labelledby="sessionIdleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: var(--shadow-elevated);">
                <div class="modal-header border-bottom border-light py-3">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="sessionIdleModalLabel">
                        <i class="fa-solid fa-triangle-exclamation text-warning animate-bounce"></i> Session Security Idle Warning
                    </h5>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3 text-warning">
                        <i class="fa-solid fa-hourglass-half fa-3x"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Are you still working?</h6>
                    <p class="text-muted small mb-0">For security compliance (NIST SP 800-63B), inactive sessions are logged out automatically.</p>
                    <p class="text-danger fw-bold fs-5 mt-2 mb-0">Logging out in <span id="idleTimerCount">120</span> seconds.</p>
                </div>
                <div class="modal-footer border-top border-light d-flex justify-content-between">
                    <button type="button" class="btn btn-light px-4" id="idleLogoutBtn">Logout Now</button>
                    <button type="button" class="btn btn-success text-white px-4" id="idleKeepAliveBtn" style="background-color: var(--clsu-green-cta, #00754A) !important;">Keep Me Logged In</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // NIST SP 800-63B Idle Session Timer (28 minutes idle warning, 2 minutes countdown)
        (function() {
            const IDLE_TIMEOUT_MS = 28 * 60 * 1000; // 28 minutes of inactivity before warning
            const COUNTDOWN_SECONDS = 120; // 2 minutes countdown
            
            let warningTimer = null;
            let countdownInterval = null;
            let countdownSecondsRemaining = COUNTDOWN_SECONDS;
            let idleModal = null;

            function resetIdleTimers() {
                // Clear any active countdown
                clearInterval(countdownInterval);
                clearTimeout(warningTimer);
                countdownSecondsRemaining = COUNTDOWN_SECONDS;

                // Hide modal if open
                const modalEl = document.getElementById('sessionIdleModal');
                if (modalEl && modalEl.classList.contains('show')) {
                    const bootstrapModal = bootstrap.Modal.getInstance(modalEl);
                    bootstrapModal?.hide();
                }

                // Start warning timer
                warningTimer = setTimeout(showIdleWarning, IDLE_TIMEOUT_MS);
            }

            function showIdleWarning() {
                const modalEl = document.getElementById('sessionIdleModal');
                if (!modalEl) return;
                
                // Only instantiate if not already present
                idleModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                idleModal.show();

                const timerDisplay = document.getElementById('idleTimerCount');
                if (timerDisplay) timerDisplay.textContent = countdownSecondsRemaining;

                countdownInterval = setInterval(() => {
                    countdownSecondsRemaining--;
                    if (timerDisplay) timerDisplay.textContent = countdownSecondsRemaining;

                    if (countdownSecondsRemaining <= 0) {
                        clearInterval(countdownInterval);
                        logoutUser();
                    }
                }, 1000);
            }

            function keepAlive() {
                // Ping server to refresh PHP session
                fetch('/notifications')
                    .then(res => {
                        resetIdleTimers();
                    })
                    .catch(err => {
                        console.error('Failed to keep session alive:', err);
                        resetIdleTimers();
                    });
            }

            function logoutUser() {
                const logoutForm = document.getElementById('logoutForm');
                if (logoutForm) {
                    logoutForm.submit();
                } else {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/logout';
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = "{{ csrf_token() }}";
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            // Reset timers on user activities
            const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
            activityEvents.forEach(evt => {
                document.addEventListener(evt, resetIdleTimers, { passive: true });
            });

            // Initialize on start
            resetIdleTimers();

            document.addEventListener('DOMContentLoaded', () => {
                // Hook keep alive button
                const keepAliveBtn = document.getElementById('idleKeepAliveBtn');
                if (keepAliveBtn) {
                    keepAliveBtn.addEventListener('click', keepAlive);
                }

                // Hook logout button
                const logoutBtn = document.getElementById('idleLogoutBtn');
                if (logoutBtn) {
                    logoutBtn.addEventListener('click', logoutUser);
                }
            });
        })();
    </script>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ── Sidebar Toggle ──────────────────────────────────────
    const sidebar = document.getElementById('mainSidebar');
    const mainWrapper = document.getElementById('mainWrapper');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const toggleIcon = document.getElementById('toggleIcon');

    let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

    function applySidebarState() {
        if (!sidebar) return;
        if (sidebarCollapsed) {
            sidebar.classList.add('collapsed');
            mainWrapper?.classList.add('collapsed');
            sidebarToggle?.classList.add('collapsed');
            if (toggleIcon) {
                toggleIcon.classList.remove('fa-chevron-left');
                toggleIcon.classList.add('fa-chevron-right');
            }
        } else {
            sidebar.classList.remove('collapsed');
            mainWrapper?.classList.remove('collapsed');
            sidebarToggle?.classList.remove('collapsed');
            if (toggleIcon) {
                toggleIcon.classList.add('fa-chevron-left');
                toggleIcon.classList.remove('fa-chevron-right');
            }
        }
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebarCollapsed = !sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
            applySidebarState();
        });
    }

    applySidebarState();

    // Mobile Sidebar Drawer handlers (WCAG 4.1.2: aria-expanded state sync)
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', () => {
            sidebar?.classList.add('mobile-show');
            sidebarBackdrop?.classList.add('show');
            mobileSidebarToggle.setAttribute('aria-expanded', 'true');
        });
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', () => {
            sidebar?.classList.remove('mobile-show');
            sidebarBackdrop?.classList.remove('show');
            mobileSidebarToggle?.setAttribute('aria-expanded', 'false');
        });
    }

    // ── UAT Star Highlighting ────────────────────────────────
    function highlightStars(input, group, val) {
        document.querySelectorAll(`.uat-star[data-group="${group}"]`).forEach(star => {
            star.style.color = parseInt(star.dataset.val) <= val ? '#F2A900' : '#e2e8f0';
        });
    }

    // Initialize stars at their default value (5)
    document.querySelectorAll('.uat-star').forEach(star => {
        if (parseInt(star.dataset.val) <= 5) {
            star.style.color = '#F2A900';
        }
    });

    // ── Notification Box JS ──────────────────────────────────
    function fetchNotifications() {
        fetch('/notifications', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(res => {
                if (!res || !res.ok) return null;
                return res.text();
            })
            .then(text => {
                if (!text || !text.trim()) return null;
                try {
                    return JSON.parse(text);
                } catch(e) {
                    return null;
                }
            })
            .then(data => {
                if (!data) return;
                const badgeAdmin = document.getElementById('notifBadgeAdmin');
                const listAdmin = document.getElementById('notifListAdmin');
                const badgeStudent = document.getElementById('notifBadgeStudent');
                const listStudent = document.getElementById('notifListStudent');
                
                const count = data.count || 0;
                const notifications = data.notifications || [];
                
                if (badgeAdmin) {
                    if (count > 0) {
                        badgeAdmin.classList.remove('d-none');
                        badgeAdmin.textContent = count;
                    } else {
                        badgeAdmin.classList.add('d-none');
                    }
                }
                
                if (listAdmin) {
                    renderNotificationList(listAdmin, notifications);
                }
                
                if (badgeStudent) {
                    if (count > 0) {
                        badgeStudent.classList.remove('d-none');
                        badgeStudent.textContent = count;
                    } else {
                        badgeStudent.classList.add('d-none');
                    }
                }
                
                if (listStudent) {
                    renderNotificationList(listStudent, notifications);
                }
            })
            .catch(() => {
                // Silently ignore network or gateway interruptions
            });
    }
    
    function renderNotificationList(listElement, notifications) {
        listElement.innerHTML = '';
        if (notifications.length === 0) {
            listElement.innerHTML = `
                <li class="px-3 py-4 text-center text-muted small">
                    <i class="fa-solid fa-bell-slash mb-2 d-block opacity-40 fs-4"></i>
                    No new notifications
                </li>
            `;
            return;
        }
        
        notifications.forEach(n => {
            const li = document.createElement('li');
            const isUnread = !n.is_read;
            li.className = 'px-3 py-2 border-bottom notification-item transition-all';
            li.style.cursor = 'pointer';
            if (isUnread) {
                li.style.backgroundColor = 'rgba(12, 78, 45, 0.05)';
            }
            li.innerHTML = `
                <div class="d-flex flex-column gap-1 text-start">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-1.5 overflow-hidden">
                            ${isUnread ? '<span class="d-inline-block rounded-circle bg-success flex-shrink-0" style="width: 7px; height: 7px;"></span>' : ''}
                            <strong class="text-truncate" style="font-size: 0.8rem; color: ${isUnread ? 'var(--clsu-green)' : 'var(--text-main)'};">${n.title}</strong>
                        </div>
                        <span class="text-muted flex-shrink-0 ms-1" style="font-size: 0.65rem;">${n.created_at}</span>
                    </div>
                    <div class="text-muted small" style="line-height: 1.35; font-size: 0.76rem;">${n.message}</div>
                </div>
            `;
            li.addEventListener('click', (e) => {
                e.stopPropagation();
                markAsRead(n.id, n.url);
            });
            listElement.appendChild(li);
        });
    }
    
    function markAsRead(id, targetUrl) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            fetchNotifications();
            if (targetUrl) {
                window.location.href = targetUrl;
            }
        })
        .catch(err => {
            if (targetUrl) {
                window.location.href = targetUrl;
            }
        });
    }
    
    function clearAllNotifications(event) {
        event.stopPropagation();
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('/notifications/clear', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            fetchNotifications();
        })
        .catch(err => console.error('Error clearing notifications:', err));
    }

    function setSystemTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        announceToScreenReader("Theme updated to " + theme.replace('-', ' ') + " mode.");
    }

    function announceToScreenReader(message) {
        const announcer = document.getElementById('aegis-a11y-announcer');
        if (announcer) {
            announcer.textContent = '';
            setTimeout(() => {
                announcer.textContent = message;
            }, 50);
        }
    }

    // ── Philippine Standard Time (PST / PHT, UTC+8) Live Clock ────────────
    function updatePstClock() {
        const clockEl = document.getElementById('pstLiveClock');
        if (!clockEl) return;
        try {
            const now = new Date();
            const options = {
                timeZone: 'Asia/Manila',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const formatter = new Intl.DateTimeFormat('en-US', options);
            clockEl.textContent = formatter.format(now);
        } catch (e) {
            const now = new Date();
            const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
            const phtDate = new Date(utc + (3600000 * 8));
            clockEl.textContent = phtDate.toLocaleTimeString('en-US', { hour12: true });
        }
    }
    setInterval(updatePstClock, 1000);
    updatePstClock();

    document.addEventListener('DOMContentLoaded', () => {
        fetchNotifications();
        updatePstClock();

        // Use standard AJAX polling instead of EventSource/SSE to prevent PHP worker exhaustion and session locking
        setInterval(fetchNotifications, 20000);

        // SweetAlert2 Logout Confirmation
        const logoutLink = document.getElementById('logoutLink');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Confirm Logout',
                    text: 'Are you sure you want to log out of your session?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0C4E2D',
                    cancelButtonColor: '#475569',
                    confirmButtonText: 'Yes, Logout',
                    customClass: { popup: 'rounded-4' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logoutForm').submit();
                    }
                });
            });
        }
        // Awwwards-caliber scroll-reveal observer
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05 });

        // Select all cards, tables, panels for elegant scroll reveal
        document.querySelectorAll('.card, .dark-stat, .chart-card, .ai-panel-doc, .empty-card').forEach(el => {
            el.classList.add('reveal-on-scroll');
            revealObserver.observe(el);
        });

        // Attach tactical click animations to all main buttons
        document.querySelectorAll('.btn, .btn-submit-app, .sidebar-link, .topbar-icon-btn').forEach(btn =>
            btn.classList.add('btn-animate-click')
        );

        // ── Phase 5: Button Ripple Trigger ─────────────────────────────────────
        // Triggers the CSS ::after ripple animation on all .btn elements.
        // The class is removed after the animation duration to allow re-triggering.
        if (window.matchMedia('(prefers-reduced-motion: no-preference)').matches) {
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn');
                if (!btn) return;
                btn.classList.remove('ripple-active');
                // Force reflow to restart animation
                void btn.offsetWidth;
                btn.classList.add('ripple-active');
                btn.addEventListener('animationend', () => btn.classList.remove('ripple-active'), { once: true });
            });
        }

        // ── Phase 5: Floating Label has-value detector ──────────────────────────
        // Marks inputs inside .form-floating-custom with .has-value when they
        // contain data, so the label stays elevated even when the field is blurred.
        document.querySelectorAll('.form-floating-custom input, .form-floating-custom select').forEach(input => {
            const check = () => {
                if (input.value) {
                    input.classList.add('has-value');
                } else {
                    input.classList.remove('has-value');
                }
            };
            input.addEventListener('input', check);
            input.addEventListener('change', check);
            check(); // Run on page load for pre-filled values
        });

        // ── Mobile Sidebar Toggle & Backdrop ──────────────────────────
        const mobileToggle = document.getElementById('mobileSidebarToggle');
        const sidebar = document.getElementById('mainSidebar');
        if (mobileToggle && sidebar) {
            let backdrop = document.querySelector('.sidebar-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'sidebar-backdrop d-none';
                document.body.appendChild(backdrop);
            }

            const toggleMobileSidebar = (forceState) => {
                const isOpening = forceState !== undefined ? forceState : !sidebar.classList.contains('mobile-show');
                if (isOpening) {
                    sidebar.classList.add('mobile-show');
                    backdrop.classList.remove('d-none');
                    mobileToggle.setAttribute('aria-expanded', 'true');
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebar.classList.remove('mobile-show');
                    backdrop.classList.add('d-none');
                    mobileToggle.setAttribute('aria-expanded', 'false');
                    document.body.style.overflow = '';
                }
            };

            mobileToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleMobileSidebar();
            });

            backdrop.addEventListener('click', () => {
                toggleMobileSidebar(false);
            });

            // Close sidebar on navigation click on small screens
            sidebar.querySelectorAll('.sidebar-link').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) {
                        toggleMobileSidebar(false);
                    }
                });
            });
        }
    });
</script>

{{-- SweetAlert2: loaded before views scripts to avoid undefined ReferenceError --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Mobile keyboard detection for sticky action bars --}}
<script>
(function() {
    let initialViewportHeight = window.innerHeight;

    window.addEventListener('resize', function() {
        // Detect if keyboard is open (viewport shrunk by >150px on mobile)
        if (window.innerWidth <= 576) {
            if (window.innerHeight < initialViewportHeight - 150) {
                document.body.classList.add('keyboard-open');
            } else {
                document.body.classList.remove('keyboard-open');
                initialViewportHeight = window.innerHeight; // Update for orientation changes
            }
        }
    });

    // Reset on orientation change
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            initialViewportHeight = window.innerHeight;
            document.body.classList.remove('keyboard-open');
        }, 200);
    });
})();
</script>

<!-- PWA Offline Status Banner -->
<div id="offlineBanner" class="position-fixed top-0 start-0 end-0 text-center py-2 px-3 fw-bold small shadow-sm animate-fade-in" style="z-index: 9999; display: none; border-bottom: 2px solid #d97706; background-color: #fef3c7 !important; color: #92400e !important;">
    <i class="fa-solid fa-wifi-slash me-2"></i> You are currently working offline. Navigating from cached portal data.
</div>

<script>
    // Register PWA Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').catch(function() {});
        });
    }

    // Monitor Online/Offline Status
    function updateOnlineStatus() {
        var banner = document.getElementById('offlineBanner');
        if (banner) {
            if (!navigator.onLine) {
                banner.style.display = 'block';
            } else {
                banner.style.display = 'none';
            }
        }
    }
    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    document.addEventListener('DOMContentLoaded', updateOnlineStatus);
</script>

<!-- PWA One-Tap Install Banner -->
<div id="pwaInstallBanner" class="position-fixed bottom-0 start-0 end-0 p-3 shadow-lg animate-fade-in" style="z-index: 1050; display: none; background: #0C4E2D !important; border-top: 3px solid #D97706; color: white !important;">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <img src="/logo.webp" alt="A.E.G.I.S." width="36" height="36" class="rounded-3 shadow-sm">
            <div>
                <strong class="d-block text-white mb-0" style="font-size: 0.9rem;">Install A.E.G.I.S. App</strong>
                <span class="text-white-50 small">Add to your home screen for fast, offline-ready access</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button id="pwaInstallBtn" class="btn btn-sm btn-warning text-dark fw-bold px-3 py-1 rounded-pill">Install</button>
            <button id="pwaDismissBtn" class="btn btn-sm btn-link text-white-50 p-1" style="box-shadow:none;"><i class="fa-solid fa-xmark fs-5"></i></button>
        </div>
    </div>
</div>

<script>
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        if (!localStorage.getItem('pwa_banner_dismissed')) {
            const banner = document.getElementById('pwaInstallBanner');
            if (banner) banner.style.display = 'block';
        }
    });

    document.getElementById('pwaInstallBtn')?.addEventListener('click', async () => {
        const banner = document.getElementById('pwaInstallBanner');
        if (banner) banner.style.display = 'none';
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;
        }
    });

    document.getElementById('pwaDismissBtn')?.addEventListener('click', () => {
        const banner = document.getElementById('pwaInstallBanner');
        if (banner) banner.style.display = 'none';
        localStorage.setItem('pwa_banner_dismissed', '1');
    });
</script>

@include('layouts.mobile-nav')

<x-data-management-guide-modal />
<x-system-demo-modal />

@stack('scripts')
</body>
</html>