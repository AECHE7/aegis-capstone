<!-- ── A.E.G.I.S. STAKEHOLDER DATA MANAGEMENT & PRIVACY GOVERNANCE GUIDE MODAL ── -->
<div class="modal fade" id="dataManagementGuideModal" tabindex="-1" aria-labelledby="dataManagementGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            
            <!-- Modal Header — Explicit inline colour overrides needed because global .modal-content sets color: var(--text-main) -->
            <div class="modal-header border-0 p-3 p-md-4" style="background: linear-gradient(135deg, #072F1B 0%, #0C4E2D 50%, #00754A 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="fa-solid fa-file-shield text-warning fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <h5 class="modal-title fw-bold mb-0" id="dataManagementGuideModalLabel" style="font-size: 1.1rem; color: #ffffff !important;">
                                Stakeholder Data Management &amp; Governance Guide
                            </h5>
                            <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded-pill" style="font-size: 0.68rem;">
                                R.A. 10173 &amp; ISO/IEC 25010 Compliant
                            </span>
                        </div>
                        <p class="mb-0 small" style="font-size: 0.78rem; color: rgba(255,255,255,0.75) !important;">
                            Official Operating Protocols for
                            @auth
                                @if(auth()->user()->role === 'student')
                                    Student Applicants
                                @elseif(auth()->user()->role === 'admin')
                                    OSA Evaluators
                                @else
                                    University Administrators & Grant Sponsors
                                @endif
                            @endauth
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto flex-shrink-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-3 p-md-4 bg-light bg-opacity-50">
                <div class="d-flex flex-column gap-3">
                    
                    <!-- Section 1: Legal Framework — shown to ALL roles -->
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-circle p-2 bg-success-subtle text-success">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </span>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">1. Statutory Compliance (Republic Act No. 10173)</h6>
                        </div>
                        <p class="small text-muted mb-2" style="font-size: 0.8rem; line-height: 1.5;">
                            A.E.G.I.S. is certified under the National Privacy Commission (NPC) Seal of Registration (valid until Nov 12, 2026). All personal data collected—including Certificate of Grades (COG), General Weighted Average (GWA), and student profiles—are processed under the lawful criteria of institutional academic administration and grant stewardship.
                        </p>
                        <div class="d-flex flex-wrap gap-2 pt-1">
                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.72rem;">
                                <i class="fa-solid fa-lock text-success me-1"></i> AES-256 Column Encryption
                            </span>
                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.72rem;">
                                <i class="fa-solid fa-fingerprint text-primary me-1"></i> SHA-256 Document Hashing
                            </span>
                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.72rem;">
                                <i class="fa-solid fa-shield-halved text-warning me-1"></i> NPC Registered DPS
                            </span>
                        </div>
                    </div>

                    @auth
                    @if(auth()->user()->role === 'student')
                    <!-- Section 2: Student Applicant Rights — students only -->
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-circle p-2 bg-primary-subtle text-primary">
                                <i class="fa-solid fa-user-graduate"></i>
                            </span>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">2. Your Rights as a Student Applicant</h6>
                        </div>
                        <ul class="small text-muted mb-0 ps-3" style="font-size: 0.8rem; line-height: 1.6;">
                            <li><strong>Right to Informed Consent:</strong> You explicitly consent to automated OCR grade reading and document forensic authenticity screening upon application submission.</li>
                            <li><strong>ID Format Standard:</strong> Your official CLSU ID must be registered strictly in the <code>00-0000</code> format (e.g. <code>23-1234</code>).</li>
                            <li><strong>Data Accuracy &amp; Rectification:</strong> You may update your academic profile and contact information at any time via the Student Profile module.</li>
                            <li><strong>Retention Period:</strong> Application dossiers and documents are retained for the duration of your active enrollment plus five (5) academic years for CHED and COA statutory audits.</li>
                            <li><strong>Right to Access:</strong> You may request a copy of all data held about you by contacting the OSA Data Protection Officer.</li>
                        </ul>
                    </div>
                    @endif

                    @if(auth()->user()->role === 'admin')
                    <!-- Section 3: OSA Evaluator Responsibilities — admin only -->
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-circle p-2 bg-warning-subtle text-warning">
                                <i class="fa-solid fa-user-shield"></i>
                            </span>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">2. OSA Evaluator Handling &amp; Confidentiality</h6>
                        </div>
                        <ul class="small text-muted mb-0 ps-3" style="font-size: 0.8rem; line-height: 1.6;">
                            <li><strong>Jurisdiction &amp; Scoped Access:</strong> You only have access to applications for the specific scholarship programs assigned to you by the Director. Do not attempt to access unassigned scholarship queues.</li>
                            <li><strong>Strict Confidentiality:</strong> Student financial indicators, grades, and emergency contacts must not be disclosed or processed outside authorized evaluation duties.</li>
                            <li><strong>AI Forensic Verification:</strong> AI fraud probability scores are advisory; you must exercise human-in-the-loop oversight before recording final status updates.</li>
                            <li><strong>Data Minimization:</strong> Download and export only the minimum data required for your evaluation task. All exports are audit-logged.</li>
                            <li><strong>Reporting:</strong> If you observe a suspected data breach or unauthorized access, report immediately to the Director and the OSA DPO.</li>
                        </ul>
                    </div>
                    @endif

                    @if(auth()->user()->role === 'superadmin')
                    <!-- Section 4: Director Governance — superadmin only -->
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-circle p-2 bg-danger-subtle text-danger">
                                <i class="fa-solid fa-crown"></i>
                            </span>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">2. Director Oversight &amp; Regulatory Auditing</h6>
                        </div>
                        <ul class="small text-muted mb-0 ps-3" style="font-size: 0.8rem; line-height: 1.6;">
                            <li><strong>Immutable Audit Logging:</strong> All evaluator decisions, document downloads, and configuration updates are timestamped with evaluator IP and user ID.</li>
                            <li><strong>Regulatory Export Security:</strong> CSV and PDF audit logs exported for institutional audits are logged in the <code>export_access_logs</code> registry.</li>
                            <li><strong>Data Protection &amp; System Availability:</strong> Production data is secured behind 2FA/MFA, automated keep-alive cron jobs ensure zero cold boot delays, and automated database backups are scheduled.</li>
                            <li><strong>Staff Assignment Governance:</strong> Only assign evaluators to scholarship programs they are qualified to review. Unassign staff promptly upon role change or departure.</li>
                            <li><strong>Annual Review:</strong> Conduct an annual review of system access logs, retained data, and data privacy compliance certificates in coordination with the CLSU DPO.</li>
                        </ul>
                    </div>
                    @endif
                    @endauth

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0 p-3 bg-white d-flex justify-content-between">
                <small class="text-muted" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-circle-check text-success me-1"></i> Central Luzon State University &mdash; Office of Student Affairs
                </small>
                <button type="button" class="btn btn-clsu-primary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">
                    I Understand
                </button>
            </div>

        </div>
    </div>
</div>
