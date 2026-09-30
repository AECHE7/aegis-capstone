@extends('layouts.app')

@section('title', 'Account Settings | A.E.G.I.S.')
@section('page-title', 'Account Settings')
@section('page-subtitle', 'Manage your personal profile, secure password, and active trusted devices')

@section('content')
<div class="row g-4">
    <!-- ── ACCOUNT OVERVIEW & HERO BANNER ── -->
    <div class="col-12">
        <div class="card card-dark-hero account-hero-card border-0 shadow-sm rounded-4 overflow-hidden position-relative" 
             style="background: linear-gradient(135deg, #072F1B 0%, #0C4E2D 55%, #166534 100%) !important; color: #ffffff !important;">
            <div class="card-body p-3 p-md-4 d-flex align-items-center justify-content-between flex-wrap gap-3"
                 style="background: transparent !important; color: #ffffff !important;">
                <div class="d-flex align-items-center gap-3">
                    <!-- Profile Avatar Circle with Initials -->
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs flex-shrink-0" 
                         style="width: 54px; height: 54px; background: rgba(255, 255, 255, 0.22) !important; border: 2px solid rgba(255, 255, 255, 0.55) !important; font-size: 1.3rem; letter-spacing: 0.5px; color: #ffffff !important;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strstr($user->name, ' ') ?: ' ', 1, 1)) }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <h4 class="fw-bold mb-0 text-white" style="font-size: 1.2rem; color: #ffffff !important;">{{ $user->name }}</h4>
                            @if($user->role === 'student' && $user->isProfileComplete())
                                <span class="badge rounded-pill px-2.5 py-0.5 fw-semibold d-inline-flex align-items-center gap-1" 
                                      style="background: rgba(16, 185, 129, 0.35) !important; color: #d1fae5 !important; border: 1px solid rgba(52, 211, 153, 0.55) !important; font-size: 0.72rem;">
                                    <i class="fa-solid fa-circle-check"></i> Verified Profile
                                </span>
                            @elseif($user->role === 'student')
                                <span class="badge rounded-pill px-2.5 py-0.5 fw-bold d-inline-flex align-items-center gap-1" 
                                      style="background: #f59e0b !important; color: #111827 !important; border: 1px solid #d97706 !important; font-size: 0.72rem;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Incomplete Profile
                                </span>
                            @else
                                <span class="badge rounded-pill px-2.5 py-0.5 fw-semibold" 
                                      style="background: rgba(255, 255, 255, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.45) !important; font-size: 0.72rem;">
                                    <i class="fa-solid fa-user-shield me-1"></i> {{ strtoupper($user->role) }}
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2 gap-md-3 small flex-wrap" style="font-size: 0.82rem; color: rgba(255,255,255,0.92) !important;">
                            <span style="color: rgba(255,255,255,0.92) !important;"><i class="fa-solid fa-envelope me-1 text-warning"></i>{{ $user->email }}</span>
                            @if($user->role === 'student' && !empty($user->profile->clsu_id_number))
                                <span class="d-none d-sm-inline opacity-50 text-white">&bull;</span>
                                <span style="color: rgba(255,255,255,0.92) !important;"><i class="fa-solid fa-id-card me-1 text-warning"></i>ID: <strong style="color: #ffffff !important;">{{ $user->profile->clsu_id_number }}</strong></span>
                            @endif
                            @if($user->role === 'student' && !empty($user->profile->college))
                                <span class="d-none d-sm-inline opacity-50 text-white">&bull;</span>
                                <span style="color: rgba(255,255,255,0.92) !important;"><i class="fa-solid fa-building-columns me-1 text-warning"></i>{{ $user->profile->college }} ({{ $user->profile->year_level ?? 'Student' }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                    @if($user->role === 'student' && $user->isProfileComplete())
                        <a href="{{ route('student.apply') }}" class="btn btn-sm px-3.5 py-2 rounded-pill fw-bold shadow-sm" 
                           style="background: #f59e0b !important; color: #111827 !important; border: 1px solid #d97706 !important; font-size: 0.82rem;">
                            <i class="fa-solid fa-paper-plane me-1.5"></i> Apply for Scholarship
                        </a>
                    @endif
                    <button type="button" class="btn btn-sm px-3.5 py-2 rounded-pill fw-semibold shadow-sm" 
                            style="background: rgba(255, 255, 255, 0.22) !important; border: 1.5px solid rgba(255, 255, 255, 0.55) !important; backdrop-filter: blur(4px); font-size: 0.82rem; color: #ffffff !important;"
                            title="Interactive Guided Demo & Training Guide"
                            onclick="openSystemTourModal('{{ auth()->user()->role }}')">
                        <i class="fa-solid fa-graduation-cap me-1.5 text-warning"></i> Start Guided Tour
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── LEFT COLUMN: PROFILE INFORMATION ── -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg, #ffffff);">
            <div class="card-header bg-transparent py-3.5 px-4 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" 
                         style="width: 38px; height: 38px; background: rgba(12, 78, 45, 0.1); color: var(--clsu-green, #0C4E2D);">
                        <i class="fa-solid fa-user-pen fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">Personal & Academic Information</h5>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem;">Keep your institutional university records up to date</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Notifications & Verification Alerts -->
                @if(session('warning'))
                    <div class="alert alert-warning border-0 small mb-4 d-flex align-items-center gap-2" style="background-color: #fef3c7; color: #92400e; border-radius: 12px;">
                        <i class="fa-solid fa-triangle-exclamation fs-5 flex-shrink-0"></i>
                        <div>{{ session('warning') }}</div>
                    </div>
                @elseif($user->role === 'student' && !$user->isProfileComplete())
                    <div class="alert alert-warning border-0 small mb-4 d-flex align-items-center gap-2" style="background-color: #fef3c7; color: #92400e; border-radius: 12px;">
                        <i class="fa-solid fa-triangle-exclamation fs-5 flex-shrink-0"></i>
                        <div>Please complete your basic student profile information below before submitting scholarship applications.</div>
                    </div>
                @endif

                @if(session('success') && !str_contains(strtolower(session('success')), 'password') && !str_contains(strtolower(session('success')), 'device'))
                    <div class="alert alert-success border-0 small mb-4 d-flex align-items-center gap-2" style="background-color: #dcfce7; color: #14532d; border-radius: 12px;">
                        <i class="fa-solid fa-circle-check fs-5 flex-shrink-0"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if($errors->any() && ($errors->has('name') || $errors->has('clsu_id_number') || $errors->has('contact_number') || $errors->has('college') || $errors->has('course') || $errors->has('year_level') || $errors->has('guardian_name') || $errors->has('emergency_contact_number')))
                    <div class="alert alert-danger border-0 small mb-4" style="background-color: #fee2e2; color: #7f1d1d; border-radius: 12px;">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ $user->role === 'student' ? route('student.profile.update') : route('profile.update') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label for="profile_name" class="form-label fw-semibold text-dark small mb-1">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="name" id="profile_name" 
                                       class="form-control border-start-0 py-2" 
                                       autocomplete="name"
                                       value="{{ old('name', $user->name) }}" required style="border-radius: 0 10px 10px 0;">
                            </div>
                        </div>

                        <!-- Email (Institutional Read-Only) -->
                        <div class="col-md-6">
                            <label for="profile_email" class="form-label fw-semibold text-dark small mb-1">
                                Institutional Email <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.65rem;">Verified</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" id="profile_email" 
                                       class="form-control border-start-0 py-2 bg-light text-muted" 
                                       autocomplete="email"
                                       value="{{ $user->email }}" readonly disabled style="border-radius: 0 10px 10px 0;">
                            </div>
                        </div>

                        @if($user->role === 'student')
                            <!-- CLSU ID Number -->
                            <div class="col-md-6">
                                <label for="clsu_id_number" class="form-label fw-semibold text-dark small mb-1">
                                    CLSU ID Number <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-id-card"></i></span>
                                    <input type="text" name="clsu_id_number" id="clsu_id_number" 
                                           class="form-control border-start-0 py-2 font-monospace" 
                                           autocomplete="off"
                                           placeholder="e.g. 23-1234" pattern="\d{2}-\d{4}" maxlength="7"
                                           title="Format: 00-0000 (e.g. 23-1234)"
                                           value="{{ old('clsu_id_number', $user->profile->clsu_id_number ?? '') }}" required style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>

                            <!-- Contact Number -->
                            <div class="col-md-6">
                                <label for="contact_number" class="form-label fw-semibold text-dark small mb-1">
                                    Mobile Phone Number <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" name="contact_number" id="contact_number" 
                                           class="form-control border-start-0 py-2" 
                                           autocomplete="tel"
                                           placeholder="e.g. 09123456789"
                                           value="{{ old('contact_number', $user->profile->contact_number ?? '') }}" required style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>

                            <!-- College -->
                            <div class="col-md-6">
                                <label for="college" class="form-label fw-semibold text-dark small mb-1">
                                    Enrolled College <span class="text-danger">*</span>
                                </label>
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
                                <label for="year_level" class="form-label fw-semibold text-dark small mb-1">
                                    Year Level <span class="text-danger">*</span>
                                </label>
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

                            <!-- Degree Course -->
                            <div class="col-12">
                                <label for="course" class="form-label fw-semibold text-dark small mb-1">
                                    Degree Course / Program <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-graduation-cap"></i></span>
                                    <input type="text" name="course" id="course" 
                                           class="form-control border-start-0 py-2" 
                                           autocomplete="off"
                                           placeholder="e.g. BS Information Technology"
                                           value="{{ old('course', $user->profile->course ?? '') }}" required style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>

                            <!-- Guardian & Emergency Contact -->
                            <div class="col-md-6">
                                <label for="guardian_name" class="form-label fw-semibold text-dark small mb-1">
                                    Parent / Guardian Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-user-shield"></i></span>
                                    <input type="text" name="guardian_name" id="guardian_name" 
                                           class="form-control border-start-0 py-2" 
                                           autocomplete="name"
                                           placeholder="e.g. Maria Santos"
                                           value="{{ old('guardian_name', $user->profile->guardian_name ?? '') }}" required style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="emergency_contact_number" class="form-label fw-semibold text-dark small mb-1">
                                    Emergency Phone Number <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" name="emergency_contact_number" id="emergency_contact_number" 
                                           class="form-control border-start-0 py-2" 
                                           autocomplete="tel"
                                           placeholder="e.g. 09123456789"
                                           value="{{ old('emergency_contact_number', $user->profile->emergency_contact_number ?? '') }}" required style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>
                        @else
                            <!-- Admin / SuperAdmin Role Overview -->
                            <div class="col-12 mt-2">
                                <div class="p-3 rounded-3 mb-3" style="background-color: {{ $user->role === 'superadmin' ? '#fffbeb' : '#f0f9ff' }}; border: 1px solid {{ $user->role === 'superadmin' ? '#fde68a' : '#bae6fd' }};">
                                    <div class="d-flex align-items-center gap-2 mb-1.5">
                                        <span class="badge py-1.5 px-2.5 fs-7" 
                                              style="background-color: {{ $user->role === 'superadmin' ? '#fef3c7' : '#e0f2fe' }}; 
                                                     color: {{ $user->role === 'superadmin' ? '#92400e' : '#0369a1' }}; 
                                                     border-radius: 6px; font-weight: 700;">
                                            <i class="fa-solid {{ $user->role === 'superadmin' ? 'fa-crown' : 'fa-user-shield' }} me-1"></i>
                                            {{ $user->role === 'superadmin' ? 'SuperAdmin (OSA Director)' : 'Admin Staff Account' }}
                                        </span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-semibold px-2 py-0.5 small">
                                            <i class="fa-solid fa-circle-check me-1"></i> Active
                                        </span>
                                    </div>
                                    <p class="small text-muted mb-0" style="line-height: 1.5;">
                                        {{ $user->role === 'superadmin' 
                                            ? 'Full administrative governance over CLSU scholarship programs, evaluator staff assignments, and integrity audit trails.' 
                                            : 'Authorized evaluator for student scholarship applications, GWA verification, and document fraud forensics.' }}
                                    </p>
                                </div>

                                <div class="card border border-light-subtle rounded-3 p-3 bg-light-subtle">
                                    <h6 class="fw-bold text-dark mb-2 small text-uppercase" style="letter-spacing: 0.5px;">
                                        <i class="fa-solid fa-shield-halved text-success me-1"></i> System Privileges
                                    </h6>
                                    <div class="row g-2 small text-secondary">
                                        <div class="col-sm-6 d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-check text-success" style="font-size: 0.75rem;"></i> Application Queue & Evaluation
                                        </div>
                                        <div class="col-sm-6 d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-check text-success" style="font-size: 0.75rem;"></i> AI Document Integrity Inspection
                                        </div>
                                        @if($user->role === 'superadmin')
                                            <div class="col-sm-6 d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-check text-success" style="font-size: 0.75rem;"></i> Program & Grant Configuration
                                            </div>
                                            <div class="col-sm-6 d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-check text-success" style="font-size: 0.75rem;"></i> Statutory Regulatory Audit Exports
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        @if($user->role === 'student')
                            <div class="text-success small fw-semibold d-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-shield-halved"></i> AES-256 Encrypted & R.A. 10173 Protected
                            </div>
                        @else
                            <div></div>
                        @endif
                        <button type="submit" class="btn btn-success px-4 py-2 rounded-pill fw-bold text-white shadow-xs" 
                                style="background: var(--clsu-green, #0C4E2D); font-size: 0.85rem;">
                            <i class="fa-solid fa-floppy-disk me-1.5"></i> Save Profile Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ── RIGHT COLUMN: SECURITY, TRUSTED DEVICES & RESOURCES ── -->
    <div class="col-lg-5">
        <div class="d-flex flex-column gap-4">
            
            <!-- Part 1: Update Password -->
            <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg, #ffffff);">
                <div class="card-header bg-transparent py-3.5 px-4 border-bottom d-flex align-items-center gap-2.5">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" 
                         style="width: 38px; height: 38px; background: rgba(12, 78, 45, 0.1); color: var(--clsu-green, #0C4E2D);">
                        <i class="fa-solid fa-key fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">Account Password</h5>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem;">Update your security credentials</p>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if(session('success') && str_contains(session('success'), 'password'))
                        <div class="alert alert-success border-0 small mb-3 d-flex align-items-center gap-2" style="background-color: #dcfce7; color: #14532d; border-radius: 12px;">
                            <i class="fa-solid fa-circle-check fs-5 flex-shrink-0"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    @if($errors->any() && ($errors->has('current_password') || $errors->has('password')))
                        <div class="alert alert-danger border-0 small mb-3" style="background-color: #fee2e2; color: #7f1d1d; border-radius: 12px;">
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
                            <label for="current_password" class="form-label fw-semibold text-dark small mb-1">Current Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="current_password" id="current_password" class="form-control border-start-0 py-2" autocomplete="current-password" style="border-radius: 0 10px 10px 0;" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold text-dark small mb-1">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-key"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-start-0 py-2" autocomplete="new-password" style="border-radius: 0 10px 10px 0;" required>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Must be at least 8 characters long.</small>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold text-dark small mb-1">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-check-double"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 py-2" autocomplete="new-password" style="border-radius: 0 10px 10px 0;" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-outline-success w-100 py-2 rounded-pill fw-bold" 
                                style="border-color: var(--clsu-green, #0C4E2D); color: var(--clsu-green, #0C4E2D); font-size: 0.85rem;">
                            <i class="fa-solid fa-shield-halved me-1.5"></i> Save New Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Part 2: Trusted Devices -->
            <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg, #ffffff);">
                <div class="card-header bg-transparent py-3.5 px-4 border-bottom d-flex align-items-center gap-2.5">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" 
                         style="width: 38px; height: 38px; background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                        <i class="fa-solid fa-laptop-code fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">Trusted 2FA Devices</h5>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem;">Browsers that bypass OTP verification</p>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if(session('success') && str_contains(session('success'), 'device'))
                        <div class="alert alert-success border-0 small mb-3" style="background-color: #dcfce7; color: #14532d; border-radius: 12px;">
                            <i class="fa-solid fa-circle-check me-1.5"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if($devices->isEmpty())
                        <div class="text-center py-3 text-muted">
                            <i class="fa-solid fa-shield-halved fs-3 mb-2" style="color: #cbd5e1;"></i>
                            <p class="mb-0 small fw-semibold">No remembered devices yet.</p>
                            <p class="text-muted small mb-0" style="font-size: 0.74rem;">Check <strong>"Remember this device"</strong> during login to bypass MFA.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush" style="max-height: 220px; overflow-y: auto;">
                            @foreach($devices as $device)
                                <div class="list-group-item px-0 py-2.5 border-0 border-bottom d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div style="font-size: 18px; color: #475569;">
                                            @if(str_contains(strtolower($device->user_agent), 'mobile') || str_contains(strtolower($device->user_agent), 'iphone') || str_contains(strtolower($device->user_agent), 'android'))
                                                <i class="fa-solid fa-mobile-screen-button"></i>
                                            @else
                                                <i class="fa-solid fa-desktop"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-0 text-dark" style="font-size: 0.85rem;">{{ $device->human_readable }}</h6>
                                            <p class="text-muted small mb-0" style="font-size: 0.72rem;">
                                                <span class="badge bg-light text-secondary border me-1">{{ $device->ip_address ?? 'Unknown IP' }}</span>
                                                {{ $device->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <form action="{{ route('profile.security.devices.revoke', $device->id) }}" method="POST" 
                                          data-confirm="Are you sure you want to revoke trust for this device ({{ $device->human_readable }})? You will need to complete MFA on your next login."
                                          data-confirm-title="Revoke Trusted Device"
                                          data-confirm-destructive="true"
                                          data-confirm-btn="Yes, Revoke Trust">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-1 border-0" 
                                                title="Revoke Device" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-trash-can" style="font-size: 0.75rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Part 3: Tools & Resources -->
            <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg, #ffffff);">
                <div class="card-header bg-transparent py-3 px-4 border-bottom d-flex align-items-center gap-2.5">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" 
                         style="width: 38px; height: 38px; background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                        <i class="fa-solid fa-toolbox fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">Help & Resources</h5>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem;">Guides, policies, and system evaluation</p>
                    </div>
                </div>
                <div class="card-body p-3.5">
                    <div class="row g-2">
                        <div class="col-4">
                            <button type="button" class="btn w-100 d-flex flex-column align-items-center gap-1.5 py-2.5 px-1 border rounded-3 fw-semibold shadow-2xs"
                                    onclick="openSystemTourModal('{{ auth()->user()->role }}')"
                                    style="font-size: 0.76rem; background: #f0fdf4; color: #15803d; border-color: #bbf7d0 !important;">
                                <i class="fa-solid fa-graduation-cap fs-5" style="color: #16a34a;"></i>
                                <span>Demo Tour</span>
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn w-100 d-flex flex-column align-items-center gap-1.5 py-2.5 px-1 border rounded-3 fw-semibold shadow-2xs"
                                    data-bs-toggle="modal" data-bs-target="#dataManagementGuideModal"
                                    style="font-size: 0.76rem; background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe !important;">
                                <i class="fa-solid fa-shield-halved fs-5" style="color: #2563eb;"></i>
                                <span>Privacy Act</span>
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn w-100 d-flex flex-column align-items-center gap-1.5 py-2.5 px-1 border rounded-3 fw-semibold shadow-2xs"
                                    data-bs-toggle="modal" data-bs-target="#uatFeedbackModal"
                                    style="font-size: 0.76rem; background: #fffbeb; color: #92400e; border-color: #fde68a !important;">
                                <i class="fa-solid fa-star fs-5" style="color: #d97706;"></i>
                                <span>Evaluation</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Part 4: Account Session / Sign Out -->
            <div class="card border-0 shadow-sm rounded-4" style="background: #fff5f5; border: 1.5px solid #fecaca !important;">
                <div class="card-body p-3.5 d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div>
                        <h6 class="fw-bold text-danger mb-0.5" style="font-size: 0.9rem;">
                            <i class="fa-solid fa-right-from-bracket me-1.5"></i> Active Session
                        </h6>
                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">Sign out of this browser</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" id="settingsLogoutForm" class="m-0">
                        @csrf
                        <button type="button" onclick="confirmSettingsLogout()" class="btn btn-danger btn-sm fw-bold px-3 py-1.5 rounded-pill shadow-xs" 
                                style="background: #dc2626; border: none; font-size: 0.8rem;">
                            <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function confirmSettingsLogout() {
        AegisAlert.confirm({
            title: 'Confirm Logout',
            text: 'Are you sure you want to sign out of your account?',
            icon: 'question',
            confirmText: 'Yes, Sign Out',
            cancelText: 'Cancel'
        }).then(function (confirmed) {
            if (confirmed) {
                document.getElementById('settingsLogoutForm').submit();
            }
        });
    }
</script>
@endsection
