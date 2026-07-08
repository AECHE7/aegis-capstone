@extends('layouts.app')

@section('title', 'Security Settings')
@section('page-title', 'Security Settings')
@section('page-subtitle', 'Manage your account password and trusted devices')

@section('content')
<div class="row g-4 justify-content-center">
    <!-- Left Column: Change Password -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="p-3 bg-light rounded-4 text-success" style="color: #0C4E2D !important; background-color: #dcfce7 !important;">
                        <i class="fa-solid fa-key fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Update Password</h4>
                        <p class="text-muted small mb-0">Ensure your account is using a secure password</p>
                    </div>
                </div>

                @if(session('success') && !str_contains(session('success'), 'device'))
                    <div class="alert alert-success border-0 small mb-4" style="background-color: #dcfce7; color: #14532d; border-radius: 10px;">
                        <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 small mb-4" style="background-color: #fee2e2; color: #7f1d1d; border-radius: 10px;">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.security.update') }}" method="POST" class="mt-auto">
                    @csrf
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold text-dark small">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control rounded-3" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold text-dark small">New Password</label>
                        <input type="password" name="password" id="password" class="form-control rounded-3" required>
                        <small class="text-muted" style="font-size: 0.75rem;">Password must be at least 8 characters long.</small>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold text-dark small">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control rounded-3" required>
                    </div>

                    <button type="submit" class="btn text-white w-100 py-2.5 fw-semibold rounded-pill animate-hover" 
                            style="background-color: #0C4E2D; box-shadow: 0 4px 6px rgba(12, 78, 45, 0.15);">
                        <i class="fa-solid fa-save me-1"></i> Save Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Trusted Devices -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="p-3 bg-light rounded-4 text-info" style="color: #0284c7 !important; background-color: #e0f2fe !important;">
                        <i class="fa-solid fa-laptop-code fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Trusted Devices</h4>
                        <p class="text-muted small mb-0">Browsers that bypass MFA verification</p>
                    </div>
                </div>

                @if(session('success') && str_contains(session('success'), 'device'))
                    <div class="alert alert-success border-0 small mb-4" style="background-color: #dcfce7; color: #14532d; border-radius: 10px;">
                        <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                    </div>
                @endif

                @if($devices->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-shield-halved fs-1 mb-3" style="color: #cbd5e1;"></i>
                        <p class="mb-0 small">No remembered devices yet.</p>
                        <p class="text-muted small">Check <strong>"Remember this device"</strong> on your next login to bypass MFA.</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($devices as $device)
                            <div class="list-group-item px-0 py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="font-size: 24px; color: #475569;">
                                        @if(str_contains(strtolower($device->user_agent), 'mobile') || str_contains(strtolower($device->user_agent), 'iphone') || str_contains(strtolower($device->user_agent), 'android'))
                                            <i class="fa-solid fa-mobile-screen-button"></i>
                                        @else
                                            <i class="fa-solid fa-desktop"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1 text-dark">{{ $device->human_readable }}</h6>
                                        <p class="text-muted small mb-0">
                                            <span class="badge bg-light text-secondary border me-1">{{ $device->ip_address ?? 'Unknown IP' }}</span>
                                            Saved {{ $device->created_at->diffForHumans() }}
                                        </p>
                                        <small class="text-muted" style="font-size: 11px;">
                                            Expires: {{ $device->expires_at->format('M d, Y') }}
                                        </small>
                                    </div>
                                </div>
                                <form action="{{ route('profile.security.devices.revoke', $device->id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to revoke trust for this device? You will need to complete MFA on your next login.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-2 border-0" 
                                            title="Revoke Device" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
