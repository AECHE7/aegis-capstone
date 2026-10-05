@extends('layouts.app')

@section('title', 'Director Analytics | A.E.G.I.S.')
@section('page-title', 'System Analytics')
@section('page-subtitle', 'High-level performance, grade compliance, and UAT metrics for A.E.G.I.S.')

@push('styles')
<style>
    /* Dark stat cards */
    .dark-stat { 
        background: linear-gradient(145deg, #07331c, #1e3932) !important; 
        border-radius: 16px; 
        padding: 1.25rem 1.5rem; 
        color: #ffffff !important; 
        border: 1px solid rgba(255,255,255,0.08); 
        position: relative; 
        overflow: hidden; 
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); 
    }
    .dark-stat:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 12px 28px rgba(7, 51, 28, 0.25); 
    }
    .dark-stat::before { 
        content: ''; 
        position: absolute; 
        top: -40px; 
        right: -40px; 
        width: 120px; 
        height: 120px; 
        border-radius: 50%; 
        background: rgba(255,255,255,0.04); 
    }
    .dark-stat-num { 
        font-family: 'Poppins', sans-serif; 
        font-size: clamp(1.5rem, 4vw + 0.5rem, 2.25rem); 
        font-weight: 800; 
        line-height: 1; 
        color: #ffffff !important; 
    }
    .dark-stat-label { 
        font-size: 0.65rem; 
        font-weight: 700; 
        letter-spacing: 1.2px; 
        text-transform: uppercase; 
        color: rgba(255,255,255,0.75) !important; 
        margin-bottom: 4px; 
    }

    @media (max-width: 480px) {
        .dark-stat { padding: 0.85rem 1rem !important; }
        .dark-stat::before { display: none; }
        .dark-stat i { display: none; }
    }

    /* Chart cards — theme-aware */
    .chart-card { 
        border: 1px solid var(--border-color); 
        border-radius: 16px; 
        background: var(--card-bg); 
        padding: 1.5rem; 
        box-shadow: var(--shadow-card); 
        color: var(--text-main); 
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .chart-card h6, .chart-card .fw-bold { color: var(--text-title) !important; }

    /* UAT Score bars — theme-aware */
    .uat-score-bar { height: 8px; border-radius: 4px; background: var(--clsu-bg); overflow: hidden; }
    .uat-score-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--clsu-green), #22c55e); transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); width: 0; }

    /* Evaluation Table — theme-aware */
    .eval-row { transition: background 0.15s ease; }
    .eval-row:hover { background: var(--clsu-bg); }

    /* Avatar initials — theme-aware */
    .eval-avatar { 
        width: 34px; 
        height: 34px; 
        border-radius: 9px; 
        background: var(--clsu-green-muted); 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-weight: 700; 
        font-size: 0.75rem; 
        color: var(--clsu-green); 
        flex-shrink: 0; 
        border: 1px solid var(--border-color); 
    }

    /* Audit Export Card */
    .export-card { 
        background: linear-gradient(145deg, #07331c, #1e3932) !important; 
        border-radius: 16px; 
        padding: 1.5rem; 
        border: 1px solid rgba(255,255,255,0.08); 
        color: #ffffff !important; 
    }
    .export-card h6 { font-size: 0.65rem; letter-spacing: 1.2px; text-transform: uppercase; color: rgba(255,255,255,0.7) !important; font-weight: 700; margin-bottom: 0.5rem; }
    .export-card h5 { font-size: 1.05rem; font-weight: 700; margin-bottom: 0.25rem; color: #ffffff !important; }
    .export-card p { font-size: 0.75rem; color: rgba(255,255,255,0.75) !important; margin-bottom: 0.75rem; }
    
    .export-btn { 
        display: inline-flex; 
        align-items: center; 
        gap: 6px; 
        padding: 7px 12px; 
        border-radius: 8px; 
        font-size: 0.76rem; 
        font-weight: 600; 
        text-decoration: none; 
        transition: all 0.2s; 
        border: none; 
        cursor: pointer; 
    }
    .export-btn-csv { 
        background: rgba(34,197,94,0.16); 
        color: #4ade80; 
        border: 1px solid rgba(34,197,94,0.3); 
    }
    .export-btn-csv:hover { 
        background: rgba(34,197,94,0.3); 
        color: #86efac; 
    }
    .export-btn-pdf { 
        background: rgba(239,68,68,0.16); 
        color: #f87171; 
        border: 1px solid rgba(239,68,68,0.3); 
    }
    .export-btn-pdf:hover { 
        background: rgba(239,68,68,0.3); 
        color: #fca5a5; 
    }

    /* Date Filter Row & Quick Presets */
    .date-filter-row { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 0.75rem; }
    .date-filter-row label { font-size: 0.68rem; color: rgba(255,255,255,0.7); display: block; margin-bottom: 3px; font-weight: 600; }
    .date-filter-row input { 
        background: rgba(255,255,255,0.08); 
        border: 1px solid rgba(255,255,255,0.16); 
        border-radius: 7px; 
        color: white; 
        padding: 5px 10px; 
        font-size: 0.76rem; 
        outline: none; 
        transition: border-color 0.15s;
    }
    .date-filter-row input:focus {
        border-color: #22c55e;
    }

    .preset-btn {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.14);
        color: rgba(255, 255, 255, 0.85);
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 0.68rem;
        font-weight: 600;
        transition: all 0.15s ease;
        cursor: pointer;
    }
    .preset-btn:hover, .preset-btn.active {
        background: rgba(34, 197, 94, 0.25);
        border-color: #22c55e;
        color: #4ade80;
    }

    /* Tier Tabs for Export Hub */
    .export-tabs .nav-link {
        color: rgba(255, 255, 255, 0.72);
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.72rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .export-tabs .nav-link:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.14);
    }
    .export-tabs .nav-link.active {
        color: #ffffff;
        background: #22c55e;
        border-color: #22c55e;
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }
    .export-group h6.group-label { 
        font-size: 0.75rem; 
        color: rgba(255,255,255,0.7); 
        font-weight: 600; 
        margin-bottom: 0.35rem; 
    }

    /* Search inputs for tables */
    .table-search-input {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        font-size: 0.8rem;
        padding: 5px 12px;
        border-radius: 8px;
        transition: border-color 0.15s;
    }
    .table-search-input:focus {
        border-color: var(--clsu-green);
        box-shadow: 0 0 0 2px rgba(7, 51, 28, 0.1);
        outline: none;
    }
</style>
@endpush

@section('content')

{{-- Filter Action Bar --}}
<div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-filter text-success me-2"></i> Scoped Analytics Filtering</h6>
            <div class="text-muted small">Filter executive metrics across academic terms and scholarship grant programs.</div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if(isset($currentActiveTerm) && $currentActiveTerm)
                <a href="{{ route('superadmin.analytics', ['academic_term_id' => $currentActiveTerm->id]) }}" 
                   class="btn btn-sm {{ $termId == $currentActiveTerm->id ? 'btn-success text-white' : 'btn-outline-success' }} fw-semibold rounded-pill px-3 py-1.5"
                   style="font-size: 0.75rem;"
                   title="Quick-filter dashboard to the current active university semester">
                    <i class="fa-solid fa-bolt me-1"></i> Active: {{ $currentActiveTerm->short_semester }}, AY {{ $currentActiveTerm->academic_year }}
                </a>
            @endif
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-medium" style="font-size: 0.75rem;">
                <i class="fa-solid fa-circle-check me-1"></i> Live System Records
            </span>
        </div>
    </div>
    
    <form action="{{ route('superadmin.analytics') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label fw-semibold text-dark small mb-1">Academic Year / Semester</label>
            <select name="academic_term_id" class="form-select py-2" style="border-radius:10px;">
                <option value="">All Academic Terms</option>
                @foreach($allTerms as $term)
                    <option value="{{ $term->id }}" {{ $termId == $term->id ? 'selected' : '' }}>
                        {{ $term->formatted_semester }} (AY {{ $term->academic_year }}) {{ $term->is_active ? '— [Active]' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label fw-semibold text-dark small mb-1">Scholarship Program</label>
            <select name="scholarship_id" class="form-select py-2" style="border-radius:10px;">
                <option value="">All Scholarship Programs</option>
                @foreach($allScholarships as $prog)
                    <option value="{{ $prog->id }}" {{ $scholarshipId == $prog->id ? 'selected' : '' }}>
                        {{ $prog->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-success py-2 w-100 fw-semibold" style="border-radius: 50px; background-color: #07331c; border-color: #07331c;">
                Apply Filter
            </button>
            @if($termId || $scholarshipId)
                <a href="{{ route('superadmin.analytics') }}" class="btn btn-light py-2 w-100 fw-semibold border border-color" style="border-radius: 50px; color: #475569;">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Active Scoped Badges --}}
    @if($termId || $scholarshipId)
        <div class="d-flex align-items-center gap-2 mt-3 pt-3 border-top flex-wrap">
            <span class="small text-muted fw-bold text-uppercase" style="font-size:0.68rem; letter-spacing:0.5px;">Active Scope:</span>
            @if($termId)
                @php $activeScopedTerm = $allTerms->firstWhere('id', $termId); @endphp
                <span class="badge bg-light text-dark border px-2.5 py-1.5" style="font-size:0.75rem;">
                    <i class="fa-solid fa-calendar-day me-1 text-success"></i>
                    {{ $activeScopedTerm ? $activeScopedTerm->formatted_semester . ' (AY ' . $activeScopedTerm->academic_year . ')' : 'Term #' . $termId }}
                </span>
            @endif
            @if($scholarshipId)
                @php $activeScopedProg = $allScholarships->firstWhere('id', $scholarshipId); @endphp
                <span class="badge bg-light text-dark border px-2.5 py-1.5" style="font-size:0.75rem;">
                    <i class="fa-solid fa-graduation-cap me-1 text-success"></i>
                    {{ $activeScopedProg ? $activeScopedProg->name : 'Program #' . $scholarshipId }}
                </span>
            @endif
            <a href="{{ route('superadmin.analytics') }}" class="small text-danger text-decoration-none ms-auto fw-semibold" style="font-size:0.75rem;">
                <i class="fa-solid fa-xmark me-1"></i>Clear all filters
            </a>
        </div>
    @endif
</div>

{{-- Scoped KPI Cards Row --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="dark-stat">
            <div class="dark-stat-label">Student Scholars</div>
            <div class="dark-stat-num count-up" data-target="{{ $totalStudents }}">0</div>
            <div style="color:rgba(255,255,255,0.6);font-size:0.75rem;margin-top:4px;">Unique active students</div>
            <div style="position:absolute;bottom:12px;right:16px;opacity:0.15;font-size:2rem;">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="dark-stat">
            <div class="dark-stat-label">Submissions</div>
            <div class="dark-stat-num count-up" data-target="{{ $submissionCount }}">0</div>
            <div style="color:rgba(255,255,255,0.6);font-size:0.75rem;margin-top:4px;">Total applications intake</div>
            <div style="position:absolute;bottom:12px;right:16px;opacity:0.15;font-size:2rem;">
                <i class="fa-solid fa-file-lines"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="dark-stat" style="background: linear-gradient(145deg, #1e3932, #0d5c34) !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="dark-stat-label">Grade Integrity Index</div>
                @if($gradeIntegrityIndex === null)
                    <span class="badge bg-white-subtle text-white-50" style="font-size:0.6rem; letter-spacing:0.5px;">BASELINE</span>
                @else
                    <span class="badge bg-success-subtle text-success" style="font-size:0.6rem; letter-spacing:0.5px;">AUDITED</span>
                @endif
            </div>
            <div class="dark-stat-num">
                @if($gradeIntegrityIndex !== null)
                    <span class="count-up" data-target="{{ round($gradeIntegrityIndex) }}">0</span>%
                @else
                    100%
                @endif
            </div>
            <div style="color:rgba(255,255,255,0.6);font-size:0.75rem;margin-top:4px;">
                @if($gradeIntegrityIndex !== null)
                    Avg approved document authenticity
                @else
                    Baseline &middot; Awaiting approved scholars
                @endif
            </div>
            <div style="position:absolute;bottom:12px;right:16px;opacity:0.15;font-size:2rem;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="dark-stat">
            <div class="dark-stat-label">Avg Cycle Time</div>
            <div class="dark-stat-num"><span>{{ $averageCycleDays }}</span>d</div>
            <div style="color:rgba(255,255,255,0.6);font-size:0.75rem;margin-top:4px;">Turnaround: submit to decision</div>
            <div style="position:absolute;bottom:12px;right:16px;opacity:0.15;font-size:2rem;">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>
    </div>
</div>

{{-- Quota Burn KPI Row --}}
@php
    $cappedQuota    = $quotaStats['total_capped_quota'] ?? 0;
    $cappedApproved = $quotaStats['total_capped_approved'] ?? 0;
    $unlimitedProgs = $quotaStats['unlimited_programs_count'] ?? 0;
    $unlimitedApps  = $quotaStats['unlimited_approved_count'] ?? 0;
    $burnPct        = $quotaStats['burn_pct'] ?? 0;
    $quotaBurnColor = $burnPct >= 85 ? '#ef4444' : ($burnPct >= 65 ? '#f59e0b' : '#22c55e');
@endphp
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="dark-stat" style="padding: 1rem 1.5rem;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div class="dark-stat-label mb-1">
                        <i class="fa-solid fa-fire-flame-curved me-1" style="color: {{ $quotaBurnColor }};"></i>
                        Quota Burn Rate — Program Slot Utilization
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="dark-stat-num" style="font-size: 1.75rem;">
                            {{ $cappedApproved }}
                        </span>
                        <span style="color:rgba(255,255,255,0.7); font-size:0.85rem;">
                            / {{ $cappedQuota > 0 ? number_format($cappedQuota) : 'Open' }} quota-governed slots allocated
                        </span>
                    </div>
                    <div style="color:rgba(255,255,255,0.55);font-size:0.75rem;margin-top:2px;">
                        @if($cappedQuota > 0)
                            Across {{ $scholarshipsBreakdown->where('quota', '>', 0)->count() }} capped program(s) &middot; {{ $unlimitedProgs }} open-capacity program(s) ({{ $unlimitedApps }} scholar{{ $unlimitedApps === 1 ? '' : 's' }} enrolled)
                        @else
                            Across {{ $scholarshipsBreakdown->count() }} active scholarship program(s) &middot; Open enrollment capacity
                        @endif
                    </div>
                </div>
                <div style="min-width: 260px; flex: 1;">
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.72rem; color:rgba(255,255,255,0.75);">
                        <span>Capped Slot Burn Progress</span>
                        <span style="color: {{ $quotaBurnColor }}; font-weight: 700;">{{ $burnPct }}%</span>
                    </div>
                    <div style="height:10px; background:rgba(255,255,255,0.1); border-radius:5px; overflow:hidden;">
                        <div style="height:100%; width:{{ $burnPct }}%; background: {{ $quotaBurnColor }}; border-radius:5px; transition: width 1s ease;"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1" style="font-size:0.65rem; color:rgba(255,255,255,0.5);">
                        <span>0 slots</span>
                        <span style="color: {{ $quotaBurnColor }}; font-weight: 600;">
                            @if($cappedQuota > 0)
                                @if($burnPct >= 85) ⚠ High capacity pressure @elseif($burnPct >= 65) Moderate slot consumption @else Normal healthy capacity @endif
                            @else
                                Open Capacity Mode
                            @endif
                        </span>
                        <span>{{ $cappedQuota > 0 ? number_format($cappedQuota) . ' slots' : 'Open' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Visual Chart Grid Row 1 --}}
<div class="row g-4 mb-4">
    {{-- Application Status Distribution --}}
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-chart-bar text-primary me-2"></i> Application Status</h6>
            <p class="text-muted small mb-3">Counts by evaluation lifecycle status.</p>
            <div style="position:relative;height:240px;">
                <canvas id="statusBarChart"></canvas>
            </div>
        </div>
    </div>
    {{-- AI Fraud Risk Tiers --}}
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-chart-pie text-danger me-2"></i> AI Fraud Risk Tiers</h6>
            <p class="text-muted small mb-3">Fraud probability metrics distribution.</p>
            <div style="position:relative;height:240px;display:flex;align-items:center;">
                <canvas id="riskDoughnutChart"></canvas>
            </div>
        </div>
    </div>
    {{-- College Application Distribution --}}
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-building-columns text-success me-2"></i> College Distribution</h6>
            <p class="text-muted small mb-3">Application volume per CLSU college.</p>
            <div style="position:relative;height:240px;display:flex;align-items:center;">
                <canvas id="collegeDoughnutChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Visual Chart Grid Row 2 --}}
<div class="row g-4 mb-4">
    {{-- GWA Distribution Density --}}
    <div class="col-lg-6">
        <div class="chart-card h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-award text-success me-2"></i> Academic GWA Profile Density</h6>
                    <p class="text-muted small mb-0">Brackets comparing overall applicants vs. approved scholars.</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size:0.7rem;" title="Average GWA of all applicants">
                        Applicant Avg: <strong>{{ $avgApplicantGwa ? number_format($avgApplicantGwa, 2) : 'Awaiting Data' }}</strong>
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:0.7rem;" title="Average GWA of approved scholars">
                        Scholar Avg: <strong>{{ $avgApprovedGwa ? number_format($avgApprovedGwa, 2) : 'Awaiting Approval' }}</strong>
                    </span>
                </div>
            </div>
            
            @php
                $hasGwaData = (array_sum($applicantGwaCounts) + array_sum($approvedGwaCounts)) > 0;
            @endphp
            @if(!$hasGwaData)
                <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center p-4 text-center my-auto rounded-3" style="background: rgba(0,0,0,0.02); border: 1px dashed var(--border-color); min-height: 220px;">
                    <i class="fa-solid fa-chart-column fa-2x text-muted mb-2 opacity-40"></i>
                    <div class="fw-semibold text-dark mb-1" style="font-size: 0.85rem;">No Grade Distribution Records In Scope</div>
                    <p class="text-muted small mb-0" style="max-width: 340px; font-size: 0.75rem;">
                        Academic GWA density brackets (1.00–1.25, 1.26–1.50, etc.) will populate dynamically once student transcripts are ingested and verified.
                    </p>
                </div>
                <div style="display:none;">
                    <canvas id="gwaComparisonChart"></canvas>
                </div>
            @else
                <div style="position:relative;height:260px;flex:1;">
                    <canvas id="gwaComparisonChart"></canvas>
                </div>
            @endif
        </div>
    </div>

    {{-- AI Anomaly Indicators --}}
    <div class="col-lg-6">
        <div class="chart-card h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-circle-exclamation text-danger me-2"></i> AI Tampering Indicators</h6>
                    <p class="text-muted small mb-0">Most common document anomalies flagged during verification.</p>
                </div>
                <span class="badge {{ array_sum($anomalyCounts) > 0 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' }} px-2.5 py-1 rounded-pill" style="font-size:0.7rem;">
                    {{ array_sum($anomalyCounts) }} anomaly flag{{ array_sum($anomalyCounts) === 1 ? '' : 's' }}
                </span>
            </div>

            @php
                $hasAnomalies = array_sum($anomalyCounts) > 0;
            @endphp
            @if(!$hasAnomalies)
                <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center p-4 text-center my-auto rounded-3" style="background: rgba(34, 197, 94, 0.04); border: 1px dashed rgba(34, 197, 94, 0.25); min-height: 220px;">
                    <i class="fa-solid fa-shield-check fa-2x text-success mb-2"></i>
                    <div class="fw-bold text-success mb-1" style="font-size: 0.9rem;">100% Document Authenticity</div>
                    <p class="text-muted small mb-0" style="max-width: 340px; font-size: 0.75rem;">
                        Zero tampering flags, digital whiteout boxes, or compression artifacts detected across submitted documents in this scope.
                    </p>
                </div>
                <div style="display:none;">
                    <canvas id="anomalyBarChart"></canvas>
                </div>
            @else
                <div style="position:relative;height:260px;flex:1;">
                    <canvas id="anomalyBarChart"></canvas>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Visual Chart Grid Row 3 --}}
<div class="row g-4 mb-4">
    {{-- Monthly Submission Trend + Processing Speed (Dual-Axis) --}}
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-chart-line text-success me-2"></i> Monthly Trends &amp; Processing Speed</h6>
            <p class="text-muted small mb-3">Application volume (bars) vs. average evaluation turnaround in days (line).</p>
            <div style="position:relative;height:240px;">
                <canvas id="monthlyDualChart"></canvas>
            </div>
        </div>
    </div>
    {{-- Process Audit Timeline --}}
    <div class="col-lg-4">
        <div class="chart-card h-100 d-flex flex-column">
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-timeline text-info me-2"></i> Process Audit Timeline</h6>
            <p class="text-muted small mb-3">Evaluation stage progression funnel.</p>
            <div style="position:relative;height:240px;">
                <canvas id="processFunnelChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Evaluator Audit + Export Row --}}
<div class="row g-4 mb-4">
    {{-- Evaluator Audit Table --}}
    <div class="col-lg-7">
        <div class="card p-4 h-100" style="border-radius:16px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-list-check text-success me-2"></i> Recent Evaluator Decisions
                </h6>
                <span class="badge bg-light text-muted border px-2.5 py-1" style="font-size:0.72rem;">
                    Latest {{ $recentEvaluations->count() }} Decisions
                </span>
            </div>
            @if($recentEvaluations->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th scope="col" style="font-size:0.68rem;letter-spacing:0.5px;text-transform:uppercase;color:#94a3b8;font-weight:700;padding:0.5rem 0.75rem;background:transparent;border:none;">Applicant</th>
                            <th scope="col" style="font-size:0.68rem;letter-spacing:0.5px;text-transform:uppercase;color:#94a3b8;font-weight:700;padding:0.5rem 0.75rem;background:transparent;border:none;">Program</th>
                            <th scope="col" class="text-nowrap" style="font-size:0.68rem;letter-spacing:0.5px;text-transform:uppercase;color:#94a3b8;font-weight:700;padding:0.5rem 0.75rem;background:transparent;border:none;">Decision</th>
                            <th scope="col" class="text-nowrap text-end" style="font-size:0.68rem;letter-spacing:0.5px;text-transform:uppercase;color:#94a3b8;font-weight:700;padding:0.5rem 0.75rem;background:transparent;border:none;">Evaluator</th>
                            <th scope="col" class="text-nowrap text-end" style="font-size:0.68rem;letter-spacing:0.5px;text-transform:uppercase;color:#94a3b8;font-weight:700;padding:0.5rem 0.75rem;background:transparent;border:none;">Form</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEvaluations as $eval)
                        <tr class="eval-row" style="border-color:#f1f5f9;">
                            <td style="padding:0.65rem 0.75rem;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="eval-avatar">{{ strtoupper(substr($eval->user->name ?? 'U', 0, 2)) }}</div>
                                    <div>
                                        <div class="fw-medium text-dark" style="font-size:0.85rem;">{{ $eval->user->name ?? 'Unknown' }}</div>
                                        <div class="text-muted monospace-data" style="font-size:0.7rem;">{{ $eval->user->profile?->clsu_id_number ?? 'Student #' . $eval->user_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:0.65rem 0.75rem;font-size:0.8rem;color:#64748b;">{{ Str::limit($eval->program_name, 22) }}</td>
                            <td class="text-nowrap" style="padding:0.65rem 0.75rem;">
                                @if($eval->status === 'Approved')
                                    <span class="status-badge approved text-nowrap"><i class="fa-solid fa-check" style="font-size:0.6rem;"></i> Approved</span>
                                @else
                                    <span class="status-badge rejected text-nowrap"><i class="fa-solid fa-times" style="font-size:0.6rem;"></i> Rejected</span>
                                @endif
                                <div class="text-muted mt-1" style="font-size:0.65rem;">
                                    {{ $eval->updated_at ? $eval->updated_at->diffForHumans() : '' }}
                                </div>
                            </td>
                            <td class="text-nowrap text-end" style="padding:0.65rem 0.75rem;">
                                @if($eval->evaluator)
                                    <div class="d-inline-flex flex-column align-items-end">
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold" style="font-size:0.72rem;">
                                            <i class="fa-solid fa-user-shield text-success me-1"></i>{{ $eval->evaluator->name }}
                                        </span>
                                        <span class="text-muted" style="font-size:0.65rem;">
                                            {{ ucfirst(str_replace('_', ' ', $eval->evaluator->role ?? 'Staff')) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-nowrap" style="background:#f1f5f9;color:#64748b;border-radius:20px;padding:3px 10px;font-size:0.72rem;font-weight:600;">
                                        <i class="fa-solid fa-user-shield me-1" style="font-size:0.6rem;"></i>Admin #{{ $eval->evaluated_by }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-nowrap text-end" style="padding:0.65rem 0.75rem;">
                                <button type="button" class="btn btn-sm btn-outline-success fw-bold px-2.5 py-1" style="border-radius: 8px; font-size: 0.72rem;" onclick="openApplicantFormModal({{ $eval->id }})" title="View and export authentic applicant form">
                                    <i class="fa-solid fa-file-contract me-1"></i> Form
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="text-center py-5">
                    <i class="fa-solid fa-inbox fa-2x text-muted mb-2 d-block opacity-30"></i>
                    <small class="text-muted">No evaluations yet under this scope.</small>
                </div>
            @endif
        </div>
    </div>
    
    {{-- Audit Log Export Actions Panel --}}
    <div class="col-lg-5">
        <div class="export-card h-100 d-flex flex-column" style="border-radius:16px;">
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6>Compliance Export Hub</h6>
                    <span class="badge bg-success-subtle text-success fw-bold" style="font-size:0.65rem;">ISO / COA AUDIT</span>
                </div>
                <h5>Generate System Audit Logs</h5>
                <p class="mb-2" style="font-size: 0.8rem; opacity: 0.85;">Select date ranges to export evaluations or security traces for administrative and compliance audits.</p>
                
                {{-- Quick Presets --}}
                <div class="d-flex align-items-center gap-1 flex-wrap mb-2">
                    <span style="font-size: 0.68rem; color: rgba(255,255,255,0.6); margin-right: 4px;">Quick:</span>
                    <button type="button" class="preset-btn" onclick="setExportPreset('today')">Today</button>
                    <button type="button" class="preset-btn" onclick="setExportPreset('last7')">Last 7d</button>
                    <button type="button" class="preset-btn" onclick="setExportPreset('last30')">Last 30d</button>
                    <button type="button" class="preset-btn" onclick="setExportPreset('thisyear')">This Year</button>
                    <button type="button" class="preset-btn" onclick="setExportPreset('all')">All Time</button>
                </div>

                <div class="date-filter-row mb-2">
                    <div class="flex-grow-1">
                        <label for="exportDateFrom">From</label>
                        <input type="date" id="exportDateFrom" class="w-100" style="color-scheme: dark;">
                    </div>
                    <div class="flex-grow-1">
                        <label for="exportDateTo">To</label>
                        <input type="date" id="exportDateTo" class="w-100" style="color-scheme: dark;">
                    </div>
                </div>
            </div>

            {{-- Tier Navigation Tabs --}}
            <ul class="nav nav-pills export-tabs gap-1 mb-3" id="exportTierTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tier1-tab" data-bs-toggle="pill" data-bs-target="#exportTier1" type="button" role="tab">
                        Tier 1: Compliance
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tier2-tab" data-bs-toggle="pill" data-bs-target="#exportTier2" type="button" role="tab">
                        Tier 2: Security
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tier3-tab" data-bs-toggle="pill" data-bs-target="#exportTier3" type="button" role="tab">
                        Tier 3: Operations
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tier4-tab" data-bs-toggle="pill" data-bs-target="#exportTier4" type="button" role="tab">
                        Tier 4: Student Logs
                    </button>
                </li>
            </ul>

            {{-- Tab Content --}}
            <div class="tab-content flex-grow-1" id="exportTierTabContent">
                <!-- TIER 1 -->
                <div class="tab-pane fade show active" id="exportTier1" role="tabpanel">
                    <div class="export-group mb-2.5">
                        <h6 class="group-label">Application Status Logs</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.audit.csv') }}" id="auditCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.audit.pdf') }}" id="auditPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                    <div class="export-group mb-2.5">
                        <h6 class="group-label">AI Document Scan Results</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.export.ai-scan.csv') }}" id="aiScanCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.export.ai-scan.pdf') }}" id="aiScanPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                    <div class="export-group">
                        <h6 class="group-label">Admin Evaluation Decisions</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.export.evaluation.csv') }}" id="evalCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.export.evaluation.pdf') }}" id="evalPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- TIER 2 -->
                <div class="tab-pane fade" id="exportTier2" role="tabpanel">
                    <div class="export-group mb-2.5">
                        <h6 class="group-label">Login &amp; Authentication Logs</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.export.auth-log.csv') }}" id="authCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.export.auth-log.pdf') }}" id="authPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                    <div class="export-group mb-2.5">
                        <h6 class="group-label">Admin Action Audit Trail</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.export.admin-action.csv') }}" id="adminCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.export.admin-action.pdf') }}" id="adminPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                    <div class="export-group">
                        <h6 class="group-label">System Settings Changes</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.export.config-change.csv') }}" id="configCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.export.config-change.pdf') }}" id="configPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- TIER 3 -->
                <div class="tab-pane fade" id="exportTier3" role="tabpanel">
                    <div class="export-group mb-2.5">
                        <h6 class="group-label">Email Notification Logs</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.emaillog.csv') }}" id="emailCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.emaillog.pdf') }}" id="emailPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                    <div class="export-group mb-2.5">
                        <h6 class="group-label">Scholarship Program Changes</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.export.scholarship-change.csv') }}" id="scholarshipCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.export.scholarship-change.pdf') }}" id="scholarshipPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                    <div class="export-group">
                        <h6 class="group-label">Data Export Access History</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.export.export-access.csv') }}" id="exportCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.export.export-access.pdf') }}" id="exportPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- TIER 4 -->
                <div class="tab-pane fade" id="exportTier4" role="tabpanel">
                    <div class="export-group mb-2.5">
                        <h6 class="group-label">Student Lifecycle Timeline</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.export.student-timeline.csv') }}" id="timelineCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.export.student-timeline.pdf') }}" id="timelinePdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                    <div class="export-group">
                        <h6 class="group-label">Document Upload History</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.export.doc-upload.csv') }}" id="uploadCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.export.doc-upload.pdf') }}" id="uploadPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scholarship Program Breakdown --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card p-4" style="border-radius:16px;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-square-poll-horizontal text-success me-2"></i> Program Breakdown Snapshot</h6>
                    <div class="text-muted small">Real-time status, quota burn, and fraud indicators across all programs.</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" id="programTableSearch" class="table-search-input" placeholder="🔍 Search programs..." style="width: 220px;">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle" id="programBreakdownTable" style="font-size:0.875rem;">
                    <thead>
                        <tr>
                            <th class="ps-4" scope="col">Scholarship Program</th>
                            <th class="text-center text-nowrap" scope="col">Quota / Slots</th>
                            <th class="text-center text-nowrap" scope="col">Limit/Max</th>
                            <th class="text-center text-nowrap" scope="col">Applicants</th>
                            <th class="text-center text-nowrap" scope="col">Approved</th>
                            <th class="text-center text-nowrap" scope="col">Rejected</th>
                            <th class="text-center text-nowrap" scope="col">Pending / Review</th>
                            <th class="text-center text-nowrap" scope="col">Avg GWA Approved</th>
                            <th class="pe-4 text-end text-nowrap" scope="col">Avg AI Fraud Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scholarshipsBreakdown as $sb)
                        <tr class="program-breakdown-row">
                            <td class="ps-4">
                                <div class="fw-semibold text-dark">{{ $sb['name'] }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">
                                    @if($sb['status'] === 'Active')
                                        <span class="text-success text-nowrap"><i class="fa-solid fa-circle-dot fs-9"></i> Open</span>
                                    @else
                                        <span class="text-danger text-nowrap"><i class="fa-solid fa-circle-dot fs-9"></i> Closed</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center text-nowrap">
                                @if(!empty($sb['quota']))
                                    @php
                                        $fillPct = round(($sb['approved_count'] / $sb['quota']) * 100);
                                        $fillColor = $fillPct >= 100 ? '#ef4444' : ($fillPct >= 75 ? '#f59e0b' : '#22c55e');
                                    @endphp
                                    <div class="d-inline-flex flex-column align-items-center" style="min-width: 100px;">
                                        <span class="badge border px-2.5 py-1 fw-bold" style="background: rgba(34,197,94,0.08); color: #07331c; border-color: rgba(34,197,94,0.2) !important;">
                                            {{ $sb['approved_count'] }} / {{ $sb['quota'] }}
                                        </span>
                                        <div class="w-100 mt-1" style="height: 4px; background: rgba(0,0,0,0.06); border-radius: 2px; overflow: hidden;">
                                            <div style="height: 100%; width: {{ min($fillPct, 100) }}%; background: {{ $fillColor }};"></div>
                                        </div>
                                        @if($sb['approved_count'] >= $sb['quota'])
                                            <span class="badge bg-warning text-dark mt-1" style="font-size:0.62rem;">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i> At Capacity
                                            </span>
                                        @else
                                            <small class="text-muted" style="font-size:0.65rem;">{{ $fillPct }}% filled</small>
                                        @endif
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-1"><i class="fa-solid fa-infinity me-1"></i> Unlimited</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="small">Max GWA: <strong>{{ $sb['min_gwa'] ?: 'None' }}</strong></div>
                                <div class="text-muted small">Max Renewals: <strong>{{ $sb['max_renew'] }}</strong></div>
                            </td>
                            <td class="text-center fw-semibold monospace-data text-nowrap">{{ $sb['total_apps'] }}</td>
                            <td class="text-center text-success fw-bold monospace-data text-nowrap">{{ $sb['approved_count'] }}</td>
                            <td class="text-center text-danger fw-bold monospace-data text-nowrap">{{ $sb['rejected_count'] }}</td>
                            <td class="text-center text-warning fw-semibold monospace-data text-nowrap">{{ $sb['pending_count'] }}</td>
                            <td class="text-center monospace-data text-nowrap">
                                @if($sb['avg_gwa_approved'] > 0)
                                    <span class="badge rounded-pill bg-light text-dark px-2.5 py-1 border border-color">{{ $sb['avg_gwa_approved'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end text-nowrap">
                                @if($sb['total_apps'] > 0)
                                    <span class="fraud-chip monospace-data {{ $sb['avg_fraud'] >= 70 ? 'fraud-high' : ($sb['avg_fraud'] >= 40 ? 'fraud-mod' : 'fraud-low') }}">
                                        {{ $sb['avg_fraud'] }}%
                                    </span>
                                @else
                                    <span class="text-muted small">No data</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr id="noProgramMatchRow" style="display:none;">
                            <td colspan="9" class="text-center py-4 text-muted small">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> No scholarship programs match your search.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Top Performing Programs + UAT Summary Row --}}
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card p-4 h-100" style="border-radius:16px;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-trophy text-warning me-2"></i> Top Performing Programs</h6>
                <span class="badge bg-light text-muted border px-2 py-0.5 rounded-pill" style="font-size:0.7rem;">
                    Ranked by Scholar GWA
                </span>
            </div>
            <p class="text-muted small mb-3">Academic performance ranking of scholarship programs based on approved scholar GWA.</p>
            
            @if($topPrograms->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 align-middle" style="font-size:0.875rem;">
                    <thead>
                        <tr>
                            <th class="ps-2 text-nowrap" scope="col">#</th>
                            <th scope="col">Program</th>
                            <th class="text-center text-nowrap" scope="col">Approved Scholars</th>
                            <th class="text-center text-nowrap" scope="col">Avg GWA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topPrograms as $i => $prog)
                        <tr>
                            <td class="ps-2 fw-bold text-muted small text-nowrap">{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $prog->program_name }}</td>
                            <td class="text-center text-nowrap">{{ $prog->total_apps }}</td>
                            <td class="text-center text-nowrap">
                                <span class="badge rounded-pill bg-light text-dark px-2.5 py-1 border border-color monospace-data fw-bold">
                                    {{ number_format($prog->avg_gwa, 2) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="d-flex flex-column align-items-center justify-content-center text-center py-4 my-auto">
                <div class="eval-avatar mb-3" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(203, 162, 88, 0.1); color: #cba258; font-size: 1.25rem;">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">Awaiting Approved Scholars</h6>
                <p class="text-muted small mb-3" style="max-width: 320px; font-size: 0.78rem;">
                    Scholarship programs will automatically rank here once applicants are approved by evaluators in the current filter scope.
                </p>
                @if(($statusCounts['Under Review'] ?? 0) > 0 || ($statusCounts['Pending'] ?? 0) > 0)
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-success fw-semibold px-3 py-1.5 rounded-pill" style="font-size:0.75rem;">
                        <i class="fa-solid fa-clipboard-check me-1"></i> Review Applications ({{ ($statusCounts['Under Review'] ?? 0) + ($statusCounts['Pending'] ?? 0) }} In Queue)
                    </a>
                @endif
            </div>
            @endif
        </div>
    </div>
    
    <div class="col-lg-5">
        <div class="chart-card h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-star text-warning me-2"></i> System UAT Ratings (ISO 25010)</h6>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                    {{ $uatStats['overall_mean'] }}/5.0
                </span>
            </div>
            <p class="text-muted small mb-2">Overall mean: <strong>{{ $uatStats['overall_mean'] }}/5</strong> &middot; {{ $uatStats['count'] }} responses</p>
            
            <div style="position:relative;height:190px;flex:1;">
                <canvas id="uatRadarChart"></canvas>
            </div>

            {{-- Pillar Numerical Chips --}}
            <div class="row g-1.5 mt-2 pt-2 border-top">
                <div class="col-6 col-sm-3 text-center">
                    <div class="p-1.5 rounded-2" style="background:var(--clsu-bg);">
                        <div class="text-muted" style="font-size:0.62rem; font-weight:700; text-transform:uppercase;">Functional</div>
                        <div class="fw-bold text-dark monospace-data" style="font-size:0.85rem;">{{ $uatStats['avg_fs'] }}<span class="text-muted" style="font-size:0.65rem;">/5</span></div>
                    </div>
                </div>
                <div class="col-6 col-sm-3 text-center">
                    <div class="p-1.5 rounded-2" style="background:var(--clsu-bg);">
                        <div class="text-muted" style="font-size:0.62rem; font-weight:700; text-transform:uppercase;">Usability</div>
                        <div class="fw-bold text-dark monospace-data" style="font-size:0.85rem;">{{ $uatStats['avg_us'] }}<span class="text-muted" style="font-size:0.65rem;">/5</span></div>
                    </div>
                </div>
                <div class="col-6 col-sm-3 text-center">
                    <div class="p-1.5 rounded-2" style="background:var(--clsu-bg);">
                        <div class="text-muted" style="font-size:0.62rem; font-weight:700; text-transform:uppercase;">Reliability</div>
                        <div class="fw-bold text-dark monospace-data" style="font-size:0.85rem;">{{ $uatStats['avg_rl'] }}<span class="text-muted" style="font-size:0.65rem;">/5</span></div>
                    </div>
                </div>
                <div class="col-6 col-sm-3 text-center">
                    <div class="p-1.5 rounded-2" style="background:var(--clsu-bg);">
                        <div class="text-muted" style="font-size:0.62rem; font-weight:700; text-transform:uppercase;">Security</div>
                        <div class="fw-bold text-dark monospace-data" style="font-size:0.85rem;">{{ $uatStats['avg_sc'] }}<span class="text-muted" style="font-size:0.65rem;">/5</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Active Scholars System-wide Monitoring Panel --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card p-4" style="border-radius:16px;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-check text-success me-2"></i> System Scholars Monitoring Hub</h6>
                    <div class="text-muted small">Roster of all currently enrolled, active approved scholars with real-time GWA compliance tracking.</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" id="scholarTableSearch" class="table-search-input" placeholder="🔍 Search scholars..." style="width: 240px;">
                    <span class="badge bg-success text-white px-2.5 py-1.5 rounded-pill fw-semibold small">
                        Total Scholars: {{ $activeScholars->count() }}
                    </span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle" id="scholarsHubTable" style="font-size:0.875rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color); font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--text-main);">
                            <th class="ps-4 text-nowrap" scope="col">Scholar Name</th>
                            <th scope="col">Scholarship Program</th>
                            <th scope="col" class="text-nowrap">Active Term</th>
                            <th class="text-center text-nowrap" scope="col">Min GWA Required</th>
                            <th class="text-center text-nowrap" scope="col">Current Student GWA</th>
                            <th class="text-center text-nowrap" scope="col">Status</th>
                            <th class="pe-4 text-end text-nowrap" scope="col">Form / Export</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeScholars as $scholar)
                        <tr class="scholar-item-row" style="border-bottom: 1px solid var(--border-color);">
                            <td class="ps-4 py-3 text-nowrap">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="eval-avatar" style="background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #15803d; width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($scholar->user->name ?? 'U', 0, 1)) }}
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
                            <td class="text-nowrap">
                                <span class="text-muted small">
                                    @if($scholar->academicTerm)
                                        {{ $scholar->academicTerm->short_semester }}, AY {{ $scholar->academicTerm->academic_year }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="monospace-data text-muted">{{ $scholar->scholarship->min_gwa_required ?? 'N/A' }}</span>
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold monospace-data" 
                                      style="background: #f0fdf4; color: var(--clsu-green); border: 1px solid #bcf0da; font-size: 0.78rem;">
                                    {{ $scholar->gwa !== null ? number_format($scholar->gwa, 2) : 'N/A' }}
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                @php
                                    $isGwaValid = !$scholar->scholarship || !$scholar->scholarship->min_gwa_required || ($scholar->gwa <= $scholar->scholarship->min_gwa_required);
                                @endphp
                                @if($isGwaValid)
                                    <span class="badge bg-success px-2 py-1 rounded-pill" style="font-size: 0.68rem;"><i class="fa-solid fa-circle-check me-1"></i> Compliant</span>
                                @else
                                    <span class="badge bg-danger px-2 py-1 rounded-pill" style="font-size: 0.68rem;" title="Student's GWA exceeds the maximum allowed limit for this scholarship program"><i class="fa-solid fa-circle-exclamation me-1"></i> GWA Violation</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-success fw-bold px-2.5 py-1" style="border-radius: 8px; font-size: 0.72rem;" onclick="openApplicantFormModal({{ $scholar->id }})" title="View authentic approved application form and export PDF">
                                    <i class="fa-solid fa-file-contract me-1"></i> Form
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="eval-avatar mx-auto mb-3" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(0,0,0,0.04); color: #94a3b8; font-size: 1.1rem;">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                                <h6 class="fw-semibold text-dark mb-1" style="font-size:0.875rem;">No Active Scholars in Current Scope</h6>
                                <p class="text-muted small mb-0" style="max-width: 360px; margin: 0 auto; font-size: 0.75rem;">
                                    Once applications are marked <span class="text-success fw-semibold">Approved</span> by evaluators, students are officially registered into this monitoring hub with automated grade verification.
                                </p>
                            </td>
                        </tr>
                        @endforelse
                        <tr id="noScholarMatchRow" style="display:none;">
                            <td colspan="7" class="text-center py-4 text-muted small">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> No active scholars match your search query.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('components.applicant-form-modal')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ── Count-up Animation ─────────────────────────────
    document.querySelectorAll('.count-up').forEach(el => {
        const target = parseInt(el.dataset.target);
        if (target === 0 || isNaN(target)) { el.textContent = '0'; return; }
        let start = 0;
        const step = Math.ceil(target / 30);
        const timer = setInterval(() => {
            start = Math.min(start + step, target);
            el.textContent = start;
            if (start >= target) clearInterval(timer);
        }, 30);
    });

    // ── UAT Score Bars Animation ───────────────────────
    document.querySelectorAll('.uat-score-fill').forEach(el => {
        setTimeout(() => { el.style.width = el.dataset.width + '%'; }, 300);
    });

    // ── 1. STATUS BAR CHART ───────────────────────────
    const statusCtx = document.getElementById('statusBarChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($statusCounts)),
                datasets: [{
                    label: 'Applications',
                    data: @json(array_values($statusCounts)),
                    backgroundColor: ['#f59e0b','#0ea5e9','#22c55e','#ef4444'],
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { ticks: { font: { size: 11 } }, grid: { display: false } }
                }
            }
        });
    }

    // ── 2. RISK DOUGHNUT ─────────────────────────────
    const riskCtx = document.getElementById('riskDoughnutChart');
    if (riskCtx) {
        new Chart(riskCtx, {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($riskTiers)),
                datasets: [{
                    data: @json(array_values($riskTiers)),
                    backgroundColor: ['#22c55e','#f59e0b','#ef4444'],
                    borderWidth: 3, borderColor: '#fff', hoverOffset: 10
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { font: { size: 9 }, boxWidth: 10, padding: 8 } } },
                cutout: '65%'
            }
        });
    }

    // ── 3. COLLEGE DISTRIBUTION DOUGHNUT ──────────────
    const collegeCtx = document.getElementById('collegeDoughnutChart');
    if (collegeCtx) {
        new Chart(collegeCtx, {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($collegeStats)),
                datasets: [{
                    data: @json(array_values($collegeStats)),
                    backgroundColor: ['#07331c', '#006241', '#1e3932', '#cba258', '#475569', '#cbd5e1'],
                    borderWidth: 2, borderColor: '#fff'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { font: { size: 9 }, boxWidth: 10, padding: 8 } } },
                cutout: '60%'
            }
        });
    }

    // ── 4. GWA DISTRIBUTION BRACKETS CHART ────────────
    const gwaCtx = document.getElementById('gwaComparisonChart');
    if (gwaCtx) {
        new Chart(gwaCtx, {
            type: 'bar',
            data: {
                labels: ['1.00 - 1.25', '1.26 - 1.50', '1.51 - 1.75', '1.76 - 2.00', '> 2.00'],
                datasets: [
                    {
                        label: 'Applicants',
                        data: @json(array_values($applicantGwaCounts)),
                        backgroundColor: 'rgba(7, 51, 28, 0.45)',
                        borderColor: '#07331c',
                        borderWidth: 1.5,
                        borderRadius: 6
                    },
                    {
                        label: 'Approved Scholars',
                        data: @json(array_values($approvedGwaCounts)),
                        backgroundColor: '#006241',
                        borderColor: '#006241',
                        borderWidth: 1.5,
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { font: { size: 10 } } } },
                scales: {
                    y: { beginAtZero: true, ticks: { font: { size: 10 }, stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                }
            }
        });
    }

    // ── 5. AI ANOMALY HORIZONTAL BAR CHART ────────────
    const anomalyCtx = document.getElementById('anomalyBarChart');
    if (anomalyCtx) {
        new Chart(anomalyCtx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($anomalyCounts)),
                datasets: [{
                    label: 'Occurrences',
                    data: @json(array_values($anomalyCounts)),
                    backgroundColor: 'rgba(239, 68, 68, 0.85)',
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { font: { size: 10 }, stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    y: { ticks: { font: { size: 10 } }, grid: { display: false } }
                }
            }
        });
    }

    // ── 6. DUAL-AXIS MONTHLY CHART (Volume + Processing Speed) ──
    const monthlyCtx = document.getElementById('monthlyDualChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($monthlyTrend)),
                datasets: [
                    {
                        label: 'Applications',
                        data: @json(array_values($monthlyTrend)),
                        backgroundColor: 'rgba(7,51,28,0.55)',
                        borderColor: '#07331c',
                        borderWidth: 1,
                        borderRadius: 4,
                        order: 2,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Avg Days to Decision',
                        data: @json(array_values($monthlyProcessingDays)),
                        type: 'line',
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.1)',
                        pointBackgroundColor: '#f59e0b',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.45,
                        borderWidth: 2,
                        order: 1,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 9 }, boxWidth: 12, padding: 8 } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 10 } },
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        title: { display: true, text: 'Applications', font: { size: 10 } }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        ticks: { font: { size: 10 } },
                        grid: { display: false },
                        title: { display: true, text: 'Days', font: { size: 10 } }
                    },
                    x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                }
            }
        });
    }

    // ── 7. PROCESS AUDIT FUNNEL CHART ──────────────────
    const processCtx = document.getElementById('processFunnelChart');
    if (processCtx) {
        new Chart(processCtx, {
            type: 'bar',
            data: {
                labels: ['Total Submitted', 'Pending', 'Under Review', 'Approved', 'Rejected'],
                datasets: [{
                    label: 'Applications',
                    data: [
                        {{ $processTimeline['total'] }},
                        {{ $processTimeline['pending'] }},
                        {{ $processTimeline['under_review'] }},
                        {{ $processTimeline['approved'] }},
                        {{ $processTimeline['rejected'] }}
                    ],
                    backgroundColor: ['#94a3b8', '#f59e0b', '#0ea5e9', '#22c55e', '#ef4444'],
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    y: { ticks: { font: { size: 10 } }, grid: { display: false } }
                }
            }
        });
    }

    // ── 8. UAT RADAR CHART ────────────────────────────
    const uatCtx = document.getElementById('uatRadarChart');
    if (uatCtx) {
        new Chart(uatCtx, {
            type: 'radar',
            data: {
                labels: ['Functional', 'Usability', 'Reliability', 'Security'],
                datasets: [{
                    label: 'Average Score',
                    data: [
                        {{ $uatStats['avg_fs'] }},
                        {{ $uatStats['avg_us'] }},
                        {{ $uatStats['avg_rl'] }},
                        {{ $uatStats['avg_sc'] }}
                    ],
                    backgroundColor: 'rgba(7, 51, 28, 0.15)',
                    borderColor: 'rgba(7, 51, 28, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(7, 51, 28, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(7, 51, 28, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    r: {
                        angleLines: { color: 'rgba(0, 0, 0, 0.08)' },
                        grid: { color: 'rgba(0, 0, 0, 0.08)' },
                        pointLabels: { font: { size: 9, weight: '600' } },
                        suggestedMin: 0,
                        suggestedMax: 5,
                        ticks: { stepSize: 1, display: false }
                    }
                }
            }
        });
    }

    // ── 9. INITIALIZE BOOTSTRAP TOOLTIPS ──────────────
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // ── 10. DATE FILTER & QUICK PRESETS WIRING ─────────
    function updateExportHrefs() {
        const from = document.getElementById('exportDateFrom')?.value ?? '';
        const to   = document.getElementById('exportDateTo')?.value ?? '';

        [
            'auditCsvBtn', 'auditPdfBtn',
            'emailCsvBtn', 'emailPdfBtn',
            'aiScanCsvBtn', 'aiScanPdfBtn',
            'evalCsvBtn', 'evalPdfBtn',
            'authCsvBtn', 'authPdfBtn',
            'adminCsvBtn', 'adminPdfBtn',
            'configCsvBtn', 'configPdfBtn',
            'scholarshipCsvBtn', 'scholarshipPdfBtn',
            'exportCsvBtn', 'exportPdfBtn',
            'timelineCsvBtn', 'timelinePdfBtn',
            'uploadCsvBtn', 'uploadPdfBtn'
        ].forEach(id => {
            const btn = document.getElementById(id);
            if (!btn) return;
            const base = btn.dataset.base || btn.href.split('?')[0];
            btn.dataset.base = base;
            const params = new URLSearchParams();
            if (from) params.set('date_from', from);
            if (to)   params.set('date_to',   to);
            btn.href = params.toString() ? base + '?' + params.toString() : base;
        });
    }

    function setExportPreset(type) {
        const fromInput = document.getElementById('exportDateFrom');
        const toInput   = document.getElementById('exportDateTo');
        if (!fromInput || !toInput) return;

        const today = new Date();
        const formatDate = (d) => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
        if (window.event && window.event.target && window.event.target.classList.contains('preset-btn')) {
            window.event.target.classList.add('active');
        }

        if (type === 'today') {
            fromInput.value = formatDate(today);
            toInput.value   = formatDate(today);
        } else if (type === 'last7') {
            const d = new Date();
            d.setDate(d.getDate() - 7);
            fromInput.value = formatDate(d);
            toInput.value   = formatDate(today);
        } else if (type === 'last30') {
            const d = new Date();
            d.setDate(d.getDate() - 30);
            fromInput.value = formatDate(d);
            toInput.value   = formatDate(today);
        } else if (type === 'thisyear') {
            const d = new Date(today.getFullYear(), 0, 1);
            fromInput.value = formatDate(d);
            toInput.value   = formatDate(today);
        } else if (type === 'all') {
            fromInput.value = '';
            toInput.value   = '';
        }
        updateExportHrefs();
    }

    document.getElementById('exportDateFrom')?.addEventListener('change', updateExportHrefs);
    document.getElementById('exportDateTo')?.addEventListener('change', updateExportHrefs);

    // ── 11. TABLE IN-PAGE LIVE SEARCH ──────────────────
    // Program Breakdown Search
    document.getElementById('programTableSearch')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#programBreakdownTable tbody tr.program-breakdown-row');
        let visibleCount = 0;
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        const noMatchRow = document.getElementById('noProgramMatchRow');
        if (noMatchRow) {
            noMatchRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }
    });

    // Scholars Monitoring Hub Search
    document.getElementById('scholarTableSearch')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#scholarsHubTable tbody tr.scholar-item-row');
        let visibleCount = 0;
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        const noMatchRow = document.getElementById('noScholarMatchRow');
        if (noMatchRow) {
            noMatchRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }
    });
</script>

@endpush