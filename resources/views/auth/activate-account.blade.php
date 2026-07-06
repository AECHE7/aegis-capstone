<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A.E.G.I.S. | Staff Activation</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
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
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(15, 89, 52, 0.15);
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            background-color: var(--clsu-green-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(15, 89, 52, 0.25);
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (min-width: 992px) {
            .hero-section {
                flex: 1 0 50%;
                min-height: 100vh;
            }
            .form-section {
                flex: 1 0 50%;
                min-height: 100vh;
            }
        }

        @media (max-width: 991.98px) {
            .hero-section {
                flex: 1 0 100%;
                padding: 3rem 2rem;
            }
            .form-section {
                flex: 1 0 100%;
                padding: 3rem 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <!-- LEFT HALF: Hero Slogan / Graphic -->
    <div class="hero-section">
        <div>
            <div class="brand-badge">
                <i class="fa-solid fa-circle-nodes text-warning me-2"></i> STAFF ACTIVATION
            </div>
            
            <h1 class="display-5 fw-bold text-white mb-3">Welcome to the Team</h1>
            <p class="lead opacity-90 mb-4" style="max-width: 500px; font-weight: 300;">
                Set up your administrator profile to begin validating student records and managing scholarship lists.
            </p>

            <div class="d-flex flex-column gap-3 mt-4">
                <div class="d-flex align-items-center text-white">
                    <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-lock text-warning fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Secure Account Setup</h6>
                        <small class="opacity-75">Configure your unique password to activate access.</small>
                    </div>
                </div>
                <div class="d-flex align-items-center text-white">
                    <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-shield-halved text-warning fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Auditable Actions</h6>
                        <small class="opacity-75">Your reviews will be signed and recorded in our security log.</small>
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

    <!-- RIGHT HALF: The Activation Form -->
    <div class="form-section">
        <div class="form-container">
            
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark">Activate Account</h3>
                <p class="text-muted">Fill out the form below to configure your credentials.</p>
            </div>

            <!-- User Info Summary -->
            <div class="card bg-light border-0 p-3 mb-4" style="border-radius: 12px;">
                <div class="small text-muted mb-1">Invited User Info</div>
                <div class="fw-bold text-dark">{{ $user->name }}</div>
                <div class="small text-secondary"><i class="fa-solid fa-envelope me-1"></i>{{ $user->email }}</div>
            </div>

            <!-- Error Alerts -->
            @if($errors->any())
                <div class="alert alert-danger py-2 small border-0 bg-danger bg-opacity-10 text-danger rounded-3 mb-4">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('activate.submit') }}">
                @csrf

                <!-- Invitation Token -->
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Password -->
                <div class="form-floating mb-3">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password" required autofocus autocomplete="new-password">
                    <label for="password"><i class="fa-solid fa-lock me-2"></i>New Password</label>
                </div>

                <!-- Confirm Password -->
                <div class="form-floating mb-4">
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
                    <label for="password_confirmation"><i class="fa-solid fa-lock me-2"></i>Confirm Password</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-register w-100 mb-3">
                    <i class="fa-solid fa-circle-check me-2"></i>Activate & Login
                </button>

                <div class="text-center">
                    <p class="text-muted small">
                        Need help? <a href="mailto:admin-support@clsu.edu.ph" class="text-primary fw-bold text-decoration-none">Contact IT Support</a>
                    </p>
                </div>
            </form>
            
        </div>
    </div>
    
</div>

</body>
</html>
