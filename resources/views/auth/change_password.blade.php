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

                @if($errors->any() && ($errors->has('name') || $errors->has('clsu_id_number') || $errors->has('contact_number') || $errors->has('college') || $errors->has('course') || $errors->has('year_level') || $errors->has('guardian_name') || $errors->has('emergency_contact_number') || $errors->has('province') || $errors->has('city_municipality') || $errors->has('barangay') || $errors->has('street_address')))
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
                                           title="Format: XX-XXXX (e.g. 23-1234)"
                                           value="{{ old('clsu_id_number', $user->profile->clsu_id_number ?? '') }}" required style="border-radius: 0 10px 10px 0;">
                                </div>
                                <div class="form-text text-muted mt-1" style="font-size: 0.72rem;">
                                    <i class="fa-solid fa-circle-info me-1 text-primary"></i>Format: <strong class="text-dark">XX-XXXX</strong> (e.g. 23-2548). Must be unique to your student record.
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
                                            'College of Fisheries',
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

                            <!-- Degree Course / Program -->
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="course" class="form-label fw-semibold text-dark small mb-0">
                                        Degree Course / Program <span class="text-danger">*</span>
                                    </label>
                                    <button type="button" id="manualCourseToggle" class="btn btn-link p-0 text-decoration-none small text-muted" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Type manually
                                    </button>
                                </div>
                                <div class="input-group" id="courseSelectGroup">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-graduation-cap"></i></span>
                                    <select name="course" id="course" class="form-select border-start-0 py-2" required style="border-radius: 0 10px 10px 0;">
                                        <option value="" disabled selected>Select College first...</option>
                                    </select>
                                </div>
                                <div class="input-group d-none" id="courseInputGroup">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-graduation-cap"></i></span>
                                    <input type="text" id="manual_course_input" class="form-control border-start-0 py-2" placeholder="e.g. BS Information Technology" style="border-radius: 0 10px 10px 0;">
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

                            <!-- Permanent Residential Address Section -->
                            <div class="col-12 mt-4 pt-3 border-top">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 28px; height: 28px;">
                                            <i class="fa-solid fa-location-dot" style="font-size: 0.85rem;"></i>
                                        </span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">Permanent Home Address</h6>
                                    </div>
                                    <span class="badge bg-light text-secondary border small">Student Residence</span>
                                </div>
                                <p class="text-muted small mb-3" style="font-size: 0.78rem;">
                                    Official residential address used for statutory scholarship eligibility verification and official certificate generation.
                                </p>
                            </div>

                            <!-- Province Dropdown -->
                            <div class="col-md-6">
                                <label for="address_province" class="form-label fw-semibold text-dark small mb-1">
                                    Province <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-map"></i></span>
                                    <select name="province" id="address_province" class="form-select border-start-0 py-2" required style="border-radius: 0 10px 10px 0;">
                                        <option value="" disabled {{ !old('province', $user->profile->province ?? '') ? 'selected' : '' }}>Select Province...</option>
                                        <option value="Nueva Ecija" {{ old('province', $user->profile->province ?? '') === 'Nueva Ecija' || (!old('province') && !($user->profile->province ?? null)) ? 'selected' : '' }}>Nueva Ecija</option>
                                        <option value="Pangasinan" {{ old('province', $user->profile->province ?? '') === 'Pangasinan' ? 'selected' : '' }}>Pangasinan</option>
                                        <option value="Pampanga" {{ old('province', $user->profile->province ?? '') === 'Pampanga' ? 'selected' : '' }}>Pampanga</option>
                                        <option value="Tarlac" {{ old('province', $user->profile->province ?? '') === 'Tarlac' ? 'selected' : '' }}>Tarlac</option>
                                        <option value="Bulacan" {{ old('province', $user->profile->province ?? '') === 'Bulacan' ? 'selected' : '' }}>Bulacan</option>
                                        <option value="Bataan" {{ old('province', $user->profile->province ?? '') === 'Bataan' ? 'selected' : '' }}>Bataan</option>
                                        <option value="Zambales" {{ old('province', $user->profile->province ?? '') === 'Zambales' ? 'selected' : '' }}>Zambales</option>
                                        <option value="Aurora" {{ old('province', $user->profile->province ?? '') === 'Aurora' ? 'selected' : '' }}>Aurora</option>
                                        <option value="Metro Manila" {{ old('province', $user->profile->province ?? '') === 'Metro Manila' ? 'selected' : '' }}>Metro Manila</option>
                                        <option value="Benguet" {{ old('province', $user->profile->province ?? '') === 'Benguet' ? 'selected' : '' }}>Benguet</option>
                                        <option value="La Union" {{ old('province', $user->profile->province ?? '') === 'La Union' ? 'selected' : '' }}>La Union</option>
                                        <option value="Ilocos Norte" {{ old('province', $user->profile->province ?? '') === 'Ilocos Norte' ? 'selected' : '' }}>Ilocos Norte</option>
                                        <option value="Ilocos Sur" {{ old('province', $user->profile->province ?? '') === 'Ilocos Sur' ? 'selected' : '' }}>Ilocos Sur</option>
                                        <option value="Cagayan" {{ old('province', $user->profile->province ?? '') === 'Cagayan' ? 'selected' : '' }}>Cagayan</option>
                                        <option value="Isabela" {{ old('province', $user->profile->province ?? '') === 'Isabela' ? 'selected' : '' }}>Isabela</option>
                                        <option value="Nueva Vizcaya" {{ old('province', $user->profile->province ?? '') === 'Nueva Vizcaya' ? 'selected' : '' }}>Nueva Vizcaya</option>
                                        <option value="Quirino" {{ old('province', $user->profile->province ?? '') === 'Quirino' ? 'selected' : '' }}>Quirino</option>
                                        <option value="Batangas" {{ old('province', $user->profile->province ?? '') === 'Batangas' ? 'selected' : '' }}>Batangas</option>
                                        <option value="Cavite" {{ old('province', $user->profile->province ?? '') === 'Cavite' ? 'selected' : '' }}>Cavite</option>
                                        <option value="Laguna" {{ old('province', $user->profile->province ?? '') === 'Laguna' ? 'selected' : '' }}>Laguna</option>
                                        <option value="Quezon" {{ old('province', $user->profile->province ?? '') === 'Quezon' ? 'selected' : '' }}>Quezon</option>
                                        <option value="Rizal" {{ old('province', $user->profile->province ?? '') === 'Rizal' ? 'selected' : '' }}>Rizal</option>
                                        <option value="Other" {{ !in_array(old('province', $user->profile->province ?? ''), ['', 'Nueva Ecija', 'Pangasinan', 'Pampanga', 'Tarlac', 'Bulacan', 'Bataan', 'Zambales', 'Aurora', 'Metro Manila', 'Benguet', 'La Union', 'Ilocos Norte', 'Ilocos Sur', 'Cagayan', 'Isabela', 'Nueva Vizcaya', 'Quirino', 'Batangas', 'Cavite', 'Laguna', 'Quezon', 'Rizal']) && old('province', $user->profile->province ?? '') ? 'selected' : '' }}>Other Province...</option>
                                    </select>
                                </div>
                                <div id="customProvinceWrapper" class="mt-2 {{ !in_array(old('province', $user->profile->province ?? ''), ['', 'Nueva Ecija', 'Pangasinan', 'Pampanga', 'Tarlac', 'Bulacan', 'Bataan', 'Zambales', 'Aurora', 'Metro Manila', 'Benguet', 'La Union', 'Ilocos Norte', 'Ilocos Sur', 'Cagayan', 'Isabela', 'Nueva Vizcaya', 'Quirino', 'Batangas', 'Cavite', 'Laguna', 'Quezon', 'Rizal']) && old('province', $user->profile->province ?? '') ? '' : 'd-none' }}">
                                    <input type="text" id="custom_province_input" class="form-control py-2" placeholder="Enter your province name..." value="{{ old('province', $user->profile->province ?? '') }}" style="border-radius: 10px;">
                                </div>
                            </div>

                            <!-- City / Municipality Dropdown -->
                            <div class="col-md-6">
                                <label for="address_city" class="form-label fw-semibold text-dark small mb-1">
                                    City / Municipality <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-city"></i></span>
                                    <select name="city_municipality" id="address_city" class="form-select border-start-0 py-2" required style="border-radius: 0 10px 10px 0;">
                                        <option value="" disabled selected>Select City / Municipality...</option>
                                    </select>
                                </div>
                                <div id="customCityWrapper" class="mt-2 d-none">
                                    <input type="text" id="custom_city_input" class="form-control py-2" placeholder="Enter city / municipality name..." style="border-radius: 10px;">
                                </div>
                            </div>

                            <!-- Barangay -->
                            <div class="col-md-6">
                                <label for="address_barangay" class="form-label fw-semibold text-dark small mb-1">
                                    Barangay <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-tree-city"></i></span>
                                    <input type="text" name="barangay" id="address_barangay" list="munoz_barangay_list"
                                           class="form-control border-start-0 py-2" 
                                           autocomplete="address-level3"
                                           placeholder="e.g. Bantug or Poblacion"
                                           value="{{ old('barangay', $user->profile->barangay ?? '') }}" required style="border-radius: 0 10px 10px 0;">
                                </div>
                                <datalist id="munoz_barangay_list">
                                    <option value="Bantug">
                                    <option value="Bagong Sikat">
                                    <option value="Bical">
                                    <option value="Catalanacan">
                                    <option value="Curva">
                                    <option value="Franza">
                                    <option value="Labney">
                                    <option value="Licaong">
                                    <option value="Linglingay">
                                    <option value="Magtanggol">
                                    <option value="Maligaya">
                                    <option value="Mangandingay">
                                    <option value="Maragol">
                                    <option value="Poblacion East">
                                    <option value="Poblacion North">
                                    <option value="Poblacion South">
                                    <option value="Poblacion West">
                                    <option value="Rang-ayan">
                                    <option value="Rizal">
                                    <option value="San Antonio">
                                    <option value="San Felipe">
                                    <option value="San Juan">
                                    <option value="Santa Sofia">
                                    <option value="Sapang Cauayan">
                                    <option value="Villa Cuizon">
                                    <option value="Villa Isla">
                                    <option value="Villa Nati">
                                    <option value="Villa Santos">
                                    <option value="Villa Soriano">
                                </datalist>
                            </div>

                            <!-- Street Address / House No. / Purok -->
                            <div class="col-md-6">
                                <label for="address_street" class="form-label fw-semibold text-dark small mb-1">
                                    Street / Purok / House No. <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-road"></i></span>
                                    <input type="text" name="street_address" id="address_street" 
                                           class="form-control border-start-0 py-2" 
                                           autocomplete="street-address"
                                           placeholder="e.g. Purok 2, Maharlika Highway"
                                           value="{{ old('street_address', $user->profile->street_address ?? '') }}" required style="border-radius: 0 10px 10px 0;">
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

    document.addEventListener('DOMContentLoaded', function () {
        // ── 0. CLSU ID NUMBER FORMATTER (XX-XXXX) ──
        const idInput = document.getElementById('clsu_id_number');
        if (idInput) {
            function formatClsuId(val) {
                const digits = val.replace(/\D/g, '').slice(0, 6);
                if (digits.length <= 2) {
                    return digits;
                }
                return digits.slice(0, 2) + '-' + digits.slice(2);
            }

            idInput.addEventListener('input', function () {
                const formatted = formatClsuId(this.value);
                if (this.value !== formatted) {
                    this.value = formatted;
                }
            });

            idInput.addEventListener('blur', function () {
                this.value = formatClsuId(this.value);
            });
        }

        // ── 1. COLLEGE & DEGREE PROGRAM CASCADING SELECTOR ──
        const collegePrograms = {
            'College of Agriculture': [
                'BS in Agriculture (BSA)',
                'BS in Agribusiness (BSAB)'
            ],
            'College of Arts and Social Sciences': [
                'BA in Development Communication (BADC)',
                'BA in Literature (BALit)',
                'BA in Social Sciences (BASS)',
                'BS in Psychology (BSPsych)'
            ],
            'College of Business Administration and Accountancy': [
                'BS in Accountancy (BSA)',
                'BS in Business Administration - Marketing Management (BSBA-MM)',
                'BS in Business Administration - Financial Management (BSBA-FM)',
                'BS in Management Accounting (BSMA)',
                'BS in Entrepreneurship (BSEntrep)'
            ],
            'College of Education': [
                'Bachelor of Elementary Education (BEEd)',
                'Bachelor of Secondary Education (BSEd)',
                'Bachelor of Culture and Arts Education (BCAEd)',
                'Bachelor of Early Childhood Education (BECEd)',
                'Bachelor of Physical Education (BPEd)',
                'BS in Hospitality Management (BSHM)',
                'BS in Tourism Management (BSTM)'
            ],
            'College of Engineering': [
                'BS in Agricultural and Biosystems Engineering (BSABE)',
                'BS in Civil Engineering (BSCE)',
                'BS in Information Technology (BSIT)',
                'BS in Meteorology (BSMet)'
            ],
            'College of Fisheries': [
                'BS in Fisheries (BSFi)'
            ],
            'College of Home Science and Industry': [
                'BS in Food Technology (BSFT)',
                'BS in Textile and Fashion Technology (BSTFT)'
            ],
            'College of Science': [
                'BS in Biology (BSBio)',
                'BS in Chemistry (BSChem)',
                'BS in Environmental Science (BSES)',
                'BS in Mathematics (BSMath)',
                'BS in Statistics (BSStat)'
            ],
            'College of Veterinary Science and Medicine': [
                'Doctor of Veterinary Medicine (DVM)'
            ]
        };

        const collegeSelect = document.getElementById('college');
        const courseSelect = document.getElementById('course');
        const courseSelectGroup = document.getElementById('courseSelectGroup');
        const courseInputGroup = document.getElementById('courseInputGroup');
        const manualCourseInput = document.getElementById('manual_course_input');
        const manualCourseToggle = document.getElementById('manualCourseToggle');

        const initialSavedCourse = {!! json_encode(old('course', $user->profile->course ?? '')) !!};
        let isManualCourseMode = false;

        function updateCourseOptions(selectedCollege, selectedValue) {
            if (!courseSelect) return;
            courseSelect.innerHTML = '<option value="" disabled selected>Select Degree Program...</option>';
            
            const programs = collegePrograms[selectedCollege] || [];
            let valueMatched = false;

            programs.forEach(function (prog) {
                const opt = document.createElement('option');
                opt.value = prog;
                opt.textContent = prog;
                if (selectedValue && (selectedValue === prog || selectedValue.toLowerCase() === prog.toLowerCase())) {
                    opt.selected = true;
                    valueMatched = true;
                }
                courseSelect.appendChild(opt);
            });

            // Add other option
            const otherOpt = document.createElement('option');
            otherOpt.value = '__OTHER__';
            otherOpt.textContent = 'Other Degree Program...';
            courseSelect.appendChild(otherOpt);

            // If user has a previously saved course that wasn't an exact match, append it so it's not lost
            if (selectedValue && !valueMatched && selectedValue !== '__OTHER__') {
                const customOpt = document.createElement('option');
                customOpt.value = selectedValue;
                customOpt.textContent = selectedValue;
                customOpt.selected = true;
                courseSelect.insertBefore(customOpt, otherOpt);
            }
        }

        if (collegeSelect && courseSelect) {
            collegeSelect.addEventListener('change', function () {
                if (!isManualCourseMode) {
                    updateCourseOptions(this.value, '');
                }
            });

            courseSelect.addEventListener('change', function () {
                if (this.value === '__OTHER__') {
                    toggleManualCourse(true);
                }
            });

            if (manualCourseToggle) {
                manualCourseToggle.addEventListener('click', function () {
                    toggleManualCourse(!isManualCourseMode);
                });
            }

            function toggleManualCourse(enableManual) {
                isManualCourseMode = enableManual;
                if (enableManual) {
                    courseSelectGroup.classList.add('d-none');
                    courseInputGroup.classList.remove('d-none');
                    manualCourseInput.value = (courseSelect.value && courseSelect.value !== '__OTHER__') ? courseSelect.value : '';
                    manualCourseInput.name = 'course';
                    courseSelect.removeAttribute('name');
                    manualCourseToggle.innerHTML = '<i class="fa-solid fa-list me-1"></i>Select from list';
                    manualCourseInput.focus();
                } else {
                    courseInputGroup.classList.add('d-none');
                    courseSelectGroup.classList.remove('d-none');
                    courseSelect.name = 'course';
                    manualCourseInput.removeAttribute('name');
                    manualCourseToggle.innerHTML = '<i class="fa-solid fa-pen-to-square me-1"></i>Type manually';
                    if (collegeSelect.value) {
                        updateCourseOptions(collegeSelect.value, manualCourseInput.value);
                    }
                }
            }

            // Initialize on page load
            if (collegeSelect.value) {
                updateCourseOptions(collegeSelect.value, initialSavedCourse);
            }
        }

        // ── 2. PROVINCE & CITY/MUNICIPALITY CASCADING SELECTOR ──
        const provinceCities = {
            'Nueva Ecija': [
                'Science City of Muñoz',
                'San Jose City',
                'Cabanatuan City',
                'Gapan City',
                'Palayan City',
                'Aliaga',
                'Bongabon',
                'Cabiao',
                'Carranglan',
                'Cuyapo',
                'Gabaldon',
                'General Mamerto Natividad',
                'General Tinio',
                'Guimba',
                'Jaen',
                'Laur',
                'Licab',
                'Llanera',
                'Lupao',
                'Nampicuan',
                'Pantabangan',
                'Peñaranda',
                'Quezon',
                'Rizal',
                'San Antonio',
                'San Isidro',
                'San Leonardo',
                'Santa Rosa',
                'Santo Domingo',
                'Talavera',
                'Talugtug',
                'Zaragoza'
            ],
            'Pangasinan': [
                'San Carlos City', 'Dagupan City', 'Urdaneta City', 'Alaminos City',
                'Rosales', 'Lingayen', 'Bayambang', 'Malasiqui', 'Calasiao', 'Mangaldan'
            ],
            'Pampanga': [
                'San Fernando City', 'Angeles City', 'Mabalacat City', 'Guagua', 'Lubao', 'Mexico', 'Arayat', 'Candaba'
            ],
            'Tarlac': [
                'Tarlac City', 'Concepcion', 'Capas', 'Paniqui', 'Camiling', 'Gerona', 'Moncada', 'Victoria'
            ],
            'Bulacan': [
                'Malolos City', 'San Jose del Monte City', 'Meycauayan City', 'Baliuag', 'Marilao', 'Santa Maria', 'Bocaue', 'San Miguel'
            ],
            'Bataan': [
                'Balanga City', 'Dinalupihan', 'Hermosa', 'Mariveles', 'Orani'
            ],
            'Zambales': [
                'Olongapo City', 'Subic', 'Iba', 'Castillejos', 'San Marcelino'
            ],
            'Aurora': [
                'Baler', 'Casiguran', 'Dilasag', 'Dinalungan', 'Dingalan', 'Dipaculao', 'Maria Aurora', 'San Luis'
            ],
            'Metro Manila': [
                'Manila', 'Quezon City', 'Caloocan', 'Taguig', 'Pasig', 'Makati', 'Parañaque', 'Valenzuela', 'Las Piñas', 'Muntinlupa', 'Mandaluyong', 'Marikina', 'Pasay', 'Malabon', 'Navotas', 'San Juan', 'Pateros'
            ]
        };

        const provinceSelect = document.getElementById('address_province');
        const customProvinceWrapper = document.getElementById('customProvinceWrapper');
        const customProvinceInput = document.getElementById('custom_province_input');
        const citySelect = document.getElementById('address_city');
        const customCityWrapper = document.getElementById('customCityWrapper');
        const customCityInput = document.getElementById('custom_city_input');

        const initialSavedCity = {!! json_encode(old('city_municipality', $user->profile->city_municipality ?? '')) !!};

        function updateCityOptions(province, selectedCity) {
            if (!citySelect) return;
            citySelect.innerHTML = '<option value="" disabled selected>Select City / Municipality...</option>';

            const cities = provinceCities[province] || [];
            let cityMatched = false;

            cities.forEach(function (c) {
                const opt = document.createElement('option');
                opt.value = c;
                opt.textContent = c;
                if (selectedCity && (selectedCity === c || selectedCity.toLowerCase() === c.toLowerCase())) {
                    opt.selected = true;
                    cityMatched = true;
                }
                citySelect.appendChild(opt);
            });

            // Other City option
            const otherOpt = document.createElement('option');
            otherOpt.value = '__OTHER_CITY__';
            otherOpt.textContent = 'Other City / Municipality...';
            citySelect.appendChild(otherOpt);

            // Retain custom or unmatched saved city
            if (selectedCity && !cityMatched && selectedCity !== '__OTHER_CITY__') {
                const customCityOpt = document.createElement('option');
                customCityOpt.value = selectedCity;
                customCityOpt.textContent = selectedCity;
                customCityOpt.selected = true;
                citySelect.insertBefore(customCityOpt, otherOpt);
            }
        }

        if (provinceSelect && citySelect) {
            provinceSelect.addEventListener('change', function () {
                if (this.value === 'Other') {
                    customProvinceWrapper.classList.remove('d-none');
                    customProvinceInput.setAttribute('required', 'required');
                    customProvinceInput.name = 'province';
                    provinceSelect.removeAttribute('name');
                    customProvinceInput.focus();
                } else {
                    customProvinceWrapper.classList.add('d-none');
                    customProvinceInput.removeAttribute('required');
                    provinceSelect.name = 'province';
                    customProvinceInput.removeAttribute('name');
                }
                updateCityOptions(this.value, '');
            });

            citySelect.addEventListener('change', function () {
                if (this.value === '__OTHER_CITY__') {
                    customCityWrapper.classList.remove('d-none');
                    customCityInput.setAttribute('required', 'required');
                    customCityInput.name = 'city_municipality';
                    citySelect.removeAttribute('name');
                    customCityInput.focus();
                } else {
                    customCityWrapper.classList.add('d-none');
                    customCityInput.removeAttribute('required');
                    citySelect.name = 'city_municipality';
                    customCityInput.removeAttribute('name');
                }
            });

            // Initialize on load
            const activeProv = provinceSelect.value || 'Nueva Ecija';
            if (activeProv) {
                updateCityOptions(activeProv, initialSavedCity);
            }
        }
    });
</script>
@endsection
