<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A.E.G.I.S. | Verify Your Email</title>
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

    <!-- Google Fonts: Poppins for Headings, Inter for body -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet"></noscript>
    
    <style>
        :root { 
            --clsu-green: #0C4E2D; /* Starbucks/CLSU Green primary */
            --clsu-green-dark: #07331c; /* Solid House Green */
            --clsu-green-accent: #00754A; /* Accent Green for CTAs */
            --clsu-gold: #D97706; 
            --clsu-gold-light: #fcd570;
            --bg-warm: #f2f0eb; /* Neutral Warm canvas */
        }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--clsu-green-dark); /* Solid House Green background */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
            letter-spacing: -0.01em;
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

        .verify-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 0 0.5px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.18);
            max-width: 480px;
            width: 100%;
            padding: 3rem 2.5rem;
            border: none;
        }

        .btn-resend {
            background-color: var(--clsu-green-accent);
            color: white;
            border-radius: 50px; /* Starbucks pill standard */
            padding: 12px 24px;
            font-weight: 600;
            border: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 117, 74, 0.15);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
        }
        .btn-resend:hover {
            background-color: var(--clsu-green);
            color: white;
            box-shadow: 0 8px 24px rgba(0, 117, 74, 0.25);
        }
        .btn-resend:active {
            transform: scale(0.95) !important;
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
