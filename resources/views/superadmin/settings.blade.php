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
                                <img src="{{ route('system.logo') }}" id="logo-preview-img" style="width:100%; height:100%; object-fit:contain;">
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


            <!-- Database Testing & Student Purge Utilities -->
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; border-left: 4px solid #ef4444 !important;">
                <div class="card-header bg-transparent py-3 border-bottom border-light d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-danger"><i class="fa-solid fa-database me-2"></i> Testing & Database Maintenance</h5>
                    <span class="badge bg-secondary rounded-pill px-3 py-1">
                        {{ \App\Models\User::where('role', 'student')->count() }} Students Active
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-8">
                            <h6 class="fw-bold text-dark mb-1">Purge All Student User Accounts</h6>
                            <p class="text-muted small mb-0">
                                Removes all student records, student profiles, applications, uploaded documents, and notifications for a clean testing environment.
                                <strong>Staff and Director accounts are completely preserved.</strong>
                            </p>
                        </div>
                        <div class="col-lg-4 text-lg-end text-start">
                            <button type="button" class="btn btn-outline-danger py-2 px-3 fw-bold rounded-pill" onclick="confirmPurgeStudents()">
                                <i class="fa-solid fa-trash-can me-1"></i> Purge Test Students
                            </button>
                        </div>
                    </div>
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

        <form id="purgeStudentsForm" action="{{ route('superadmin.settings.purge-students') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <script>
            function confirmRevokeDevices() {
                if (confirm('CAUTION: Are you sure you want to revoke all remembered trusted devices system-wide? Every user will be required to re-verify using MFA on their next login.')) {
                    document.getElementById('revokeDevicesForm').submit();
                }
            }

            function confirmPurgeStudents() {
                if (confirm('WARNING: Are you sure you want to delete ALL student users and their application data from the database? This action cannot be undone and is intended for clean-slate testing.')) {
                    document.getElementById('purgeStudentsForm').submit();
                }
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
