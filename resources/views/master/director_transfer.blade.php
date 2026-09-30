@extends('layouts.app')

@section('title', 'Director Ownership Transfer | A.E.G.I.S.')
@section('page-title', 'Transfer System Ownership')
@section('page-subtitle', 'Invite your client to become the OSA Director of A.E.G.I.S.')

@section('content')
<div class="container-fluid px-0" style="max-width: 1060px; margin: 0 auto; padding: 1.5rem 1rem 4rem;">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert border-0 mb-4 shadow-sm d-flex align-items-center gap-2" role="status" style="border-radius: 12px; background: #dcfce7; color: #14532d; border-left: 4px solid #22c55e !important;">
            <i class="fa-solid fa-circle-check fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert border-0 mb-4 shadow-sm d-flex align-items-center gap-2" role="alert" style="border-radius: 12px; background: #fee2e2; color: #7f1d1d; border-left: 4px solid #ef4444 !important;">
            <i class="fa-solid fa-circle-exclamation fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="card border-0 mb-4 p-4" style="border-radius: 20px; background: linear-gradient(135deg, #0C4E2D 0%, #07331c 100%); color: white; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -30px; top: -30px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,0.04); pointer-events: none;"></div>
        <div style="position: absolute; right: 30px; top: 30px; width: 100px; height: 100px; border-radius: 50%; background: rgba(217,119,6,0.12); pointer-events: none;"></div>
        <div class="d-flex align-items-center gap-3">
            <div style="width: 54px; height: 54px; border-radius: 16px; background: rgba(217,119,6,0.2); display:flex; align-items:center; justify-content:center; flex-shrink:0; border: 1px solid rgba(217,119,6,0.3);">
                <i class="fa-solid fa-crown fa-lg" style="color: #D97706;"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1" style="color: white;">Director Ownership Transfer</h5>
                <p class="mb-0 small" style="color: rgba(255,255,255,0.65);">
                    Invite your client via email to take ownership as the <strong style="color: #fcd34d;">OSA Director (superadmin)</strong>.
                    Once accepted, they gain full governance access to the A.E.G.I.S. portal.
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- LEFT: Send Invitation --}}
        <div class="col-lg-5">

            {{-- Send Invitation Form --}}
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                <h6 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-envelope-open-text text-success me-2"></i>Send Director Invitation</h6>
                <p class="text-muted small mb-4">Enter the client's email address. They will receive a secure link to register and accept the Director role.</p>

                <form action="{{ route('director.send-invitation') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="directorName" class="form-label fw-semibold text-dark small">Full Name <span class="text-muted fw-normal">(optional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                            <input type="text" name="recipient_name" id="directorName"
                                class="form-control border-start-0"
                                placeholder="e.g. Dr. Maria Santos"
                                value="{{ old('recipient_name') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="directorEmail" class="form-label fw-semibold text-dark small">Email Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="recipient_email" id="directorEmail"
                                class="form-control border-start-0"
                                placeholder="e.g. director@clsu.edu.ph"
                                value="{{ old('recipient_email') }}"
                                required>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            The recipient must register or log in using exactly this email to accept.
                        </div>
                    </div>

                    <button type="submit" class="btn w-100 fw-bold py-2 rounded-pill text-white"
                        style="background: linear-gradient(135deg, #0C4E2D, #07331c); border: none; box-shadow: 0 4px 14px rgba(12,78,45,0.3);">
                        <i class="fa-solid fa-paper-plane me-2"></i>Send Director Invitation
                    </button>
                </form>
            </div>

            {{-- Current Directors --}}
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                <h6 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-users-gear text-primary me-2"></i>Current Directors</h6>
                @forelse($currentDirectors as $director)
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg,#0C4E2D,#1a7a47); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fa-solid fa-crown text-warning" style="font-size: 0.9rem;"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-dark small text-truncate">{{ $director->name }}</div>
                            <div class="text-muted" style="font-size: 0.72rem; word-break: break-all;">{{ $director->email }}</div>
                        </div>
                        <span class="badge rounded-pill fw-semibold" style="background: #0C4E2D; font-size: 0.65rem; padding: 4px 10px;">
                            Director
                        </span>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">
                        <i class="fa-solid fa-circle-xmark mb-2 d-block fs-4 text-muted opacity-40"></i>
                        No Director accounts exist yet.<br>Send an invitation to get started.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT: Invitation History --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h6 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-clock-rotate-left text-warning me-2"></i>Invitation History</h6>

                {{-- Pending --}}
                @if($pendingInvitations->isNotEmpty())
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; animation: pulse 1.5s infinite;"></div>
                            <span class="fw-semibold text-dark small">Pending Invitations</span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @foreach($pendingInvitations as $inv)
                                <div class="p-3 rounded-3 d-flex align-items-start gap-3" style="background: #fffbeb; border: 1px dashed #D97706;">
                                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(217,119,6,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i class="fa-solid fa-hourglass-half text-warning" style="font-size:0.8rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        @if($inv->recipient_name)
                                            <div class="fw-semibold text-dark small">{{ $inv->recipient_name }}</div>
                                        @endif
                                        <div class="text-dark small" style="word-break: break-all;">{{ $inv->recipient_email }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            Sent {{ $inv->created_at->diffForHumans() }} &bull;
                                            Expires {{ $inv->expires_at->format('M d, Y h:i A') }}
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-2">
                                        <span class="badge rounded-pill fw-semibold" style="background: #D97706; font-size:0.65rem; padding: 4px 10px;">Pending</span>
                                        <form action="{{ route('director.revoke-invitation', $inv->id) }}" method="POST"
                                              onsubmit="return confirm('Revoke invitation for {{ $inv->recipient_email }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill"
                                                style="font-size: 0.65rem; padding: 2px 10px; border-radius: 20px !important;">
                                                <i class="fa-solid fa-ban me-1"></i>Revoke
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Completed --}}
                @if($completedInvitations->isNotEmpty())
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e;"></div>
                            <span class="fw-semibold text-dark small">Accepted</span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @foreach($completedInvitations as $inv)
                                <div class="p-3 rounded-3 d-flex align-items-start gap-3" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(22,163,74,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i class="fa-solid fa-circle-check text-success" style="font-size:0.85rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        @if($inv->recipient_name)
                                            <div class="fw-semibold text-dark small">{{ $inv->recipient_name }}</div>
                                        @endif
                                        <div class="text-dark small" style="word-break: break-all;">{{ $inv->recipient_email }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            Accepted {{ $inv->accepted_at->format('M d, Y · h:i A') }}
                                        </div>
                                    </div>
                                    <span class="badge rounded-pill fw-semibold bg-success" style="font-size:0.65rem; padding: 4px 10px; flex-shrink:0;">Accepted</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Expired --}}
                @if($expiredInvitations->isNotEmpty())
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #94a3b8;"></div>
                            <span class="fw-semibold text-muted small">Expired / Revoked</span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @foreach($expiredInvitations as $inv)
                                <div class="p-3 rounded-3 d-flex align-items-start gap-3 opacity-60" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i class="fa-solid fa-clock text-muted" style="font-size:0.8rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        @if($inv->recipient_name)
                                            <div class="fw-semibold text-muted small">{{ $inv->recipient_name }}</div>
                                        @endif
                                        <div class="text-muted small" style="word-break: break-all;">{{ $inv->recipient_email }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            Expired {{ $inv->expires_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <span class="badge rounded-pill fw-semibold bg-secondary" style="font-size:0.65rem; padding: 4px 10px; flex-shrink:0;">Expired</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($pendingInvitations->isEmpty() && $completedInvitations->isEmpty() && $expiredInvitations->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-envelope-open d-block mb-3 fs-2 opacity-30"></i>
                        <div class="small">No invitations sent yet.</div>
                        <div class="small">Use the form on the left to invite your client as Director.</div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Back link --}}
    <div class="mt-4 text-center">
        <a href="{{ route('master.gateway') }}" class="text-decoration-none text-muted small">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Master Gateway
        </a>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}
</style>
@endsection
