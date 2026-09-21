@auth
<div class="mobile-bottom-nav d-md-none" role="navigation" aria-label="Mobile Bottom Navigation">
    @if(auth()->user()->role === 'student')
        <a href="{{ route('student.dashboard') }}" 
           class="mobile-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>

        <a href="{{ route('student.apply') }}" 
           class="mobile-nav-item {{ request()->routeIs('student.apply') ? 'active' : '' }}">
            <div class="nav-fab-wrapper">
                <i class="fa-solid fa-plus-circle"></i>
            </div>
            <span>Apply</span>
        </a>

        <a href="{{ Route::has('student.announcements') ? route('student.announcements') : (Route::has('admin.announcements.index') ? route('admin.announcements.index') : '#') }}" 
           class="mobile-nav-item {{ request()->routeIs('student.announcements') || request()->routeIs('admin.announcements.index') ? 'active' : '' }}">
            <i class="fa-solid fa-bullhorn"></i>
            <span>News</span>
        </a>

        <a href="{{ route('student.profile') }}" 
           class="mobile-nav-item {{ request()->routeIs('student.profile') ? 'active' : '' }}">
            <i class="fa-solid fa-id-card"></i>
            <span>Profile</span>
        </a>

        <a href="{{ route('profile.security') }}" 
           class="mobile-nav-item {{ request()->routeIs('profile.security') ? 'active' : '' }}">
            <i class="fa-solid fa-gear"></i>
            <span>Settings</span>
        </a>
    @elseif(auth()->user()->role === 'admin')
        <a href="{{ route('admin.dashboard') }}" 
           class="mobile-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-layer-group"></i>
            <span>Queue</span>
        </a>

        <a href="{{ route('admin.announcements.index') }}" 
           class="mobile-nav-item {{ request()->routeIs('admin.announcements.index') ? 'active' : '' }}">
            <i class="fa-solid fa-bullhorn"></i>
            <span>News</span>
        </a>

        <a href="{{ route('admin.export') }}" 
           class="mobile-nav-item {{ request()->routeIs('admin.export') ? 'active' : '' }}">
            <i class="fa-solid fa-file-export"></i>
            <span>Export</span>
        </a>

        <a href="{{ route('profile.security') }}" 
           class="mobile-nav-item {{ request()->routeIs('profile.security') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear"></i>
            <span>Account</span>
        </a>
    @elseif(auth()->user()->role === 'superadmin')
        <a href="{{ route('superadmin.analytics') }}" 
           class="mobile-nav-item {{ request()->routeIs('superadmin.analytics') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i>
            <span>Analytics</span>
        </a>

        <a href="{{ route('superadmin.scholarships') }}" 
           class="mobile-nav-item {{ request()->routeIs('superadmin.scholarships') ? 'active' : '' }}">
            <i class="fa-solid fa-list-check"></i>
            <span>Grants</span>
        </a>

        <a href="{{ Route::has('superadmin.users') ? route('superadmin.users') : route('superadmin.staff') }}" 
           class="mobile-nav-item {{ request()->routeIs('superadmin.users') || request()->routeIs('superadmin.staff') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i>
            <span>Users</span>
        </a>

        <a href="{{ route('superadmin.settings') }}" 
           class="mobile-nav-item {{ request()->routeIs('superadmin.settings') ? 'active' : '' }}">
            <i class="fa-solid fa-gears"></i>
            <span>Settings</span>
        </a>
    @endif
</div>

<style>
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 64px;
        background: #ffffff;
        border-top: 1px solid var(--border-color, #edebe9);
        display: flex;
        align-items: center;
        justify-content: space-around;
        z-index: 1040;
        box-shadow: 0 -4px 16px rgba(0,0,0,0.06);
        padding-bottom: max(0px, env(safe-area-inset-bottom));
    }

    [data-theme="dark"] .mobile-bottom-nav {
        background: #111827;
        border-top-color: rgba(255,255,255,0.08);
        box-shadow: 0 -4px 16px rgba(0,0,0,0.3);
    }

    .mobile-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
        color: #64748b;
        text-decoration: none;
        font-size: 0.7rem;
        font-weight: 600;
        flex: 1;
        height: 100%;
        transition: color 0.15s ease, transform 0.15s ease;
        -webkit-tap-highlight-color: transparent;
    }

    [data-theme="dark"] .mobile-nav-item {
        color: #94a3b8;
    }

    .mobile-nav-item i {
        font-size: 1.15rem;
    }

    .mobile-nav-item.active {
        color: var(--clsu-green, #0C4E2D);
        font-weight: 700;
    }

    [data-theme="dark"] .mobile-nav-item.active {
        color: #4ade80;
    }

    .mobile-nav-item:active {
        transform: scale(0.92);
    }

    .nav-fab-wrapper i {
        font-size: 1.35rem;
        color: var(--clsu-gold, #D97706);
    }

    /* Support 5-item mobile nav bar (student role) */
    .mobile-bottom-nav:has(.mobile-nav-item:nth-child(5)) {
        justify-content: space-evenly;
    }
    .mobile-bottom-nav:has(.mobile-nav-item:nth-child(5)) .mobile-nav-item {
        font-size: 0.63rem;
    }
    .mobile-bottom-nav:has(.mobile-nav-item:nth-child(5)) .mobile-nav-item i {
        font-size: 1.05rem;
    }

    /* Body & page-content padding buffer so content is never hidden behind fixed bottom bar on mobile */
    @media (max-width: 767.98px) {
        body {
            padding-bottom: calc(72px + env(safe-area-inset-bottom, 16px)) !important;
        }
        .page-content {
            padding-bottom: calc(88px + env(safe-area-inset-bottom, 16px)) !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var navItems = document.querySelectorAll('.mobile-nav-item');
        navItems.forEach(function(item) {
            item.addEventListener('click', function() {
                if ('vibrate' in navigator) {
                    try { navigator.vibrate(12); } catch(e) {}
                }
            });
        });
    });
</script>
@endauth
