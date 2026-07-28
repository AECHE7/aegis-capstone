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
        background: var(--clsu-green-cta, #00754A);
        color: white; border: none;
        padding: 12px 24px; border-radius: var(--radius-pill, 50px);
        font-weight: 700; font-size: 0.95rem;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(0, 117, 74, 0.15);
    }
    .btn-approve:hover { background: var(--clsu-green, #0C4E2D); color: white; box-shadow: 0 8px 24px rgba(0, 117, 74, 0.25); }
    .btn-approve:active { transform: scale(0.95); }

    .btn-reject {
        background: #c82014; /* Crimson accent */
        color: white; border: none;
        padding: 12px 24px; border-radius: var(--radius-pill, 50px);
        font-weight: 700; font-size: 0.95rem;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(200, 32, 20, 0.15);
    }
    .btn-reject:hover { background: #991b1b; color: white; box-shadow: 0 8px 24px rgba(200, 32, 20, 0.25); }
    .btn-reject:active { transform: scale(0.95); }

    .btn-return {
        background: #d97706;
        color: white; border: none;
        padding: 12px 24px; border-radius: var(--radius-pill, 50px);
        font-weight: 700; font-size: 0.95rem;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.15);
    }
    .btn-return:hover { background: #b45309; color: white; box-shadow: 0 8px 24px rgba(217, 119, 6, 0.25); }
    .btn-return:active { transform: scale(0.95); }

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
                <span class="info-chip"><i class="fa-solid fa-star text-warning"></i> GWA: <strong class="monospace-data">{{ $application->gwa !== null ? number_format($application->gwa, 2) : 'N/A' }}</strong></span>
            </div>
        </div>
        <div>
            @if($application->trashed())
                @if($application->forfeit_reason)
                    <span class="status-badge bg-dark text-white" style="background-color: #475569 !important;"><i class="fa-solid fa-user-slash"></i> Forfeited</span>
                @else
                    <span class="status-badge bg-secondary text-white"><i class="fa-solid fa-ban"></i> Cancelled</span>
                @endif
            @elseif($application->status == 'Pending')
                <span class="status-badge pending"><i class="fa-solid fa-hourglass-half"></i> Pending</span>
            @elseif($application->status == 'Under Review')
                <span class="status-badge review"><i class="fa-solid fa-magnifying-glass"></i> Under Review</span>
            @elseif($application->status == 'Approved')
                <span class="status-badge approved"><i class="fa-solid fa-check"></i> Approved</span>
            @elseif($application->status == 'Returned')
                <span class="status-badge bg-warning text-dark border border-warning border-opacity-50"><i class="fa-solid fa-reply"></i> Returned</span>
            @else
                <span class="status-badge rejected"><i class="fa-solid fa-times"></i> Rejected</span>
            @endif
        </div>
    </div>
</div>

{{-- Main 2-column layout (Document viewer first on mobile, on the right on desktop) --}}
<div class="row g-4">

    {{-- LEFT: AI Panel + Decision --}}
    <div class="col-lg-4 order-last order-lg-first">

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
                            <div id="syncFallbackContainer-{{ $doc->id }}" class="mt-3 p-3 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-20 d-none text-start" style="font-size:0.75rem;">
                                <div class="fw-bold text-warning mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i> Queue taking longer than usual...</div>
                                <span class="text-muted">The background queue worker may be offline on Render. You can run the scan synchronously in the foreground web process instead:</span>
                                <button type="button" class="btn btn-xs btn-warning fw-bold text-dark mt-2 w-100" onclick="runSyncScan('{{ $doc->id }}')">
                                    <i class="fa-solid fa-bolt me-1"></i> Run Scan Synchronously
                                </button>
                            </div>
                            <script>
                                if (typeof window.scanStatusPoller === 'undefined') {
                                    let pollSeconds = 0;
                                    window.scanStatusPoller = setInterval(async () => {
                                        pollSeconds += 3;
                                        if (pollSeconds >= 9) {
                                            const leftFallback = document.getElementById("syncFallbackContainer-{{ $doc->id }}");
                                            const rightFallback = document.getElementById("syncFallbackRight-{{ $doc->id }}");
                                            if (leftFallback) leftFallback.classList.remove('d-none');
                                            if (rightFallback) rightFallback.classList.remove('d-none');
                                        }
                                        try {
                                            const res = await fetch("{{ route('admin.scanStatus', $application->id) }}", {
                                                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                                            });
                                            const data = await res.json();
                                            if (data && data.success && !data.is_scanning) {
                                                clearInterval(window.scanStatusPoller);
                                                window.scanStatusPoller = undefined;
                                                location.reload();
                                            }
                                        } catch (err) {
                                            console.error('Scan status check error:', err);
                                        }
                                    }, 3000);
                                }
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
                                <div style="font-size:0.75rem;color:var(--text-main);opacity:0.6;">% fraud prob. <i class="fa-solid fa-circle-info ms-1 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="The probability (0-100%) that this academic document has been digitally altered or tampered with."></i></div>
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
                                <span class="fw-semibold">TruFor + CAT-Net Dual-CNN <i class="fa-solid fa-circle-info ms-1 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Dual-CNN architecture combining TruFor (noiseprint-based splicing detection) and CAT-Net (JPEG compression artifact tracing) for advanced fraud detection."></i></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span style="color:var(--text-main);opacity:0.7;">Preprocessing</span>
                                <span class="fw-semibold">Error Level Analysis <i class="fa-solid fa-circle-info ms-1 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="ELA (Error Level Analysis) highlights pixel discrepancies by resaving the image at a known compression rate and mapping the error density to spot edits."></i></span>
                            </div>
                            @if(!empty($doc->aiResult->detected_software))
                            <div class="d-flex justify-content-between mb-2">
                                <span style="color:var(--text-main);opacity:0.7;">Software Detected</span>
                                <span class="badge bg-danger text-white fw-bold px-2 py-1" style="font-size:0.72rem;">
                                    <i class="fa-solid fa-laptop-code me-1"></i> {{ $doc->aiResult->detected_software }}
                                </span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between">
                                <span style="color:var(--text-main);opacity:0.7;">Classification</span>
                                <span class="fw-semibold" style="color:{{ $riskColor }};">{{ ucfirst($doc->aiResult->classification) }}</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <form action="{{ route('admin.scan', $application->id) }}" method="POST" class="d-inline-block w-100">
                                @csrf
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold text-muted mb-1" for="scanModeSelect-{{ $doc->id }}" style="font-size: 0.72rem;"><i class="fa-solid fa-sliders me-1"></i> Scan Mode</label>
                                    <select name="mode" id="scanModeSelect-{{ $doc->id }}" class="form-select form-select-sm rounded-3" style="font-size: 0.78rem; border-color: var(--border-color); background-color: var(--clsu-bg); color: var(--text-main);">
                                        <option value="standard" selected>Enhanced Scan (V2 - Standard)</option>
                                        <option value="deep">Deep Forensics (V3 - Heatmaps/Crops)</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-info w-100 rounded-3 py-2" style="font-size: 0.78rem; border-color: rgba(0, 212, 255, 0.4); color: #00d4ff; background: rgba(0, 212, 255, 0.05);">
                                    <i class="fa-solid fa-arrow-rotate-right me-1"></i> Re-run AI Forensic Scan
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div style="width:72px;height:72px;border-radius:12px;background:var(--clsu-bg);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;border: 1px solid var(--border-color);">
                                <i class="fa-solid fa-file-shield fa-2x text-muted" style="opacity:0.6;"></i>
                            </div>
                            <p class="mb-3 text-muted" style="font-size:0.85rem;">Document awaiting forensic verification.</p>
                            <form action="{{ route('admin.scan', $application->id) }}" method="POST">
                                @csrf
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-bold text-muted mb-1" for="scanModeSelectInitial-{{ $doc->id }}" style="font-size: 0.72rem;"><i class="fa-solid fa-sliders me-1"></i> Scan Mode</label>
                                    <select name="mode" id="scanModeSelectInitial-{{ $doc->id }}" class="form-select form-select-sm rounded-3" style="font-size: 0.78rem; border-color: var(--border-color); background-color: var(--clsu-bg); color: var(--text-main);">
                                        <option value="standard" selected>Enhanced Scan (V2 - Standard)</option>
                                        <option value="deep">Deep Forensics (V3 - Heatmaps/Crops)</option>
                                    </select>
                                </div>
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

                {{-- Forensic Verification Matrix (snapWONDERS style) --}}
                <div class="card p-4 mb-3 border-0 shadow-sm" style="border-radius:16px; background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                    <h6 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-list-check text-primary me-2"></i> Forensic Integrity Check</h6>
                    
                    <div class="d-flex flex-column gap-2 text-start">
                        {{-- Category: Origin & Metadata --}}
                        <div class="pb-2 border-bottom">
                            <span class="text-xs font-bold text-uppercase text-muted" style="font-size:0.68rem; letter-spacing:0.5px;">Origin &amp; Metadata</span>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span class="small text-muted">Original Capture Origin</span>
                                @if(in_array('exif_metadata_cleaned', $doc->aiResult->anomaly_indicators ?? []))
                                    <span class="badge bg-warning text-dark px-2 py-1" style="font-size:0.7rem;">Metadata Stripped</span>
                                @else
                                    <span class="badge bg-success text-white px-2 py-1" style="font-size:0.7rem;">Verifiable</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span class="small text-muted">Editing Software Signature</span>
                                @if(!empty($doc->aiResult->detected_software))
                                    <span class="badge bg-danger text-white px-2 py-1" style="font-size:0.7rem;"><i class="fa-solid fa-laptop-code me-1"></i> {{ $doc->aiResult->detected_software }}</span>
                                @else
                                    <span class="badge bg-success text-white px-2 py-1" style="font-size:0.7rem;">None Detected</span>
                                @endif
                            </div>
                        </div>

                        {{-- Category: Pixel Integrity --}}
                        <div class="pb-2 border-bottom">
                            <span class="text-xs font-bold text-uppercase text-muted" style="font-size:0.68rem; letter-spacing:0.5px;">Pixel-Level Forensics</span>
                            
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span class="small text-muted">Copy-Move (Clone Stamp)</span>
                                @if(in_array('clone_stamp_detected', $doc->aiResult->anomaly_indicators ?? []))
                                    <span class="badge bg-danger text-white px-2 py-1" style="font-size:0.7rem;">Anomalous (Clone)</span>
                                @else
                                    <span class="badge bg-success text-white px-2 py-1" style="font-size:0.7rem;">Clean</span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span class="small text-muted">Compression Resampling</span>
                                @if(in_array('resampling_traces_detected', $doc->aiResult->anomaly_indicators ?? []) || in_array('catnet_dct_compression_anomaly', $doc->aiResult->anomaly_indicators ?? []))
                                    <span class="badge bg-danger text-white px-2 py-1" style="font-size:0.7rem;">Resampled (Edited)</span>
                                @else
                                    <span class="badge bg-success text-white px-2 py-1" style="font-size:0.7rem;">Clean</span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span class="small text-muted">Noise Variance</span>
                                @if(in_array('trufor_noiseprint_anomaly', $doc->aiResult->anomaly_indicators ?? []) || in_array('deep_analysis_multiple_high_severity_regions', $doc->aiResult->anomaly_indicators ?? []))
                                    <span class="badge bg-danger text-white px-2 py-1" style="font-size:0.7rem;">Inconsistent Noise</span>
                                @else
                                    <span class="badge bg-success text-white px-2 py-1" style="font-size:0.7rem;">Uniform</span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span class="small text-muted">Digital Whiteout (LAB)</span>
                                @if(in_array('digital_whiteout_box_detected', $doc->aiResult->anomaly_indicators ?? []))
                                    <span class="badge bg-danger text-white px-2 py-1" style="font-size:0.7rem;">Detected Mask</span>
                                @else
                                    <span class="badge bg-success text-white px-2 py-1" style="font-size:0.7rem;">Clean</span>
                                @endif
                            </div>
                        </div>

                        {{-- Category: OCR & Grade Integrity --}}
                        <div>
                            <span class="text-xs font-bold text-uppercase text-muted" style="font-size:0.68rem; letter-spacing:0.5px;">GWA &amp; OCR Verification</span>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span class="small text-muted">GWA Match Status</span>
                                @php
                                    $gwaMismatch = false;
                                    $extracted = null;
                                    if (isset($doc->aiResult->deep_analysis_report['extracted_gwa'])) {
                                        $extracted = $doc->aiResult->deep_analysis_report['extracted_gwa'];
                                        if (!empty($application->gwa)) {
                                            $gwaMismatch = abs((float)$application->gwa - (float)$extracted) > 0.01;
                                        }
                                    }
                                @endphp
                                @if($extracted === null)
                                    <span class="badge bg-secondary text-white px-2 py-1" style="font-size:0.7rem;">No OCR Data</span>
                                @elseif($gwaMismatch)
                                    <span class="badge bg-danger text-white px-2 py-1" style="font-size:0.7rem;" data-bs-toggle="tooltip" title="Declared: {{ $application->gwa }}, Extracted: {{ $extracted }}"><i class="fa-solid fa-triangle-exclamation me-1"></i> Mismatch</span>
                                @else
                                    <span class="badge bg-success text-white px-2 py-1" style="font-size:0.7rem;" data-bs-toggle="tooltip" title="GWA Match Checked"><i class="fa-solid fa-check me-1"></i> Verified</span>
                                @endif
                            </div>
                        </div>
                    </div>
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
                    @if($application->forfeit_reason)
                        <div class="alert alert-danger text-center rounded-3 mb-3 small" style="border: none; background: #fef2f2; color: #991b1b;">
                            <i class="fa-solid fa-user-slash me-1"></i> <strong>Forfeited / Backed Out Scholarship</strong><br>
                            This scholar backed out / forfeited their scholarship grant.<br>
                            <strong>Reason:</strong> <em>"{{ $application->forfeit_reason }}"</em>
                        </div>
                    @else
                        <div class="alert alert-warning text-center rounded-3 mb-3 small" style="border: none; background: #fffbeb; color: #b45309;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> <strong>Cancelled Application</strong><br>
                            This application was cancelled by the student and is soft-deleted.
                        </div>
                    @endif
                    <button type="button" id="restoreReviewBtn" class="btn btn-success fw-bold w-100 py-2 text-white" style="border-radius:10px;">
                        <i class="fa-solid fa-trash-arrow-up me-1"></i> Restore Application
                    </button>
                @elseif($application->status == 'Pending' || $application->status == 'Under Review')
                    <div class="d-flex flex-column gap-2 mobile-sticky-action-bar">
                        <div class="d-flex gap-2 w-100">
                            <button type="button" class="btn-approve w-50 py-2.5" onclick="confirmDecision('Approved')">
                                <i class="fa-solid fa-check-circle me-1"></i> Approve
                            </button>
                            <button type="button" class="btn-reject w-50 py-2.5" onclick="confirmDecision('Rejected')">
                                <i class="fa-solid fa-times-circle me-1"></i> Reject
                            </button>
                        </div>
                        <button type="button" class="btn-return w-100 py-2.5" onclick="confirmDecision('Returned')">
                            <i class="fa-solid fa-reply me-1"></i> Return for Document Correction
                        </button>
                    </div>
                    <input type="hidden" name="status" id="statusInput">
                @else
                    <div class="alert mb-0 text-center fw-bold rounded-3"
                         style="background: {{ $application->status == 'Approved' ? '#dcfce7' : ($application->status == 'Returned' ? '#fffbeb' : '#fee2e2') }}; color: {{ $application->status == 'Approved' ? '#15803d' : ($application->status == 'Returned' ? '#b45309' : '#b91c1c') }}; border: none; font-size: 0.875rem;">
                        <i class="fa-solid fa-lock me-1"></i> Application is {{ $application->status == 'Returned' ? 'returned for document correction' : 'finalized as ' . $application->status }}.
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
                            @php
                                $isFieldFile = Str::startsWith($field->field_value, 'uploads/') || 
                                              (Str::startsWith($field->field_value, 'http') && 
                                               collect(['.pdf', '.png', '.jpg', '.jpeg', '.docx', '.gif', '.webp', '.xlsx', '.xls', '.csv'])->contains(fn($ext) => Str::endsWith(strtolower($field->field_value), $ext)));
                            @endphp
                            @if($isFieldFile)
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

        {{-- Staff Notes Card --}}
        <div class="card p-4 mt-3 border-0 shadow-sm" style="border-radius:16px;">
            <label class="form-label fw-bold mb-1 text-dark" for="staffNotesTextarea"><i class="fa-solid fa-note-sticky text-warning me-2"></i> Staff Notes (Private)</label>
            <p class="text-muted mb-3" style="font-size:0.75rem;">Internal review notes. Student cannot see this. Auto-saves on focus out.</p>
            <div class="position-relative">
                <textarea id="staffNotesTextarea" name="admin_notes" class="form-control" rows="4" 
                          placeholder="Write comments, cross-referencing notes, or verification details here..." 
                          style="resize:none; font-size:0.82rem; border-radius:10px;"
                          onblur="saveStaffNotes(this.value)">{{ $application->admin_notes }}</textarea>
                <div id="notesSaveIndicator" class="position-absolute bottom-0 end-0 mb-2 me-2 text-success d-none" style="font-size:0.72rem; pointer-events: none;">
                    <i class="fa-solid fa-circle-check"></i> Saved
                </div>
            </div>
        </div>

        {{-- Scholarship History Card --}}
        <div class="card p-4 mt-3 border-0 shadow-sm" style="border-radius:16px;">
            <h6 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-clock-rotate-left text-info me-2"></i> Scholarship History</h6>
            @php
                $histApprovedCount = $history->where('status', 'Approved')->count() + ($application->status === 'Approved' ? 1 : 0);
                $maxRenew = $scholarship->max_renewals ?? 4;
            @endphp
            <p class="text-muted mb-3" style="font-size:0.75rem;">
                Student has <strong class="text-dark">{{ $histApprovedCount }} approved</strong> application(s) of {{ $maxRenew }} max renewals.
            </p>
            
            @if($history->isEmpty())
                <div class="text-center py-3 text-muted small bg-light rounded-3">
                    No prior applications found for this scholarship.
                </div>
            @else
                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-sm mb-0" style="font-size:0.78rem;">
                        <thead>
                            <tr>
                                <th scope="col">Term</th>
                                <th class="text-center" scope="col">GWA</th>
                                <th class="text-end" scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $histApp)
                            <tr>
                                <td class="text-muted" style="font-size: 0.72rem;">
                                    {{ $histApp->academicTerm?->semester ?? 'N/A' }}<br>
                                    <small>{{ $histApp->academicTerm?->academic_year ?? 'N/A' }}</small>
                                </td>
                                <td class="text-center monospace-data" style="vertical-align: middle;">{{ $histApp->gwa }}</td>
                                <td class="text-end" style="vertical-align: middle;">
                                    @if($histApp->status === 'Approved')
                                        <span class="badge bg-success text-success bg-opacity-10 border border-success border-opacity-20 rounded-pill px-2" style="font-size:0.68rem;">Approved</span>
                                    @elseif($histApp->status === 'Rejected')
                                        <span class="badge bg-danger text-danger bg-opacity-10 border border-danger border-opacity-20 rounded-pill px-2" style="font-size:0.68rem;">Rejected</span>
                                    @elseif($histApp->status === 'Under Review')
                                        <span class="badge bg-info text-info bg-opacity-10 border border-info border-opacity-20 rounded-pill px-2" style="font-size:0.68rem;">Review</span>
                                    @else
                                        <span class="badge bg-secondary text-secondary bg-opacity-10 border border-secondary border-opacity-20 rounded-pill px-2" style="font-size:0.68rem;">{{ $histApp->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- RIGHT: Document Viewer --}}
    <div class="col-lg-8 order-first order-lg-last">
        <div class="card p-4 h-100">
            @if($application->documents->isEmpty())
                <div class="text-center py-5 my-auto">
                    <div style="width:80px;height:80px;border-radius:50%;background:var(--clsu-bg);display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--border-color);" class="mb-3">
                        <i class="fa-solid fa-folder-open fa-2x text-muted" style="opacity:0.6;"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No Documents Uploaded</h6>
                    <p class="text-muted small mb-0">This program does not require any document submissions.</p>
                </div>
            @else
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
                        $isPdf       = str_ends_with(strtolower($doc->original_name), '.pdf') || str_ends_with(strtolower($doc->file_path), '.pdf');
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
                                    @if($isPdf)
                                        <iframe src="{{ route('document.view', $doc->id) }}"
                                                style="width: 100%; height: 450px; border: none; border-radius: 8px;"></iframe>
                                    @else
                                        <img src="{{ route('document.view', $doc->id) }}"
                                             alt="Original Student Document" class="img-zoomable"
                                             role="button"
                                             tabindex="0"
                                             aria-label="Click or press Enter to enlarge image"
                                             onerror="this.src='https://placehold.co/600x800?text=Image+Not+Found'"
                                             onclick="openLightbox(this.src)"
                                             onkeydown="if(event.key==='Enter' || event.key===' ') { event.preventDefault(); openLightbox(this.src); }">
                                    @endif
                                </div>
                            </div>
                                            {{-- Heatmap / Forensic Toolkit --}}
                            <div class="col-md-6">
                                <div class="viewer-box {{ $riskClass }}-box h-100 flex-column d-flex align-items-stretch" style="min-height: 520px;">
                                    <div class="viewer-label d-flex justify-content-between align-items-center w-100 px-3 py-2" style="position:static; transform:none; border-radius:0; background:#334155; color: white;">
                                        <span><i class="fa-solid fa-flask-vial me-1"></i> Forensic Map Toolkit</span>
                                        <span class="badge bg-{{ $riskClass }}" style="font-size: 0.65rem;">{{ $riskLabel }}</span>
                                    </div>
                                    @if($isScanning)
                                        <div class="d-flex flex-column align-items-center justify-content-center py-5 w-100 flex-grow-1" style="min-height:300px;">
                                            <i class="fa-solid fa-spinner fa-spin fa-3x text-primary mb-2"></i>
                                            <small class="text-muted">AI Scanning in progress...</small>
                                            <div id="syncFallbackRight-{{ $doc->id }}" class="mt-3 p-3 rounded bg-warning bg-opacity-10 border border-warning border-opacity-20 d-none text-start mx-3" style="font-size:0.75rem; max-width: 320px;">
                                                <div class="fw-bold text-warning mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i> Queue delayed...</div>
                                                <span class="text-muted">Background job queue not responding. Force run the scan in your browser window:</span>
                                                <button type="button" class="btn btn-sm btn-warning fw-bold text-dark mt-2 w-100" onclick="runSyncScan('{{ $doc->id }}')">
                                                    <i class="fa-solid fa-bolt me-1"></i> Force Sync Scan
                                                </button>
                                            </div>
                                        </div>
                                    @elseif($isFailed)
                                        <div class="d-flex flex-column align-items-center justify-content-center py-5 w-100 flex-grow-1" style="min-height:300px;">
                                            <i class="fa-solid fa-triangle-exclamation fa-3x text-danger mb-2 opacity-50"></i>
                                            <small class="text-muted">Scan failed. Please retry.</small>
                                        </div>
                                    @elseif($hasAiResult)
                                        @php
                                            $deepReport = $doc->aiResult->deep_analysis_report ?? null;
                                            $layers = $deepReport['visualizations']['layer_heatmaps'] ?? [];
                                            $compositeHeatmap = $deepReport['visualizations']['composite_heatmap_base64'] ?? null;
                                            $originalPageBase64 = $deepReport['visualizations']['original_page_base64'] ?? null;
                                            $originalUrl = $originalPageBase64 ? route('document.originalPage', $doc->id) : route('document.view', $doc->id);
                                        @endphp
                                        
                                        {{-- Tab buttons --}}
                                        <div class="bg-light border-bottom p-2 d-flex gap-1.5 overflow-auto forensic-tabs-container" style="scrollbar-width: thin; border-radius: 0;">
                                            <button type="button" class="btn btn-xs btn-outline-secondary active py-1 px-3 text-nowrap rounded-pill forensic-tab-btn" 
                                                    onclick="switchForensicTab(this, 'original', '{{ $doc->id }}')" style="font-size:0.7rem; font-weight:600;">
                                                Original
                                            </button>
                                            @if($compositeHeatmap || $doc->aiResult->heatmap_path)
                                                <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-3 text-nowrap rounded-pill forensic-tab-btn" 
                                                        data-src="{{ route('document.heatmap', $doc->id) }}"
                                                        onclick="switchForensicTab(this, 'composite', '{{ $doc->id }}')" style="font-size:0.7rem; font-weight:600;">
                                                    Annotated (CAM)
                                                </button>
                                            @endif
                                            
                                            @if(isset($layers['ela_detailed']))
                                                <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-3 text-nowrap rounded-pill forensic-tab-btn" 
                                                        data-src="{{ route('document.forensicLayer', [$doc->id, 'ela_detailed']) }}"
                                                        onclick="switchForensicTab(this, 'ela', '{{ $doc->id }}')" style="font-size:0.7rem; font-weight:600;">
                                                    ELA Map
                                                </button>
                                            @endif
                                            @if(isset($layers['noise_consistency']))
                                                <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-3 text-nowrap rounded-pill forensic-tab-btn" 
                                                        data-src="{{ route('document.forensicLayer', [$doc->id, 'noise_consistency']) }}"
                                                        onclick="switchForensicTab(this, 'noise', '{{ $doc->id }}')" style="font-size:0.7rem; font-weight:600;">
                                                    Noise Map
                                                </button>
                                            @endif
                                            @if(isset($layers['edge_consistency']))
                                                <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-3 text-nowrap rounded-pill forensic-tab-btn" 
                                                        data-src="{{ route('document.forensicLayer', [$doc->id, 'edge_consistency']) }}"
                                                        onclick="switchForensicTab(this, 'edge', '{{ $doc->id }}')" style="font-size:0.7rem; font-weight:600;">
                                                    Edge Map
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Interactive Image Viewer Box with CSS Opacity Overlay --}}
                                        <div class="position-relative flex-grow-1 bg-dark d-flex align-items-center justify-content-center p-3 overflow-hidden" 
                                             style="min-height: 380px;">
                                            
                                            <!-- Base Original Image -->
                                            <img id="baseImage-{{ $doc->id }}" src="{{ $originalUrl }}" 
                                                 class="img-fluid object-contain w-100 h-100 img-zoomable" style="max-height: 380px; object-fit: contain;" alt="Forensic Analysis Base"
                                                 onclick="openLightbox(this.src)">

                                            <!-- Forensic Layer Overlay -->
                                            <img id="overlayImage-{{ $doc->id }}" src="" 
                                                 class="img-fluid object-contain w-100 h-100 position-absolute" 
                                                 style="max-height: 380px; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.5; mix-blend-mode: normal; pointer-events: none; display: none;" 
                                                 alt="Forensic Overlay Layer">
                                        </div>

                                        {{-- Opacity slider controls --}}
                                        <div class="p-3 bg-light border-top" id="opacityControls-{{ $doc->id }}" style="display: none;">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="small fw-bold text-muted"><i class="fa-solid fa-circle-half-stroke me-1"></i> Layer Opacity</span>
                                                <span class="small fw-semibold text-primary" id="opacityVal-{{ $doc->id }}">50%</span>
                                            </div>
                                            <input type="range" class="form-range" id="opacitySlider-{{ $doc->id }}" min="0" max="100" value="50"
                                                   oninput="adjustOverlayOpacity(this.value, '{{ $doc->id }}')">
                                        </div>
                                    @elseif($isPdf)
                                        <div class="d-flex flex-column align-items-center justify-content-center py-5 w-100 flex-grow-1" style="min-height:300px; text-align: center; padding: 20px;">
                                            <i class="fa-solid fa-file-pdf fa-3x text-success mb-2 opacity-50"></i>
                                            <small class="text-success fw-bold">PDF Document Bypassed AI Image Scan</small>
                                            <span class="text-muted mt-2 d-block" style="font-size: 0.72rem; line-height: 1.4; max-width: 250px; margin: 0 auto;">
                                                ELA pixel compression and ResNet-50 visual scan are only applicable to rasterized image formats (PNG, JPG, WebP).
                                            </span>
                                        </div>
                                    @else
                                        <div class="d-flex flex-column align-items-center justify-content-center py-5 w-100 flex-grow-1" style="min-height:300px;">
                                            <i class="fa-solid fa-robot fa-3x text-danger mb-2 opacity-25"></i>
                                            <small class="text-muted">Awaiting AI scan execution</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Suspect Regions Crops Grid --}}
                        @if($hasAiResult && !empty($doc->aiResult->deep_analysis_report['region_crops']))
                        <div class="mt-3 p-3 rounded-3" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark small"><i class="fa-solid fa-crop-simple me-1 text-danger"></i> Suspect Regions Crop Grid</span>
                                <span class="badge bg-danger text-white" style="font-size: 0.65rem;">{{ count($doc->aiResult->deep_analysis_report['region_crops']) }} Anomalies Pinpointed</span>
                            </div>
                            
                            <div class="row g-2">
                                @foreach($doc->aiResult->deep_analysis_report['region_crops'] as $crop)
                                    <div class="col-6 col-sm-4 col-md-3">
                                        <div class="border rounded bg-white p-1 text-center cursor-pointer hover-shadow transition" 
                                             onclick="zoomToRegion('{{ $crop['crop_base64'] }}', '{{ $crop['x'] }}', '{{ $crop['y'] }}', '{{ $crop['w'] }}', '{{ $crop['h'] }}', '{{ $crop['detector'] }}', '{{ $crop['severity'] }}')"
                                             style="font-size: 0.7rem; border-color: var(--border-color);">
                                            <div style="height: 70px;" class="d-flex align-items-center justify-content-center overflow-hidden bg-light rounded">
                                                <img src="data:image/png;base64,{{ $crop['crop_base64'] }}" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                            </div>
                                            <div class="mt-1 d-flex align-items-center justify-content-between px-1">
                                                <span class="text-xs fw-semibold">{{ $crop['detector'] }}</span>
                                                <span class="badge bg-{{ $crop['severity'] === 'high' ? 'danger' : 'warning' }} text-white" style="font-size:0.55rem; padding: 2px 4px;">{{ $crop['severity'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @elseif($hasAiResult && !empty($doc->aiResult->cropped_patch_data))
                        <div class="mt-3 p-3 rounded-3" style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.3);">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-danger small"><i class="fa-solid fa-crop-simple me-1"></i> Tampered Region Micro-Crop Zoom Preview</span>
                                <span class="badge bg-danger text-white" style="font-size: 0.68rem;">Target Area Zoom</span>
                            </div>
                            <div class="text-center bg-white p-2 rounded-2 border">
                                <img src="data:image/png;base64,{{ $doc->aiResult->cropped_patch_data }}" 
                                     alt="Tampered Patch Micro-Crop" class="img-fluid rounded img-zoomable" style="max-height: 150px;"
                                     onclick="openLightbox(this.src)">
                            </div>
                            <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">
                                <i class="fa-solid fa-magnifying-glass-plus text-danger me-1"></i> Zoomed-in crop pinpointing the specific anomalous cell/row patch detected by TruFor + CAT-Net Dual-CNN.
                            </small>
                        </div>
                        @endif
                    </div>
                @endforeach
            @endif

            {{-- Footer explanation --}}
            <div class="mt-4 rounded-3 p-3 small text-muted" style="background:#f8fafc;border:1px solid #e2e8f0;">
                <i class="fa-solid fa-circle-info text-primary me-1"></i>
                <strong>How to interpret:</strong> Hot/red areas on the Grad-CAM heatmap highlight pixels with anomalous ELA compression signatures — strong indicators of digital manipulation or pixel-level copy-paste forgery, as detected by the ResNet-50 CNN pipeline.
            </div>
        </div>
    </div>
</div>

{{-- Interactive Lightbox overlay --}}
<div id="lightbox" class="position-fixed inset-0" onclick="if(event.target===this) closeLightbox()"
     style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.92);backdrop-filter:blur(8px);z-index:9999;align-items:center;justify-content:center;flex-direction:column;">
    
    <!-- Lightbox Toolbar -->
    <div class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded-pill bg-dark border border-secondary shadow" onclick="event.stopPropagation()">
        <button type="button" class="btn btn-sm btn-outline-light rounded-circle" onclick="zoomLightbox(1.2)" title="Zoom In"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
        <button type="button" class="btn btn-sm btn-outline-light rounded-circle" onclick="zoomLightbox(0.8)" title="Zoom Out"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
        <button type="button" class="btn btn-sm btn-outline-light rounded-circle" onclick="rotateLightbox(90)" title="Rotate 90°"><i class="fa-solid fa-rotate-right"></i></button>
        <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="resetLightbox()" title="Reset View">Reset</button>
        <button type="button" class="btn btn-sm btn-danger rounded-circle ms-2" onclick="closeLightbox()" title="Close (ESC)"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="overflow-hidden d-flex align-items-center justify-content-center" style="max-width:92vw;max-height:82vh;position:relative;">
        <img id="lightboxImg" src="" alt="Enlarged view" style="max-width:90vw;max-height:80vh;object-fit:contain;border-radius:12px;box-shadow:0 24px 64px rgba(0,0,0,0.5);transition:transform 0.15s ease-out;cursor:grab;">
    </div>
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
        const returnBtn = document.querySelector('.btn-return');
        const remarksField = document.getElementById('evaluatorRemarks');
        
        const originalApprove = approveBtn ? approveBtn.innerHTML : '';
        const originalReject = rejectBtn ? rejectBtn.innerHTML : '';
        const originalReturn = returnBtn ? returnBtn.innerHTML : '';

        if (approveBtn) { approveBtn.disabled = true; }
        if (rejectBtn) { rejectBtn.disabled = true; }
        if (returnBtn) { returnBtn.disabled = true; }
        if (remarksField) { remarksField.disabled = true; }

        if (status === 'Approved' && approveBtn) {
            approveBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-1"></i> Saving...';
        } else if (status === 'Rejected' && rejectBtn) {
            rejectBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-1"></i> Saving...';
        } else if (status === 'Returned' && returnBtn) {
            returnBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-1"></i> Saving...';
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
                         style="background: ${data.status === 'Approved' ? '#dcfce7' : (data.status === 'Returned' ? '#fffbeb' : '#fee2e2')}; color: ${data.status === 'Approved' ? '#15803d' : (data.status === 'Returned' ? '#b45309' : '#b91c1c')}; border: none; font-size: 0.875rem;">
                        <i class="fa-solid fa-lock me-1"></i> Application is ${data.status === 'Returned' ? 'returned for document correction' : 'finalized as ' + data.status}.
                    </div>
                `;
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                if (approveBtn) { approveBtn.disabled = false; approveBtn.innerHTML = originalApprove; }
                if (rejectBtn) { rejectBtn.disabled = false; rejectBtn.innerHTML = originalReject; }
                if (returnBtn) { returnBtn.disabled = false; returnBtn.innerHTML = originalReturn; }
                if (remarksField) { remarksField.disabled = false; }
            }
        } catch (error) {
            console.error('Decision Submission Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.', confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
            if (approveBtn) { approveBtn.disabled = false; approveBtn.innerHTML = originalApprove; }
            if (rejectBtn) { rejectBtn.disabled = false; rejectBtn.innerHTML = originalReject; }
            if (returnBtn) { returnBtn.disabled = false; returnBtn.innerHTML = originalReturn; }
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

    // ── Interactive Lightbox Inspector State ──────────────
    let lbScale = 1;
    let lbRotation = 0;
    let lbTransX = 0;
    let lbTransY = 0;
    let isDraggingLb = false;
    let startX = 0, startY = 0;

    function applyLbTransform() {
        const img = document.getElementById('lightboxImg');
        if (img) {
            img.style.transform = `translate(${lbTransX}px, ${lbTransY}px) scale(${lbScale}) rotate(${lbRotation}deg)`;
        }
    }

    function zoomLightbox(factor) {
        lbScale = Math.min(Math.max(0.5, lbScale * factor), 5);
        applyLbTransform();
    }

    function rotateLightbox(deg) {
        lbRotation = (lbRotation + deg) % 360;
        applyLbTransform();
    }

    function resetLightbox() {
        lbScale = 1;
        lbRotation = 0;
        lbTransX = 0;
        lbTransY = 0;
        applyLbTransform();
    }

    function openLightbox(src) {
        const lb = document.getElementById('lightbox');
        const img = document.getElementById('lightboxImg');
        img.src = src;
        resetLightbox();
        lb.style.display = 'flex';
    }

    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
    }

    // ── Forensic Map Switcher & Opacity Slider ─────────
    const forensicState = {};

    function switchForensicTab(btn, layerKey, docId) {
        if (!forensicState[docId]) {
            forensicState[docId] = {
                currentLayer: 'original',
                opacity: 50
            };
        }

        // Deactivate all sibling buttons
        const container = btn.closest('.forensic-tabs-container');
        if (container) {
            container.querySelectorAll('.forensic-tab-btn').forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-secondary');
            });
        }
        btn.classList.add('active', 'btn-primary');
        btn.classList.remove('btn-outline-secondary');

        const overlayImg = document.getElementById('overlayImage-' + docId);
        const opacityCtrls = document.getElementById('opacityControls-' + docId);

        if (layerKey === 'original') {
            if (overlayImg) overlayImg.style.display = 'none';
            if (opacityCtrls) opacityCtrls.style.display = 'none';
            forensicState[docId].currentLayer = 'original';
        } else {
            const src = btn.dataset.src;
            if (overlayImg) {
                overlayImg.src = src;
                overlayImg.style.display = 'block';
            }
            if (opacityCtrls) opacityCtrls.style.display = 'block';
            forensicState[docId].currentLayer = layerKey;
            
            // Trigger slider update
            const slider = document.getElementById('opacitySlider-' + docId);
            if (slider) {
                adjustOverlayOpacity(slider.value, docId);
            }
        }
    }

    function adjustOverlayOpacity(val, docId) {
        const overlayImg = document.getElementById('overlayImage-' + docId);
        const opacityVal = document.getElementById('opacityVal-' + docId);
        if (overlayImg) {
            overlayImg.style.opacity = val / 100;
        }
        if (opacityVal) {
            opacityVal.textContent = val + '%';
        }
    }

    function zoomToRegion(cropBase64, x, y, w, h, detector, severity) {
        const src = 'data:image/png;base64,' + cropBase64;
        openLightbox(src);
        
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: `${detector} Anomaly Detected`,
            text: `Area severity: ${severity.toUpperCase()} (x: ${x}, y: ${y})`,
            showConfirmButton: false,
            timer: 4000
        });
    }

    async function runSyncScan(docId) {
        Swal.fire({
            title: 'Running Foreground AI Scan',
            text: 'Bypassing queue to run scan synchronously. Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            // Find selected mode from dropdowns (re-scan or initial)
            const modeSelect = document.getElementById('scanModeSelect-' + docId) || document.getElementById('scanModeSelectInitial-' + docId);
            const mode = modeSelect ? modeSelect.value : 'standard';

            const response = await fetch("{{ route('admin.scanSync', $application->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ mode: mode })
            });

            const data = await response.json();
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Scan Complete!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Scan Failed',
                    text: data.message || 'An error occurred during verification.'
                });
            }
        } catch (error) {
            console.error('Foreground scan failed:', error);
            Swal.fire({
                icon: 'error',
                title: 'Connection Error',
                text: 'An unexpected connection error occurred.'
            });
        }
    }

    // ESC key to close lightbox
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' || e.key === 'Esc') {
            const lightbox = document.getElementById('lightbox');
            if (lightbox && lightbox.style.display === 'flex') {
                closeLightbox();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const img = document.getElementById('lightboxImg');
        if (!img) return;

        img.addEventListener('mousedown', (e) => {
            if (lbScale > 1) {
                isDraggingLb = true;
                startX = e.clientX - lbTransX;
                startY = e.clientY - lbTransY;
                img.style.cursor = 'grabbing';
                e.preventDefault();
            }
        });

        document.addEventListener('mousemove', (e) => {
            if (isDraggingLb) {
                lbTransX = e.clientX - startX;
                lbTransY = e.clientY - startY;
                applyLbTransform();
            }
        });

        document.addEventListener('mouseup', () => {
            if (isDraggingLb) {
                isDraggingLb = false;
                if (img) img.style.cursor = 'grab';
            }
        });
    });

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
    async function saveStaffNotes(notes) {
        const indicator = document.getElementById('notesSaveIndicator');
        if (!indicator) return;

        try {
            const response = await fetch("{{ route('admin.applications.save-notes', $application->id) }}", {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    admin_notes: notes
                })
            });

            const data = await response.json();
            if (response.ok && data.success) {
                indicator.classList.remove('d-none');
                setTimeout(() => {
                    indicator.classList.add('d-none');
                }, 2000);
            }
        } catch (error) {
            console.error('Failed to save staff notes:', error);
        }
    }

    // Initialize Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush