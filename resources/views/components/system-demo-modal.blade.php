<!-- ── A.E.G.I.S. INTERACTIVE SYSTEM DEMO & GUIDED ROLE TOUR MODAL ── -->
<div class="modal fade" id="systemDemoModal" tabindex="-1" aria-labelledby="systemDemoModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            
            <!-- Modal Header -->
            <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #072F1B 0%, #0C4E2D 50%, #00754A 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; min-width: 52px;">
                        <i class="fa-solid fa-graduation-cap fs-3 text-warning"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <h4 class="modal-title fw-bold mb-0 text-white" id="systemDemoModalLabel">A.E.G.I.S. System Demo & Guided Walkthrough</h4>
                            <span class="badge bg-warning text-dark fw-bold px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Interactive Training</span>
                        </div>
                        <p class="mb-0 text-white-50 small">Learn end-to-end features and operational workflows across every user role in the portal.</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Role Selector Nav Tabs -->
            <div class="bg-light px-4 pt-3 border-bottom">
                <ul class="nav nav-pills gap-2 pb-3" id="demoRoleTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 {{ (auth()->user()->role ?? 'student') === 'student' ? 'active' : '' }}" 
                                id="student-demo-tab" data-bs-toggle="pill" data-bs-target="#student-demo-pane" type="button" role="tab">
                            <i class="fa-solid fa-user-graduate"></i>
                            <span>Student Applicant</span>
                            @if((auth()->user()->role ?? '') === 'student')
                                <span class="badge bg-light text-success ms-1" style="font-size: 0.65rem;">YOUR ROLE</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 {{ (auth()->user()->role ?? '') === 'admin' ? 'active' : '' }}" 
                                id="admin-demo-tab" data-bs-toggle="pill" data-bs-target="#admin-demo-pane" type="button" role="tab">
                            <i class="fa-solid fa-user-shield"></i>
                            <span>OSA Staff Evaluator</span>
                            @if((auth()->user()->role ?? '') === 'admin')
                                <span class="badge bg-light text-success ms-1" style="font-size: 0.65rem;">YOUR ROLE</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 {{ (auth()->user()->role ?? '') === 'superadmin' ? 'active' : '' }}" 
                                id="director-demo-tab" data-bs-toggle="pill" data-bs-target="#director-demo-pane" type="button" role="tab">
                            <i class="fa-solid fa-crown text-warning"></i>
                            <span>OSA Director / Super Admin</span>
                            @if((auth()->user()->role ?? '') === 'superadmin')
                                <span class="badge bg-light text-success ms-1" style="font-size: 0.65rem;">YOUR ROLE</span>
                            @endif
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Modal Body with Tabs Content -->
            <div class="modal-body p-4 bg-light bg-opacity-50">
                <div class="tab-content" id="demoRoleTabsContent">

                    <!-- ========================================================= -->
                    <!-- TAB 1: STUDENT APPLICANT WORKFLOW                         -->
                    <!-- ========================================================= -->
                    <div class="tab-pane fade {{ (auth()->user()->role ?? 'student') === 'student' ? 'show active' : '' }}" id="student-demo-pane" role="tabpanel">
                        
                        <!-- Role Banner -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #0C4E2D, #166534); color: white;">
                            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div>
                                    <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 mb-2 fw-semibold">STUDENT PORTAL LIFE-CYCLE</span>
                                    <h5 class="fw-bold mb-1 text-white">Paperless Application, Integrity Check & Award Tracking</h5>
                                    <p class="text-white-50 small mb-0">From registering with your verified @clsu.edu.ph institutional email to receiving your official stipend clearance report.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-warning text-dark fw-bold px-3 py-2 rounded-pill shadow-sm" onclick="startLiveElementTour('student')">
                                        <i class="fa-solid fa-play me-1"></i> Launch Live Screen Tour
                                    </button>
                                    @if(auth()->check() && auth()->user()->role === 'student')
                                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-light fw-semibold px-3 py-2 rounded-pill">
                                            <i class="fa-solid fa-house me-1"></i> Go to Dashboard
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 6-Stage Detailed Grid -->
                        <div class="row g-3">
                            <!-- Stage 1 -->
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">STAGE 01</span>
                                        <h6 class="fw-bold mb-0 text-dark">Institutional Registration</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Register using your official <code>@clsu.edu.ph</code> student email and accept the <strong>Data Privacy Act (R.A. 10173)</strong> consent.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>6-digit email OTP activation</li>
                                        <li>NPC Seal of Registration verified</li>
                                        <li>30-day trusted browser tokens</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Stage 2 -->
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">STAGE 02</span>
                                        <h6 class="fw-bold mb-0 text-dark">Profile Completion</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Encode your CLSU Student ID number, enrolled College, Degree Program, and Year Level before applying.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>One-time basic profile onboarding</li>
                                        <li>Automatic redirect protection</li>
                                        <li>Emergency contacts & guardian info</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Stage 3 -->
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">STAGE 03</span>
                                        <h6 class="fw-bold mb-0 text-dark">Explore & Select Grant</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Browse the live scholarship catalog for Institutional (University/College Scholar), DOST, CHED, or Varsity grants.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Transparent criteria & max GWA cutoffs</li>
                                        <li>Stipend amounts & renewal rules</li>
                                        <li>Single active application enforcement</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Stage 4 -->
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">STAGE 04</span>
                                        <h6 class="fw-bold mb-0 text-dark">Apply & Upload COG</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Fill out the step-by-step application form with local auto-save draft resilience and upload your Certificate of Grades.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Auto-saves form drafts locally</li>
                                        <li>Accepts PDF, JPG, and PNG uploads</li>
                                        <li>After-work-hours queue transparency</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Stage 5 -->
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">STAGE 05</span>
                                        <h6 class="fw-bold mb-0 text-dark">AI Grade Integrity Scan</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Uploaded transcripts pass through the ResNet-50 ELA & OCR neural network for tamper verification.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Instant pixel noise & ELA inspection</li>
                                        <li>GWA transcript discrepancy audit</li>
                                        <li>Camera sensor EXIF integrity check</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Stage 6 -->
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">STAGE 06</span>
                                        <h6 class="fw-bold mb-0 text-dark">Clearance & Award Notice</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Upon evaluation approval, receive an official award confirmation email with your clearance report and stipend details.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Real-time dashboard status badge</li>
                                        <li>Official signed PDF approval document</li>
                                        <li>Eligible for semester renewals</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ========================================================= -->
                    <!-- TAB 2: OSA STAFF EVALUATOR WORKFLOW                       -->
                    <!-- ========================================================= -->
                    <div class="tab-pane fade {{ (auth()->user()->role ?? '') === 'admin' ? 'show active' : '' }}" id="admin-demo-pane" role="tabpanel">
                        
                        <!-- Role Banner -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #1e3a8a, #0369a1); color: white;">
                            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div>
                                    <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 mb-2 fw-semibold">OSA STAFF EVALUATOR WORKFLOW</span>
                                    <h5 class="fw-bold mb-1 text-white">Application Triage, Forensic Heatmaps & Rapid Determination</h5>
                                    <p class="text-white-50 small mb-0">Evaluate student applications with AI-assisted document fraud analysis, 1-click preset remarks, and automated audits.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-warning text-dark fw-bold px-3 py-2 rounded-pill shadow-sm" onclick="startLiveElementTour('admin')">
                                        <i class="fa-solid fa-play me-1"></i> Launch Live Screen Tour
                                    </button>
                                    @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin'))
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light fw-semibold px-3 py-2 rounded-pill">
                                            <i class="fa-solid fa-list-check me-1"></i> Go to Review Queue
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 6-Stage Detailed Grid -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">STEP 01</span>
                                        <h6 class="fw-bold mb-0 text-dark">Live Review Queue</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Manage incoming student applications filtered by status (Pending, Under Review, Queued), college, and GWA standing.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Real-time SLA turn-around tracking</li>
                                        <li>Assigned coordinator indicators</li>
                                        <li>Bulk action approval options</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">STEP 02</span>
                                        <h6 class="fw-bold mb-0 text-dark">Forensic Dual-Pane Inspection</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Inspect Certificate of Grades side-by-side with neural Error Level Analysis (ELA) and Grad-CAM heatmaps.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>High-resolution pan & zoom inspection</li>
                                        <li>Neural tamper probability rating</li>
                                        <li>Camera EXIF & software signature check</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">STEP 03</span>
                                        <h6 class="fw-bold mb-0 text-dark">OCR Discrepancy Gate</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Cross-examine student self-encoded GWA against raw OCR extracted grades directly from the registrar's seal.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Automated GWA difference calculation</li>
                                        <li>Instant flag if discrepancy > tolerance</li>
                                        <li>Prevents accidental qualification errors</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">STEP 04</span>
                                        <h6 class="fw-bold mb-0 text-dark">1-Click Fast Remarks</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Accelerate review times using curated preset remarks for missing requirements, blurriness, or clear qualifications.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Private staff evaluator notes</li>
                                        <li>One-click re-upload requirement request</li>
                                        <li>Audited remarks timestamp</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">STEP 05</span>
                                        <h6 class="fw-bold mb-0 text-dark">Status Determination & Revocation</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Execute official determinations: Approve, Reject, or Revoke grant with mandatory audit reason capture.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Automatic notification to scholar</li>
                                        <li>Renewal vs new grant auto-tagging</li>
                                        <li>Trash recovery safeguard for accidental deletions</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">STEP 06</span>
                                        <h6 class="fw-bold mb-0 text-dark">1-Page Forensic Audit PDF</h6>
                                    </div>
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

                    <!-- ========================================================= -->
                    <!-- TAB 3: DIRECTOR & SUPER ADMIN WORKFLOW                    -->
                    <!-- ========================================================= -->
                    <div class="tab-pane fade {{ (auth()->user()->role ?? '') === 'superadmin' ? 'show active' : '' }}" id="director-demo-pane" role="tabpanel">
                        
                        <!-- Role Banner -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #78350f, #b45309); color: white;">
                            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div>
                                    <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 mb-2 fw-semibold">DIRECTOR & SUPERADMIN WORKFLOW</span>
                                    <h5 class="fw-bold mb-1 text-white">Executive Analytics, User Control & Program Governance</h5>
                                    <p class="text-white-50 small mb-0">Oversee university scholarship budgets, audit statutory compliance, manage staff permissions, and calibrate AI models.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-warning text-dark fw-bold px-3 py-2 rounded-pill shadow-sm" onclick="startLiveElementTour('superadmin')">
                                        <i class="fa-solid fa-play me-1"></i> Launch Live Screen Tour
                                    </button>
                                    @if(auth()->check() && auth()->user()->role === 'superadmin')
                                        <a href="{{ route('superadmin.analytics') }}" class="btn btn-outline-light fw-semibold px-3 py-2 rounded-pill">
                                            <i class="fa-solid fa-chart-line me-1"></i> Open Analytics
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 6-Stage Detailed Grid -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1">MODULE 01</span>
                                        <h6 class="fw-bold mb-0 text-dark">Executive Analytics</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Real-time KPI metrics tracking active scholars, total grants awarded, college demographic distribution, and GWA averages.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Active academic term filtering</li>
                                        <li>Grant allocation breakdown charts</li>
                                        <li>Application conversion funnels</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1">MODULE 02</span>
                                        <h6 class="fw-bold mb-0 text-dark">Complete User Management</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Manage both Student and Staff accounts: toggle active status, invite new evaluators, reset MFA tokens, and send password resets.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Instant account deactivation/reactivation</li>
                                        <li>MFA security reset in 1 click</li>
                                        <li>Staff scholarship assignment controls</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1">MODULE 03</span>
                                        <h6 class="fw-bold mb-0 text-dark">Scholarship Programs</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Configure institutional, government, and private scholarship programs with tailored eligibility rules and criteria.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Set minimum GWA & slot limits</li>
                                        <li>Add custom application form fields</li>
                                        <li>Define semester renewal boundaries</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1">MODULE 04</span>
                                        <h6 class="fw-bold mb-0 text-dark">Announcement Management</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Publish, edit, and schedule urgent university broadcasts, requirements notices, and semester application windows.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>SuperAdmin full edit capabilities</li>
                                        <li>Pinned priority notices</li>
                                        <li>Auto-hides expired semester announcements</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1">MODULE 05</span>
                                        <h6 class="fw-bold mb-0 text-dark">Statutory Audit Logs</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Download tamper-proof regulatory audit logs covering evaluation decisions, evaluator actions, auth events, and document uploads.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>8+ specialized CSV & PDF audit exports</li>
                                        <li>SHA-256 evidence chain verification</li>
                                        <li>IP address & user-agent tracking</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1">MODULE 06</span>
                                        <h6 class="fw-bold mb-0 text-dark">System Governance & Utilities</h6>
                                    </div>
                                    <p class="text-muted small mb-2">Calibrate AI fraud detection thresholds, configure multi-factor enforcement, and execute testing purge utilities.</p>
                                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                                        <li>Adjust ResNet-50 AI fraud threshold (0-100%)</li>
                                        <li>MFA system-wide enforcement toggle</li>
                                        <li>One-click test students database purge</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="fa-solid fa-shield-halved text-success"></i>
                    <span>Official CLSU OSA Training & Demonstration Suite &bull; R.A. 10173 Compliant</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-pill fw-semibold" data-bs-dismiss="modal">
                        Close Guide
                    </button>
                    <button type="button" class="btn btn-success px-4 py-2 rounded-pill fw-bold text-white shadow-sm" style="background: var(--clsu-green, #0C4E2D);" onclick="startCurrentRoleTour()">
                        <i class="fa-solid fa-compass me-1"></i> Start Interactive Tour
                    </button>
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
    }
    #demoRoleTabs .nav-link.active {
        background: var(--clsu-green, #0C4E2D) !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(12, 78, 45, 0.25);
    }
</style>

<script>
    window.openSystemTourModal = function(role) {
        const modalEl = document.getElementById('systemDemoModal');
        if (!modalEl) return;

        if (role) {
            const tabBtn = document.getElementById(role + '-demo-tab') || 
                           (role === 'superadmin' ? document.getElementById('director-demo-tab') : null);
            if (tabBtn) {
                const tab = new bootstrap.Tab(tabBtn);
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
        const role = activePane && activePane.id.includes('admin') ? 'admin' :
                    (activePane && activePane.id.includes('director') ? 'superadmin' : 'student');

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

        // Ensure Shepherd is loaded
        if (typeof Shepherd === 'undefined') {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css';
            document.head.appendChild(link);

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/js/shepherd.min.js';
            script.onload = () => runShepherdTour(role);
            document.body.appendChild(script);
        } else {
            runShepherdTour(role);
        }
    };

    function runShepherdTour(role) {
        const tour = new Shepherd.Tour({
            useModalOverlay: true,
            defaultStepOptions: {
                classes: 'shadow-lg rounded-4 border-0 p-3 bg-white',
                scrollTo: { behavior: 'smooth', block: 'center' },
                cancelIcon: { enabled: true }
            }
        });

        // Step 1: Sidebar Navigation
        const sidebarEl = document.getElementById('mainSidebar') || document.querySelector('.sidebar') || document.querySelector('nav');
        if (sidebarEl) {
            tour.addStep({
                id: 'tour-sidebar',
                title: '📌 Portal Navigation Menu',
                text: role === 'student' 
                    ? 'Use this navigation menu to check your application status, browse the scholarship catalog, and update your student profile.'
                    : (role === 'admin' 
                        ? 'Access the live student evaluation queue, announcement board, and private evaluator notes from here.'
                        : 'Access university analytics, complete user management, scholarship configurations, and compliance audit logs.'),
                attachTo: { element: sidebarEl, on: 'right' },
                buttons: [
                    { text: 'Skip', action: tour.complete, classes: 'btn btn-sm btn-light' },
                    { text: 'Next Step →', action: tour.next, classes: 'btn btn-sm btn-success' }
                ]
            });
        }

        // Step 2: Main Workspace / Dashboard Header
        const mainContentEl = document.querySelector('.main-content') || document.querySelector('main') || document.querySelector('.container-fluid');
        if (mainContentEl) {
            tour.addStep({
                id: 'tour-main',
                title: '⚡ Active Workspace & Actions',
                text: role === 'student'
                    ? 'Here you can view your live scholarship status, submission queue notices, and download official award certificates.'
                    : (role === 'admin'
                        ? 'Inspect student documents, examine AI tamper heatmaps, verify GWA discrepancies, and record evaluation remarks.'
                        : 'Monitor university-wide KPIs, track student demographics, and calibrate fraud tolerance thresholds.'),
                attachTo: { element: mainContentEl, on: 'top' },
                buttons: [
                    { text: '← Back', action: tour.back, classes: 'btn btn-sm btn-light' },
                    { text: 'Next Step →', action: tour.next, classes: 'btn btn-sm btn-success' }
                ]
            });
        }

        // Step 3: Top Navigation Controls
        const topNavEl = document.querySelector('.navbar') || document.querySelector('header');
        if (topNavEl) {
            tour.addStep({
                id: 'tour-header',
                title: '🔔 Notifications & Security Profile',
                text: 'View real-time status notifications, switch light/dark themes, and manage your trusted 2FA devices and security settings.',
                attachTo: { element: topNavEl, on: 'bottom' },
                buttons: [
                    { text: '← Back', action: tour.back, classes: 'btn btn-sm btn-light' },
                    { text: 'Finish Tour 🎉', action: tour.complete, classes: 'btn btn-sm btn-success' }
                ]
            });
        }

        tour.start();
    }
</script>
