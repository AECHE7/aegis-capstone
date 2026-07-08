@extends('layouts.app')

@section('title', 'My Application | A.E.G.I.S.')

@push('styles')
<style>
    /* Status Hero Banner */
    .status-hero {
        border-radius: 16px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        color: white;
    }

    .status-hero.pending  { background: #b45309; }
    .status-hero.review   { background: #0284c7; }
    .status-hero.approved { background: #0C4E2D; }
    .status-hero.rejected { background: #b91c1c; }
    .status-hero.empty    { background: #475569; }

    .status-hero-icon {
        width: 64px; height: 64px;
        border-radius: 12px;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
        border: 1px solid rgba(255,255,255,0.2);
    }

    /* Steps */
    .step-track {
        display: flex;
        align-items: center;
        gap: 0;
    }

    .step-node {
        width: 40px; height: 40px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        border: 3px solid white;
        position: relative;
        z-index: 2;
        box-shadow: none;
        flex-shrink: 0;
    }

    .step-connector {
        flex: 1;
        height: 3px;
        background: rgba(255,255,255,0.2);
        position: relative;
    }

    .step-connector.done { background: rgba(255,255,255,0.7); }

    /* Timeline */
    .timeline-item { display: flex; gap: 16px; margin-bottom: 24px; position: relative; }
    .timeline-icon {
        width: 38px; height: 38px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 0.85rem;
        z-index: 2;
    }

    .timeline-connector {
        position: absolute;
        left: 19px; top: 38px; bottom: -24px;
        width: 2px;
        background: linear-gradient(to bottom, var(--border-color), transparent);
    }

    .timeline-content {
        flex: 1;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 12px 16px;
        box-shadow: none;
    }

    /* Confetti */
    .confetti-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999; }

    /* Empty state */
    .empty-card {
        background: var(--card-bg);
        border-radius: 16px;
        border: 2px dashed var(--border-color);
        padding: 4rem 2rem;
        text-align: center;
    }

    @media (max-width: 767.98px) {
        .status-hero {
            padding: 1.25rem 1.25rem;
        }
        .status-hero > .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 16px !important;
        }
        .status-hero .text-end {
            text-align: left !important;
            margin-top: 0.25rem;
            width: 100%;
            border-top: 1px solid rgba(255,255,255,0.15);
            padding-top: 0.75rem;
        }
        .status-hero .d-flex.align-items-center.gap-4 {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 12px !important;
        }
        
        .step-track {
            padding: 0 10px;
        }
        
        .timeline-item {
            gap: 12px;
            margin-bottom: 20px;
        }
        .timeline-content {
            padding: 10px 12px;
        }
    }

    @media (max-width: 480px) {
        .step-node {
            width: 32px !important;
            height: 32px !important;
            border-width: 2px !important;
        }
        .step-node i {
            font-size: 0.68rem !important;
        }
        .step-track + div {
            font-size: 0.6rem !important;
        }
    }

    /* Scholar Mode Styling */
    .scholar-banner {
        background: linear-gradient(135deg, #07331c 0%, #0C4E2D 100%);
        border-radius: 16px;
        padding: 2.25rem;
        color: white;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        border-bottom: 4px solid #D97706;
    }
    .scholar-banner::after {
        content: '';
        position: absolute;
        bottom: -60px;
        right: -60px;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: rgba(255,255,255,0.03);
        pointer-events: none;
    }
    .scholar-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: white;
        padding: 1.5rem;
        height: 100%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .gwa-progress-container {
        position: relative;
        height: 16px;
        background-color: #f1f5f9;
        border-radius: 50px;
        overflow: hidden;
    }
    .gwa-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #0C4E2D, #16a34a);
        border-radius: 50px;
        transition: width 0.4s ease;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="max-width: 1040px; margin: 0 auto; padding: 1.5rem 1rem 3rem;">

    {{-- Official Announcements Section --}}
    @if(isset($announcements) && $announcements->count() > 0)
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fa-solid fa-bullhorn text-warning"></i>
                <h6 class="fw-bold mb-0 text-dark" style="font-size:0.9rem; letter-spacing: 0.5px;">Official Announcements</h6>
            </div>
            <div class="row g-3">
                @foreach($announcements as $ann)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm p-3 position-relative" style="border-radius: 12px; border-left: 4px solid var(--clsu-gold) !important; background: #fffbeb;">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                <h6 class="fw-bold text-dark mb-1" style="font-size:0.875rem;">{{ $ann->title }}</h6>
                                <span class="text-muted monospace-data" style="font-size: 0.65rem; white-space: nowrap;">
                                    {{ $ann->created_at->format('M d, Y') }}
                                </span>
                            </div>
                            <p class="text-muted mb-2 small" style="line-height:1.45; font-size: 0.8rem;">{!! nl2br(e($ann->content)) !!}</p>
                            <div class="text-muted" style="font-size: 0.68rem; font-weight: 550;">
                                Broadcasted by: <span class="text-dark">{{ $ann->author->name ?? 'Office of Student Affairs' }}</span> ({{ ucfirst($ann->author->role ?? 'Staff') }})
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($application)
        @if($application->status === 'Approved')
            {{-- ═══════════════════════════════════════════
                 SCHOLAR MODE DASHBOARD
                 ═══════════════════════════════════════════ --}}
            <div class="scholar-banner mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div style="font-size:0.75rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--clsu-gold);" class="mb-2">
                            CLSU Scholar Portal
                        </div>
                        <h2 class="fw-bold mb-2 text-white" style="font-family:'Poppins',sans-serif;">
                            Welcome back, {{ auth()->user()->name }}! 🎉
                        </h2>
                        <p class="mb-0 text-white-50 small" style="max-width: 500px;">
                            You are currently an active scholar for <strong>{{ $application->program_name }}</strong>. Keep up your excellent academic performance!
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-graduation-cap me-1"></i> Active Scholar
                        </span>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <!-- Allowance Status Card -->
                <div class="col-md-6">
                    <div class="scholar-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="p-3 bg-light rounded-4 text-success">
                                <i class="fa-solid fa-wallet fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Scholarship Benefits</h6>
                                <p class="text-muted small mb-0">Allowances & disbursements</p>
                            </div>
                        </div>
                        <hr class="text-muted opacity-20">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Semester Grant</span>
                            <span class="fw-bold text-dark">Php 15,000.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Active Term</span>
                            <span class="fw-semibold text-dark">
                                @if($application->academicTerm)
                                    {{ $application->academicTerm->semester }} Sem, AY {{ $application->academicTerm->academic_year }}
                                @else
                                    Current Term
                                @endif
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Disbursement Status</span>
                            <span class="badge bg-success px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">Released</span>
                        </div>
                    </div>
                </div>

                <!-- Academic Monitoring Card -->
                <div class="col-md-6">
                    <div class="scholar-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="p-3 bg-light rounded-4 text-info">
                                <i class="fa-solid fa-chart-line fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Academic Monitoring</h6>
                                <p class="text-muted small mb-0">Grades & GWA progression</p>
                            </div>
                        </div>
                        <hr class="text-muted opacity-20">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Declared GWA</span>
                                <span class="fw-bold text-dark">{{ number_format($application->gwa, 2) }}</span>
                            </div>
                            <div class="gwa-progress-container">
                                @php
                                    $pct = $application->gwa > 0 ? max(0, min(100, (5.00 - $application->gwa) / 4.00 * 100)) : 0;
                                @endphp
                                <div class="gwa-progress-bar" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Academic Standing</span>
                            <span class="badge bg-success px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">Good Standing</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scholar Actions Card -->
            <div class="card p-4 border-0 shadow-sm mb-4 scholar-actions-card" style="border-radius: 16px;">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-gears text-success me-2"></i> Scholar Actions</h5>
                <p class="text-muted small mb-4">Manage your scholarship active status or submit a renewal request for the next term.</p>
                <div class="d-flex flex-wrap gap-3">
                    @php
                        $canRenew = $application->academicTerm && !$application->academicTerm->is_active;
                    @endphp
                    @if($canRenew)
                        <a href="{{ route('student.apply', ['renew_from' => $application->id]) }}" 
                           class="btn text-white px-4 py-2.5 fw-bold rounded-pill"
                           style="background-color: #0C4E2D; box-shadow: 0 4px 6px rgba(12, 78, 45, 0.15);">
                            <i class="fa-solid fa-arrows-rotate me-1"></i> Request Scholarship Renewal
                        </a>
                    @else
                        <button type="button" class="btn btn-secondary px-4 py-2.5 fw-bold rounded-pill cursor-not-allowed" disabled
                                title="Renewal opens when the current term ends.">
                            <i class="fa-solid fa-lock me-1"></i> Renewal Locked
                        </button>
                    @endif

                    <button type="button" class="btn btn-outline-danger px-4 py-2.5 fw-bold rounded-pill" onclick="triggerForfeit({{ $application->id }})">
                        <i class="fa-solid fa-circle-xmark me-1"></i> Forfeit Scholarship / Back Out
                    </button>
                </div>
                
                <div class="mt-3">
                    @if(!$canRenew && $application->academicTerm)
                        <div class="alert alert-info border-0 rounded-3 p-3 mb-0 small" style="background-color: #e0f2fe; color: #0369a1;">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            <strong>Renewal Locked:</strong> Your current scholarship term ({{ $application->academicTerm->semester }} Sem, AY {{ $application->academicTerm->academic_year }}) is still active. 
                            Renewal applications will open automatically once this term closes.
                        </div>
                    @elseif($canRenew)
                        <div class="alert alert-success border-0 rounded-3 p-3 mb-0 small" style="background-color: #dcfce7; color: #14532d;">
                            <i class="fa-solid fa-circle-check me-1"></i>
                            <strong>Renewal Open:</strong> The previous academic term is now closed. Please click "Request Scholarship Renewal" above to submit your renewal application.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Confetti for approved --}}
            <canvas id="confettiCanvas" class="confetti-container"></canvas>
        @else
            @php
                $statusClass = match($application->status) {
                    'Pending'      => 'pending',
                    'Under Review' => 'review',
                    'Approved'     => 'approved',
                    'Rejected'     => 'rejected',
                    default        => 'pending'
                };
                $statusIcon = match($application->status) {
                    'Pending'      => 'fa-hourglass-half',
                    'Under Review' => 'fa-magnifying-glass-chart',
                    'Approved'     => 'fa-award',
                    'Rejected'     => 'fa-circle-xmark',
                    default        => 'fa-hourglass-half'
                };
            @endphp

            <div class="row g-4">
                {{-- Left Column: Hero & Timeline --}}
                <div class="col-lg-7">
                    {{-- STATUS HERO BANNER --}}
                    <div class="status-hero {{ $statusClass }}">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:3;">
                            <div class="d-flex align-items-center gap-4">
                                <div class="status-hero-icon {{ $application->status === 'Approved' ? 'approved-pulse' : '' }}">
                                    <i class="fa-solid {{ $statusIcon }}"></i>
                                </div>
                                <div>
                                    <div style="font-size:0.7rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;opacity:0.7;" class="mb-1">
                                        {{ $application->program_name }}
                                    </div>
                                    <h4 class="fw-bold mb-0" style="font-family:'Poppins',sans-serif;">
                                        @if($application->status === 'Approved') 🎉 Congratulations! You are Approved!
                                        @elseif($application->status === 'Rejected') Application Not Approved
                                        @elseif($application->status === 'Under Review') Your Application is Under Review
                                        @else Your Application is Pending Review
                                        @endif
                                    </h4>
                                    @if($application->remarks && $application->status !== 'Pending')
                                    <p class="mb-0 mt-1" style="opacity:0.8;font-size:0.875rem;">
                                        <i class="fa-solid fa-quote-left me-1" style="font-size:0.65rem;opacity:0.6;"></i>
                                        {{ $application->remarks }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end monospace-data" style="opacity:0.85;font-size:0.8rem;">
                                <div>APP-{{ $application->id }}</div>
                                <div>{{ $application->created_at->format('M d, Y') }}</div>
                                @if(in_array($application->status, ['Pending', 'Under Review']))
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-danger fw-bold cancel-app-btn px-2 py-1" data-id="{{ $application->id }}" style="font-size:0.7rem; border-radius:6px; background:#b91c1c; border:none; color:white;">
                                            <i class="fa-solid fa-ban me-1"></i> Cancel Application
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- STEP PROGRESS --}}
                        <div class="mt-4" style="position:relative;z-index:3;">
                            <div class="step-track">
                                {{-- Submitted --}}
                                <div class="step-node" style="background:#22c55e;color:white;" title="Submitted">
                                    <i class="fa-solid fa-check" style="font-size:0.75rem;"></i>
                                </div>
                                <div class="step-connector {{ in_array($application->status, ['Under Review','Approved','Rejected']) ? 'done' : '' }}"></div>
                                {{-- Under Review --}}
                                @php
                                    $reviewDone = in_array($application->status, ['Under Review','Approved','Rejected']);
                                    $reviewActive = $application->status === 'Under Review';
                                @endphp
                                <div class="step-node {{ $reviewDone ? ($reviewActive ? 'active-pulse' : '') : '' }}"
                                     style="background: {{ $reviewDone ? 'rgba(255,255,255,0.9)' : 'rgba(255,255,255,0.15)' }}; color: {{ $reviewDone ? '#0369a1' : 'rgba(255,255,255,0.4)' }};"
                                     title="Under Review">
                                    <i class="fa-solid {{ $reviewActive ? 'fa-magnifying-glass' : ($reviewDone ? 'fa-check' : 'fa-magnifying-glass') }}" style="font-size:0.75rem;"></i>
                                </div>
                                <div class="step-connector {{ in_array($application->status, ['Approved','Rejected']) ? 'done' : '' }}"></div>
                                {{-- Decision --}}
                                @php $decided = in_array($application->status, ['Approved','Rejected']); @endphp
                                <div class="step-node"
                                     style="background: {{ $decided ? ($application->status === 'Approved' ? 'rgba(255,255,255,0.9)' : 'rgba(255,100,100,0.8)') : 'rgba(255,255,255,0.15)' }}; color: {{ $decided ? ($application->status === 'Approved' ? '#15803d' : 'white') : 'rgba(255,255,255,0.3)' }};"
                                     title="Decision">
                                    <i class="fa-solid {{ $decided ? ($application->status === 'Approved' ? 'fa-award' : 'fa-times') : 'fa-lock' }}" style="font-size:0.75rem;"></i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-1" style="font-size:0.68rem;font-weight:600;opacity:0.65;letter-spacing:0.3px;">
                                <span>Submitted</span>
                                <span style="flex:1;text-align:center;">OSA Review</span>
                                <span>Decision</span>
                            </div>
                        </div>
                    </div>

                    {{-- AUDIT TIMELINE --}}
                    <div class="card p-4" style="border-radius:12px; box-shadow:none;">
                        <h6 class="fw-bold text-dark mb-4">
                            <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Verification History & Audit Trail
                        </h6>

                        <div style="padding-left: 4px;">
                            @forelse($application->statusLogs as $log)
                                @php
                                    $logColor = match($log->status) {
                                        'Approved'     => ['bg' => '#22c55e', 'light' => '#dcfce7', 'text' => '#15803d', 'icon' => 'fa-award'],
                                        'Rejected'     => ['bg' => '#ef4444', 'light' => '#fee2e2', 'text' => '#b91c1c', 'icon' => 'fa-circle-xmark'],
                                        'Under Review' => ['bg' => '#0284c7', 'light' => '#e0f2fe', 'text' => '#0369a1', 'icon' => 'fa-magnifying-glass-chart'],
                                        default        => ['bg' => '#f59e0b', 'light' => '#fef9c3', 'text' => '#a16207', 'icon' => 'fa-hourglass-half'],
                                    };
                                @endphp
                                <div class="timeline-item">
                                    @if(!$loop->last)
                                        <div class="timeline-connector"></div>
                                    @endif
                                    <div class="timeline-icon" style="background: {{ $logColor['light'] }}; color: {{ $logColor['text'] }}; border: 2px solid {{ $logColor['bg'] }}30;">
                                        <i class="fa-solid {{ $logColor['icon'] }}" style="font-size:0.8rem;"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;background:{{ $logColor['light'] }};color:{{ $logColor['text'] }};font-size:0.72rem;font-weight:700;letter-spacing:0.3px;">
                                                {{ strtoupper($log->status) }}
                                            </span>
                                            <span class="text-muted small monospace-data" style="font-size:0.75rem;">
                                                <i class="fa-regular fa-clock me-1"></i>
                                                {{ $log->created_at->format('M d, Y · h:i A') }}
                                            </span>
                                        </div>
                                        @if($log->remarks)
                                        <p class="mb-0 mt-2 text-muted" style="font-size:0.82rem;line-height:1.5;">{{ $log->remarks }}</p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted small">No audit trail history available yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Right Column: Application Details --}}
                <div class="col-lg-5">
                    <div class="card p-4 h-100" style="border-radius:20px; border: 1px solid #e2e8f0; background: white;">
                        <h6 class="fw-bold text-dark mb-4">
                            <i class="fa-solid fa-file-invoice text-success me-2"></i> Submitted Application Details
                        </h6>
                        
                        <div class="d-flex flex-column gap-3 text-start">
                            <div class="border-bottom pb-2">
                                <div class="small fw-semibold text-muted mb-1">Scholarship Program</div>
                                <div class="text-dark fw-bold" style="font-size: 0.9rem;">{{ $application->program_name }}</div>
                            </div>
                            
                            @if($application->academicTerm)
                            <div class="border-bottom pb-2">
                                <div class="small fw-semibold text-muted mb-1">Academic Term</div>
                                <div class="text-dark fw-semibold" style="font-size: 0.85rem;">{{ $application->academicTerm->semester }} Semester, A.Y. {{ $application->academicTerm->academic_year }}</div>
                            </div>
                            @endif
                            
                            @if($application->gwa !== null)
                            <div class="border-bottom pb-2">
                                <div class="small fw-semibold text-muted mb-1">Submitted GWA</div>
                                <div>
                                    <span style="background:#fef9c3;color:#a16207;border:1px solid #fde047;border-radius:20px;padding:3px 12px;font-size:0.75rem;font-weight:700;">
                                        GWA: {{ $application->gwa }}
                                    </span>
                                </div>
                            </div>
                            @endif
                            
                            @if($application->document !== null)
                            <div class="border-bottom pb-2">
                                <div class="small fw-semibold text-muted mb-1">Certificate of Grades (COG)</div>
                                <div>
                                    <a href="{{ route('document.view', $application->document->id) }}" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3 fw-bold" style="border-radius: 8px; font-size: 0.75rem;">
                                        <i class="fa-solid fa-file-pdf me-1"></i> View Submitted COG
                                    </a>
                                </div>
                            </div>
                            @endif
                            
                            @if($application->customFields && $application->customFields->count() > 0)
                                <div class="mt-2">
                                    <div class="small fw-bold text-muted mb-2" style="font-size:0.7rem; letter-spacing:0.5px; text-transform:uppercase;">Custom Responses</div>
                                    <div class="d-flex flex-column gap-3">
                                        @foreach($application->customFields as $field)
                                            <div class="border-bottom pb-2">
                                                <div class="small fw-semibold text-muted mb-1">{{ $field->field_name }}</div>
                                                <div class="text-dark fw-medium" style="font-size:0.82rem;">
                                                    @php
                                                        $isFieldFile = Str::startsWith($field->field_value, 'uploads/') || 
                                                                      (Str::startsWith($field->field_value, 'http') && 
                                                                       collect(['.pdf', '.png', '.jpg', '.jpeg', '.docx', '.gif', '.webp', '.xlsx', '.xls', '.csv'])->contains(fn($ext) => Str::endsWith(strtolower($field->field_value), $ext)));
                                                    @endphp
                                                    @if($isFieldFile)
                                                        <a href="{{ route('application-field.file', $field->id) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" style="border-radius: 6px; font-size: 0.72rem;">
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
                    </div>
                </div>
            </div>
        @endif

    @else

    {{-- EMPTY STATE --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Welcome, {{ auth()->user()->name }}! 👋</h4>
            <p class="text-muted small mb-0">Track your scholarship applications and requirements here.</p>
        </div>
        <a href="{{ route('student.apply') }}" class="btn fw-bold shadow-sm px-4 py-2"
           style="background: linear-gradient(135deg, var(--clsu-green), #16703f); color: white; border-radius: 10px; white-space: nowrap;">
            <i class="fa-solid fa-plus me-1"></i> New Application
        </a>
    </div>

    <div class="empty-card">
        <div style="width:90px;height:90px;border-radius:22px;background:linear-gradient(135deg,#f1f5f9,#e2e8f0);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
            <i class="fa-solid fa-folder-open fa-2x" style="color:#94a3b8;"></i>
        </div>
        <h4 class="fw-bold mb-2">No Active Applications</h4>
        <p class="text-muted mx-auto mb-4" style="max-width: 380px; font-size: 0.9rem;">
            You haven't submitted any scholarship applications yet. Check out the available grants and start your journey!
        </p>
        <a href="{{ route('student.apply') }}" class="btn fw-bold px-5 py-3 rounded-pill"
           style="background: linear-gradient(135deg, var(--clsu-green), #16703f); color: white; font-size: 1rem;">
            <i class="fa-solid fa-paper-plane me-2"></i> Submit an Application
        </a>
    </div>
    @endif

    @if(isset($cancelledApplications) && $cancelledApplications->count() > 0)
        <div class="card p-4 mt-4" style="border-radius:12px; border: 1px solid var(--border-color); background: var(--card-bg); box-shadow: none;">
            <h6 class="fw-bold text-dark mb-3">
                <i class="fa-solid fa-trash-can text-danger me-2"></i> My Cancelled Applications (History)
            </h6>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr class="text-muted small">
                            <th>Ref ID</th>
                            <th>Scholarship Program</th>
                            <th>GWA</th>
                            <th>Status When Cancelled</th>
                            <th>Cancelled Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cancelledApplications as $cApp)
                            <tr>
                                <td class="fw-bold text-dark monospace-data" style="font-size:0.8rem;">APP-{{ $cApp->id }}</td>
                                <td class="fw-semibold text-dark" style="font-size:0.82rem;">{{ $cApp->program_name }}</td>
                                <td><span class="badge bg-light text-dark monospace-data" style="border: 1px solid var(--border-color);">{{ $cApp->gwa }}</span></td>
                                <td>
                                    <span class="badge bg-secondary text-white">{{ $cApp->status }}</span>
                                </td>
                                <td class="text-muted small monospace-data" style="font-size:0.75rem;">{{ $cApp->deleted_at->format('M d, Y · h:i A') }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        @if(!$application || $application->status !== 'Pending')
                                            <button type="button" class="btn btn-sm btn-outline-success fw-bold restore-app-btn" data-id="{{ $cApp->id }}" style="font-size:0.75rem; border-radius:8px;">
                                                <i class="fa-solid fa-trash-arrow-up me-1"></i> Restore
                                            </button>
                                        @endif
                                        @if($cApp->status === 'Pending')
                                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold withdraw-app-btn" data-id="{{ $cApp->id }}" style="font-size:0.75rem; border-radius:8px;">
                                                <i class="fa-solid fa-times-circle me-1"></i> Withdraw
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted small">No cancelled applications.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
@if(isset($application) && $application->status === 'Approved')
    // Simple confetti effect
    (function(){
        const canvas = document.getElementById('confettiCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const colors = ['#F2A900','#0F5934','#22c55e','#fbbf24','#34d399','#60a5fa'];
        const pieces = Array.from({length: 120}, () => ({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height - canvas.height,
            w: Math.random() * 10 + 5,
            h: Math.random() * 6 + 3,
            color: colors[Math.floor(Math.random() * colors.length)],
            r: Math.random() * Math.PI * 2,
            rx: Math.random() * 0.3 - 0.15,
            vy: Math.random() * 3 + 2,
            vx: Math.random() * 2 - 1,
        }));

        let frame = 0;
        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            pieces.forEach(p => {
                ctx.save();
                ctx.translate(p.x, p.y);
                ctx.rotate(p.r);
                ctx.fillStyle = p.color;
                ctx.fillRect(-p.w/2, -p.h/2, p.w, p.h);
                ctx.restore();
                p.x += p.vx; p.y += p.vy; p.r += p.rx;
                if (p.y > canvas.height) { p.y = -10; p.x = Math.random() * canvas.width; }
            });
            frame++;
            if (frame < 180) requestAnimationFrame(draw);
            else ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
        draw();
    })();
@endif

    // Deletion Management Event Handlers
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.cancel-app-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!confirm('Are you sure you want to cancel this application? This will withdraw it from the OSA review pipeline and allow you to submit a new one.')) return;
            
            const id = this.dataset.id;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Cancelling...';
            
            fetch(`/application/${id}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error cancelling application.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-ban me-1"></i> Cancel Application';
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-ban me-1"></i> Cancel Application';
            });
        });
    });

    document.querySelectorAll('.restore-app-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to restore this application?')) return;
            
            const id = this.dataset.id;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Restoring...';
            
            fetch(`/application/${id}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-trash-arrow-up me-1"></i> Restore';
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-trash-arrow-up me-1"></i> Restore';
            });
        });
    });

    document.querySelectorAll('.withdraw-app-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('WARNING: Are you sure you want to permanently delete and withdraw this application? This action is irreversible and will delete all files from storage.')) return;
            
            const id = this.dataset.id;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Deleting...';
            
            fetch(`/application/${id}/withdraw`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-times-circle me-1"></i> Withdraw';
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-times-circle me-1"></i> Withdraw';
            });
        });
    });

    // Forfeit SweetAlert2 Modal
    window.triggerForfeit = function(appId) {
        Swal.fire({
            title: 'Forfeit Scholarship?',
            text: 'Are you sure you want to back out and forfeit this scholarship? This action is irreversible and will release your slot.',
            icon: 'warning',
            input: 'textarea',
            inputPlaceholder: 'Please enter your reason for backing out (mandatory)...',
            inputAttributes: {
                'aria-label': 'Reason for backing out'
            },
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#475569',
            confirmButtonText: 'Yes, Forfeit It',
            preConfirm: (reason) => {
                if (!reason || reason.trim().length < 5) {
                    Swal.showValidationMessage('A detailed reason (at least 5 characters) is required.');
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                // Submit form dynamically
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/application/${appId}/forfeit`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = "{{ csrf_token() }}";
                
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'reason';
                reasonInput.value = result.value;
                
                form.appendChild(csrfInput);
                form.appendChild(reasonInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    };
</script>

@if(!auth()->user()->has_completed_tour)
    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css">
    <style>
        .shepherd-element {
            background: white !important;
            border-radius: 12px !important;
            border: 0 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            padding: 1.25rem !important;
        }
        .shepherd-button {
            border-radius: 50px !important;
            font-weight: 600 !important;
            padding: 0.5rem 1.25rem !important;
            font-size: 0.8rem !important;
        }
        .shepherd-button-primary {
            background-color: #0C4E2D !important;
            color: white !important;
        }
        .shepherd-button-secondary {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
        }
        .shepherd-title {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 700 !important;
            color: #1e293b !important;
        }
        .shepherd-text {
            font-size: 0.85rem !important;
            color: #475569 !important;
            line-height: 1.5 !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/js/shepherd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tour = new Shepherd.Tour({
                useModalOverlay: true,
                defaultStepOptions: {
                    classes: 'shadow-lg rounded-4 border-0 p-3',
                    scrollTo: { behavior: 'smooth', block: 'center' }
                }
            });

            tour.addStep({
                id: 'step-nav',
                title: 'Portal Navigation',
                text: 'Navigate through the dashboard, apply for scholarships, and manage your profile here.',
                attachTo: { element: '#mainSidebar', on: 'right' },
                buttons: [
                    { text: 'Skip', action: tour.complete, classes: 'shepherd-button-secondary' },
                    { text: 'Next', action: tour.next, classes: 'shepherd-button-primary' }
                ]
            });

            tour.addStep({
                id: 'step-welcome',
                title: 'Official Updates',
                text: 'This feed shows announcements, deadlines, and real-time updates from the OSA team.',
                attachTo: { element: '.container-fluid', on: 'top' },
                buttons: [
                    { text: 'Finish', action: tour.complete, classes: 'shepherd-button-primary' }
                ]
            });

            const markTourComplete = () => {
                fetch("{{ route('student.complete-tour') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).catch(err => console.error(err));
            };

            tour.on('complete', markTourComplete);
            tour.on('cancel', markTourComplete);

            // Start tour after a brief delay
            setTimeout(() => tour.start(), 1500);
        });
    </script>
    @endpush
@endif
@endpush