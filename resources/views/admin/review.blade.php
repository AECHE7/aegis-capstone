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

    /* AI & Studio Cards */
    .forensics-studio-card, .ai-deck-card {
        background: var(--card-bg, #ffffff);
        border-radius: 16px;
        color: var(--text-main);
        position: relative;
        border: 1px solid var(--border-color, #e2e8f0);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    }

    /* Radial Progress Ring */
    .risk-ring-wrapper {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto;
    }

    .risk-ring-svg {
        transform: rotate(-90deg);
        width: 120px;
        height: 120px;
    }

    .risk-ring-track { fill: none; stroke: var(--border-color, #f1f5f9); stroke-width: 9; }
    .risk-ring-fill  { fill: none; stroke-width: 9; stroke-linecap: round; transition: stroke-dashoffset 1.2s cubic-bezier(0.4, 0, 0.2, 1); }

    .risk-ring-center {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    /* Document Canvas Viewer */
    .forensic-canvas-box {
        background: #0f172a;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 460px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .forensic-canvas-box img {
        max-height: 440px;
        width: 100%;
        object-fit: contain;
        transition: transform 0.2s ease;
        user-select: none;
    }

    /* Tab Switcher in Studio */
    .forensic-layer-btn {
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 50px;
        padding: 6px 14px;
        transition: all 0.2s ease;
        border: 1px solid var(--border-color, #cbd5e1);
        background: #f8fafc;
        color: #475569;
    }

    .forensic-layer-btn.active {
        background: var(--clsu-green, #0C4E2D);
        color: #ffffff;
        border-color: var(--clsu-green, #0C4E2D);
        box-shadow: 0 2px 8px rgba(12, 78, 45, 0.25);
    }

    /* Decision buttons */
    .btn-approve {
        background: var(--clsu-green-cta, #00754A);
        color: white; border: none;
        padding: 10px 20px; border-radius: 50px;
        font-weight: 700; font-size: 0.9rem;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(0, 117, 74, 0.15);
    }
    .btn-approve:hover { background: var(--clsu-green, #0C4E2D); color: white; box-shadow: 0 8px 24px rgba(0, 117, 74, 0.25); }
    .btn-approve:active { transform: scale(0.96); }

    .btn-reject {
        background: #dc2626;
        color: white; border: none;
        padding: 10px 20px; border-radius: 50px;
        font-weight: 700; font-size: 0.9rem;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
    }
    .btn-reject:hover { background: #b91c1c; color: white; box-shadow: 0 8px 24px rgba(220, 38, 38, 0.25); }
    .btn-reject:active { transform: scale(0.96); }

    .btn-return {
        background: #d97706;
        color: white; border: none;
        padding: 10px 20px; border-radius: 50px;
        font-weight: 700; font-size: 0.9rem;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.15);
    }
    .btn-return:hover { background: #b45309; color: white; box-shadow: 0 8px 24px rgba(217, 119, 6, 0.25); }
    .btn-return:active { transform: scale(0.96); }

    /* Dossier Nav Tabs */
    .dossier-tab-btn {
        font-size: 0.78rem;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 10px;
        color: var(--text-main);
        background: transparent;
        border: none;
        transition: all 0.2s;
    }
    .dossier-tab-btn.active {
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 700;
    }

    .img-zoomable { cursor: zoom-in; }
</style>
@endpush

@section('content')

@php
    $hasAnyAiResult = $application->documents->contains(fn($d) => $d->aiResult);
    $anyScanning  = $application->documents->contains(fn($d) => $d->aiResult && $d->aiResult->classification === 'scanning');
    $anyFailed    = $application->documents->contains(fn($d) => $d->aiResult && $d->aiResult->classification === 'failed');
@endphp

{{-- Compact Navigation & Breadcrumb --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light fw-semibold rounded-pill px-3 border shadow-sm" style="font-size:0.8rem;">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Queue
    </a>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-secondary-subtle text-secondary px-3 py-1.5 rounded-pill monospace-data fw-bold" style="font-size:0.75rem;">
            APP-{{ $application->id }}
        </span>
        @if($application->trashed())
            <span class="badge bg-dark text-white rounded-pill px-3 py-1.5"><i class="fa-solid fa-ban me-1"></i> Cancelled</span>
        @elseif($application->status == 'Pending')
            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-1.5 fw-bold"><i class="fa-solid fa-hourglass-half me-1"></i> Pending</span>
        @elseif($application->status == 'Under Review')
            <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-1.5 fw-bold"><i class="fa-solid fa-magnifying-glass me-1"></i> Under Review</span>
        @elseif($application->status == 'Approved')
            <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-1.5 fw-bold"><i class="fa-solid fa-check me-1"></i> Approved</span>
        @elseif($application->status == 'Returned')
            <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 fw-bold"><i class="fa-solid fa-reply me-1"></i> Returned</span>
        @else
            <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-1.5 fw-bold"><i class="fa-solid fa-times me-1"></i> Rejected</span>
        @endif
    </div>
</div>

{{-- Applicant Summary Hero Card --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: var(--card-bg);">
    <div class="card-body p-3.5 px-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark">{{ $application->program_name }}</h5>
                <div class="d-flex flex-wrap gap-2 pt-1">
                    <span class="badge bg-light text-dark border px-2.5 py-1 fw-medium"><i class="fa-solid fa-user text-success me-1"></i> {{ $application->user->name ?? 'Unknown' }}</span>
                    <span class="badge bg-light text-dark border px-2.5 py-1 fw-medium monospace-data"><i class="fa-solid fa-id-card text-success me-1"></i> {{ $application->user->profile?->clsu_id_number ?? 'N/A' }}</span>
                    <span class="badge bg-light text-dark border px-2.5 py-1 fw-medium"><i class="fa-solid fa-graduation-cap text-success me-1"></i> {{ $application->user->profile?->course ?? 'N/A' }} — {{ $application->user->profile?->year_level ?? 'N/A' }}</span>
                    <span class="badge bg-light text-dark border px-2.5 py-1 fw-medium"><i class="fa-solid fa-star text-warning me-1"></i> GWA: <strong class="monospace-data">{{ $application->gwa !== null ? number_format($application->gwa, 2) : 'N/A' }}</strong></span>
                </div>
            </div>
            @if($application->documents->count() > 1)
                <div class="d-flex align-items-center gap-2 bg-light p-2 rounded-3 border">
                    <label class="small fw-bold text-muted mb-0" for="docSelector"><i class="fa-solid fa-file-invoice me-1"></i> Select Document:</label>
                    <select class="form-select form-select-sm border-0 fw-semibold" id="docSelector" style="font-size: 0.82rem; min-width: 140px;">
                        @foreach($application->documents as $doc)
                            <option value="{{ $doc->id }}">{{ $doc->document_type }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Main Workspace Grid --}}
<div class="row g-4">

    {{-- LEFT: Document Forensics Studio Canvas (8 Cols) --}}
    <div class="col-lg-8">
        <div class="forensics-studio-card p-4">
            @if($application->documents->isEmpty())
                <div class="text-center py-5 my-auto">
                    <i class="fa-solid fa-folder-open fa-3x text-muted opacity-50 mb-3"></i>
                    <h6 class="fw-bold text-dark mb-1">No Documents Uploaded</h6>
                    <p class="text-muted small mb-0">This program does not require any document submissions.</p>
                </div>
            @else
                @foreach($application->documents as $doc)
                    @php
                        $hasAiResult = $doc->aiResult;
                        $isScanning  = $hasAiResult && $doc->aiResult->classification === 'scanning';
                        $isFailed    = $hasAiResult && $doc->aiResult->classification === 'failed';
                        $fraudScore  = $hasAiResult && !$isScanning && !$isFailed ? $doc->aiResult->fraud_probability : 0;
                        $riskClass   = $fraudScore >= 70 ? 'danger' : ($fraudScore >= 35 ? 'warning' : 'success');
                        $isPdf       = str_ends_with(strtolower($doc->original_name), '.pdf') || str_ends_with(strtolower($doc->file_path), '.pdf');

                        $deepReport = $doc->aiResult->deep_analysis_report ?? null;
                        $layers = $deepReport['visualizations']['layer_heatmaps'] ?? [];
                        $compositeHeatmap = $deepReport['visualizations']['composite_heatmap_base64'] ?? null;
                        $originalPageBase64 = $deepReport['visualizations']['original_page_base64'] ?? null;
                        $originalUrl = $originalPageBase64 ? route('document.originalPage', $doc->id) : route('document.view', $doc->id);
                    @endphp

                    <div class="doc-viewer-wrapper" id="viewer-doc-{{ $doc->id }}" style="display: {{ $loop->first ? 'block' : 'none' }};">
                        
                        {{-- Studio Header Toolbar --}}
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-images text-success me-2"></i> {{ $doc->document_type }} Forensic Canvas</h6>
                                <small class="text-muted" style="font-size:0.75rem;">Interactive multi-spectrum visual inspection studio</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($hasAiResult && !$isScanning && !$isFailed)
                                    <a href="{{ route('admin.forensicPdf', [$application->id, $doc->id]) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-medium" style="font-size:0.78rem;" target="_blank">
                                        <i class="fa-solid fa-file-pdf me-1 text-danger"></i> Export Audit Certificate
                                    </a>
                                @endif
                                <a href="{{ route('admin.document.download', $doc->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-medium" style="font-size:0.78rem;">
                                    <i class="fa-solid fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>

                        {{-- Layer Selection Switcher --}}
                        @if($hasAiResult && !$isScanning && !$isFailed && !$isPdf)
                            <div class="d-flex align-items-center gap-1.5 overflow-auto mb-3 pb-1" style="scrollbar-width: thin;">
                                <button type="button" class="forensic-layer-btn active forensic-tab-btn" onclick="switchForensicTab(this, 'original', '{{ $doc->id }}')">
                                    <i class="fa-solid fa-image me-1"></i> Original
                                </button>
                                @if($compositeHeatmap || $doc->aiResult->heatmap_path)
                                    <button type="button" class="forensic-layer-btn forensic-tab-btn" data-src="{{ route('document.heatmap', $doc->id) }}" onclick="switchForensicTab(this, 'composite', '{{ $doc->id }}')">
                                        <i class="fa-solid fa-fire me-1 text-danger"></i> Annotated (CAM)
                                    </button>
                                @endif
                                @if(isset($layers['ela_detailed']))
                                    <button type="button" class="forensic-layer-btn forensic-tab-btn" data-src="{{ route('document.forensicLayer', [$doc->id, 'ela_detailed']) }}" onclick="switchForensicTab(this, 'ela', '{{ $doc->id }}')">
                                        <i class="fa-solid fa-wave-square me-1 text-warning"></i> ELA Compression Map
                                    </button>
                                @endif
                                @if(isset($layers['noise_consistency']))
                                    <button type="button" class="forensic-layer-btn forensic-tab-btn" data-src="{{ route('document.forensicLayer', [$doc->id, 'noise_consistency']) }}" onclick="switchForensicTab(this, 'noise', '{{ $doc->id }}')">
                                        <i class="fa-solid fa-braille me-1 text-info"></i> Noise Consistency
                                    </button>
                                @endif
                                @if(isset($layers['edge_consistency']))
                                    <button type="button" class="forensic-layer-btn forensic-tab-btn" data-src="{{ route('document.forensicLayer', [$doc->id, 'edge_consistency']) }}" onclick="switchForensicTab(this, 'edge', '{{ $doc->id }}')">
                                        <i class="fa-solid fa-draw-polygon me-1 text-primary"></i> Edge Continuity
                                    </button>
                                @endif
                            </div>
                        @endif

                        {{-- Main Forensic Canvas Display --}}
                        <div class="forensic-canvas-box mb-3">
                            @if($isScanning)
                                <div class="text-center py-5">
                                    <i class="fa-solid fa-circle-notch fa-spin fa-3x text-info mb-3"></i>
                                    <p class="text-white-50 small mb-2">Analyzing ELA + ResNet-50 Dual-CNN compression signatures...</p>
                                    <button type="button" class="btn btn-sm btn-warning fw-bold text-dark mt-2" onclick="runSyncScan('{{ $doc->id }}')">
                                        <i class="fa-solid fa-bolt me-1"></i> Run Scan Synchronously
                                    </button>
                                </div>
                            @elseif($isFailed)
                                <div class="text-center py-5">
                                    <i class="fa-solid fa-triangle-exclamation fa-3x text-danger mb-3"></i>
                                    <p class="text-white-50 small mb-3">AI scan execution encountered an error.</p>
                                    <form action="{{ route('admin.scan', $application->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold px-4">Retry AI Scan</button>
                                    </form>
                                </div>
                            @elseif($isPdf)
                                <iframe src="{{ route('document.view', $doc->id) }}" style="width: 100%; height: 460px; border: none;"></iframe>
                            @else
                                <!-- Base Image -->
                                <img id="baseImage-{{ $doc->id }}" src="{{ $originalUrl }}" class="img-zoomable" alt="Student Document" onclick="openLightbox(this.src)">
                                
                                <!-- Overlay Layer -->
                                <img id="overlayImage-{{ $doc->id }}" src="" class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.5; pointer-events: none; display: none;" alt="Forensic Overlay Layer">
                            @endif
                        </div>

                        {{-- Opacity Slider Toolbar --}}
                        <div class="p-3 bg-light rounded-3 border mb-3" id="opacityControls-{{ $doc->id }}" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <span class="small fw-bold text-dark" style="font-size:0.75rem;"><i class="fa-solid fa-circle-half-stroke me-1 text-primary"></i> Overlay Opacity Blending</span>
                                <span class="badge bg-primary text-white" id="opacityVal-{{ $doc->id }}">50%</span>
                            </div>
                            <input type="range" class="form-range" id="opacitySlider-{{ $doc->id }}" min="0" max="100" value="50" oninput="adjustOverlayOpacity(this.value, '{{ $doc->id }}')">
                        </div>

                        {{-- Suspect Regions Micro-Crops Grid --}}
                        @if($hasAiResult && !empty($doc->aiResult->deep_analysis_report['region_crops']))
                            <div class="p-3 rounded-3 border bg-light-subtle mb-2">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-dark small"><i class="fa-solid fa-crop-simple me-1 text-danger"></i> Pinpointed Anomalous Regions</span>
                                    <span class="badge bg-danger text-white">{{ count($doc->aiResult->deep_analysis_report['region_crops']) }} Anomalies</span>
                                </div>
                                <div class="row g-2">
                                    @foreach($doc->aiResult->deep_analysis_report['region_crops'] as $crop)
                                        <div class="col-6 col-sm-4 col-md-3">
                                            <div class="border rounded bg-white p-1 text-center cursor-pointer shadow-sm" onclick="zoomToRegion('{{ $crop['crop_base64'] }}', '{{ $crop['x'] }}', '{{ $crop['y'] }}', '{{ $crop['w'] }}', '{{ $crop['h'] }}', '{{ $crop['detector'] }}', '{{ $crop['severity'] }}')" style="font-size: 0.7rem;">
                                                <div style="height: 64px;" class="d-flex align-items-center justify-content-center overflow-hidden bg-light rounded">
                                                    <img src="data:image/png;base64,{{ $crop['crop_base64'] }}" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                                </div>
                                                <div class="mt-1 d-flex align-items-center justify-content-between px-1">
                                                    <span class="text-truncate fw-semibold text-muted" style="max-width: 60px;">{{ $crop['detector'] }}</span>
                                                    <span class="badge bg-{{ $crop['severity'] === 'high' ? 'danger' : 'warning' }} text-white" style="font-size:0.55rem;">{{ $crop['severity'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- RIGHT: Triage, Scoring & Decision Hub (4 Cols) --}}
    <div class="col-lg-4">
        
        @foreach($application->documents as $doc)
            @php
                $hasAiResult = $doc->aiResult;
                $isScanning  = $hasAiResult && $doc->aiResult->classification === 'scanning';
                $isFailed    = $hasAiResult && $doc->aiResult->classification === 'failed';

                $fraudScore  = $hasAiResult && !$isScanning && !$isFailed ? $doc->aiResult->fraud_probability : 0;
                $isUnrecognizedFormat = in_array('unrecognized_document_format', $doc->aiResult->anomaly_indicators ?? []);
                
                if ($isUnrecognizedFormat) {
                    $riskColor   = '#d97706';
                    $riskLabel   = 'UNRECOGNIZED FORMAT';
                    $riskBadgeBg = '#fef3c7';
                    $riskBadgeFg = '#92400e';
                } else {
                    $riskColor   = $fraudScore >= 70 ? '#ef4444' : ($fraudScore >= 35 ? '#f59e0b' : '#22c55e');
                    $riskLabel   = $fraudScore >= 70 ? 'HIGH TAMPERING RISK' : ($fraudScore >= 35 ? 'REVIEW RECOMMENDED' : 'AUTHENTIC / LOW RISK');
                    $riskBadgeBg = $fraudScore >= 70 ? '#fee2e2' : ($fraudScore >= 35 ? '#fef3c7' : '#dcfce7');
                    $riskBadgeFg = $fraudScore >= 70 ? '#991b1b' : ($fraudScore >= 35 ? '#92400e' : '#166534');
                }
            @endphp

            <div class="ai-panel-doc" id="ai-panel-{{ $doc->id }}" style="display: {{ $loop->first ? 'block' : 'none' }};">
                
                {{-- Card 1: AI Authenticity Verdict & 4-Pillar Evidence Matrix --}}
                <div class="ai-deck-card p-4 mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <span class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.8px; font-size: 0.7rem;">
                            <i class="fa-solid fa-microchip text-success me-1"></i> Explainable Forensic Matrix
                        </span>
                        <span class="badge py-1 px-2.5 rounded-pill fw-bold" style="background: {{ $riskBadgeBg }}; color: {{ $riskBadgeFg }}; font-size: 0.72rem;">
                            {{ $riskLabel }}
                        </span>
                    </div>

                    @if($hasAiResult && !$isScanning && !$isFailed)
                        @if($isUnrecognizedFormat)
                            <div class="alert alert-warning p-2.5 rounded-3 mb-3 border border-warning border-opacity-30" style="font-size:0.75rem;">
                                <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>
                                <strong>Non-Academic Format:</strong> File lacks standard grade tables or university headers. Recommended Action: <em>Return for Correction</em>.
                            </div>
                        @endif

                        {{-- Radial Progress Gauge --}}
                        <div class="risk-ring-wrapper mb-3">
                            <svg class="risk-ring-svg" viewBox="0 0 140 140">
                                <circle class="risk-ring-track" cx="70" cy="70" r="60"/>
                                <circle class="risk-ring-fill" cx="70" cy="70" r="60"
                                    stroke="{{ $riskColor }}"
                                    stroke-dasharray="376.99"
                                    stroke-dashoffset="376.99"
                                    id="riskRing-{{ $doc->id }}"/>
                            </svg>
                            <div class="risk-ring-center">
                                <div class="monospace-data fw-bold" style="font-size:1.85rem; color:{{ $riskColor }}; line-height:1;">{{ number_format($fraudScore, 1) }}</div>
                                <div class="text-muted" style="font-size:0.7rem;">% fraud prob.</div>
                            </div>
                        </div>

                        {{-- 4-Pillar Evidence Breakdown --}}
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="fw-bold text-muted text-uppercase mb-2 pb-1 border-bottom" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-scale-balanced text-primary me-1"></i> 4-Pillar Evidence Matrix
                            </div>
                            
                            {{-- Pillar 1: Text & OCR (35%) --}}
                            <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-2">
                                <div>
                                    <div class="small fw-semibold text-dark" style="font-size:0.75rem;">1. Text & Grade OCR</div>
                                    <small class="text-muted" style="font-size:0.65rem;">Weight: 35%</small>
                                </div>
                                @php
                                    $gwaMismatch = false;
                                    $extracted = $doc->aiResult->deep_analysis_report['extracted_gwa'] ?? null;
                                    if ($extracted !== null && !empty($application->gwa)) {
                                        $gwaMismatch = abs((float)$application->gwa - (float)$extracted) > 0.01;
                                    }
                                @endphp
                                @if($extracted === null)
                                    <span class="badge bg-secondary text-white" style="font-size:0.68rem;">No OCR Data</span>
                                @elseif($gwaMismatch)
                                    <span class="badge bg-danger text-white" style="font-size:0.68rem;">Mismatch</span>
                                @else
                                    <span class="badge bg-success text-white" style="font-size:0.68rem;">Verified</span>
                                @endif
                            </div>

                            {{-- Pillar 2: Pixel Compression (25%) --}}
                            <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-2">
                                <div>
                                    <div class="small fw-semibold text-dark" style="font-size:0.75rem;">2. Pixel Compression (ELA/DCT)</div>
                                    <small class="text-muted" style="font-size:0.65rem;">Weight: 25%</small>
                                </div>
                                @if(in_array('resampling_traces_detected', $doc->aiResult->anomaly_indicators ?? []) || in_array('catnet_dct_compression_anomaly', $doc->aiResult->anomaly_indicators ?? []))
                                    <span class="badge bg-danger text-white" style="font-size:0.68rem;">Resampled</span>
                                @else
                                    <span class="badge bg-success text-white" style="font-size:0.68rem;">Clean</span>
                                @endif
                            </div>

                            {{-- Pillar 3: Sensor & Spatial Continuity (25%) --}}
                            <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-2">
                                <div>
                                    <div class="small fw-semibold text-dark" style="font-size:0.75rem;">3. Sensor Continuity (TruFor)</div>
                                    <small class="text-muted" style="font-size:0.65rem;">Weight: 25%</small>
                                </div>
                                @if(in_array('trufor_noiseprint_anomaly', $doc->aiResult->anomaly_indicators ?? []) || in_array('clone_stamp_detected', $doc->aiResult->anomaly_indicators ?? []))
                                    <span class="badge bg-danger text-white" style="font-size:0.68rem;">Anomalous</span>
                                @else
                                    <span class="badge bg-success text-white" style="font-size:0.68rem;">Uniform</span>
                                @endif
                            </div>

                            {{-- Pillar 4: Metadata Provenance (15%) --}}
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small fw-semibold text-dark" style="font-size:0.75rem;">4. Metadata & Provenance</div>
                                    <small class="text-muted" style="font-size:0.65rem;">Weight: 15%</small>
                                </div>
                                @if(!empty($doc->aiResult->detected_software))
                                    <span class="badge bg-danger text-white" style="font-size:0.68rem;">{{ $doc->aiResult->detected_software }}</span>
                                @elseif(in_array('exif_metadata_cleaned', $doc->aiResult->anomaly_indicators ?? []))
                                    <span class="badge bg-warning text-dark" style="font-size:0.68rem;">Stripped</span>
                                @else
                                    <span class="badge bg-success text-white" style="font-size:0.68rem;">Verifiable</span>
                                @endif
                            </div>
                        </div>

                        {{-- Re-run Scan trigger --}}
                        <form action="{{ route('admin.scan', $application->id) }}" method="POST">
                            @csrf
                            <div class="d-flex gap-2">
                                <select name="mode" class="form-select form-select-sm" style="font-size: 0.75rem;">
                                    <option value="standard" selected>Standard Scan (V2)</option>
                                    <option value="deep">Deep Forensic Scan (V3)</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-success fw-semibold px-3 text-nowrap" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-arrow-rotate-right me-1"></i> Re-Scan
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

            </div>
        @endforeach

        {{-- Card 2: Decision Actions Hub --}}
        <div class="ai-deck-card p-4 mb-3">
            <h6 class="fw-bold mb-2 text-dark"><i class="fa-solid fa-gavel text-success me-2"></i> Evaluator Decision</h6>

            {{-- 1-Click Smart Preset Remarks --}}
            <div class="mb-2">
                <span class="text-muted small d-block mb-1" style="font-size:0.68rem; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Quick Presets:</span>
                <div class="d-flex flex-wrap gap-1.5">
                    <button type="button" class="btn btn-xs btn-light border py-1 px-2 text-start rounded-pill" style="font-size:0.7rem;" onclick="setRemarks('GWA verified with OCR extraction. Student cleared for scholarship grant.')">
                        <i class="fa-solid fa-check text-success me-1"></i> GWA Verified
                    </button>
                    <button type="button" class="btn btn-xs btn-light border py-1 px-2 text-start rounded-pill" style="font-size:0.7rem;" onclick="setRemarks('Uploaded document lacks standard academic grade records (unrecognized format). Please re-upload an official Form 6 or Certificate of Grades.')">
                        <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Unrecognized Format
                    </button>
                    <button type="button" class="btn btn-xs btn-light border py-1 px-2 text-start rounded-pill" style="font-size:0.7rem;" onclick="setRemarks('Document image contains excessive camera glare/blur over grade cells. Please submit a flat, clear scan.')">
                        <i class="fa-solid fa-eye-slash text-info me-1"></i> Camera Glare/Blur
                    </button>
                    <button type="button" class="btn btn-xs btn-light border py-1 px-2 text-start rounded-pill" style="font-size:0.7rem;" onclick="setRemarks('Pixel compression discrepancies and anomalous grade cell edits detected upon multi-spectrum forensic verification.')">
                        <i class="fa-solid fa-ban text-danger me-1"></i> Tampered Records
                    </button>
                </div>
            </div>

            <form action="{{ route('admin.updateStatus', $application->id) }}" method="POST" id="decisionForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted mb-1" for="evaluatorRemarks">Evaluator Remarks</label>
                    <textarea name="remarks" id="evaluatorRemarks" class="form-control" rows="3" required
                              placeholder="e.g., GWA verified. Cleared for DOST-SEI Merit."
                              style="resize:none; font-size:0.85rem; border-radius: 10px;">{{ $application->remarks }}</textarea>
                </div>

                @if($application->trashed())
                    <div class="alert alert-warning text-center rounded-3 mb-0 small">
                        <i class="fa-solid fa-ban me-1"></i> Application was cancelled / deleted.
                    </div>
                @elseif($application->status == 'Pending' || $application->status == 'Under Review')
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex gap-2">
                            <button type="button" id="btnApprove" class="btn-approve w-50" onclick="confirmDecision('Approved')">
                                <i class="fa-solid fa-check-circle me-1"></i> Approve
                            </button>
                            <button type="button" id="btnReject" class="btn-reject w-50" onclick="confirmDecision('Rejected')">
                                <i class="fa-solid fa-times-circle me-1"></i> Reject
                            </button>
                        </div>
                        <button type="button" id="btnReturn" class="btn-return w-100" onclick="confirmDecision('Returned')">
                            <i class="fa-solid fa-reply me-1"></i> Return for Correction
                        </button>
                    </div>
                    <input type="hidden" name="status" id="statusInput">
                @else
                    <div class="alert mb-0 text-center fw-bold rounded-3"
                         style="background: {{ $application->status == 'Approved' ? '#dcfce7' : ($application->status == 'Returned' ? '#fffbeb' : '#fee2e2') }}; color: {{ $application->status == 'Approved' ? '#15803d' : ($application->status == 'Returned' ? '#b45309' : '#b91c1c') }}; border: none; font-size: 0.85rem;">
                        <i class="fa-solid fa-lock me-1"></i> Finalized as {{ $application->status }}.
                    </div>
                @endif
            </form>
        </div>

        {{-- Card 3: Tabbed Administrative Dossier (Custom Fields, Staff Notes, History) --}}
        <div class="ai-deck-card p-3.5 mb-3">
            <ul class="nav nav-pills nav-fill gap-1 mb-3 bg-light p-1 rounded-3" id="dossierTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link dossier-tab-btn active" id="tab-custom-btn" data-bs-toggle="pill" data-bs-target="#tab-custom" type="button" role="tab">
                        <i class="fa-solid fa-list-check me-1"></i> Fields
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link dossier-tab-btn" id="tab-notes-btn" data-bs-toggle="pill" data-bs-target="#tab-notes" type="button" role="tab">
                        <i class="fa-solid fa-note-sticky me-1"></i> Notes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link dossier-tab-btn" id="tab-history-btn" data-bs-toggle="pill" data-bs-target="#tab-history" type="button" role="tab">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i> History
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="dossierTabContent">
                {{-- Tab 1: Custom Form Fields --}}
                <div class="tab-pane fade show active p-2" id="tab-custom" role="tabpanel">
                    @if($application->customFields && $application->customFields->count() > 0)
                        <div class="d-flex flex-column gap-2.5">
                            @foreach($application->customFields as $field)
                                <div class="border-bottom pb-2">
                                    <div class="small fw-semibold text-muted" style="font-size:0.72rem;">{{ $field->field_name }}</div>
                                    <div class="text-dark fw-medium" style="font-size:0.82rem;">
                                        @php
                                            $isFieldFile = Str::startsWith($field->field_value, 'uploads/') || 
                                                          (Str::startsWith($field->field_value, 'http') && 
                                                           collect(['.pdf', '.png', '.jpg', '.jpeg', '.docx', '.webp'])->contains(fn($ext) => Str::endsWith(strtolower($field->field_value), $ext)));
                                        @endphp
                                        @if($isFieldFile)
                                            <a href="{{ route('application-field.file', $field->id) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0.5 px-2 mt-1" style="font-size:0.72rem; border-radius:6px;">
                                                <i class="fa-solid fa-file-arrow-down me-1"></i> View Uploaded File
                                            </a>
                                        @else
                                            {{ $field->field_value }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 text-muted small">No custom fields for this program.</div>
                    @endif
                </div>

                {{-- Tab 2: Private Staff Notes --}}
                <div class="tab-pane fade p-2" id="tab-notes" role="tabpanel">
                    <p class="text-muted small mb-2" style="font-size:0.72rem;">Internal staff notes. Auto-saves on blur.</p>
                    <div class="position-relative">
                        <textarea id="staffNotesTextarea" name="admin_notes" class="form-control" rows="4" 
                                  placeholder="Write verification notes or cross-referencing details..." 
                                  style="resize:none; font-size:0.8rem; border-radius:8px;"
                                  onblur="saveStaffNotes(this.value)">{{ $application->admin_notes }}</textarea>
                        <div id="notesSaveIndicator" class="position-absolute bottom-0 end-0 mb-2 me-2 text-success d-none" style="font-size:0.7rem; pointer-events: none;">
                            <i class="fa-solid fa-circle-check"></i> Saved
                        </div>
                    </div>
                </div>

                {{-- Tab 3: Scholarship History --}}
                <div class="tab-pane fade p-2" id="tab-history" role="tabpanel">
                    @if($history->isEmpty())
                        <div class="text-center py-3 text-muted small bg-light rounded-3">No prior applications found.</div>
                    @else
                        <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                            <table class="table table-sm mb-0" style="font-size:0.75rem;">
                                <thead>
                                    <tr>
                                        <th>Term</th>
                                        <th class="text-center">GWA</th>
                                        <th class="text-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($history as $histApp)
                                    <tr>
                                        <td class="text-muted">{{ $histApp->academicTerm?->semester ?? 'N/A' }}</td>
                                        <td class="text-center monospace-data">{{ $histApp->gwa }}</td>
                                        <td class="text-end"><span class="badge bg-light text-dark border">{{ $histApp->status }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Interactive Lightbox overlay --}}
<div id="lightbox" class="position-fixed inset-0" onclick="if(event.target===this) closeLightbox()"
     style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.94);backdrop-filter:blur(8px);z-index:9999;align-items:center;justify-content:center;flex-direction:column;">
    
    <div class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded-pill bg-dark border border-secondary shadow" onclick="event.stopPropagation()">
        <button type="button" class="btn btn-sm btn-outline-light rounded-circle" onclick="zoomLightbox(1.2)" title="Zoom In"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
        <button type="button" class="btn btn-sm btn-outline-light rounded-circle" onclick="zoomLightbox(0.8)" title="Zoom Out"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
        <button type="button" class="btn btn-sm btn-outline-light rounded-circle" onclick="rotateLightbox(90)" title="Rotate 90°"><i class="fa-solid fa-rotate-right"></i></button>
        <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="resetLightbox()">Reset</button>
        <button type="button" class="btn btn-sm btn-danger rounded-circle ms-2" onclick="closeLightbox()"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="overflow-hidden d-flex align-items-center justify-content-center" style="max-width:92vw;max-height:82vh;position:relative;">
        <img id="lightboxImg" src="" alt="Enlarged view" style="max-width:90vw;max-height:80vh;object-fit:contain;border-radius:12px;box-shadow:0 24px 64px rgba(0,0,0,0.5);transition:transform 0.15s ease-out;cursor:grab;">
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Radial ring animation on load
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
                }, 250);
            }
        @endforeach

        // Document selector listener
        const docSelector = document.getElementById('docSelector');
        if (docSelector) {
            docSelector.addEventListener('change', function() {
                const docId = this.value;
                document.querySelectorAll('.ai-panel-doc').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.doc-viewer-wrapper').forEach(el => el.style.display = 'none');
                
                const selectedAiPanel = document.getElementById('ai-panel-' + docId);
                const selectedViewer = document.getElementById('viewer-doc-' + docId);
                if (selectedAiPanel) selectedAiPanel.style.display = 'block';
                if (selectedViewer) selectedViewer.style.display = 'block';
            });
        }
    });

    // Layer switcher
    function switchForensicTab(btn, layerType, docId) {
        const container = btn.closest('.doc-viewer-wrapper');
        container.querySelectorAll('.forensic-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const overlayImg = document.getElementById('overlayImage-' + docId);
        const opacityCtrl = document.getElementById('opacityControls-' + docId);

        if (layerType === 'original') {
            if (overlayImg) overlayImg.style.display = 'none';
            if (opacityCtrl) opacityCtrl.style.display = 'none';
        } else {
            const src = btn.getAttribute('data-src');
            if (overlayImg && src) {
                overlayImg.src = src;
                overlayImg.style.display = 'block';
            }
            if (opacityCtrl) opacityCtrl.style.display = 'block';
        }
    }

    function adjustOverlayOpacity(val, docId) {
        const overlayImg = document.getElementById('overlayImage-' + docId);
        const valBadge = document.getElementById('opacityVal-' + docId);
        if (overlayImg) overlayImg.style.opacity = val / 100;
        if (valBadge) valBadge.innerText = val + '%';
    }

    // Decision confirm
    function confirmDecision(status) {
        const remarks = document.getElementById('evaluatorRemarks').value.trim();
        if (!remarks) {
            Swal.fire({
                icon: 'warning',
                title: 'Remarks Required',
                text: 'Please enter evaluator remarks before finalizing.',
                confirmButtonColor: '#f59e0b'
            });
            return;
        }

        Swal.fire({
            title: `Confirm ${status}?`,
            text: `Mark this application as ${status}?`,
            icon: status === 'Approved' ? 'success' : 'warning',
            showCancelButton: true,
            confirmButtonColor: status === 'Approved' ? '#00754A' : '#dc2626',
            confirmButtonText: `Yes, finalize as ${status}`
        }).then(res => {
            if (res.isConfirmed) {
                submitDecisionAjax(status);
            }
        });
    }

    async function submitDecisionAjax(status) {
        const form = document.getElementById('decisionForm');
        document.getElementById('statusInput').value = status;
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Decision Saved', text: data.message, confirmButtonColor: '#00754A' });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#dc2626' });
            }
        } catch (e) {
            console.error(e);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Decision submission failed.', confirmButtonColor: '#dc2626' });
        }
    }

    async function runSyncScan(docId) {
        const url = "{{ route('admin.scanSync', $application->id) }}";
        Swal.fire({
            title: 'Running AI Verification...',
            text: 'Processing neural network inference synchronously.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Scan Completed', text: data.message, confirmButtonColor: '#00754A' });
                setTimeout(() => location.reload(), 1200);
            } else {
                Swal.fire({ icon: 'error', title: 'Scan Failed', text: data.message, confirmButtonColor: '#dc2626' });
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Sync scan failed.', confirmButtonColor: '#dc2626' });
        }
    }

    async function saveStaffNotes(notes) {
        try {
            const res = await fetch("{{ route('admin.saveNotes', $application->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ admin_notes: notes })
            });
            const data = await res.json();
            if (data.success) {
                const ind = document.getElementById('notesSaveIndicator');
                if (ind) {
                    ind.classList.remove('d-none');
                    setTimeout(() => ind.classList.add('d-none'), 2000);
                }
            }
        } catch (e) {
            console.error('Notes save error:', e);
        }
    }

    function setRemarks(text) {
        const textarea = document.getElementById('evaluatorRemarks');
        if (textarea) {
            textarea.value = text;
            textarea.focus();
        }
    }

    // Lightbox Controls
    let lbScale = 1, lbRot = 0;
    function openLightbox(src) {
        const lb = document.getElementById('lightbox');
        const img = document.getElementById('lightboxImg');
        img.src = src;
        lbScale = 1; lbRot = 0;
        img.style.transform = 'scale(1) rotate(0deg)';
        lb.style.display = 'flex';
    }
    function closeLightbox() { document.getElementById('lightbox').style.display = 'none'; }
    function zoomLightbox(factor) {
        lbScale = Math.max(0.4, Math.min(4, lbScale * factor));
        document.getElementById('lightboxImg').style.transform = `scale(${lbScale}) rotate(${lbRot}deg)`;
    }
    function rotateLightbox(deg) {
        lbRot = (lbRot + deg) % 360;
        document.getElementById('lightboxImg').style.transform = `scale(${lbScale}) rotate(${lbRot}deg)`;
    }
    function resetLightbox() {
        lbScale = 1; lbRot = 0;
        document.getElementById('lightboxImg').style.transform = 'scale(1) rotate(0deg)';
    }
</script>
@endpush