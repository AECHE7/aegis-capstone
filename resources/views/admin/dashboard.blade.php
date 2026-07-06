@extends('layouts.app')

@section('title', 'OSA Admin Dashboard | A.E.G.I.S.')
@section('page-title', 'Application Queue')
@section('page-subtitle', 'Review and evaluate all scholarship applications')

@push('styles')
<style>
    /* Count-up animation */
    .stat-number { font-size: 2rem; font-weight: 700; font-family: 'Poppins', sans-serif; line-height: 1; }

    /* Searchbar */
    .filter-bar { background: var(--card-bg); border-radius: 12px; padding: 1.25rem 1.5rem; border: 1px solid var(--border-color); box-shadow: none; }

    /* Table */
    .queue-table { border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); background: var(--card-bg); }
    .queue-table thead th { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: var(--text-main); background: var(--clsu-bg); padding: 0.9rem 1rem; border-bottom: 2px solid var(--border-color); }
    .queue-table tbody tr { border-color: var(--border-color); cursor: pointer; transition: var(--transition); }
    .queue-table tbody tr:hover { background: var(--clsu-bg); }
    .queue-table td { padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.875rem; }

    /* Avatar */
    .student-avatar { width: 36px; height: 36px; border-radius: 8px; background: var(--clsu-bg); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; color: var(--clsu-green); flex-shrink: 0; }

    /* Fraud Score Chip */
    .fraud-chip { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; }
    .fraud-low    { background: rgba(34, 197, 94, 0.15); color: #16a34a; }
    .fraud-mod    { background: rgba(217, 119, 6, 0.15); color: #d97706; }
    .fraud-high   { background: rgba(220, 38, 38, 0.15); color: #dc2626; }
    .fraud-none   { background: var(--clsu-bg); color: var(--text-main); }

    /* Action button */
    .btn-evaluate { background: var(--clsu-green); color: white; border: none; padding: 6px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; transition: var(--transition); }
    .btn-evaluate:hover { background: var(--clsu-green-light); color: white; }

    /* Export buttons */
    .btn-export { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; border: 1.5px solid; transition: all 0.2s; text-decoration: none; }
    .btn-export:hover { transform: translateY(-1px); }
    .btn-export-csv { border-color: #22c55e; color: #16a34a; }
    .btn-export-csv:hover { background: #22c55e; color: white; }
    .btn-export-pdf { border-color: #ef4444; color: #dc2626; }
    .btn-export-pdf:hover { background: #ef4444; color: white; }
</style>
@endpush

@section('content')

{{-- Stat Cards Row --}}
<div class="row g-3 mb-4">
    {{-- Pending --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card warning card-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem;letter-spacing:0.8px;">Pending</div>
                    <div class="stat-number text-dark count-up monospace-data" data-target="{{ $pendingCount }}">0</div>
                </div>
                <div class="stat-icon" style="background:#fef9c3;">
                    <i class="fa-solid fa-hourglass-half" style="color:#ca8a04;"></i>
                </div>
            </div>
            <div class="small text-muted">Awaiting review</div>
        </div>
    </div>
    {{-- Under Review --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card info card-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem;letter-spacing:0.8px;">Under Review</div>
                    <div class="stat-number text-dark count-up monospace-data" data-target="{{ $underReviewCount }}">0</div>
                </div>
                <div class="stat-icon" style="background:#e0f2fe;">
                    <i class="fa-solid fa-magnifying-glass-chart" style="color:#0284c7;"></i>
                </div>
            </div>
            <div class="small text-muted">Being evaluated</div>
        </div>
    </div>
    {{-- Approved --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card success card-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem;letter-spacing:0.8px;">Verified Scholars</div>
                    <div class="stat-number text-dark count-up monospace-data" data-target="{{ $approvedCount }}">0</div>
                </div>
                <div class="stat-icon" style="background:#dcfce7;">
                    <i class="fa-solid fa-user-graduate" style="color:#16a34a;"></i>
                </div>
            </div>
            <div class="small text-muted">Fully approved</div>
        </div>
    </div>
    {{-- Rejected --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card danger card-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem;letter-spacing:0.8px;">Anomalies Detected</div>
                    <div class="stat-number text-dark count-up monospace-data" data-target="{{ $rejectedCount }}">0</div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;">
                    <i class="fa-solid fa-shield-virus" style="color:#dc2626;"></i>
                </div>
            </div>
            <div class="small text-muted">Rejected applications</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar mb-4">
    <form action="{{ route('admin.dashboard') }}" method="GET">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted mb-1" for="searchInput"><i class="fa-solid fa-magnifying-glass me-1"></i> Search</label>
                <input type="text" name="search" id="searchInput" class="form-control" value="{{ request('search') }}" placeholder="Search..." autocomplete="off">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted mb-1" for="scholarshipSelect"><i class="fa-solid fa-graduation-cap me-1"></i> Scholarship</label>
                <select name="scholarship_id" id="scholarshipSelect" class="form-select">
                    <option value="">All Programs</option>
                    @foreach($scholarships as $s)
                        <option value="{{ $s->id }}" {{ request('scholarship_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted mb-1" for="statusSelect"><i class="fa-solid fa-circle-half-stroke me-1"></i> Status</label>
                <select name="status" id="statusSelect" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="Under Review" {{ request('status') === 'Under Review' ? 'selected' : '' }}>🔍 Under Review</option>
                    <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>✅ Approved</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>❌ Rejected</option>
                    <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>🗑️ Cancelled / Trash</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted mb-1" for="academicPeriodSelect"><i class="fa-solid fa-calendar me-1"></i> Period</label>
                <select name="academic_term_id" id="academicPeriodSelect" class="form-select">
                    <option value="">All Periods</option>
                    @foreach($academicTerms as $term)
                        <option value="{{ $term->id }}" {{ request('academic_term_id') == $term->id ? 'selected' : '' }}>
                            {{ $term->semester }}, SY {{ $term->academic_year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted mb-1" for="sortSelect"><i class="fa-solid fa-arrow-down-wide-short me-1"></i> Sort By</label>
                <select name="sort" id="sortSelect" class="form-select">
                    <option value="">Newest</option>
                    <option value="priority" {{ request('sort') === 'priority' ? 'selected' : '' }}>🔥 AEGIS Priority</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn fw-bold flex-grow-1" style="background: var(--clsu-green); color: white; border-radius: 8px; font-size:0.875rem;">
                    Filter
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light fw-bold" style="border-radius:8px;font-size:0.875rem;" title="Clear">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Application Queue Table --}}
<div class="queue-table shadow-sm" id="tableContainer">
    @include('admin.partials.application_table')
</div>

{{-- Floating Bulk Action Bar --}}
<div id="bulkActionBar" class="position-fixed bottom-0 start-50 translate-middle-x mb-4 bg-white border border-color rounded-4 p-3 shadow-lg d-none align-items-center gap-3" style="z-index: 1050; min-width: 500px; transition: all 0.3s ease; border-width: 1.5px !important;">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success rounded-pill px-2.5 py-1.5 fs-7" id="selectedCountBadge">0</span>
        <span class="small fw-semibold text-dark">selected</span>
    </div>
    <div style="width: 1px; height: 24px; background: var(--border-color);"></div>
    <div class="d-flex align-items-center gap-2 flex-grow-1">
        <select id="bulkStatusSelect" class="form-select form-select-sm" style="border-radius: 8px; width: 140px;">
            <option value="">Choose action...</option>
            <option value="Approved">✅ Approve</option>
            <option value="Rejected">❌ Reject</option>
        </select>
        <input type="text" id="bulkRemarksInput" class="form-control form-control-sm" placeholder="Bulk process remarks (optional)..." style="border-radius: 8px;" />
        <button class="btn btn-sm btn-success px-3 fw-bold" onclick="submitBulkAction()" style="border-radius: 8px;">Apply</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Count-up animation ──────────────────────────────
    function animateCountUp(el, target) {
        if (target === 0) { el.textContent = '0'; return; }
        let start = parseInt(el.textContent) || 0;
        const diff = target - start;
        if (diff === 0) return;
        const duration = 400; // ms
        const steps = 15;
        const stepTime = duration / steps;
        const stepVal = Math.ceil(diff / steps);
        let currentStep = 0;
        
        const timer = setInterval(() => {
            currentStep++;
            start += stepVal;
            if (diff > 0) {
                start = Math.min(start, target);
            } else {
                start = Math.max(start, target);
            }
            el.textContent = start;
            if (currentStep >= steps || start === target) {
                el.textContent = target;
                clearInterval(timer);
            }
        }, stepTime);
    }

    document.querySelectorAll('.count-up').forEach(el => {
        const target = parseInt(el.dataset.target);
        animateCountUp(el, target);
    });

    // ── AJAX Queue Reloading ───────────────────────────
    const filterForm = document.querySelector('.filter-bar form');
    const tableContainer = document.getElementById('tableContainer');
    const searchInput = document.getElementById('searchInput');
    const scholarshipSelect = document.getElementById('scholarshipSelect');
    const statusSelect = document.getElementById('statusSelect');
    const academicPeriodSelect = document.getElementById('academicPeriodSelect');
    const sortSelect = document.getElementById('sortSelect');
    
    let debounceTimer;
    let currentArchivedState = "{{ request('archived') == '1' ? '1' : '0' }}";

    function getFilterParams(page = 1) {
        const params = new URLSearchParams();
        if (searchInput.value) params.set('search', searchInput.value);
        if (scholarshipSelect.value) params.set('scholarship_id', scholarshipSelect.value);
        if (statusSelect.value) params.set('status', statusSelect.value);
        if (academicPeriodSelect.value) params.set('academic_term_id', academicPeriodSelect.value);
        if (sortSelect && sortSelect.value) params.set('sort', sortSelect.value);
        if (currentArchivedState === '1') params.set('archived', '1');
        params.set('page', page);
        return params.toString();
    }

    async function reloadQueue(page = 1) {
        // Show subtle loading overlay or opacity
        tableContainer.style.opacity = '0.6';
        tableContainer.style.transition = 'opacity 0.2s ease';
        
        const queryParams = getFilterParams(page);
        const url = `{{ route('admin.dashboard') }}?${queryParams}`;

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            // Update table html
            tableContainer.innerHTML = data.html;
            tableContainer.style.opacity = '1';
            
            // Update counts cards
            if (data.counts) {
                const countMappings = {
                    'pending': '.stat-card:nth-of-type(1) .stat-number',
                    'under_review': '.stat-card:nth-of-type(2) .stat-number',
                    'approved': '.stat-card:nth-of-type(3) .stat-number',
                    'rejected': '.stat-card:nth-of-type(4) .stat-number'
                };
                for (const [key, selector] of Object.entries(countMappings)) {
                    const el = document.querySelector(selector);
                    if (el) animateCountUp(el, data.counts[key]);
                }
            }

            // Sync address bar
            window.history.pushState({}, '', url);
            
            // Reset bulk actions bar selection state
            const selectAll = document.getElementById('selectAllCheckbox');
            if (selectAll) selectAll.checked = false;
            updateBulkActionBar();
        } catch (error) {
            console.error('AJAX Load Error:', error);
            tableContainer.style.opacity = '1';
        }
    }

    // Event listeners
    filterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        reloadQueue();
    });

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => reloadQueue(), 300);
    });

    [scholarshipSelect, statusSelect, academicPeriodSelect, sortSelect].forEach(select => {
        if (select) {
            select.addEventListener('change', () => reloadQueue());
        }
    });

    // Handle pagination links click via event delegation
    tableContainer.addEventListener('click', (e) => {
        const pageLink = e.target.closest('.pagination a');
        if (pageLink) {
            e.preventDefault();
            const urlObj = new URL(pageLink.href);
            const page = urlObj.searchParams.get('page') || 1;
            reloadQueue(page);
            window.scrollTo({ top: tableContainer.offsetTop - 100, behavior: 'smooth' });
        }
    });

    // Handle Active/Archived toggle
    tableContainer.addEventListener('click', (e) => {
        const toggleBtn = e.target.closest('.active-queue-toggle');
        if (toggleBtn) {
            e.preventDefault();
            currentArchivedState = toggleBtn.dataset.archived;
            reloadQueue();
        }
    });

    // Handle Cancelled Queue toggle
    tableContainer.addEventListener('click', (e) => {
        const cancelledBtn = e.target.closest('#cancelledQueueToggle');
        if (cancelledBtn) {
            e.preventDefault();
            statusSelect.value = 'Cancelled';
            reloadQueue();
        }
    });

    // Handle Archive/Unarchive AJAX Actions
    tableContainer.addEventListener('submit', async (e) => {
        const archiveForm = e.target.closest('.archive-form');
        if (archiveForm) {
            e.preventDefault();
            const submitBtn = archiveForm.querySelector('button');
            const originalHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';

            try {
                const response = await fetch(archiveForm.action, {
                    method: 'POST',
                    body: new FormData(archiveForm),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4' }
                    });
                    // Reload current page of queue
                    const activePage = document.querySelector('.pagination .active span')?.textContent || 1;
                    reloadQueue(activePage);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Archive Action Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }
    });

    // ── Bulk Actions Handlers ──────────────────────────
    function toggleSelectAll(selectAllInput) {
        const checkboxes = document.querySelectorAll('.app-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = selectAllInput.checked;
        });
        updateBulkActionBar();
    }

    function toggleAppSelect(checkboxInput) {
        const selectAll = document.getElementById('selectAllCheckbox');
        const checkboxes = document.querySelectorAll('.app-checkbox');
        
        // Update select all state
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        if (selectAll) selectAll.checked = allChecked;

        updateBulkActionBar();
    }

    function updateBulkActionBar() {
        const bar = document.getElementById('bulkActionBar');
        const badge = document.getElementById('selectedCountBadge');
        const checkedBoxes = document.querySelectorAll('.app-checkbox:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            badge.textContent = count;
            bar.classList.remove('d-none');
            bar.classList.add('d-flex');
        } else {
            bar.classList.remove('d-flex');
            bar.classList.add('d-none');
        }
    }

    async function submitBulkAction() {
        const statusSelect = document.getElementById('bulkStatusSelect');
        const remarksInput = document.getElementById('bulkRemarksInput');
        const checkedBoxes = document.querySelectorAll('.app-checkbox:checked');
        
        const status = statusSelect.value;
        if (!status) {
            Swal.fire({
                icon: 'warning',
                title: 'No Action Selected',
                text: 'Please select whether to Approve or Reject the selected applications.',
                confirmButtonColor: '#ca8a04',
                customClass: { popup: 'rounded-4' }
            });
            return;
        }

        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        
        // Show confirm dialog
        const result = await Swal.fire({
            title: `Bulk Process Applications?`,
            text: `You are about to set ${ids.length} application(s) status to "${status}". This action will notify all selected applicants.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0F5934',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, proceed',
            customClass: { popup: 'rounded-4' }
        });

        if (!result.isConfirmed) return;

        // Perform AJAX request
        Swal.fire({
            title: 'Processing Request',
            html: 'Updating status and sending notification emails in background...',
            allowOutsideClick: false,
            showConfirmButton: false,
            customClass: { popup: 'rounded-4' },
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch("{{ route('admin.applications.bulk-action') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    application_ids: ids,
                    status: status,
                    remarks: remarksInput.value
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Bulk Process Completed',
                    text: data.message,
                    confirmButtonColor: '#0F5934',
                    customClass: { popup: 'rounded-4' }
                });
                
                // Hide actions bar
                document.getElementById('bulkStatusSelect').value = '';
                document.getElementById('bulkRemarksInput').value = '';
                updateBulkActionBar();
                
                // Reload current page of queue
                const activePage = document.querySelector('.pagination .active span')?.textContent || 1;
                reloadQueue(activePage);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Bulk Processing Failed',
                    text: data.message || 'An error occurred while processing bulk request.',
                    confirmButtonColor: '#dc2626',
                    customClass: { popup: 'rounded-4' }
                });
            }
        } catch (error) {
            console.error('Bulk Action Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Request Failed',
                text: 'A connection issue occurred. Please try again.',
                confirmButtonColor: '#dc2626',
                customClass: { popup: 'rounded-4' }
            });
        }
    }
</script>
@endpush