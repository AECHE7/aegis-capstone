@extends('layouts.app')

@section('title', 'Applicant & Student Forms Generator | ' . \App\Models\Setting::get('app_name', 'A.E.G.I.S.'))

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

    {{-- Top Hero Header --}}
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" 
         style="border-radius: 18px; background: linear-gradient(135deg, #072314 0%, #0c4e2d 60%, #157342 100%);">
        <div class="card-body p-4 text-white position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark px-3 py-1.5 fw-bold" style="border-radius: 20px; font-size: 0.72rem; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-stamp me-1"></i> OFFICIAL FORM GENERATOR
                        </span>
                        <span class="badge text-white-50 px-2 py-1" style="background: rgba(255,255,255,0.12); border-radius: 20px; font-size: 0.72rem;">
                            Director & Staff Module
                        </span>
                    </div>
                    <h3 class="fw-bold text-white mb-2" style="letter-spacing: -0.02em;">
                        Applicant & Scholar Information Forms
                    </h3>
                    <p class="text-white-50 mb-0 small" style="max-width: 680px; line-height: 1.55;">
                        Generate, inspect, and export official CLSU OSA application & evaluation forms for applicants and scholars across all workflow stages. Every form matches the official approved institutional design with dual seals, academic credentials, and forensic integrity signatures.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="{{ route('admin.exportPdf') }}" class="btn btn-outline-light btn-sm fw-semibold rounded-pill px-3 py-2 me-2">
                        <i class="fa-solid fa-file-pdf me-1"></i> Bulk Summary PDF
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm fw-bold text-success rounded-pill px-3 py-2 shadow-sm">
                        <i class="fa-solid fa-layer-group me-1"></i> Review Queue
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Metrics Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 14px; background: #ffffff;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-semibold">Total Records</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 34px; height: 34px;">
                        <i class="fa-solid fa-users" style="font-size: 0.85rem;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total'] ?? 0) }}</h4>
                <span class="text-muted" style="font-size: 0.7rem;">Application Dossiers</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 14px; background: #ffffff;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-semibold">Approved Scholars</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 34px; height: 34px;">
                        <i class="fa-solid fa-award" style="font-size: 0.85rem;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-success">{{ number_format($stats['approved'] ?? 0) }}</h4>
                <span class="text-muted" style="font-size: 0.7rem;">Official Grants Active</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 14px; background: #ffffff;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-semibold">Pending / Under Review</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 34px; height: 34px;">
                        <i class="fa-solid fa-hourglass-half" style="font-size: 0.85rem;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-warning">{{ number_format($stats['review'] ?? 0) }}</h4>
                <span class="text-muted" style="font-size: 0.7rem;">Evaluation in Progress</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 14px; background: #ffffff;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-semibold">Today's Inflow</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info" style="width: 34px; height: 34px;">
                        <i class="fa-solid fa-calendar-day" style="font-size: 0.85rem;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['today'] ?? 0) }}</h4>
                <span class="text-muted" style="font-size: 0.7rem;">New Submissions</span>
            </div>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('admin.applicant-forms.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0 ps-0" 
                               placeholder="Search applicant name, CLSU ID, email, or APP-#" autocomplete="off">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="scholarship_id" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="">All Scholarship Programs</option>
                        @foreach($scholarships as $scholarship)
                            <option value="{{ $scholarship->id }}" {{ request('scholarship_id') == $scholarship->id ? 'selected' : '' }}>
                                {{ $scholarship->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="status" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Under Review" {{ request('status') === 'Under Review' ? 'selected' : '' }}>Under Review</option>
                        <option value="Pending Verification" {{ request('status') === 'Pending Verification' ? 'selected' : '' }}>Pending Verification</option>
                        <option value="Incomplete" {{ request('status') === 'Incomplete' ? 'selected' : '' }}>Incomplete</option>
                        <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="academic_term_id" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="">All Terms</option>
                        @foreach($academicTerms as $term)
                            <option value="{{ $term->id }}" {{ request('academic_term_id') == $term->id ? 'selected' : '' }}>
                                {{ $term->semester }} ({{ $term->academic_year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-success w-100 fw-bold rounded-3" title="Apply Filter">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                    @if(request()->anyFilled(['q', 'scholarship_id', 'status', 'academic_term_id']))
                        <a href="{{ route('admin.applicant-forms.index') }}" class="btn btn-sm btn-light border w-100 text-muted" title="Clear Filters">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Applicant Dossier Cards --}}
    @if($applications->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 16px;">
            <div class="card-body">
                <i class="fa-solid fa-folder-open text-muted opacity-25 mb-3" style="font-size: 3rem;"></i>
                <h5 class="fw-bold text-dark">No applicant records found</h5>
                <p class="text-muted small mb-3">No applications match your selected search or filter criteria.</p>
                <a href="{{ route('admin.applicant-forms.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-4">
                    Reset All Filters
                </a>
            </div>
        </div>
    @else
        <div class="row g-3 mb-4">
            @foreach($applications as $app)
                @php
                    $profile = $app->user->profile ?? null;
                    $statusColor = match($app->status) {
                        'Approved' => '#0c4e2d',
                        'Under Review' => '#0284c7',
                        'Incomplete' => '#d97706',
                        'Rejected' => '#dc2626',
                        default => '#475569',
                    };
                    $statusBg = match($app->status) {
                        'Approved' => 'rgba(12, 78, 45, 0.1)',
                        'Under Review' => 'rgba(2, 132, 199, 0.1)',
                        'Incomplete' => 'rgba(217, 119, 6, 0.1)',
                        'Rejected' => 'rgba(220, 38, 38, 0.1)',
                        default => 'rgba(71, 85, 105, 0.1)',
                    };
                @endphp
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100 transition-all applicant-card" 
                         style="border-radius: 16px; border: 1px solid rgba(0,0,0,0.06) !important; background: #ffffff;">
                        <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                            
                            {{-- Card Header: Student Avatar & Basic Info --}}
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2.5">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-circle overflow-hidden flex-shrink-0 border" 
                                             style="width: 44px; height: 44px; background: #f1f5f9;">
                                            @if($profile && !empty($profile->profile_picture))
                                                <img src="{{ asset('storage/' . $profile->profile_picture) }}" 
                                                     alt="{{ $app->user->name ?? 'Applicant' }}" 
                                                     style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted fw-bold" style="font-size: 0.95rem;">
                                                    {{ strtoupper(substr($app->user->name ?? 'A', 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem; line-height: 1.25;">
                                                {{ $app->user->name ?? 'Applicant' }}
                                            </h6>
                                            <span class="text-muted" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-id-card me-1 opacity-60"></i>
                                                {{ $profile->clsu_id_number ?? 'No ID Set' }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="badge fw-semibold text-truncate" 
                                          style="font-size: 0.68rem; padding: 0.35rem 0.65rem; border-radius: 20px; color: {{ $statusColor }}; background: {{ $statusBg }}; border: 1px solid {{ $statusColor }}33;">
                                        {{ $app->status }}
                                    </span>
                                </div>

                                {{-- Academic & Program Details --}}
                                <div class="p-2.5 rounded-3 mb-3" style="background: #f8fafc; font-size: 0.75rem;">
                                    <div class="d-flex justify-content-between text-muted mb-1">
                                        <span>Program:</span>
                                        <strong class="text-dark text-truncate ms-2" style="max-width: 180px;">
                                            {{ $app->scholarship->name ?? $app->program_name }}
                                        </strong>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted mb-1">
                                        <span>College:</span>
                                        <span class="text-dark text-truncate ms-2" style="max-width: 180px;">
                                            {{ $profile->college ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted">
                                        <span>Control No:</span>
                                        <span class="font-monospace fw-bold text-success">
                                            APP-{{ str_pad((string)$app->id, 5, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons: Live Form Preview & Export PDF --}}
                            <div class="d-flex gap-2 pt-2 border-top">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-success fw-bold flex-grow-1 rounded-pill py-1.5"
                                        style="font-size: 0.78rem;"
                                        onclick="openApplicantFormModal({{ $app->id }})">
                                    <i class="fa-solid fa-file-lines me-1"></i> View Live Form
                                </button>
                                <a href="{{ route('admin.application.download-form', $app->id) }}" 
                                   target="_blank"
                                   class="btn btn-sm btn-success fw-bold rounded-pill px-3 py-1.5 text-white"
                                   style="font-size: 0.78rem; background: #0c4e2d; border-color: #0c4e2d;"
                                   title="Export Official PDF Form">
                                    <i class="fa-solid fa-download me-1"></i> Export PDF
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $applications->links() }}
        </div>
    @endif

</div>

{{-- Universal On-Screen Live Form Preview Modal Component --}}
@include('components.applicant-form-modal')

<style>
.applicant-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.applicant-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
}
</style>
@endsection
