@extends('layouts.app')

@section('title', 'Director Analytics | A.E.G.I.S.')
@section('page-title', 'System Analytics')
@section('page-subtitle', 'High-level performance and UAT metrics for A.E.G.I.S.')

@push('styles')
<style>
    /* Dark stat cards */
    .dark-stat { background: linear-gradient(145deg, #0f172a, #1e293b); border-radius: 16px; padding: 1.25rem 1.5rem; color: white; border: 1px solid rgba(255,255,255,0.06); position: relative; overflow: hidden; transition: all 0.25s; }
    .dark-stat:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(0,0,0,0.2); }
    .dark-stat::before { content: ''; position: absolute; top: -40px; right: -40px; width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.03); }
    .dark-stat-num { font-family: 'Poppins', sans-serif; font-size: 2.25rem; font-weight: 800; line-height: 1; }
    .dark-stat-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: rgba(255,255,255,0.4); margin-bottom: 4px; }

    /* Chart cards */
    .chart-card { border: 1px solid #e2e8f0; border-radius: 16px; background: white; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }

    /* UAT Score bars */
    .uat-score-bar { height: 8px; border-radius: 4px; background: #f1f5f9; overflow: hidden; }
    .uat-score-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--clsu-green), #22c55e); transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); width: 0; }

    /* Evaluation Table */
    .eval-row { transition: background 0.15s; }
    .eval-row:hover { background: #f8fafc; }

    /* Avatar initials */
    .eval-avatar { width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, #e0f2fe, #bae6fd); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; color: #0369a1; flex-shrink: 0; }

    /* Audit Export Card */
    .export-card { background: linear-gradient(145deg, #0f172a, #1e293b); border-radius: 16px; padding: 1.5rem; border: 1px solid rgba(255,255,255,0.07); color: white; }
    .export-card h6 { font-size: 0.65rem; letter-spacing: 1.2px; text-transform: uppercase; color: rgba(255,255,255,0.4); font-weight: 700; margin-bottom: 0.75rem; }
    .export-card h5 { font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem; }
    .export-card p { font-size: 0.75rem; color: rgba(255,255,255,0.45); margin-bottom: 1.25rem; }
    .export-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
    .export-btn-csv  { background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.25); }
    .export-btn-csv:hover  { background: rgba(34,197,94,0.28); color: #86efac; }
    .export-btn-pdf  { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.25); }
    .export-btn-pdf:hover  { background: rgba(239,68,68,0.28); color: #fca5a5; }
    .date-filter-row { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .date-filter-row label { font-size: 0.7rem; color: rgba(255,255,255,0.45); display: block; margin-bottom: 3px; }
    .date-filter-row input { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 7px; color: white; padding: 5px 10px; font-size: 0.78rem; outline: none; }
    .export-group h6.group-label { font-size: 0.72rem; color: rgba(255,255,255,0.55); font-weight: 600; margin-bottom: 0.5rem; }
</style>
@endpush

@section('content')

{{-- Dark Stat Cards Row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="dark-stat">
            <div class="dark-stat-label">Total Students</div>
            <div class="dark-stat-num count-up" data-target="{{ $totalStudents }}">0</div>
            <div style="color:rgba(255,255,255,0.35);font-size:0.75rem;margin-top:4px;">Registered in portal</div>
            <div class="dark-stat-icon" style="position:absolute;bottom:12px;right:16px;opacity:0.15;font-size:2rem;">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="dark-stat">
            <div class="dark-stat-label">Applications</div>
            <div class="dark-stat-num count-up" data-target="{{ $submissionCount }}">0</div>
            <div style="color:rgba(255,255,255,0.35);font-size:0.75rem;margin-top:4px;">Total submissions</div>
            <div style="position:absolute;bottom:12px;right:16px;opacity:0.15;font-size:2rem;">
                <i class="fa-solid fa-file-lines"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="dark-stat">
            <div class="dark-stat-label">Active Programs</div>
            <div class="dark-stat-num count-up" data-target="{{ $totalScholarships }}">0</div>
            <div style="color:rgba(255,255,255,0.35);font-size:0.75rem;margin-top:4px;">Scholarship grants</div>
            <div style="position:absolute;bottom:12px;right:16px;opacity:0.15;font-size:2rem;">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="dark-stat">
            <div class="dark-stat-label">Anomalies Flagged</div>
            <div class="dark-stat-num count-up" data-target="{{ $anomaliesDetected }}" style="color: #f87171;">0</div>
            <div style="color:rgba(255,255,255,0.35);font-size:0.75rem;margin-top:4px;">Rejected by AI review</div>
            <div style="position:absolute;bottom:12px;right:16px;opacity:0.15;font-size:2rem;">
                <i class="fa-solid fa-shield-virus"></i>
            </div>
        </div>
</div>

{{-- Financial & Budget Tracker Panel --}}
<div class="card p-4 border-0 shadow-sm mb-4" style="border-radius:16px;">
    <h5 class="fw-bold text-dark mb-1"><i class="fa-solid fa-calculator text-success me-2"></i> Financial & Budget Tracker</h5>
    <p class="text-muted small mb-4">Real-time monitoring of fund allocation and stipend disbursements based on approved scholars per scholarship program.</p>
    
    @php
        $utilizationRate = $totalBudget > 0 ? min(100, ($disbursed / $totalBudget) * 100) : 0;
        $activeScholarsCount = $statusCounts['Approved'] ?? 0;
    @endphp

    <div class="row g-3">
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-3" style="border-left: 4px solid #0C4E2D;">
                <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 0.8px;">Total Allocated Budget</div>
                <div class="fw-bold text-dark fs-4 monospace-data">Php {{ number_format($totalBudget, 2) }}</div>
                <small class="text-muted">Configurable in System Settings</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-3" style="border-left: 4px solid #D97706;">
                <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 0.8px;">Disbursed Stipends</div>
                <div class="fw-bold text-dark fs-4 monospace-data" style="color: #0C4E2D !important;">
                    Php {{ number_format($disbursed, 2) }}
                </div>
                <small class="text-muted">Across {{ $activeScholarsCount }} Approved Scholars</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-3" style="border-left: 4px solid #b91c1c;">
                <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 0.8px;">Remaining Balance</div>
                <div class="fw-bold text-dark fs-4 monospace-data">
                    Php {{ number_format($remaining, 2) }}
                </div>
                <small class="text-muted">Unallocated Funds</small>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small fw-semibold text-dark">Budget Utilization Rate</span>
            <span class="fw-bold text-dark small">{{ number_format($utilizationRate, 1) }}%</span>
        </div>
        <div class="progress" style="height: 10px; border-radius: 50px; background-color: #e2e8f0; overflow: hidden;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                 style="width: {{ $utilizationRate }}%; background: linear-gradient(90deg, #0C4E2D, #16a34a); border-radius: 50px;" 
                 aria-valuenow="{{ $utilizationRate }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>

{{-- Chart Row --}}
<div class="row g-4 mb-4">
    {{-- Bar Chart --}}
    <div class="col-lg-5">
        <div class="chart-card h-100">
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-chart-bar text-primary me-2"></i> Application Status Distribution</h6>
            <p class="text-muted small mb-3">Applications by current status.</p>
            <div style="position:relative;height:240px;">
                <canvas id="statusBarChart"></canvas>
            </div>
        </div>
    </div>
    {{-- Doughnut --}}
    <div class="col-lg-3">
        <div class="chart-card h-100">
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-chart-pie text-danger me-2"></i> AI Fraud Risk Tiers</h6>
            <p class="text-muted small mb-3">Based on fraud probability score.</p>
            <div style="position:relative;height:240px;display:flex;align-items:center;">
                <canvas id="riskDoughnutChart"></canvas>
            </div>
        </div>
    </div>
    {{-- Line Chart --}}
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-chart-line text-success me-2"></i> Monthly Submission Trend</h6>
            <p class="text-muted small mb-3">Applications over the past 6 months.</p>
            <div style="position:relative;height:240px;">
                <canvas id="monthlyLineChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- UAT Summary + Eval Audit Row --}}
<div class="row g-4">

    {{-- UAT Scores Card --}}
    <div class="col-lg-6">
        <div class="card p-4 h-100" style="border-radius:16px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-star text-warning me-2"></i> UAT Ratings (ISO/IEC 25010)</h6>
                    <small class="text-muted">Total Responses: <strong>{{ $uatStats['count'] }}</strong></small>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @php
                        $mean = $uatStats['overall_mean'];
                        if ($mean >= 4.5) {
                            $badgeClass = 'bg-success text-white';
                            $badgeText = 'Outstanding';
                        } elseif ($mean >= 4.0) {
                            $badgeClass = 'bg-info text-dark';
                            $badgeText = 'Very Good';
                        } elseif ($mean >= 3.5) {
                            $badgeClass = 'bg-warning text-dark';
                            $badgeText = 'Satisfactory';
                        } else {
                            $badgeClass = 'bg-danger text-white';
                            $badgeText = 'Needs Improvement';
                        }
                    @endphp
                    <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">
                        {{ $badgeText }}
                    </span>
                    <div style="background:linear-gradient(135deg,var(--clsu-green),#16703f);color:white;border-radius:12px;padding:8px 14px;text-align:center;">
                        <div style="font-family:'Poppins',sans-serif;font-size:1.5rem;font-weight:800;line-height:1;">{{ $uatStats['overall_mean'] }}</div>
                        <div style="font-size:0.65rem;opacity:0.8;letter-spacing:0.5px;">Overall Mean Score</div>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-7">
                    @foreach([
                        ['label' => 'Functional Suitability', 'icon' => 'fa-gear', 'color' => '#0284c7', 'score' => $uatStats['avg_fs'], 'desc' => 'Degree to which functions meet stated and implied needs.'],
                        ['label' => 'Usability', 'icon' => 'fa-hand-pointer', 'color' => '#7c3aed', 'score' => $uatStats['avg_us'], 'desc' => 'Ease of use, learning, and overall user interface satisfaction.'],
                        ['label' => 'Reliability', 'icon' => 'fa-server', 'color' => '#0F5934', 'score' => $uatStats['avg_rl'], 'desc' => 'System uptime, error-free operations, and pipeline stability.'],
                        ['label' => 'Security', 'icon' => 'fa-shield-halved', 'color' => '#d97706', 'score' => $uatStats['avg_sc'], 'desc' => 'Data encryption, access control, and IDOR protection compliance.'],
                    ] as $metric)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid {{ $metric['icon'] }} small" style="color: {{ $metric['color'] }};"></i>
                                <span class="small fw-semibold text-dark">{{ $metric['label'] }}</span>
                                <i class="fa-solid fa-circle-info text-muted ms-1" style="font-size:0.7rem; cursor:help;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $metric['desc'] }}"></i>
                            </div>
                            <span class="fw-bold small" style="color: {{ $metric['color'] }};">{{ $metric['score'] }}/5</span>
                        </div>
                        <div class="uat-score-bar">
                            <div class="uat-score-fill" data-width="{{ ($metric['score'] / 5) * 100 }}" style="background: linear-gradient(90deg, {{ $metric['color'] }}, {{ $metric['color'] }}99); width: 0%;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="col-md-5">
                    <div style="position:relative; height: 210px; width: 100%;">
                        <canvas id="uatRadarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Evaluator Audit Table --}}
    <div class="col-lg-6">
        <div class="card p-4 h-100" style="border-radius:16px;">
            <h6 class="fw-bold text-dark mb-3">
                <i class="fa-solid fa-list-check text-primary me-2"></i> Recent Evaluator Decisions
            </h6>
            @if($recentEvaluations->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="font-size:0.68rem;letter-spacing:0.5px;text-transform:uppercase;color:#94a3b8;font-weight:700;padding:0.5rem 0.75rem;background:transparent;border:none;">Applicant</th>
                            <th style="font-size:0.68rem;letter-spacing:0.5px;text-transform:uppercase;color:#94a3b8;font-weight:700;padding:0.5rem 0.75rem;background:transparent;border:none;">Program</th>
                            <th style="font-size:0.68rem;letter-spacing:0.5px;text-transform:uppercase;color:#94a3b8;font-weight:700;padding:0.5rem 0.75rem;background:transparent;border:none;">Decision</th>
                            <th style="font-size:0.68rem;letter-spacing:0.5px;text-transform:uppercase;color:#94a3b8;font-weight:700;padding:0.5rem 0.75rem;background:transparent;border:none;text-align:right;">Evaluator</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEvaluations as $eval)
                        <tr class="eval-row" style="border-color:#f1f5f9;">
                            <td style="padding:0.65rem 0.75rem;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="eval-avatar">{{ strtoupper(substr($eval->user->name ?? 'U', 0, 2)) }}</div>
                                    <span class="fw-medium" style="font-size:0.85rem;">{{ $eval->user->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td style="padding:0.65rem 0.75rem;font-size:0.8rem;color:#64748b;">{{ Str::limit($eval->program_name, 22) }}</td>
                            <td style="padding:0.65rem 0.75rem;">
                                @if($eval->status === 'Approved')
                                    <span class="status-badge approved"><i class="fa-solid fa-check" style="font-size:0.6rem;"></i> Approved</span>
                                @else
                                    <span class="status-badge rejected"><i class="fa-solid fa-times" style="font-size:0.6rem;"></i> Rejected</span>
                                @endif
                            </td>
                            <td style="padding:0.65rem 0.75rem;text-align:right;">
                                <span style="background:#f1f5f9;color:#64748b;border-radius:20px;padding:3px 10px;font-size:0.72rem;font-weight:600;">
                                    <i class="fa-solid fa-user-shield me-1" style="font-size:0.6rem;"></i>Admin #{{ $eval->evaluated_by }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="text-center py-5">
                    <i class="fa-solid fa-inbox fa-2x text-muted mb-2 d-block opacity-30"></i>
                    <small class="text-muted">No evaluations yet.</small>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Phase 40: Per-Scholarship Program Breakdown Stats --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card p-4" style="border-radius:16px;">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-square-poll-horizontal text-primary me-2"></i> Program Breakdown Snapshot</h6>
            <div class="table-responsive">
                <table class="table mb-0 align-middle" style="font-size:0.875rem;">
                    <thead>
                        <tr>
                            <th class="ps-4">Scholarship Program</th>
                            <th class="text-center">Limit/Max</th>
                            <th class="text-center">Applicants</th>
                            <th class="text-center">Approved</th>
                            <th class="text-center">Rejected</th>
                            <th class="text-center">Pending / Review</th>
                            <th class="text-center">Avg GWA Approved</th>
                            <th class="pe-4 text-end">Avg AI Fraud Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scholarshipsBreakdown as $sb)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold text-dark">{{ $sb['name'] }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">
                                    @if($sb['status'] === 'Active')
                                        <span class="text-success"><i class="fa-solid fa-circle-dot fs-9"></i> Open</span>
                                    @else
                                        <span class="text-danger"><i class="fa-solid fa-circle-dot fs-9"></i> Closed</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="small">Max GWA: <strong>≤ {{ $sb['min_gwa'] }}</strong></div>
                                <div class="text-muted small">Max Renewals: <strong>{{ $sb['max_renew'] }}</strong></div>
                            </td>
                            <td class="text-center fw-semibold monospace-data">{{ $sb['total_apps'] }}</td>
                            <td class="text-center text-success fw-bold monospace-data">{{ $sb['approved_count'] }}</td>
                            <td class="text-center text-danger fw-bold monospace-data">{{ $sb['rejected_count'] }}</td>
                            <td class="text-center text-warning fw-semibold monospace-data">{{ $sb['pending_count'] }}</td>
                            <td class="text-center monospace-data">
                                @if($sb['avg_gwa_approved'] > 0)
                                    <span class="badge rounded-pill bg-light text-dark px-2.5 py-1 border border-color">{{ $sb['avg_gwa_approved'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Active Scholars System-wide Monitoring Panel --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card p-4" style="border-radius:16px;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-check text-success me-2"></i> System Scholars Monitoring Hub</h6>
                <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-semibold small">
                    Total Scholars: {{ $activeScholars->count() }}
                </span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle" style="font-size:0.875rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color); font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--text-main);">
                            <th class="ps-4">Scholar Name</th>
                            <th>Scholarship Program</th>
                            <th>Active Term</th>
                            <th class="text-center">Min GWA Required</th>
                            <th class="text-center">Current Student GWA</th>
                            <th class="text-center">Active Stipend</th>
                            <th class="pe-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeScholars as $scholar)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td class="ps-4 py-3">
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
                                <span class="fw-bold text-dark monospace-data">Php {{ number_format($scholar->scholarship->stipend_amount ?? 0, 2) }}</span>
                            </td>
                            <td class="pe-4 text-center">
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
                            <td colspan="7" class="text-center py-4 text-muted small">
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

{{-- Phase 33: Audit History Log Exports Card --}}
<div class="row g-3 mt-2 mb-4">
    <div class="col-12">
        <div class="export-card">
            <h6><i class="fa-solid fa-shield-halved me-1"></i> Audit History Log Exports</h6>
            <h5>Download Compliance Reports</h5>
            <p>Export the full application status audit trail or email dispatch history as a structured CSV spreadsheet or branded PDF document. Use the date filter to scope the export window.</p>

            {{-- Date Range Filter --}}
            <div class="date-filter-row" id="auditExportFilters">
                <div>
                    <label>Date From</label>
                    <input type="date" id="exportDateFrom" placeholder="YYYY-MM-DD">
                </div>
                <div>
                    <label>Date To</label>
                    <input type="date" id="exportDateTo" placeholder="YYYY-MM-DD">
                </div>
            </div>

            {{-- Export Buttons --}}
            <div class="d-flex flex-wrap gap-3">
                <div class="export-group">
                    <h6 class="group-label"><i class="fa-solid fa-clock-rotate-left me-1"></i> Status Audit Trail</h6>
                    <div class="d-flex gap-2">
                        <a id="auditCsvBtn" href="{{ route('superadmin.audit.csv') }}" class="export-btn export-btn-csv" target="_blank">
                            <i class="fa-solid fa-file-csv"></i> Download CSV
                        </a>
                        <a id="auditPdfBtn" href="{{ route('superadmin.audit.pdf') }}" class="export-btn export-btn-pdf" target="_blank">
                            <i class="fa-solid fa-file-pdf"></i> Download PDF
                        </a>
                    </div>
                </div>
                <div class="export-group" style="margin-left:1rem;padding-left:1rem;border-left:1px solid rgba(255,255,255,0.08);">
                    <h6 class="group-label"><i class="fa-solid fa-envelope me-1"></i> Email Dispatch History</h6>
                    <div class="d-flex gap-2">
                        <a id="emailCsvBtn" href="{{ route('superadmin.emaillog.csv') }}" class="export-btn export-btn-csv" target="_blank">
                            <i class="fa-solid fa-file-csv"></i> Download CSV
                        </a>
                        <a id="emailPdfBtn" href="{{ route('superadmin.emaillog.pdf') }}" class="export-btn export-btn-pdf" target="_blank">
                            <i class="fa-solid fa-file-pdf"></i> Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ── Count-up ──────────────────────────────────────
    document.querySelectorAll('.count-up').forEach(el => {
        const target = parseInt(el.dataset.target);
        if (target === 0) { el.textContent = '0'; return; }
        let start = 0;
        const step = Math.ceil(target / 30);
        const timer = setInterval(() => {
            start = Math.min(start + step, target);
            el.textContent = start;
            if (start >= target) clearInterval(timer);
        }, 30);
    });

    // ── UAT Score Bars ────────────────────────────────
    document.querySelectorAll('.uat-score-fill').forEach(el => {
        setTimeout(() => { el.style.width = el.dataset.width + '%'; }, 300);
    });

    // ── 1. STATUS BAR CHART ───────────────────────────
    new Chart(document.getElementById('statusBarChart'), {
        type: 'bar',
        data: {
            labels: @json(array_keys($statusCounts)),
            datasets: [{
                label: 'Applications',
                data: @json(array_values($statusCounts)),
                backgroundColor: ['rgba(251,191,36,0.85)','rgba(56,189,248,0.85)','rgba(34,197,94,0.85)','rgba(239,68,68,0.85)'],
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

    // ── 2. RISK DOUGHNUT ─────────────────────────────
    new Chart(document.getElementById('riskDoughnutChart'), {
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
            plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, boxWidth: 12, padding: 10 } } },
            cutout: '65%'
        }
    });

    // ── 3. MONTHLY LINE CHART ────────────────────────
    new Chart(document.getElementById('monthlyLineChart'), {
        type: 'line',
        data: {
            labels: @json(array_keys($monthlyTrend)),
            datasets: [{
                label: 'Applications',
                data: @json(array_values($monthlyTrend)),
                fill: true, tension: 0.45,
                borderColor: '#0F5934',
                backgroundColor: 'rgba(15,89,52,0.07)',
                pointBackgroundColor: '#0F5934',
                pointRadius: 5, pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: 'rgba(0,0,0,0.04)' } },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });

    // ── 4. UAT RADAR CHART ────────────────────────────
    new Chart(document.getElementById('uatRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Functional Suitability', 'Usability', 'Reliability', 'Security'],
            datasets: [{
                label: 'Average Score',
                data: [
                    {{ $uatStats['avg_fs'] }},
                    {{ $uatStats['avg_us'] }},
                    {{ $uatStats['avg_rl'] }},
                    {{ $uatStats['avg_sc'] }}
                ],
                backgroundColor: 'rgba(15, 89, 52, 0.18)',
                borderColor: 'rgba(15, 89, 52, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(15, 89, 52, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(15, 89, 52, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
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

    // ── 5. INITIALIZE TOOLTIPS ────────────────────────
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // ── Phase 33: Audit Export Date Filter Wiring ─────
    function updateExportHrefs() {
        const from = document.getElementById('exportDateFrom')?.value ?? '';
        const to   = document.getElementById('exportDateTo')?.value ?? '';

        ['auditCsvBtn','auditPdfBtn','emailCsvBtn','emailPdfBtn'].forEach(id => {
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
    document.getElementById('exportDateFrom')?.addEventListener('change', updateExportHrefs);
    document.getElementById('exportDateTo')?.addEventListener('change', updateExportHrefs);

</script>
@endpush