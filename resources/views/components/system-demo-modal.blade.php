<!-- ── A.E.G.I.S. INTERACTIVE SYSTEM DEMO & GUIDED ROLE TOUR MODAL ── -->
@php
    $userRole = auth()->check() ? (auth()->user()->role ?? 'student') : 'student';
    $isStudent = $userRole === 'student';
    $isAdmin = $userRole === 'admin';
    $isSuperAdmin = $userRole === 'superadmin';
@endphp
<div class="modal fade" id="systemDemoModal" tabindex="-1" aria-labelledby="systemDemoModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            
            <!-- Modal Header -->
            <div class="modal-header border-0 text-white p-3 p-md-4" style="background: linear-gradient(135deg, #072F1B 0%, #0C4E2D 50%, #00754A 100%);">
                <div class="d-flex align-items-center gap-2.5 gap-md-3">
                    <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; min-width: 44px;">
                        <i class="fa-solid fa-graduation-cap text-warning fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-0.5 flex-wrap">
                            <h5 class="modal-title fw-bold mb-0 text-white" id="systemDemoModalLabel" style="font-size: 1.05rem;">
                                A.E.G.I.S. Interactive System Demo & Guided Walkthrough
                            </h5>
                            <span class="badge fw-bold px-2.5 py-1 rounded-pill" style="background-color: #f59e0b !important; color: #111827 !important; font-size: 0.68rem; border: 1px solid #d97706; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                Interactive Training Suite
                            </span>
                        </div>
                        <p class="mb-0 text-white-50 small d-none d-sm-block" style="font-size: 0.78rem;">
                            @if($isStudent)
                                Explore the 6-stage student application life-cycle, AI grade verification, and official award clearance process.
                            @else
                                Click any stage card below for deep-dive technical specs, or launch the interactive simulator to experience all portal roles.
                            @endif
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Role Selector Nav Tabs -->
            @if($isStudent)
                {{-- Student Role: Dedicated applicant guide header without staff/director tabs --}}
                <div class="bg-light px-3 px-md-4 py-2.5 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1.5 shadow-2xs" style="background: var(--clsu-green, #0C4E2D); color: white; font-size: 0.8rem;">
                            <i class="fa-solid fa-user-graduate"></i> Student Applicant Guide
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 0.68rem;">
                            YOUR ACTIVE PORTAL
                        </span>
                    </div>
                    <span class="badge bg-light-subtle text-muted border rounded-pill px-2.5 py-1 small d-none d-sm-inline-flex align-items-center gap-1">
                        <i class="fa-solid fa-hand-pointer text-success"></i> Click any stage to inspect technical specs
                    </span>
                </div>
            @else
                <!-- Role Selector Nav Tabs for Staff / Admin -->
                <div class="bg-light px-3 px-md-4 py-2.5 border-bottom">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <ul class="nav nav-pills gap-1.5 gap-md-2 d-flex flex-nowrap overflow-x-auto pb-1 mb-0" id="demoRoleTabs" role="tablist" style="scrollbar-width: none; -webkit-overflow-scrolling: touch;">
                            <li class="nav-item flex-shrink-0" role="presentation">
                                <button class="nav-link px-3 py-1.5 rounded-pill fw-semibold d-flex align-items-center gap-1.5 {{ $isStudent ? 'active' : '' }}" 
                                        id="student-demo-tab" data-bs-toggle="pill" data-bs-target="#student-demo-pane" type="button" role="tab"
                                        aria-controls="student-demo-pane" aria-selected="{{ $isStudent ? 'true' : 'false' }}" data-role="student">
                                    <i class="fa-solid fa-user-graduate"></i>
                                    <span>Student Applicant</span>
                                    @if($isStudent)
                                        <span class="badge bg-light text-success ms-1 d-none d-sm-inline" style="font-size: 0.62rem;">YOUR ROLE</span>
                                    @endif
                                </button>
                            </li>
                            @if($isAdmin || $isSuperAdmin)
                                <li class="nav-item flex-shrink-0" role="presentation">
                                    <button class="nav-link px-3 py-1.5 rounded-pill fw-semibold d-flex align-items-center gap-1.5 {{ $isAdmin ? 'active' : '' }}" 
                                            id="admin-demo-tab" data-bs-toggle="pill" data-bs-target="#admin-demo-pane" type="button" role="tab"
                                            aria-controls="admin-demo-pane" aria-selected="{{ $isAdmin ? 'true' : 'false' }}" data-role="admin">
                                        <i class="fa-solid fa-user-shield"></i>
                                        <span>OSA Staff Evaluator</span>
                                        @if($isAdmin)
                                            <span class="badge bg-light text-primary ms-1 d-none d-sm-inline" style="font-size: 0.62rem;">YOUR ROLE</span>
                                        @endif
                                    </button>
                                </li>
                            @endif
                            @if($isSuperAdmin)
                                <li class="nav-item flex-shrink-0" role="presentation">
                                    <button class="nav-link px-3 py-1.5 rounded-pill fw-semibold d-flex align-items-center gap-1.5 {{ $isSuperAdmin ? 'active' : '' }}" 
                                            id="director-demo-tab" data-bs-toggle="pill" data-bs-target="#director-demo-pane" type="button" role="tab"
                                            aria-controls="director-demo-pane" aria-selected="{{ $isSuperAdmin ? 'true' : 'false' }}" data-role="superadmin">
                                        <i class="fa-solid fa-crown text-warning"></i>
                                        <span>OSA Director / Super Admin</span>
                                        @if($isSuperAdmin)
                                            <span class="badge bg-light text-warning-emphasis ms-1 d-none d-sm-inline" style="font-size: 0.62rem;">YOUR ROLE</span>
                                        @endif
                                    </button>
                                </li>
                            @endif
                        </ul>
                        <span class="badge bg-light-subtle text-muted border rounded-pill px-2.5 py-1 small d-none d-lg-inline-flex align-items-center gap-1">
                            <i class="fa-solid fa-hand-pointer text-success"></i> Click any card to inspect technical specs
                        </span>
                    </div>
                </div>
            @endif

            <!-- Modal Body with Tabs Content -->
            <div class="modal-body p-3 p-md-4 bg-light bg-opacity-50">
                <div class="tab-content" id="demoRoleTabsContent">

                    <!-- ========================================================= -->
                    <!-- TAB 1: STUDENT APPLICANT WORKFLOW                         -->
                    <!-- ========================================================= -->
                    <div class="tab-pane fade {{ $isStudent ? 'show active' : '' }}" id="student-demo-pane" role="tabpanel" aria-labelledby="student-demo-tab">
                        
                        <!-- Role Banner -->
                        <div class="demo-role-banner demo-role-banner-student rounded-4 p-3 p-md-4 mb-3 mb-md-4 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                                        <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background: rgba(255,255,255,0.2) !important; color: #ffffff !important; font-size: 0.72rem; border: 1px solid rgba(255,255,255,0.3);">STUDENT PORTAL LIFE-CYCLE</span>
                                        @if($isStudent)
                                            <span class="badge badge-active-role px-2.5 py-1 rounded-pill">Active Account Role</span>
                                        @else
                                            <span class="badge badge-preview-role px-2.5 py-1 rounded-pill">Demonstration View</span>
                                        @endif
                                    </div>
                                    <h5 class="fw-bold mb-1 text-white" style="font-size: 1.05rem;">Paperless Application, Integrity Check & Award Tracking</h5>
                                    <p class="small mb-0" style="color: rgba(255, 255, 255, 0.9) !important; font-size: 0.8rem;">From registering with your verified @clsu.edu.ph institutional email to receiving your official stipend clearance report.</p>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    @if(auth()->check() && auth()->user()->role === 'student')
                                        <a href="{{ route('student.dashboard') }}" class="btn btn-sm btn-banner-dash fw-semibold px-3 py-1.5 rounded-pill">
                                            <i class="fa-solid fa-house me-1"></i> Go to Dashboard
                                        </a>
                                        <a href="{{ route('student.apply') }}" class="btn btn-sm btn-banner-action px-3 py-1.5 rounded-pill">
                                            <i class="fa-solid fa-paper-plane me-1"></i> Apply for Scholarship
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-banner-dash fw-semibold px-3 py-1.5 rounded-pill" onclick="openRoleSimulator('student')">
                                            <i class="fa-solid fa-play me-1"></i> Preview Student Simulation
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 6-Stage Detailed Grid -->
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-2.5 g-md-3">
                            <!-- Stage 1 -->
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-student" onclick="openStageDetailModal('student', 1)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">STAGE 01</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Institutional Registration</h6>
                                    <p class="text-muted small mb-2">Register using your official <code>@clsu.edu.ph</code> student email and accept the <strong>Data Privacy Act (R.A. 10173)</strong> consent.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>6-digit email OTP activation</li>
                                        <li>NPC Seal of Registration verified</li>
                                        <li>30-day trusted browser tokens</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Stage 2 -->
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-student" onclick="openStageDetailModal('student', 2)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">STAGE 02</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Profile Completion</h6>
                                    <p class="text-muted small mb-2">Encode your CLSU Student ID number, enrolled College, Degree Program, and Year Level before applying.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>One-time basic profile onboarding</li>
                                        <li>Automatic redirect protection</li>
                                        <li>Emergency contacts & guardian info</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Stage 3 -->
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-student" onclick="openStageDetailModal('student', 3)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">STAGE 03</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Explore & Select Grant</h6>
                                    <p class="text-muted small mb-2">Browse the live scholarship catalog for Institutional (University/College Scholar), DOST, CHED, or Varsity grants.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Transparent criteria & max GWA cutoffs</li>
                                        <li>Stipend amounts & renewal rules</li>
                                        <li>Single active application enforcement</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Stage 4 -->
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-student" onclick="openStageDetailModal('student', 4)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">STAGE 04</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Apply & Upload COG</h6>
                                    <p class="text-muted small mb-2">Fill out the step-by-step application form with local auto-save draft resilience and upload your Certificate of Grades.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Auto-saves form drafts locally</li>
                                        <li>Accepts PDF, JPG, and PNG uploads</li>
                                        <li>After-work-hours queue transparency</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Stage 5 -->
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-student" onclick="openStageDetailModal('student', 5)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">STAGE 05</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">AI Grade Integrity Scan</h6>
                                    <p class="text-muted small mb-2">Uploaded transcripts pass through the ResNet-50 ELA & OCR neural network for tamper verification.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Instant pixel noise & ELA inspection</li>
                                        <li>GWA transcript discrepancy audit</li>
                                        <li>Camera sensor EXIF integrity check</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Stage 6 -->
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-student" onclick="openStageDetailModal('student', 6)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">STAGE 06</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Clearance & Award Notice</h6>
                                    <p class="text-muted small mb-2">Upon evaluation approval, receive an official award confirmation with your signed application PDF and stipend schedule.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Real-time dashboard status badge</li>
                                        <li>Official signed PDF approval document</li>
                                        <li>Eligible for semester renewals</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>

                    @if($isAdmin || $isSuperAdmin)
                    <!-- ========================================================= -->
                    <!-- TAB 2: OSA STAFF EVALUATOR WORKFLOW                       -->
                    <!-- ========================================================= -->
                    <div class="tab-pane fade {{ $isAdmin ? 'show active' : '' }}" id="admin-demo-pane" role="tabpanel" aria-labelledby="admin-demo-tab">
                        
                        <!-- Role Banner -->
                        <div class="demo-role-banner demo-role-banner-admin rounded-4 p-3 p-md-4 mb-3 mb-md-4 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                                        <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background: rgba(255,255,255,0.2) !important; color: #ffffff !important; font-size: 0.72rem; border: 1px solid rgba(255,255,255,0.3);">OSA STAFF EVALUATOR WORKFLOW</span>
                                        @if($isAdmin)
                                            <span class="badge badge-active-role px-2.5 py-1 rounded-pill">Active Account Role</span>
                                        @else
                                            <span class="badge badge-preview-role px-2.5 py-1 rounded-pill">Demonstration View</span>
                                        @endif
                                    </div>
                                    <h5 class="fw-bold mb-1 text-white" style="font-size: 1.05rem;">Application Triage, Forensic Heatmaps & Rapid Determination</h5>
                                    <p class="small mb-0" style="color: rgba(255, 255, 255, 0.9) !important; font-size: 0.8rem;">Evaluate student applications with AI-assisted document fraud analysis, 1-click preset remarks, and automated audits.</p>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin'))
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-banner-dash fw-semibold px-3 py-1.5 rounded-pill">
                                            <i class="fa-solid fa-list-check me-1"></i> Go to Review Queue
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-banner-action px-3 py-1.5 rounded-pill" onclick="openRoleSimulator('admin')">
                                            <i class="fa-solid fa-play me-1"></i> Launch Staff Simulator
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 6-Stage Detailed Grid -->
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-2.5 g-md-3">
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-admin" onclick="openStageDetailModal('admin', 1)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">STEP 01</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Live Review Queue</h6>
                                    <p class="text-muted small mb-2">Manage incoming student applications filtered by status (Pending, Under Review, Queued), college, and GWA standing.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Real-time SLA turn-around tracking</li>
                                        <li>Assigned coordinator indicators</li>
                                        <li>Bulk action approval options</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-admin" onclick="openStageDetailModal('admin', 2)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">STEP 02</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Forensic Dual-Pane Inspection</h6>
                                    <p class="text-muted small mb-2">Inspect Certificate of Grades side-by-side with neural Error Level Analysis (ELA) and Grad-CAM heatmaps.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>High-resolution pan & zoom inspection</li>
                                        <li>Neural tamper probability rating</li>
                                        <li>1-click "Test COG Fixtures" toolbar dropdown</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-admin" onclick="openStageDetailModal('admin', 3)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">STEP 03</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">OCR Discrepancy Gate</h6>
                                    <p class="text-muted small mb-2">Cross-examine student self-encoded GWA against raw OCR extracted grades directly from the registrar's seal.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Automated GWA difference calculation</li>
                                        <li>Instant flag if discrepancy > tolerance</li>
                                        <li>Prevents accidental qualification errors</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-admin" onclick="openStageDetailModal('admin', 4)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">STEP 04</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">1-Click Fast Remarks</h6>
                                    <p class="text-muted small mb-2">Accelerate review times using curated preset remarks for missing requirements, blurriness, or clear qualifications.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Private staff evaluator notes</li>
                                        <li>One-click re-upload requirement request</li>
                                        <li>Audited remarks timestamp</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-admin" onclick="openStageDetailModal('admin', 5)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">STEP 05</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Status Determination & Revocation</h6>
                                    <p class="text-muted small mb-2">Execute official determinations: Approve, Reject, or Revoke grant with mandatory audit reason capture.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Automatic notification to scholar</li>
                                        <li>Renewal vs new grant auto-tagging</li>
                                        <li>Trash recovery safeguard for accidental deletions</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-admin" onclick="openStageDetailModal('admin', 6)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">STEP 06</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">1-Page Forensic Audit PDF</h6>
                                    <p class="text-muted small mb-2">Export signed official forensic inspection certificates with QR verification, sensor breakdown, and evaluator signature.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Official institutional legal certificate</li>
                                        <li>Complete evidence chain of custody</li>
                                        <li>CSV batch export for university records</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endif

                    @if($isSuperAdmin)
                    <!-- ========================================================= -->
                    <!-- TAB 3: DIRECTOR & SUPER ADMIN WORKFLOW                    -->
                    <!-- ========================================================= -->
                    <div class="tab-pane fade {{ $isSuperAdmin ? 'show active' : '' }}" id="director-demo-pane" role="tabpanel" aria-labelledby="director-demo-tab">
                        
                        <!-- Role Banner -->
                        <div class="demo-role-banner demo-role-banner-director rounded-4 p-3 p-md-4 mb-3 mb-md-4 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                                        <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background: rgba(255,255,255,0.2) !important; color: #ffffff !important; font-size: 0.72rem; border: 1px solid rgba(255,255,255,0.3);">DIRECTOR & SUPERADMIN WORKFLOW</span>
                                        @if($isSuperAdmin)
                                            <span class="badge badge-active-role px-2.5 py-1 rounded-pill">Active Account Role</span>
                                        @else
                                            <span class="badge badge-preview-role px-2.5 py-1 rounded-pill">Demonstration View</span>
                                        @endif
                                    </div>
                                    <h5 class="fw-bold mb-1 text-white" style="font-size: 1.05rem;">Executive Analytics, User Control & Program Governance</h5>
                                    <p class="small mb-0" style="color: rgba(255, 255, 255, 0.9) !important; font-size: 0.8rem;">Oversee university scholarship budgets, audit statutory compliance, manage staff permissions, and calibrate AI models.</p>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    @if(auth()->check() && auth()->user()->role === 'superadmin')
                                        <a href="{{ route('superadmin.analytics') }}" class="btn btn-sm btn-banner-dash fw-semibold px-3 py-1.5 rounded-pill">
                                            <i class="fa-solid fa-chart-line me-1"></i> Open Analytics
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-banner-action px-3 py-1.5 rounded-pill" onclick="openRoleSimulator('superadmin')">
                                            <i class="fa-solid fa-play me-1"></i> Launch Director Simulator
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 6-Stage Detailed Grid -->
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-2.5 g-md-3">
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-director" onclick="openStageDetailModal('superadmin', 1)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1">MODULE 01</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Executive Analytics</h6>
                                    <p class="text-muted small mb-2">Real-time KPI metrics tracking active scholars, total grants awarded, college demographic distribution, and GWA averages.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Active academic term filtering</li>
                                        <li>Grant allocation breakdown charts</li>
                                        <li>Application conversion funnels</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-director" onclick="openStageDetailModal('superadmin', 2)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1">MODULE 02</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Complete User Management</h6>
                                    <p class="text-muted small mb-2">Manage both Student and Staff accounts: toggle active status, invite new evaluators, reset MFA tokens, and send password resets.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Instant account deactivation/reactivation</li>
                                        <li>MFA security reset in 1 click</li>
                                        <li>Staff scholarship assignment controls</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-director" onclick="openStageDetailModal('superadmin', 3)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1">MODULE 03</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Scholarship Programs</h6>
                                    <p class="text-muted small mb-2">Configure institutional, government, and private scholarship programs with tailored eligibility rules and criteria.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Set minimum GWA & slot limits</li>
                                        <li>Add custom application form fields</li>
                                        <li>Define semester renewal boundaries</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-director" onclick="openStageDetailModal('superadmin', 4)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1">MODULE 04</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Announcement Management</h6>
                                    <p class="text-muted small mb-2">Publish, edit, and schedule urgent university broadcasts, requirements notices, and semester application windows.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>SuperAdmin full edit capabilities</li>
                                        <li>Pinned priority notices</li>
                                        <li>Auto-hides expired semester announcements</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-director" onclick="openStageDetailModal('superadmin', 5)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1">MODULE 05</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Statutory Audit Logs</h6>
                                    <p class="text-muted small mb-2">Download tamper-proof regulatory audit logs covering evaluation decisions, evaluator actions, auth events, and document uploads.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>8+ specialized CSV & PDF audit exports</li>
                                        <li>SHA-256 evidence chain verification</li>
                                        <li>IP address & user-agent tracking</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white stage-interactive-card card-director" onclick="openStageDetailModal('superadmin', 6)" role="button" tabindex="0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1">MODULE 06</span>
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;"><i class="fa-solid fa-eye me-1"></i> View Specs</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">System Governance & Security</h6>
                                    <p class="text-muted small mb-2">Calibrate AI fraud detection thresholds, configure multi-factor enforcement, and review immutable audit records.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Adjust ResNet-50 AI fraud threshold (0-100%)</li>
                                        <li>MFA system-wide enforcement toggle</li>
                                        <li>Global device revocation & security resets</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endif

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="fa-solid fa-shield-halved text-success"></i>
                    <span>Official CLSU OSA Training & Demonstration Suite &bull; R.A. 10173 Compliant</span>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-sm btn-light border px-3.5 py-1.5 rounded-pill fw-semibold" data-bs-dismiss="modal">
                        Close Guide
                    </button>
                    <button type="button" id="demoPrimaryActionBtn" class="btn btn-sm px-4 py-1.5 rounded-pill fw-bold text-white shadow-xs" 
                            style="background: var(--clsu-green, #0C4E2D);" onclick="handleDemoPrimaryAction()">
                        <i class="fa-solid fa-play me-1.5"></i> <span id="demoPrimaryActionText">Start Live Screen Tour</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ── STAGE DETAIL SPECIFICATION MODAL ── -->
<div class="modal fade" id="stageDetailModal" tabindex="-1" aria-labelledby="stageDetailModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header text-white p-3 p-md-3.5" id="stageDetailHeader" style="background: var(--clsu-green, #0C4E2D);">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-circle-nodes text-white" id="stageDetailIcon"></i>
                    </div>
                    <div>
                        <span class="badge bg-white bg-opacity-25 text-white fw-bold px-2 py-0.5 rounded-pill" id="stageDetailPill" style="font-size: 0.65rem;">STAGE 01</span>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="stageDetailTitle" style="font-size: 1.05rem;">Stage Title</h5>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4 bg-light bg-opacity-50">
                <p class="text-secondary small mb-3" id="stageDetailDescription" style="font-size: 0.85rem; line-height: 1.6;"></p>

                <!-- 2-Column Specs Layout -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-2xs rounded-3 p-3 bg-white">
                            <h6 class="fw-bold text-dark small mb-2 d-flex align-items-center gap-1.5">
                                <i class="fa-solid fa-list-check text-primary"></i> Operational Workflow
                            </h6>
                            <ul class="text-muted small ps-3 mb-0" id="stageDetailWorkflow" style="line-height: 1.65;"></ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-2xs rounded-3 p-3 bg-white">
                            <h6 class="fw-bold text-dark small mb-2 d-flex align-items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved text-success"></i> Security & Compliance
                            </h6>
                            <ul class="text-muted small ps-3 mb-0" id="stageDetailSecurity" style="line-height: 1.65;"></ul>
                        </div>
                    </div>
                </div>

                <!-- Simulation Preview Box -->
                <div class="card border-0 shadow-2xs rounded-3 p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-light text-secondary border fw-semibold" style="font-size: 0.7rem;">
                            <i class="fa-solid fa-microchip text-warning me-1"></i> Technical Mechanism Simulation
                        </span>
                        <span class="text-muted small" style="font-size: 0.7rem;" id="stageDetailAlgorithm"></span>
                    </div>
                    <div class="p-2.5 bg-light rounded-3 font-monospace small" id="stageDetailSimulation" style="font-size: 0.76rem; border: 1px dashed #cbd5e1;"></div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top p-2.5 d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="stagePrevBtn" onclick="navigateStage(-1)">
                    <i class="fa-solid fa-chevron-left me-1"></i> Previous
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" id="stageNextBtn" onclick="navigateStage(1)">
                        Next <i class="fa-solid fa-chevron-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── ROLE SIMULATOR WALKTHROUGH MODAL (CROSS-ROLE SHOWCASE) ── -->
<div class="modal fade" id="roleSimulatorModal" tabindex="-1" aria-labelledby="roleSimulatorModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header text-white p-3 p-md-3.5" id="simulatorHeader" style="background: linear-gradient(135deg, #1e3a8a, #0284c7);">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-laptop-code text-white"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title fw-bold mb-0 text-white" id="simulatorRoleTitle" style="font-size: 1.05rem;">Role Simulator</h5>
                            <span class="badge fw-bold px-2 py-0.5 rounded-pill" style="background-color: #f59e0b !important; color: #111827 !important; font-size: 0.65rem; border: 1px solid #d97706; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">Interactive Walkthrough</span>
                        </div>
                        <p class="mb-0 text-white-50 small" id="simulatorRoleSubtitle" style="font-size: 0.76rem;"></p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4 bg-light">
                
                <!-- Stepper Progress Bar -->
                <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                    <span class="fw-bold text-dark small" id="simulatorStepIndicator">Step 1 of 4</span>
                    <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1" id="simulatorStepBadge" style="font-size: 0.68rem;">MODULE DEMO</span>
                </div>

                <!-- Simulation Interactive Card -->
                <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white mb-3" id="simulatorCardContent">
                    <!-- Dynamic Slide Content Appended Here -->
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3.5" id="simPrevBtn" onclick="navigateSimulator(-1)">
                        <i class="fa-solid fa-arrow-left me-1"></i> Previous Slide
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3.5" data-bs-dismiss="modal">
                            Exit Showcase
                        </button>
                        <button type="button" class="btn btn-sm btn-success rounded-pill px-4 fw-bold text-white shadow-xs" id="simNextBtn" onclick="navigateSimulator(1)">
                            Next Slide <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    #demoRoleTabs .nav-link {
        color: #475569;
        background: #e2e8f0;
        transition: all 0.2s ease;
        white-space: nowrap;
        font-size: 0.82rem;
    }
    #demoRoleTabs .nav-link.active {
        background: var(--clsu-green, #0C4E2D) !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(12, 78, 45, 0.25);
    }
    #demoRoleTabs .nav-link#admin-demo-tab.active {
        background: #1e3a8a !important;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
    }
    #demoRoleTabs .nav-link#director-demo-tab.active {
        background: #b45309 !important;
        box-shadow: 0 4px 12px rgba(180, 83, 9, 0.25);
    }
    #demoRoleTabs::-webkit-scrollbar {
        display: none;
    }

    .stage-interactive-card {
        cursor: pointer;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease;
        border: 1.5px solid transparent !important;
    }
    .stage-interactive-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
    }
    .stage-interactive-card.card-student:hover {
        border-color: #10b981 !important;
    }
    .stage-interactive-card.card-admin:hover {
        border-color: #3b82f6 !important;
    }
    .stage-interactive-card.card-director:hover {
        border-color: #f59e0b !important;
    }

    /* ── DEDICATED DEMO ROLE BANNER STYLING (OVERRIDES GLOBAL .CARD BACKGROUND) ── */
    .demo-role-banner {
        border-radius: 18px !important;
        position: relative !important;
        overflow: hidden !important;
        box-shadow: 0 6px 24px -4px rgba(0, 0, 0, 0.18) !important;
        border: none !important;
    }
    .demo-role-banner-student {
        background: linear-gradient(135deg, #072F1B 0%, #0C4E2D 50%, #166534 100%) !important;
        color: #ffffff !important;
    }
    .demo-role-banner-admin {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0284c7 100%) !important;
        color: #ffffff !important;
    }
    .demo-role-banner-director {
        background: linear-gradient(135deg, #451a03 0%, #78350f 50%, #b45309 100%) !important;
        color: #ffffff !important;
    }
    .demo-role-banner h5 {
        color: #ffffff !important;
        font-weight: 700 !important;
    }
    .demo-role-banner p {
        color: rgba(255, 255, 255, 0.92) !important;
    }
    .demo-role-banner .badge-active-role {
        background-color: #f59e0b !important;
        color: #111827 !important;
        font-weight: 800 !important;
        font-size: 0.65rem !important;
        border: 1px solid #d97706 !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
    }
    .demo-role-banner .badge-preview-role {
        background-color: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
        font-size: 0.65rem !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
    }
    .demo-role-banner .btn-banner-dash {
        background-color: rgba(255, 255, 255, 0.18) !important;
        color: #ffffff !important;
        border: 1.5px solid rgba(255, 255, 255, 0.6) !important;
        backdrop-filter: blur(4px) !important;
        transition: all 0.2s ease !important;
    }
    .demo-role-banner .btn-banner-dash:hover {
        background-color: #ffffff !important;
        color: #0C4E2D !important;
        border-color: #ffffff !important;
    }
    .demo-role-banner .btn-banner-action {
        background-color: #f59e0b !important;
        color: #111827 !important;
        font-weight: 800 !important;
        border: 1px solid #d97706 !important;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35) !important;
    }
    .demo-role-banner .btn-banner-action:hover {
        background-color: #d97706 !important;
        color: #ffffff !important;
    }

    .stage-interactive-card h6 {
        color: #0f172a !important;
    }
    .stage-interactive-card p {
        color: #334155 !important;
    }
    .stage-interactive-card ul {
        color: #475569 !important;
    }

    @media (max-width: 575.98px) {
        #systemDemoModal .modal-dialog {
            margin: 0.35rem;
            max-width: calc(100vw - 0.7rem);
        }
        #systemDemoModal .modal-header {
            padding: 0.85rem !important;
        }
        #systemDemoModal .modal-body {
            padding: 0.75rem !important;
        }
        .shepherd-element {
            max-width: calc(100vw - 1.5rem) !important;
            margin: 0.5rem auto !important;
        }
    }
</style>

<script>
    // ── STAGE DETAIL SPECIFICATIONS DICTIONARY ──
    const STAGE_SPECS = {
        student: {
            title: "Student Applicant Portal",
            color: "#0C4E2D",
            stages: [
                {
                    stage: 1,
                    pill: "STAGE 01",
                    title: "Institutional Registration & Identity Verification",
                    description: "Students register using their verified CLSU email address (@clsu.edu.ph). This prevents identity spoofing, duplicate profile registration, and ensures compliance with the National Privacy Commission (NPC) requirements under Republic Act No. 10173.",
                    workflow: [
                        "Enter official @clsu.edu.ph student address",
                        "Accept Data Privacy Act (R.A. 10173) explicit consent",
                        "Verify via 6-digit email OTP security token",
                        "Optionally store 30-day cryptographic trusted device token"
                    ],
                    security: [
                        "SHA-256 device fingerprinting & UA binding",
                        "Email OTP hashed via Bcrypt prior to storage",
                        "Automated rate limiting (5 attempts/min) to prevent brute force",
                        "NPC Seal of Registration compliance certificate"
                    ],
                    algorithm: "RFC 6238 HMAC-Based OTP Verification",
                    simulation: `[AUTH_PIPELINE] Input: juan.delacruz@clsu.edu.ph\n[NPC_CHECK] R.A. 10173 Consent Timestamp: ${new Date().toISOString()}\n[OTP_DISPATCH] 6-digit code sent via SMTP -> Hashed Bcrypt stored in cache\n[DEVICE_TOKEN] Fingerprint: SHA-256(UA + IP_SALT) -> 30-day bypass granted.`
                },
                {
                    stage: 2,
                    pill: "STAGE 02",
                    title: "Academic Profile Onboarding & Verification",
                    description: "Captures essential institutional student details including formal 7-character CLSU ID Number (00-0000), designated College, Course Program, and Parent/Guardian emergency contacts.",
                    workflow: [
                        "Input validated CLSU ID Number (format regex: ^\\d{2}-\\d{4}$)",
                        "Select enrolled College and Undergraduate Degree Program",
                        "Encode current Year Level and Parent/Guardian information",
                        "Automatic redirect protection ensures complete profile before application"
                    ],
                    security: [
                        "Format regex pattern enforcement prevents SQL injection",
                        "Student ID uniquely indexed across the active database",
                        "AES-256 column-level encryption on emergency contacts",
                        "One-time onboarding locks unauthorized multiple identities"
                    ],
                    algorithm: "Regex Validator: /^\\d{2}-\\d{4}$/",
                    simulation: `[PROFILE_GUARD] User: Juan Dela Cruz (ID: #104)\n[VALIDATION] CLSU ID: "23-0184" -> MATCH (Valid Format)\n[COLLEGE_MAP] College of Science -> BS Information Technology\n[ENCRYPT_PII] Guardian & emergency phone serialized with AES-256-CBC.`
                },
                {
                    stage: 3,
                    pill: "STAGE 03",
                    title: "Scholarship Exploration & Dynamic Criteria Matching",
                    description: "Students explore available institutional (University/College Scholar), government (CHED, DOST), and private foundation grants with completely transparent minimum GWA requirements and stipend amounts.",
                    workflow: [
                        "Browse live scholarship catalog with active application deadlines",
                        "View stipend allowances (e.g. ₱5,000/sem) and slots available",
                        "Check grade eligibility requirements (e.g. GWA ≤ 1.75)",
                        "Enforces single-active-application rule to guarantee fair allocation"
                    ],
                    security: [
                        "Database transaction locks slot capacity race conditions",
                        "Active academic semester validation rejects expired cycles",
                        "Single-active-grant constraint enforced via DB unique composite key",
                        "Immutable scholarship audit trail on catalog modifications"
                    ],
                    algorithm: "Eligibility Gate: Candidate GWA <= Grant Max Cutoff",
                    simulation: `[CATALOG_FILTER] Grant: "University Academic Scholar"\n[CRITERIA] Max GWA: 1.75 | Slots: 50 Remaining\n[ELIGIBILITY_CHECK] Student GWA: 1.45 <= 1.75 -> ELIGIBLE\n[SINGLE_GRANT_LOCK] Active applications count: 0 -> Proceed enabled.`
                },
                {
                    stage: 4,
                    pill: "STAGE 04",
                    title: "Application Submission & Document Upload",
                    description: "Dynamic step-by-step application form with local auto-save draft resilience. Accepts official Certificate of Grades (COG) uploads in PDF, PNG, or JPG formats with automatic virus and MIME-type verification.",
                    workflow: [
                        "Auto-saves draft input values in browser localStorage",
                        "Fill out dynamic custom fields tailored to specific scholarship",
                        "Upload Certificate of Grades (max 10MB, PDF/JPG/PNG)",
                        "After-work-hours operational queue notice (CLSU OSA: 8AM-5PM PHT)"
                    ],
                    security: [
                        "Magic byte MIME verification prevents extension spoofing",
                        "SHA-256 hash computed on upload for evidence integrity",
                        "Secure non-public storage directory with private stream controller",
                        "Local auto-save prevents data loss during unstable connectivity"
                    ],
                    algorithm: "SHA-256 File Checksum & MIME Validation",
                    simulation: `[UPLOAD_HANDLER] File: "COG_1stSem_2025.pdf" (1.8 MB)\n[MIME_CHECK] Detected: application/pdf (Magic Bytes: %PDF-1.4)\n[SHA256_HASH] d41d8cd98f00b204e9800998ecf8427e...\n[TIME_GATE] Local Time: 04:50 PHT -> Tagged: "Queued (After OSA Hours)".`
                },
                {
                    stage: 5,
                    pill: "STAGE 05",
                    title: "ResNet-50 AI Grade Integrity & Fraud Verification",
                    description: "Uploaded Certificate of Grades passes through our Explainable Forensic Decision Framework (EFDF), combining Error Level Analysis (ELA) pixel tampering detection, OCR text extraction, and EXIF sensor validation.",
                    workflow: [
                        "Asynchronous background worker dispatches document to AI microservice",
                        "ResNet-50 neural network inspects high-frequency compression artifacts",
                        "OCR engine extracts student name and GWA directly from registrar seal",
                        "Cross-verifies extracted GWA against student self-reported grade"
                    ],
                    security: [
                        "Tiered fraud scoring: Authentic (<35%), Caution (35-70%), High-Risk (>70%)",
                        "EXIF sensor continuity check detects Photoshop / Photopea manipulation",
                        "Automatic tolerance discrepancy gate prevents accidental approvals",
                        "Explainable Grad-CAM heatmaps prevent opaque black-box AI bias"
                    ],
                    algorithm: "Fusion Scoring: ELA(25%) + OCR(35%) + Sensor(25%) + Meta(15%)",
                    simulation: `[AI_FORENSICS] Job ID: #9842 -> ResNet-50 ELA Model\n[ELA_SCORE] Compression Variance: 12.4% (Authentic Camera Pixel Noise)\n[OCR_PARSER] Detected GWA: 1.45 | Self-Encoded: 1.45 -> Discrepancy: 0.00\n[SENSOR_CHECK] Camera: Samsung Galaxy S23 (Exif intact) -> Integrity Score: 94.2% (Low Risk).`
                },
                {
                    stage: 6,
                    pill: "STAGE 06",
                    title: "Clearance, Award Notice & Official Signed PDF",
                    description: "Approved scholars receive an official award confirmation email with their formal, dynamic Application & Evaluation Form PDF featuring authentic CLSU and OSA institutional seals and live approval timestamps.",
                    workflow: [
                        "Real-time dashboard status badge updates to 'Approved Scholar'",
                        "Receive automated award notice email with stipend release dates",
                        "Download official 1-page signed application form PDF in 1 click",
                        "Grantee cleared for semester scholarship stipend disbursement"
                    ],
                    security: [
                        "Dynamic DomPDF rendering with zero-network inline Base64 seals",
                        "Evaluator and Director digital signatures with immutable timestamps",
                        "Unique control code: APP-00012 with verifiable student ID reference",
                        "Tamper-proof clearance record preserved in statutory compliance archive"
                    ],
                    algorithm: "DomPDF Dynamic Layout Engine + Inline Base64 Seals",
                    simulation: `[AWARD_ENGINE] Status: "Approved" by Evaluator: Admin Santos\n[PDF_GENERATOR] Program: "UNIVERSITY ACADEMIC SCHOLAR APPLICATION FORM"\n[EMBED_SEALS] CLSU Seal (Base64) + OSA Seal (Base64) rendered\n[DISPATCH] Clearance email sent -> Application Form ready for download.`
                }
            ]
        },
        admin: {
            title: "OSA Staff Evaluator Portal",
            color: "#1e3a8a",
            stages: [
                {
                    stage: 1,
                    pill: "STEP 01",
                    title: "Live Review Queue & Turn-Around SLA Tracking",
                    description: "Centralized review queue where OSA staff triage incoming scholarship applications filtered by submission date, priority, college, GWA, and after-hours queue status.",
                    workflow: [
                        "Triage applications in real-time with SLA countdown timers",
                        "Filter by College (e.g. College of Science, Agriculture, Engineering)",
                        "Sort by GWA merit ranking or AI fraud risk score",
                        "Batch selection capabilities for high-volume evaluation"
                    ],
                    security: [
                        "Role-based authorization prevents student access to review queues",
                        "Staff action logging records every access and queue update",
                        "Assigned coordinator indicators prevent dual-evaluator race conflicts",
                        "Read-only protection prevents accidental applicant record modification"
                    ],
                    algorithm: "Queue Priority Ordering: SLA_Due ASC, GWA ASC",
                    simulation: `[QUEUE_TRIAGE] Total In Queue: 42 Applications\n[FILTER] College: College of Science | Status: "Pending Review"\n[SLA_MONITOR] Turn-around Target: 48h (Elapsed: 4h 12m)\n[COORDINATOR] Assigned Evaluator: Staff Cruz -> Locked for Review.`
                },
                {
                    stage: 2,
                    pill: "STEP 02",
                    title: "Forensic Dual-Pane Inspection & ELA Heatmaps",
                    description: "High-resolution side-by-side inspection viewer displaying the original student transcript alongside the neural Error Level Analysis (ELA) heatmap to pinpoint forged digits or copied seals.",
                    workflow: [
                        "Pan and zoom up to 400% on Certificate of Grades documents",
                        "Toggle ELA noise heatmap overlay to inspect pixel tampering",
                        "Download sample test COGs via 'Test COG Fixtures' toolbar dropdown",
                        "Review camera metadata: device model, original timestamp, and software",
                        "Check 3-tier risk badge (Low Risk, Review Recommended, High Tampering)"
                    ],
                    security: [
                        "Private document stream controller prevents direct public file access",
                        "Cache-Control: private, no-store headers protect student PII",
                        "SHA-256 evidence chain verification guarantees unmanipulated evidence",
                        "Audit log records when a staff member views student transcripts"
                    ],
                    algorithm: "ResNet-50 Pixel Noise Compression Variance Gate",
                    simulation: `[DUAL_PANE] Loading COG Document ID #104...\n[HEATMAP_VIEW] ELA Layer: Uniform noise frequency detected\n[TAMPER_RATING] Fraud Probability: 12% (Grade lines authentic)\n[EXIF_PROBE] Software: "Android 14 Gallery Crop" (Benign tool, +10% penalty only).`
                },
                {
                    stage: 3,
                    pill: "STEP 03",
                    title: "OCR Discrepancy Gate & Transcript Cross-Check",
                    description: "Automated discrepancy gate that compares the student's self-encoded GWA with the raw numerical grades extracted by the optical character recognition (OCR) engine.",
                    workflow: [
                        "System highlights self-reported GWA (e.g. 1.45) vs OCR extracted GWA",
                        "Automatic variance calculation flags differences exceeding 0.05",
                        "Staff evaluator can manually re-examine flagged transcript line items",
                        "Eliminates human mathematical error and intentional grade inflation"
                    ],
                    security: [
                        "Immutable OCR log stored alongside application record",
                        "Prevents approval when variance exceeds strict tolerance without justification",
                        "Evaluator override requires mandatory recorded rationale in audit trail",
                        "Zero manual spreadsheet calculations required"
                    ],
                    algorithm: "Variance = |Encoded_GWA - OCR_GWA|; Flag if > 0.05",
                    simulation: `[OCR_GATE] Encoded GWA: 1.45\n[OCR_EXTRACT] Extracted Registrar Rows: 6 Subjects -> Weighted Avg: 1.45\n[VARIANCE] |1.45 - 1.45| = 0.00 (Delta <= 0.05)\n[GATE_STATUS] PASSED: Discrepancy gate cleared with zero errors.`
                },
                {
                    stage: 4,
                    pill: "STEP 04",
                    title: "1-Click Fast Remarks & Evaluator Collaboration",
                    description: "Accelerate evaluation turnaround times with curated preset remarks for common outcomes: complete requirements, blurriness, or qualification confirmation.",
                    workflow: [
                        "Select 1-click preset remarks (e.g. 'GWA verified with registrar seal')",
                        "Add private internal remarks visible only to OSA evaluation staff",
                        "Request one-click re-upload for blurry or cropped documents",
                        "Real-time timestamp and evaluator signature appended automatically"
                    ],
                    security: [
                        "XSS sanitization on all remarks and evaluator notes",
                        "Internal notes cryptographically separated from public student remarks",
                        "All remark edits preserved in immutable action history",
                        "Evaluator ID permanently linked to evaluation determination"
                    ],
                    algorithm: "Pre-Configured Institutional Remark Macro Presets",
                    simulation: `[PRESET_MACRO] Selected: "CLEAR_QUALIFIED"\n[SYSTEM_REMARK] "Document authentic. GWA 1.45 exceeds program minimum 1.75."\n[INTERNAL_NOTE] "Cross-referenced with 2nd year class list (CS Dept)."\n[AUDIT] Action committed by: staff@clsu.edu.ph.`
                },
                {
                    stage: 5,
                    pill: "STEP 05",
                    title: "Evaluation Determination & Revocation Safeguards",
                    description: "Official determination suite to Approve, Reject, or Revoke grant applications with mandatory justification logging and accidental deletion recovery safeguards.",
                    workflow: [
                        "Execute definitive action: Approve, Reject, or Revoke",
                        "System triggers automated email and in-portal notification to student",
                        "Auto-tags student for upcoming semester renewal cycles",
                        "Soft-delete trash recovery safeguard protects against accidental removal"
                    ],
                    security: [
                        "Mandatory rejection/revocation reason capture required for compliance",
                        "Atomic database transactions prevent partial status updates",
                        "Trash recovery with 30-day statutory retention window",
                        "Immediate notification dispatch via queue workers"
                    ],
                    algorithm: "State Machine: Pending -> Reviewing -> Approved/Rejected",
                    simulation: `[DECISION_GATE] Status Change: "Under Review" -> "Approved"\n[DB_COMMIT] Award allocated. Available slots: 49 / 50\n[EVENT_DISPATCH] ApplicationStatusNotification queued for Juan Dela Cruz\n[AUDIT_LOG] Event: "application.approved" committed with SHA-256 seal.`
                },
                {
                    stage: 6,
                    pill: "STEP 06",
                    title: "1-Page Forensic Audit PDF Certificate Generation",
                    description: "Generate official institutional 1-page forensic audit certificates complete with QR code verification, sensor breakdown, and evaluator signature for legal records.",
                    workflow: [
                        "Export signed 1-page Forensic Audit PDF directly from review suite",
                        "Includes complete evidence chain of custody and SHA-256 hash",
                        "QR code enables instant third-party authenticity verification",
                        "Downloadable batch CSV reports for CLSU board and auditor general"
                    ],
                    security: [
                        "Cryptographic SHA-256 evidence integrity verification",
                        "QR code points to tamper-proof system verification endpoint",
                        "Zero-network inline Base64 seals guarantee 100% rendering reliability",
                        "Permanent statutory audit record compliant with Philippine legal standards"
                    ],
                    algorithm: "DomPDF Stream + QR Verification Hash Generator",
                    simulation: `[CERT_GENERATOR] Target: App #104 (Juan Dela Cruz)\n[CHAIN_OF_CUSTODY] File SHA-256: d41d8cd98f00b204e9800998ecf8427e\n[QR_CODE] URL: https://clsu.osa.scholarship/verify/APP-00104\n[RENDER] Output: 1-Page Official Forensic Inspection Certificate (PDF).`
                }
            ]
        },
        superadmin: {
            title: "OSA Director & Super Admin Portal",
            color: "#78350f",
            stages: [
                {
                    stage: 1,
                    pill: "MODULE 01",
                    title: "Executive Analytics & Institutional KPIs",
                    description: "Real-time executive oversight tracking university-wide scholarship metrics, total financial aid awarded, college demographic distribution, and GWA performance curves.",
                    workflow: [
                        "Monitor total active scholars, pending reviews, and approved funds",
                        "Analyze demographic breakdown across all 8 CLSU colleges",
                        "Track application conversion funnels and review turnaround times",
                        "Filter metrics by active academic year and semester"
                    ],
                    security: [
                        "Aggregated database queries prevent leaking individual student PII",
                        "Strict SuperAdmin-only middleware gate (`role:superadmin`)",
                        "Real-time SQL query caching prevents database saturation",
                        "Executive dashboard logging for statutory oversight"
                    ],
                    algorithm: "Real-Time Aggregations: Sum, GroupBy, and Funnel Rates",
                    simulation: `[ANALYTICS_QUERY] Term: 1st Sem 2025-2026\n[METRICS] Active Scholars: 342 | Total Stipends: ₱1,710,000.00\n[DEMOGRAPHICS] Leading: College of Science (28%), Agriculture (24%)\n[SLA_AVERAGE] Median Review Time: 18.4 hours (Goal: < 48h).`
                },
                {
                    stage: 2,
                    pill: "MODULE 02",
                    title: "User Governance, MFA & Device Management",
                    description: "Comprehensive administration of all student and evaluator accounts: instant status toggling, evaluator staff onboarding, 1-click MFA reset, and device revocation.",
                    workflow: [
                        "Instant account deactivation/reactivation with 1 click",
                        "Invite new OSA staff evaluators via secure email activation tokens",
                        "1-click MFA reset for students who lost mobile authenticators",
                        "Global device revocation terminates active sessions on compromised devices"
                    ],
                    security: [
                        "Session encryption enabled (`SESSION_ENCRYPT=true`) with AES-256",
                        "Dual-key rate limiting (IP + Email) stops credential stuffing",
                        "Session termination on password updates (`logoutOtherDevices`)",
                        "Immutable user management audit logs with IP & User-Agent capture"
                    ],
                    algorithm: "RBAC + Dual-Key Rate Limiting + Session Revocation",
                    simulation: `[USER_GOVERNANCE] SuperAdmin action initiated\n[MFA_OVERRIDE] Reset requested for user #84 (Student)\n[TOKEN_WIPE] Invalidated 2 UserMfaDevice records & cleared active session\n[AUDIT] Action logged: "user.mfa_reset" by Director Santos.`
                },
                {
                    stage: 3,
                    pill: "MODULE 03",
                    title: "Scholarship Program & Custom Field Builder",
                    description: "Configure institutional, government, and private scholarship programs: define minimum GWA cutoffs, slot quotas, renewal rules, and dynamic application fields.",
                    workflow: [
                        "Create and configure grant programs with custom criteria",
                        "Build dynamic application form fields (text, numbers, files, dropdowns)",
                        "Set slot capacity limits and strict semester renewal boundaries",
                        "Activate or archive programs without deleting historic records"
                    ],
                    security: [
                        "Custom field inputs encrypted at rest with AES-256",
                        "Foreign key constraints protect historical student applications",
                        "Program archival retains immutable audit history for accreditation",
                        "Input validation prevents malformed custom field schemas"
                    ],
                    algorithm: "JSON Dynamic Field Schema Engine + DB Constraint Locking",
                    simulation: `[PROGRAM_BUILDER] New Grant: "DOST-SEI Merit Scholarship"\n[PARAMS] Max GWA: 1.50 | Slots: 25 | Stipend: ₱8,000/mo\n[CUSTOM_FIELDS] Appended: [Text: "DOST Exam Score", File: "Endorsement Letter"]\n[SCHEMA_COMMIT] Dynamic schema committed to 'scholarship_custom_fields'.`
                },
                {
                    stage: 4,
                    pill: "MODULE 04",
                    title: "Announcement Board & Campus Broadcasts",
                    description: "Publish, update, and schedule urgent university-wide scholarship announcements, deadline reminders, and requirements guides with priority pinning.",
                    workflow: [
                        "Publish rich-text announcements visible across Student and Staff dashboards",
                        "Pin critical deadline alerts to the top of the announcement feed",
                        "Schedule automatic expiration to auto-hide obsolete semester notices",
                        "Automated real-time notifications dispatched to active scholars"
                    ],
                    security: [
                        "HTMLPurifier sanitization eliminates Stored XSS vectors",
                        "Automated expiration cleaner removes stale broadcasts from active view",
                        "Broadcast dispatch rate limited to prevent notification spam",
                        "Full edit and audit history logged for all administrative broadcasts"
                    ],
                    algorithm: "HTMLPurifier XSS Sanitizer + Notification Queue Pipeline",
                    simulation: `[ANNOUNCEMENT_DISPATCH] Title: "Midterm Scholarship Applications Open"\n[SANITIZER] HTMLPurifier: 0 unsafe tags detected -> Cleaned\n[BROADCAST] Target: All Verified Students (Count: 1,240)\n[STATUS] Pinned: TRUE | Expiry: 2026-10-15 (Auto-hides on expiry).`
                },
                {
                    stage: 5,
                    pill: "MODULE 05",
                    title: "Statutory Regulatory Audit Logs & Exports",
                    description: "Download immutable regulatory audit trails covering evaluation decisions, evaluator remarks, auth events, and document upload history in standard CSV and PDF formats.",
                    workflow: [
                        "Generate 8+ specialized CSV & PDF compliance audit logs",
                        "Inspect evidence chains verified by SHA-256 checksums",
                        "Audit evaluator response times and decision justification logs",
                        "Export data formatted specifically for CHED & institutional accreditors"
                    ],
                    security: [
                        "Append-only audit table prevents modification or deletion of records",
                        "SHA-256 evidence chain verification prevents forensic tampering",
                        "IP addresses and full User-Agent headers recorded for every action",
                        "Streamed CSV download prevents memory exhaustion on large datasets"
                    ],
                    algorithm: "SHA-256 Cryptographic Evidence Ledger + Streamed Export",
                    simulation: `[AUDIT_EXPORT] Type: "Full Statutory Compliance Ledger"\n[RECORDS] Filtered: 1,420 events across active semester\n[HASH_CHAIN] Ledger Genesis: #0001 -> Current: #1420 (Zero Tampering Detected)\n[STREAM_DISPATCH] Download: "AEGIS_STATUTORY_AUDIT_2025_SEM1.csv".`
                },
                {
                    stage: 6,
                    pill: "MODULE 06",
                    title: "System Governance & AI Model Calibration",
                    description: "Calibrate the ResNet-50 AI fraud detection sensitivity threshold (0-100%), manage system-wide MFA enforcement policies, and monitor microservice health.",
                    workflow: [
                        "Calibrate ResNet-50 AI fraud tolerance threshold (default: 70.0%)",
                        "Toggle university-wide Multi-Factor Authentication (MFA) enforcement",
                        "Trigger live auto-wake probes to Hugging Face / Render AI microservice",
                        "Run automated `aegis:security-audit` CLI tool directly from portal"
                    ],
                    security: [
                        "Settings updates restricted to superadmin with re-authentication guard",
                        "AI health endpoint shielded by token authorization",
                        "Automated 9-checkpoint security audit scorecard generation",
                        "Immediate logging of all security and threshold calibration changes"
                    ],
                    algorithm: "Threshold Tuner: Fraud_Flag = (Risk_Score >= Setting::get('ai_threshold'))",
                    simulation: `[SYSTEM_GOVERNANCE] Setting: "ai_fraud_threshold"\n[CALIBRATION] Value changed: 50.0% -> 70.0% (Accommodate mobile camera noise)\n[MFA_POLICY] Status: "Strict Enforced (All Staff & Students)"\n[HEALTH_CHECK] AI Microservice: Responsive (HTTP 200, 240ms latency).`
                }
            ]
        }
    };

    // ── SIMULATOR WALKTHROUGH SLIDES ──
    const SIMULATOR_SLIDES = {
        student: [
            {
                badge: "STAGE 01 OF 04",
                title: "1. Institutional Registration & Consent",
                desc: "Student registers with @clsu.edu.ph institutional email, accepts Data Privacy consent, and receives a 6-digit OTP code.",
                visual: `<div class="p-3 bg-light rounded-3 border"><div class="d-flex align-items-center justify-content-between mb-2"><span class="badge bg-success">Step 1: Institutional Auth</span><span class="text-muted small">R.A. 10173 Verified</span></div><div class="input-group mb-2"><span class="input-group-text"><i class="fa-solid fa-envelope"></i></span><input type="text" class="form-control" value="juan.delacruz@clsu.edu.ph" readonly></div><div class="alert alert-success py-1.5 px-2.5 small mb-0"><i class="fa-solid fa-check me-1"></i> OTP Code: <strong>819-204</strong> (Verified in 12s)</div></div>`
            },
            {
                badge: "STAGE 02 OF 04",
                title: "2. Exploring Grants & Dynamic Form",
                desc: "Browse live scholarships with GWA cutoffs and fill out custom questions with local auto-save draft resilience.",
                visual: `<div class="p-3 bg-light rounded-3 border"><div class="card p-2.5 mb-2 border-success border-2 bg-success-subtle bg-opacity-10"><div class="d-flex justify-content-between"><h6 class="fw-bold mb-0 text-success">University Scholar</h6><span class="badge bg-success">GWA &le; 1.75</span></div><small class="text-muted">₱5,000 / semester stipend &bull; 50 slots</small></div><div class="small text-muted"><i class="fa-solid fa-cloud-arrow-up text-primary me-1"></i> Draft auto-saved locally at 04:52 PHT</div></div>`
            },
            {
                badge: "STAGE 03 OF 04",
                title: "3. Certificate of Grades AI Scan",
                desc: "Upload official transcript image or PDF. The ResNet-50 neural network scans for pixel noise anomalies and checks GWA discrepancies.",
                visual: `<div class="p-3 bg-light rounded-3 border"><div class="d-flex align-items-center justify-content-between mb-2"><span class="fw-bold small text-dark"><i class="fa-solid fa-file-pdf text-danger me-1"></i> COG_1stSem.pdf</span><span class="badge bg-success-subtle text-success">Low Risk (12%)</span></div><div class="progress mb-2" style="height: 6px;"><div class="progress-bar bg-success" style="width: 12%;"></div></div><small class="text-muted d-block">OCR Extracted GWA: <strong>1.45</strong> &bull; Self-Reported: <strong>1.45</strong> &bull; Discrepancy: <strong>0.00</strong></small></div>`
            },
            {
                badge: "STAGE 04 OF 04",
                title: "4. Clearance & Approved Application PDF",
                desc: "Upon evaluation approval, download your official dynamic Application Form with authentic CLSU and OSA institutional seals.",
                visual: `<div class="p-3 bg-light rounded-3 border text-center"><div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle mb-2" style="width: 42px; height: 42px;"><i class="fa-solid fa-certificate fs-5"></i></div><h6 class="fw-bold text-success mb-1">Scholarship Grant Approved!</h6><p class="small text-muted mb-2">Reference: APP-00104 &bull; Official Signed PDF Clearance Ready</p><button class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-semibold"><i class="fa-solid fa-download me-1"></i> Download Approved Form (PDF)</button></div>`
            }
        ],
        admin: [
            {
                badge: "STEP 01 OF 04",
                title: "1. Staff Review Queue & SLA Turnaround",
                desc: "Manage incoming student applications filtered by SLA due date, college, GWA merit, and after-hours queue status.",
                visual: `<div class="p-3 bg-light rounded-3 border"><div class="table-responsive"><table class="table table-sm table-bordered bg-white mb-0 small"><thead><tr class="table-light"><th>ID</th><th>Applicant</th><th>College</th><th>GWA</th><th>Action</th></tr></thead><tbody><tr><td>#104</td><td>Juan Dela Cruz</td><td>COS</td><td><span class="badge bg-success">1.45</span></td><td><span class="badge bg-primary">Review</span></td></tr><tr><td>#105</td><td>Maria Santos</td><td>CBAA</td><td><span class="badge bg-success">1.62</span></td><td><span class="badge bg-primary">Review</span></td></tr></tbody></table></div></div>`
            },
            {
                badge: "STEP 02 OF 04",
                title: "2. Dual-Pane Forensic Viewer & ELA Heatmap",
                desc: "Inspect student documents side-by-side with ResNet-50 Error Level Analysis (ELA) and Grad-CAM heatmaps to detect photo manipulation.",
                visual: `<div class="p-3 bg-light rounded-3 border"><div class="row g-2 text-center small"><div class="col-6"><div class="p-2 bg-white border rounded"><strong>Original COG</strong><div class="py-3 text-muted"><i class="fa-solid fa-file-invoice fs-3 text-secondary"></i><br>CLSU Registrar Seal</div></div></div><div class="col-6"><div class="p-2 bg-dark text-white rounded"><strong>ELA Heatmap</strong><div class="py-3 text-info"><i class="fa-solid fa-microchip fs-3"></i><br>Uniform Noise (Authentic)</div></div></div></div></div>`
            },
            {
                badge: "STEP 03 OF 04",
                title: "3. 1-Click Fast Remarks & Determinations",
                desc: "Select curated preset remarks to expedite determinations: Complete, Missing Requirement, or Tampering Suspected.",
                visual: `<div class="p-3 bg-light rounded-3 border"><div class="d-flex gap-1.5 flex-wrap mb-2"><span class="badge bg-primary px-2.5 py-1">GWA Verified</span><span class="badge bg-secondary px-2.5 py-1">Seal Legible</span><span class="badge bg-warning text-dark px-2.5 py-1">Request Re-Upload</span></div><div class="p-2 bg-white rounded border small text-muted">Remark: "GWA 1.45 verified with registrar seal. Approved for University Scholar."</div></div>`
            },
            {
                badge: "STEP 04 OF 04",
                title: "4. Official 1-Page Forensic Audit Certificate",
                desc: "Export signed legal inspection certificates featuring QR verification codes, sensor breakdowns, and evaluator timestamps.",
                visual: `<div class="p-3 bg-light rounded-3 border text-center"><div class="p-2.5 bg-white border rounded shadow-2xs d-inline-block text-start" style="max-width: 320px;"><div class="d-flex align-items-center justify-content-between border-bottom pb-1 mb-1"><strong class="small text-dark">CLSU OSA FORENSIC AUDIT</strong><span class="badge bg-success" style="font-size: 0.6rem;">VERIFIED</span></div><div class="text-muted" style="font-size: 0.68rem;">Hash: d41d8cd98f00b204e9800998ecf8427e<br>QR: aegis.clsu.edu.ph/verify/APP-00104<br>Signed: OSA Evaluator Cruz</div></div></div>`
            }
        ],
        superadmin: [
            {
                badge: "MODULE 01 OF 04",
                title: "1. Executive Analytics & University Demographics",
                desc: "Track university-wide KPIs, active grant distributions, GWA averages, and college demographic funnels in real-time.",
                visual: `<div class="p-3 bg-light rounded-3 border"><div class="row g-2 text-center small mb-2"><div class="col-4"><div class="p-2 bg-white border rounded"><strong class="text-success fs-6">342</strong><br><span class="text-muted" style="font-size: 0.65rem;">Active Scholars</span></div></div><div class="col-4"><div class="p-2 bg-white border rounded"><strong class="text-primary fs-6">₱1.71M</strong><br><span class="text-muted" style="font-size: 0.65rem;">Total Grants</span></div></div><div class="col-4"><div class="p-2 bg-white border rounded"><strong class="text-warning fs-6">18.4h</strong><br><span class="text-muted" style="font-size: 0.65rem;">Avg SLA Time</span></div></div></div></div>`
            },
            {
                badge: "MODULE 02 OF 04",
                title: "2. User Governance, 1-Click MFA & Device Control",
                desc: "Manage student and evaluator accounts: activate/deactivate, reset MFA tokens with 1 click, and terminate sessions on compromised devices.",
                visual: `<div class="p-3 bg-light rounded-3 border"><div class="d-flex align-items-center justify-content-between p-2 bg-white border rounded mb-2 small"><div><strong>Admin Staff Account</strong><br><span class="text-muted" style="font-size: 0.68rem;">staff.cruz@clsu.edu.ph</span></div><div class="d-flex gap-1"><span class="badge bg-warning text-dark"><i class="fa-solid fa-key me-1"></i> Reset MFA</span><span class="badge bg-success">Active</span></div></div></div>`
            },
            {
                badge: "MODULE 03 OF 04",
                title: "3. Dynamic Scholarship Custom Field Builder",
                desc: "Configure grant eligibility cutoffs, slots limits, and dynamically build tailored application questions and document requirements.",
                visual: `<div class="p-3 bg-light rounded-3 border"><div class="p-2 bg-white border rounded small"><div class="d-flex justify-content-between mb-1"><strong>Program: DOST-SEI Merit</strong><span class="badge bg-primary">Dynamic Fields</span></div><div class="text-muted" style="font-size: 0.7rem;">+ Field: "DOST Exam Score" (Numeric, Required)<br>+ Field: "Endorsement Document" (PDF, Max 5MB)</div></div></div>`
            },
            {
                badge: "MODULE 04 OF 04",
                title: "4. AI Fraud Calibration & Statutory Audit Exports",
                desc: "Adjust the ResNet-50 fraud threshold slider (0-100%) and export 8+ specialized statutory CSV & PDF audit reports.",
                visual: `<div class="p-3 bg-light rounded-3 border"><div class="mb-2"><label class="small fw-bold text-dark d-flex justify-content-between"><span>ResNet-50 Fraud Threshold:</span><span class="text-success">70.0% (Calibrated)</span></label><div class="progress" style="height: 6px;"><div class="progress-bar bg-success" style="width: 70%;"></div></div></div><div class="d-flex justify-content-end"><button class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5" style="font-size: 0.7rem;"><i class="fa-solid fa-file-csv me-1"></i> Export Statutory CSV</button></div></div>`
            }
        ]
    };

    // ── STATE VARIABLES ──
    let currentRole = '{{ $userRole }}';
    let currentStageIndex = 1;
    let currentSimRole = 'student';
    let currentSimIndex = 0;

    document.addEventListener('DOMContentLoaded', () => {
        // Tab switch handler to update primary action button dynamically
        const tabs = document.querySelectorAll('#demoRoleTabs button[data-bs-toggle="pill"]');
        tabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', (e) => {
                const targetRole = e.target?.getAttribute('data-role') || 'student';
                updateDemoPrimaryAction(targetRole);
            });
        });

        // Initialize primary action button state
        updateDemoPrimaryAction(currentRole);
    });

    function updateDemoPrimaryAction(viewingRole) {
        const btn = document.getElementById('demoPrimaryActionBtn');
        const text = document.getElementById('demoPrimaryActionText');
        if (!btn || !text) return;

        const authRole = '{{ $userRole }}';

        if (viewingRole === authRole) {
            btn.style.background = 'var(--clsu-green, #0C4E2D)';
            btn.className = 'btn btn-sm px-4 py-1.5 rounded-pill fw-bold text-white shadow-xs';
            text.innerHTML = 'Start Live Screen Tour';
            btn.setAttribute('data-action', 'live-tour');
            btn.setAttribute('data-role', viewingRole);
        } else {
            let roleColor = viewingRole === 'admin' ? '#1e3a8a' : (viewingRole === 'superadmin' ? '#b45309' : '#0C4E2D');
            let roleLabel = viewingRole === 'admin' ? 'Staff Evaluator' : (viewingRole === 'superadmin' ? 'Director / SuperAdmin' : 'Student');
            btn.style.background = roleColor;
            btn.className = 'btn btn-sm px-4 py-1.5 rounded-pill fw-bold text-white shadow-xs';
            text.innerHTML = `Launch ${roleLabel} Simulator`;
            btn.setAttribute('data-action', 'simulate');
            btn.setAttribute('data-role', viewingRole);
        }
    }

    window.handleDemoPrimaryAction = function() {
        const btn = document.getElementById('demoPrimaryActionBtn');
        if (!btn) return;

        const action = btn?.getAttribute('data-action') || 'live-tour';
        const role = btn?.getAttribute('data-role') || 'student';

        if (action === 'live-tour') {
            startCurrentRoleTour();
        } else {
            openRoleSimulator(role);
        }
    };

    // ── STAGE DEEP DIVE MODAL HANDLER ──
    window.openStageDetailModal = function(role, stageNum) {
        const isStudent = {{ $isStudent ? 'true' : 'false' }};
        if (isStudent && role !== 'student') return; // Security boundary: students cannot inspect staff/director specs

        const specs = STAGE_SPECS[role];
        if (!specs || !specs.stages) return;

        const stageData = specs.stages.find(s => s.stage === stageNum) || specs.stages[0];
        currentRole = role;
        currentStageIndex = stageData.stage;

        document.getElementById('stageDetailHeader').style.background = specs.color;
        document.getElementById('stageDetailPill').innerText = stageData.pill;
        document.getElementById('stageDetailTitle').innerText = stageData.title;
        document.getElementById('stageDetailDescription').innerText = stageData.description;
        document.getElementById('stageDetailAlgorithm').innerText = stageData.algorithm;
        document.getElementById('stageDetailSimulation').innerText = stageData.simulation;

        // Workflow
        const wfList = document.getElementById('stageDetailWorkflow');
        wfList.innerHTML = '';
        stageData.workflow.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = item;
            wfList.appendChild(li);
        });

        // Security
        const secList = document.getElementById('stageDetailSecurity');
        secList.innerHTML = '';
        stageData.security.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = item;
            secList.appendChild(li);
        });

        // Prev/Next buttons
        document.getElementById('stagePrevBtn').disabled = (currentStageIndex <= 1);
        document.getElementById('stageNextBtn').disabled = (currentStageIndex >= specs.stages.length);

        const modalEl = document.getElementById('stageDetailModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    };

    window.navigateStage = function(direction) {
        const nextIndex = currentStageIndex + direction;
        const specs = STAGE_SPECS[currentRole];
        if (specs && specs.stages.some(s => s.stage === nextIndex)) {
            openStageDetailModal(currentRole, nextIndex);
        }
    };

    // ── ROLE SIMULATOR HANDLER (CROSS-ROLE SHOWCASE) ──
    window.openRoleSimulator = function(role) {
        const isStudent = {{ $isStudent ? 'true' : 'false' }};
        if (isStudent && role !== 'student') return; // Security boundary: students cannot launch staff/director simulator

        currentSimRole = role || 'admin';
        currentSimIndex = 0;

        const modalEl = document.getElementById('systemDemoModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }

        renderSimulatorSlide();

        setTimeout(() => {
            const simModalEl = document.getElementById('roleSimulatorModal');
            const simModal = bootstrap.Modal.getOrCreateInstance(simModalEl);
            simModal.show();
        }, 300);
    };

    function renderSimulatorSlide() {
        const slides = SIMULATOR_SLIDES[currentSimRole] || SIMULATOR_SLIDES['admin'];
        const slide = slides[currentSimIndex] || slides[0];

        let headerGrad = currentSimRole === 'admin' 
            ? 'linear-gradient(135deg, #1e3a8a, #0284c7)'
            : (currentSimRole === 'superadmin' ? 'linear-gradient(135deg, #78350f, #b45309)' : 'linear-gradient(135deg, #072F1B, #0C4E2D)');
        
        let roleTitle = currentSimRole === 'admin' 
            ? 'OSA Staff Evaluator Workflow' 
            : (currentSimRole === 'superadmin' ? 'OSA Director & SuperAdmin Workflow' : 'Student Applicant Workflow');

        let roleSubtitle = currentSimRole === 'admin'
            ? 'Application Triage, Document Fraud Forensics & Fast Determinations'
            : (currentSimRole === 'superadmin' ? 'Executive Governance, Statutory Audits & AI Threshold Calibration' : 'Paperless Registration, Grade Scanning & Stipend Clearance');

        document.getElementById('simulatorHeader').style.background = headerGrad;
        document.getElementById('simulatorRoleTitle').innerText = roleTitle;
        document.getElementById('simulatorRoleSubtitle').innerText = roleSubtitle;

        document.getElementById('simulatorStepIndicator').innerText = `Step ${currentSimIndex + 1} of ${slides.length}`;
        document.getElementById('simulatorStepBadge').innerText = slide.badge;

        const container = document.getElementById('simulatorCardContent');
        container.innerHTML = `
            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">${slide.title}</h5>
            <p class="text-muted small mb-3" style="line-height: 1.6;">${slide.desc}</p>
            ${slide.visual}
        `;

        document.getElementById('simPrevBtn').disabled = (currentSimIndex === 0);
        const nextBtn = document.getElementById('simNextBtn');
        if (currentSimIndex === slides.length - 1) {
            nextBtn.innerHTML = 'Complete Showcase 🎉';
        } else {
            nextBtn.innerHTML = 'Next Slide <i class="fa-solid fa-arrow-right ms-1"></i>';
        }
    }

    window.navigateSimulator = function(direction) {
        const slides = SIMULATOR_SLIDES[currentSimRole] || SIMULATOR_SLIDES['admin'];
        const nextIdx = currentSimIndex + direction;

        if (nextIdx >= slides.length) {
            const simModalEl = document.getElementById('roleSimulatorModal');
            const simModal = bootstrap.Modal.getInstance(simModalEl);
            if (simModal) simModal.hide();
            return;
        }

        if (nextIdx >= 0 && nextIdx < slides.length) {
            currentSimIndex = nextIdx;
            renderSimulatorSlide();
        }
    };

    // ── SYSTEM TOUR MODAL LAUNCHER ──
    window.openSystemTourModal = function(role) {
        const isStudent = {{ $isStudent ? 'true' : 'false' }};
        if (isStudent && role && role !== 'student') role = 'student'; // Force student role for students

        const modalEl = document.getElementById('systemDemoModal');
        if (!modalEl) return;

        if (role) {
            let tabId = 'student-demo-tab';
            if (role === 'admin') tabId = 'admin-demo-tab';
            else if (role === 'superadmin') tabId = 'director-demo-tab';

            const tabBtn = document.getElementById(tabId);
            if (tabBtn && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                const tab = bootstrap.Tab.getOrCreateInstance(tabBtn);
                tab.show();
            }
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    };

    window.startCurrentRoleTour = function() {
        const modalEl = document.getElementById('systemDemoModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }

        const activePane = document.querySelector('#demoRoleTabsContent .tab-pane.active');
        let role = 'student';
        if (activePane) {
            if (activePane.id.includes('admin')) role = 'admin';
            else if (activePane.id.includes('director')) role = 'superadmin';
            else role = 'student';
        }

        setTimeout(() => {
            startLiveElementTour(role);
        }, 350);
    };

    window.startLiveElementTour = function(role) {
        const modalEl = document.getElementById('systemDemoModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }

        const activeRole = role || '{{ $userRole }}';

        if (typeof Shepherd === 'undefined') {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css';
            document.head.appendChild(link);

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/js/shepherd.min.js';
            script.onload = () => runShepherdTour(activeRole);
            document.body.appendChild(script);
        } else {
            runShepherdTour(activeRole);
        }
    };

    function runShepherdTour(role) {
        const isMobile = window.innerWidth < 992;

        const tour = new Shepherd.Tour({
            useModalOverlay: true,
            exitOnEsc: true,
            defaultStepOptions: {
                classes: 'shadow-lg rounded-4 border-0 p-3 bg-white shepherd-theme-aegis',
                scrollTo: { behavior: 'smooth', block: 'center' },
                cancelIcon: { enabled: true }
            }
        });

        // Step 1: Navigation Menu
        const bottomNav = document.querySelector('.mobile-bottom-nav');
        const sidebarEl = document.getElementById('mainSidebar') || document.querySelector('.sidebar') || document.querySelector('nav');
        
        if (isMobile && bottomNav && bottomNav.offsetParent !== null) {
            tour.addStep({
                id: 'tour-mobile-nav',
                title: '📱 Mobile Bottom Navigation',
                text: 'Quickly switch between Home, Available Scholarships, News, and your Profile using this convenient navigation bar.',
                attachTo: { element: bottomNav, on: 'top' },
                buttons: [
                    { text: 'Skip', action: tour.complete, classes: 'btn btn-sm btn-light' },
                    { text: 'Next Step →', action: tour.next, classes: 'btn btn-sm btn-success' }
                ]
            });
        } else if (sidebarEl && sidebarEl.offsetParent !== null) {
            const isAdmin = role === 'admin' || role === 'superadmin';
            tour.addStep({
                id: 'tour-sidebar',
                title: '📌 Portal Navigation Menu',
                text: isAdmin
                    ? 'Navigate administrative workspaces: review applications, oversee grant programs, broadcast communications, and configure settings.'
                    : 'Navigate your personalized portal: view active scholarship grants, browse application catalogs, and manage account security.',
                attachTo: { element: sidebarEl, on: 'right' },
                buttons: [
                    { text: 'Skip', action: tour.complete, classes: 'btn btn-sm btn-light' },
                    { text: 'Next Step →', action: tour.next, classes: 'btn btn-sm btn-success' }
                ]
            });
        }

        // Step 2: Page-Specific Contextual Target
        const profileCard = document.querySelector('.card:has(#profile_name)') || document.querySelector('.card:has(form)');
        const appStatusCard = document.querySelector('.stat-card') || document.querySelector('.card:has(.badge)');
        const mainContentEl = profileCard || appStatusCard || document.querySelector('.main-content') || document.querySelector('main');

        if (mainContentEl) {
            const isAdmin = role === 'admin' || role === 'superadmin';
            let stepText = '';
            if (profileCard) {
                stepText = isAdmin
                    ? 'Manage your institutional administrator profile, security credentials, active 2FA trusted devices, and governance privileges.'
                    : 'Maintain your verified student identity: CLSU ID Number, enrolled degree program, parent emergency contacts, and active 2FA trusted devices.';
            } else {
                stepText = isAdmin
                    ? 'Review incoming scholarship applications, inspect AI tampering traces, and execute evaluative decisions.'
                    : 'Track your live scholarship review status, turnaround times, and download approved application forms here.';
            }

            tour.addStep({
                id: 'tour-main',
                title: '⚡ Active Workspace & Actions',
                text: stepText,
                attachTo: { element: mainContentEl, on: isMobile ? 'bottom' : 'top' },
                buttons: [
                    { text: '← Back', action: tour.back, classes: 'btn btn-sm btn-light' },
                    { text: 'Next Step →', action: tour.next, classes: 'btn btn-sm btn-success' }
                ]
            });
        }

        // Step 3: Top Navigation Controls
        const topNavEl = document.querySelector('.topbar') || document.querySelector('.navbar') || document.querySelector('header');
        if (topNavEl) {
            const isAdmin = role === 'admin' || role === 'superadmin';
            tour.addStep({
                id: 'tour-header',
                title: '🔔 Live Clock & Notifications',
                text: isAdmin
                    ? 'View official Philippine Standard Time (PHT, UTC+8), monitor the active academic term badge, and receive real-time applicant and system notifications.'
                    : 'View official Philippine Standard Time (PHT, UTC+8), monitor the active academic term badge, and receive real-time scholarship status alerts.',
                attachTo: { element: topNavEl, on: 'bottom' },
                buttons: [
                    { text: '← Back', action: tour.back, classes: 'btn btn-sm btn-light' },
                    { text: 'Finish Tour 🎉', action: tour.complete, classes: 'btn btn-sm btn-success' }
                ]
            });
        }

        // Allow tapping backdrop overlay to dismiss tour cleanly
        document.addEventListener('click', (e) => {
            if (e.target && (e.target.classList.contains('shepherd-modal-overlay-container') || (e.target.tagName && e.target.tagName.toLowerCase() === 'path' && e.target.closest('.shepherd-modal-overlay-container')))) {
                tour.complete();
            }
        });

        tour.start();
    }

    // Resolve WAI-ARIA aria-hidden descendant focus retention warning on modal close
    document.addEventListener('DOMContentLoaded', () => {
        ['systemDemoModal', 'stageDetailModal', 'roleSimulatorModal'].forEach(id => {
            const m = document.getElementById(id);
            if (m) {
                m.addEventListener('hide.bs.modal', () => {
                    if (m.contains(document.activeElement)) {
                        document.activeElement.blur();
                    }
                });
            }
        });
    });
</script>
