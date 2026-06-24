@extends('layouts.app')

@section('title', 'My Profile | A.E.G.I.S.')

@push('styles')
<style>
    /* Profile form specific styles */
    .profile-card {
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: var(--shadow-sm);
        background: #ffffff;
    }
    
    .profile-header {
        background: linear-gradient(135deg, var(--clsu-green-dark) 0%, var(--clsu-green) 100%);
        color: white;
        padding: 2rem;
        position: relative;
    }

    .profile-avatar-container {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--clsu-gold-light), var(--clsu-gold));
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(242, 169, 0, 0.3);
        font-size: 2.2rem;
        color: var(--clsu-dark);
        font-weight: 700;
        border: 3px solid rgba(255, 255, 255, 0.95);
    }

    .security-shield-card {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 1.25rem;
    }

    .btn-save-profile {
        background: linear-gradient(135deg, var(--clsu-green), #16703f);
        color: white;
        border: none;
        padding: 12px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s;
        box-shadow: 0 4px 16px rgba(15, 89, 52, 0.2);
    }

    .btn-save-profile:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(15, 89, 52, 0.3);
        color: white;
    }

    .info-badge {
        font-size: 0.72rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.15);
        color: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 20px;
        padding: 4px 12px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 950px; padding: 1.5rem 1rem 3rem;">

    <div class="row g-4">
        {{-- Left Column: Form --}}
        <div class="col-lg-8">
            <div class="profile-card">
                <div class="profile-header d-flex flex-column flex-md-row align-items-center gap-4">
                    <div class="profile-avatar-container">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="text-center text-md-start">
                        <h4 class="fw-bold text-white mb-1">{{ $user->name }}</h4>
                        <p class="text-white-50 mb-2" style="font-size: 0.9rem;">{{ $user->email }}</p>
                        <div class="info-badge">
                            <i class="fa-solid fa-graduation-cap"></i> Student Applicant
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <form action="{{ route('student.profile.update') }}" method="POST" id="profileForm">
                        @csrf

                        <h6 class="fw-bold text-dark mb-4 pb-2 border-bottom"><i class="fa-solid fa-user-gear text-success me-2"></i> Personal & University Information</h6>

                        <div class="row g-3">
                            {{-- Full Name --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small" for="nameInput">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" name="name" id="nameInput" 
                                           class="form-control border-start-0" 
                                           value="{{ old('name', $user->name) }}" required autocomplete="name">
                                </div>
                                <div class="text-muted mt-1" style="font-size: 0.72rem;">Enter your official name registered in CLSU.</div>
                            </div>

                            {{-- Institutional Email (Read-Only) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small" for="emailInput">Institutional Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" id="emailInput" 
                                           class="form-control border-start-0 bg-light" 
                                           value="{{ $user->email }}" readonly disabled>
                                </div>
                                <div class="text-muted mt-1" style="font-size: 0.72rem;">Institutional email addresses cannot be modified.</div>
                            </div>

                            {{-- CLSU ID Number --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small" for="idNumberInput">CLSU ID Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-id-card"></i></span>
                                    <input type="text" name="clsu_id_number" id="idNumberInput" 
                                           class="form-control border-start-0" 
                                           placeholder="e.g. 2023-4567" 
                                           value="{{ old('clsu_id_number', $user->profile->clsu_id_number ?? '') }}" required autocomplete="off">
                                </div>
                                <div class="text-muted mt-1" style="font-size: 0.72rem;">Format: YYYY-XXXX (e.g. 2023-4567).</div>
                            </div>

                            {{-- Contact Number --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small" for="contactInput">Contact Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" name="contact_number" id="contactInput" 
                                           class="form-control border-start-0" 
                                           placeholder="e.g. 09123456789" 
                                           value="{{ old('contact_number', $user->profile->contact_number ?? '') }}" required autocomplete="tel">
                                </div>
                                <div class="text-muted mt-1" style="font-size: 0.72rem;">Philippine mobile number (e.g. 09123456789).</div>
                            </div>

                            {{-- College --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small" for="collegeSelect">College</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-building-columns"></i></span>
                                    <select name="college" id="collegeSelect" class="form-select border-start-0" required>
                                        <option value="" disabled {{ !old('college', $user->profile->college ?? '') ? 'selected' : '' }}>Select your College...</option>
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
                                            <option value="{{ $college }}" {{ old('college', $user->profile->college ?? '') === $college ? 'selected' : '' }}>
                                                {{ $college }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Year Level --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small" for="yearLevelSelect">Year Level</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-layer-group"></i></span>
                                    <select name="year_level" id="yearLevelSelect" class="form-select border-start-0" required>
                                        <option value="" disabled {{ !old('year_level', $user->profile->year_level ?? '') ? 'selected' : '' }}>Select Year Level...</option>
                                        @foreach(['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year', '6th Year'] as $yl)
                                            <option value="{{ $yl }}" {{ old('year_level', $user->profile->year_level ?? '') === $yl ? 'selected' : '' }}>
                                                {{ $yl }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Degree Course --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark small" for="courseInput">Degree Course / Program</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-graduation-cap"></i></span>
                                    <input type="text" name="course" id="courseInput" 
                                           class="form-control border-start-0" 
                                           placeholder="e.g. BS Information Technology" 
                                           value="{{ old('course', $user->profile->course ?? '') }}" required autocomplete="off">
                                </div>
                                <div class="text-muted mt-1" style="font-size: 0.72rem;">Enter the full name of your degree program (e.g. BS Information Technology).</div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn-save-profile px-4" id="saveProfileBtn" onclick="showSavingLoading()">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column: Information & Privacy Guard --}}
        <div class="col-lg-4">
            {{-- Privacy Shield Notice --}}
            <div class="security-shield-card mb-4 shadow-sm">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div style="width:30px;height:30px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-user-shield text-success" style="font-size: 0.9rem;"></i>
                    </div>
                    <span class="fw-bold text-success" style="font-size:0.9rem;">AES-256 Privacy Guard</span>
                </div>
                <p class="text-success mb-0" style="font-size: 0.78rem; line-height: 1.45;">
                    Your **CLSU ID Number** and **Contact Number** are automatically encrypted inside our database tables. 
                    Even administrators cannot view your raw identifiers directly in database backups. 
                    Your privacy is protected in full compliance with academic safety standards.
                </p>
            </div>

            {{-- Info Notice about Applications Form --}}
            <div class="card p-4 shadow-sm" style="border-radius:16px;">
                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-circle-info text-primary me-2"></i> Document Integration</h6>
                <p class="text-muted mb-0" style="font-size: 0.8rem; line-height: 1.5;">
                    Updates made to your profile here will immediately propagate and reflect on:
                </p>
                <ul class="text-muted mt-2 mb-0 ps-3" style="font-size: 0.8rem; line-height: 1.5;">
                    <li>Your digital scholarship application form metadata.</li>
                    <li>The system-generated verification audit reports.</li>
                    <li>The official PDF application forms sent to your institutional email inbox upon evaluation approval.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showSavingLoading() {
        const btn = document.getElementById('saveProfileBtn');
        const form = document.getElementById('profileForm');
        if (form.checkValidity()) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving Profile...';
            setTimeout(() => {
                btn.classList.add('disabled');
                btn.disabled = true;
            }, 10);
        }
    }

    @if ($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Validation Failed',
        html: `{!! implode('<br>', $errors->all()) !!}`,
        confirmButtonColor: '#0F5934',
        customClass: { popup: 'rounded-4' }
    });
    @endif
</script>
@endpush
