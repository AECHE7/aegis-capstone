<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A.E.G.I.S. | Forgot Password</title>
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
            background: linear-gradient(135deg, rgba(15, 89, 52, 0.95) 0%, rgba(10, 64, 37, 0.98) 100%), 
                        url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
        }

        h3, h5 {
            font-family: 'Poppins', sans-serif;
        }

        .verify-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            max-width: 480px;
            width: 100%;
            padding: 3rem 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .verify-icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(15, 89, 52, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: var(--clsu-green);
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

        .btn-submit {
            background-color: var(--clsu-green);
            color: white;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(15, 89, 52, 0.2);
        }
        .btn-submit:hover {
            background-color: var(--clsu-green-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 89, 52, 0.3);
            color: var(--clsu-gold);
        }
    </style>
</head>
<body>

<div class="verify-card text-center">
    
    <div class="verify-icon-wrapper">
        <i class="fa-solid fa-key fa-3x"></i>
    </div>

    <h3 class="fw-bold text-dark mb-3">Forgot Password</h3>
    
    <p class="text-muted small mb-4" style="line-height: 1.6;">
        Forgot your password? No problem. Enter your institutional email address and we will email you a password reset link to configure a new one.
    </p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 small py-2 mb-4">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('status') }}
        </div>
    @endif

    <!-- Error Alerts -->
    @if($errors->any())
        <div class="alert alert-danger py-2 small border-0 bg-danger bg-opacity-10 text-danger rounded-3 mb-4">
            <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-floating mb-4">
            <input type="email" name="email" id="email" class="form-control" placeholder="name@clsu.edu.ph" required autofocus value="{{ old('email') }}" autocomplete="email">
            <label for="email"><i class="fa-solid fa-envelope me-2 text-muted"></i>Email Address</label>
        </div>

        <button type="submit" class="btn btn-submit w-100 mb-3">
            <i class="fa-solid fa-paper-plane me-2"></i>Email Password Reset Link
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-muted small fw-semibold text-decoration-none">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Login
            </a>
        </div>
    </form>
    
</div>

</body>
</html>
