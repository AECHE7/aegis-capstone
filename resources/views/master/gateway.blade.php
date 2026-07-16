@extends('layouts.app')

@section('title', 'Master Gatekeeper Portal | A.E.G.I.S.')
@section('page-title', 'Master Gateway')
@section('page-subtitle', 'Dynamically switch system roles or securely transfer Master privileges')

@section('content')
<div class="container-fluid px-0" style="max-width: 1100px; margin: 0 auto; padding: 1.5rem 1rem 3rem;">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 mb-4 shadow-sm" role="status" aria-live="polite" style="border-radius: 12px; background: #dcfce7; color: #14532d; border-left: 4px solid #22c55e !important;">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-check me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 mb-4 shadow-sm" role="alert" style="border-radius: 12px; background: #fee2e2; color: #7f1d1d; border-left: 4px solid #ef4444 !important;">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-exclamation me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- LEFT: Role Selector Cards -->
        <div class="col-lg-6">
            <div class="card border-0 p-4 h-100" style="border-radius: 20px;">
                <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-shapes text-success me-2"></i> Select Active Workspace Role</h5>
                <p class="text-muted small mb-4">Choose which portal workspace role you want to activate. This session-level change updates the entire UI interface dynamically without affecting your actual account identity.</p>
                
                <div class="d-flex flex-column gap-3">
                    <!-- Student Option -->
                    <form action="{{ route('master.switch-role') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="student">
                        <button type="submit" class="w-100 text-start btn p-3 border border-light d-flex align-items-center gap-3 bg-light-hover" style="border-radius: 16px; transition: all 0.2s; background: white;">
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(25,135,84,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="fa-solid fa-user-graduate text-success fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark mb-0 d-flex align-items-center justify-content-between">
                                    <span>Student Applicant</span>
                                    @if(auth()->user()->role === 'student')
                                        <span class="badge bg-success rounded-pill fw-semibold" style="font-size:0.65rem; padding: 4px 8px;">Active</span>
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size: 0.78rem; font-weight: normal; white-space: normal; line-height: 1.4;">Submit scholarship applications, track requirements checklist, upload documents, and view AI scan details.</div>
                            </div>
                        </button>
                    </form>

                    <!-- Admin Option -->
                    <form action="{{ route('master.switch-role') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="admin">
                        <button type="submit" class="w-100 text-start btn p-3 border border-light d-flex align-items-center gap-3 bg-light-hover" style="border-radius: 16px; transition: all 0.2s; background: white;">
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(13,110,253,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="fa-solid fa-user-shield text-primary fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark mb-0 d-flex align-items-center justify-content-between">
                                    <span>OSA Administrator</span>
                                    @if(auth()->user()->role === 'admin')
                                        <span class="badge bg-primary rounded-pill fw-semibold" style="font-size:0.65rem; padding: 4px 8px;">Active</span>
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size: 0.78rem; font-weight: normal; white-space: normal; line-height: 1.4;">Access review queue, evaluate applications, triggers AI fraud analysis, and manage announcements feed.</div>
                            </div>
                        </button>
                    </form>

                    <!-- Director Option -->
                    <form action="{{ route('master.switch-role') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="superadmin">
                        <button type="submit" class="w-100 text-start btn p-3 border border-light d-flex align-items-center gap-3 bg-light-hover" style="border-radius: 16px; transition: all 0.2s; background: white;">
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(217,119,6,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="fa-solid fa-crown text-warning fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark mb-0 d-flex align-items-center justify-content-between">
                                    <span>OSA Director</span>
                                    @if(auth()->user()->role === 'superadmin')
                                        <span class="badge bg-warning text-dark rounded-pill fw-semibold" style="font-size:0.65rem; padding: 4px 8px;">Active</span>
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size: 0.78rem; font-weight: normal; white-space: normal; line-height: 1.4;">Access system-wide analytics, invite staff, configure active settings parameters, and export full audit log histories.</div>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT: Privilege Transfer & Activity -->
        <div class="col-lg-6">
            <div class="card border-0 p-4 h-100" style="border-radius: 20px;">
                <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-share-nodes text-warning me-2"></i> Pass Master Privilege</h5>
                <p class="text-muted small mb-4">Transfer full role-switching Master capability to another email address. <strong>Warning:</strong> The moment the recipient email accepts the invitation, your master access is immediately revoked.</p>

                <!-- Transfer form -->
                <form action="{{ route('master.transfer') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label for="recipientEmailInput" class="form-label fw-semibold text-dark small">Recipient Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="recipient_email" id="recipientEmailInput" class="form-control border-start-0" placeholder="e.g. user@clsu.edu.ph" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold rounded-pill py-2 text-dark" style="background: linear-gradient(135deg, var(--clsu-gold), #e09500); border: none;">
                        <i class="fa-solid fa-paper-plane me-1"></i> Send Transfer Invitation
                    </button>
                </form>

                <hr class="my-4 border-light">

                <!-- History list -->
                <h6 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-clock-rotate-left me-2"></i> Recent Transfer Logs</h6>
                <div class="d-flex flex-column gap-2" style="max-height: 250px; overflow-y: auto;">
                    @forelse($pendingTransfers as $pt)
                        <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center bg-light" style="border-radius: 12px !important; border: 1px dashed var(--clsu-gold) !important;">
                            <div>
                                <div class="fw-bold text-dark small" style="word-break: break-all;">To: {{ $pt->recipient_email }}</div>
                                <div class="text-muted" style="font-size:0.7rem;">Expires: {{ $pt->expires_at->format('M d, Y h:i A') }}</div>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill fw-semibold" style="font-size: 0.65rem; padding: 4px 8px;">Pending</span>
                        </div>
                    @empty
                        <!-- If no pending, show standard completed list -->
                    @endforelse

                    @forelse($completedTransfers as $ct)
                        @if($ct->accepted_at)
                            <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center bg-white" style="border-radius: 12px !important; border-color: #e2e8f0 !important;">
                                <div>
                                    <div class="fw-bold text-dark small" style="word-break: break-all;">To: {{ $ct->recipient_email }}</div>
                                    <div class="text-muted" style="font-size:0.7rem;">Accepted: {{ $ct->accepted_at->format('M d, Y h:i A') }}</div>
                                </div>
                                <span class="badge bg-success rounded-pill fw-semibold" style="font-size: 0.65rem; padding: 4px 8px;">Completed</span>
                            </div>
                        @else
                            <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center bg-light" style="border-radius: 12px !important; opacity: 0.6;">
                                <div>
                                    <div class="fw-bold text-muted small" style="word-break: break-all;">To: {{ $ct->recipient_email }}</div>
                                    <div class="text-muted" style="font-size:0.7rem;">Expired invitation</div>
                                </div>
                                <span class="badge bg-secondary rounded-pill fw-semibold" style="font-size: 0.65rem; padding: 4px 8px;">Expired</span>
                            </div>
                        @endif
                    @empty
                        @if($pendingTransfers->isEmpty())
                            <div class="text-center py-4 text-muted small">No recent transfer activities logged.</div>
                        @endif
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
