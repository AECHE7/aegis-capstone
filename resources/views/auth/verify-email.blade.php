<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A.E.G.I.S. | Verify Your Email</title>
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

        .btn-resend {
            background-color: var(--clsu-green);
            color: white;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(15, 89, 52, 0.2);
        }
        .btn-resend:hover {
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
        <i class="fa-solid fa-envelope-open-text fa-3x"></i>
    </div>

    <h3 class="fw-bold text-dark mb-3">Verify Your Email</h3>
    
    <p class="text-muted small mb-4" style="line-height: 1.6;">
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just sent to your inbox? If you didn't receive the email, click the button below to request another.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 small py-2 mb-4">
            <i class="fa-solid fa-circle-check me-1"></i> A new verification link has been sent to your email.
        </div>
    @endif

    <div class="d-flex flex-column gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-resend w-100">
                <i class="fa-solid fa-paper-plane me-2"></i>Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-muted small text-decoration-none py-2">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Log Out
            </button>
        </form>
    </div>

</div>

<script>
    // Poll the verification status every 2 seconds
    setInterval(function() {
        fetch("{{ route('verification.status') }}")
            .then(response => response.json())
            .then(data => {
                if (data.verified) {
                    window.location.href = "{{ route('student.dashboard') }}";
                }
            })
            .catch(error => console.error('Error checking verification status:', error));
    }, 2000);
</script>

</body>
</html>
