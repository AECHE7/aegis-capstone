<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MFA Verification | {{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #07331c 0%, #0C4E2D 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            padding: 20px;
        }
        .mfa-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 440px;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
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
            border-radius: 12px;
            border: 2px solid #cbd5e1;
            padding: 12px 5px 12px 15px;
            font-family: monospace;
            transition: all 0.2s ease;
            color: #0C4E2D;
        }
        .otp-input-field:focus {
            border-color: #0C4E2D;
            box-shadow: 0 0 0 3px rgba(12, 78, 45, 0.15);
            outline: none;
        }
        .btn-verify {
            background-color: #0C4E2D;
            color: white;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 15px;
            border: none;
            width: 100%;
            box-shadow: 0 4px 10px rgba(12, 78, 45, 0.2);
            transition: all 0.2s ease;
        }
        .btn-verify:hover {
            background-color: #07331c;
            color: white;
            transform: translateY(-1px);
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
            @if(\App\Models\Setting::get('app_logo'))
                <img src="{{ route('system.logo') }}" alt="CLSU Logo">
            @else
                <img src="{{ asset('logo.png') }}" alt="CLSU Logo">
            @endif
        </div>
        <h1 class="mfa-title">Security Verification</h1>
        <p class="mfa-desc">Enter the 6-digit verification code sent to your registered email address to complete signing in.</p>

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
                       maxlength="6" placeholder="000000" required autocomplete="off" autofocus
                       inputmode="numeric" pattern="[0-9]*">
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
    // OTP digit-only input
    document.getElementById('otpInput').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Resend countdown timer
    var seconds = 60;
    var countdownEl = document.getElementById('countdown');
    var resendTimer = document.getElementById('resendTimer');
    var resendBtn = document.getElementById('resendBtn');

    var timer = setInterval(function () {
        seconds--;
        if (countdownEl) countdownEl.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(timer);
            resendTimer.style.display = 'none';
            resendBtn.style.display = 'inline';
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
