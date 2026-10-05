@extends('layouts.app')

@section('title', 'Communications & Broadcast Center | A.E.G.I.S.')
@section('page-title', 'Communications & Broadcast Hub')
@section('page-subtitle', 'Unified portal announcements, scheduling, and targeted institutional email broadcasts')

@push('styles')
<style>
    /* Metric stats cards */
    .comm-stat {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        box-shadow: var(--shadow-card);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s;
    }
    .comm-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(7, 51, 28, 0.08);
    }
    .comm-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .comm-stat-num {
        font-family: 'Poppins', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1.1;
        color: var(--text-title);
    }
    .comm-stat-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-muted);
        margin-bottom: 2px;
    }

    /* Tab Switcher Navigation */
    .nav-tabs-comm {
        border-bottom: 2px solid var(--border-color);
        gap: 0.5rem;
    }
    .nav-tabs-comm .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        background: transparent;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.88rem;
        padding: 0.75rem 1.25rem;
        border-radius: 0;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }
    .nav-tabs-comm .nav-link:hover {
        color: var(--clsu-green);
        border-bottom-color: rgba(7, 51, 28, 0.25);
    }
    .nav-tabs-comm .nav-link.active {
        color: var(--clsu-green);
        border-bottom-color: var(--clsu-green);
        font-weight: 700;
    }

    /* Custom form styling */
    .comm-card {
        border: 1px solid var(--border-color);
        border-radius: 16px;
        background: var(--card-bg);
        box-shadow: var(--shadow-card);
    }

    /* Quick Preset chips */
    .template-chip {
        background: var(--clsu-bg);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        font-size: 0.72rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .template-chip:hover {
        background: var(--clsu-green-muted);
        color: var(--clsu-green);
        border-color: var(--clsu-green);
    }
</style>
@endpush

@section('content')

{{-- Success / Error Flash Alerts --}}
@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 py-3" style="background-color: #dcfce7; color: #14532d; border-radius: 14px;">
        <i class="fa-solid fa-circle-check fs-5 me-2 flex-shrink-0"></i>
        <div class="fw-medium small flex-grow-1">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4 py-3" style="border-radius: 14px;">
        <i class="fa-solid fa-circle-exclamation fs-5 me-2 flex-shrink-0"></i>
        <div class="fw-medium small flex-grow-1">{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Top Executive Metrics Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="comm-stat h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="comm-stat-label">Portal Notices</div>
                <div class="comm-stat-num">{{ $announcements->total() }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">Feed &amp; dashboard alerts</div>
            </div>
            <div class="comm-stat-icon" style="background: rgba(0, 117, 74, 0.1); color: var(--clsu-green);">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="comm-stat h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="comm-stat-label">Broadcasts Dispatched</div>
                <div class="comm-stat-num">{{ number_format($totalBroadcasts) }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">Mass email notifications</div>
            </div>
            <div class="comm-stat-icon" style="background: rgba(14, 165, 233, 0.1); color: #0284c7;">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="comm-stat h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="comm-stat-label">Unique Recipients</div>
                <div class="comm-stat-num">{{ number_format($uniqueRecipients) }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">Scholars &amp; student inboxes</div>
            </div>
            <div class="comm-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 d-flex flex-column gap-2 justify-content-center">
        <button class="btn btn-success fw-bold py-2 w-100 text-nowrap shadow-sm" 
                style="background: linear-gradient(135deg, #07331c, #0F5934); border-radius: 12px; font-size: 0.82rem;"
                data-bs-toggle="modal" data-bs-target="#newAnnouncementModal">
            <i class="fa-solid fa-plus me-1"></i> Publish Announcement
        </button>
        <button type="button" class="btn btn-outline-success fw-semibold py-2 w-100 text-nowrap" 
                style="border-radius: 12px; font-size: 0.82rem;"
                onclick="switchToTab('broadcast')">
            <i class="fa-solid fa-paper-plane me-1"></i> Compose Email Broadcast
        </button>
    </div>
</div>

{{-- Unified Navigation Tabs --}}
<div class="d-flex justify-content-between align-items-center border-bottom mb-4">
    <ul class="nav nav-tabs-comm" id="commTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'announcements' ? 'active' : '' }}" 
                    id="announcements-tab" data-bs-toggle="tab" data-bs-target="#tab-announcements" 
                    type="button" role="tab" onclick="setTabQuery('announcements')">
                <i class="fa-solid fa-bullhorn"></i> Portal Announcements ({{ $announcements->total() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'broadcast' ? 'active' : '' }}" 
                    id="broadcast-tab" data-bs-toggle="tab" data-bs-target="#tab-broadcast" 
                    type="button" role="tab" onclick="setTabQuery('broadcast')">
                <i class="fa-solid fa-paper-plane"></i> Compose Email Broadcast
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'history' ? 'active' : '' }}" 
                    id="history-tab" data-bs-toggle="tab" data-bs-target="#tab-history" 
                    type="button" role="tab" onclick="setTabQuery('history')">
                <i class="fa-solid fa-clock-rotate-left"></i> Broadcast History &amp; Logs ({{ number_format($totalBroadcasts) }})
            </button>
        </li>
    </ul>
</div>

<div class="tab-content" id="commTabsContent">
    
    {{-- =============================================================== --}}
    {{-- TAB 1: PORTAL ANNOUNCEMENTS                                     --}}
    {{-- =============================================================== --}}
    <div class="tab-pane fade {{ $activeTab === 'announcements' ? 'show active' : '' }}" id="tab-announcements" role="tabpanel">
        <div class="comm-card p-0 mb-4" style="overflow: hidden;">
            {{-- Search & Filter Header --}}
            <div class="p-3 border-bottom bg-light">
                <form method="GET" action="{{ route('admin.announcements.index') }}" class="row g-2 align-items-center">
                    <input type="hidden" name="tab" value="announcements">
                    <div class="col-md-6 col-lg-7">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Search announcement title or content..." value="{{ request('search') }}" style="border-radius: 0 8px 8px 0;">
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <select name="status" class="form-select form-select-sm" style="border-radius: 8px;">
                            <option value="" {{ !request('status') ? 'selected' : '' }}>All Announcements</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active / Published</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled for Future</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-dark flex-grow-1 fw-semibold" style="border-radius: 8px;">
                            Filter
                        </button>
                        @if(request('search') || request('status'))
                            <a href="{{ route('admin.announcements.index', ['tab' => 'announcements']) }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters" style="border-radius: 8px;">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>

                {{-- Bulk Action Bar --}}
                <div id="announcementBulkBar" class="alert alert-light border d-none align-items-center justify-content-between p-2 mt-2 mb-0" style="border-radius: 10px;">
                    <div class="small fw-semibold text-dark">
                        <span id="announcementSelectedCount">0</span> announcements selected
                    </div>
                    <button type="button" class="btn btn-sm btn-danger fw-bold rounded-pill px-3" onclick="submitAnnouncementBulkDelete()" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-trash-can me-1"></i> Delete Selected
                    </button>
                </div>
            </div>

            {{-- Announcements Table --}}
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color); font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--text-main);">
                            <th class="ps-3" style="width: 40px;">
                                <input type="checkbox" id="selectAllAnnouncements" onchange="toggleSelectAllAnnouncements(this)" style="cursor: pointer;" title="Select All">
                            </th>
                            <th>Announcement Title</th>
                            <th>Content Preview</th>
                            <th>Author</th>
                            <th class="text-nowrap">Schedule / Status</th>
                            <th class="pe-4 text-end text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                        <tr id="announcement-row-{{ $announcement->id }}" class="border-bottom">
                            <td class="ps-3">
                                <input type="checkbox" value="{{ $announcement->id }}" class="announcement-checkbox" onchange="updateAnnouncementSelectedCount()" style="cursor: pointer;">
                            </td>
                            <td class="ps-2">
                                <div class="fw-bold text-dark">{{ $announcement->title }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">ID #{{ $announcement->id }} &middot; Created {{ $announcement->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <span class="text-muted small">{{ Str::limit($announcement->content, 85) }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="eval-avatar" style="width: 30px; height: 30px; font-size: 0.7rem;">
                                        {{ strtoupper(substr($announcement->author->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark small">{{ $announcement->author->name ?? 'System' }}</div>
                                        <div class="text-muted text-uppercase" style="font-size: 0.65rem; font-weight: 700;">
                                            {{ ucfirst($announcement->author->role ?? 'Staff') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                @php
                                    $isScheduled = $announcement->scheduled_publish_at && $announcement->scheduled_publish_at->isFuture();
                                    $isExpired = $announcement->scheduled_delete_at && $announcement->scheduled_delete_at->isPast();
                                @endphp
                                @if($isScheduled)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                                        <i class="fa-solid fa-clock me-1"></i> Scheduled: {{ $announcement->scheduled_publish_at->format('M d, H:i') }}
                                    </span>
                                @elseif($isExpired)
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                                        <i class="fa-solid fa-hourglass-end me-1"></i> Expired
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                                        <i class="fa-solid fa-circle-check me-1"></i> Published
                                    </span>
                                @endif
                                <div class="text-muted mt-1" style="font-size: 0.68rem;">
                                    {{ $announcement->created_at->format('M d, Y h:i A') }}
                                </div>
                            </td>
                            <td class="pe-4 text-end text-nowrap">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary" 
                                            onclick="openEditModal(this)"
                                            data-id="{{ $announcement->id }}"
                                            data-title="{{ $announcement->title }}"
                                            data-content="{{ $announcement->content }}"
                                            data-publish="{{ $announcement->scheduled_publish_at ? $announcement->scheduled_publish_at->format('Y-m-d\TH:i') : '' }}"
                                            data-delete="{{ $announcement->scheduled_delete_at ? $announcement->scheduled_delete_at->format('Y-m-d\TH:i') : '' }}"
                                            title="Edit Announcement">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteAnnouncement({{ $announcement->id }})"
                                            title="Delete Announcement">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="eval-avatar mx-auto mb-3" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(0,0,0,0.04); color: #94a3b8; font-size: 1.25rem;">
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                                <h6 class="fw-semibold text-dark mb-1">No Announcements Published</h6>
                                <p class="text-muted small mb-3" style="max-width: 320px; margin: 0 auto; font-size: 0.75rem;">
                                    Post guidelines, schedule updates, or urgent notices to display directly on student feeds.
                                </p>
                                <button class="btn btn-sm btn-success fw-bold px-3 py-1.5 rounded-pill" data-bs-toggle="modal" data-bs-target="#newAnnouncementModal" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-plus me-1"></i> Create First Announcement
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($announcements->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- =============================================================== --}}
    {{-- TAB 2: COMPOSE EMAIL BROADCAST                                  --}}
    {{-- =============================================================== --}}
    <div class="tab-pane fade {{ $activeTab === 'broadcast' ? 'show active' : '' }}" id="tab-broadcast" role="tabpanel">
        <div class="row g-4 mb-4">
            {{-- Broadcast Composer Form --}}
            <div class="col-lg-7">
                <div class="comm-card p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="p-3 bg-light rounded-4 text-success">
                            <i class="fa-solid fa-paper-plane fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Compose Institutional Email Broadcast</h5>
                            <p class="text-muted small mb-0">Direct email alert dispatched to student inboxes and synced to in-app alerts</p>
                        </div>
                    </div>

                    <form action="{{ route('superadmin.broadcast.send') }}" method="POST" id="broadcastForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="targetSelect" class="form-label fw-semibold text-dark small">Target Audience</label>
                            <select name="target" id="targetSelect" class="form-select rounded-3 py-2" required onchange="updateAudienceBadge(this)">
                                <option value="" disabled selected>Select target group...</option>
                                <option value="all_users">All Users (Students, Staff &amp; Administrators)</option>
                                <option value="all_students">All Students (Applicants &amp; Active Scholars)</option>
                                <option value="approved_scholars">Approved Scholars (Currently active grants only)</option>
                                <optgroup label="Specific Scholarship Program">
                                    @foreach($scholarships as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }} Applicants</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <span class="badge bg-light text-muted border" id="audienceNotice" style="font-size: 0.72rem;">
                                    <i class="fa-solid fa-circle-info me-1"></i> Select an audience to preview scope
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="broadcastTitle" class="form-label fw-semibold text-dark small">Email Subject</label>
                            <input type="text" name="title" id="broadcastTitle" class="form-control rounded-3 py-2" 
                                   placeholder="e.g., Mandatory Orientation Assembly for GAD Scholars" required autocomplete="off">
                        </div>

                        {{-- Quick Presets / Templates --}}
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold mb-1">Quick Presets:</label>
                            <div class="d-flex flex-wrap gap-1.5">
                                <span class="template-chip" onclick="applyPreset('assembly')"><i class="fa-solid fa-users me-1"></i> Scholar Assembly</span>
                                <span class="template-chip" onclick="applyPreset('stipend')"><i class="fa-solid fa-money-bill-wave me-1"></i> Stipend Disbursement</span>
                                <span class="template-chip" onclick="applyPreset('grades')"><i class="fa-solid fa-file-circle-check me-1"></i> Grade Verification Deadline</span>
                                <span class="template-chip" onclick="applyPreset('advisory')"><i class="fa-solid fa-triangle-exclamation me-1"></i> Urgent Campus Advisory</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="broadcastBody" class="form-label fw-semibold text-dark small">Message Body</label>
                            <textarea name="body" id="broadcastBody" class="form-control rounded-3" rows="8" 
                                      placeholder="Write your official announcement details here..." required style="resize: none;"></textarea>
                            <div class="d-flex justify-content-between text-muted small mt-1" style="font-size: 0.72rem;">
                                <span>Includes official CLSU institutional email signature.</span>
                                <span id="charCounter">0 characters</span>
                            </div>
                        </div>

                        <button type="submit" class="btn text-white w-100 py-2.5 fw-semibold rounded-pill shadow-sm" id="sendBtn"
                                style="background: linear-gradient(135deg, #07331c, #0F5934);">
                            <span id="btnText"><i class="fa-solid fa-paper-plane me-1.5"></i> Send Institutional Broadcast</span>
                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Communication Guidelines & Dispatch Rules --}}
            <div class="col-lg-5">
                <div class="comm-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="fa-solid fa-shield-halved text-success me-2"></i> Broadcast Guidelines
                        </h6>
                        <div class="alert alert-light border mb-3 py-2.5" style="border-radius: 12px;">
                            <div class="fw-bold text-dark small mb-1"><i class="fa-solid fa-check-double text-success me-1"></i> Dual In-App Sync</div>
                            <p class="text-muted small mb-0" style="font-size: 0.78rem;">
                                Every email broadcast is automatically mirrored into the recipient's in-app notification tray with instantaneous real-time delivery.
                            </p>
                        </div>
                        <div class="alert alert-light border mb-3 py-2.5" style="border-radius: 12px;">
                            <div class="fw-bold text-dark small mb-1"><i class="fa-solid fa-lock text-primary me-1"></i> Anti-Spam Queue Safeguards</div>
                            <p class="text-muted small mb-0" style="font-size: 0.78rem;">
                                Outgoing emails are queued via background SMTP jobs to prevent server timeouts and respect institutional mail rate limits.
                            </p>
                        </div>
                        <div class="alert alert-light border mb-3 py-2.5" style="border-radius: 12px;">
                            <div class="fw-bold text-dark small mb-1"><i class="fa-solid fa-clipboard-list text-warning me-1"></i> Audited Action Logging</div>
                            <p class="text-muted small mb-0" style="font-size: 0.78rem;">
                                All dispatch activities are logged permanently in the institutional audit trail with dispatcher identity, timestamp, and IP address.
                            </p>
                        </div>
                    </div>

                    <div class="p-3 rounded-3" style="background: var(--clsu-bg); border: 1px solid var(--border-color);">
                        <div class="d-flex align-items-center gap-2">
                            <div class="eval-avatar" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold text-dark small">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size: 0.68rem;">Authorized Dispatcher ({{ ucfirst(auth()->user()->role) }})</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =============================================================== --}}
    {{-- TAB 3: BROADCAST HISTORY & LOGS                                 --}}
    {{-- =============================================================== --}}
    <div class="tab-pane fade {{ $activeTab === 'history' ? 'show active' : '' }}" id="tab-history" role="tabpanel">
        <div class="comm-card p-0 mb-4" style="overflow: hidden;">
            {{-- Header with Search and Clear Action --}}
            <div class="p-4 border-bottom bg-white">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Broadcast Dispatch History</h5>
                        <p class="text-muted small mb-0">Complete audit log of dispatched institutional emails and system alerts</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($totalBroadcasts > 0 && auth()->user()->role === 'superadmin')
                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#clearAllModal" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-trash-can me-1"></i> Clear History
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Metric Counters --}}
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-envelopes-bulk text-primary me-1"></i> Total Dispatched: <strong>{{ number_format($totalBroadcasts) }}</strong>
                    </span>
                    <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-users text-success me-1"></i> Unique Recipients: <strong>{{ number_format($uniqueRecipients) }}</strong>
                    </span>
                </div>

                {{-- Search Filter Form --}}
                <form method="GET" action="{{ route('admin.announcements.index') }}" class="row g-2 align-items-center">
                    <input type="hidden" name="tab" value="history">
                    <div class="col-md-9 col-lg-10">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" name="broadcast_search" class="form-control border-start-0" 
                                   placeholder="Search recipient email, subject, or message content..." 
                                   value="{{ request('broadcast_search') }}" style="border-radius: 0 8px 8px 0;">
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-dark flex-grow-1 fw-semibold" style="border-radius: 8px;">
                            Filter
                        </button>
                        @if(request('broadcast_search'))
                            <a href="{{ route('admin.announcements.index', ['tab' => 'history']) }}" class="btn btn-sm btn-outline-secondary" title="Reset Search" style="border-radius: 8px;">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>

                {{-- Bulk Action Bar for Broadcasts --}}
                <div id="broadcastBulkBar" class="alert alert-light border d-none align-items-center justify-content-between p-2 mt-3 mb-0" style="border-radius: 10px;">
                    <div class="small fw-semibold text-dark">
                        <span id="broadcastSelectedCount">0</span> log records selected
                    </div>
                    <button type="button" class="btn btn-sm btn-danger fw-bold rounded-pill px-3" onclick="submitBroadcastBulkDelete()" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-trash-can me-1"></i> Delete Selected
                    </button>
                </div>
            </div>

            {{-- Bulk Delete Form --}}
            <form id="bulkDeleteBroadcastForm" action="{{ route('superadmin.broadcast.bulk-delete') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border-color); font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--text-main);">
                                <th class="ps-3" style="width: 40px;">
                                    <input type="checkbox" id="selectAllBroadcasts" onchange="toggleSelectAllBroadcasts(this)" style="cursor: pointer;" title="Select All">
                                </th>
                                <th>Recipient</th>
                                <th>Subject</th>
                                <th class="text-nowrap">Sent At</th>
                                <th class="pe-4 text-end text-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($broadcasts as $log)
                            <tr class="border-bottom">
                                <td class="ps-3">
                                    <input type="checkbox" name="ids[]" value="{{ $log->id }}" class="broadcast-checkbox" onchange="updateBroadcastSelectedCount()" style="cursor: pointer;">
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark monospace-data" style="font-size: 0.85rem;">{{ $log->recipient }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Direct Email Dispatch</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark text-truncate" style="max-width: 320px; font-size: 0.85rem;">
                                        {{ str_replace('[A.E.G.I.S. Broadcast] ', '', $log->subject) }}
                                    </div>
                                    <div class="text-muted small text-truncate" style="max-width: 320px; font-size: 0.72rem;">
                                        {{ Str::limit($log->content, 60) }}
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <span class="text-dark small fw-medium">{{ $log->created_at->format('M d, Y h:i A') }}</span>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $log->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="pe-4 text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2.5 py-1" 
                                            data-bs-toggle="modal" data-bs-target="#viewBroadcastModal"
                                            data-recipient="{{ $log->recipient }}"
                                            data-subject="{{ str_replace('[A.E.G.I.S. Broadcast] ', '', $log->subject) }}"
                                            data-content="{{ $log->content }}"
                                            data-date="{{ $log->created_at->format('F d, Y h:i A') }}"
                                            onclick="populateViewModal(this)"
                                            title="View Message">
                                        <i class="fa-solid fa-eye me-1"></i> Details
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 ms-1"
                                            onclick="confirmSingleBroadcastDelete({{ $log->id }}, '{{ addslashes(str_replace('[A.E.G.I.S. Broadcast] ', '', $log->subject)) }}')"
                                            title="Delete Log Record">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="eval-avatar mx-auto mb-3" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(0,0,0,0.04); color: #94a3b8; font-size: 1.25rem;">
                                        <i class="fa-solid fa-envelope-open"></i>
                                    </div>
                                    <h6 class="fw-semibold text-dark mb-1">No Broadcast History Logged</h6>
                                    <p class="text-muted small mb-0" style="max-width: 320px; margin: 0 auto; font-size: 0.75rem;">
                                        Dispatched email announcements will appear here with recipient tracking and delivery audit traces.
                                    </p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            @if($broadcasts->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $broadcasts->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

{{-- =============================================================== --}}
{{-- MODALS SECTION                                                  --}}
{{-- =============================================================== --}}

{{-- New Announcement Modal (With Dual-Channel Broadcast Option) --}}
<div class="modal fade" id="newAnnouncementModal" tabindex="-1" aria-labelledby="newAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #07331c, #0F5934); padding: 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="newAnnouncementModalLabel">
                        <i class="fa-solid fa-bullhorn text-warning me-2"></i> Publish Official Announcement
                    </h5>
                    <small class="text-white-50">Post guidelines, schedule notifications, and advisories to student portals</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="announcementForm" onsubmit="publishAnnouncement(event)">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="announcementTitle">Announcement Title</label>
                        <input type="text" name="title" id="announcementTitle" class="form-control py-2" required 
                               placeholder="e.g., Mandatory Guidelines Update for GAD Scholarship Applications" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="announcementContent">Content Details</label>
                        <textarea name="content" id="announcementContent" class="form-control" rows="5" required 
                                  placeholder="Provide the complete announcement detail text here..." style="resize: none;"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted" for="scheduledPublishAt">Publish Date &amp; Time (Optional)</label>
                            <input type="datetime-local" name="scheduled_publish_at" id="scheduledPublishAt" class="form-control">
                            <small class="text-muted" style="font-size: 0.72rem;">Leave blank to publish instantly.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted" for="scheduledDeleteAt">Expiration Date &amp; Time (Optional)</label>
                            <input type="datetime-local" name="scheduled_delete_at" id="scheduledDeleteAt" class="form-control">
                            <small class="text-muted" style="font-size: 0.72rem;">Auto-hide from student feeds after this time.</small>
                        </div>
                    </div>

                    {{-- Cross-Channel Email Dispatch Option --}}
                    <div class="p-3 rounded-3 border mt-3" style="background: var(--clsu-bg);">
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" id="sendEmailBroadcast" name="send_email_broadcast" value="1" onchange="toggleBroadcastTarget(this)">
                            <label class="form-check-label fw-bold text-dark small" for="sendEmailBroadcast">
                                <i class="fa-solid fa-paper-plane text-success me-1"></i> Also dispatch as Email Broadcast to student inboxes
                            </label>
                        </div>
                        <p class="text-muted small mb-0" style="font-size: 0.72rem;">
                            When enabled, this advisory will post to the portal feed AND email active students simultaneously.
                        </p>
                        <div id="modalTargetContainer" class="mt-2 d-none">
                            <label class="form-label fw-semibold small text-muted mb-1">Email Recipient Target</label>
                            <select name="broadcast_target" class="form-select form-select-sm">
                                <option value="all_students" selected>All Students (Applicants &amp; Active Scholars)</option>
                                <option value="approved_scholars">Approved Scholars Only</option>
                                <option value="all_users">All Portal Users (Including Staff)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" id="submitBtn" class="btn text-white fw-bold px-4" style="background: var(--clsu-green); border-radius: 8px;">
                        Publish Broadcast
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Announcement Modal --}}
<div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #07331c, #0F5934); padding: 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="editAnnouncementModalLabel">
                        <i class="fa-solid fa-pen-to-square text-warning me-2"></i> Edit Announcement
                    </h5>
                    <small class="text-white-50">Modify announcement guidelines or reschedule publish/expiration dates</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAnnouncementForm" onsubmit="submitEditAnnouncement(event)">
                @csrf
                @method('PATCH')
                <input type="hidden" name="id" id="editAnnouncementId">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="editAnnouncementTitle">Announcement Title</label>
                        <input type="text" name="title" id="editAnnouncementTitle" class="form-control py-2" required placeholder="e.g., Mandatory Guidelines Update" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="editAnnouncementContent">Content Details</label>
                        <textarea name="content" id="editAnnouncementContent" class="form-control" rows="5" required 
                                  placeholder="Provide the complete announcement detail text here..." style="resize: none;"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted" for="editScheduledPublishAt">Publish Date &amp; Time (Optional)</label>
                            <input type="datetime-local" name="scheduled_publish_at" id="editScheduledPublishAt" class="form-control">
                            <small class="text-muted" style="font-size: 0.72rem;">Leave blank to publish instantly.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted" for="editScheduledDeleteAt">Expiration Date &amp; Time (Optional)</label>
                            <input type="datetime-local" name="scheduled_delete_at" id="editScheduledDeleteAt" class="form-control">
                            <small class="text-muted" style="font-size: 0.72rem;">Auto-hide from student feed after this time.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" id="editSubmitBtn" class="btn text-white fw-bold px-4" style="background: var(--clsu-green); border-radius: 8px;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Broadcast Details Modal --}}
<div class="modal fade" id="viewBroadcastModal" tabindex="-1" aria-labelledby="viewBroadcastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #07331c, #0F5934); padding: 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="viewBroadcastModalLabel">
                        <i class="fa-solid fa-envelope-open-text text-warning me-2"></i> Dispatched Broadcast Details
                    </h5>
                    <small class="text-white-50">Dispatched institutional broadcast message log</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Recipient Email</label>
                        <div id="modalRecipient" class="fw-semibold text-dark monospace-data py-1">—</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <label class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Dispatched Timestamp</label>
                        <div id="modalDate" class="text-muted small py-1">—</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Subject</label>
                    <div id="modalSubject" class="fw-bold text-dark fs-6 p-2.5 bg-light rounded-3 border">
                        —
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Message Content</label>
                    <div id="modalContent" class="p-3 bg-light rounded-3 border text-dark" style="white-space: pre-wrap; line-height: 1.6; min-height: 120px; max-height: 320px; overflow-y: auto; font-size: 0.88rem;">
                        —
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-success fw-bold rounded-pill px-3" onclick="copyToComposer()" data-bs-dismiss="modal">
                    <i class="fa-solid fa-copy me-1"></i> Copy to Composer (Reuse)
                </button>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Clear All History Modal --}}
<div class="modal fade" id="clearAllModal" tabindex="-1" aria-labelledby="clearAllModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #7f1d1d, #dc2626); padding: 1.5rem;">
                <h5 class="modal-title fw-bold text-white mb-0" id="clearAllModalLabel">
                    <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i> Clear Broadcast History
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('superadmin.broadcast.clear-all') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-center">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="fa-solid fa-trash-can fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Permanently Clear All {{ number_format($totalBroadcasts) }} Broadcast Logs?</h5>
                    <p class="text-muted small mb-0">
                        This action will purge all logged broadcast email records from the database.
                        In-app notifications previously delivered to users will remain intact.
                    </p>
                </div>
                <div class="modal-footer border-top bg-light p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold rounded-pill px-4">
                        <i class="fa-solid fa-trash-can me-1"></i> Yes, Clear All
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Single Delete Form for Broadcast Log --}}
<form id="singleBroadcastDeleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    // ── Tab URL Routing & Switching ────────────────────────
    function setTabQuery(tabName) {
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);
    }

    function switchToTab(tabName) {
        const triggerEl = document.querySelector(`#${tabName}-tab`);
        if (triggerEl) {
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
            setTabQuery(tabName);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    // ── Announcement Modal Handlers ────────────────────────
    function toggleBroadcastTarget(checkbox) {
        const container = document.getElementById('modalTargetContainer');
        if (checkbox.checked) {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    }

    async function publishAnnouncement(event) {
        event.preventDefault();
        const form = document.getElementById('announcementForm');
        const submitBtn = document.getElementById('submitBtn');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Publishing...';

        try {
            const response = await fetch("{{ route('admin.announcements.store') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: new FormData(form)
            });

            const data = await response.json();
            if (response.ok && data.success) {
                if (typeof AegisAlert !== 'undefined') {
                    AegisAlert.toast({
                        icon: 'success',
                        title: data.message || 'Announcement Published!'
                    });
                }
                setTimeout(() => location.reload(), 1200);
            } else {
                if (typeof AegisAlert !== 'undefined') {
                    AegisAlert.error({
                        title: 'Failed',
                        text: data.message || 'An error occurred.'
                    });
                } else {
                    alert(data.message || 'An error occurred.');
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Publish Broadcast';
            }
        } catch (error) {
            console.error(error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Publish Broadcast';
        }
    }

    function openEditModal(btn) {
        const id = btn.dataset.id;
        const title = btn.dataset.title;
        const content = btn.dataset.content;
        const publish = btn.dataset.publish;
        const delTime = btn.dataset.delete;

        document.getElementById('editAnnouncementId').value = id;
        document.getElementById('editAnnouncementTitle').value = title;
        document.getElementById('editAnnouncementContent').value = content;
        document.getElementById('editScheduledPublishAt').value = publish;
        document.getElementById('editScheduledDeleteAt').value = delTime;

        const modal = new bootstrap.Modal(document.getElementById('editAnnouncementModal'));
        modal.show();
    }

    async function submitEditAnnouncement(event) {
        event.preventDefault();
        const form = document.getElementById('editAnnouncementForm');
        const id = document.getElementById('editAnnouncementId').value;
        const submitBtn = document.getElementById('editSubmitBtn');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Saving...';

        try {
            const response = await fetch(`/admin/announcements/${id}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: new FormData(form)
            });

            const data = await response.json();
            if (response.ok && data.success) {
                if (typeof AegisAlert !== 'undefined') {
                    AegisAlert.toast({
                        icon: 'success',
                        title: data.message || 'Changes saved successfully!'
                    });
                }
                setTimeout(() => location.reload(), 1200);
            } else {
                if (typeof AegisAlert !== 'undefined') {
                    AegisAlert.error({
                        title: 'Failed',
                        text: data.message || 'An error occurred.'
                    });
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save Changes';
            }
        } catch (error) {
            console.error(error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save Changes';
        }
    }

    function deleteAnnouncement(id) {
        if (typeof AegisAlert !== 'undefined') {
            AegisAlert.delete({
                title: 'Delete Announcement?',
                text: "This will remove the announcement from all student dashboards permanently.",
                confirmText: 'Yes, Delete It'
            }).then(async (confirmed) => {
                if (confirmed) {
                    executeDeleteAnnouncement(id);
                }
            });
        } else if (confirm('Are you sure you want to delete this announcement?')) {
            executeDeleteAnnouncement(id);
        }
    }

    async function executeDeleteAnnouncement(id) {
        try {
            const response = await fetch(`/admin/announcements/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            if (response.ok && data.success) {
                const row = document.getElementById(`announcement-row-${id}`);
                if (row) {
                    row.remove();
                    const rows = document.querySelectorAll('#tab-announcements tbody tr');
                    if (rows.length === 0) location.reload();
                }
            }
        } catch (error) {
            console.error(error);
        }
    }

    // ── Announcement Bulk Selection ────────────────────────
    function toggleSelectAllAnnouncements(master) {
        const checkboxes = document.querySelectorAll('.announcement-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateAnnouncementSelectedCount();
    }

    function updateAnnouncementSelectedCount() {
        const checked = document.querySelectorAll('.announcement-checkbox:checked');
        const count = checked.length;
        const bar = document.getElementById('announcementBulkBar');
        const countSpan = document.getElementById('announcementSelectedCount');

        if (count > 0) {
            bar.classList.remove('d-none');
            bar.classList.add('d-flex');
            countSpan.textContent = count;
        } else {
            bar.classList.add('d-none');
            bar.classList.remove('d-flex');
            countSpan.textContent = '0';
        }

        const totalCheckboxes = document.querySelectorAll('.announcement-checkbox').length;
        const selectAll = document.getElementById('selectAllAnnouncements');
        if (selectAll) {
            selectAll.checked = (count === totalCheckboxes && totalCheckboxes > 0);
        }
    }

    function submitAnnouncementBulkDelete() {
        const checked = document.querySelectorAll('.announcement-checkbox:checked');
        const ids = Array.from(checked).map(cb => parseInt(cb.value));
        if (ids.length === 0) return;

        if (typeof AegisAlert !== 'undefined') {
            AegisAlert.delete({
                title: `Delete ${ids.length} Announcements?`,
                text: "This will remove the selected announcements permanently.",
                confirmText: `Yes, Delete ${ids.length}`
            }).then(async (confirmed) => {
                if (confirmed) executeBulkDelete(ids);
            });
        } else if (confirm(`Delete ${ids.length} announcements?`)) {
            executeBulkDelete(ids);
        }
    }

    async function executeBulkDelete(ids) {
        try {
            const response = await fetch("{{ route('admin.announcements.bulk-destroy') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ ids: ids })
            });

            const data = await response.json();
            if (response.ok && data.success) {
                location.reload();
            }
        } catch (error) {
            console.error(error);
        }
    }

    // ── Email Broadcast Composer ───────────────────────────
    document.getElementById('broadcastForm')?.addEventListener('submit', function() {
        document.getElementById('btnText').style.display = 'none';
        document.getElementById('btnSpinner').classList.remove('d-none');
        document.getElementById('sendBtn').disabled = true;
    });

    document.getElementById('broadcastBody')?.addEventListener('input', function(e) {
        document.getElementById('charCounter').textContent = `${e.target.value.length} characters`;
    });

    function updateAudienceBadge(select) {
        const text = select.options[select.selectedIndex]?.text || '';
        const badge = document.getElementById('audienceNotice');
        if (badge) {
            badge.innerHTML = `<i class="fa-solid fa-users text-success me-1"></i> Dispatches to: <strong>${text}</strong>`;
        }
    }

    // Template Presets
    const presets = {
        assembly: {
            title: "Mandatory Scholar General Assembly",
            body: "Greetings CLSU Scholars,\n\nPlease be informed of our upcoming Mandatory General Assembly scheduled this coming Friday at 9:00 AM at the University Auditorium. Important updates regarding semester allowances, compliance submission, and academic monitoring will be discussed.\n\nAttendance is strictly required. Please bring your valid CLSU Student ID."
        },
        stipend: {
            title: "Scholarship Allowance & Stipend Disbursement Schedule",
            body: "Dear Scholars,\n\nWe are pleased to announce that your scholarship allowances for the current academic term have been processed and are now ready for release. Please coordinate with the Cashier Office following the designated schedule per college.\n\nEnsure to present your Certificate of Registration (COR) and student ID during pickup."
        },
        grades: {
            title: "Urgent: Submission of Official Grades & Transcript Requirements",
            body: "Notice to all Scholarship Grant Recipients:\n\nPlease be reminded that the deadline for uploading your certified Grade Slip / Transcript of Records for the concluding semester is on Friday at 5:00 PM.\n\nFailure to submit before the deadline may impact your scholarship renewal eligibility. Log in to the AEGIS portal to submit your documents immediately."
        },
        advisory: {
            title: "Urgent University Advisory: Schedule Adjustment",
            body: "Official Advisory from the Office of Student Affairs:\n\nDue to scheduled institutional activities, administrative scholarship verification and document evaluation transactions are temporarily adjusted. Online portal submissions remain fully active 24/7.\n\nFor urgent inquiries, please contact the scholarship coordinator via email."
        }
    };

    function applyPreset(key) {
        const p = presets[key];
        if (p) {
            document.getElementById('broadcastTitle').value = p.title;
            document.getElementById('broadcastBody').value = p.body;
            document.getElementById('charCounter').textContent = `${p.body.length} characters`;
            document.getElementById('broadcastTitle').focus();
        }
    }

    // ── Broadcast History Log Handlers ─────────────────────
    let currentSubject = '';
    let currentContent = '';

    function populateViewModal(btn) {
        const recipient = btn.getAttribute('data-recipient') || 'N/A';
        currentSubject  = btn.getAttribute('data-subject') || '';
        currentContent  = btn.getAttribute('data-content') || '';
        const date      = btn.getAttribute('data-date') || '';

        document.getElementById('modalRecipient').textContent = recipient;
        document.getElementById('modalSubject').textContent   = currentSubject;
        document.getElementById('modalContent').textContent   = currentContent || '(No text content provided)';
        document.getElementById('modalDate').textContent      = date;
    }

    function copyToComposer() {
        document.getElementById('broadcastTitle').value = currentSubject;
        document.getElementById('broadcastBody').value  = currentContent;
        switchToTab('broadcast');
        document.getElementById('broadcastTitle').focus();
    }

    function confirmSingleBroadcastDelete(id, subject) {
        if (typeof AegisAlert !== 'undefined') {
            AegisAlert.delete({
                title: 'Delete Broadcast Log?',
                text: `Are you sure you want to delete this broadcast log for "${subject}"?`,
                confirmText: 'Yes, Delete Log'
            }).then(confirmed => {
                if (confirmed) {
                    const form = document.getElementById('singleBroadcastDeleteForm');
                    form.action = `/superadmin/broadcast/${id}`;
                    form.submit();
                }
            });
        } else if (confirm(`Delete broadcast log for "${subject}"?`)) {
            const form = document.getElementById('singleBroadcastDeleteForm');
            form.action = `/superadmin/broadcast/${id}`;
            form.submit();
        }
    }

    function toggleSelectAllBroadcasts(master) {
        const checkboxes = document.querySelectorAll('.broadcast-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateBroadcastSelectedCount();
    }

    function updateBroadcastSelectedCount() {
        const checked = document.querySelectorAll('.broadcast-checkbox:checked');
        const count = checked.length;
        const bar = document.getElementById('broadcastBulkBar');
        const countSpan = document.getElementById('broadcastSelectedCount');

        if (count > 0) {
            bar.classList.remove('d-none');
            bar.classList.add('d-flex');
            countSpan.textContent = count;
        } else {
            bar.classList.add('d-none');
            bar.classList.remove('d-flex');
            countSpan.textContent = '0';
        }

        const totalCheckboxes = document.querySelectorAll('.broadcast-checkbox').length;
        const selectAll = document.getElementById('selectAllBroadcasts');
        if (selectAll) {
            selectAll.checked = (count === totalCheckboxes && totalCheckboxes > 0);
        }
    }

    function submitBroadcastBulkDelete() {
        const count = document.querySelectorAll('.broadcast-checkbox:checked').length;
        if (count === 0) return;

        if (typeof AegisAlert !== 'undefined') {
            AegisAlert.delete({
                title: 'Delete Selected Logs?',
                text: `Are you sure you want to delete ${count} selected broadcast records?`,
                confirmText: 'Yes, Delete Selected'
            }).then(confirmed => {
                if (confirmed) {
                    document.getElementById('bulkDeleteBroadcastForm').submit();
                }
            });
        } else if (confirm(`Delete ${count} selected broadcast records?`)) {
            document.getElementById('bulkDeleteBroadcastForm').submit();
        }
    }
</script>
@endpush
