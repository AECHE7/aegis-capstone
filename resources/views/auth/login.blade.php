<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} | Sign In to Account</title>
    <link rel="icon" type="image/webp" href="{{ \App\Models\Setting::get('app_logo') ? route('system.logo') : asset('logo.webp') }}">
    <link rel="icon" type="image/png" href="{{ \App\Models\Setting::get('app_logo') ? route('system.logo') : asset('logo.png') }}">

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --clsu-green: #0C4E2D;
            --clsu-gold: #D97706;
            --bg-warm: #f2f0eb;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-warm);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
    </style>
</head>
<body>

<div class="text-center position-fixed top-50 start-50 translate-middle">
    <div class="spinner-border text-success mb-2" role="status">
        <span class="visually-hidden">Loading Portal Gateway...</span>
    </div>
    <p class="text-muted small fw-semibold">Opening Secure Auth Gateway...</p>
</div>

<x-auth-modal :emergency-read-only="$emergencyReadOnly ?? false" />

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('authModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            const loginModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            loginModal.show();

            // Redirect to welcome page if modal is dismissed on standalone route
            modalEl.addEventListener('hidden.bs.modal', function () {
                window.location.href = "{{ url('/') }}";
            });
        }
    });
</script>
</body>
</html>