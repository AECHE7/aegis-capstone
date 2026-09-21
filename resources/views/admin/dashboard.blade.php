@extends('layouts.app')

@section('title', 'OSA Admin Dashboard | A.E.G.I.S.')
@section('page-title', 'Application Queue')
@section('page-subtitle', 'Review and evaluate all scholarship applications')

@push('styles')
<style>
    /* Count-up animation */
    .stat-number { font-size: 2rem; font-weight: 700; font-family: 'Poppins', sans-serif; line-height: 1; }

    /* Searchbar */
    .filter-bar {
        background: rgba(255, 255, 255, 0.45);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.015) !important;
    }
    [data-theme="dark"] .filter-bar {
        background: rgba(17, 24, 39, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.05) !important;
    }

    /* Table */
    .queue-table { border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); background: var(--card-bg); }
    .queue-table thead th { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: var(--text-main); background: var(--clsu-bg); padding: 0.9rem 1rem; border-bottom: 2px solid var(--border-color); }
    .queue-table tbody tr { border-color: var(--border-color); cursor: pointer; transition: var(--transition); }
    .queue-table tbody tr:hover { background: var(--clsu-bg); }
    .queue-table td { padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.875rem; }

    /* Avatar */
    .student-avatar { width: 36px; height: 36px; border-radius: 8px; background: var(--clsu-bg); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; color: var(--clsu-green); flex-shrink: 0; }

    /* Fraud Score Chip */
    .fraud-chip { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; }
    .fraud-low    { background: rgba(34, 197, 94, 0.15); color: #16a34a; }
    .fraud-mod    { background: rgba(217, 119, 6, 0.15); color: #d97706; }
    .fraud-high   { background: rgba(220, 38, 38, 0.15); color: #dc2626; }
    .fraud-none   { background: var(--clsu-bg); color: var(--text-main); }

    /* Action button */
    .btn-evaluate { background: var(--clsu-green); color: white; border: none; padding: 6px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; transition: var(--transition); }
    .btn-evaluate:hover { background: var(--clsu-green-light); color: white; }

    /* Export buttons */
    .btn-export { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; border: 1.5px solid; transition: all 0.2s; text-decoration: none; }
    .btn-export:hover { transform: translateY(-1px); }
    .btn-export-csv { border-color: #22c55e; color: #16a34a; }
    .btn-export-csv:hover { background: #22c55e; color: white; }
    .btn-export-pdf { border-color: #ef4444; color: #dc2626; }
    .btn-export-pdf:hover { background: #ef4444; color: white; }
</style>
@endpush

@section('content')

{{-- Stat Cards Row --}}
<div class="row g-3 mb-4">
    {{-- Pending --}}
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card warning card-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem;letter-spacing:0.8px;">Pending</div>
                    <div class="stat-number text-dark count-up monospace-data" data-target="{{ $pendingCount }}">0</div>
                </div>
                <div class="stat-icon" style="background:#fef9c3;">
                    <i class="fa-solid fa-hourglass-half" style="color:#ca8a04;"></i>
                </div>
            </div>
            <div class="small text-muted">Awaiting review</div>
        </div>
    </div>
    {{-- Under Review --}}
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card info card-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem;letter-spacing:0.8px;">Under Review</div>
                    <div class="stat-number text-dark count-up monospace-data" data-target="{{ $underReviewCount }}">0</div>
                </div>
                <div class="stat-icon" style="background:#e0f2fe;">
                    <i class="fa-solid fa-magnifying-glass-chart" style="color:#0284c7;"></i>
                </div>
            </div>
            <div class="small text-muted">Being evaluated</div>
        </div>
    </div>
    {{-- Approved --}}
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card success card-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem;letter-spacing:0.8px;">Verified Scholars</div>
                    <div class="stat-number text-dark count-up monospace-data" data-target="{{ $approvedCount }}">0</div>
                </div>
                <div class="stat-icon" style="background:#dcfce7;">
                    <i class="fa-solid fa-user-graduate" style="color:#16a34a;"></i>
                </div>
            </div>
            <div class="small text-muted">Fully approved</div>
        </div>
    </div>
    {{-- Rejected --}}
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card danger card-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem;letter-spacing:0.8px;">Rejected</div>
                    <div class="stat-number text-dark count-up monospace-data" data-target="{{ $rejectedCount }}">0</div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;">
                    <i class="fa-solid fa-shield-virus" style="color:#dc2626;"></i>
                </div>
            </div>
            <div class="small text-muted">Denied by reviewer</div>
        </div>
    </div>
</div>

{{-- Space-Efficient Unified Control Bar --}}
<div class="unified-control-bar mb-3">
    <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap w-100 mb-0">
        {{-- Search Input (Live Debounced) --}}
        <div class="position-relative flex-grow-1" style="min-width: 220px; max-width: 320px;">
            <i class="fa-solid fa-magnifying-glass position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 0.82rem;" aria-hidden="true"></i>
            <input type="text" name="search" id="searchInput" class="form-control ps-5 py-1.5" 
                   value="{{ request('search') }}" placeholder="Search applicant, ID, program..." 
                   autocomplete="off" style="font-size: 0.85rem; border-radius: 20px; min-height: 38px;">
            @if(request('search'))
                <button type="button" class="btn btn-link position-absolute p-0 text-muted" style="right: 12px; top: 50%; transform: translateY(-50%); text-decoration: none;" onclick="document.getElementById('searchInput').value=''; reloadQueue();">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            @endif
        </div>

        {{-- Quick Status Filter Pills (1-Click Toggling) --}}
        <div class="d-flex align-items-center gap-1.5 flex-wrap" id="statusPillsGroup">
            <button type="button" class="filter-status-pill {{ !request('status') ? 'active' : '' }}" data-status="">
                <i class="fa-solid fa-layer-group" aria-hidden="true"></i> All
            </button>
            <button type="button" class="filter-status-pill {{ request('status') === 'Pending' ? 'active' : '' }}" data-status="Pending">
                <i class="fa-solid fa-hourglass-half text-warning" aria-hidden="true"></i> Pending
                <span class="pill-count count-up" data-target="{{ $pendingCount }}">{{ $pendingCount }}</span>
            </button>
            <button type="button" class="filter-status-pill {{ request('status') === 'Under Review' ? 'active' : '' }}" data-status="Under Review">
                <i class="fa-solid fa-magnifying-glass-chart text-info" aria-hidden="true"></i> In Review
                <span class="pill-count count-up" data-target="{{ $underReviewCount }}">{{ $underReviewCount }}</span>
            </button>
            <button type="button" class="filter-status-pill {{ request('status') === 'Approved' ? 'active' : '' }}" data-status="Approved">
                <i class="fa-solid fa-user-graduate text-success" aria-hidden="true"></i> Approved
                <span class="pill-count count-up" data-target="{{ $approvedCount }}">{{ $approvedCount }}</span>
            </button>
            <button type="button" class="filter-status-pill {{ request('status') === 'Rejected' ? 'active' : '' }}" data-status="Rejected">
                <i class="fa-solid fa-shield-virus text-danger" aria-hidden="true"></i> Rejected
                <span class="pill-count count-up" data-target="{{ $rejectedCount }}">{{ $rejectedCount }}</span>
            </button>
        </div>

        {{-- More Filters & Sort Dropdown Popover --}}
        <div class="dropdown ms-auto">
            @php
                $activeSecondaryCount = 0;
                if(request('scholarship_id')) $activeSecondaryCount++;
                if(request('type')) $activeSecondaryCount++;
                if(request('academic_term_id')) $activeSecondaryCount++;
                if(request('sort')) $activeSecondaryCount++;
            @endphp
            <button class="btn btn-sm btn-clsu-secondary rounded-pill px-3 py-1.5 text-nowrap d-inline-flex align-items-center gap-1.5" 
                    type="button" id="moreFiltersDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <i class="fa-solid fa-sliders text-success" aria-hidden="true"></i>
                <span>Filters & Sort</span>
                @if($activeSecondaryCount > 0)
                    <span class="badge bg-success rounded-pill px-1.5 py-0.5" id="activeFilterBadge" style="font-size:0.65rem;">{{ $activeSecondaryCount }}</span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0" aria-labelledby="moreFiltersDropdown" style="width: 320px; border-radius: 16px; z-index: 1050;">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <span class="fw-bold small text-dark"><i class="fa-solid fa-filter text-success me-1"></i> Secondary Filters</span>
                    <a href="{{ route('admin.dashboard') }}" class="small text-decoration-none text-muted" title="Reset All Filters">Reset</a>
                </div>

                {{-- Controlled Status Selector --}}
                <select name="status" id="statusSelect" class="d-none">
                    <option value="" {{ !request('status') ? 'selected' : '' }}>All</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Under Review" {{ request('status') === 'Under Review' ? 'selected' : '' }}>Under Review</option>
                    <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <div class="mb-2">
                    <label class="form-label fw-semibold small text-muted mb-1" for="scholarshipSelect">Scholarship Program</label>
                    <select name="scholarship_id" id="scholarshipSelect" class="form-select form-select-sm" style="border-radius: 8px;">
                        <option value="">All Programs</option>
                        @foreach($scholarships as $s)
                            <option value="{{ $s->id }}" {{ request('scholarship_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-semibold small text-muted mb-1" for="academicPeriodSelect">Academic Period</label>
                    <select name="academic_term_id" id="academicPeriodSelect" class="form-select form-select-sm" style="border-radius: 8px;">
                        <option value="">All Periods</option>
                        @foreach($academicTerms as $term)
                            <option value="{{ $term->id }}" {{ request('academic_term_id') == $term->id ? 'selected' : '' }}>
                                {{ $term->semester }}, SY {{ $term->academic_year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small text-muted mb-1" for="typeSelect">App Type</label>
                        <select name="type" id="typeSelect" class="form-select form-select-sm" style="border-radius: 8px;">
                            <option value="">All Types</option>
                            <option value="new" {{ request('type') === 'new' ? 'selected' : '' }}>First Time</option>
                            <option value="renewal" {{ request('type') === 'renewal' ? 'selected' : '' }}>Renewal</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small text-muted mb-1" for="sortSelect">Sort Order</label>
                        <select name="sort" id="sortSelect" class="form-select form-select-sm" style="border-radius: 8px;">
                            <option value="">Newest</option>
                            <option value="priority" {{ request('sort') === 'priority' ? 'selected' : '' }}>AEGIS Priority</option>
                            <option value="gwa_asc" {{ request('sort') === 'gwa_asc' ? 'selected' : '' }}>GWA (Lowest)</option>
                            <option value="gwa_desc" {{ request('sort') === 'gwa_desc' ? 'selected' : '' }}>GWA (Highest)</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-clsu-primary btn-sm w-100">
                        <i class="fa-solid fa-check me-1"></i> Apply
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm border" style="border-radius: 8px;" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- 1-Click Clear Button (Only visible when filters active) --}}
        @if(request('search') || request('status') || request('scholarship_id') || request('academic_term_id') || request('type') || request('sort'))
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-muted" title="Clear all filters">
                <i class="fa-solid fa-rotate-left me-1"></i> Clear
            </a>
        @endif
    </form>
</div>

{{-- Application Queue Table --}}
<div class="queue-table shadow-sm mb-4" id="tableContainer">
    @include('admin.partials.application_table')
</div>

{{-- Active Scholars Monitoring Panel (Collapsible to prevent visual crowding) --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#activeScholarsCollapse" style="cursor: pointer;" aria-expanded="false" aria-controls="activeScholarsCollapse">
            <div>
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-success"></i> Current Term Scholars Monitoring
                    <i class="fa-solid fa-chevron-down text-muted fs-6 transition" style="transition: transform 0.2s;"></i>
                </h5>
                <small class="text-muted">Direct oversight of active approved scholars and grade performance (click to toggle)</small>
            </div>
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold small" style="background-color: #dcfce7; color: #14532d;">
                Active Grants: {{ $activeScholars->count() }}
            </span>
        </div>

        <div class="collapse mt-4" id="activeScholarsCollapse">


        <div class="table-responsive">
            <table class="table table-mobile-cards mb-0 align-middle">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--text-main);">
                        <th class="ps-3" scope="col">Scholar</th>
                        <th scope="col">Scholarship Program</th>
                        <th scope="col">Active Term</th>
                        <th class="text-center" scope="col">Min GWA</th>
                        <th class="text-center" scope="col">Student GWA</th>
                        <th class="text-center" scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeScholars as $scholar)
                        <tr style="border-bottom: 1px solid var(--border-color); font-size: 0.85rem;">
                            <td class="ps-3 py-3" data-label="Scholar"><div class="d-flex align-items-center gap-2">
                                    <div class="student-avatar" style="width: 32px; height: 32px; font-size: 0.8rem; background-color: #f0fdf4; color: var(--clsu-green);">
                                        {{ strtoupper(substr($scholar->user->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $scholar->user->name ?? 'Unknown' }}</div>
                                        <div class="text-muted small monospace-data" style="font-size: 0.7rem;">{{ $scholar->user->profile?->clsu_id_number ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-medium text-dark">{{ $scholar->scholarship->name ?? $scholar->program_name }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    @if($scholar->academicTerm)
                                        {{ $scholar->academicTerm->semester }} Sem, AY {{ $scholar->academicTerm->academic_year }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="monospace-data text-muted">{{ $scholar->scholarship->min_gwa_required ?? 'N/A' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold monospace-data" 
                                      style="background: #f0fdf4; color: var(--clsu-green); border: 1px solid #bcf0da; font-size: 0.78rem;">
                                    {{ $scholar->gwa !== null ? number_format($scholar->gwa, 2) : 'N/A' }}
                                </span>
                            </td>

                            <td class="text-center">
                                @php
                                    $isGwaValid = !$scholar->scholarship || !$scholar->scholarship->min_gwa_required || ($scholar->gwa <= $scholar->scholarship->min_gwa_required);
                                @endphp
                                @if($isGwaValid)
                                    <span class="badge bg-success px-2 py-1 rounded-pill" style="font-size: 0.68rem;"><i class="fa-solid fa-circle-check me-1"></i> Compliant</span>
                                @else
                                    <span class="badge bg-danger px-2 py-1 rounded-pill" style="font-size: 0.68rem;" title="Student's GWA exceeds the maximum allowed limit for this scholarship program"><i class="fa-solid fa-circle-exclamation me-1"></i> GWA Violation</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">
                                <i class="fa-solid fa-circle-info me-1"></i> No approved scholars currently registered in this tracking term.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>

{{-- Floating Bulk Action Bar --}}
<div id="bulkActionBar" class="position-fixed bottom-0 start-50 translate-middle-x mb-4 bg-white border border-color rounded-4 p-3 shadow-lg d-none align-items-center gap-3" style="z-index: 1050; min-width: 500px; transition: all 0.3s ease; border-width: 1.5px !important;">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success rounded-pill px-2.5 py-1.5 fs-7" id="selectedCountBadge">0</span>
        <span class="small fw-semibold text-dark">selected</span>
    </div>
    <div style="width: 1px; height: 24px; background: var(--border-color);"></div>
    <div class="d-flex align-items-center gap-2 flex-grow-1">
        <select id="bulkStatusSelect" class="form-select form-select-sm" style="border-radius: 8px; width: 140px;">
            <option value="">Choose action...</option>
            <option value="Approved">✅ Approve</option>
            <option value="Rejected">❌ Reject</option>
        </select>
        <input type="text" id="bulkRemarksInput" class="form-control form-control-sm" placeholder="Bulk process remarks (optional)..." style="border-radius: 8px;" />
        <button class="btn btn-sm btn-success px-3 fw-bold" onclick="submitBulkAction()" style="border-radius: 8px;">Apply</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Count-up animation ──────────────────────────────
    function animateCountUp(el, target) {
        if (target === 0) { el.textContent = '0'; return; }
        let start = parseInt(el.textContent) || 0;
        const diff = target - start;
        if (diff === 0) return;
        const duration = 400; // ms
        const steps = 15;
        const stepTime = duration / steps;
        const stepVal = Math.ceil(diff / steps);
        let currentStep = 0;
        
        const timer = setInterval(() => {
            currentStep++;
            start += stepVal;
            if (diff > 0) {
                start = Math.min(start, target);
            } else {
                start = Math.max(start, target);
            }
            el.textContent = start;
            if (currentStep >= steps || start === target) {
                el.textContent = target;
                clearInterval(timer);
            }
        }, stepTime);
    }

    document.querySelectorAll('.count-up').forEach(el => {
        const target = parseInt(el.dataset.target);
        animateCountUp(el, target);
    });

    // ── AJAX Queue Reloading ───────────────────────────
    const filterForm = document.querySelector('.filter-bar form');
    const tableContainer = document.getElementById('tableContainer');
    const searchInput = document.getElementById('searchInput');
    const scholarshipSelect = document.getElementById('scholarshipSelect');
    const statusSelect = document.getElementById('statusSelect');
    const academicPeriodSelect = document.getElementById('academicPeriodSelect');
    const typeSelect = document.getElementById('typeSelect');
    const sortSelect = document.getElementById('sortSelect');
    
    let debounceTimer;
    let currentArchivedState = "{{ request('archived') == '1' ? '1' : '0' }}";

    function getFilterParams(page = 1) {
        const params = new URLSearchParams();
        if (searchInput && searchInput.value) params.set('search', searchInput.value);
        if (scholarshipSelect && scholarshipSelect.value) params.set('scholarship_id', scholarshipSelect.value);
        if (statusSelect && statusSelect.value) params.set('status', statusSelect.value);
        if (academicPeriodSelect && academicPeriodSelect.value) params.set('academic_term_id', academicPeriodSelect.value);
        if (typeSelect && typeSelect.value) params.set('type', typeSelect.value);
        if (sortSelect && sortSelect.value) params.set('sort', sortSelect.value);
        if (currentArchivedState === '1') params.set('archived', '1');
        params.set('page', page);
        return params.toString();
    }

    async function reloadQueue(page = 1) {
        // Show subtle loading overlay or opacity
        tableContainer.style.opacity = '0.6';
        tableContainer.style.transition = 'opacity 0.2s ease';
        
        const queryParams = getFilterParams(page);
        const url = `{{ route('admin.dashboard') }}?${queryParams}`;

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            // Update table html
            tableContainer.innerHTML = data.html;
            tableContainer.style.opacity = '1';
            
            // Update counts cards and pills
            if (data.counts) {
                const countMappings = {
                    'pending': '.stat-card:nth-of-type(1) .stat-number',
                    'under_review': '.stat-card:nth-of-type(2) .stat-number',
                    'approved': '.stat-card:nth-of-type(3) .stat-number',
                    'rejected': '.stat-card:nth-of-type(4) .stat-number'
                };
                for (const [key, selector] of Object.entries(countMappings)) {
                    const el = document.querySelector(selector);
                    if (el) animateCountUp(el, data.counts[key]);
                }

                // Update pill count badges
                const pendingPill = document.querySelector('.filter-status-pill[data-status="Pending"] .pill-count');
                if (pendingPill) pendingPill.textContent = data.counts.pending || 0;
                const reviewPill = document.querySelector('.filter-status-pill[data-status="Under Review"] .pill-count');
                if (reviewPill) reviewPill.textContent = data.counts.under_review || 0;
                const approvedPill = document.querySelector('.filter-status-pill[data-status="Approved"] .pill-count');
                if (approvedPill) approvedPill.textContent = data.counts.approved || 0;
                const rejectedPill = document.querySelector('.filter-status-pill[data-status="Rejected"] .pill-count');
                if (rejectedPill) rejectedPill.textContent = data.counts.rejected || 0;
            }

            // Sync active pill selection
            const currentStatusVal = statusSelect ? statusSelect.value : '';
            document.querySelectorAll('.filter-status-pill').forEach(pill => {
                pill.classList.toggle('active', pill.dataset.status === currentStatusVal);
            });

            // Sync address bar
            window.history.pushState({}, '', url);
            
            // Reset bulk actions bar selection state
            const selectAll = document.getElementById('selectAllCheckbox');
            if (selectAll) selectAll.checked = false;
            updateBulkActionBar();
        } catch (error) {
            console.error('AJAX Load Error:', error);
            tableContainer.style.opacity = '1';
        }
    }

    // Status Pill Event Listeners (1-click quick filtering)
    document.querySelectorAll('.filter-status-pill').forEach(pill => {
        pill.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('.filter-status-pill').forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            if (statusSelect) {
                statusSelect.value = pill.dataset.status;
                reloadQueue();
            }
        });
    });

    // Event listeners
    if (filterForm) {
        filterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            reloadQueue();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => reloadQueue(), 300);
        });
    }

    [scholarshipSelect, statusSelect, academicPeriodSelect, typeSelect, sortSelect].forEach(select => {
        if (select) {
            select.addEventListener('change', () => reloadQueue());
        }
    });


    // Handle pagination links click via event delegation
    tableContainer.addEventListener('click', (e) => {
        const pageLink = e.target.closest('.pagination a');
        if (pageLink) {
            e.preventDefault();
            const urlObj = new URL(pageLink.href);
            const page = urlObj.searchParams.get('page') || 1;
            reloadQueue(page);
            window.scrollTo({ top: tableContainer.offsetTop - 100, behavior: 'smooth' });
        }
    });

    // Handle Active/Archived toggle
    tableContainer.addEventListener('click', (e) => {
        const toggleBtn = e.target.closest('.active-queue-toggle');
        if (toggleBtn) {
            e.preventDefault();
            currentArchivedState = toggleBtn.dataset.archived;
            reloadQueue();
        }
    });

    // Handle Cancelled Queue toggle
    tableContainer.addEventListener('click', (e) => {
        const cancelledBtn = e.target.closest('#cancelledQueueToggle');
        if (cancelledBtn) {
            e.preventDefault();
            statusSelect.value = 'Cancelled';
            reloadQueue();
        }
    });

    // Handle Archive/Unarchive AJAX Actions
    tableContainer.addEventListener('submit', async (e) => {
        const archiveForm = e.target.closest('.archive-form');
        if (archiveForm) {
            e.preventDefault();
            const submitBtn = archiveForm.querySelector('button');
            const originalHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';

            try {
                const response = await fetch(archiveForm.action, {
                    method: 'POST',
                    body: new FormData(archiveForm),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4' }
                    });
                    // Reload current page of queue
                    const activePage = document.querySelector('.pagination .active span')?.textContent || 1;
                    reloadQueue(activePage);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Archive Action Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }
    });

    // ── Bulk Actions Handlers ──────────────────────────
    function toggleSelectAll(selectAllInput) {
        const checkboxes = document.querySelectorAll('.app-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = selectAllInput.checked;
        });
        updateBulkActionBar();
    }

    function toggleAppSelect(checkboxInput) {
        const selectAll = document.getElementById('selectAllCheckbox');
        const checkboxes = document.querySelectorAll('.app-checkbox');
        
        // Update select all state
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        if (selectAll) selectAll.checked = allChecked;

        updateBulkActionBar();
    }

    function updateBulkActionBar() {
        const bar = document.getElementById('bulkActionBar');
        const badge = document.getElementById('selectedCountBadge');
        const checkedBoxes = document.querySelectorAll('.app-checkbox:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            badge.textContent = count;
            bar.classList.remove('d-none');
            bar.classList.add('d-flex');
        } else {
            bar.classList.remove('d-flex');
            bar.classList.add('d-none');
        }
    }

    async function submitBulkAction() {
        const statusSelect = document.getElementById('bulkStatusSelect');
        const remarksInput = document.getElementById('bulkRemarksInput');
        const checkedBoxes = document.querySelectorAll('.app-checkbox:checked');
        
        const status = statusSelect.value;
        if (!status) {
            Swal.fire({
                icon: 'warning',
                title: 'No Action Selected',
                text: 'Please select whether to Approve or Reject the selected applications.',
                confirmButtonColor: '#ca8a04',
                customClass: { popup: 'rounded-4' }
            });
            return;
        }

        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        
        // Show confirm dialog
        const result = await Swal.fire({
            title: `Bulk Process Applications?`,
            text: `You are about to set ${ids.length} application(s) status to "${status}". This action will notify all selected applicants.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0F5934',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, proceed',
            customClass: { popup: 'rounded-4' }
        });

        if (!result.isConfirmed) return;

        // Perform AJAX request
        Swal.fire({
            title: 'Processing Request',
            html: 'Updating status and sending notification emails in background...',
            allowOutsideClick: false,
            showConfirmButton: false,
            customClass: { popup: 'rounded-4' },
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch("{{ route('admin.applications.bulk-action') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    application_ids: ids,
                    status: status,
                    remarks: remarksInput.value
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Bulk Process Completed',
                    text: data.message,
                    confirmButtonColor: '#0F5934',
                    customClass: { popup: 'rounded-4' }
                });
                
                // Hide actions bar
                document.getElementById('bulkStatusSelect').value = '';
                document.getElementById('bulkRemarksInput').value = '';
                updateBulkActionBar();
                
                // Reload current page of queue
                const activePage = document.querySelector('.pagination .active span')?.textContent || 1;
                reloadQueue(activePage);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Bulk Processing Failed',
                    text: data.message || 'An error occurred while processing bulk request.',
                    confirmButtonColor: '#dc2626',
                    customClass: { popup: 'rounded-4' }
                });
            }
        } catch (error) {
            console.error('Bulk Action Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Request Failed',
                text: 'A connection issue occurred. Please try again.',
                confirmButtonColor: '#dc2626',
                customClass: { popup: 'rounded-4' }
            });
        }
    }
</script>
@endpush