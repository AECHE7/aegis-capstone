{{-- Spotlight Command Palette Component (Ctrl+K) --}}
<div id="commandPalette" class="command-palette-backdrop d-none" tabindex="-1">
    <div class="command-palette-dialog" role="dialog" aria-modal="true" aria-label="Quick Command Search">
        <div class="p-3 border-bottom d-flex align-items-center gap-2">
            <i class="fa-solid fa-magnifying-glass text-muted ms-2"></i>
            <input type="text" id="commandPaletteInput" class="command-palette-input" placeholder="Type a command, student name, ID number, or screen..." autocomplete="off">
            <span class="badge bg-secondary text-white px-2 py-1 me-2 font-monospace" style="font-size: 0.75rem;">ESC</span>
        </div>
        <div id="commandPaletteResults" class="py-2 overflow-y-auto" style="max-height: 380px;">
            <div class="px-3 py-2 text-muted small fw-semibold text-uppercase tracking-wider">Quick Navigation</div>
            @if(auth()->check())
                @if(auth()->user()->role === 'superadmin')
                    <a href="{{ route('superadmin.analytics') }}" class="command-palette-item">
                        <span><i class="fa-solid fa-chart-line text-success me-2"></i> Operational Analytics & Logs Hub</span>
                        <span class="badge bg-light text-dark border">Dashboard</span>
                    </a>
                    <a href="{{ route('superadmin.scholarships') }}" class="command-palette-item">
                        <span><i class="fa-solid fa-graduation-cap text-warning me-2"></i> Scholarship Program Builder</span>
                        <span class="badge bg-light text-dark border">Programs</span>
                    </a>
                    <a href="{{ route('superadmin.staff') }}" class="command-palette-item">
                        <span><i class="fa-solid fa-users-gear text-info me-2"></i> Staff Invitation & Access Management</span>
                        <span class="badge bg-light text-dark border">Staff</span>
                    </a>
                    <a href="{{ route('superadmin.settings') }}" class="command-palette-item">
                        <span><i class="fa-solid fa-sliders text-danger me-2"></i> System & AI Verification Settings</span>
                        <span class="badge bg-light text-dark border">Settings</span>
                    </a>
                @elseif(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="command-palette-item">
                        <span><i class="fa-solid fa-folder-open text-primary me-2"></i> Application Evaluation Queue</span>
                        <span class="badge bg-light text-dark border">Queue</span>
                    </a>
                @else
                    <a href="{{ route('student.dashboard') }}" class="command-palette-item">
                        <span><i class="fa-solid fa-gauge-high text-success me-2"></i> Student Portal Dashboard</span>
                        <span class="badge bg-light text-dark border">Portal</span>
                    </a>
                    <a href="{{ route('student.apply') }}" class="command-palette-item">
                        <span><i class="fa-solid fa-paper-plane text-warning me-2"></i> Apply for Scholarship</span>
                        <span class="badge bg-light text-dark border">Apply</span>
                    </a>
                    <a href="{{ route('student.profile') }}" class="command-palette-item">
                        <span><i class="fa-solid fa-id-card text-info me-2"></i> Profile & Academic Info</span>
                        <span class="badge bg-light text-dark border">Profile</span>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="command-palette-item">
                    <span><i class="fa-solid fa-right-to-bracket text-primary me-2"></i> Sign In to Portal</span>
                    <span class="badge bg-light text-dark border">Auth</span>
                </a>
            @endif

            <div class="px-3 py-2 text-muted small fw-semibold text-uppercase tracking-wider mt-2">Actions & Preferences</div>
            <button type="button" class="command-palette-item w-100 text-start border-0 bg-transparent" id="cmdToggleTheme">
                <span><i class="fa-solid fa-moon text-primary me-2"></i> Toggle Dark / Light Color Theme</span>
                <span class="badge bg-light text-dark border">Theme</span>
            </button>
            @if(auth()->check() && auth()->user()->role !== 'student')
            <a href="{{ route('admin.announcements.index') }}" class="command-palette-item">
                <span><i class="fa-solid fa-bullhorn text-warning me-2"></i> System Announcements</span>
                <span class="badge bg-light text-dark border">Bulletins</span>
            </a>
            @endif
        </div>
        <div class="p-2 border-top bg-light d-flex justify-content-between align-items-center text-muted small px-3">
            <span>Use <kbd class="bg-white text-dark border px-1">↑</kbd> <kbd class="bg-white text-dark border px-1">↓</kbd> to navigate</span>
            <span>Press <kbd class="bg-white text-dark border px-1">ESC</kbd> to exit</span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const palette = document.getElementById('commandPalette');
    const input = document.getElementById('commandPaletteInput');
    const results = document.getElementById('commandPaletteResults');
    const themeBtn = document.getElementById('cmdToggleTheme');
    let selectedIndex = -1;

    if (!palette || !input) return;

    function getVisibleItems() {
        return Array.from(results.querySelectorAll('.command-palette-item')).filter(el => !el.classList.contains('d-none'));
    }

    function updateHighlight(items) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('active');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('active');
            }
        });
    }

    function openPalette() {
        palette.classList.remove('d-none');
        input.focus();
        input.value = '';
        filterItems('');
    }

    function closePalette() {
        palette.classList.add('d-none');
        selectedIndex = -1;
    }

    // Keyboard shortcut listener (Ctrl+K / Cmd+K) & Arrow keys navigation
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            const active = document.activeElement;
            const isInput = active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA' || active.isContentEditable);
            
            if (!isInput || active === input) {
                e.preventDefault();
                if (palette.classList.contains('d-none')) {
                    openPalette();
                } else {
                    closePalette();
                }
            }
        } else if (!palette.classList.contains('d-none')) {
            const visibleItems = getVisibleItems();

            if (e.key === 'Escape') {
                closePalette();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (visibleItems.length > 0) {
                    selectedIndex = (selectedIndex + 1) % visibleItems.length;
                    updateHighlight(visibleItems);
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (visibleItems.length > 0) {
                    selectedIndex = (selectedIndex - 1 + visibleItems.length) % visibleItems.length;
                    updateHighlight(visibleItems);
                }
            } else if (e.key === 'Enter' && selectedIndex >= 0 && selectedIndex < visibleItems.length) {
                e.preventDefault();
                visibleItems[selectedIndex].click();
            }
        }
    });

    palette.addEventListener('click', function(e) {
        if (e.target === palette) closePalette();
    });

    if (themeBtn) {
        themeBtn.addEventListener('click', function() {
            if (typeof toggleTheme === 'function') {
                toggleTheme();
            } else {
                const current = document.documentElement.getAttribute('data-theme') || 'light';
                const next = current === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', next);
                localStorage.setItem('aegis-theme', next);
            }
            closePalette();
        });
    }

    function filterItems(query) {
        const q = query.toLowerCase();
        const items = results.querySelectorAll('.command-palette-item');
        items.forEach(item => {
            const text = item.innerText.toLowerCase();
            if (text.includes(q)) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });
        selectedIndex = -1;
    }

    input.addEventListener('input', function() {
        filterItems(this.value);
    });

    window.openAegisCommandPalette = openPalette;
});
</script>
