@extends('layouts.app')

@section('title', 'Director Analytics | A.E.G.I.S.')
@section('page-title', 'System Analytics')
@section('page-subtitle', 'High-level performance, grade compliance, and UAT metrics for A.E.G.I.S.')

@push('styles')
<style>
    /* Dark stat cards */
    .dark-stat { background: linear-gradient(145deg, #07331c, #1e3932); border-radius: 16px; padding: 1.25rem 1.5rem; color: white; border: 1px solid rgba(255,255,255,0.06); position: relative; overflow: hidden; transition: all 0.25s; }
    .dark-stat:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(7, 51, 28, 0.2); }
    .dark-stat::before { content: ''; position: absolute; top: -40px; right: -40px; width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.03); }
    .dark-stat-num { font-family: 'Poppins', sans-serif; font-size: 2.25rem; font-weight: 800; line-height: 1; }
    .dark-stat-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: rgba(255,255,255,0.6); margin-bottom: 4px; }

    /* Chart cards */
    .chart-card { border: 1px solid #e2e8f0; border-radius: 16px; background: white; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }

    /* UAT Score bars */
    .uat-score-bar { height: 8px; border-radius: 4px; background: #f1f5f9; overflow: hidden; }
    .uat-score-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--clsu-green), #22c55e); transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); width: 0; }

    /* Evaluation Table */
    .eval-row { transition: background 0.15s; }
    .eval-row:hover { background: #f8fafc; }

    /* Avatar initials */
    .eval-avatar { width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, #dcfce7, #bbf7d0); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; color: #15803d; flex-shrink: 0; }

    /* Audit Export Card */
    .export-card { background: linear-gradient(145deg, #07331c, #1e3932); border-radius: 16px; padding: 1.5rem; border: 1px solid rgba(255,255,255,0.07); color: white; }
    .export-card h6 { font-size: 0.65rem; letter-spacing: 1.2px; text-transform: uppercase; color: rgba(255,255,255,0.6); font-weight: 700; margin-bottom: 0.75rem; }
    .export-card h5 { font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem; }
    .export-card p { font-size: 0.75rem; color: rgba(255,255,255,0.65); margin-bottom: 1.25rem; }
    .export-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
    .export-btn-csv  { background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.25); }
    .export-btn-csv:hover  { background: rgba(34,197,94,0.28); color: #86efac; }
    .export-btn-pdf  { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.25); }
    .export-btn-pdf:hover  { background: rgba(239,68,68,0.28); color: #fca5a5; }
    .date-filter-row { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .date-filter-row label { font-size: 0.7rem; color: rgba(255,255,255,0.65); display: block; margin-bottom: 3px; }
    .date-filter-row input { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 7px; color: white; padding: 5px 10px; font-size: 0.78rem; outline: none; }
    .export-group h6.group-label { font-size: 0.72rem; color: rgba(255,255,255,0.55); font-weight: 600; margin-bottom: 0.5rem; }
</style>
@endpush

@section('content')

{{-- Filter Action Bar --}}
<div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-filter text-success me-2"></i> Scoped Analytics Filtering</h6>
    <form action="{{ route('superadmin.analytics') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label fw-semibold text-dark small mb-1">Academic Year / Semester</label>
            <select name="academic_term_id" class="form-select py-2" style="border-radius:10px;">
                <option value="">All Academic Terms</option>
                @foreach($allTerms as $term)
                    <option value="{{ $term->id }}" {{ $termId == $term->id ? 'selected' : '' }}>
                        {{ $term->semester }} Semester (AY {{ $term->academic_year }})
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
</div>

{{-- Scoped KPI Cards Row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="dark-stat">
            <div class="dark-stat-label">Student Scholars</div>
            <div class="dark-stat-num count-up" data-target="{{ $totalStudents }}">0</div>
            <div style="color:rgba(255,255,255,0.45);font-size:0.75rem;margin-top:4px;">Unique active students</div>
            <div style="position:absolute;bottom:12px;right:16px;opacity:0.15;font-size:2rem;">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="dark-stat">
            <div class="dark-stat-label">Submissions</div>
            <div class="dark-stat-num count-up" data-target="{{ $submissionCount }}">0</div>
            <div style="color:rgba(255,255,255,0.45);font-size:0.75rem;margin-top:4px;">Total applications</div>
            <div style="position:absolute;bottom:12px;right:16px;opacity:0.15;font-size:2rem;">
                <i class="fa-solid fa-file-lines"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="dark-stat" style="background: linear-gradient(145deg, #1e3932, #0d5c34) !important;">
            <div class="dark-stat-label">Grade Integrity Index</div>
            <div class="dark-stat-num"><span class="count-up" data-target="{{ round($gradeIntegrityIndex) }}">0</span>%</div>
            <div style="color:rgba(255,255,255,0.45);font-size:0.75rem;margin-top:4px;">Avg approved authenticity</div>
            <div style="position:absolute;bottom:12px;right:16px;opacity:0.15;font-size:2rem;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="dark-stat">
            <div class="dark-stat-label">Avg Cycle Time</div>
            <div class="dark-stat-num"><span>{{ $averageCycleDays }}</span>d</div>
            <div style="color:rgba(255,255,255,0.45);font-size:0.75rem;margin-top:4px;">Turnaround: submit to decision</div>
            <div style="position:absolute;bottom:12px;right:16px;opacity:0.15;font-size:2rem;">
                <i class="fa-solid fa-hourglass-half"></i>
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
            <p class="text-muted small mb-3">Counts by evaluation status.</p>
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
        <div class="chart-card h-100">
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-award text-success me-2"></i> Academic GWA Profile Density</h6>
            <p class="text-muted small mb-3">Brackets comparing overall applicants vs. approved scholars.</p>
            <div style="position:relative;height:280px;">
                <canvas id="gwaComparisonChart"></canvas>
            </div>
        </div>
    </div>
    {{-- AI Anomaly Indicators --}}
    <div class="col-lg-6">
        <div class="chart-card h-100">
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-circle-exclamation text-danger me-2"></i> AI Tampering Indicators</h6>
            <p class="text-muted small mb-3">Most common document anomalies flagged during verification.</p>
            <div style="position:relative;height:280px;">
                <canvas id="anomalyBarChart"></canvas>
            </div>
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
            <h6 class="fw-bold text-dark mb-3">
                <i class="fa-solid fa-list-check text-success me-2"></i> Recent Evaluator Decisions
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
                    <small class="text-muted">No evaluations yet under this scope.</small>
                </div>
            @endif
        </div>
    </div>
    
    {{-- Audit Log Export Actions Panel --}}
    <div class="col-lg-5">
        <div class="export-card h-100 d-flex flex-column justify-content-between" style="border-radius:16px;">
            <div>
                <h6>Compliance Export Hub</h6>
                <h5>Generate System Audit Logs</h5>
                <p class="mb-3" style="font-size: 0.85rem; opacity: 0.85;">Select date ranges to export evaluations or security traces for administrative and compliance audits.</p>
                
                <div class="date-filter-row mb-3">
                    <div>
                        <label for="exportDateFrom">From</label>
                        <input type="date" id="exportDateFrom" style="color-scheme: dark;">
                    </div>
                    <div>
                        <label for="exportDateTo">To</label>
                        <input type="date" id="exportDateTo" style="color-scheme: dark;">
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column gap-3" style="max-height: 380px; overflow-y: auto; padding-right: 6px;">
                <!-- TIER 1 -->
                <div class="border-bottom pb-2 mb-1">
                    <span class="badge bg-success-subtle text-success mb-2" style="font-size:0.75rem;">Tier 1 — Critical Compliance Logs</span>
                    
                    <div class="export-group mb-2">
                        <h6 class="group-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">Application Status Logs</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.audit.csv') }}" id="auditCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.audit.pdf') }}" id="auditPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>

                    <div class="export-group mb-2">
                        <h6 class="group-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">AI Document Scan Results</h6>
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
                        <h6 class="group-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">Admin Evaluation Decisions</h6>
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
                <div class="border-bottom pb-2 mb-1">
                    <span class="badge bg-info-subtle text-info mb-2" style="font-size:0.75rem;">Tier 2 — Security & Access Logs</span>

                    <div class="export-group mb-2">
                        <h6 class="group-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">Login & Authentication Logs</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.export.auth-log.csv') }}" id="authCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.export.auth-log.pdf') }}" id="authPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>

                    <div class="export-group mb-2">
                        <h6 class="group-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">Admin Action Audit Trail</h6>
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
                        <h6 class="group-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">System Settings Changes</h6>
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
                <div class="border-bottom pb-2 mb-1">
                    <span class="badge bg-warning-subtle text-warning mb-2" style="font-size:0.75rem;">Tier 3 — Operational Oversight</span>

                    <div class="export-group mb-2">
                        <h6 class="group-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">Email Notification Logs</h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.emaillog.csv') }}" id="emailCsvBtn" class="export-btn export-btn-csv flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('superadmin.emaillog.pdf') }}" id="emailPdfBtn" class="export-btn export-btn-pdf flex-grow-1 text-center justify-content-center">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>

                    <div class="export-group mb-2">
                        <h6 class="group-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">Scholarship Program Changes</h6>
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
                        <h6 class="group-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">Data Export Access History</h6>
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
                <div>
                    <span class="badge bg-secondary-subtle text-secondary mb-2" style="font-size:0.75rem;">Tier 4 — Student Activity Logs</span>

                    <div class="export-group mb-2">
                        <h6 class="group-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">Student Lifecycle Timeline</h6>
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
                        <h6 class="group-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">Document Upload History</h6>
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
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-square-poll-horizontal text-success me-2"></i> Program Breakdown Snapshot</h6>
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
                                <div class="small">Max GWA: <strong>{{ $sb['min_gwa'] ?: 'None' }}</strong></div>
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

{{-- Top Performing Programs + UAT Summary Row --}}
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card p-4 h-100" style="border-radius:16px;">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-trophy text-warning me-2"></i> Top Performing Programs</h6>
            <p class="text-muted small mb-3">Programs ranked by highest average approved scholar GWA.</p>
            @if($topPrograms->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 align-middle" style="font-size:0.875rem;">
                    <thead>
                        <tr>
                            <th class="ps-2">#</th>
                            <th>Program</th>
                            <th class="text-center">Approved Scholars</th>
                            <th class="text-center">Avg GWA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topPrograms as $i => $prog)
                        <tr>
                            <td class="ps-2 fw-bold text-muted small">{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $prog->program_name }}</td>
                            <td class="text-center">{{ $prog->total_apps }}</td>
                            <td class="text-center">
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
            <div class="text-center py-5">
                <i class="fa-solid fa-inbox fa-2x text-muted mb-2 d-block opacity-30"></i>
                <small class="text-muted">No approved scholars yet under this scope.</small>
            </div>
            @endif
        </div>
    </div>
    <div class="col-lg-5">
        <div class="chart-card h-100 d-flex flex-column">
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-star text-warning me-2"></i> System UAT Ratings (ISO 25010)</h6>
            <p class="text-muted small mb-3">Overall mean: <strong>{{ $uatStats['overall_mean'] }}/5</strong> &middot; {{ $uatStats['count'] }} responses</p>
            <div style="position:relative;height:220px;flex:1;">
                <canvas id="uatRadarChart"></canvas>
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
                            <td colspan="6" class="text-center py-4 text-muted small">
                                <i class="fa-solid fa-circle-info me-1"></i> No approved scholars currently registered matching these filters.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
            plugins: { legend: { position: 'bottom', labels: { font: { size: 9 }, boxWidth: 10, padding: 8 } } },
            cutout: '65%'
        }
    });

    // ── 3. COLLEGE DISTRIBUTION DOUGHNUT ──────────────
    new Chart(document.getElementById('collegeDoughnutChart'), {
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

    // ── 4. GWA DISTRIBUTION BRACKETS CHART ────────────
    new Chart(document.getElementById('gwaComparisonChart'), {
        type: 'bar',
        data: {
            labels: ['1.00-1.25', '1.26-1.50', '1.51-1.75', '1.76-2.00', '>2.00'],
            datasets: [
                {
                    label: 'Applicants',
                    data: @json(array_values($applicantGwaCounts)),
                    backgroundColor: 'rgba(7, 51, 28, 0.4)',
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
                y: { beginAtZero: true, ticks: { font: { size: 10 } }, grid: { color: 'rgba(0,0,0,0.04)' } },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });

    // ── 5. AI ANOMALY HORIZONTAL BAR CHART ────────────
    new Chart(document.getElementById('anomalyBarChart'), {
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
                x: { beginAtZero: true, ticks: { font: { size: 10 } }, grid: { color: 'rgba(0,0,0,0.04)' } },
                y: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });

    // ── 6. DUAL-AXIS MONTHLY CHART (Volume + Processing Speed) ──
    new Chart(document.getElementById('monthlyDualChart'), {
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

    // ── 7. PROCESS AUDIT FUNNEL CHART ──────────────────
    new Chart(document.getElementById('processFunnelChart'), {
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

    // ── 8. UAT RADAR CHART ────────────────────────────
    new Chart(document.getElementById('uatRadarChart'), {
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

    // ── 9. INITIALIZE TOOLTIPS ────────────────────────
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // ── Date Filter Wiring ────────────────────────────
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
    document.getElementById('exportDateFrom')?.addEventListener('change', updateExportHrefs);
    document.getElementById('exportDateTo')?.addEventListener('change', updateExportHrefs);
</script>
@endpush