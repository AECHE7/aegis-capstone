@extends('layouts.app')

@section('title', 'User Management | A.E.G.I.S. Director Portal')
@section('page-title', 'User Management')
@section('page-subtitle', 'Complete administrative oversight of student applicants, active scholars, and OSA evaluation staff')

@section('content')

{{-- Success / Warning Alerts --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px; background: #e8f5e9; color: #1b5e20;">
    <div class="d-flex align-items-center">
        <i class="fa-solid fa-circle-check fa-lg me-2"></i>
        <div><strong>Success:</strong> {{ session('success') }}</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px; background: #fff8e1; color: #8d6e63;">
    <div class="d-flex align-items-center">
        <i class="fa-solid fa-triangle-exclamation fa-lg me-2"></i>
        <div><strong>Notice:</strong> {{ session('warning') }}</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px; background: #fee2e2; color: #991b1b;">
    <div class="d-flex align-items-center">
        <i class="fa-solid fa-circle-exclamation fa-lg me-2"></i>
        <div><strong>Error:</strong> {{ session('error') }}</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Director KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 18px; background: white; border-left: 5px solid var(--clsu-green) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Student Accounts</span>
                    <h3 class="fw-bold mb-0 mt-1" style="color: var(--text-main);">{{ number_format($metrics['total_students']) }}</h3>
                    <small class="text-muted" style="font-size: 0.75rem;">Registered portal applicants</small>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(15, 89, 52, 0.1); color: var(--clsu-green); display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-user-graduate fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 18px; background: white; border-left: 5px solid #0284c7 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Staff Personnel</span>
                    <h3 class="fw-bold mb-0 mt-1" style="color: var(--text-main);">{{ number_format($metrics['total_staff']) }}</h3>
                    <small class="text-muted" style="font-size: 0.75rem;">OSA evaluators & officers</small>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(2, 132, 199, 0.1); color: #0284c7; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-users-gear fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 18px; background: white; border-left: 5px solid var(--clsu-gold) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Active Scholars</span>
                    <h3 class="fw-bold mb-0 mt-1" style="color: var(--text-main);">{{ number_format($metrics['active_scholars']) }}</h3>
                    <small class="text-muted" style="font-size: 0.75rem;">Holding verified grants</small>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(217, 119, 6, 0.1); color: var(--clsu-gold); display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-award fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 18px; background: white; border-left: 5px solid #dc2626 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Inactive Accounts</span>
                    <h3 class="fw-bold mb-0 mt-1" style="color: var(--text-main);">{{ number_format($metrics['inactive_users']) }}</h3>
                    <small class="text-muted" style="font-size: 0.75rem;">Deactivated / access revoked</small>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(220, 38, 38, 0.1); color: #dc2626; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-user-slash fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Container Card --}}
<div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; background: white;">
    
    {{-- Card Header with Tabs and Action Button --}}
    <div class="card-header bg-white border-bottom p-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            
            {{-- Navigation Tabs --}}
            <ul class="nav nav-pills" id="userManagementTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{ route('superadmin.users', ['tab' => 'students', 'q' => $search, 'status' => $status, 'college' => $college]) }}"
                       class="nav-link px-4 py-2.5 fw-bold {{ $tab === 'students' ? 'active' : 'text-muted' }}" 
                       style="{{ $tab === 'students' ? 'background: var(--clsu-green); border-radius: 12px;' : 'border-radius: 12px;' }}">
                        <i class="fa-solid fa-user-graduate me-2"></i> Students Directory 
                        <span class="badge bg-white text-dark ms-1 rounded-pill" style="font-size: 0.72rem;">{{ $metrics['total_students'] }}</span>
                    </a>
                </li>
                <li class="nav-item ms-md-2" role="presentation">
                    <a href="{{ route('superadmin.users', ['tab' => 'staff', 'q' => $search, 'status' => $status]) }}"
                       class="nav-link px-4 py-2.5 fw-bold {{ $tab === 'staff' ? 'active' : 'text-muted' }}"
                       style="{{ $tab === 'staff' ? 'background: var(--clsu-green); border-radius: 12px;' : 'border-radius: 12px;' }}">
                        <i class="fa-solid fa-users-gear me-2"></i> Staff Personnel 
                        <span class="badge bg-white text-dark ms-1 rounded-pill" style="font-size: 0.72rem;">{{ $metrics['total_staff'] }}</span>
                    </a>
                </li>
            </ul>

            {{-- Action Tools: Invite Staff / Quick Stats --}}
            <div class="d-flex align-items-center gap-2">
                @if($tab === 'staff')
                    <button class="btn fw-bold px-4 text-white" 
                            style="background: linear-gradient(135deg, var(--clsu-green), #16703f); border-radius: 12px; box-shadow: 0 4px 12px rgba(15,89,52,0.25);"
                            data-bs-toggle="modal" data-bs-target="#inviteStaffModal">
                        <i class="fa-solid fa-user-plus me-1"></i> Invite New Staff
                    </button>
                @endif
            </div>
        </div>

        {{-- Filters & Search Bar --}}
        <form method="GET" action="{{ route('superadmin.users') }}" class="row g-2 mt-3 pt-3 border-top align-items-center">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;">
                        <i class="fa-solid fa-magnifying-glass text-muted"></i>
                    </span>
                    <input type="text" name="q" class="form-control bg-light border-start-0" 
                           placeholder="{{ $tab === 'students' ? 'Search by name, email, student ID, course...' : 'Search staff by name or institutional email...' }}" 
                           value="{{ $search }}" style="border-radius: 0 10px 10px 0; font-size: 0.85rem;">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <select name="status" class="form-select bg-light" style="border-radius: 10px; font-size: 0.85rem;">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Account Statuses</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Accounts Only</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Deactivated Only</option>
                </select>
            </div>

            @if($tab === 'students')
            <div class="col-6 col-md-3">
                <select name="college" class="form-select bg-light" style="border-radius: 10px; font-size: 0.85rem;">
                    <option value="">All Colleges</option>
                    <option value="College of Agriculture" {{ $college === 'College of Agriculture' ? 'selected' : '' }}>College of Agriculture</option>
                    <option value="College of Arts and Social Sciences" {{ $college === 'College of Arts and Social Sciences' ? 'selected' : '' }}>College of Arts and Social Sciences</option>
                    <option value="College of Business Administration and Accountancy" {{ $college === 'College of Business Administration and Accountancy' ? 'selected' : '' }}>College of Business & Accountancy</option>
                    <option value="College of Education" {{ $college === 'College of Education' ? 'selected' : '' }}>College of Education</option>
                    <option value="College of Engineering" {{ $college === 'College of Engineering' ? 'selected' : '' }}>College of Engineering</option>
                    <option value="College of Fisheries" {{ $college === 'College of Fisheries' ? 'selected' : '' }}>College of Fisheries</option>
                    <option value="College of Home Science and Industry" {{ $college === 'College of Home Science and Industry' ? 'selected' : '' }}>College of Home Science & Industry</option>
                    <option value="College of Science" {{ $college === 'College of Science' ? 'selected' : '' }}>College of Science</option>
                    <option value="College of Veterinary Science and Medicine" {{ $college === 'College of Veterinary Science and Medicine' ? 'selected' : '' }}>College of Veterinary Medicine</option>
                </select>
            </div>
            @endif

            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark fw-bold w-100" style="border-radius: 10px; font-size: 0.85rem;">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if($search || $status !== 'all' || $college)
                    <a href="{{ route('superadmin.users', ['tab' => $tab]) }}" class="btn btn-light border" style="border-radius: 10px; font-size: 0.85rem;" title="Reset filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Content Body --}}
    <div class="card-body p-0">
        @if($tab === 'students')
            {{-- Students Table --}}
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Student Details</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">College & Program</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Active Application</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Security & DPA</th>
                            <th class="py-3 text-center text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Status</th>
                            <th class="pe-4 py-3 text-end text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        @php
                            $latestApp = $student->applications->first();
                            $isComplete = $student->isProfileComplete();
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            {{-- Student Info --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div style="width: 42px; height: 42px; border-radius: 50%; background: #e8f5e9; color: var(--clsu-green); font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0;" class="me-3">
                                        {{ strtoupper(substr($student->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $student->name }}</div>
                                        <div class="text-muted small" style="font-size: 0.78rem;">{{ $student->email }}</div>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.68rem;">
                                                ID: {{ $student->profile?->clsu_id_number ?? 'Not Enrolled' }}
                                            </span>
                                            @if(!$isComplete)
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 0.65rem;">
                                                    Profile Incomplete
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- College & Course --}}
                            <td class="py-3">
                                <div class="fw-semibold text-dark" style="font-size: 0.82rem;">
                                    {{ $student->profile?->course ?? 'Course Unspecified' }}
                                </div>
                                <div class="text-muted small" style="font-size: 0.74rem;">
                                    {{ $student->profile?->college ?? 'College Unspecified' }}
                                </div>
                                @if($student->profile?->year_level)
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.68rem;">
                                        {{ $student->profile->year_level }}
                                    </span>
                                @endif
                            </td>

                            {{-- Application Status --}}
                            <td class="py-3">
                                @if($latestApp)
                                    <div class="fw-semibold text-dark" style="font-size: 0.82rem;">
                                        {{ $latestApp->program_name }}
                                    </div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        @if($latestApp->status === 'Approved')
                                            <span class="badge bg-success text-white" style="font-size: 0.68rem;">
                                                <i class="fa-solid fa-circle-check me-1"></i> Scholar (Approved)
                                            </span>
                                        @elseif(in_array($latestApp->status, ['Pending', 'Under Review']))
                                            <span class="badge bg-warning text-dark" style="font-size: 0.68rem;">
                                                <i class="fa-solid fa-hourglass-half me-1"></i> {{ $latestApp->status }}
                                            </span>
                                        @elseif($latestApp->status === 'Revoked')
                                            <span class="badge bg-danger text-white" style="font-size: 0.68rem;">
                                                <i class="fa-solid fa-ban me-1"></i> Revoked
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white" style="font-size: 0.68rem;">
                                                {{ $latestApp->status }}
                                            </span>
                                        @endif
                                        <span class="text-muted font-monospace" style="font-size: 0.7rem;">APP-{{ $latestApp->id }}</span>
                                    </div>
                                @else
                                    <span class="text-muted small italic">No active applications</span>
                                @endif
                            </td>

                            {{-- Security & DPA --}}
                            <td class="py-3">
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="fa-solid fa-shield-halved {{ $student->mfaDevices->count() > 0 ? 'text-success' : 'text-muted' }}" style="font-size: 0.75rem;"></i>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            {{ $student->mfaDevices->count() }} Remembered Device(s)
                                        </small>
                                    </div>
                                    <div>
                                        @if($student->dpa_consent_at)
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle" style="font-size: 0.65rem;" title="Agreed on {{ $student->dpa_consent_at->format('M d, Y h:i A') }}">
                                                <i class="fa-solid fa-file-contract me-1"></i> DPA 2012 Consent
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">
                                                DPA Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3 text-center">
                                @if($student->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-circle me-1" style="font-size: 0.45rem;"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-circle me-1" style="font-size: 0.45rem;"></i> Deactivated
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="pe-4 py-3 text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.78rem;">
                                        Manage <i class="fa-solid fa-ellipsis-vertical ms-1"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; font-size: 0.82rem;">
                                        <li>
                                            <form action="{{ route('superadmin.users.toggle-status', $student->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-2 {{ $student->is_active ? 'text-danger' : 'text-success' }}">
                                                    <i class="fa-solid {{ $student->is_active ? 'fa-user-slash text-danger' : 'fa-user-check text-success' }} me-2"></i>
                                                    {{ $student->is_active ? 'Deactivate Account' : 'Reactivate Account' }}
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('superadmin.users.reset-mfa', $student->id) }}" method="POST" onsubmit="return confirm('Reset all MFA sessions and OTP tokens for {{ $student->name }}?')">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-2 text-warning">
                                                    <i class="fa-solid fa-key text-warning me-2"></i> Reset MFA / Clear Devices
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('superadmin.users.send-reset', $student->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-2 text-primary">
                                                    <i class="fa-solid fa-paper-plane text-primary me-2"></i> Send Password Reset Link
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div style="width: 64px; height: 64px; border-radius: 16px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                    <i class="fa-solid fa-user-slash fa-2x text-muted" style="opacity: 0.4;"></i>
                                </div>
                                <h6 class="fw-bold text-muted">No Student Records Found</h6>
                                <p class="text-muted small mb-0">Try changing your search query or college filter criteria.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $students->links() }}
                </div>
            @endif

        @else
            {{-- Staff Personnel Table --}}
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Staff Member</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Institutional Email</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Role & Permissions</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Assigned Scholarships</th>
                            <th class="py-3 text-center text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Status</th>
                            <th class="pe-4 py-3 text-end text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffList as $staff)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div style="width: 42px; height: 42px; border-radius: 50%; background: #e0f2fe; color: #0369a1; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0;" class="me-3">
                                        {{ strtoupper(substr($staff->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $staff->name }}</div>
                                        @if($staff->isMaster())
                                            <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.65rem;">
                                                <i class="fa-solid fa-crown me-1"></i> Master Account
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <span class="font-monospace small text-muted">{{ $staff->email }}</span>
                            </td>

                            <td class="py-3">
                                @if($staff->role === 'superadmin')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-user-shield me-1"></i> Director / SuperAdmin
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-shield me-1"></i> OSA Evaluator (Admin)
                                    </span>
                                @endif
                            </td>

                            <td class="py-3">
                                @if($staff->role === 'superadmin')
                                    <span class="badge text-white px-2 py-1 rounded" style="background: var(--clsu-green); font-size: 0.72rem;">
                                        All Programs (Full Authority)
                                    </span>
                                @else
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($staff->scholarships as $prog)
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.7rem;">
                                                {{ $prog->name }}
                                            </span>
                                        @empty
                                            <span class="text-muted small italic">No assigned programs</span>
                                        @endforelse
                                    </div>
                                @endif
                            </td>

                            <td class="py-3 text-center">
                                @if(!$staff->is_active)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                        Deactivated
                                    </span>
                                @elseif($staff->email_verified_at)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                        Active
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                        Pending Invite
                                    </span>
                                @endif
                            </td>

                            <td class="pe-4 py-3 text-end">
                                @if(!$staff->isMaster() && $staff->id !== auth()->id())
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.78rem;">
                                            Manage <i class="fa-solid fa-ellipsis-vertical ms-1"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; font-size: 0.82rem;">
                                            <li>
                                                <button type="button" class="dropdown-item py-2" data-bs-toggle="modal" data-bs-target="#assignModal-{{ $staff->id }}">
                                                    <i class="fa-solid fa-list-check text-primary me-2"></i> Reassign Programs
                                                </button>
                                            </li>
                                            <li>
                                                <form action="{{ route('superadmin.users.toggle-status', $staff->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-2 {{ $staff->is_active ? 'text-danger' : 'text-success' }}">
                                                        <i class="fa-solid {{ $staff->is_active ? 'fa-user-slash text-danger' : 'fa-user-check text-success' }} me-2"></i>
                                                        {{ $staff->is_active ? 'Revoke Access' : 'Reactivate Access' }}
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('superadmin.users.reset-mfa', $staff->id) }}" method="POST" onsubmit="return confirm('Reset MFA tokens for {{ $staff->name }}?')">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-2 text-warning">
                                                        <i class="fa-solid fa-key text-warning me-2"></i> Reset MFA / Clear Devices
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Assignment Modal for this staff --}}
                                    <div class="modal fade" id="assignModal-{{ $staff->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                                <div class="modal-header border-0 bg-light">
                                                    <h6 class="modal-title fw-bold text-dark">
                                                        Assign Programs: {{ $staff->name }}
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('superadmin.staff.assign', $staff->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body text-start">
                                                        <p class="small text-muted mb-3">Select the scholarship programs this evaluator has jurisdiction to review:</p>
                                                        @foreach($scholarships as $prog)
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="scholarship_ids[]" value="{{ $prog->id }}" id="prog-{{ $staff->id }}-{{ $prog->id }}"
                                                                    {{ $staff->scholarships->contains($prog->id) ? 'checked' : '' }}>
                                                                <label class="form-check-label small fw-semibold" for="prog-{{ $staff->id }}-{{ $prog->id }}">
                                                                    {{ $prog->name }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn text-white fw-bold" style="background: var(--clsu-green);">Save Assignments</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small fw-semibold">Protected</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div style="width: 64px; height: 64px; border-radius: 16px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                    <i class="fa-solid fa-users-gear fa-2x text-muted" style="opacity: 0.4;"></i>
                                </div>
                                <h6 class="fw-bold text-muted">No Staff Personnel Found</h6>
                                <p class="text-muted small mb-0">Use the "Invite New Staff" button above to grant evaluation access.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Invite Staff Modal --}}
<div class="modal fade" id="inviteStaffModal" tabindex="-1" aria-labelledby="inviteStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #0f1f12, #0F5934); padding: 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="inviteStaffModalLabel">
                        <i class="fa-solid fa-user-plus text-warning me-2"></i> Invite Staff Evaluator
                    </h5>
                    <small class="text-white-50">Authorized OSA evaluators will receive an institutional activation email</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('superadmin.staff.invite') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="staffName">Full Name</label>
                        <input type="text" name="name" id="staffName" class="form-control" required placeholder="e.g. Maria Santos" autocomplete="off" style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="staffEmail">CLSU Institutional Email</label>
                        <input type="email" name="email" id="staffEmail" class="form-control" required placeholder="e.g. msantos@clsu.edu.ph" style="border-radius: 10px;">
                        <small class="text-muted" style="font-size: 0.72rem;">Must be @clsu.edu.ph or @clsu2.edu.ph</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="staffRole">Role Level</label>
                        <select name="role" id="staffRole" class="form-select" style="border-radius: 10px;">
                            <option value="admin" selected>OSA Staff Evaluator (Admin)</option>
                            <option value="superadmin">Director / Full Authority (SuperAdmin)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Initial Scholarship Assignments</label>
                        <div class="border rounded-3 p-3" style="max-height: 160px; overflow-y: auto;">
                            @foreach($scholarships as $prog)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="scholarship_ids[]" value="{{ $prog->id }}" id="invite-prog-{{ $prog->id }}">
                                    <label class="form-check-label small" for="invite-prog-{{ $prog->id }}">
                                        {{ $prog->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">SuperAdmins automatically receive full access to all programs.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                    <button type="submit" class="btn text-white fw-bold px-4" style="background: var(--clsu-green); border-radius: 10px;">
                        Send Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
