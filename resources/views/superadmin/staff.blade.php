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
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td class="ps-4 py-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-3" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #e2e8f0, #cbd5e1); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #475569;">
                                {{ strtoupper(substr($staff->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">{{ $staff->name }}</div>
                                <div class="mt-1 d-flex flex-wrap gap-1">
                                    @forelse($staff->scholarships as $s)
                                        <span class="badge bg-success text-white px-2 py-0.5 rounded" style="font-size: 0.7rem; font-weight: 500;">
                                            {{ $s->name }}
                                        </span>
                                    @empty
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 rounded" style="font-size: 0.7rem; font-weight: 500;">
                                            No assignments
                                        </span>
                                    @endforelse
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
                                <form action="{{ route('superadmin.staff.revoke', $staff->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate/revoke this staff member?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger border border-danger text-white" style="border-radius: 8px; padding: 5px 10px;" title="Deactivate/Revoke Access">
                                        <i class="fa-solid fa-user-slash"></i> <span class="small fw-semibold ms-1">Revoke</span>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('superadmin.staff.reactivate', $staff->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success border border-success text-white" style="border-radius: 8px; padding: 5px 10px;" title="Reactivate Access">
                                        <i class="fa-solid fa-user-check"></i> <span class="small fw-semibold ms-1">Reactivate</span>
                                    </button>
                                </form>
                            @endif
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
                        <label class="form-label fw-semibold small text-muted" for="staffEmail">Institutional Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 bg-white" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-envelope text-muted small"></i></span>
                            <input type="email" name="email" id="staffEmail" class="form-control border-start-0" style="border-radius: 0 10px 10px 0;" required placeholder="e.g., janesmith@clsu.edu.ph" value="{{ old('email') }}" autocomplete="email">
                        </div>
                        <div class="form-text small">Email must end with @clsu.edu.ph or @clsu2.edu.ph.</div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
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
