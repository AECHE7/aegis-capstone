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
</script>
@endpush