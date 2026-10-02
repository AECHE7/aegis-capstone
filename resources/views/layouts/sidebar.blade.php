<!-- Sidebar -->
<aside class="sidebar" id="mainSidebar" role="complementary" aria-label="Application navigation sidebar">
    <!-- Brand -->
    <a href="#" class="sidebar-brand text-decoration-none">
        <div class="sidebar-brand-icon d-flex align-items-center justify-content-center">
            <img src="{{ \App\Models\Setting::getLogoUrl() }}"
                 alt="{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} logo"
                 width="28"
                 height="28"
                 style="width: 28px; height: 28px; object-fit: contain;"
                 decoding="async"
                 onerror="this.onerror=null; this.src='{{ asset('images/clsu-seal.png') }}';">
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

            <a href="{{ route('admin.applicant-forms.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.applicant-forms.*') ? 'active' : '' }}"
               {{ request()->routeIs('admin.applicant-forms.*') ? 'aria-current="page"' : '' }}
               data-tooltip="Forms">
                <span class="sidebar-icon"><i class="fa-solid fa-file-signature" aria-hidden="true"></i></span>
                <span class="sidebar-text">Applicant Forms</span>
            </a>

            <a href="{{ route('admin.announcements.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.announcements.index') ? 'active' : '' }}"
               {{ request()->routeIs('admin.announcements.index') ? 'aria-current="page"' : '' }}
               data-tooltip="Announcements">
                <span class="sidebar-icon"><i class="fa-solid fa-bullhorn" aria-hidden="true"></i></span>
                <span class="sidebar-text">Announcements</span>
            </a>

            <div class="sidebar-label mt-2">Reports</div>
            <a href="{{ route('admin.export') }}"
               class="sidebar-link"
               data-tooltip="CSV (CHED/DOST)">
                <span class="sidebar-icon"><i class="fa-solid fa-file-csv" aria-hidden="true"></i></span>
                <span class="sidebar-text">Export CSV (CHED/DOST)</span>
            </a>
            <a href="{{ route('admin.exportPdf') }}"
               class="sidebar-link"
               data-tooltip="PDF">
                <span class="sidebar-icon"><i class="fa-solid fa-file-pdf" aria-hidden="true"></i></span>
                <span class="sidebar-text">Export PDF</span>
            </a>

            <div class="sidebar-label mt-2">Account</div>
            <a href="{{ route('profile.security') }}"
               class="sidebar-link {{ request()->routeIs('profile.security') ? 'active' : '' }}"
               {{ request()->routeIs('profile.security') ? 'aria-current="page"' : '' }}
               data-tooltip="Settings">
                <span class="sidebar-icon"><i class="fa-solid fa-user-gear" aria-hidden="true"></i></span>
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

            <a href="{{ route('admin.applicant-forms.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.applicant-forms.*') ? 'active' : '' }}"
               {{ request()->routeIs('admin.applicant-forms.*') ? 'aria-current="page"' : '' }}
               data-tooltip="Forms">
                <span class="sidebar-icon"><i class="fa-solid fa-file-signature" aria-hidden="true"></i></span>
                <span class="sidebar-text">Applicant Forms</span>
            </a>

            <a href="{{ route('superadmin.users') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.users') || request()->routeIs('superadmin.staff') ? 'active' : '' }}"
               {{ request()->routeIs('superadmin.users') || request()->routeIs('superadmin.staff') ? 'aria-current="page"' : '' }}
               data-tooltip="Users">
                <span class="sidebar-icon"><i class="fa-solid fa-users-gear" aria-hidden="true"></i></span>
                <span class="sidebar-text">User Management</span>
            </a>
            <a href="{{ route('admin.announcements.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.announcements.index') ? 'active' : '' }}"
               {{ request()->routeIs('admin.announcements.index') ? 'aria-current="page"' : '' }}
               data-tooltip="Announcements">
                <span class="sidebar-icon"><i class="fa-solid fa-bullhorn" aria-hidden="true"></i></span>
                <span class="sidebar-text">Announcements</span>
            </a>
            <a href="{{ route('superadmin.broadcast') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.broadcast') ? 'active' : '' }}"
               {{ request()->routeIs('superadmin.broadcast') ? 'aria-current="page"' : '' }}
               data-tooltip="Broadcasts">
                <span class="sidebar-icon"><i class="fa-solid fa-envelope" aria-hidden="true"></i></span>
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
                <span class="sidebar-icon"><i class="fa-solid fa-gears" aria-hidden="true"></i></span>
                <span class="sidebar-text">System Settings</span>
            </a>

            <div class="sidebar-label mt-2">Account</div>
            <a href="{{ route('profile.security') }}"
               class="sidebar-link {{ request()->routeIs('profile.security') ? 'active' : '' }}"
               {{ request()->routeIs('profile.security') ? 'aria-current="page"' : '' }}
               data-tooltip="Settings">
                <span class="sidebar-icon"><i class="fa-solid fa-user-gear" aria-hidden="true"></i></span>
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

            <a href="{{ route('scholarships.catalog') }}"
               class="sidebar-link {{ request()->routeIs('scholarships.catalog') ? 'active' : '' }}"
               {{ request()->routeIs('scholarships.catalog') ? 'aria-current="page"' : '' }}
               data-tooltip="Scholarships">
                <span class="sidebar-icon"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i></span>
                <span class="sidebar-text">Available Scholarships</span>
            </a>

            <a href="{{ route('student.announcements') }}"
               class="sidebar-link {{ request()->routeIs('student.announcements') ? 'active' : '' }}"
               {{ request()->routeIs('student.announcements') ? 'aria-current="page"' : '' }}
               data-tooltip="Announcements">
                <span class="sidebar-icon"><i class="fa-solid fa-bullhorn" aria-hidden="true"></i></span>
                <span class="sidebar-text">Announcements</span>
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
                    data-tooltip="Renew">
                    <span class="sidebar-icon"><i class="fa-solid fa-rotate-right" aria-hidden="true"></i></span>
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

            <a href="{{ route('student.profile') }}"
               class="sidebar-link {{ request()->routeIs('student.profile') || request()->routeIs('profile.security') ? 'active' : '' }}"
               {{ request()->routeIs('student.profile') || request()->routeIs('profile.security') ? 'aria-current="page"' : '' }}
               data-tooltip="Profile & Security">
                <span class="sidebar-icon"><i class="fa-solid fa-id-card" aria-hidden="true"></i></span>
                <span class="sidebar-text">Profile & Security</span>
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

            <div class="sidebar-role-switcher px-2 py-2">
                <form action="{{ route('master.switch-role') }}" method="POST" id="masterRoleForm" class="d-none">
                    @csrf
                    <input type="hidden" name="role" id="masterRoleInput" value="">
                </form>

                <div class="dropdown">
                    <button class="sidebar-role-switch-btn w-100 d-flex align-items-center justify-content-between text-start" 
                            type="button" 
                            id="masterRoleDropdownBtn" 
                            data-bs-toggle="dropdown" 
                            data-bs-display="dynamic"
                            data-bs-popper-config='{"strategy":"fixed"}'
                            aria-expanded="false"
                            data-tooltip="Switch Portal Role">
                        <span class="d-flex align-items-center gap-2 overflow-hidden" style="min-width: 0;">
                            @if(auth()->user()->role === 'superadmin')
                                <span class="sidebar-role-icon text-warning"><i class="fa-solid fa-crown" aria-hidden="true"></i></span>
                                <span class="sidebar-text text-truncate fw-semibold" style="color: #ffffff; font-size: 0.8rem;">Director Portal</span>
                            @elseif(auth()->user()->role === 'admin')
                                <span class="sidebar-role-icon text-info"><i class="fa-solid fa-user-shield" aria-hidden="true"></i></span>
                                <span class="sidebar-text text-truncate fw-semibold" style="color: #ffffff; font-size: 0.8rem;">Admin Portal</span>
                            @else
                                <span class="sidebar-role-icon text-success"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i></span>
                                <span class="sidebar-text text-truncate fw-semibold" style="color: #ffffff; font-size: 0.8rem;">Student Portal</span>
                            @endif
                        </span>
                        <i class="fa-solid fa-chevron-down sidebar-text text-white-50" style="font-size: 0.65rem;" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark sidebar-role-dropdown-menu shadow-lg border-0 py-1.5" aria-labelledby="masterRoleDropdownBtn" 
                        style="border-radius: 14px; font-size: 0.82rem; background: #072F1B; border: 1px solid rgba(255,255,255,0.15) !important; min-width: 190px; z-index: 2100;">
                        <li><h6 class="dropdown-header text-uppercase text-warning fw-bold py-1.5 px-3" style="font-size: 0.68rem; letter-spacing: 0.5px;">Switch Active Role</h6></li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 {{ auth()->user()->role === 'student' ? 'active' : '' }}" onclick="document.getElementById('masterRoleInput').value='student'; document.getElementById('masterRoleForm').submit();">
                                <i class="fa-solid fa-graduation-cap text-success" style="width: 16px;"></i>
                                <span class="flex-grow-1">Student Portal</span>
                                @if(auth()->user()->role === 'student')<i class="fa-solid fa-check text-success ms-auto"></i>@endif
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 {{ auth()->user()->role === 'admin' ? 'active' : '' }}" onclick="document.getElementById('masterRoleInput').value='admin'; document.getElementById('masterRoleForm').submit();">
                                <i class="fa-solid fa-user-shield text-info" style="width: 16px;"></i>
                                <span class="flex-grow-1">Admin Portal</span>
                                @if(auth()->user()->role === 'admin')<i class="fa-solid fa-check text-info ms-auto"></i>@endif
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 {{ auth()->user()->role === 'superadmin' ? 'active' : '' }}" onclick="document.getElementById('masterRoleInput').value='superadmin'; document.getElementById('masterRoleForm').submit();">
                                <i class="fa-solid fa-crown text-warning" style="width: 16px;"></i>
                                <span class="flex-grow-1">Director Portal</span>
                                @if(auth()->user()->role === 'superadmin')<i class="fa-solid fa-check text-warning ms-auto"></i>@endif
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        @endif
    </nav>
</aside>
