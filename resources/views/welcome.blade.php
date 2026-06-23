<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLSU Scholarship Portal | Powered by A.E.G.I.S.</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --clsu-green: #0F5934; --clsu-green-dark: #0b4026; --clsu-gold: #F2A900; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; overflow-x: hidden; }
        
        /* Navbar */
        .navbar-custom { background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0,0,0,0.05); transition: all 0.3s ease; }
        .nav-link { font-weight: 500; color: #475569; transition: color 0.2s; }
        .nav-link:hover { color: var(--clsu-green); }
        
        /* Hero Section */
        .hero-section { position: relative; padding: 140px 0 100px; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); overflow: hidden; }
        .hero-bg-pattern { position: absolute; top: 0; right: 0; width: 50%; height: 100%; background-image: radial-gradient(var(--clsu-green) 1px, transparent 1px); background-size: 30px 30px; opacity: 0.03; z-index: 0; }
        .hero-badge { display: inline-block; padding: 8px 16px; background-color: rgba(15, 89, 52, 0.1); color: var(--clsu-green); border-radius: 50px; font-weight: 600; font-size: 0.85rem; margin-bottom: 24px; letter-spacing: 0.5px; }
        .hero-title { font-size: 3.5rem; font-weight: 800; color: #0f172a; line-height: 1.15; letter-spacing: -1px; margin-bottom: 24px; }
        .hero-subtitle { font-size: 1.15rem; color: #475569; line-height: 1.7; margin-bottom: 40px; font-weight: 400; max-width: 600px; }
        
        /* Buttons */
        .btn-primary-custom { background-color: var(--clsu-green); color: white; padding: 14px 28px; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; border: none; }
        .btn-primary-custom:hover { background-color: var(--clsu-green-dark); color: var(--clsu-gold); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(15, 89, 52, 0.15); }
        .btn-secondary-custom { background-color: white; color: #0f172a; padding: 14px 28px; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; border: 1px solid #cbd5e1; }
        .btn-secondary-custom:hover { border-color: var(--clsu-green); color: var(--clsu-green); background-color: #f8fafc; transform: translateY(-2px); }

        /* Floating Cards Animation (Brought Back!) */
        .floating-card { position: absolute; background: white; border-radius: 12px; padding: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.08); z-index: 2; animation: float 6s ease-in-out infinite; border: 1px solid rgba(0,0,0,0.05); width: max-content; }
        .card-1 { top: 10%; right: 15%; animation-delay: 0s; }
        .card-2 { bottom: 15%; right: 35%; animation-delay: 2s; }
        .card-3 { top: 40%; right: 5%; animation-delay: 4s; }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0px); } }

        /* Scholarship Category Cards */
        .scholarship-card { background: white; border-radius: 16px; padding: 40px 30px; height: 100%; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); transition: all 0.3s ease; position: relative; overflow: hidden; }
        .scholarship-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border-color: var(--clsu-gold); }
        .scholarship-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background-color: var(--clsu-green); transform: scaleX(0); transform-origin: left; transition: transform 0.3s ease; }
        .scholarship-card:hover::before { transform: scaleX(1); }
        .icon-wrapper { width: 70px; height: 70px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 24px; }
        
        /* Process Steps */
        .step-number { width: 40px; height: 40px; background: var(--clsu-gold); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; margin: 0 auto 15px; }
        .step-box { text-align: center; padding: 20px; }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ asset('logo.png') }}" alt="CLSU" height="40" class="me-2" style="object-fit: contain;">
            <div>
                <span class="fw-bold fs-5 text-dark" style="letter-spacing: -0.5px;">CLSU Scholarships</span>
                <span class="d-block text-muted" style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.5px;">OFFICE OF STUDENT AFFAIRS</span>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item"><a class="nav-link px-3" href="#categories">Programs</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#process">How to Apply</a></li>
                <li class="nav-item ms-lg-3">
                    @auth
                        @php
                            $dashRoute = auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'superadmin' ? route('superadmin.analytics') : route('student.dashboard'));
                        @endphp
                        <a href="{{ $dashRoute }}" class="btn btn-primary-custom btn-sm px-4 py-2">
                            <i class="fa-solid fa-gauge-high me-1"></i> My Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary-custom btn-sm px-4 py-2">Login / Apply</a>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-bg-pattern"></div>
    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-center">
            <div class="col-lg-7 pe-lg-5 mb-5 mb-lg-0">
                <div class="hero-badge">
                    <i class="fa-solid fa-graduation-cap me-2"></i> Excellent Service for Excellent Students
                </div>
                <h1 class="hero-title">
                    Access Educational Opportunities at <span style="color: var(--clsu-green);">CLSU.</span>
                </h1>
                <p class="hero-subtitle">
                    The Office of Student Affairs (OSA) is dedicated to assisting deserving students through various institutional, government, and private scholarship programs. Apply online securely and track your grant status instantly.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3">
                    @auth
                        @php
                            $dashRoute = auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'superadmin' ? route('superadmin.analytics') : route('student.dashboard'));
                        @endphp
                        <a href="{{ $dashRoute }}" class="btn btn-primary-custom text-decoration-none text-center">
                            <i class="fa-solid fa-gauge-high me-2"></i> Go to My Dashboard
                        </a>
                        <a href="{{ route('logout') }}" class="btn btn-secondary-custom text-decoration-none text-center"
                           onclick="event.preventDefault(); document.getElementById('welcome-logout').submit();">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                        </a>
                        <form id="welcome-logout" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary-custom text-decoration-none text-center">
                            Login to Apply <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                        <a href="#process" class="btn btn-secondary-custom text-decoration-none text-center">
                            <i class="fa-solid fa-magnifying-glass me-2"></i> How It Works
                        </a>
                    @endauth
                </div>
                <div class="mt-4 pt-3 d-flex align-items-center text-muted small fw-medium">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 me-2"><i class="fa-solid fa-shield-halved me-1"></i> A.E.G.I.S. Secured</span>
                    Powered by Automated Evaluation & Grading Intelligence System
                </div>
            </div>
            
            <!-- Hero Graphics (The Floating Cards are Back!) -->
            <div class="col-lg-5 position-relative d-none d-lg-block" style="min-height: 450px;">
                <!-- Abstract Graphic Background -->
                <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); width: 350px; height: 350px; background: radial-gradient(circle, rgba(15,89,52,0.08) 0%, rgba(255,255,255,0) 70%); border-radius: 50%;"></div>
                
                <!-- Floating Card 1 -->
                <div class="floating-card card-1 d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-check fs-5"></i></div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">DOST-SEI Merit</h6>
                        <small class="text-muted" style="font-size: 0.8rem;">Status: <span class="text-success fw-bold">Verified</span></small>
                    </div>
                </div>

                <!-- Floating Card 2 -->
                <div class="floating-card card-2 d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-file-shield fs-5"></i></div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">ELA-CNN Scan</h6>
                        <small class="text-muted" style="font-size: 0.8rem;">Authenticity: <span class="text-dark fw-bold">100% Match</span></small>
                    </div>
                </div>

                <!-- Floating Card 3 -->
                <div class="floating-card card-3 d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-graduation-cap fs-5"></i></div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">GWA Analysis</h6>
                        <small class="text-muted" style="font-size: 0.8rem;">Processing grades...</small>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- Scholarship Categories Section -->
<section id="categories" class="py-5 bg-white">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h6 class="text-uppercase fw-bold mb-2" style="color: var(--clsu-gold); letter-spacing: 1px;">Financial Assistance</h6>
            <h2 class="fw-bold text-dark mb-3">CLSU Scholarship Programs</h2>
            <p class="text-muted mx-auto" style="max-width: 700px;">The university offers a wide array of financial assistance to ensure that quality education remains accessible to outstanding and deserving students.</p>
        </div>
        
        <div class="row g-4">
            <!-- Category 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="scholarship-card">
                    <div class="icon-wrapper bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-book-open-reader"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Institutional Academic Grants</h4>
                    <p class="text-muted mb-4">Awarded to top-performing CLSU students who maintain exemplary academic standings (GWA).</p>
                    <ul class="list-unstyled text-muted small mb-0">
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> University Scholar (1.00 - 1.45 GWA)</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> College Scholar (1.46 - 1.75 GWA)</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Entrance Scholarships (Val/Sal)</li>
                    </ul>
                </div>
            </div>

            <!-- Category 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="scholarship-card">
                    <div class="icon-wrapper bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Government & Private Grants</h4>
                    <p class="text-muted mb-4">Nationally funded programs and private foundation partnerships facilitated by the OSA.</p>
                    <ul class="list-unstyled text-muted small mb-0">
                        <li class="mb-2"><i class="fa-solid fa-check text-primary me-2"></i> DOST-SEI Merit & RA 7687</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-primary me-2"></i> CHED Tulong Dunong / Half Merit</li>
                        <li><i class="fa-solid fa-check text-primary me-2"></i> DA-ACEF & SM Foundation</li>
                    </ul>
                </div>
            </div>

            <!-- Category 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="scholarship-card">
                    <div class="icon-wrapper bg-warning bg-opacity-10 text-warning">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Talent & Service Grants</h4>
                    <p class="text-muted mb-4">Special grants for students representing the university or providing internal services.</p>
                    <ul class="list-unstyled text-muted small mb-0">
                        <li class="mb-2"><i class="fa-solid fa-check text-warning me-2"></i> Varsity / Athletic Scholarships</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-warning me-2"></i> Cultural & Arts (Maestro Singers, etc.)</li>
                        <li><i class="fa-solid fa-check text-warning me-2"></i> Student Assistantship Program</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- The Process Section -->
<section id="process" class="py-5 bg-light border-top">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">How to Apply Online</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Experience a faster, paperless application process powered by the A.E.G.I.S. digital forensics pipeline.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="step-box">
                    <div class="step-number">1</div>
                    <h6 class="fw-bold">Select Program</h6>
                    <p class="text-muted small">Choose the specific scholarship grant you are applying for from the dropdown menu.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="step-box">
                    <div class="step-number">2</div>
                    <h6 class="fw-bold">Upload COG</h6>
                    <p class="text-muted small">Upload a clear, unaltered digital copy of your official Certificate of Grades.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="step-box">
                    <div class="step-number" style="background-color: var(--clsu-green);">3</div>
                    <h6 class="fw-bold">A.E.G.I.S. Verification</h6>
                    <p class="text-muted small">Our AI deep-learning engine automatically scans your document for digital forgery or tampering.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="step-box">
                    <div class="step-number">4</div>
                    <h6 class="fw-bold">OSA Approval</h6>
                    <p class="text-muted small">An OSA Administrator reviews your verified grades and approves your application.</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
             <a href="{{ route('student.apply') }}" class="btn btn-primary-custom px-5">Get Started</a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-auto">
    <div class="container text-center text-md-start">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <img src="{{ asset('logo.png') }}" alt="CLSU" height="30" class="me-2 mb-2" style="filter: brightness(0) invert(1);">
                <p class="mb-0 text-white-50 small">&copy; {{ date('Y') }} Central Luzon State University - Office of Student Affairs. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end text-white-50 small">
                Powered by A.E.G.I.S. | <a href="#" class="text-white-50 text-decoration-none ms-2">Privacy Policy</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>