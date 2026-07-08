<!-- Sidebar -->
<aside class="sidebar" id="mainSidebar" role="complementary" aria-label="Application navigation sidebar">
    <!-- Brand -->
    <a href="#" class="sidebar-brand text-decoration-none">
        <div class="sidebar-brand-icon d-flex align-items-center justify-content-center">
            @if(\App\Models\Setting::get('app_logo'))
                <img src="{{ route('system.logo') }}" style="width: 24px; height: 24px; object-fit: contain;">
            @else
                <img src="{{ asset('logo.png') }}" style="width: 24px; height: 24px; object-fit: contain;">
            @endif
        </div>
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }}</span>
            <span class="sidebar-brand-sub">OSA Portal</span>
        </div>
    </a>

    <!-- Role Badge -->
    <div class="sidebar-role">
        <div class="sidebar-role-badge">
            @if(auth()->user()->role === 'superadmin')
                <i class="fa-solid fa-crown text-warning" style="min-width: 14px;"></i>
                <span class="role-text fw-bold text-warning" style="overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</span>
            @elseif(auth()->user()->role === 'admin')
                <i class="fa-solid fa-user-shield text-info" style="min-width: 14px;"></i>
                <span class="role-text" style="overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</span>
            @else
                <i class="fa-solid fa-user-graduate text-success" style="min-width: 14px;"></i>
                <span class="role-text text-success" style="overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</span>
            @endif
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav" aria-label="Main navigation">
        @if(auth()->user()->role === 'admin')
            <div class="sidebar-label">Main Menu</div>
            <a href="{{ route('admin.dashboard') }}" 
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               data-tooltip="Queue">
                <span class="sidebar-icon"><i class="fa-solid fa-layer-group"></i></span>
                <span class="sidebar-text">Application Queue</span>
            </a>

            <a href="{{ route('admin.announcements.index') }}" 
               class="sidebar-link {{ request()->routeIs('admin.announcements.index') ? 'active' : '' }}"
               data-tooltip="Announcements">
                <span class="sidebar-icon"><i class="fa-solid fa-bullhorn text-warning"></i></span>
                <span class="sidebar-text">Announcements</span>
            </a>

            <div class="sidebar-label mt-2">Reports</div>
            <a href="{{ route('admin.export') }}" 
               class="sidebar-link"
               data-tooltip="CSV">
                <span class="sidebar-icon"><i class="fa-solid fa-file-csv text-success"></i></span>
                <span class="sidebar-text">Export CSV</span>
            </a>
            <a href="{{ route('admin.exportPdf') }}" 
               class="sidebar-link"
               data-tooltip="PDF">
                <span class="sidebar-icon"><i class="fa-solid fa-file-pdf text-danger"></i></span>
                <span class="sidebar-text">Export PDF</span>
            </a>

            <div class="sidebar-label mt-2">Account</div>
            <a href="{{ route('profile.security') }}" 
               class="sidebar-link {{ request()->routeIs('profile.security') ? 'active' : '' }}"
               data-tooltip="Security">
                <span class="sidebar-icon"><i class="fa-solid fa-key text-secondary"></i></span>
                <span class="sidebar-text">Change Password</span>
            </a>

        @elseif(auth()->user()->role === 'superadmin')
            <div class="sidebar-label">Director</div>
            <a href="{{ route('superadmin.analytics') }}" 
               class="sidebar-link {{ request()->routeIs('superadmin.analytics') ? 'active' : '' }}"
               data-tooltip="Analytics">
                <span class="sidebar-icon"><i class="fa-solid fa-chart-line"></i></span>
                <span class="sidebar-text">Analytics</span>
            </a>
            <a href="{{ route('superadmin.scholarships') }}" 
               class="sidebar-link {{ request()->routeIs('superadmin.scholarships') ? 'active' : '' }}"
               data-tooltip="Programs">
                <span class="sidebar-icon"><i class="fa-solid fa-list-check"></i></span>
                <span class="sidebar-text">Scholarship Programs</span>
            </a>
            <a href="{{ route('superadmin.staff') }}" 
               class="sidebar-link {{ request()->routeIs('superadmin.staff') ? 'active' : '' }}"
               data-tooltip="Staff">
                <span class="sidebar-icon"><i class="fa-solid fa-users-gear"></i></span>
                <span class="sidebar-text">Staff Accounts</span>
            </a>
            <a href="{{ route('admin.announcements.index') }}" 
               class="sidebar-link {{ request()->routeIs('admin.announcements.index') ? 'active' : '' }}"
               data-tooltip="Announcements">
                <span class="sidebar-icon"><i class="fa-solid fa-bullhorn text-warning"></i></span>
                <span class="sidebar-text">Announcements</span>
            </a>
            <a href="{{ route('superadmin.broadcast') }}" 
               class="sidebar-link {{ request()->routeIs('superadmin.broadcast') ? 'active' : '' }}"
               data-tooltip="Broadcasts">
                <span class="sidebar-icon"><i class="fa-solid fa-envelope text-info"></i></span>
                <span class="sidebar-text">Email Broadcasts</span>
            </a>
            <a href="{{ route('superadmin.trash') }}" 
               class="sidebar-link {{ request()->routeIs('superadmin.trash') ? 'active' : '' }}"
               data-tooltip="Trash">
                <span class="sidebar-icon"><i class="fa-solid fa-trash-can"></i></span>
                <span class="sidebar-text">System Trash</span>
            </a>
            <a href="{{ route('superadmin.settings') }}" 
               class="sidebar-link {{ request()->routeIs('superadmin.settings') ? 'active' : '' }}"
               data-tooltip="Settings">
                <span class="sidebar-icon"><i class="fa-solid fa-gears text-success"></i></span>
                <span class="sidebar-text">System Settings</span>
            </a>

            <div class="sidebar-label mt-2">Account</div>
            <a href="{{ route('profile.security') }}" 
               class="sidebar-link {{ request()->routeIs('profile.security') ? 'active' : '' }}"
               data-tooltip="Security">
                <span class="sidebar-icon"><i class="fa-solid fa-key text-secondary"></i></span>
                <span class="sidebar-text">Change Password</span>
            </a>

        @elseif(auth()->user()->role === 'student')
            @php
                $latestApp = auth()->user()->applications()->latest()->first();
                $isScholar = $latestApp && $latestApp->status === 'Approved';
                $canRenew  = $isScholar && !auth()->user()->hasActiveApplication();
            @endphp
            <div class="sidebar-label">Student Menu</div>
            <a href="{{ route('student.dashboard') }}" 
               class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"
               data-tooltip="My Application">
                <span class="sidebar-icon"><i class="fa-solid fa-house"></i></span>
                <span class="sidebar-text">My Application</span>
            </a>

            @if($isScholar && !$canRenew)
                {{-- Scholar with active grant — show disabled Apply link --}}
                <span class="sidebar-link text-muted" style="opacity:0.45; cursor:not-allowed;" data-tooltip="Already a Scholar"
                      title="You already hold an active scholarship grant.">
                    <span class="sidebar-icon"><i class="fa-solid fa-award"></i></span>
                    <span class="sidebar-text">Active Scholar</span>
                </span>
            @elseif($canRenew)
                {{-- Term ended, scholar can now renew --}}
                <a href="{{ route('student.apply', ['renew_from' => $latestApp->id]) }}" 
                   class="sidebar-link {{ request()->routeIs('student.apply') ? 'active' : '' }}"
                   data-tooltip="Renew" style="color: var(--clsu-gold);">
                    <span class="sidebar-icon"><i class="fa-solid fa-rotate-right" style="color:var(--clsu-gold);"></i></span>
                    <span class="sidebar-text">Renew Scholarship</span>
                </a>
            @else
                {{-- No active application — show normal Apply link --}}
                <a href="{{ route('student.apply') }}" 
                   class="sidebar-link {{ request()->routeIs('student.apply') ? 'active' : '' }}"
                   data-tooltip="Apply">
                    <span class="sidebar-icon"><i class="fa-solid fa-plus"></i></span>
                    <span class="sidebar-text">Apply for Scholarship</span>
                </a>
            @endif

            <a href="{{ route('student.profile') }}" 
               class="sidebar-link {{ request()->routeIs('student.profile') ? 'active' : '' }}"
               data-tooltip="Profile">
                <span class="sidebar-icon"><i class="fa-solid fa-user"></i></span>
                <span class="sidebar-text">My Profile</span>
            </a>
        @endif
    </nav>

    <!-- Logout -->
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST" id="logoutForm">
            @csrf
            <button type="button" class="sidebar-logout-btn w-100 border-0 bg-transparent text-start" id="logoutLink">
                <i class="fa-solid fa-right-from-bracket" style="min-width: 16px;"></i>
                <span class="sidebar-text ms-2">Logout</span>
            </button>
        </form>
    </div>
</aside>

<script>
    (function () {
        var logoutLink = document.getElementById('logoutLink');
        var logoutForm = document.getElementById('logoutForm');
        if (!logoutLink || !logoutForm) return;

        logoutLink.addEventListener('click', function () {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Confirm Logout',
                    text: 'Are you sure you want to sign out?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0C4E2D',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Sign Out',
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        logoutForm.submit();
                    }
                });
            } else {
                // Fallback if SweetAlert2 CDN failed to load
                if (confirm('Are you sure you want to sign out?')) {
                    logoutForm.submit();
                }
            }
        });
    })();
</script>
