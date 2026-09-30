@extends('layouts.app')

@section('title', 'Accept Director Role | A.E.G.I.S.')
@section('page-title', 'Accept System Ownership')
@section('page-subtitle', 'You have been invited to become the A.E.G.I.S. OSA Director')

@section('content')
<div class="container" style="max-width: 640px; padding: 3rem 1rem;">

    {{-- Celebration header --}}
    <div class="text-center mb-4">
        <div style="width: 90px; height: 90px; border-radius: 24px; background: linear-gradient(135deg, #0C4E2D, #1a7a47); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; box-shadow: 0 8px 32px rgba(12,78,45,0.25);">
            <i class="fa-solid fa-crown fa-2x text-warning"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Director Role Invitation</h3>
        <p class="text-muted small">Review the details below and accept to activate your full Director access.</p>
    </div>

    <div class="card border-0 shadow-lg p-4 p-md-5" style="border-radius: 24px; overflow: hidden; position: relative;">

        {{-- Gradient glow --}}
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #0C4E2D, #16a34a, #D97706);"></div>

        {{-- Sender info --}}
        <div class="p-3 mb-4 rounded-3" style="background: #f0fdf4; border: 1px solid #bbf7d0; font-size: 0.88rem;">
            <div class="row g-2">
                <div class="col-5 text-muted">Invited by</div>
                <div class="col-7 fw-semibold text-dark" style="word-break: break-all;">{{ $invitation->invited_by_email }}</div>

                <div class="col-5 text-muted">Your email</div>
                <div class="col-7 fw-semibold text-dark" style="word-break: break-all;">{{ $invitation->recipient_email }}</div>

                <div class="col-5 text-muted">Role granted</div>
                <div class="col-7">
                    <span class="badge rounded-pill fw-bold" style="background: #0C4E2D; font-size: 0.7rem; padding: 5px 12px; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-crown me-1" style="color: #D97706;"></i> OSA DIRECTOR · SUPERADMIN
                    </span>
                </div>

                <div class="col-5 text-muted">Link expires</div>
                <div class="col-7 fw-semibold text-danger">{{ $invitation->expires_at->format('M d, Y · h:i A') }}</div>
            </div>
        </div>

        {{-- Privileges list --}}
        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-shield-halved text-success me-2"></i>Your Director Privileges</h6>
            <div class="row g-2">
                @foreach([
                    ['fa-graduation-cap', 'Scholarship program management'],
                    ['fa-users-gear',     'Staff invitation &amp; management'],
                    ['fa-chart-line',     'System analytics &amp; reporting'],
                    ['fa-file-export',    'Full audit log exports'],
                    ['fa-gear',           'Global system settings'],
                    ['fa-shield-check',   'Security &amp; device controls'],
                ] as [$icon, $label])
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2 text-dark" style="font-size: 0.8rem;">
                        <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(12,78,45,0.08); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fa-solid {{ $icon }} text-success" style="font-size: 0.75rem;"></i>
                        </div>
                        {!! $label !!}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Warning --}}
        <div class="alert border-0 small mb-4 d-flex gap-2" style="background: #fffbeb; color: #78350f; border-left: 4px solid #D97706 !important; border-radius: 12px;">
            <i class="fa-solid fa-circle-info fs-5 mt-1" style="color: #D97706; flex-shrink: 0;"></i>
            <div>
                <strong>Before you accept:</strong> Make sure you are logged in as
                <strong>{{ $invitation->recipient_email }}</strong>.
                Once accepted, your account will immediately gain full Director access to the A.E.G.I.S. system.
            </div>
        </div>

        {{-- Accept form --}}
        <form action="{{ route('director.accept-invitation', ['token' => $invitation->token]) }}" method="POST">
            @csrf
            <button type="submit" id="acceptDirectorBtn"
                class="btn w-100 fw-bold py-3 rounded-pill text-white d-flex align-items-center justify-content-center gap-2"
                style="background: linear-gradient(135deg, #0C4E2D, #07331c); border: none; font-size: 1rem; box-shadow: 0 6px 20px rgba(12,78,45,0.3); transition: all 0.2s;">
                <i class="fa-solid fa-crown" style="color: #D97706;"></i>
                Accept &amp; Activate Director Role
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-decoration-none small text-muted">
                <i class="fa-solid fa-arrow-left me-1"></i>Decline and return to login
            </a>
        </div>
    </div>
</div>
@endsection
