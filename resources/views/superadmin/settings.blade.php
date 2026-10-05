@extends('layouts.app')

@section('title', 'System Settings | A.E.G.I.S.')
@section('page-title', 'System Settings')
@section('page-subtitle', 'Configure system branding, assets, and AI fraud parameters')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px;" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px;" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px;" role="alert">
                <i class="fa-solid fa-circle-info me-2"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Academic Terms & Current Semester Manager Card -->
        <div class="card mb-4" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02); border: 1px solid var(--border-color, #e2e8f0);">
            <div class="card-header bg-transparent py-3.5 px-4 border-bottom border-light d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-graduation-cap text-success"></i> Academic Terms & Current Semester
                    </h5>
                    <p class="text-muted small mb-0">Control which academic semester is actively accepting applications, renewals, and evaluations.</p>
                </div>
                <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-pill shadow-xs d-inline-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#newAcademicTermModal">
                    <i class="fa-solid fa-plus"></i> Add Academic Term
                </button>
            </div>
            <div class="card-body p-4">
                <!-- Current Active Term Hero Callout -->
                <div class="p-3.5 rounded-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3"
                     style="background: linear-gradient(135deg, rgba(12, 78, 45, 0.08) 0%, rgba(242, 169, 0, 0.08) 100%); border: 1px solid rgba(12, 78, 45, 0.15);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 48px; height: 48px; background: var(--clsu-green, #0c4e2d); color: #ffffff;">
                            <i class="fa-solid fa-calendar-check fs-4"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="badge bg-success px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem; letter-spacing: 0.03em;">
                                    <i class="fa-solid fa-circle-check me-1"></i> CURRENT ACTIVE SEMESTER
                                </span>
                                @if(isset($activeTerm) && $activeTerm)
                                    <span class="text-muted small">Updated {{ $activeTerm->updated_at->diffForHumans() }}</span>
                                @endif
                            </div>
                            <h4 class="mb-0 fw-bold text-dark">
                                @if(isset($activeTerm) && $activeTerm)
                                    {{ $activeTerm->formatted_semester }}, Academic Year {{ $activeTerm->academic_year }}
                                @else
                                    <span class="text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> No Active Semester Configured</span>
                                @endif
                            </h4>
                            <p class="text-muted small mb-0 mt-1">
                                @if(isset($activeTerm) && $activeTerm)
                                    All student scholarship applications and renewal submissions are currently assigned to this active term.
                                @else
                                    Please create or activate an academic term so students can apply.
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-end d-none d-md-block">
                        <div class="small text-muted fw-semibold">Applications Filed</div>
                        <div class="fs-4 fw-bold text-success">{{ isset($activeTerm) && $activeTerm ? $activeTerm->applications_count : 0 }}</div>
                    </div>
                </div>

                <!-- Terms List Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th scope="col" class="py-2.5">Academic Term</th>
                                <th scope="col" class="py-2.5">Academic Year</th>
                                <th scope="col" class="py-2.5 text-center">Status</th>
                                <th scope="col" class="py-2.5 text-center">Applications</th>
                                <th scope="col" class="py-2.5 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($academicTerms ?? [] as $term)
                                <tr class="{{ $term->is_active ? 'table-success-subtle fw-semibold' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($term->is_active)
                                                <i class="fa-solid fa-circle-dot text-success" title="Active Semester"></i>
                                            @else
                                                <i class="fa-regular fa-circle text-muted" title="Inactive"></i>
                                            @endif
                                            <span class="text-dark">{{ $term->formatted_semester }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="font-monospace text-dark">{{ $term->academic_year }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($term->is_active)
                                            <span class="badge bg-success px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-check me-1"></i> ACTIVE
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                            {{ $term->applications_count }} {{ Str::plural('application', $term->applications_count) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex align-items-center gap-2">
                                            @if(!$term->is_active)
                                                <form action="{{ route('superadmin.terms.activate', $term->id) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirmSwitchTerm(event, '{{ addslashes($term->full_term_label) }}', this);">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5"
                                                            style="font-size: 0.78rem;">
                                                        <i class="fa-solid fa-toggle-on"></i> Set Active
                                                    </button>
                                                </form>

                                                @if($term->applications_count === 0)
                                                    <form action="{{ route('superadmin.terms.destroy', $term->id) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirmDeleteTerm(event, '{{ addslashes($term->full_term_label) }}', this);">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1.5"
                                                                title="Delete Unused Term" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                            <i class="fa-solid fa-trash-can" style="font-size: 0.75rem;"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle p-1.5 disabled" 
                                                            disabled title="Cannot delete: contains linked student applications"
                                                            style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; opacity: 0.4;">
                                                        <i class="fa-solid fa-trash-can" style="font-size: 0.75rem;"></i>
                                                    </button>
                                                @endif
                                            @else
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill" style="font-size: 0.76rem;">
                                                    <i class="fa-solid fa-lock me-1"></i> Current Active
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-folder-open fa-2x mb-2 d-block text-secondary"></i>
                                        No academic terms configured yet. Click "Add Academic Term" above to set up the first semester.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Branding Settings -->
            <div class="card mb-4" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <div class="card-header bg-transparent py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-palette me-2 text-success"></i> Portal Branding</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="app_name" class="form-label fw-semibold small text-muted">Application Name</label>
                            <input type="text" class="form-control py-2 @error('app_name') is-invalid @enderror" 
                                   id="app_name" name="app_name" 
                                   value="{{ old('app_name', $settings['app_name']) }}" required style="border-radius: 10px;">
                            @error('app_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="university_name" class="form-label fw-semibold small text-muted">University Name</label>
                            <input type="text" class="form-control py-2 @error('university_name') is-invalid @enderror" 
                                   id="university_name" name="university_name" 
                                   value="{{ old('university_name', $settings['university_name']) }}" required style="border-radius: 10px;">
                            @error('university_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Smart Auto-Approval Settings -->
            <div class="card mb-4" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <div class="card-header bg-transparent py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-wand-magic-sparkles me-2 text-success"></i> Smart Auto-Approval Engine</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="form-check form-switch mb-3">
                                <input type="hidden" name="auto_approval_enabled" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="auto_approval_enabled" name="auto_approval_enabled" value="1" {{ old('auto_approval_enabled', $settings['auto_approval_enabled']) === '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-dark" for="auto_approval_enabled">Enable Smart Auto-Approval</label>
                            </div>
                            <div class="form-text small text-muted mb-4">
                                Automatically approve applications with 100% GWA match, zero anomaly flags, and high confidence scores. Applications with custom uploads or any AI fraud flag will bypass this and enter the manual review queue.
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="auto_approval_min_confidence" class="form-label fw-semibold small text-muted mb-0">Minimum Auto-Approval AI Confidence (%)</label>
                                <span id="auto_approval_val" class="badge bg-success fw-bold px-2 py-1" style="border-radius: 6px;">{{ $settings['auto_approval_min_confidence'] }}%</span>
                            </div>
                            <input type="number" class="form-control py-2 @error('auto_approval_min_confidence') is-invalid @enderror" 
                                   id="auto_approval_min_confidence" name="auto_approval_min_confidence" min="0" max="100" step="0.1" 
                                   value="{{ old('auto_approval_min_confidence', $settings['auto_approval_min_confidence']) }}" required style="border-radius: 10px;"
                                   oninput="document.getElementById('auto_approval_slider').value = this.value; document.getElementById('auto_approval_val').innerText = this.value + '%'">
                            
                            <input type="range" class="form-range mt-2" id="auto_approval_slider" min="0" max="100" step="0.5"
                                   value="{{ old('auto_approval_min_confidence', $settings['auto_approval_min_confidence']) }}"
                                   oninput="document.getElementById('auto_approval_min_confidence').value = this.value; document.getElementById('auto_approval_val').innerText = this.value + '%'">
                            
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.68rem; margin-top: 2px;">
                                <span>0% (Allow Any)</span>
                                <span class="fw-bold text-success">≥85% Recommended</span>
                                <span>100% (Strict)</span>
                            </div>

                            <div class="form-text small text-muted mt-1">
                                Minimum AI confidence score required (calculated as 100 - fraud probability) to qualify for auto-approval.
                            </div>
                            @error('auto_approval_min_confidence')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="auto_approval_max_anomalies" class="form-label fw-semibold small text-muted">Maximum Allowed Anomaly Flags</label>
                            <input type="number" class="form-control py-2 @error('auto_approval_max_anomalies') is-invalid @enderror" 
                                   id="auto_approval_max_anomalies" name="auto_approval_max_anomalies" min="0" step="1" 
                                   value="{{ old('auto_approval_max_anomalies', $settings['auto_approval_max_anomalies']) }}" required style="border-radius: 10px;">
                            <div class="form-text small text-muted mt-1">
                                Maximum count of minor AI scanner anomaly flags (like blurry pages) allowed. Recommend 0 for maximum safety.
                            </div>
                            @error('auto_approval_max_anomalies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Settings -->
            <div class="card mb-4" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <div class="card-header bg-transparent py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-microchip me-2 text-success"></i> AI Fraud Parameters</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="ai_fraud_threshold" class="form-label fw-semibold small text-muted mb-0">AI Fraud Threshold (%)</label>
                                <span id="threshold-val" class="badge bg-success fw-bold px-2 py-1" style="border-radius: 6px;">{{ $settings['ai_fraud_threshold'] }}%</span>
                            </div>
                            <input type="range" class="form-range" id="ai_fraud_threshold" name="ai_fraud_threshold" 
                                   min="0" max="100" step="0.5" 
                                   value="{{ old('ai_fraud_threshold', $settings['ai_fraud_threshold']) }}"
                                   oninput="document.getElementById('threshold-val').innerText = this.value + '%'">
                            <div class="form-text small text-muted mt-2">
                                Flag document scans with scores higher than or equal to this threshold. Lower values increase strictness.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="gwa_discrepancy_tolerance" class="form-label fw-semibold small text-muted">GWA Discrepancy Tolerance</label>
                            <input type="number" class="form-control py-2 @error('gwa_discrepancy_tolerance') is-invalid @enderror" 
                                   id="gwa_discrepancy_tolerance" name="gwa_discrepancy_tolerance" 
                                   step="0.001" min="0" max="5"
                                   value="{{ old('gwa_discrepancy_tolerance', $settings['gwa_discrepancy_tolerance']) }}" required style="border-radius: 10px;">
                            <div class="form-text small text-muted mt-1">
                                Maximum variance allowed between the declared GWA and the AI-extracted document GWA (e.g. 0.01).
                            </div>
                            @error('gwa_discrepancy_tolerance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- AI Microservice Health & Live Auto-Wake Controller -->
                    <div class="mt-4 pt-4 border-top border-light">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-server text-success"></i> AI Microservice Container Health
                                    <span id="aiLiveBadge" class="badge bg-secondary px-2 py-1 small" style="border-radius: 6px;">
                                        <i class="fa-solid fa-spinner fa-spin me-1"></i> Checking status...
                                    </span>
                                </h6>
                                <p class="small text-muted mb-0">
                                    Endpoint: <code class="text-dark bg-light px-2 py-1 rounded" style="font-size: 0.8rem;">{{ config('services.ai.url', 'http://127.0.0.1:5000') }}</code>
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnRefreshAiStatus" onclick="checkAiHealth()" style="border-radius: 8px;">
                                    <i class="fa-solid fa-arrows-rotate me-1"></i> Check Health
                                </button>
                                <button type="button" class="btn btn-sm btn-success fw-bold text-white px-3" id="btnWakeAi" onclick="wakeAiService()" style="border-radius: 8px;">
                                    <i class="fa-solid fa-bolt me-1"></i> Wake Up AI
                                </button>
                            </div>
                        </div>

                        <!-- Wake Progress / Message Banner -->
                        <div id="aiStatusAlert" class="alert py-2 px-3 small d-none" role="alert" style="border-radius: 10px;"></div>

                        <!-- 24/7 Automated Keep-Alive Card -->
                        <div class="card bg-light border-0 mt-3" style="border-radius: 12px;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fa-solid fa-bell text-warning mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark small mb-1">How to Keep AI Awake 24/7 Automatically (Free)</div>
                                        <div class="small text-muted mb-2">
                                            Free cloud tiers (Hugging Face Spaces & Render) put inactive containers to sleep. To keep the AI hot 24/7 without manual intervention, configure a free keep-alive cron job (using <a href="https://cron-job.org" target="_blank" class="fw-semibold text-success">Cron-Job.org</a> or <a href="https://uptimerobot.com" target="_blank" class="fw-semibold text-success">UptimeRobot</a>) targeting either of these URLs every <strong>10 minutes</strong>:
                                        </div>
                                        <div class="input-group input-group-sm mb-2" style="max-width: 600px;">
                                            <span class="input-group-text bg-white fw-semibold text-muted" style="font-size: 0.75rem;">AI Wake Endpoint</span>
                                            <input type="text" class="form-control bg-white" readonly value="{{ route('ai.wake') }}" id="wakeEndpointInput" style="font-size: 0.78rem;">
                                            <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('wakeEndpointInput').value); this.innerText='Copied!'; setTimeout(() => this.innerText='Copy', 2000);">Copy</button>
                                        </div>
                                        <div class="input-group input-group-sm" style="max-width: 600px;">
                                            <span class="input-group-text bg-white fw-semibold text-muted" style="font-size: 0.75rem;">Unified Scheduler</span>
                                            <input type="text" class="form-control bg-white" readonly value="{{ url('/scheduler/run?key=' . config('services.scheduler.key', 'aegis_cron_secret')) }}" id="schedulerEndpointInput" style="font-size: 0.78rem;">
                                            <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('schedulerEndpointInput').value); this.innerText='Copied!'; setTimeout(() => this.innerText='Copy', 2000);">Copy</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branding Assets / Logo -->
            <div class="card mb-4" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <div class="card-header bg-transparent py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-image me-2 text-success"></i> Portal Logo Asset</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-4 flex-wrap flex-md-nowrap">
                        <div class="flex-shrink-0" style="width: 100px; height: 100px; border-radius: 16px; background: var(--clsu-bg); border: 2px dashed var(--border-color); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            @if($settings['app_logo'])
                                <img src="{{ route('system.logo') }}" id="logo-preview-img" alt="Logo Preview" style="width:100%; height:100%; object-fit:contain;" onerror="this.onerror=null; this.src='{{ asset('images/clsu-seal.png') }}';">
                            @else
                                <div id="logo-preview-placeholder" class="text-muted"><i class="fa-solid fa-shield-halved fa-2x"></i></div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <label for="app_logo" class="form-label fw-semibold small text-muted">Upload Custom Logo</label>
                            <input type="file" class="form-control py-2 @error('app_logo') is-invalid @enderror" 
                                   id="app_logo" name="app_logo" accept="image/*" style="border-radius: 10px;">
                            <div class="form-text small text-muted mt-1">
                                Recommended square format (PNG or WebP), max size 2MB.
                            </div>
                            @error('app_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if($settings['app_logo'])
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" name="reset_logo" id="reset_logo" value="1">
                                    <label class="form-check-label text-danger small fw-semibold" for="reset_logo">
                                        Reset to Default Shield Icon
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- MFA & Device Security Controls -->
            <div class="card mb-4" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <div class="card-header bg-transparent py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-shield-halved me-2 text-success"></i> MFA & Device Security Controls</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6">
                            <label for="mfa_enforcement" class="form-label fw-semibold small text-muted">System-Wide MFA Enforcement</label>
                            <select class="form-select py-2" id="mfa_enforcement" name="mfa_enforcement" style="border-radius: 10px;">
                                <option value="all" {{ old('mfa_enforcement', $settings['mfa_enforcement']) === 'all' ? 'selected' : '' }}>Enforced for All Users (Highest Security)</option>
                                <option value="students" {{ old('mfa_enforcement', $settings['mfa_enforcement']) === 'students' ? 'selected' : '' }}>Enforced for Students Only</option>
                                <option value="none" {{ old('mfa_enforcement', $settings['mfa_enforcement']) === 'none' ? 'selected' : '' }}>Disabled System-Wide</option>
                            </select>
                            <div class="form-text small text-muted mt-2">
                                Control which users are required to undergo Multi-Factor Authentication upon logging in.
                            </div>
                        </div>

                        <div class="col-md-6 text-md-end text-start">
                            <label class="form-label fw-semibold small text-muted d-block">Emergency Security Action</label>
                            <button type="button" class="btn btn-outline-danger py-2 fw-semibold" style="border-radius: 10px;" onclick="confirmRevokeDevices()">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Revoke All Devices System-Wide
                            </button>
                            <div class="form-text small text-muted mt-2">
                                Revokes all remembered device tokens. All users will be forced to undergo MFA next time.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactive Training & Role Guided Demos -->
            <div class="card mb-4 border-0 shadow-xs rounded-3 overflow-hidden" style="background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #e2e8f0) !important;">
                <div class="card-body py-2.5 px-3 d-flex align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2.5 overflow-hidden">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px; background: rgba(242, 169, 0, 0.15);">
                            <i class="fa-solid fa-graduation-cap text-warning" style="font-size: 0.95rem;"></i>
                        </div>
                        <div class="overflow-hidden">
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">Interactive Training & Director Walkthrough</h6>
                            <p class="text-muted small mb-0 d-none d-md-block" style="font-size: 0.74rem;">Learn and review end-to-end executive governance workflows and audit logs.</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-warning btn-sm text-dark fw-bold px-3 py-1.5 rounded-pill shadow-xs flex-shrink-0 d-inline-flex align-items-center gap-1.5" 
                            style="font-size: 0.78rem;" onclick="openSystemTourModal('superadmin')">
                        <i class="fa-solid fa-crown me-1" style="font-size: 0.7rem;"></i> Launch Director Guide
                    </button>
                </div>
            </div>


            <!-- Actions -->
            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn fw-bold px-4 py-2" 
                        style="background: linear-gradient(135deg, var(--clsu-green), #16703f); color: white; border-radius: 10px; box-shadow: 0 4px 12px rgba(15,89,52,0.25);">
                    <i class="fa-solid fa-save me-1"></i> Save Changes
                </button>
            </div>
        </form>

        <form id="revokeDevicesForm" action="{{ route('superadmin.settings.security-reset') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <!-- Add Academic Term Modal -->
        <div class="modal fade" id="newAcademicTermModal" tabindex="-1" aria-labelledby="newAcademicTermModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                    <form action="{{ route('superadmin.terms.store') }}" method="POST">
                        @csrf
                        <div class="modal-header py-3 px-4" style="background: linear-gradient(135deg, var(--clsu-green, #0c4e2d), #16703f); color: white;">
                            <h5 class="modal-title fw-bold" id="newAcademicTermModalLabel">
                                <i class="fa-solid fa-calendar-plus me-2"></i> Add New Academic Term
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="term_semester" class="form-label fw-semibold small text-muted">Semester</label>
                                <select class="form-select py-2" id="term_semester" name="semester" required style="border-radius: 10px;">
                                    <option value="1st Semester">1st Semester</option>
                                    <option value="2nd Semester">2nd Semester</option>
                                    <option value="Midyear">Midyear / Summer</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="term_academic_year" class="form-label fw-semibold small text-muted">Academic Year (Format: YYYY-YYYY)</label>
                                <input type="text" class="form-control py-2 font-monospace" id="term_academic_year" name="academic_year" 
                                       placeholder="e.g. 2026-2027" pattern="\d{4}-\d{4}" maxlength="9" required style="border-radius: 10px;"
                                       value="{{ date('Y') . '-' . (date('Y') + 1) }}">
                                <div class="form-text small text-muted">
                                    Example: <code>2025-2026</code> or <code>2026-2027</code>
                                </div>
                            </div>

                            <div class="form-check form-switch p-3 rounded-3 bg-light border mt-4">
                                <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="term_set_active" name="set_active" value="1" checked>
                                <label class="form-check-label fw-semibold text-dark" for="term_set_active">
                                    Set as Current Active Semester Immediately
                                </label>
                                <div class="form-text small text-muted ms-0 mt-1">
                                    When checked, this semester immediately becomes active across student application forms and admin analytics.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer py-3 px-4 bg-light border-0 d-flex justify-content-between">
                            <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success fw-bold px-4 py-2 rounded-pill shadow-xs">
                                <i class="fa-solid fa-save me-1"></i> Save Academic Term
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function confirmSwitchTerm(event, termLabel, form) {
                event.preventDefault();
                if (typeof AegisAlert !== 'undefined' && AegisAlert.confirm) {
                    AegisAlert.confirm({
                        title: 'Switch Active Semester?',
                        text: `Are you sure you want to set "${termLabel}" as the CURRENT ACTIVE semester? All incoming scholarship applications and renewals will link to this term.`,
                        icon: 'question',
                        confirmText: 'Yes, Switch Semester',
                        cancelText: 'Cancel'
                    }).then(confirmed => {
                        if (confirmed) form.submit();
                    });
                } else {
                    if (confirm(`Switch current active semester to "${termLabel}"?`)) {
                        form.submit();
                    }
                }
                return false;
            }

            function confirmDeleteTerm(event, termLabel, form) {
                event.preventDefault();
                if (typeof AegisAlert !== 'undefined' && AegisAlert.confirm) {
                    AegisAlert.confirm({
                        title: 'Delete Academic Term?',
                        text: `Are you sure you want to permanently delete "${termLabel}"? This action cannot be undone.`,
                        icon: 'warning',
                        isDestructive: true,
                        confirmText: 'Yes, Delete Term',
                        cancelText: 'Cancel'
                    }).then(confirmed => {
                        if (confirmed) form.submit();
                    });
                } else {
                    if (confirm(`Delete academic term "${termLabel}"?`)) {
                        form.submit();
                    }
                }
                return false;
            }

            function confirmRevokeDevices() {
                AegisAlert.confirm({
                    title: 'Revoke All Remembered Devices?',
                    text: 'CAUTION: Are you sure you want to revoke all remembered trusted devices system-wide? Every user will be required to re-verify using MFA on their next login.',
                    icon: 'warning',
                    isDestructive: true,
                    confirmText: 'Yes, Revoke All Devices',
                    cancelText: 'Cancel'
                }).then(confirmed => {
                    if (confirmed) {
                        document.getElementById('revokeDevicesForm').submit();
                    }
                });
            }

            async function checkAiHealth() {
                const badge = document.getElementById('aiLiveBadge');
                const btn = document.getElementById('btnRefreshAiStatus');
                if (!badge) return;
                badge.className = 'badge bg-warning text-dark px-2 py-1 small';
                badge.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Testing...';
                if (btn) btn.disabled = true;

                try {
                    const res = await fetch('{{ route('superadmin.settings.ai-status') }}');
                    const data = await res.json();
                    if (data.status === 'online') {
                        badge.className = 'badge bg-success px-2 py-1 small';
                        badge.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i> Online & Warm (${data.latency_ms}ms)`;
                    } else if (data.status === 'sleeping') {
                        badge.className = 'badge bg-danger px-2 py-1 small';
                        badge.innerHTML = '<i class="fa-solid fa-moon me-1"></i> Sleeping (Needs Wake-up)';
                    } else {
                        badge.className = 'badge bg-warning text-dark px-2 py-1 small';
                        badge.innerHTML = `<i class="fa-solid fa-triangle-exclamation me-1"></i> ${data.status}`;
                    }
                } catch (e) {
                    badge.className = 'badge bg-danger px-2 py-1 small';
                    badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Unreachable';
                } finally {
                    if (btn) btn.disabled = false;
                }
            }

            async function wakeAiService() {
                const badge = document.getElementById('aiLiveBadge');
                const btn = document.getElementById('btnWakeAi');
                const alertBox = document.getElementById('aiStatusAlert');

                if (badge) {
                    badge.className = 'badge bg-warning text-dark px-2 py-1 small';
                    badge.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Waking container...';
                }
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Waking...';
                }

                if (alertBox) {
                    alertBox.className = 'alert alert-info py-2 px-3 small d-block';
                    alertBox.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-1"></i> Sent wake-up handshake. Waiting for container to initialize TensorFlow model (takes 15–30 seconds)...';
                }

                try {
                    const res = await fetch('{{ route('ai.wake') }}');
                    const data = await res.json();
                    if (data.success) {
                        if (badge) {
                            badge.className = 'badge bg-success px-2 py-1 small';
                            badge.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i> Active & Warmed Up (${data.latency_ms}ms)`;
                        }
                        if (alertBox) {
                            alertBox.className = 'alert alert-success py-2 px-3 small d-block';
                            alertBox.innerHTML = `<strong>Success:</strong> ${data.message} Response time: ${data.latency_ms}ms.`;
                        }
                    } else {
                        if (badge) {
                            badge.className = 'badge bg-danger px-2 py-1 small';
                            badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Wake Failed';
                        }
                        if (alertBox) {
                            alertBox.className = 'alert alert-danger py-2 px-3 small d-block';
                            alertBox.innerHTML = `<strong>Error:</strong> ${data.message || 'Service could not be reached.'}`;
                        }
                    }
                } catch (e) {
                    if (badge) {
                        badge.className = 'badge bg-danger px-2 py-1 small';
                        badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Timeout / Error';
                    }
                    if (alertBox) {
                        alertBox.className = 'alert alert-danger py-2 px-3 small d-block';
                        alertBox.innerHTML = '<strong>Wake notice:</strong> The wake probe timed out after 35s. The container is likely still spinning up in the background. Click "Check Health" in 10-15 seconds.';
                    }
                } finally {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-bolt me-1"></i> Wake Up AI';
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                checkAiHealth();
            });
        </script>
    </div>
</div>
@endsection
