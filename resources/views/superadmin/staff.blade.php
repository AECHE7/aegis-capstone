@extends('layouts.app')

@section('title', 'Staff Management | A.E.G.I.S.')
@section('page-title', 'Staff Management')
@section('page-subtitle', 'Invite administrative staff members and track their activation status')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background: #e8f5e9; color: #2e7d32;">
    <i class="fa-solid fa-circle-check me-2"></i><strong>Success!</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="d-flex justify-content-end mb-4">
    <button class="btn fw-bold px-4 text-white" 
            style="background: linear-gradient(135deg, var(--clsu-green), #16703f); border-radius: 10px; box-shadow: 0 4px 12px rgba(15,89,52,0.25);"
            data-bs-toggle="modal" data-bs-target="#inviteStaffModal">
        <i class="fa-solid fa-user-plus me-1"></i> Invite Staff
    </button>
</div>

<div class="card border-0" style="border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white;">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light text-muted small uppercase fw-bold" style="background-color: #f8fafc;">
                <tr>
                    <th class="ps-4 py-3" style="font-size: 0.8rem; letter-spacing: 0.5px;">Name & Assignments</th>
                    <th class="py-3" style="font-size: 0.8rem; letter-spacing: 0.5px;">Email</th>
                    <th class="py-3 text-center" style="font-size: 0.8rem; letter-spacing: 0.5px;">Role</th>
                    <th class="py-3 text-center" style="font-size: 0.8rem; letter-spacing: 0.5px;">Status</th>
                    <th class="py-3 text-center" style="font-size: 0.8rem; letter-spacing: 0.5px;">Invitation Sent</th>
                    <th class="pe-4 py-3 text-end" style="font-size: 0.8rem; letter-spacing: 0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffList as $staff)
                <tr style="border-bottom: 1px solid var(--border-color);" data-staff-id="{{ $staff->id }}">
                    <td class="ps-4 py-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-3" style="width: 40px; height: 40px; border-radius: 50%; background: var(--clsu-bg); border: 1.5px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--text-main); font-size: 0.85rem;">
                                {{ strtoupper(substr($staff->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">{{ $staff->name }}</div>
                                <div class="mt-1 d-flex flex-wrap gap-1">
                                    @if($staff->role === 'superadmin')
                                        <span class="badge text-white px-2 rounded" style="font-size: 0.7rem; font-weight: 600; background-color: var(--clsu-green) !important;">
                                            All Programs (Director)
                                        </span>
                                    @else
                                        @forelse($staff->scholarships as $s)
                                            <span class="badge bg-success text-white px-2 py-0.5 rounded" style="font-size: 0.7rem; font-weight: 500;">
                                                {{ $s->name }}
                                            </span>
                                        @empty
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 rounded" style="font-size: 0.7rem; font-weight: 500;">
                                                No assignments
                                            </span>
                                        @endforelse
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3">
                        <span class="text-muted small">{{ $staff->email }}</span>
                    </td>
                    <td class="py-3 text-center">
                        <span class="badge rounded-pill bg-light text-secondary border px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                            {{ ucfirst($staff->role) }}
                        </span>
                    </td>
                    <td class="py-3 text-center">
                        @if(!$staff->is_active)
                            <span class="status-badge rejected d-inline-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem; color: #ef4444;"></i> Revoked / Inactive
                            </span>
                        @elseif($staff->email_verified_at)
                            <span class="status-badge approved d-inline-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem; color: #10b981;"></i> Active
                            </span>
                        @else
                            @if($staff->invitation && $staff->invitation->expires_at->isPast())
                                <span class="status-badge rejected d-inline-flex align-items-center gap-1">
                                    <i class="fa-solid fa-circle" style="font-size: 0.5rem; color: #ef4444;"></i> Expired
                                </span>
                            @else
                                <span class="status-badge pending d-inline-flex align-items-center gap-1">
                                    <i class="fa-solid fa-circle" style="font-size: 0.5rem; color: #f59e0b;"></i> Invited / Pending
                                </span>
                            @endif
                        @endif
                    </td>
                    <td class="py-3 text-center text-muted small">
                        @if($staff->invitation)
                            {{ $staff->invitation->created_at->diffForHumans() }}
                        @else
                            <span class="text-muted italic">—</span>
                        @endif
                    </td>
                    <td class="pe-4 py-3 text-end">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <!-- Assign button -->
                            <button class="btn btn-sm btn-light border" style="border-radius: 8px; padding: 5px 10px;" 
                                    data-bs-toggle="modal" data-bs-target="#editAssignmentsModal_{{ $staff->id }}" 
                                    title="Edit Assignments">
                                <i class="fa-solid fa-tasks text-primary"></i> <span class="small fw-semibold ms-1">Assign</span>
                            </button>

                            <!-- Toggle activation status form -->
                            @if($staff->is_active)
                                <form action="{{ route('superadmin.staff.revoke', $staff->id) }}" method="POST" class="d-inline revoke-staff-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger border border-danger text-white" style="border-radius: 8px; padding: 5px 10px;" title="Deactivate/Revoke Access">
                                        <i class="fa-solid fa-user-slash"></i> <span class="small fw-semibold ms-1">Revoke</span>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('superadmin.staff.reactivate', $staff->id) }}" method="POST" class="d-inline reactivate-staff-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success border border-success text-white" style="border-radius: 8px; padding: 5px 10px;" title="Reactivate Access">
                                        <i class="fa-solid fa-user-check"></i> <span class="small fw-semibold ms-1">Reactivate</span>
                                    </button>
                                </form>
                            @endif

                            <!-- Soft Delete staff form -->
                            <form action="{{ route('superadmin.staff.delete', $staff->id) }}" method="POST" class="d-inline delete-staff-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px; padding: 5px 10px;" title="Delete Staff Account">
                                    <i class="fa-solid fa-trash-can me-1"></i> <span class="small fw-semibold">Delete</span>
                                </button>
                            </form>
                        </div>

                        <!-- Edit Assignments Modal for each staff -->
                        <div class="modal fade" id="editAssignmentsModal_{{ $staff->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered text-start">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                                    <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f1f12, #0F5934); padding: 1.5rem;">
                                        <div>
                                            <h5 class="modal-title fw-bold text-white mb-0">
                                                <i class="fa-solid fa-tasks text-warning me-2"></i> Manage Assignments
                                            </h5>
                                            <small class="text-white-50">Assign scholarship programs to {{ $staff->name }}</small>
                                        </div>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('superadmin.staff.assign', $staff->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <label class="form-label fw-semibold small text-muted mb-2">Scholarship Programs</label>
                                            <div class="p-3 bg-light border" style="border-radius: 10px; max-height: 250px; overflow-y: auto;">
                                                @foreach($scholarships as $scholarship)
                                                    @php
                                                        $assigned = $staff->scholarships->contains($scholarship->id);
                                                    @endphp
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="scholarship_ids[]" value="{{ $scholarship->id }}" id="edit_scholarship_{{ $staff->id }}_{{ $scholarship->id }}" {{ $assigned ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="edit_scholarship_{{ $staff->id }}_{{ $scholarship->id }}">
                                                            {{ $scholarship->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                                @if($scholarships->isEmpty())
                                                    <div class="text-muted small">No active scholarship programs available.</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                            <button type="button" class="btn btn-light fw-semibold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn fw-bold rounded-pill px-5 text-white"
                                                    style="background: linear-gradient(135deg, var(--clsu-green), #16703f);">
                                                Save Assignments
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($staffList->isEmpty())
    <div class="text-center py-5">
        <div style="width:72px;height:72px;border-radius:18px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <i class="fa-solid fa-user-shield fa-2x" style="color:#cbd5e1;"></i>
        </div>
        <h6 class="fw-bold text-muted">No Staff Members Yet</h6>
        <p class="text-muted small">Invite administrative staff to start managing applications.</p>
    </div>
    @endif
</div>

{{-- Invite Staff Modal --}}
<div class="modal fade" id="inviteStaffModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f1f12, #0F5934); padding: 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0">
                        <i class="fa-solid fa-envelope-open-text text-warning me-2"></i> Invite Staff Member
                    </h5>
                    <small class="text-white-50">Create a staff account and send activation link</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('superadmin.staff.invite') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="staffName">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 bg-white" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-user text-muted small"></i></span>
                            <input type="text" name="name" id="staffName" class="form-control border-start-0" style="border-radius: 0 10px 10px 0;" required placeholder="e.g., Jane Smith" value="{{ old('name') }}" autocomplete="name">
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="staffRole">System Role</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 bg-white" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-user-shield text-muted small"></i></span>
                            <select name="role" id="staffRole" class="form-select border-start-0" style="border-radius: 0 10px 10px 0;" onchange="toggleScholarshipBlock(this.value)" required>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Staff (Admin)</option>
                                <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Director (SuperAdmin)</option>
                            </select>
                        </div>
                        @error('role')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="staffEmail">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 bg-white" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-envelope text-muted small"></i></span>
                            <input type="email" name="email" id="staffEmail" class="form-control border-start-0" style="border-radius: 0 10px 10px 0;" required placeholder="e.g., user@clsu.edu.ph" value="{{ old('email') }}" autocomplete="email">
                        </div>
                        <div class="form-text small" id="emailDomainHelpText">Email must end with @clsu.edu.ph or @clsu2.edu.ph.</div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0" id="scholarshipAssignmentBlock">
                        <label class="form-label fw-semibold small text-muted mb-2">Assign Scholarship Programs</label>
                        <div class="p-3 bg-light border" style="border-radius: 10px; max-height: 180px; overflow-y: auto;">
                            @foreach($scholarships as $scholarship)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="scholarship_ids[]" value="{{ $scholarship->id }}" id="invite_scholarship_{{ $scholarship->id }}">
                                    <label class="form-check-label small" for="invite_scholarship_{{ $scholarship->id }}">
                                        {{ $scholarship->name }}
                                    </label>
                                </div>
                            @endforeach
                            @if($scholarships->isEmpty())
                                <div class="text-muted small">No active scholarship programs available.</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light fw-semibold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn fw-bold rounded-pill px-5 text-white"
                            style="background: linear-gradient(135deg, var(--clsu-green), #16703f);">
                        <i class="fa-solid fa-paper-plane me-1"></i> Send Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->has('name') || $errors->has('email'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('inviteStaffModal'));
        myModal.show();
    });
</script>
@endif

@endsection

@push('scripts')
<script>
    function toggleScholarshipBlock(role) {
        const block = document.getElementById('scholarshipAssignmentBlock');
        const helpText = document.getElementById('emailDomainHelpText');
        if (role === 'superadmin') {
            block.style.display = 'none';
            helpText.innerText = 'Any valid email address is accepted for Directors.';
        } else {
            block.style.display = 'block';
            helpText.innerText = 'Email must end with @clsu.edu.ph or @clsu2.edu.ph.';
        }
    }

    // ── AJAX Staff Invitation ─────────────────────────
    const inviteForm = document.querySelector('#inviteStaffModal form');
    if (inviteForm) {
        inviteForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = inviteForm.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Inviting...';

            // Clear errors
            inviteForm.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            inviteForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            try {
                const response = await fetch(inviteForm.action, {
                    method: 'POST',
                    body: new FormData(inviteForm),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (response.status === 422) {
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Please correct validation issues.', confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                    
                    if (data.errors) {
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const input = inviteForm.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = messages[0];
                                input.parentElement.parentElement.appendChild(errorDiv);
                            }
                        }
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                } else if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Invitation Sent!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4' }
                    });
                    
                    const modalEl = document.getElementById('inviteStaffModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Invite Staff Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        });
    }

    // ── AJAX Staff Assignment Update ──────────────────
    document.querySelectorAll('.modal form').forEach(form => {
        if (form.action && form.action.includes('/assign')) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalHtml = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Saving...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Assignments Saved',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-4' }
                        });
                        
                        const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                        if (modal) modal.hide();
                        
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;
                    }
                } catch (error) {
                    console.error('Save Assignments Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            });
        }
    });

    // ── AJAX Revoke / Reactivate ──────────────────────
    document.querySelector('table').addEventListener('submit', async (e) => {
        const form = e.target;
        if (form.action && (form.action.includes('/revoke') || form.action.includes('/reactivate'))) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Access Updated',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4' }
                    });

                    // Update UI status badge and button inline
                    const row = form.closest('tr');
                    const statusCell = row.querySelector('.status-badge').parentElement;
                    const actionCell = form.parentElement;

                    if (data.is_active) {
                        statusCell.innerHTML = `
                            <span class="status-badge approved">
                                <i class="fa-solid fa-circle-check" style="font-size:0.5rem;"></i> Active
                            </span>
                        `;
                        actionCell.innerHTML = `
                            <form action="${form.action.replace('/reactivate', '/revoke')}" method="POST" class="d-inline">
                                <input type="hidden" name="_token" value="${form.querySelector('[name="_token"]').value}">
                                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold px-2 py-1.5" style="border-radius: 8px; font-size: 0.75rem;" title="Deactivate Staff Account">
                                    <i class="fa-solid fa-user-slash"></i> Revoke
                                </button>
                            </form>
                            <button class="btn btn-sm btn-outline-primary fw-bold px-2 py-1.5 ms-1" style="border-radius: 8px; font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#assignModal_${row.dataset.staffId}">
                                <i class="fa-solid fa-tasks"></i> Assign
                            </button>
                        `;
                    } else {
                        statusCell.innerHTML = `
                            <span class="status-badge rejected">
                                <i class="fa-solid fa-circle-xmark" style="font-size:0.5rem;"></i> Suspended
                            </span>
                        `;
                        actionCell.innerHTML = `
                            <form action="${form.action.replace('/revoke', '/reactivate')}" method="POST" class="d-inline">
                                <input type="hidden" name="_token" value="${form.querySelector('[name="_token"]').value}">
                                <button type="submit" class="btn btn-sm btn-outline-success fw-bold px-2 py-1.5" style="border-radius: 8px; font-size: 0.75rem;" title="Activate Staff Account">
                                    <i class="fa-solid fa-user-check"></i> Reactivate
                                </button>
                            </form>
                            <button class="btn btn-sm btn-outline-primary fw-bold px-2 py-1.5 ms-1" style="border-radius: 8px; font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#assignModal_${row.dataset.staffId}">
                                <i class="fa-solid fa-tasks"></i> Assign
                            </button>
                        `;
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Staff Update Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }
    });

    // ── Delete Staff Member ──────────────────────────
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('.delete-staff-form');
        if (form) {
            e.preventDefault();
            const result = await Swal.fire({
                title: 'Delete Staff Account?',
                text: "This will move the staff account to the System Trash. They won't be able to log in anymore.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Deleting...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;
                    }
                } catch (error) {
                    console.error('Delete Staff Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            }
        }
    });
</script>
@endpush
