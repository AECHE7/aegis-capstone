@extends('layouts.app')

@section('title', 'OSA Admin Dashboard | A.E.G.I.S.')
@section('page-title', 'Application Queue')
@section('page-subtitle', 'Review and evaluate all scholarship applications')

@push('styles')
<style>
    /* Count-up animation */
    .stat-number { font-size: 2.25rem; font-weight: 800; font-family: 'Poppins', sans-serif; line-height: 1; }

    /* Searchbar */
    .filter-bar { background: white; border-radius: 16px; padding: 1.25rem 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }

    /* Table */
    .queue-table { border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; background: white; }
    .queue-table thead th { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: #64748b; background: #f8fafc; padding: 0.9rem 1rem; border-bottom: 2px solid #e2e8f0; }
    .queue-table tbody tr { border-color: #f1f5f9; cursor: pointer; transition: all 0.15s; }
    .queue-table tbody tr:hover { background: #f8fafc; }
    .queue-table td { padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.875rem; }

    /* Avatar */
    .student-avatar { width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #e0f2fe, #bae6fd); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; color: #0369a1; flex-shrink: 0; }

    /* Fraud Score Chip */
    .fraud-chip { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; }
    .fraud-low    { background: #dcfce7; color: #15803d; }
    .fraud-mod    { background: #fef9c3; color: #a16207; }
    .fraud-high   { background: #fee2e2; color: #b91c1c; }
    .fraud-none   { background: #f1f5f9; color: #94a3b8; }

    /* Action button */
    .btn-evaluate { background: linear-gradient(135deg, var(--clsu-green), #16703f); color: white; border: none; padding: 6px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; transition: all 0.2s; }
    .btn-evaluate:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(15,89,52,0.3); color: white; }

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
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card warning card-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem;letter-spacing:0.8px;">Pending</div>
                    <div class="stat-number text-dark count-up" data-target="{{ $pendingCount }}">0</div>
                </div>
                <div class="stat-icon" style="background:#fef9c3;">
                    <i class="fa-solid fa-hourglass-half" style="color:#ca8a04;"></i>
                </div>
            </div>
            <div class="small text-muted">Awaiting review</div>
        </div>
    </div>
    {{-- Under Review --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card info card-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem;letter-spacing:0.8px;">Under Review</div>
                    <div class="stat-number text-dark count-up" data-target="{{ $underReviewCount }}">0</div>
                </div>
                <div class="stat-icon" style="background:#e0f2fe;">
                    <i class="fa-solid fa-magnifying-glass-chart" style="color:#0284c7;"></i>
                </div>
            </div>
            <div class="small text-muted">Being evaluated</div>
        </div>
    </div>
    {{-- Approved --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card success card-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem;letter-spacing:0.8px;">Verified Scholars</div>
                    <div class="stat-number text-dark count-up" data-target="{{ $approvedCount }}">0</div>
                </div>
                <div class="stat-icon" style="background:#dcfce7;">
                    <i class="fa-solid fa-user-graduate" style="color:#16a34a;"></i>
                </div>
            </div>
            <div class="small text-muted">Fully approved</div>
        </div>
    </div>
    {{-- Rejected --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card danger card-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem;letter-spacing:0.8px;">Anomalies Detected</div>
                    <div class="stat-number text-dark count-up" data-target="{{ $rejectedCount }}">0</div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;">
                    <i class="fa-solid fa-shield-virus" style="color:#dc2626;"></i>
                </div>
            </div>
            <div class="small text-muted">Rejected applications</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar mb-4">
    <form action="{{ route('admin.dashboard') }}" method="GET">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-muted mb-1"><i class="fa-solid fa-magnifying-glass me-1"></i> Search</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, ID, or Program...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-muted mb-1"><i class="fa-solid fa-graduation-cap me-1"></i> Scholarship</label>
                <select name="scholarship_id" class="form-select">
                    <option value="">All Programs</option>
                    @foreach($scholarships as $s)
                        <option value="{{ $s->id }}" {{ request('scholarship_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted mb-1"><i class="fa-solid fa-circle-half-stroke me-1"></i> Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="Under Review" {{ request('status') === 'Under Review' ? 'selected' : '' }}>🔍 Under Review</option>
                    <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>✅ Approved</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>❌ Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted mb-1"><i class="fa-solid fa-calendar me-1"></i> Academic Period</label>
                <select name="academic_term_id" class="form-select">
                    <option value="">All Periods</option>
                    @foreach($academicTerms as $term)
                        <option value="{{ $term->id }}" {{ request('academic_term_id') == $term->id ? 'selected' : '' }}>
                            {{ $term->semester }}, SY {{ $term->academic_year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn fw-bold flex-grow-1" style="background: var(--clsu-green); color: white; border-radius: 8px; font-size:0.875rem;">
                    Filter
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light fw-bold" style="border-radius:8px;font-size:0.875rem;" title="Clear">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Application Queue Table --}}
<div class="queue-table shadow-sm">
    {{-- Table Header with Export --}}
    <div class="d-flex justify-content-between align-items-center p-4 border-bottom" style="background: white;">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-users-viewfinder text-primary me-2"></i> Applicant Evaluation Queue</h6>
            <small class="text-muted">{{ $applications->total() }} total {{ $applications->total() === 1 ? 'application' : 'applications' }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.export', request()->query()) }}" class="btn-export btn-export-csv">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('admin.exportPdf', request()->query()) }}" class="btn-export btn-export-pdf">
                <i class="fa-solid fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table mb-0" style="border-collapse: separate;">
            <thead>
                <tr>
                    <th class="ps-4">Ref ID</th>
                    <th>Applicant</th>
                    <th>Program / Grant</th>
                    <th class="text-center">GWA</th>
                    <th class="text-center">AI Risk</th>
                    <th class="text-center">Status</th>
                    <th>Submitted</th>
                    <th class="pe-4 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $app)
                <tr onclick="window.location='{{ route('admin.review', $app->id) }}'" style="cursor:pointer;">
                    <td class="ps-4">
                        <span class="fw-bold text-dark" style="font-size:0.8rem;font-family:'Poppins',sans-serif;">APP-{{ $app->id }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="student-avatar">{{ strtoupper(substr($app->user->name ?? 'U', 0, 2)) }}</div>
                            <div>
                                <div class="fw-semibold text-dark" style="font-size:0.875rem;">{{ $app->user->name ?? 'Unknown' }}</div>
                                <div class="text-muted" style="font-size:0.72rem;">{{ $app->user->profile?->clsu_id_number ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium text-dark" style="font-size:0.875rem;">{{ $app->program_name }}</div>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill px-2 py-1 fw-bold" style="background:#f1f5f9;color:#475569;font-size:0.8rem;border:1px solid #e2e8f0;">{{ $app->gwa }}</span>
                    </td>
                    <td class="text-center">
                        @if($app->document && $app->document->aiResult && !in_array($app->document->aiResult->classification, ['scanning','failed']))
                            @php $score = $app->document->aiResult->fraud_probability; @endphp
                            <span class="fraud-chip {{ $score >= 70 ? 'fraud-high' : ($score >= 40 ? 'fraud-mod' : 'fraud-low') }}">
                                <i class="fa-solid fa-microchip" style="font-size:0.6rem;"></i>
                                {{ $score }}%
                            </span>
                        @elseif($app->document && $app->document->aiResult && $app->document->aiResult->classification === 'scanning')
                            <span class="fraud-chip fraud-none"><i class="fa-solid fa-circle-notch fa-spin" style="font-size:0.6rem;"></i> Scanning</span>
                        @else
                            <span class="fraud-chip fraud-none"><i class="fa-solid fa-minus" style="font-size:0.6rem;"></i> N/A</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($app->status == 'Pending')
                            <span class="status-badge pending"><i class="fa-solid fa-hourglass-half" style="font-size:0.65rem;"></i> Pending</span>
                        @elseif($app->status == 'Under Review')
                            <span class="status-badge review"><i class="fa-solid fa-magnifying-glass" style="font-size:0.65rem;"></i> Under Review</span>
                        @elseif($app->status == 'Approved')
                            <span class="status-badge approved"><i class="fa-solid fa-check" style="font-size:0.65rem;"></i> Approved</span>
                        @else
                            <span class="status-badge rejected"><i class="fa-solid fa-times" style="font-size:0.65rem;"></i> Rejected</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:0.82rem;color:#64748b;">{{ $app->created_at->format('M d, Y') }}</div>
                        <div style="font-size:0.72rem;color:#94a3b8;">{{ $app->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="pe-4 text-end" onclick="event.stopPropagation()">
                        <a href="{{ route('admin.review', $app->id) }}" class="btn-evaluate">
                            Evaluate <i class="fa-solid fa-arrow-right ms-1" style="font-size:0.7rem;"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($applications->isEmpty())
    <div class="text-center py-5">
        <div class="mb-3">
            <div style="width:80px;height:80px;border-radius:20px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                <i class="fa-solid fa-inbox fa-2x" style="color:#cbd5e1;"></i>
            </div>
        </div>
        <h6 class="fw-bold text-muted">The queue is empty</h6>
        <p class="text-muted small mb-0">No applications match your current filters.</p>
    </div>
    @endif

    <div class="px-4 py-3 border-top bg-white" style="border-radius: 0 0 16px 16px;">
        {{ $applications->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Count-up animation ──────────────────────────────
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
</script>
@endpush