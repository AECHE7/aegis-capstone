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

    /* AI Dark Card */
    .ai-card {
        background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
        border-radius: 16px;
        color: white;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.06);
    }

    .ai-card::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 180px; height: 180px;
        background: radial-gradient(circle, rgba(242,169,0,0.12) 0%, transparent 70%);
        border-radius: 50%;
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

    .risk-ring-track { fill: none; stroke: rgba(255,255,255,0.06); stroke-width: 10; }
    .risk-ring-fill  { fill: none; stroke-width: 10; stroke-linecap: round; transition: stroke-dashoffset 1.2s cubic-bezier(0.4, 0, 0.2, 1); }

    .risk-ring-center {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    /* Document Viewer */
    .viewer-box {
        background: #f8fafc;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .viewer-box.danger-box { border-color: rgba(239, 68, 68, 0.3); background: #fff5f5; }

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
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: white; border: none;
        padding: 12px; border-radius: 10px;
        font-weight: 700; font-size: 0.95rem;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
    }
    .btn-approve:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(22, 163, 74, 0.4); color: white; }

    .btn-reject {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: white; border: none;
        padding: 12px; border-radius: 10px;
        font-weight: 700; font-size: 0.95rem;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    .btn-reject:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(220, 38, 38, 0.4); color: white; }

    /* Applicant info card */
    .applicant-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
    }

    /* Info chip */
    .info-chip { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; background: #f1f5f9; font-size: 0.78rem; color: #475569; font-weight: 500; }

    /* Lightbox */
    .img-zoomable { cursor: zoom-in; }
</style>
@endpush

@section('content')

@php
    $hasAiResult = $application->document && $application->document->aiResult;
    $isScanning  = $hasAiResult && $application->document->aiResult->classification === 'scanning';
    $isFailed    = $hasAiResult && $application->document->aiResult->classification === 'failed';

    $fraudScore  = $hasAiResult && !$isScanning && !$isFailed ? $application->document->aiResult->fraud_probability : 0;
    $riskColor   = $fraudScore >= 70 ? '#ef4444' : ($fraudScore >= 40 ? '#f59e0b' : '#22c55e');
    $riskLabel   = $fraudScore >= 70 ? 'HIGH RISK' : ($fraudScore >= 40 ? 'MODERATE RISK' : 'LOW RISK');
    $riskClass   = $fraudScore >= 70 ? 'danger' : ($fraudScore >= 40 ? 'warning' : 'success');

    // Ring math — circumference of r=60 circle = 2π×60 ≈ 376.99
    $circumference = 376.99;
    $dashOffset = $circumference - ($fraudScore / 100) * $circumference;
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
                <span class="badge rounded-pill px-3 py-1" style="background:#f1f5f9;color:#475569;font-size:0.75rem;font-weight:700;">APP-{{ $application->id }}</span>
                <h5 class="fw-bold mb-0 text-dark">{{ $application->program_name }}</h5>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="info-chip"><i class="fa-solid fa-user text-primary"></i> {{ $application->user->name ?? 'Unknown' }}</span>
                <span class="info-chip"><i class="fa-solid fa-id-card text-primary"></i> {{ $application->user->profile?->clsu_id_number ?? 'N/A' }}</span>
                <span class="info-chip"><i class="fa-solid fa-graduation-cap text-primary"></i> {{ $application->user->profile?->course ?? 'N/A' }} — {{ $application->user->profile?->year_level ?? 'N/A' }}</span>
                <span class="info-chip"><i class="fa-solid fa-star text-warning"></i> GWA: <strong>{{ $application->gwa }}</strong></span>
            </div>
        </div>
        <div>
            @if($application->status == 'Pending')
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

        {{-- AI Card --}}
        <div class="ai-card p-4 mb-3">
            <div class="text-center mb-3">
                <div style="font-size:0.65rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.4);" class="mb-2">
                    <i class="fa-solid fa-microchip me-1 text-info"></i> A.E.G.I.S. Deep Learning Analysis
                </div>
            </div>

            @if($isScanning)
                <div class="text-center py-4">
                    <i class="fa-solid fa-circle-notch fa-spin fa-4x text-info mb-3"></i>
                    <p class="text-white-50 small mb-2">ELA + ResNet-50 analysis running...<br>Page will auto-refresh.</p>
                    <form action="{{ route('admin.scan', $application->id) }}" method="POST" class="d-inline-block">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-info rounded-pill px-3 mt-1" style="font-size: 0.72rem; border-color: rgba(0, 212, 255, 0.4); color: #00d4ff;">
                            <i class="fa-solid fa-arrow-rotate-right me-1"></i> Force Restart Scan
                        </button>
                    </form>
                    <script>
                        setTimeout(() => {
                            // Only reload if not submitting
                            location.reload();
                        }, 5000);
                    </script>
                </div>
            @elseif($isFailed)
                <div class="text-center py-4">
                    <i class="fa-solid fa-triangle-exclamation fa-4x text-danger mb-3"></i>
                    <p class="text-white-50 small mb-3">AI Scan failed.<br>The document file might be missing from the server disk (wiped during deployment updates) or the AI service returned an error.</p>
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
                            id="riskRing"/>
                    </svg>
                    <div class="risk-ring-center">
                        <div style="font-size:2rem;font-weight:800;font-family:'Poppins',sans-serif;color:{{ $riskColor }};line-height:1;">{{ $fraudScore }}</div>
                        <div style="font-size:0.75rem;color:rgba(255,255,255,0.4);">% fraud prob.</div>
                    </div>
                </div>

                <div class="text-center mb-3">
                    <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: {{ $riskColor }}22; color: {{ $riskColor }}; border: 1px solid {{ $riskColor }}44; font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $riskLabel }}
                    </span>
                </div>

                <div class="rounded-3 p-3 small" style="background: rgba(255,255,255,0.05);">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color:rgba(255,255,255,0.4);">Architecture</span>
                        <span class="fw-semibold">ResNet-50 CNN</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color:rgba(255,255,255,0.4);">Preprocessing</span>
                        <span class="fw-semibold">Error Level Analysis</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:rgba(255,255,255,0.4);">Classification</span>
                        <span class="fw-semibold" style="color:{{ $riskColor }};">{{ ucfirst($application->document->aiResult->classification) }}</span>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <div style="width:72px;height:72px;border-radius:18px;background:rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="fa-solid fa-file-shield fa-2x" style="color:rgba(255,255,255,0.3);"></i>
                    </div>
                    <p class="mb-3" style="color:rgba(255,255,255,0.4);font-size:0.85rem;">Document awaiting forensic verification.</p>
                    <form action="{{ route('admin.scan', $application->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-info text-dark fw-bold w-100 rounded-3">
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

                @if($application->status == 'Pending' || $application->status == 'Under Review')
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
                                <a href="{{ asset($field->field_value) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" style="border-radius: 6px; font-size: 0.75rem;">
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
                @if($application->document)
                    <a href="{{ route('admin.document.download', $application->document->id) }}"
                       class="btn btn-sm btn-light fw-semibold rounded-pill px-3"
                       style="font-size:0.78rem;border:1px solid #e2e8f0;white-space:nowrap;">
                        <i class="fa-solid fa-download me-1"></i> Download Original
                    </a>
                @endif
            </div>

            <div class="row g-3">
                {{-- Original --}}
                <div class="col-md-6">
                    <div class="viewer-box">
                        <div class="viewer-label"><i class="fa-solid fa-file me-1"></i> Original Document</div>
                        @if($application->document)
                            <img src="{{ asset($application->document->file_path) }}"
                                 alt="Original Student Document" class="img-zoomable"
                                 onerror="this.src='https://placehold.co/600x800?text=Image+Not+Found'"
                                 onclick="openLightbox(this.src)">
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 py-5 text-muted">
                                <i class="fa-solid fa-file-circle-xmark fa-3x mb-2 opacity-30"></i>
                                <small>No Document Found</small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Heatmap --}}
                <div class="col-md-6">
                    <div class="viewer-box danger-box">
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
                            <img src="{{ route('document.heatmap', $application->document->id) }}"
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
        const ring = document.getElementById('riskRing');
        if (ring) {
            setTimeout(() => {
                ring.style.strokeDashoffset = '{{ $dashOffset }}';
            }, 200);
        }
    });

    // ── Decision confirm ───────────────────────────────
    function confirmDecision(status) {
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
                document.getElementById('statusInput').value = status;
                document.getElementById('decisionForm').submit();
            }
        });
    }

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
</script>
@endpush