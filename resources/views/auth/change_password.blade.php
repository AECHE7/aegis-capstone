@extends('layouts.app')

@section('title', 'Account Settings | A.E.G.I.S.')
@section('page-title', 'Account Settings')
@section('page-subtitle', 'Manage your personal profile, secure password, and active trusted devices')

@section('content')
<div class="row g-4">
    <!-- Left Column: Profile Information -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-transparent py-3 border-bottom border-light">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-user-gear me-2 text-success"></i> Profile Details</h5>
            </div>
            <div class="card-body p-4">
                @if(session('success') && (str_contains(session('success'), 'Profile') || str_contains(session('success'), 'profile')))
                    <div class="alert alert-success border-0 small mb-4" style="background-color: #dcfce7; color: #14532d; border-radius: 12px;">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any() && ($errors->has('name') || $errors->has('clsu_id_number') || $errors->has('contact_number') || $errors->has('college') || $errors->has('course') || $errors->has('year_level') || $errors->has('bank_name') || $errors->has('bank_account_name') || $errors->has('bank_account_number')))
                    <div class="alert alert-danger border-0 small mb-4" style="background-color: #fee2e2; color: #7f1d1d; border-radius: 12px;">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label for="profile_name" class="form-label fw-semibold text-dark small mb-1">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="name" id="profile_name" 
                                       class="form-control border-start-0 py-2" 
                                       value="{{ old('name', $user->name) }}" required style="border-radius: 0 10px 10px 0;">
                            </div>
                        </div>

                        <!-- Email (Read-Only) -->
                        <div class="col-md-6">
                            <label for="profile_email" class="form-label fw-semibold text-dark small mb-1">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" id="profile_email" 
                                       class="form-control border-start-0 py-2 bg-light text-muted" 
                                       value="{{ $user->email }}" readonly disabled style="border-radius: 0 10px 10px 0;">
                            </div>
                        </div>

                        @if($user->role === 'student')
                            <!-- CLSU ID Number -->
                            <div class="col-md-6">
                                <label for="clsu_id_number" class="form-label fw-semibold text-dark small mb-1">CLSU ID Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-id-card"></i></span>
                                    <input type="text" name="clsu_id_number" id="clsu_id_number" 
                                           class="form-control border-start-0 py-2" 
                                           placeholder="e.g. 2023-4567"
                                           value="{{ old('clsu_id_number', $user->profile->clsu_id_number ?? '') }}" required style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>

                            <!-- Contact Number -->
                            <div class="col-md-6">
                                <label for="contact_number" class="form-label fw-semibold text-dark small mb-1">Contact Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" name="contact_number" id="contact_number" 
                                           class="form-control border-start-0 py-2" 
                                           placeholder="e.g. 09123456789"
                                           value="{{ old('contact_number', $user->profile->contact_number ?? '') }}" required style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>

                            <!-- College -->
                            <div class="col-md-6">
                                <label for="college" class="form-label fw-semibold text-dark small mb-1">College</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-building-columns"></i></span>
                                    <select name="college" id="college" class="form-select border-start-0 py-2" required style="border-radius: 0 10px 10px 0;">
                                        <option value="" disabled {{ !old('college', $user->profile->college ?? '') ? 'selected' : '' }}>Select College...</option>
                                        @foreach([
                                            'College of Agriculture',
                                            'College of Arts and Social Sciences',
                                            'College of Business Administration and Accountancy',
                                            'College of Education',
                                            'College of Engineering',
                                            'College of Home Science and Industry',
                                            'College of Science',
                                            'College of Veterinary Science and Medicine'
                                        ] as $college)
                                            <option value="{{ $college }}" {{ old('college', $user->profile->college ?? '') === $college ? 'selected' : '' }}>{{ $college }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Year Level -->
                            <div class="col-md-6">
                                <label for="year_level" class="form-label fw-semibold text-dark small mb-1">Year Level</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-layer-group"></i></span>
                                    <select name="year_level" id="year_level" class="form-select border-start-0 py-2" required style="border-radius: 0 10px 10px 0;">
                                        <option value="" disabled {{ !old('year_level', $user->profile->year_level ?? '') ? 'selected' : '' }}>Select Year Level...</option>
                                        @foreach(['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year', '6th Year'] as $yl)
                                            <option value="{{ $yl }}" {{ old('year_level', $user->profile->year_level ?? '') === $yl ? 'selected' : '' }}>{{ $yl }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Course -->
                            <div class="col-12">
                                <label for="course" class="form-label fw-semibold text-dark small mb-1">Degree Course / Program</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-graduation-cap"></i></span>
                                    <input type="text" name="course" id="course" 
                                           class="form-control border-start-0 py-2" 
                                           placeholder="e.g. BS Information Technology"
                                           value="{{ old('course', $user->profile->course ?? '') }}" required style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>

                            <!-- Bank Stipend Details -->
                            <div class="col-12 mt-3 pt-3 border-top">
                                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-money-bill-transfer text-success me-2"></i> Landbank Stipend Release Details</h6>
                                <p class="text-muted small mb-3">Provide your Landbank account details to receive stipend releases upon scholarship payouts.</p>
                            </div>

                            <div class="col-md-4">
                                <label for="bank_name" class="form-label fw-semibold text-dark small mb-1">Bank Name</label>
                                <input type="text" name="bank_name" id="bank_name" class="form-control py-2" placeholder="e.g. Landbank" value="{{ old('bank_name', $user->profile->bank_name ?? '') }}" style="border-radius: 10px;">
                            </div>

                            <div class="col-md-4">
                                <label for="bank_account_name" class="form-label fw-semibold text-dark small mb-1">Account Name</label>
                                <input type="text" name="bank_account_name" id="bank_account_name" class="form-control py-2" placeholder="e.g. JUAN DELA CRUZ" value="{{ old('bank_account_name', $user->profile->bank_account_name ?? '') }}" style="border-radius: 10px;">
                            </div>

                            <div class="col-md-4">
                                <label for="bank_account_number" class="form-label fw-semibold text-dark small mb-1">Account Number</label>
                                <input type="text" name="bank_account_number" id="bank_account_number" class="form-control py-2" placeholder="e.g. 1234-5678-90" value="{{ old('bank_account_number', $user->profile->bank_account_number ?? '') }}" style="border-radius: 10px;">
                            </div>
                        @else
                            <!-- Admin / SuperAdmin Role Badge -->
                            <div class="col-12 mt-2">
                                <span class="badge py-2 px-3 fs-7" 
                                      style="background-color: {{ $user->role === 'superadmin' ? '#fef3c7' : '#e0f2fe' }}; 
                                             color: {{ $user->role === 'superadmin' ? '#92400e' : '#0369a1' }}; 
                                             border-radius: 8px; font-weight: 700;">
                                    <i class="fa-solid {{ $user->role === 'superadmin' ? 'fa-crown' : 'fa-user-shield' }} me-1"></i>
                                    {{ $user->role === 'superadmin' ? 'SuperAdmin (OSA Director)' : 'Admin Staff Account' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        @if($user->role === 'student')
                            <div class="text-success small fw-semibold d-flex align-items-center gap-1">
                                <i class="fa-solid fa-lock"></i> AES-256 Encrypted Fields
                            </div>
                        @else
                            <div></div>
                        @endif
                        <button type="submit" class="btn btn-primary text-white px-4 py-2 fw-semibold shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Password & Trusted Devices -->
    <div class="col-lg-5">
        <!-- Part 1: Update Password -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="p-3 bg-light rounded-4 text-success" style="color: #0C4E2D !important; background-color: #dcfce7 !important;">
                        <i class="fa-solid fa-key fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Update Password</h5>
                        <p class="text-muted small mb-0">Secure your portal password</p>
                    </div>
                </div>

                @if(session('success') && str_contains(session('success'), 'password'))
                    <div class="alert alert-success border-0 small mb-4" style="background-color: #dcfce7; color: #14532d; border-radius: 12px;">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any() && ($errors->has('current_password') || $errors->has('password')))
                    <div class="alert alert-danger border-0 small mb-4" style="background-color: #fee2e2; color: #7f1d1d; border-radius: 12px;">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                @if(str_contains($error, 'password') || str_contains($error, 'Password'))
                                    <li>{{ $error }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.security.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold text-dark small">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" style="border-radius: 10px;" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold text-dark small">New Password</label>
                        <input type="password" name="password" id="password" class="form-control" style="border-radius: 10px;" required>
                        <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Must be at least 8 characters long.</small>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold text-dark small">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" style="border-radius: 10px;" required>
                    </div>

                    <button type="submit" class="btn btn-primary text-white w-100 py-2 fw-semibold shadow-sm">
                        <i class="fa-solid fa-save me-1"></i> Save Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Part 2: Trusted Devices -->
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="p-3 bg-light rounded-4 text-info" style="color: #0284c7 !important; background-color: #e0f2fe !important;">
                        <i class="fa-solid fa-laptop-code fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Trusted Devices</h5>
                        <p class="text-muted small mb-0">Browsers that bypass MFA checks</p>
                    </div>
                </div>

                @if(session('success') && str_contains(session('success'), 'device'))
                    <div class="alert alert-success border-0 small mb-4" style="background-color: #dcfce7; color: #14532d; border-radius: 12px;">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if($devices->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fa-solid fa-shield-halved fs-2 mb-3" style="color: #cbd5e1;"></i>
                        <p class="mb-0 small">No remembered devices yet.</p>
                        <p class="text-muted small mt-1" style="font-size: 0.75rem;">Check <strong>"Remember this device"</strong> on your next login to bypass MFA.</p>
                    </div>
                @else
                    <div class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                        @foreach($devices as $device)
                            <div class="list-group-item px-0 py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="font-size: 20px; color: #475569;">
                                        @if(str_contains(strtolower($device->user_agent), 'mobile') || str_contains(strtolower($device->user_agent), 'iphone') || str_contains(strtolower($device->user_agent), 'android'))
                                            <i class="fa-solid fa-mobile-screen-button"></i>
                                        @else
                                            <i class="fa-solid fa-desktop"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-0 text-dark" style="font-size: 0.88rem;">{{ $device->human_readable }}</h6>
                                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                            <span class="badge bg-light text-secondary border me-1">{{ $device->ip_address ?? 'Unknown IP' }}</span>
                                            {{ $device->created_at->diffForHumans() }}
                                        </p>
                                        <small class="text-muted" style="font-size: 10px;">
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
