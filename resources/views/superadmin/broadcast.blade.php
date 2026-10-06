@extends('layouts.app')

@section('title', 'Email Broadcast Center')
@section('page-title', 'Email Broadcast Center')
@section('page-subtitle', 'Direct email broadcasts to students, scholars, or specific scholarship programs')

@section('content')
<div class="row g-4">
    <!-- Left Column: Compose Broadcast -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="p-3 bg-light rounded-4 text-info">
                        <i class="fa-solid fa-paper-plane fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Compose Email</h5>
                        <p class="text-muted small mb-0">Broadcast alerts or updates instantly</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 small mb-4 py-2" style="background-color: #dcfce7; color: #14532d; border-radius: 10px;">
                        <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-0 small mb-4 py-2" style="border-radius: 10px;">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('superadmin.broadcast.send') }}" method="POST" id="broadcastForm">
                    @csrf
                    <div class="mb-3">
                        <label for="targetSelect" class="form-label fw-semibold text-dark small">Target Audience</label>
                        <select name="target" id="targetSelect" class="form-select rounded-3" required>
                            <option value="" disabled selected>Select target group...</option>
                            <option value="all_users">All Users (Students, Staff & Administrators)</option>
                            <option value="all_students">All Students (Applicants & Scholars)</option>
                            <option value="approved_scholars">Approved Scholars (Active only)</option>
                            <optgroup label="Scholarship Programs">
                                @foreach($scholarships as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} Applicants</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="broadcastTitle" class="form-label fw-semibold text-dark small">Email Subject</label>
                        <input type="text" name="title" id="broadcastTitle" class="form-control rounded-3" 
                               placeholder="e.g. Mandatory Assembly for GAD Scholars" required autocomplete="off">
                    </div>

                    <div class="mb-4">
                        <label for="broadcastBody" class="form-label fw-semibold text-dark small">Message Body</label>
                        <textarea name="body" id="broadcastBody" class="form-control rounded-3" rows="8" 
                                  placeholder="Write your email announcement details here..." required style="resize: none;"></textarea>
                    </div>

                    <button type="submit" class="btn text-white w-100 py-2.5 fw-semibold rounded-pill" id="sendBtn"
                            style="background-color: #0C4E2D; box-shadow: 0 4px 6px rgba(12, 78, 45, 0.15);">
                        <span id="btnText"><i class="fa-solid fa-paper-plane me-1"></i> Send Broadcast</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: History with Data Management -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white border-0 p-4 pb-2">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Broadcast History</h5>
                        <p class="text-muted small mb-0">Archive of dispatched email broadcasts and system notices</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($totalBroadcasts > 0)
                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#clearAllModal" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-trash-can me-1"></i> Clear History
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Metric Counters --}}
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-envelopes-bulk text-primary me-1"></i> Total Sent: <strong>{{ number_format($totalBroadcasts) }}</strong>
                    </span>
                    <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-users text-success me-1"></i> Unique Recipients: <strong>{{ number_format($uniqueRecipients) }}</strong>
                    </span>
                </div>

                {{-- Search & Bulk Actions Bar --}}
                <form method="GET" action="{{ route('superadmin.broadcast') }}" class="row g-2 align-items-center">
                    <div class="col-sm-8 col-md-9">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Search recipient, subject, or message content..." value="{{ request('search') }}" style="border-radius: 0 8px 8px 0;">
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-3 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-dark flex-grow-1 fw-semibold" style="border-radius: 8px;">
                            Filter
                        </button>
                        @if(request('search'))
                            <a href="{{ route('superadmin.broadcast') }}" class="btn btn-sm btn-outline-secondary" title="Reset Search" style="border-radius: 8px;">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>

                {{-- Live Bulk Action Bar --}}
                <div id="bulkActionBar" class="alert alert-light border d-none align-items-center justify-content-between p-2 mt-3 mb-0" style="border-radius: 10px;">
                    <div class="small fw-semibold text-dark">
                        <span id="selectedCount">0</span> records selected
                    </div>
                    <button type="button" class="btn btn-sm btn-danger fw-bold rounded-pill px-3" onclick="submitBulkDelete()" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-trash-can me-1"></i> Delete Selected
                    </button>
                </div>
            </div>

            <div class="card-body p-0 mt-2">
                <form id="bulkDeleteForm" action="{{ route('superadmin.broadcast.bulk-delete') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr class="bg-light">
                                    <th class="ps-3" style="width: 40px;">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" style="cursor: pointer;" title="Select All">
                                    </th>
                                    <th style="font-size: 0.78rem; text-transform: uppercase;">Recipient</th>
                                    <th style="font-size: 0.78rem; text-transform: uppercase;">Subject</th>
                                    <th class="text-center text-nowrap" style="font-size: 0.78rem; text-transform: uppercase;">Sent At</th>
                                    <th class="pe-4 text-end text-nowrap" style="font-size: 0.78rem; text-transform: uppercase;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($broadcasts as $b)
                                    <tr class="border-bottom" id="broadcast-row-{{ $b->id }}">
                                        <td class="ps-3">
                                            <input type="checkbox" name="ids[]" value="{{ $b->id }}" class="broadcast-checkbox" onchange="updateSelectedCount()" style="cursor: pointer;">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width: 28px; height: 28px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; flex-shrink: 0;">
                                                    <i class="fa-solid fa-envelope"></i>
                                                </div>
                                                <span class="fw-semibold text-dark small text-break">{{ $b->recipient }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $cleanSubject = str_replace('[A.E.G.I.S. Broadcast] ', '', $b->subject);
                                            @endphp
                                            <div class="text-dark small fw-semibold text-truncate" style="max-width: 240px;" title="{{ $cleanSubject }}">
                                                {{ $cleanSubject }}
                                            </div>
                                            @if($b->content)
                                                <div class="text-muted small text-truncate" style="max-width: 240px; font-size: 0.72rem;">
                                                    {{ Str::limit($b->content, 60) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <span class="text-muted small monospace-data">{{ $b->created_at->format('M d, Y') }}</span>
                                            <div style="font-size: 0.68rem; color: #94a3b8;">{{ $b->created_at->format('h:i A') }}</div>
                                        </td>
                                        <td class="pe-4 text-end text-nowrap">
                                            <div class="d-flex justify-content-end align-items-center gap-1.5 flex-nowrap">
                                                {{-- View details modal trigger --}}
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-dark" 
                                                        title="View Message Details"
                                                        data-bs-toggle="modal" data-bs-target="#viewBroadcastModal"
                                                        data-recipient="{{ $b->recipient }}"
                                                        data-subject="{{ $cleanSubject }}"
                                                        data-content="{{ $b->content }}"
                                                        data-date="{{ $b->created_at->format('F d, Y · h:i A') }}"
                                                        onclick="populateViewModal(this)">
                                                    <i class="fa-solid fa-eye text-primary"></i> <span class="d-none d-sm-inline ms-1 small fw-semibold">View</span>
                                                </button>

                                                {{-- Single delete button --}}
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" 
                                                        title="Delete this record"
                                                        onclick="confirmSingleDelete({{ $b->id }}, '{{ addslashes($cleanSubject) }}')">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <div style="width: 56px; height: 56px; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
                                                <i class="fa-solid fa-envelope-open fa-xl text-muted opacity-40"></i>
                                            </div>
                                            @if(request('search'))
                                                <h6 class="fw-bold text-dark mb-1">No matching broadcasts found</h6>
                                                <p class="mb-2 small text-muted">Try refining your search keyword or clearing filters.</p>
                                                <a href="{{ route('superadmin.broadcast') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                    <i class="fa-solid fa-rotate-left me-1"></i> Clear Filter
                                                </a>
                                            @else
                                                <h6 class="fw-bold text-muted mb-1">No Broadcast History</h6>
                                                <p class="mb-0 small text-muted">Dispatched announcements to students and staff will appear here.</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                @if($broadcasts->hasPages())
                    <div class="p-3 border-top">
                        {{ $broadcasts->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Single Delete Form (Hidden) --}}
<form id="singleDeleteForm" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

{{-- View Broadcast Modal --}}
<div class="modal fade" id="viewBroadcastModal" tabindex="-1" aria-labelledby="viewBroadcastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #0f1f12, #0F5934); padding: 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="viewBroadcastModalLabel">
                        <i class="fa-solid fa-envelope-open-text text-warning me-2"></i> Broadcast Details
                    </h5>
                    <small class="text-white-50">Dispatched system broadcast message log</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase">Recipient</label>
                        <div id="modalRecipient" class="fw-semibold text-dark monospace-data py-1">—</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <label class="text-muted small fw-bold text-uppercase">Dispatched At</label>
                        <div id="modalDate" class="text-muted small py-1">—</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small fw-bold text-uppercase">Subject</label>
                    <div id="modalSubject" class="fw-bold text-dark fs-6 p-2.5 bg-light rounded-3 border">
                        —
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small fw-bold text-uppercase">Message Content</label>
                    <div id="modalContent" class="p-3 bg-light rounded-3 border text-dark" style="white-space: pre-wrap; line-height: 1.6; min-height: 120px; max-height: 320px; overflow-y: auto; font-size: 0.9rem;">
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

{{-- Clear All Confirmation Modal --}}
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

@push('scripts')
<script>
    document.getElementById('broadcastForm').addEventListener('submit', function() {
        document.getElementById('btnText').style.display = 'none';
        document.getElementById('btnSpinner').classList.remove('d-none');
        document.getElementById('sendBtn').disabled = true;
    });

    let currentSubject = '';
    let currentContent = '';

    function populateViewModal(btn) {
        const recipient = btn.getAttribute('data-recipient') || 'N/A';
        currentSubject = btn.getAttribute('data-subject') || '';
        currentContent = btn.getAttribute('data-content') || '';
        const date = btn.getAttribute('data-date') || '';

        document.getElementById('modalRecipient').textContent = recipient;
        document.getElementById('modalSubject').textContent = currentSubject;
        document.getElementById('modalContent').textContent = currentContent || '(No text content provided)';
        document.getElementById('modalDate').textContent = date;
    }

    function copyToComposer() {
        document.getElementById('broadcastTitle').value = currentSubject;
        document.getElementById('broadcastBody').value = currentContent;
        document.getElementById('broadcastTitle').focus();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function confirmSingleDelete(id, subject) {
        AegisAlert.delete({
            title: 'Delete Broadcast Log?',
            text: 'Are you sure you want to delete this broadcast log for "' + subject + '"?',
            confirmText: 'Yes, Delete Log'
        }).then(confirmed => {
            if (confirmed) {
                const form = document.getElementById('singleDeleteForm');
                form.action = '/superadmin/broadcast/' + id;
                form.submit();
            }
        });
    }

    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('.broadcast-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.broadcast-checkbox:checked');
        const count = checked.length;
        const bar = document.getElementById('bulkActionBar');
        const countSpan = document.getElementById('selectedCount');

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
        const selectAll = document.getElementById('selectAllCheckbox');
        if (selectAll) {
            selectAll.checked = (count === totalCheckboxes && totalCheckboxes > 0);
        }
    }

    function submitBulkDelete() {
        const count = document.querySelectorAll('.broadcast-checkbox:checked').length;
        if (count === 0) return;

        AegisAlert.delete({
            title: 'Delete Selected Logs?',
            text: 'Are you sure you want to delete ' + count + ' selected broadcast records? This action cannot be undone.',
            confirmText: 'Yes, Delete Selected'
        }).then(confirmed => {
            if (confirmed) {
                document.getElementById('bulkDeleteForm').submit();
            }
        });
    }
</script>
@endpush
@endsection
