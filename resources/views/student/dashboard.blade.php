@extends('layouts.app')

@section('title', 'My Application | A.E.G.I.S.')

@push('styles')
<style>
    /* Status Hero Banner */
    .status-hero {
        border-radius: 20px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        color: white;
    }

    .status-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }

    .status-hero::after {
        content: '';
        position: absolute;
        bottom: -60px; right: 80px;
        width: 280px; height: 280px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }

    .status-hero.pending  { background: linear-gradient(135deg, #b45309, #d97706); }
    .status-hero.review   { background: linear-gradient(135deg, #0369a1, #0284c7); }
    .status-hero.approved { background: linear-gradient(135deg, #15803d, #16a34a); }
    .status-hero.rejected { background: linear-gradient(135deg, #b91c1c, #dc2626); }
    .status-hero.empty    { background: linear-gradient(135deg, #334155, #475569); }

    .status-hero-icon {
        width: 64px; height: 64px;
        border-radius: 18px;
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
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        flex-shrink: 0;
    }

    .step-connector {
        flex: 1;
        height: 3px;
        background: rgba(255,255,255,0.2);
        position: relative;
    }

    .step-connector.done { background: rgba(255,255,255,0.7); }

    /* Pulse animation on active step */
    @keyframes pulse-ring {
        0%   { box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
        70%  { box-shadow: 0 0 0 10px rgba(255,255,255,0); }
        100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
    }

    .step-node.active-pulse { animation: pulse-ring 2s infinite; }

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
        background: linear-gradient(to bottom, #e2e8f0, transparent);
    }

    .timeline-content {
        flex: 1;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }

    /* Confetti */
    .confetti-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999; }

    /* Empty state */
    .empty-card {
        background: white;
        border-radius: 20px;
        border: 2px dashed #e2e8f0;
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
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="max-width: 1040px; margin: 0 auto; padding: 1.5rem 1rem 3rem;">

    @if($application)
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
                        <div class="text-end" style="opacity:0.75;font-size:0.8rem;">
                            <div>APP-{{ $application->id }}</div>
                            <div>{{ $application->created_at->format('M d, Y') }}</div>
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
                <div class="card p-4" style="border-radius:20px;">
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
                                        <span class="text-muted" style="font-size:0.75rem;">
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
                        
                        <div class="border-bottom pb-2">
                            <div class="small fw-semibold text-muted mb-1">Submitted GWA</div>
                            <div>
                                <span style="background:#fef9c3;color:#a16207;border:1px solid #fde047;border-radius:20px;padding:3px 12px;font-size:0.75rem;font-weight:700;">
                                    GWA: {{ $application->gwa }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="border-bottom pb-2">
                            <div class="small fw-semibold text-muted mb-1">Certificate of Grades (COG)</div>
                            <div>
                                @if($application->document)
                                    <a href="{{ route('document.view', $application->document->id) }}" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3 fw-bold" style="border-radius: 8px; font-size: 0.75rem;">
                                        <i class="fa-solid fa-file-pdf me-1"></i> View Submitted COG
                                    </a>
                                @else
                                    <span class="text-muted small">No document uploaded.</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($application->customFields && $application->customFields->count() > 0)
                            <div class="mt-2">
                                <div class="small fw-bold text-muted mb-2" style="font-size:0.7rem; letter-spacing:0.5px; text-transform:uppercase;">Custom Responses</div>
                                <div class="d-flex flex-column gap-3">
                                    @foreach($application->customFields as $field)
                                        <div class="border-bottom pb-2">
                                            <div class="small fw-semibold text-muted mb-1">{{ $field->field_name }}</div>
                                            <div class="text-dark fw-medium" style="font-size:0.82rem;">
                                                @if(Str::startsWith($field->field_value, 'uploads/'))
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

        {{-- Confetti for approved --}}
        @if($application->status === 'Approved')
        <canvas id="confettiCanvas" class="confetti-container"></canvas>
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
</script>
@endpush