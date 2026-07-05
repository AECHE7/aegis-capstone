@extends('layouts.app')

@section('title', 'Evaluate APP-' . $application->id . ' | A.E.G.I.S. Forensics')
@section('page-title', 'Document Forensics Suite')
@section('page-subtitle', 'AI-powered authenticity analysis for APP-' . $application->id)

@push('styles')
<style>
    /* Risk Colors */
    .text-high-risk { color: #ef4444; }
    .text-mod-risk  { color: #f59e0b; }
    .text-low-risk  { color: #22c55e; }

    /* AI Card */
    .ai-card {
        background: var(--card-bg);
        border-radius: 12px;
        color: var(--text-main);
        position: relative;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    /* Radial Progress Ring */
    .risk-ring-wrapper {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto;
    }

    .risk-ring-svg {
        transform: rotate(-90deg);
        width: 140px;
        height: 140px;
    }

    .risk-ring-track { fill: none; stroke: var(--border-color); stroke-width: 10; }
    .risk-ring-fill  { fill: none; stroke-width: 10; stroke-linecap: round; transition: stroke-dashoffset 1.2s cubic-bezier(0.4, 0, 0.2, 1); }

    .risk-ring-center {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    /* Document Viewer */
    .viewer-box {
        background: var(--clsu-bg);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .viewer-box.danger-box { border-color: rgba(239, 68, 68, 0.4); background: rgba(239, 68, 68, 0.05); }

    .viewer-label {
        position: absolute;
        top: -1px; left: 50%;
        transform: translateX(-50%);
        background: #475569;
        color: white;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 4px 14px;
        border-radius: 0 0 8px 8px;
        z-index: 5;
        white-space: nowrap;
    }

    .danger-box .viewer-label { background: #dc2626; }

    .viewer-box img {
        max-height: 500px;
        width: 100%;
        object-fit: contain;
        padding: 45px 10px 10px;
    }

    /* Decision buttons */
    .btn-approve {
        background: var(--clsu-green);
        color: white; border: none;
        padding: 12px; border-radius: 8px;
        font-weight: 700; font-size: 0.95rem;
        transition: var(--transition);
    }
    .btn-approve:hover { background: var(--clsu-green-light); color: white; }

    .btn-reject {
        background: #dc2626;
        color: white; border: none;
        padding: 12px; border-radius: 8px;
        font-weight: 700; font-size: 0.95rem;
        transition: var(--transition);
    }
    .btn-reject:hover { background: #b91c1c; color: white; }

    /* Applicant info card */
    .applicant-card {
        background: var(--card-bg);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
    }

    /* Info chip */
    .info-chip { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; background: var(--clsu-bg); font-size: 0.78rem; color: var(--text-main); font-weight: 500; }

    /* Lightbox */
    .img-zoomable { cursor: zoom-in; }
</style>
@endpush

@section('content')

@php
    $hasAnyAiResult = $application->documents->contains(fn($d) => $d->aiResult);
    $anyScanning  = $application->documents->contains(fn($d) => $d->aiResult && $d->aiResult->classification === 'scanning');
    $anyFailed    = $application->documents->contains(fn($d) => $d->aiResult && $d->aiResult->classification === 'failed');
@endphp

{{-- Back button --}}
<div class="mb-3">
    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light fw-semibold rounded-pill px-3"
       style="font-size:0.8rem;border:1px solid #e2e8f0;">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Queue
    </a>
</div>

<div class="applicant-card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge rounded-pill px-3 py-1 monospace-data" style="background:#f1f5f9;color:#475569;font-size:0.75rem;font-weight:700;">APP-{{ $application->id }}</span>
                <h5 class="fw-bold mb-0 text-dark">{{ $application->program_name }}</h5>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="info-chip"><i class="fa-solid fa-user text-primary"></i> {{ $application->user->name ?? 'Unknown' }}</span>
                <span class="info-chip"><i class="fa-solid fa-id-card text-primary"></i> <span class="monospace-data">{{ $application->user->profile?->clsu_id_number ?? 'N/A' }}</span></span>
                <span class="info-chip"><i class="fa-solid fa-graduation-cap text-primary"></i> {{ $application->user->profile?->course ?? 'N/A' }} — {{ $application->user->profile?->year_level ?? 'N/A' }}</span>
                <span class="info-chip"><i class="fa-solid fa-star text-warning"></i> GWA: <strong class="monospace-data">{{ $application->gwa }}</strong></span>
            </div>
        </div>
        <div>
            @if($application->trashed())
                <span class="status-badge bg-secondary text-white"><i class="fa-solid fa-ban"></i> Cancelled</span>
            @elseif($application->status == 'Pending')
                <span class="status-badge pending"><i class="fa-solid fa-hourglass-half"></i> Pending</span>
            @elseif($application->status == 'Under Review')
                <span class="status-badge review"><i class="fa-solid fa-magnifying-glass"></i> Under Review</span>
            @elseif($application->status == 'Approved')
                <span class="status-badge approved"><i class="fa-solid fa-check"></i> Approved</span>
            @else
                <span class="status-badge rejected"><i class="fa-solid fa-times"></i> Rejected</span>
            @endif
        </div>
    </div>
</div>

{{-- Main 2-column layout --}}
<div class="row g-4">

    {{-- LEFT: AI Panel + Decision --}}
    <div class="col-lg-4">

        @foreach($application->documents as $doc)
            @php
                $hasAiResult = $doc->aiResult;
                $isScanning  = $hasAiResult && $doc->aiResult->classification === 'scanning';
                $isFailed    = $hasAiResult && $doc->aiResult->classification === 'failed';

                $fraudScore  = $hasAiResult && !$isScanning && !$isFailed ? $doc->aiResult->fraud_probability : 0;
                $riskColor   = $fraudScore >= 70 ? '#ef4444' : ($fraudScore >= 40 ? '#f59e0b' : '#22c55e');
                $riskLabel   = $fraudScore >= 70 ? 'HIGH RISK' : ($fraudScore >= 40 ? 'MODERATE RISK' : 'LOW RISK');
                $riskClass   = $fraudScore >= 70 ? 'danger' : ($fraudScore >= 40 ? 'warning' : 'success');

                // Ring math — circumference of r=60 circle = 2π×60 ≈ 376.99
                $circumference = 376.99;
                $dashOffset = $circumference - ($fraudScore / 100) * $circumference;
            @endphp
            
            <div class="ai-panel-doc" id="ai-panel-{{ $doc->id }}" style="display: {{ $loop->first ? 'block' : 'none' }};">
                {{-- AI Card --}}
                <div class="ai-card p-4 mb-3">
                    <div class="text-center mb-3">
                        <div style="font-size:0.65rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-main);opacity:0.6;" class="mb-2">
                            <i class="fa-solid fa-microchip me-1 text-info"></i> {{ $doc->document_type }} Verification Analysis
                        </div>
                    </div>

                    @if($isScanning)
                        <div class="text-center py-4">
                            <i class="fa-solid fa-circle-notch fa-spin fa-4x text-info mb-3"></i>
                            <p class="text-muted small mb-2">ELA + ResNet-50 analysis running...<br>Page will auto-refresh.</p>
                            <form action="{{ route('admin.scan', $application->id) }}" method="POST" class="d-inline-block">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-info rounded-pill px-3 mt-1" style="font-size: 0.72rem; border-color: rgba(0, 212, 255, 0.4); color: #00d4ff;">
                                    <i class="fa-solid fa-arrow-rotate-right me-1"></i> Force Restart Scan
                                </button>
                            </form>
                            <script>
                                setTimeout(() => {
                                    location.reload();
                                }, 5000);
                            </script>
                        </div>
                    @elseif($isFailed)
                        <div class="text-center py-4">
                            <i class="fa-solid fa-triangle-exclamation fa-4x text-danger mb-3"></i>
                            <p class="text-muted small mb-3">AI Scan failed.<br>The document file might be missing from the server disk or the AI service returned an error.</p>
                            <form action="{{ route('admin.scan', $application->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning text-dark fw-bold w-100 rounded-3">
                                    <i class="fa-solid fa-arrow-rotate-right me-1"></i> Retry AI Scan
                                </button>
                            </form>
                        </div>
                    @elseif($hasAiResult)
                        {{-- Radial ring --}}
                        <div class="risk-ring-wrapper mb-3">
                            <svg class="risk-ring-svg" viewBox="0 0 140 140">
                                <circle class="risk-ring-track" cx="70" cy="70" r="60"/>
                                <circle class="risk-ring-fill" cx="70" cy="70" r="60"
                                    stroke="{{ $riskColor }}"
                                    stroke-dasharray="{{ $circumference }}"
                                    stroke-dashoffset="{{ $circumference }}"
                                    id="riskRing-{{ $doc->id }}"/>
                            </svg>
                            <div class="risk-ring-center">
                                <div class="monospace-data" style="font-size:2rem;font-weight:700;color:{{ $riskColor }};line-height:1;">{{ $fraudScore }}</div>
                                <div style="font-size:0.75rem;color:var(--text-main);opacity:0.6;">% fraud prob.</div>
                            </div>
                        </div>

                        <div class="text-center mb-3">
                            <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: {{ $riskColor }}22; color: {{ $riskColor }}; border: 1px solid {{ $riskColor }}44; font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $riskLabel }}
                            </span>
                        </div>

                        <div class="rounded-3 p-3 small" style="background: var(--clsu-bg); border: 1px solid var(--border-color);">
                            <div class="d-flex justify-content-between mb-2">
                                <span style="color:var(--text-main);opacity:0.7;">Architecture</span>
                                <span class="fw-semibold">ResNet-50 CNN</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span style="color:var(--text-main);opacity:0.7;">Preprocessing</span>
                                <span class="fw-semibold">Error Level Analysis</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span style="color:var(--text-main);opacity:0.7;">Classification</span>
                                <span class="fw-semibold" style="color:{{ $riskColor }};">{{ ucfirst($doc->aiResult->classification) }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div style="width:72px;height:72px;border-radius:12px;background:var(--clsu-bg);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;border: 1px solid var(--border-color);">
                                <i class="fa-solid fa-file-shield fa-2x text-muted" style="opacity:0.6;"></i>
                            </div>
                            <p class="mb-3 text-muted" style="font-size:0.85rem;">Document awaiting forensic verification.</p>
                            <form action="{{ route('admin.scan', $application->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info text-dark fw-bold w-100 rounded-3" style="border-radius:8px !important;">
                                    <i class="fa-solid fa-bolt me-1"></i> Execute AI Scan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- Risk Legend --}}
                @if($hasAiResult && !$isScanning && !$isFailed)
                <div class="d-flex gap-2 mb-3 justify-content-center">
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;background:#dcfce7;color:#16a34a;font-size:0.7rem;font-weight:700;"><span>●</span> 0–39% Low</span>
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;background:#fef9c3;color:#a16207;font-size:0.7rem;font-weight:700;"><span>●</span> 40–69% Moderate</span>
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;background:#fee2e2;color:#b91c1c;font-size:0.7rem;font-weight:700;"><span>●</span> 70–100% High</span>
                </div>
                @endif
            </div>
        @endforeach

        {{-- Decision Form --}}
        <div class="card p-4">
            <h6 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-gavel text-primary me-2"></i> Final Eligibility Decision</h6>

            <form action="{{ route('admin.updateStatus', $application->id) }}" method="POST" id="decisionForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted" for="evaluatorRemarks">Evaluator Remarks</label>
                    <textarea name="remarks" id="evaluatorRemarks" class="form-control" rows="4" required
                              placeholder="e.g., GWA verified. Cleared for DOST-SEI Merit."
                              style="resize:none;font-size:0.875rem;">{{ $application->remarks }}</textarea>
                </div>

                @if($application->trashed())
                    <div class="alert alert-warning text-center rounded-3 mb-3 small" style="border: none; background: #fffbeb; color: #b45309;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> <strong>Cancelled Application</strong><br>
                        This application was cancelled by the student and is soft-deleted.
                    </div>
                    <button type="button" id="restoreReviewBtn" class="btn btn-success fw-bold w-100 py-2 text-white" style="border-radius:10px;">
                        <i class="fa-solid fa-trash-arrow-up me-1"></i> Restore Application
                    </button>
                @elseif($application->status == 'Pending' || $application->status == 'Under Review')
                    <div class="d-flex gap-2">
                        <button type="button" class="btn-approve w-50" onclick="confirmDecision('Approved')">
                            <i class="fa-solid fa-check-circle me-1"></i> Approve
                        </button>
                        <button type="button" class="btn-reject w-50" onclick="confirmDecision('Rejected')">
                            <i class="fa-solid fa-times-circle me-1"></i> Reject
                        </button>
                    </div>
                    <input type="hidden" name="status" id="statusInput">
                @else
                    <div class="alert mb-0 text-center fw-bold rounded-3"
                         style="background: {{ $application->status == 'Approved' ? '#dcfce7' : '#fee2e2' }}; color: {{ $application->status == 'Approved' ? '#15803d' : '#b91c1c' }}; border: none; font-size: 0.875rem;">
                        <i class="fa-solid fa-lock me-1"></i> Application is finalized as {{ $application->status }}.
                    </div>
                @endif
            </form>
        </div>

        {{-- Custom Fields Card --}}
        @if($application->customFields && $application->customFields->count() > 0)
        <div class="card p-4 mt-3">
            <h6 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-list-check text-primary me-2"></i> Custom Form Responses</h6>
            <div class="d-flex flex-column gap-3 text-start">
                @foreach($application->customFields as $field)
                    <div class="border-bottom pb-2">
                        <div class="small fw-semibold text-muted mb-1">{{ $field->field_name }}</div>
                        <div class="text-dark fw-medium" style="font-size:0.875rem;">
                            @if(Str::startsWith($field->field_value, 'uploads/'))
                                <a href="{{ route('application-field.file', $field->id) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" style="border-radius: 6px; font-size: 0.75rem;">
                                    <i class="fa-solid fa-file-arrow-down me-1"></i> View Uploaded File
                                </a>
                            @else
                                {{ $field->field_value }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- RIGHT: Document Viewer --}}
    <div class="col-lg-8">
        <div class="card p-4 h-100">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
                <div>
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-images text-primary me-2"></i> Document Forensics Viewer</h6>
                    <small class="text-muted">Click images to enlarge</small>
                </div>
                @if($application->documents->count() > 1)
                    <div class="d-flex align-items-center gap-2">
                        <label class="small fw-bold text-muted mb-0 me-1" for="docSelector"><i class="fa-solid fa-file-invoice"></i> Document:</label>
                        <select class="form-select form-select-sm" id="docSelector" style="width: auto; font-size: 0.8rem; border-radius: 8px;">
                            @foreach($application->documents as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->document_type }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            @foreach($application->documents as $doc)
                @php
                    $hasAiResult = $doc->aiResult;
                    $isScanning  = $hasAiResult && $doc->aiResult->classification === 'scanning';
                    $isFailed    = $hasAiResult && $doc->aiResult->classification === 'failed';
                    $fraudScore  = $hasAiResult && !$isScanning && !$isFailed ? $doc->aiResult->fraud_probability : 0;
                    $riskClass   = $fraudScore >= 70 ? 'danger' : ($fraudScore >= 40 ? 'warning' : 'success');
                @endphp
                <div class="doc-viewer-wrapper" id="viewer-doc-{{ $doc->id }}" style="display: {{ $loop->first ? 'block' : 'none' }};">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('admin.document.download', $doc->id) }}"
                           class="btn btn-sm btn-light fw-semibold rounded-pill px-3"
                           style="font-size:0.78rem;border:1px solid #e2e8f0;white-space:nowrap;">
                            <i class="fa-solid fa-download me-1"></i> Download {{ $doc->document_type }}
                        </a>
                    </div>
                    <div class="row g-3">
                        {{-- Original --}}
                        <div class="col-md-6">
                            <div class="viewer-box">
                                <div class="viewer-label"><i class="fa-solid fa-file me-1"></i> Original {{ $doc->document_type }}</div>
                                <img src="{{ route('document.view', $doc->id) }}"
                                     alt="Original Student Document" class="img-zoomable"
                                     onerror="this.src='https://placehold.co/600x800?text=Image+Not+Found'"
                                     onclick="openLightbox(this.src)">
                            </div>
                        </div>

                        {{-- Heatmap --}}
                        <div class="col-md-6">
                            <div class="viewer-box {{ $riskClass }}-box">
                                <div class="viewer-label"><i class="fa-solid fa-fire me-1"></i> Grad-CAM Heatmap</div>
                                @if($isScanning)
                                    <div class="d-flex flex-column align-items-center justify-content-center py-5 w-100" style="min-height:300px;">
                                        <i class="fa-solid fa-spinner fa-spin fa-3x text-primary mb-2"></i>
                                        <small class="text-muted">AI Scanning in progress...</small>
                                    </div>
                                @elseif($isFailed)
                                    <div class="d-flex flex-column align-items-center justify-content-center py-5 w-100" style="min-height:300px;">
                                        <i class="fa-solid fa-triangle-exclamation fa-3x text-danger mb-2 opacity-50"></i>
                                        <small class="text-muted">Scan failed. Please retry.</small>
                                    </div>
                                @elseif($hasAiResult)
                                    <img src="{{ route('document.heatmap', $doc->id) }}"
                                         alt="AI Heatmap Overlay" class="img-zoomable"
                                         onclick="openLightbox(this.src)">
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center py-5 w-100" style="min-height:300px;">
                                        <i class="fa-solid fa-robot fa-3x text-danger mb-2 opacity-25"></i>
                                        <small class="text-muted">Awaiting AI scan execution</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Footer explanation --}}
            <div class="mt-4 rounded-3 p-3 small text-muted" style="background:#f8fafc;border:1px solid #e2e8f0;">
                <i class="fa-solid fa-circle-info text-primary me-1"></i>
                <strong>How to interpret:</strong> Hot/red areas on the Grad-CAM heatmap highlight pixels with anomalous ELA compression signatures — strong indicators of digital manipulation or pixel-level copy-paste forgery, as detected by the ResNet-50 CNN pipeline.
            </div>
        </div>
    </div>
</div>

{{-- Lightbox overlay --}}
<div id="lightbox" onclick="closeLightbox()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.88);z-index:9999;align-items:center;justify-content:center;cursor:zoom-out;">
    <img id="lightboxImg" src="" alt="Enlarged view" style="max-width:90vw;max-height:90vh;object-fit:contain;border-radius:12px;box-shadow:0 24px 64px rgba(0,0,0,0.5);">
</div>

@endsection

@push('scripts')
<script>
    // ── Radial ring animation ──────────────────────────
    window.addEventListener('DOMContentLoaded', () => {
        @foreach($application->documents as $doc)
            @php
                $hasAiResult = $doc->aiResult;
                $isScanning  = $hasAiResult && $doc->aiResult->classification === 'scanning';
                $isFailed    = $hasAiResult && $doc->aiResult->classification === 'failed';
                $fraudScore  = $hasAiResult && !$isScanning && !$isFailed ? $doc->aiResult->fraud_probability : 0;
                $dashOffset = 376.99 - ($fraudScore / 100) * 376.99;
            @endphp
            const ring{{ $doc->id }} = document.getElementById('riskRing-{{ $doc->id }}');
            if (ring{{ $doc->id }}) {
                setTimeout(() => {
                    ring{{ $doc->id }}.style.strokeDashoffset = '{{ $dashOffset }}';
                }, 200);
            }
        @endforeach

        // Document selector listener
        const docSelector = document.getElementById('docSelector');
        if (docSelector) {
            docSelector.addEventListener('change', function() {
                const docId = this.value;
                
                // Hide all AI panels and viewers
                document.querySelectorAll('.ai-panel-doc').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.doc-viewer-wrapper').forEach(el => el.style.display = 'none');
                
                // Show selected
                const selectedAiPanel = document.getElementById('ai-panel-' + docId);
                const selectedViewer = document.getElementById('viewer-doc-' + docId);
                if (selectedAiPanel) selectedAiPanel.style.display = 'block';
                if (selectedViewer) selectedViewer.style.display = 'block';
            });
        }
    });

    // ── Decision confirm & AJAX submit ─────────────────
    function confirmDecision(status) {
        const remarks = document.getElementById('evaluatorRemarks').value.trim();
        if (!remarks) {
            Swal.fire({
                icon: 'warning',
                title: 'Remarks Required',
                text: 'Please write evaluator remarks before finalizing your decision.',
                confirmButtonColor: '#f59e0b',
                customClass: { popup: 'rounded-4' }
            });
            return;
        }

        Swal.fire({
            title: `Confirm ${status}?`,
            text: `You are about to permanently mark this application as ${status}.`,
            icon: status === 'Approved' ? 'success' : 'warning',
            showCancelButton: true,
            confirmButtonColor: status === 'Approved' ? '#16a34a' : '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${status} it!`,
            customClass: { popup: 'rounded-4' }
        }).then(result => {
            if (result.isConfirmed) {
                submitDecisionAjax(status);
            }
        });
    }

    async function submitDecisionAjax(status) {
        const form = document.getElementById('decisionForm');
        document.getElementById('statusInput').value = status;
        
        // Show loading state on buttons
        const approveBtn = document.querySelector('.btn-approve');
        const rejectBtn = document.querySelector('.btn-reject');
        const remarksField = document.getElementById('evaluatorRemarks');
        
        const originalApprove = approveBtn ? approveBtn.innerHTML : '';
        const originalReject = rejectBtn ? rejectBtn.innerHTML : '';

        if (approveBtn) { approveBtn.disabled = true; }
        if (rejectBtn) { rejectBtn.disabled = true; }
        if (remarksField) { remarksField.disabled = true; }

        if (status === 'Approved' && approveBtn) {
            approveBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-1"></i> Saving...';
        } else if (status === 'Rejected' && rejectBtn) {
            rejectBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-1"></i> Saving...';
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Decision Finalized',
                    text: data.message,
                    confirmButtonColor: '#16a34a',
                    customClass: { popup: 'rounded-4' }
                });

                // Update final state in UI
                const container = form.parentElement;
                container.innerHTML = `
                    <h6 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-gavel text-primary me-2"></i> Final Eligibility Decision</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Evaluator Remarks</label>
                        <div class="p-3 rounded-3 bg-light border small text-dark">${data.remarks || 'No remarks provided.'}</div>
                    </div>
                    <div class="alert mb-0 text-center fw-bold rounded-3"
                         style="background: ${data.status === 'Approved' ? '#dcfce7' : '#fee2e2'}; color: ${data.status === 'Approved' ? '#15803d' : '#b91c1c'}; border: none; font-size: 0.875rem;">
                        <i class="fa-solid fa-lock me-1"></i> Application is finalized as ${data.status}.
                    </div>
                `;
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                if (approveBtn) { approveBtn.disabled = false; approveBtn.innerHTML = originalApprove; }
                if (rejectBtn) { rejectBtn.disabled = false; rejectBtn.innerHTML = originalReject; }
                if (remarksField) { remarksField.disabled = false; }
            }
        } catch (error) {
            console.error('Decision Submission Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.', confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
            if (approveBtn) { approveBtn.disabled = false; approveBtn.innerHTML = originalApprove; }
            if (rejectBtn) { rejectBtn.disabled = false; rejectBtn.innerHTML = originalReject; }
            if (remarksField) { remarksField.disabled = false; }
        }
    }

    // ── AJAX Scan Execution ────────────────────────────
    document.addEventListener('submit', async (e) => {
        const form = e.target;
        if (form.action && form.action.includes('/scan')) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Scanning...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Scan Started',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4' }
                    });
                    
                    // Temporarily update UI to show scanning state
                    const viewerBox = form.closest('.viewer-box') || document.querySelector('.viewer-box.danger-box');
                    if (viewerBox) {
                        viewerBox.innerHTML = `
                            <div class="viewer-label"><i class="fa-solid fa-fire me-1"></i> Grad-CAM Heatmap</div>
                            <div class="d-flex flex-column align-items-center justify-content-center py-5 w-100" style="min-height:300px;">
                                <i class="fa-solid fa-spinner fa-spin fa-3x text-primary mb-2"></i>
                                <small class="text-muted">AI Scanning in progress...</small>
                            </div>
                        `;
                    }
                    
                    // Reload page after a short delay or let SSE handle it
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire({ icon: 'error', title: 'Scan Failed', text: data.message, confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Scan Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }
    });

    // ── Lightbox ───────────────────────────────────────
    function openLightbox(src) {
        const lb = document.getElementById('lightbox');
        const img = document.getElementById('lightboxImg');
        img.src = src;
        lb.style.display = 'flex';
    }
    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
    }

    // ── Session alerts ─────────────────────────────────
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", confirmButtonColor: '#16a34a', customClass: { popup: 'rounded-4' } });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Error', text: "{{ session('error') }}", confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
    @endif

    // ── Restore Application from Review Page ───────────
    const restoreBtn = document.getElementById('restoreReviewBtn');
    if (restoreBtn) {
        restoreBtn.addEventListener('click', async () => {
            const result = await Swal.fire({
                title: 'Restore Application?',
                text: "This will move the application back to the active review queue.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Yes, restore it!'
            });

            if (result.isConfirmed) {
                restoreBtn.disabled = true;
                restoreBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Restoring...';

                try {
                    const response = await fetch("{{ route('admin.restore', $application->id) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Restored!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                        restoreBtn.disabled = false;
                        restoreBtn.innerHTML = '<i class="fa-solid fa-trash-arrow-up me-1"></i> Restore Application';
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.' });
                    restoreBtn.disabled = false;
                    restoreBtn.innerHTML = '<i class="fa-solid fa-trash-arrow-up me-1"></i> Restore Application';
                }
            }
        });
    }
</script>
@endpush