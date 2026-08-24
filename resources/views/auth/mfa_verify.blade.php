<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MFA Verification | {{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }}</title>
    {{-- Preconnect hints --}}
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

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

    {{-- Google Fonts — must load for visual consistency with login page --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #07331c; /* Solid House Green background */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            padding: 20px;
            letter-spacing: -0.01em;
        }
        .mfa-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 0 0.5px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.18);
            width: 100%;
            max-width: 440px;
            padding: 40px 30px;
            text-align: center;
            border: none;
        }
        .logo-container {
            margin-bottom: 25px;
        }
        .logo-container img {
            height: 64px;
            object-fit: contain;
        }
        .mfa-title {
            color: #0C4E2D;
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }
        .mfa-desc {
            color: #64748b;
            font-size: 14.5px;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .otp-input-field {
            letter-spacing: 12px;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            border-radius: 8px; /* Standard input radius */
            border: 1.5px solid #d6dbde;
            padding: 12px 5px 12px 15px;
            font-family: monospace;
            transition: all 0.2s ease;
            color: #0C4E2D;
        }
        .otp-input-field:focus {
            border-color: #00754A;
            box-shadow: 0 0 0 3px rgba(0, 117, 74, 0.12);
            outline: none;
        }
        .btn-verify {
            background-color: #00754A; /* Accent green CTA */
            color: white;
            border-radius: 50px; /* Starbucks pill standard */
            padding: 13px 30px;
            font-weight: 600;
            font-size: 15px;
            border: none;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0, 117, 74, 0.15);
            transition: all 0.2s ease;
        }
        .btn-verify:hover {
            background-color: #0C4E2D;
            color: white;
            box-shadow: 0 8px 24px rgba(0, 117, 74, 0.25);
        }
        .btn-verify:active {
            transform: scale(0.95) !important;
        }
        .btn-cancel {
            color: #64748b;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            display: inline-block;
            margin-top: 20px;
            transition: color 0.15s;
        }
        .btn-cancel:hover {
            color: #0C4E2D;
        }
    </style>
</head>
<body>
    <div class="mfa-card">
        <div class="logo-container">
            <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="CLSU Logo" style="height: 64px; object-fit: contain;">
        </div>
        <h1 class="mfa-title">Security Verification</h1>
        <p class="mfa-desc">Enter the 6-digit verification code sent to your registered email address to complete signing in.</p>

        {{-- OTP expiry countdown (10 min = 600s, matches backend TTL) --}}
        <div id="otpExpiry" class="mb-3" style="font-size: 0.82rem; color: #64748b; background: #f8fafc; border-radius: 8px; padding: 8px 14px; display: inline-block;">
            <i class="fa-solid fa-clock me-1" style="font-size: 0.75rem;"></i>
            Code expires in <span id="expiryDisplay" style="font-weight: 700; color: #0C4E2D; font-family: monospace;">10:00</span>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 small mb-4 py-2" style="background-color: #dcfce7; color: #14532d;">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning border-0 small mb-4 py-2" style="background-color: #fffbeb; color: #78350f;">
                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('warning') }}
            </div>
        @endif

        @if($errors->has('code'))
            <div class="alert alert-danger border-0 small mb-4 py-2" style="background-color: #fee2e2; color: #7f1d1d;">
                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first('code') }}
            </div>
        @endif

        <form action="{{ route('login.mfa.verify') }}" method="POST">
            @csrf
            <div class="mb-4">
                <input type="text" name="code" id="otpInput" class="form-control otp-input-field" 
                       maxlength="6" placeholder="000000" required
                       autocomplete="one-time-code"
                       autofocus
                       inputmode="numeric" pattern="[0-9]*"
                       aria-label="One-time verification code">
            </div>
            <div class="form-check text-start mb-4 d-flex align-items-center gap-2" style="margin-left: 2px;">
                <input class="form-check-input" type="checkbox" name="remember_device" id="rememberDevice" checked style="cursor: pointer; width: 16px; height: 16px; margin: 0; accent-color: #0C4E2D;">
                <label class="form-check-label" for="rememberDevice" style="cursor: pointer; color: #475569; font-size: 13.5px; user-select: none;">
                    Remember this device for 30 days
                </label>
            </div>
            <button type="submit" class="btn btn-verify">
                <i class="fa-solid fa-shield-halved me-2"></i> Verify Code
            </button>
        </form>

        <div class="mt-4" style="color: #64748b; font-size: 13.5px;">
            <span id="resendTimer">Didn't receive the code? Resend in <strong id="countdown">60</strong>s</span>
            <button id="resendBtn" onclick="resendOtp()" style="display:none; background:none; border:none; color:#0C4E2D; font-weight:600; font-size:13.5px; cursor:pointer; padding:0; text-decoration:underline;">
                <i class="fa-solid fa-rotate-right me-1"></i> Resend Code
            </button>
        </div>

        <a href="{{ route('login') }}" class="btn-cancel">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Login
        </a>
    </div>

<script>
    // Force fresh page load if restored from browser back/forward cache (bfcache)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });

    // OTP digit-only input
    const otpInput = document.getElementById('otpInput');
    if (otpInput) {
        const checkAutoSubmit = function() {
            otpInput.value = otpInput.value.replace(/[^0-9]/g, '').slice(0, 6);
            if (otpInput.value.length === 6) {
                const form = otpInput.closest('form');
                if (form && !form.dataset.submitting) {
                    form.dataset.submitting = 'true';
                    form.submit();
                }
            }
        };

        otpInput.addEventListener('input', checkAutoSubmit);
        otpInput.addEventListener('paste', function(e) {
            setTimeout(checkAutoSubmit, 50);
        });
    }

    // Resend countdown timer (dynamic cooldown)
    var seconds = {{ isset($resendCooldown) ? $resendCooldown : 60 }};
    var countdownEl = document.getElementById('countdown');
    var resendTimer = document.getElementById('resendTimer');
    var resendBtn = document.getElementById('resendBtn');

    if (seconds <= 0) {
        if (resendTimer) resendTimer.style.display = 'none';
        if (resendBtn) resendBtn.style.display = 'inline';
    } else {
        if (countdownEl) countdownEl.textContent = seconds;
        var timer = setInterval(function () {
            seconds--;
            if (countdownEl) countdownEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(timer);
                if (resendTimer) resendTimer.style.display = 'none';
                if (resendBtn) resendBtn.style.display = 'inline';
            }
        }, 1000);
    }

    // OTP Expiry countdown (dynamic remaining TTL from DB)
    var expirySeconds = {{ isset($remainingSeconds) ? $remainingSeconds : 600 }};
    var expiryDisplay = document.getElementById('expiryDisplay');
    var expiryContainer = document.getElementById('otpExpiry');
    
    // Initial display formatting
    if (expiryDisplay) {
        var initialM = Math.floor(expirySeconds / 60);
        var initialS = expirySeconds % 60;
        expiryDisplay.textContent = initialM + ':' + (initialS < 10 ? '0' : '') + initialS;
    }
    var expiryInterval = setInterval(function() {
        expirySeconds--;
        var m = Math.floor(expirySeconds / 60);
        var s = expirySeconds % 60;
        if (expiryDisplay) {
            expiryDisplay.textContent = m + ':' + (s < 10 ? '0' : '') + s;
            // Turn red in final 60 seconds
            if (expirySeconds <= 60) {
                expiryDisplay.style.color = '#dc2626';
                expiryContainer.style.background = '#fee2e2';
            }
        }
        if (expirySeconds <= 0) {
            clearInterval(expiryInterval);
            if (expiryDisplay) {
                expiryContainer.innerHTML = '<i class="fa-solid fa-circle-xmark me-1" style="color:#dc2626;"></i>'
                    + '<span style="color:#dc2626;font-weight:700;">Code expired.</span>'
                    + ' <a href="{{ route("login") }}" style="color:#0C4E2D;font-weight:600;">Sign in again</a> to get a new code.';
                expiryContainer.style.background = '#fee2e2';
            }
        }
    }, 1000);

    async function resendOtp() {
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Sending...';
        try {
            const res = await fetch("{{ route('login.mfa.resend') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                resendBtn.style.display = 'none';
                resendTimer.style.display = 'inline';
                seconds = 60;
                if (countdownEl) countdownEl.textContent = seconds;
                timer = setInterval(function () {
                    seconds--;
                    if (countdownEl) countdownEl.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(timer);
                        resendTimer.style.display = 'none';
                        resendBtn.style.display = 'inline';
                        resendBtn.disabled = false;
                        resendBtn.innerHTML = '<i class="fa-solid fa-rotate-right me-1"></i> Resend Code';
                    }
                }, 1000);
            } else {
                alert(data.message || 'Failed to resend. Please go back and log in again.');
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<i class="fa-solid fa-rotate-right me-1"></i> Resend Code';
            }
        } catch (e) {
            alert('Connection error. Please try again.');
            resendBtn.disabled = false;
            resendBtn.innerHTML = '<i class="fa-solid fa-rotate-right me-1"></i> Resend Code';
        }
    }
</script>
</body>
</html>
