@extends('layouts.app')

@section('title', 'Verify Privilege Acceptance | A.E.G.I.S.')
@section('page-title', 'Accept Master Privilege')
@section('page-subtitle', 'Confirm and activate your Master role privileges')

@section('content')
<div class="container" style="max-width: 600px; padding: 3rem 1rem;">
    <div class="card border-0 shadow-lg p-4 p-md-5 position-relative" style="border-radius: 24px; overflow: hidden; background: white;">
        
        <!-- Subtle gradient glow -->
        <div style="position: absolute; top: -50px; left: 50%; transform: translateX(-50%); width: 220px; height: 100px; background: radial-gradient(circle, rgba(217,119,6,0.08) 0%, transparent 70%); pointer-events: none;"></div>

        <div class="text-center mb-4">
            <div style="width: 80px; height: 80px; border-radius: 20px; background: rgba(217,119,6,0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; position: relative;">
                <div style="position: absolute; inset: 5px; border-radius: 16px; border: 2px dashed rgba(217,119,6,0.25);"></div>
                <i class="fa-solid fa-key fa-2x text-warning" style="position: relative; z-index: 2;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">Verify Master Privilege Transfer</h4>
            <p class="text-muted small">A.E.G.I.S. Role-Switching Security Protocol</p>
        </div>

        <div class="p-3 bg-light border mb-4 text-start" style="border-radius: 14px; font-size: 0.88rem; line-height: 1.5;">
            <p class="mb-2"><strong>Sender:</strong> <span class="text-dark fw-semibold">{{ $transfer->sender_email }}</span></p>
            <p class="mb-2"><strong>Recipient (You):</strong> <span class="text-dark fw-semibold">{{ $transfer->recipient_email }}</span></p>
            <p class="mb-0"><strong>Expires:</strong> <span class="text-muted">{{ $transfer->expires_at->format('M d, Y · h:i A') }}</span></p>
        </div>

        <div class="alert alert-warning border-0 small text-start mb-4" style="background: #fffbeb; color: #78350f; border-left: 4px solid var(--clsu-gold) !important; border-radius: 12px;">
            <div class="d-flex gap-2">
                <i class="fa-solid fa-circle-info fs-5 mt-1" style="color: var(--clsu-gold);"></i>
                <div>
                    <strong>Revocation Warning:</strong> Upon clicking "Accept & Activate", the sender's Master privilege is immediately revoked. Your account (<strong>{{ $transfer->recipient_email }}</strong>) will become the system's active Master account.
                </div>
            </div>
        </div>

        <form action="{{ route('master.accept-transfer', ['token' => $transfer->token]) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-warning w-100 fw-bold py-3 rounded-pill text-dark d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, var(--clsu-gold), #e09500); border: none; font-size: 1rem; box-shadow: 0 4px 14px rgba(217,119,6,0.3);">
                <i class="fa-solid fa-circle-check"></i> Accept & Activate Privileges
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-decoration-none small text-muted">Decline and return to login</a>
        </div>
    </div>
</div>
@endsection
