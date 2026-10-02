@extends('layouts.app')

@section('title', 'Notifications Center | ' . \App\Models\Setting::get('app_name', 'A.E.G.I.S.'))

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

    {{-- Top Hero Header --}}
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" 
         style="border-radius: 18px; background: linear-gradient(135deg, #072314 0%, #0c4e2d 60%, #157342 100%);">
        <div class="card-body p-4 text-white position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark px-3 py-1.5 fw-bold" style="border-radius: 20px; font-size: 0.72rem; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-bell me-1"></i> COMMUNICATIONS HUB
                        </span>
                        <span class="badge text-white-50 px-2 py-1" style="background: rgba(255,255,255,0.12); border-radius: 20px; font-size: 0.72rem;">
                            Live Real-Time Sync
                        </span>
                    </div>
                    <h3 class="fw-bold text-white mb-2" style="letter-spacing: -0.02em;">
                        Notifications &amp; Alerts Center
                    </h3>
                    <p class="text-white-50 mb-0 small" style="max-width: 680px; line-height: 1.55;">
                        View, search, filter, and manage all your institutional announcements, application status updates, and university advisories. Configure delivery preferences to customize alerts.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <form method="POST" action="{{ route('notifications.test') }}" class="d-inline" id="testNotifForm">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm fw-semibold rounded-pill px-3 py-2 me-2">
                            <i class="fa-solid fa-paper-plane me-1"></i> Send Test Alert
                        </button>
                    </form>
                    <form method="POST" action="{{ route('notifications.clear') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-light btn-sm fw-bold text-success rounded-pill px-3 py-2 shadow-sm" {{ $unreadCount === 0 ? 'disabled' : '' }}>
                            <i class="fa-solid fa-check-double me-1"></i> Mark All as Read
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content: Two Columns --}}
    <div class="row g-4">
        
        {{-- Left Column: Notifications Feed & Management (8 cols) --}}
        <div class="col-lg-8">

            {{-- Filter & Search Card --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 16px;">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('notifications.index') }}" class="row g-2 align-items-center">
                        <div class="col-12 col-md-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0 ps-0" 
                                       placeholder="Search alerts by title or content..." autocomplete="off">
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <select name="category" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                                <option value="all" {{ request('category') === 'all' || !request('category') ? 'selected' : '' }}>All Categories</option>
                                <option value="application" {{ request('category') === 'application' ? 'selected' : '' }}>Applications & Reviews</option>
                                <option value="announcement" {{ request('category') === 'announcement' ? 'selected' : '' }}>Announcements</option>
                                <option value="broadcast" {{ request('category') === 'broadcast' ? 'selected' : '' }}>Broadcasts</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-3">
                            <select name="status" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                                <option value="" {{ !request('status') ? 'selected' : '' }}>All Statuses</option>
                                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread Only ({{ $unreadCount }})</option>
                                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read Only</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-1 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-success w-100 fw-bold rounded-3" title="Apply Filter">
                                <i class="fa-solid fa-filter"></i>
                            </button>
                            @if(request()->anyFilled(['q', 'category', 'status']))
                                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light border w-100 text-muted" title="Reset">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Bulk Actions Toolbar --}}
            <form id="bulkNotifForm" method="POST" action="{{ route('notifications.bulk') }}">
                @csrf
                <input type="hidden" name="action" id="bulkActionInput" value="">
                
                <div class="card border-0 shadow-sm mb-3 bg-light" style="border-radius: 12px;">
                    <div class="card-body py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                            <label class="form-check-label small fw-semibold text-muted" for="selectAllCheckbox">
                                Select All on Page
                            </label>
                        </div>
                        <div class="d-flex align-items-center gap-1.5">
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1 small" onclick="submitBulkAction('mark_read')">
                                <i class="fa-solid fa-check me-1"></i> Mark Read
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1 small" onclick="submitBulkAction('mark_unread')">
                                <i class="fa-solid fa-envelope me-1"></i> Mark Unread
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1 small" onclick="submitBulkAction('delete')">
                                <i class="fa-solid fa-trash me-1"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Notifications Feed --}}
                @if($notifications->isEmpty())
                    <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 16px;">
                        <div class="card-body">
                            <i class="fa-solid fa-bell-slash text-muted opacity-25 mb-3" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold text-dark">No notifications found</h5>
                            <p class="text-muted small mb-3">You have caught up with all your alerts or no items match your selected filters.</p>
                            <form method="POST" action="{{ route('notifications.test') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-4">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Send a Test Notification
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="d-flex flex-column gap-2 mb-4">
                        @foreach($notifications as $n)
                            @php
                                $data = is_string($n->data) ? json_decode($n->data, true) : ($n->data ?? []);
                                $isUnread = $n->read_at === null;
                                $rawType = $data['type'] ?? 'system';
                                $category = match($rawType) {
                                    'new_application', 'submission_confirmation', 'status_update' => 'application',
                                    'announcement' => 'announcement',
                                    'broadcast' => 'broadcast',
                                    default => 'system',
                                };
                                $icon = match($rawType) {
                                    'submission_confirmation' => 'fa-file-circle-check',
                                    'status_update' => 'fa-certificate',
                                    'new_application' => 'fa-user-shield',
                                    'announcement' => 'fa-bullhorn',
                                    'broadcast' => 'fa-tower-broadcast',
                                    default => 'fa-bell',
                                };
                                $iconColor = match($category) {
                                    'application' => '#0c4e2d',
                                    'announcement' => '#d97706',
                                    'broadcast' => '#7c3aed',
                                    default => '#0284c7',
                                };
                                $iconBg = match($category) {
                                    'application' => 'rgba(12, 78, 45, 0.1)',
                                    'announcement' => 'rgba(217, 119, 6, 0.1)',
                                    'broadcast' => 'rgba(124, 58, 237, 0.1)',
                                    default => 'rgba(2, 132, 199, 0.1)',
                                };

                                // Robust Relative URL sanitization and intelligent role fallbacks
                                $rawUrl = $data['url'] ?? null;
                                $targetUrl = null;
                                if (!empty($rawUrl)) {
                                    $parsed = parse_url($rawUrl);
                                    if (!empty($parsed['path'])) {
                                        $targetUrl = $parsed['path'] . (!empty($parsed['query']) ? '?' . $parsed['query'] : '');
                                    }
                                }
                                if (empty($targetUrl)) {
                                    $currentUser = auth()->user();
                                    $isStudentUser = $currentUser && $currentUser->role === 'student';
                                    $targetUrl = match($category) {
                                        'application' => !empty($data['application_id']) && !$isStudentUser
                                            ? route('admin.review', $data['application_id'], false)
                                            : ($isStudentUser ? route('student.dashboard', [], false) : route('admin.dashboard', [], false)),
                                        'announcement' => $isStudentUser
                                            ? route('student.announcements', [], false)
                                            : route('admin.announcements.index', [], false),
                                        'broadcast' => route('notifications.index', [], false),
                                        default => route('notifications.index', [], false),
                                    };
                                }
                            @endphp
                            <div class="card border-0 shadow-sm transition-all notification-card {{ $isUnread ? 'unread-card' : '' }}" 
                                 style="border-radius: 14px; background: {{ $isUnread ? 'rgba(12, 78, 45, 0.03)' : '#ffffff' }}; border-left: 4px solid {{ $isUnread ? 'var(--clsu-green)' : 'transparent' }} !important;">
                                <div class="card-body p-3 d-flex align-items-start gap-3">
                                    {{-- Checkbox --}}
                                    <div class="form-check mt-1">
                                        <input class="form-check-input notif-checkbox" type="checkbox" name="ids[]" value="{{ $n->id }}">
                                    </div>

                                    {{-- Category Icon Avatar --}}
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                                         style="width: 40px; height: 40px; background: {{ $iconBg }}; color: {{ $iconColor }};">
                                        <i class="fa-solid {{ $icon }}" style="font-size: 0.95rem;"></i>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                <h6 class="fw-bold mb-0 text-truncate text-dark" style="font-size: 0.88rem;">
                                                    {{ $data['title'] ?? 'System Alert' }}
                                                </h6>
                                                @if($isUnread)
                                                    <span class="badge bg-success" style="font-size: 0.6rem; padding: 2px 6px; border-radius: 10px;">NEW</span>
                                                @endif
                                            </div>
                                            <span class="text-muted small flex-shrink-0" style="font-size: 0.72rem;" title="{{ $n->created_at ? $n->created_at->format('M d, Y h:i A') : '' }}">
                                                {{ $n->created_at ? $n->created_at->diffForHumans() : 'Recently' }}
                                            </span>
                                        </div>

                                        <p class="text-muted mb-2 small" style="line-height: 1.45; font-size: 0.8rem;">
                                            {{ $data['message'] ?? '' }}
                                        </p>

                                        {{-- Actions on Notification --}}
                                        <div class="d-flex align-items-center gap-3">
                                            <a href="{{ $targetUrl }}" 
                                               class="btn btn-sm btn-outline-success rounded-pill px-3 py-0.5 fw-semibold" 
                                               style="font-size: 0.72rem;"
                                               onclick="event.preventDefault(); openNotification('{{ $n->id }}', '{{ $targetUrl }}');">
                                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open & View
                                            </a>

                                            @if($isUnread)
                                                <button type="button" class="btn btn-link text-decoration-none p-0 text-muted small" 
                                                        style="font-size: 0.72rem;" onclick="quickMarkRead('{{ $n->id }}', true)">
                                                    <i class="fa-solid fa-check me-1"></i> Mark as Read
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-link text-decoration-none p-0 text-muted small" 
                                                        style="font-size: 0.72rem;" onclick="quickMarkUnread('{{ $n->id }}')">
                                                    <i class="fa-solid fa-envelope me-1"></i> Mark as Unread
                                                </button>
                                            @endif

                                            <button type="button" class="btn btn-link text-decoration-none p-0 text-danger opacity-75 small" 
                                                    style="font-size: 0.72rem;" onclick="quickDelete('{{ $n->id }}')">
                                                <i class="fa-solid fa-trash-can me-1"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination (Bootstrap 5 template to avoid Tailwind unstyled full-width SVG arrows) --}}
                    <div class="d-flex justify-content-center mt-3">
                        {{ $notifications->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </form>
        </div>

        {{-- Right Column: Delivery Preferences & Settings (4 cols) --}}
        <div class="col-lg-4">

            {{-- Preferences Card --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-3.5 pb-2 px-3.5">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-sliders text-success me-2"></i> Delivery Preferences
                    </h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Customize what notifications you receive</small>
                </div>
                <div class="card-body p-3.5 pt-0">
                    <form method="POST" action="{{ route('notifications.preferences') }}">
                        @csrf

                        <div class="d-flex flex-column gap-3 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="d-block text-dark small">In-App Portal Alerts</strong>
                                    <small class="text-muted" style="font-size: 0.7rem;">Show alerts in the topbar bell dropdown</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="in_app" value="1" 
                                           {{ ($preferences['in_app'] ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>

                            <hr class="my-1 opacity-10">

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="d-block text-dark small">Applications & Reviews</strong>
                                    <small class="text-muted" style="font-size: 0.7rem;">Status updates, review assignments, and submissions</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="applications" value="1" 
                                           {{ ($preferences['applications'] ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>

                            <hr class="my-1 opacity-10">

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="d-block text-dark small">Campus Announcements</strong>
                                    <small class="text-muted" style="font-size: 0.7rem;">Deadlines, scholarship openings, and advisories</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="announcements" value="1" 
                                           {{ ($preferences['announcements'] ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>

                            <hr class="my-1 opacity-10">

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="d-block text-dark small">Executive Broadcasts</strong>
                                    <small class="text-muted" style="font-size: 0.7rem;">University-wide urgent messages from the Director</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="broadcasts" value="1" 
                                           {{ ($preferences['broadcasts'] ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>

                            <hr class="my-1 opacity-10">

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="d-block text-dark small">Email Delivery</strong>
                                    <small class="text-muted" style="font-size: 0.7rem;">Receive email copy for critical approvals</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="email" value="1" 
                                           {{ ($preferences['email'] ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold rounded-pill py-2 mt-3 text-white" 
                                style="background: #0c4e2d; border-color: #0c4e2d; font-size: 0.82rem;">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Preferences
                        </button>
                    </form>
                </div>
            </div>

            {{-- System Status Card --}}
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #f8fafc;">
                <div class="card-body p-3.5">
                    <h6 class="fw-bold mb-2 text-dark small">
                        <i class="fa-solid fa-shield-halved text-primary me-1.5"></i> Notification Engine SLA
                    </h6>
                    <ul class="list-unstyled mb-0 small text-muted d-flex flex-column gap-1.5" style="font-size: 0.75rem;">
                        <li><i class="fa-solid fa-circle-check text-success me-1.5"></i> Real-time DB persistence with SHA-256 audit logging.</li>
                        <li><i class="fa-solid fa-circle-check text-success me-1.5"></i> Asynchronous 20s live sync across all tabs.</li>
                        <li><i class="fa-solid fa-circle-check text-success me-1.5"></i> Direct action routing with 1-click execution.</li>
                    </ul>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
function toggleSelectAll(masterCheckbox) {
    const checkboxes = document.querySelectorAll('.notif-checkbox');
    checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
}

function submitBulkAction(action) {
    const selected = document.querySelectorAll('.notif-checkbox:checked');
    if (selected.length === 0) {
        if (typeof AegisAlert !== 'undefined') {
            AegisAlert.toast({ icon: 'warning', title: 'Please select at least one notification.' });
        }
        return;
    }

    if (action === 'delete') {
        const count = selected.length;
        if (typeof AegisAlert !== 'undefined') {
            AegisAlert.delete({
                title: 'Delete Selected Notifications?',
                text: `Are you sure you want to permanently delete ${count} selected notification${count > 1 ? 's' : ''}? This action cannot be undone.`,
                confirmText: 'Yes, Delete Selected'
            }).then(confirmed => {
                if (confirmed) {
                    document.getElementById('bulkActionInput').value = action;
                    document.getElementById('bulkNotifForm').submit();
                }
            });
            return;
        }
    }

    document.getElementById('bulkActionInput').value = action;
    document.getElementById('bulkNotifForm').submit();
}

function quickMarkRead(id, reload = false) {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    }).then(res => res.json()).then(data => {
        if (reload) window.location.reload();
    });
}

function quickMarkUnread(id) {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch(`/notifications/${id}/unread`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    }).then(res => res.json()).then(data => {
        window.location.reload();
    });
}

function quickDelete(id) {
    if (typeof AegisAlert !== 'undefined') {
        AegisAlert.delete({
            title: 'Delete Notification?',
            text: 'Are you sure you want to permanently delete this notification? This action cannot be undone.',
            confirmText: 'Yes, Delete'
        }).then(confirmed => {
            if (!confirmed) return;
            executeDelete(id);
        });
    } else {
        executeDelete(id);
    }
}

function executeDelete(id) {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    }).then(res => res.json()).then(data => {
        window.location.reload();
    });
}

function openNotification(id, targetUrl) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (id && csrf) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            keepalive: true
        }).catch(() => {});
    }
    if (targetUrl) {
        window.location.href = targetUrl;
    }
}
</script>

<style>
.notification-card {
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.notification-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
}

/* Pagination sizing & aesthetics to prevent oversized arrows */
.pagination {
    margin-bottom: 0;
    gap: 4px;
    align-items: center;
}
.pagination svg {
    width: 14px !important;
    height: 14px !important;
    max-width: 14px !important;
    max-height: 14px !important;
    display: inline-block !important;
    vertical-align: middle !important;
}
.pagination .page-link {
    color: var(--clsu-green, #0c4e2d);
    border-radius: 8px !important;
    padding: 6px 12px;
    font-size: 0.82rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.pagination .page-item.active .page-link {
    background-color: var(--clsu-green, #0c4e2d);
    border-color: var(--clsu-green, #0c4e2d);
    color: #ffffff;
}
</style>
@endsection
