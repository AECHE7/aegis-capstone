<!-- Sidebar -->
<aside class="sidebar" id="mainSidebar" role="complementary" aria-label="Application navigation sidebar">
    <!-- Brand -->
    <a href="#" class="sidebar-brand text-decoration-none">
        <div class="sidebar-brand-icon d-flex align-items-center justify-content-center">
            <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} logo" style="width: 28px; height: 28px; object-fit: contain;" decoding="async">
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
                <i class="fa-solid fa-crown text-warning" style="min-width: 14px;" aria-hidden="true"></i>
                <span class="role-text fw-bold text-warning" style="overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</span>
            @elseif(auth()->user()->role === 'admin')
                <i class="fa-solid fa-user-shield text-info" style="min-width: 14px;" aria-hidden="true"></i>
                <span class="role-text" style="overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</span>
            @else
                <i class="fa-solid fa-user-graduate text-success" style="min-width: 14px;" aria-hidden="true"></i>
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
               {{ request()->routeIs('admin.dashboard') ? 'aria-current="page"' : '' }}
               data-tooltip="Queue">
                <span class="sidebar-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></span>
                <span class="sidebar-text">Application Queue</span>
            </a>

            <a href="{{ route('admin.announcements.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.announcements.index') ? 'active' : '' }}"
               {{ request()->routeIs('admin.announcements.index') ? 'aria-current="page"' : '' }}
               data-tooltip="Announcements">
                <span class="sidebar-icon"><i class="fa-solid fa-bullhorn text-warning" aria-hidden="true"></i></span>
                <span class="sidebar-text">Announcements</span>
            </a>

            <div class="sidebar-label mt-2">Reports</div>
            <a href="{{ route('admin.export') }}"
               class="sidebar-link"
               data-tooltip="CSV">
                <span class="sidebar-icon"><i class="fa-solid fa-file-csv text-success" aria-hidden="true"></i></span>
                <span class="sidebar-text">Export CSV</span>
            </a>
            <a href="{{ route('admin.exportPdf') }}"
               class="sidebar-link"
               data-tooltip="PDF">
                <span class="sidebar-icon"><i class="fa-solid fa-file-pdf text-danger" aria-hidden="true"></i></span>
                <span class="sidebar-text">Export PDF</span>
            </a>

            <div class="sidebar-label mt-2">Account</div>
            <a href="{{ route('profile.security') }}"
               class="sidebar-link {{ request()->routeIs('profile.security') ? 'active' : '' }}"
               {{ request()->routeIs('profile.security') ? 'aria-current="page"' : '' }}
               data-tooltip="Settings">
                <span class="sidebar-icon"><i class="fa-solid fa-user-gear text-secondary" aria-hidden="true"></i></span>
                <span class="sidebar-text">Account Settings</span>
            </a>

        @elseif(auth()->user()->role === 'superadmin')
            <div class="sidebar-label">Director</div>
            <a href="{{ route('superadmin.analytics') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.analytics') ? 'active' : '' }}"
               {{ request()->routeIs('superadmin.analytics') ? 'aria-current="page"' : '' }}
               data-tooltip="Analytics">
                <span class="sidebar-icon"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></span>
                <span class="sidebar-text">Analytics</span>
            </a>
            <a href="{{ route('superadmin.scholarships') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.scholarships') ? 'active' : '' }}"
               {{ request()->routeIs('superadmin.scholarships') ? 'aria-current="page"' : '' }}
               data-tooltip="Programs">
                <span class="sidebar-icon"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
                <span class="sidebar-text">Scholarship Programs</span>
            </a>
            <a href="{{ route('superadmin.staff') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.staff') ? 'active' : '' }}"
               {{ request()->routeIs('superadmin.staff') ? 'aria-current="page"' : '' }}
               data-tooltip="Staff">
                <span class="sidebar-icon"><i class="fa-solid fa-users-gear" aria-hidden="true"></i></span>
                <span class="sidebar-text">Staff Accounts</span>
            </a>
            <a href="{{ route('admin.announcements.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.announcements.index') ? 'active' : '' }}"
               {{ request()->routeIs('admin.announcements.index') ? 'aria-current="page"' : '' }}
               data-tooltip="Announcements">
                <span class="sidebar-icon"><i class="fa-solid fa-bullhorn text-warning" aria-hidden="true"></i></span>
                <span class="sidebar-text">Announcements</span>
            </a>
            <a href="{{ route('superadmin.broadcast') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.broadcast') ? 'active' : '' }}"
               {{ request()->routeIs('superadmin.broadcast') ? 'aria-current="page"' : '' }}
               data-tooltip="Broadcasts">
                <span class="sidebar-icon"><i class="fa-solid fa-envelope text-info" aria-hidden="true"></i></span>
                <span class="sidebar-text">Email Broadcasts</span>
            </a>
            <a href="{{ route('superadmin.trash') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.trash') ? 'active' : '' }}"
               {{ request()->routeIs('superadmin.trash') ? 'aria-current="page"' : '' }}
               data-tooltip="Trash">
                <span class="sidebar-icon"><i class="fa-solid fa-trash-can" aria-hidden="true"></i></span>
                <span class="sidebar-text">System Trash</span>
            </a>
            <a href="{{ route('superadmin.settings') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.settings') ? 'active' : '' }}"
               {{ request()->routeIs('superadmin.settings') ? 'aria-current="page"' : '' }}
               data-tooltip="Settings">
                <span class="sidebar-icon"><i class="fa-solid fa-gears text-success" aria-hidden="true"></i></span>
                <span class="sidebar-text">System Settings</span>
            </a>

            <div class="sidebar-label mt-2">Account</div>
            <a href="{{ route('profile.security') }}"
               class="sidebar-link {{ request()->routeIs('profile.security') ? 'active' : '' }}"
               {{ request()->routeIs('profile.security') ? 'aria-current="page"' : '' }}
               data-tooltip="Settings">
                <span class="sidebar-icon"><i class="fa-solid fa-user-gear text-secondary" aria-hidden="true"></i></span>
                <span class="sidebar-text">Account Settings</span>
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
               {{ request()->routeIs('student.dashboard') ? 'aria-current="page"' : '' }}
               data-tooltip="My Application">
                <span class="sidebar-icon"><i class="fa-solid fa-house" aria-hidden="true"></i></span>
                <span class="sidebar-text">My Application</span>
            </a>

            @if($isScholar && !$canRenew)
                {{-- Scholar with active grant — show disabled Apply link --}}
                <span class="sidebar-link text-muted" style="opacity:0.45; cursor:not-allowed;" data-tooltip="Already a Scholar"
                      title="You already hold an active scholarship grant."
                      aria-disabled="true">
                    <span class="sidebar-icon"><i class="fa-solid fa-award" aria-hidden="true"></i></span>
                    <span class="sidebar-text">Active Scholar</span>
                </span>
            @elseif($canRenew)
                {{-- Term ended, scholar can now renew --}}
                <a href="{{ route('student.apply', ['renew_from' => $latestApp->id]) }}"
                   class="sidebar-link {{ request()->routeIs('student.apply') ? 'active' : '' }}"
                   {{ request()->routeIs('student.apply') ? 'aria-current="page"' : '' }}
                   data-tooltip="Renew" style="color: var(--clsu-gold);">
                    <span class="sidebar-icon"><i class="fa-solid fa-rotate-right" style="color:var(--clsu-gold);" aria-hidden="true"></i></span>
                    <span class="sidebar-text">Renew Scholarship</span>
                </a>
            @else
                {{-- No active application — show normal Apply link --}}
                <a href="{{ route('student.apply') }}"
                   class="sidebar-link {{ request()->routeIs('student.apply') ? 'active' : '' }}"
                   {{ request()->routeIs('student.apply') ? 'aria-current="page"' : '' }}
                   data-tooltip="Apply">
                    <span class="sidebar-icon"><i class="fa-solid fa-plus" aria-hidden="true"></i></span>
                    <span class="sidebar-text">Apply for Scholarship</span>
                </a>
            @endif

            <a href="{{ route('profile.security') }}"
               class="sidebar-link {{ request()->routeIs('profile.security') ? 'active' : '' }}"
               {{ request()->routeIs('profile.security') ? 'aria-current="page"' : '' }}
               data-tooltip="Settings">
                <span class="sidebar-icon"><i class="fa-solid fa-user-gear" aria-hidden="true"></i></span>
                <span class="sidebar-text">Account Settings</span>
            </a>
        @endif

        @if(auth()->user()->isMaster())
            <div class="sidebar-label mt-3 text-warning" style="color:var(--clsu-gold) !important;"><i class="fa-solid fa-gears me-1"></i> Master Controls</div>
            
            <a href="{{ route('master.gateway') }}"
               class="sidebar-link {{ request()->routeIs('master.gateway') ? 'active' : '' }}"
               {{ request()->routeIs('master.gateway') ? 'aria-current="page"' : '' }}
               data-tooltip="Gateway" style="color: var(--clsu-gold) !important;">
                <span class="sidebar-icon"><i class="fa-solid fa-door-open" style="color: var(--clsu-gold) !important;" aria-hidden="true"></i></span>
                <span class="sidebar-text">Master Gateway</span>
            </a>

            <div class="px-3 py-2">
                <form action="{{ route('master.switch-role') }}" method="POST" id="masterRoleForm">
                    @csrf
                    <label for="masterRoleSelect" class="form-label small text-muted mb-1" style="font-size: 0.72rem; color: rgba(255,255,255,0.6) !important;">Switch Active Role:</label>
                    <select name="role" id="masterRoleSelect" class="form-select form-select-sm text-dark bg-white border-0" style="font-size: 0.8rem; border-radius: 8px; font-weight: 550;" onchange="document.getElementById('masterRoleForm').submit()">
                        <option value="student" {{ auth()->user()->role === 'student' ? 'selected' : '' }}>Student Portal</option>
                        <option value="admin" {{ auth()->user()->role === 'admin' ? 'selected' : '' }}>Admin Portal</option>
                        <option value="superadmin" {{ auth()->user()->role === 'superadmin' ? 'selected' : '' }}>Director Portal</option>
                    </select>
                </form>
            </div>
        @endif
    </nav>
</aside>
