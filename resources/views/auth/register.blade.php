<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A.E.G.I.S. | Student Registration</title>
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
        .btn-register {
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
        .btn-register:hover {
            background-color: var(--clsu-green-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 89, 52, 0.3);
            color: var(--clsu-gold);
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
                Create Your Account<br>
                <span style="color: var(--clsu-gold);">Start Your Application.</span>
            </h1>
            <p class="lead opacity-75 mb-4" style="max-width: 500px; font-size: 1.1rem;">
                Student self-registration is secure and restricted to Central Luzon State University institutional accounts to maintain application integrity.
            </p>

            <div class="d-flex flex-column gap-3 mt-4">
                <div class="d-flex align-items-center text-white">
                    <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-envelope-circle-check text-warning fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Institutional Email Check</h6>
                        <small class="opacity-75">Registers exclusively with CLSU student domains.</small>
                    </div>
                </div>
                <div class="d-flex align-items-center text-white">
                    <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-envelope text-warning fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Email Verification Required</h6>
                        <small class="opacity-75">Verifies email ownership to block unauthorized accounts.</small>
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

    <!-- RIGHT HALF: The Registration Form -->
    <div class="form-section">
        <div class="form-container">
            
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark">Get Started</h3>
                <p class="text-muted">Enter your details to register as a student.</p>
            </div>

            <!-- Error Alerts -->
            @if($errors->any())
                <div class="alert alert-danger py-2 small border-0 bg-danger bg-opacity-10 text-danger rounded-3 mb-4">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="form-floating mb-3">
                    <input type="text" name="name" class="form-control" id="name" placeholder="Juan Dela Cruz" value="{{ old('name') }}" required autofocus autocomplete="name">
                    <label for="name"><i class="fa-solid fa-user me-2"></i>Full Name</label>
                </div>

                <!-- Email -->
                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="email" placeholder="student@clsu2.edu.ph" value="{{ old('email') }}" required autocomplete="email">
                    <label for="email"><i class="fa-solid fa-envelope me-2"></i>CLSU Student Email</label>
                    <div class="form-text text-muted small mt-1 ps-2">
                        Must end in <strong class="text-dark">@clsu.edu.ph</strong> or <strong class="text-dark">@clsu2.edu.ph</strong>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-floating mb-3">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password" required autocomplete="new-password">
                    <label for="password"><i class="fa-solid fa-lock me-2"></i>Password</label>
                </div>

                <!-- Confirm Password -->
                <div class="form-floating mb-4">
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
                    <label for="password_confirmation"><i class="fa-solid fa-lock me-2"></i>Confirm Password</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-register w-100 mb-3">
                    <i class="fa-solid fa-user-plus me-2"></i>Register Account
                </button>

                <div class="text-center">
                    <p class="text-muted small">
                        Already registered? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Log in instead</a>
                    </p>
                </div>
            </form>
            
        </div>
    </div>
    
</div>

</body>
</html>
