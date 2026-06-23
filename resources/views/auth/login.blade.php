<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A.E.G.I.S. | Secure Gateway</title>
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Poppins for Headings, Inter for body -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --clsu-green: #0F5934; 
            --clsu-green-dark: #0a4025;
            --clsu-gold: #F2A900; 
            --clsu-gold-light: #fcd570;
        }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #ffffff;
            margin: 0;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Poppins', sans-serif;
        }

        /* Split Screen Layout */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            flex-wrap: wrap;
        }

        /* Left Side: Hero / Landing Graphic */
        .hero-section {
            background: linear-gradient(135deg, rgba(15, 89, 52, 0.92) 0%, rgba(10, 64, 37, 0.98) 100%), 
                        url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 4rem;
        }

        .brand-badge {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        /* Right Side: Form Area */
        .form-section {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background-color: #ffffff;
        }

        .form-container {
            width: 100%;
            max-width: 420px;
            animation: fadeUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        /* Floating Labels & Inputs */
        .form-floating > .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }
        .form-floating > .form-control:focus {
            border-color: var(--clsu-green);
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(15, 89, 52, 0.1);
        }
        .form-floating > label {
            color: #64748b;
            font-weight: 500;
        }

        /* Buttons */
        .btn-login {
            background-color: var(--clsu-green);
            color: white;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: 1.05rem;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(15, 89, 52, 0.2);
        }
        .btn-login:hover {
            background-color: var(--clsu-green-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 89, 52, 0.3);
            color: var(--clsu-gold);
        }

        /* Quick Access Chips */
        .demo-divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
            margin: 2rem 0 1.5rem;
        }
        .demo-divider::before, .demo-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        .demo-divider:not(:empty)::before { margin-right: 1em; }
        .demo-divider:not(:empty)::after { margin-left: 1em; }

        .demo-chip {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 15px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }
        .demo-chip:hover {
            background: #ffffff;
            border-color: var(--clsu-green);
            color: var(--clsu-green);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Breakpoints */
        @media (min-width: 992px) {
            .hero-section { width: 55%; }
            .form-section { width: 45%; }
        }
        @media (max-width: 991px) {
            .hero-section { width: 100%; min-height: 40vh; padding: 2rem; text-align: center; align-items: center; }
            .form-section { width: 100%; padding: 2rem 1rem; }
            .brand-badge { margin: 0 auto 1.5rem; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    
    <!-- LEFT HALF: The Landing Hero -->
    <div class="hero-section">
        <div>
            <div class="brand-badge text-warning">
                <i class="fa-solid fa-building-columns me-2"></i> CLSU OFFICE OF STUDENT AFFAIRS
            </div>
            <h1 class="display-4 fw-bold mb-3" style="line-height: 1.2;">
                Your Gateway to<br>
                <span style="color: var(--clsu-gold);">Scholarship Grants.</span>
            </h1>
            <p class="lead opacity-75 mb-4" style="max-width: 500px; font-size: 1.1rem;">
                We are committed to supporting deserving students through various financial assistance programs. A.E.G.I.S. streamlines this journey by providing a secure, paperless, and AI-verified application process.
            </p>

            <!-- Key Features & Offerings -->
            <div class="d-flex flex-column gap-3 mt-4">
                <div class="d-flex align-items-center text-white">
                    <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-award text-warning fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Institutional & Private Grants</h6>
                        <small class="opacity-75">Access to University, CHED, and DOST-SEI scholarships.</small>
                    </div>
                </div>
                <div class="d-flex align-items-center text-white">
                    <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-bolt text-warning fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Fast-Tracked Evaluation</h6>
                        <small class="opacity-75">Automated document screening for quicker application results.</small>
                    </div>
                </div>
                <div class="d-flex align-items-center text-white">
                    <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-shield-halved text-warning fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Secure AI Verification</h6>
                        <small class="opacity-75">Document forensics powered by A.E.G.I.S. ResNet-50 deep learning.</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-5 mt-lg-0">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-shield-halved fa-xl text-success"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">A.E.G.I.S. Portal</h5>
                    <div class="small opacity-75">Automated Evaluation & Grading Intelligence System</div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT HALF: The Gateway Form -->
    <div class="form-section">
        <div class="form-container">
            
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark">Welcome Back</h3>
                <p class="text-muted">Enter your credentials to access the portal.</p>
            </div>

            <!-- Error Alerts -->
            @if($errors->any())
                <div class="alert alert-danger py-2 small border-0 bg-danger bg-opacity-10 text-danger rounded-3 mb-4">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                
                <!-- Floating Email Input -->
                <div class="form-floating mb-3">
                    <input type="email" name="email" id="emailInput" class="form-control" placeholder="name@clsu.edu.ph" required value="{{ old('email') }}">
                    <label for="emailInput"><i class="fa-solid fa-envelope me-2 text-muted"></i>Email Address</label>
                </div>

                <!-- Floating Password Input with Show/Hide -->
                <div class="form-floating mb-4" style="position: relative;">
                    <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Password" required style="padding-right: 48px;">
                    <label for="passwordInput"><i class="fa-solid fa-lock me-2 text-muted"></i>Password</label>
                    <button type="button" id="togglePassword" onclick="togglePwd()"
                            style="position:absolute;top:50%;right:14px;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;z-index:10;padding:4px;">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </button>
                </div>

                <button type="submit" class="btn btn-login w-100 d-flex justify-content-center align-items-center gap-2" id="loginBtn" onclick="showLoginSpinner()">
                    <span id="loginBtnText">Secure Login</span>
                    <span id="loginSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
                    <i class="fa-solid fa-arrow-right-to-bracket" id="loginArrow"></i>
                </button>
            </form>

            <!-- Quick Access Demo Profiles -->
            <div class="demo-divider text-uppercase tracking-wide">Or Quick Access</div>
            
            <div class="row g-2">
                <div class="col-4">
                    <div class="demo-chip" onclick="fillDemo('student@clsu.edu.ph')">
                        <i class="fa-solid fa-user-graduate fs-5 text-primary mb-1"></i>
                        Student
                    </div>
                </div>
                <div class="col-4">
                    <div class="demo-chip" onclick="fillDemo('admin@clsu.edu.ph')">
                        <i class="fa-solid fa-user-shield fs-5 text-success mb-1"></i>
                        Admin
                    </div>
                </div>
                <div class="col-4">
                    <div class="demo-chip" onclick="fillDemo('director@clsu.edu.ph')">
                        <i class="fa-solid fa-crown fs-5 text-warning mb-1"></i>
                        Director
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i> Tip: Click a profile above to auto-fill credentials for the demo.</small>
            </div>

        </div>
    </div>

</div>

<!-- Auto-fill + Password Toggle + Loading Script -->
<script>
    function fillDemo(email) {
        const emailInput = document.getElementById('emailInput');
        const passInput = document.getElementById('passwordInput');
        emailInput.value = email;
        passInput.value = 'password';
        emailInput.style.transform = "scale(1.02)";
        passInput.style.transform = "scale(1.02)";
        setTimeout(() => {
            emailInput.style.transform = "scale(1)";
            passInput.style.transform = "scale(1)";
        }, 150);
    }

    function togglePwd() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function showLoginSpinner() {
        setTimeout(() => {
            document.getElementById('loginBtnText').textContent = 'Authenticating...';
            document.getElementById('loginSpinner').style.display = 'inline-block';
            document.getElementById('loginArrow').style.display = 'none';
        }, 10);
    }
</script>

</body>
</html>