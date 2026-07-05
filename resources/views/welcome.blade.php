<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLSU Scholarship Portal | Powered by A.E.G.I.S.</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #334155;
            --text-title: #0f172a;
            --border-color: #cbd5e1;
            --clsu-green: #0C4E2D;
            --clsu-green-dark: #072F1B;
            --clsu-gold: #F2A900;
            --clsu-gold-light: #FFD466;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
        }

        [data-theme="dark"] {
            --bg-main: #0B0F19;
            --card-bg: #111827;
            --text-main: #94a3b8;
            --text-title: #f1f5f9;
            --border-color: #334155;
            --clsu-green: #14532d;
            --clsu-green-dark: #0f172a;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            transition: var(--transition);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            color: var(--text-title);
            font-weight: 700;
        }

        .monospace-tag {
            font-family: 'SF Mono', 'Fira Code', 'Courier New', monospace;
            font-size: 0.78rem;
            letter-spacing: -0.2px;
        }

        /* Navbar Styling */
        .navbar-custom {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
            backdrop-filter: blur(12px);
        }

        .nav-link-custom {
            font-weight: 500;
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
        }

        .nav-link-custom:hover {
            background: rgba(12, 78, 45, 0.08);
            color: var(--clsu-green);
        }

        /* Hero Layout */
        .hero-section {
            padding: 130px 0 100px;
            border-bottom: 1px solid var(--border-color);
            position: relative;
            background-image: radial-gradient(var(--border-color) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            background: rgba(12, 78, 45, 0.08);
            border: 1px solid rgba(12, 78, 45, 0.15);
            color: var(--clsu-green);
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.78rem;
            margin-bottom: 20px;
        }

        .hero-title {
            font-size: 3.5rem;
            line-height: 1.15;
            letter-spacing: -1.5px;
            margin-bottom: 20px;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            line-height: 1.6;
            color: var(--text-main);
            margin-bottom: 35px;
            max-width: 600px;
        }

        /* Custom Interactive Buttons */
        .btn-flat-primary {
            background-color: var(--clsu-green);
            color: white;
            border: 1px solid var(--clsu-green);
            border-radius: var(--radius-sm);
            padding: 12px 24px;
            font-weight: 600;
            font-size: 0.92rem;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .btn-flat-primary:hover {
            background-color: var(--clsu-green-dark);
            color: var(--clsu-gold-light);
            border-color: var(--clsu-green-dark);
        }

        .btn-flat-secondary {
            background-color: transparent;
            color: var(--text-title);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 12px 24px;
            font-weight: 600;
            font-size: 0.92rem;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .btn-flat-secondary:hover {
            border-color: var(--text-title);
            background: rgba(0, 0, 0, 0.03);
        }

        [data-theme="dark"] .btn-flat-secondary:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        /* Grade Forensic Scan Panel */
        .forensic-panel {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 24px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .scan-line {
            position: absolute;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--clsu-gold), transparent);
            animation: scanAnim 4s linear infinite;
            z-index: 10;
        }

        @keyframes scanAnim {
            0% { top: 0%; opacity: 0; }
            5% { opacity: 1; }
            95% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }

        .forensic-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
        }

        .forensic-log-box {
            background: rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 16px;
            color: var(--text-main);
            font-size: 0.8rem;
            line-height: 1.45;
        }

        [data-theme="dark"] .forensic-log-box {
            background: rgba(0, 0, 0, 0.3);
        }

        /* Scholarship Flat Grid */
        .scholarship-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 30px;
            height: 100%;
            transition: var(--transition);
        }

        .scholarship-card:hover {
            border-color: var(--clsu-green);
        }

        .icon-wrapper {
            width: 54px;
            height: 54px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
        }

        /* Steps Layout */
        .step-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 24px;
            height: 100%;
            position: relative;
        }

        .step-index {
            position: absolute;
            top: 24px;
            right: 24px;
            font-size: 2rem;
            font-weight: 800;
            color: var(--border-color);
            line-height: 1;
        }

        /* Footer */
        .footer-custom {
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            padding: 40px 0;
            color: var(--text-main);
        }

        /* Theme Toggle styles */
        .theme-toggle-btn {
            background: none;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            color: var(--text-main);
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .theme-toggle-btn:hover {
            color: var(--clsu-green);
            background: rgba(12, 78, 45, 0.08);
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center text-decoration-none" href="#">
            <img src="{{ asset('logo.png') }}" alt="CLSU Logo" height="38" class="me-2" style="object-fit: contain;">
            <div>
                <span class="fw-bold fs-5 text-dark d-block" style="letter-spacing: -0.5px; line-height: 1;">CLSU Scholarships</span>
                <span class="text-muted monospace-tag" style="font-size: 0.62rem; font-weight: 700; text-transform: uppercase;">OSA Forensic Portal</span>
            </div>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fa-solid fa-bars" style="color: var(--text-title);"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto align-items-center gap-3 mt-3 mt-lg-0">
                <a class="nav-link-custom" href="#programs">Programs</a>
                <a class="nav-link-custom" href="#pipeline">Audit Pipeline</a>
                <button class="theme-toggle-btn" id="themeToggleBtn" onclick="toggleTheme()" title="Toggle Light/Dark Mode">
                    <i class="fa-solid fa-moon" id="themeToggleIcon"></i>
                </button>
                @auth
                    @php
                        $dashRoute = auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'superadmin' ? route('superadmin.analytics') : route('student.dashboard'));
                    @endphp
                    <a href="{{ $dashRoute }}" class="btn-flat-primary py-2 px-3">
                        <i class="fa-solid fa-gauge-high me-1"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-flat-primary py-2 px-3">Login to Apply</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 pe-lg-5 mb-5 mb-lg-0">
                <div class="hero-badge">
                    <span class="monospace-tag"><i class="fa-solid fa-circle-nodes me-2"></i>A.E.G.I.S. FORENSICS ENFORCED</span>
                </div>
                <h1 class="hero-title">
                    Fair Merit & Secured Scholarship Access at <span style="color: var(--clsu-green);">CLSU.</span>
                </h1>
                <p class="hero-subtitle">
                    The Central Luzon State University Office of Student Affairs coordinates academic, government, and private grants. Powered by automated pixel forensics, we protect grade integrity and accelerate scholarship awards.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    @auth
                        @php
                            $dashRoute = auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'superadmin' ? route('superadmin.analytics') : route('student.dashboard'));
                        @endphp
                        <a href="{{ $dashRoute }}" class="btn-flat-primary">
                            <i class="fa-solid fa-gauge-high me-2"></i> Open Dashboard
                        </a>
                        <a href="{{ route('logout') }}" class="btn-flat-secondary"
                           onclick="event.preventDefault(); document.getElementById('welcome-logout').submit();">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> Log out
                        </a>
                        <form id="welcome-logout" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                    @else
                        <a href="{{ route('login') }}" class="btn-flat-primary">
                            Login & Apply Now <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                        <a href="#pipeline" class="btn-flat-secondary">
                            <i class="fa-solid fa-magnifying-glass me-2"></i> Technical Pipeline
                        </a>
                    @endauth
                </div>
                <div class="mt-5 d-flex gap-4 border-top border-dashed pt-4 border-color monospace-tag" style="opacity: 0.85;">
                    <div>
                        <div class="fw-bold fs-5 text-dark" style="color: var(--clsu-green) !important;">99.8%</div>
                        <div class="text-muted small">ELA Accuracy</div>
                    </div>
                    <div style="width: 1px; background: var(--border-color);"></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark" style="color: var(--clsu-green) !important;">&lt;0.05ms</div>
                        <div class="text-muted small">Classification Latency</div>
                    </div>
                    <div style="width: 1px; background: var(--border-color);"></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark" style="color: var(--clsu-green) !important;">100%</div>
                        <div class="text-muted small">Encrypted Records</div>
                    </div>
                </div>
            </div>

            <!-- Forensic Scanning Simulation Element -->
            <div class="col-lg-5 d-none d-lg-block">
                <div class="forensic-panel">
                    <div class="scan-line"></div>
                    <div class="forensic-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-success"></i>
                            <span class="monospace-tag fw-bold">AEGIS-SHIELD: ACTIVE_SCAN</span>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success monospace-tag">UAT OK</span>
                    </div>

                    <div class="d-flex flex-column gap-2 text-start small border-bottom pb-3 border-color">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Target Document:</span>
                            <span class="monospace-tag fw-semibold text-dark">Certificate_of_Grades.pdf</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Analysis Model:</span>
                            <span class="monospace-tag fw-semibold text-dark">ResNet-50 ELA-CNN</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Integrity Confidence:</span>
                            <span class="monospace-tag fw-semibold text-success">99.82% Authentic</span>
                        </div>
                    </div>

                    <div class="forensic-log-box monospace-tag">
                        <div>$ aegis --scan-file="COG_STUDENT.pdf"</div>
                        <div style="color: var(--clsu-gold);">[INFO] Initializing forensic pixel matrices...</div>
                        <div style="color: var(--clsu-gold);">[INFO] ELA contrast normalization: COMPLETE</div>
                        <div style="color: var(--clsu-green); font-weight: bold;">[SUCCESS] Forgery score: 0.00% (No tampering detected)</div>
                        <div class="mt-2 text-dark">Status: READY_FOR_OSA_REVIEW</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scholarships Section -->
<section id="programs" class="py-5 bg-white">
    <div class="container py-5">
        <div class="text-center mb-5">
            <span class="monospace-tag text-uppercase fw-bold text-success" style="letter-spacing: 1px;">Scholarship Selection</span>
            <h2 class="fw-bold mt-2">Available Grant Programs</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;"> deservng CLSU students can access multiple funding support options directly verified through our platform.</p>
        </div>

        <div class="row g-4">
            <!-- Program 1 -->
            <div class="col-md-4">
                <div class="scholarship-card">
                    <div class="icon-wrapper bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success monospace-tag mb-3">Institutional</span>
                    <h4 class="fw-bold mb-3">Academic Awards</h4>
                    <p class="text-muted small mb-4">Direct academic grants for undergraduate students maintaining outstanding scholastic standings (GWA).</p>
                    <ul class="list-unstyled text-muted small mb-0">
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> University Scholar (1.00 - 1.45)</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> College Scholar (1.46 - 1.75)</li>
                    </ul>
                </div>
            </div>

            <!-- Program 2 -->
            <div class="col-md-4">
                <div class="scholarship-card">
                    <div class="icon-wrapper bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary monospace-tag mb-3">External</span>
                    <h4 class="fw-bold mb-3">Government & Private</h4>
                    <p class="text-muted small mb-4">National grants and foundation awards integrated with the Office of Student Affairs.</p>
                    <ul class="list-unstyled text-muted small mb-0">
                        <li class="mb-2"><i class="fa-solid fa-check text-primary me-2"></i> DOST-SEI Merit & RA 7687</li>
                        <li><i class="fa-solid fa-check text-primary me-2"></i> CHED Tulong Dunong Assistance</li>
                    </ul>
                </div>
            </div>

            <!-- Program 3 -->
            <div class="col-md-4">
                <div class="scholarship-card">
                    <div class="icon-wrapper bg-warning bg-opacity-10 text-warning">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning monospace-tag mb-3">Talent & Service</span>
                    <h4 class="fw-bold mb-3">Special Service Grants</h4>
                    <p class="text-muted small mb-4">Financial assistance for cultural group representatives, athletes, and student assistants.</p>
                    <ul class="list-unstyled text-muted small mb-0">
                        <li class="mb-2"><i class="fa-solid fa-check text-warning me-2"></i> Varsity / Athletic Scholarship</li>
                        <li><i class="fa-solid fa-check text-warning me-2"></i> Student Assistantship Support</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pipeline Section -->
<section id="pipeline" class="py-5 bg-light border-top">
    <div class="container py-5">
        <div class="text-center mb-5">
            <span class="monospace-tag text-uppercase fw-bold text-success" style="letter-spacing: 1px;">Security & Validation</span>
            <h2 class="fw-bold mt-2">The Evaluation Pipeline</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">A.E.G.I.S. provides an end-to-end digital audit trail protecting merit and transparency.</p>
        </div>

        <div class="row g-4">
            <!-- Step 1 -->
            <div class="col-lg-3 col-sm-6">
                <div class="step-card">
                    <span class="step-index monospace-tag">01</span>
                    <h5 class="fw-bold mt-3 mb-2">Upload Files</h5>
                    <p class="text-muted small mb-0">Students submit profiles and attach official Certificates of Grades (COG) and required custom files securely.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="col-lg-3 col-sm-6">
                <div class="step-card">
                    <span class="step-index monospace-tag">02</span>
                    <h5 class="fw-bold mt-3 mb-2">AI Pixel Scan</h5>
                    <p class="text-muted small mb-0">The ResNet-50 ELA engine checks for pixel inconsistencies, metadata tampering, or forged elements.</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="col-lg-3 col-sm-6">
                <div class="step-card">
                    <span class="step-index monospace-tag">03</span>
                    <h5 class="fw-bold mt-3 mb-2">Staff Review</h5>
                    <p class="text-muted small mb-0">OSA administrators review GWA eligibility alongside neural scan heatmaps for verified document clearance.</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="col-lg-3 col-sm-6">
                <div class="step-card">
                    <span class="step-index monospace-tag">04</span>
                    <h5 class="fw-bold mt-3 mb-2">Clearance & Award</h5>
                    <p class="text-muted small mb-0">Upon approval, dynamic clearance reports are embedded in signed PDFs and emailed directly to students.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer-custom">
    <div class="container text-center text-md-start">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <img src="{{ asset('logo.png') }}" alt="CLSU Logo" height="34" class="me-2 mb-2" style="filter: brightness(0) invert(var(--dark-mode-invert, 0));">
                <p class="mb-0 text-muted small">&copy; {{ date('Y') }} Central Luzon State University - Office of Student Affairs. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end text-muted small monospace-tag">
                AEGIS Grade Integrity System | <a href="#" class="text-decoration-none ms-2" style="color: var(--clsu-green);">Privacy Shield</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleTheme() {
        const root = document.documentElement;
        const currentTheme = root.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        root.setAttribute('data-theme', newTheme);
        localStorage.setItem('aegis-theme', newTheme);
        
        updateThemeToggleIcon(newTheme);
    }

    function updateThemeToggleIcon(theme) {
        const icon = document.getElementById('themeToggleIcon');
        if (icon) {
            if (theme === 'dark') {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('aegis-theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeToggleIcon(savedTheme);
    });
</script>
</body>
</html>