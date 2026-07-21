@props(['emergencyReadOnly' => false])

{{-- Interactive Auth Modal Component (Login & Create Account) --}}
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 480px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; background: var(--card-bg, #ffffff); color: var(--text-main, #1e293b);">
            {{-- Modal Header --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4 position-relative">
                <div class="w-100 text-center me-2">
                    <img src="{{ \App\Models\Setting::get('app_logo') ? route('system.logo') : asset('logo.webp') }}" 
                         alt="CLSU Logo" 
                         height="42" 
                         class="mb-2 object-fit-contain"
                         onerror="this.onerror=null; this.src='{{ asset('logo.png') }}';">
                    <h5 class="modal-title fw-bold text-success" id="authModalLabel" style="color: var(--clsu-green, #0C4E2D) !important;">
                        {{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} Portal
                    </h5>
                    <p class="text-muted small mb-0">{{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }}</p>
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Tab Switcher Header --}}
            <div class="px-4 pt-3">
                <ul class="nav nav-pills nav-fill p-1 bg-light rounded-pill border" id="authTabHeader" role="tablist" style="background: var(--clsu-green-muted, #f1f5f9) !important;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill fw-semibold py-2 font-poppins" 
                                id="authTabLogin" 
                                data-bs-toggle="tab" 
                                data-bs-target="#loginTabPanel" 
                                type="button" 
                                role="tab" 
                                aria-controls="loginTabPanel" 
                                aria-selected="true"
                                style="font-size: 0.9rem;">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Sign In
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill fw-semibold py-2 font-poppins" 
                                id="authTabRegister" 
                                data-bs-toggle="tab" 
                                data-bs-target="#registerTabPanel" 
                                type="button" 
                                role="tab" 
                                aria-controls="registerTabPanel" 
                                aria-selected="false"
                                style="font-size: 0.9rem;">
                            <i class="fa-solid fa-user-plus me-1"></i> Create Account
                        </button>
                    </li>
                </ul>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4">
                <div class="tab-content" id="authTabContent">
                    {{-- ── TAB 1: LOGIN FORM PANEL ────────────────────────────── --}}
                    <div class="tab-pane fade show active" id="loginTabPanel" role="tabpanel" aria-labelledby="authTabLogin">
                        @if(isset($emergencyReadOnly) && $emergencyReadOnly)
                            <div class="alert alert-warning border-0 rounded-3 mb-3 small" role="alert" style="background: #fef3c7; color: #92400e;">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                <strong>Emergency Read-Only Mode:</strong> The application database is temporarily offline or locked under heavy load. Please try again shortly.
                            </div>
                        @endif

                        @if(session('status'))
                            <div class="alert alert-success alert-dismissible fade show small rounded-3 mb-3 border-0" role="alert" style="background: #dcfce7; color: #15803d;">
                                <i class="fa-solid fa-circle-check me-1"></i> {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(isset($errors) && $errors->any() && !old('is_register'))
                            <div class="alert alert-danger alert-dismissible fade show small rounded-3 mb-3 border-0" role="alert" style="background: #fee2e2; color: #b91c1c;">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" id="modalLoginForm">
                            @csrf
                            <input type="hidden" name="is_register" value="0">

                            <div class="mb-3">
                                <label for="modalLoginEmail" class="form-label small fw-semibold">Email Address / User ID</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" 
                                           class="form-control border-start-0 ps-0 {{ isset($errors) && $errors->has('email') ? 'is-invalid' : '' }}" 
                                           id="modalLoginEmail" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="e.g. juan.delacruz@clsu2.edu.ph" 
                                           required 
                                           autocomplete="username">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="modalLoginPassword" class="form-label small fw-semibold mb-0">Password</label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="small text-decoration-none text-success fw-semibold">Forgot Password?</a>
                                    @endif
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" 
                                           class="form-control border-start-0 border-end-0 ps-0 pe-0 {{ isset($errors) && $errors->has('password') ? 'is-invalid' : '' }}" 
                                           id="modalLoginPassword" 
                                           name="password" 
                                           placeholder="••••••••" 
                                           required 
                                           autocomplete="current-password">
                                    <button type="button" class="input-group-text bg-light text-muted border-start-0 toggle-password-btn" data-target="modalLoginPassword">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="remember" id="modalRemember">
                                <label class="form-check-label small text-muted" for="modalRemember">Remember this device</label>
                            </div>

                            <button type="submit" class="btn btn-success fw-bold w-100 py-2.5 rounded-pill shadow-sm text-white font-poppins" style="background-color: var(--clsu-green, #0C4E2D); border: none;">
                                <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In to Account
                            </button>
                        </form>
                    </div>

                    {{-- ── TAB 2: REGISTER FORM PANEL ─────────────────────────── --}}
                    <div class="tab-pane fade" id="registerTabPanel" role="tabpanel" aria-labelledby="authTabRegister">
                        @if(isset($errors) && $errors->any() && old('is_register'))
                            <div class="alert alert-danger alert-dismissible fade show small rounded-3 mb-3 border-0" role="alert" style="background: #fee2e2; color: #b91c1c;">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" id="modalRegisterForm">
                            @csrf
                            <input type="hidden" name="is_register" value="1">

                            <div class="mb-3">
                                <label for="modalRegName" class="form-label small fw-semibold">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" 
                                           class="form-control border-start-0 ps-0 {{ isset($errors) && $errors->has('name') ? 'is-invalid' : '' }}" 
                                           id="modalRegName" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="e.g. Maria Santos" 
                                           required 
                                           autocomplete="name">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="modalRegEmail" class="form-label small fw-semibold">CLSU Student Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" 
                                           class="form-control border-start-0 ps-0 {{ isset($errors) && $errors->has('email') ? 'is-invalid' : '' }}" 
                                           id="modalRegEmail" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="student@clsu2.edu.ph" 
                                           required 
                                           autocomplete="email">
                                </div>
                                <div class="form-text text-muted" style="font-size: 0.75rem;">Must be an official @clsu2.edu.ph or @clsu.edu.ph email address.</div>
                            </div>

                            <div class="mb-3">
                                <label for="modalRegStudentId" class="form-label small fw-semibold">Student ID Number (Optional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-id-card"></i></span>
                                    <input type="text" 
                                           class="form-control border-start-0 ps-0 {{ isset($errors) && $errors->has('student_id') ? 'is-invalid' : '' }}" 
                                           id="modalRegStudentId" 
                                           name="student_id" 
                                           value="{{ old('student_id') }}" 
                                           placeholder="e.g. 23-1234">
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label for="modalRegPassword" class="form-label small fw-semibold">Password</label>
                                    <input type="password" 
                                           class="form-control {{ isset($errors) && $errors->has('password') ? 'is-invalid' : '' }}" 
                                           id="modalRegPassword" 
                                           name="password" 
                                           placeholder="••••••••" 
                                           required 
                                           autocomplete="new-password">
                                </div>
                                <div class="col-6">
                                    <label for="modalRegPasswordConfirm" class="form-label small fw-semibold">Confirm Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="modalRegPasswordConfirm" 
                                           name="password_confirmation" 
                                           placeholder="••••••••" 
                                           required 
                                           autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="terms" id="modalTerms" required>
                                <label class="form-check-label small text-muted" for="modalTerms">
                                    I agree to CLSU OSA portal terms & privacy guidelines
                                </label>
                            </div>

                            <button type="submit" class="btn btn-warning fw-bold w-100 py-2.5 rounded-pill shadow-sm text-white font-poppins" style="background-color: var(--clsu-gold, #D97706); border: none;">
                                <i class="fa-solid fa-user-plus me-2"></i> Create Student Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #authTabHeader .nav-link {
        color: var(--text-main, #475569);
        border: none;
        transition: all 0.2s ease;
    }
    #authTabHeader .nav-link.active {
        background-color: var(--card-bg, #ffffff) !important;
        color: var(--clsu-green, #0C4E2D) !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    [data-theme="dark"] #authTabHeader .nav-link.active {
        background-color: #1e293b !important;
        color: var(--clsu-gold, #D97706) !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    document.querySelectorAll('.toggle-password-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            if (!input) return;
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Handle tab switching trigger attributes (data-auth-tab="login" or "register")
    document.querySelectorAll('[data-auth-tab]').forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            const tabName = this.dataset.auth-tab;
            if (tabName === 'register') {
                const regTabBtn = document.getElementById('authTabRegister');
                if (regTabBtn && typeof bootstrap !== 'undefined') {
                    const tab = new bootstrap.Tab(regTabBtn);
                    tab.show();
                }
            } else {
                const loginTabBtn = document.getElementById('authTabLogin');
                if (loginTabBtn && typeof bootstrap !== 'undefined') {
                    const tab = new bootstrap.Tab(loginTabBtn);
                    tab.show();
                }
            }
        });
    });

    // Auto-launch modal if validation errors exist in session or URL query parameter requests it
    const urlParams = new URLSearchParams(window.location.search);
    const showModalParam = urlParams.get('showModal');
    const hasErrors = @json(isset($errors) && $errors->any());
    const isRegister = @json(old('is_register') === '1');

    if (hasErrors || showModalParam) {
        const modalEl = document.getElementById('authModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            const authModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            
            if (isRegister || showModalParam === 'register') {
                const regTabBtn = document.getElementById('authTabRegister');
                if (regTabBtn) {
                    const tab = new bootstrap.Tab(regTabBtn);
                    tab.show();
                }
            } else {
                const loginTabBtn = document.getElementById('authTabLogin');
                if (loginTabBtn) {
                    const tab = new bootstrap.Tab(loginTabBtn);
                    tab.show();
                }
            }

            authModal.show();
        }
    }
});
</script>
