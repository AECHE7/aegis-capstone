@extends('layouts.app')

@section('title', 'System Trash | A.E.G.I.S.')
@section('page-title', 'System Trash')
@section('page-subtitle', 'Restore or permanently delete system records')

@section('content')

<div class="mb-4">
    <a href="{{ route('superadmin.scholarships') }}" class="btn btn-sm btn-light fw-semibold rounded-pill px-3"
       style="font-size:0.8rem;border:1px solid #e2e8f0;">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Scholarships
    </a>
</div>

{{-- Trash Navigation Tabs --}}
<div class="card p-3 mb-4" style="border-radius: 12px; border: 1px solid var(--border-color); background: var(--card-bg); box-shadow: none;">
    <ul class="nav nav-pills gap-2" id="trashTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4 py-2" id="apps-tab" data-bs-toggle="tab" data-bs-target="#apps-pane" type="button" role="tab" style="border-radius: 8px;">
                <i class="fa-solid fa-file-invoice me-1"></i> Student Applications ({{ $applications->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-2 text-danger" id="scholarships-tab" data-bs-toggle="tab" data-bs-target="#scholarships-pane" type="button" role="tab" style="border-radius: 8px;">
                <i class="fa-solid fa-graduation-cap me-1"></i> Scholarship Programs ({{ $scholarships->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-2 text-secondary" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff-pane" type="button" role="tab" style="border-radius: 8px;">
                <i class="fa-solid fa-users-gear me-1"></i> Staff Accounts ({{ $staffMembers->count() }})
            </button>
        </li>
    </ul>
</div>

{{-- Tab Contents --}}
<div class="tab-content" id="trashTabsContent">
    
    {{-- 1. Student Applications --}}
    <div class="tab-pane fade show active" id="apps-pane" role="tabpanel">
        <div class="card" style="border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); background: var(--card-bg); box-shadow: none;">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="text-muted small fw-bold" style="background: var(--clsu-bg);">
                        <tr>
                            <th class="ps-4 py-3 text-nowrap">Ref ID</th>
                            <th class="py-3">Applicant Name</th>
                            <th class="py-3">Scholarship Program</th>
                            <th class="py-3 text-nowrap">Status</th>
                            <th class="py-3 text-nowrap">Deleted Date</th>
                            <th class="pe-4 py-3 text-end text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td class="ps-4 fw-bold text-dark monospace-data text-nowrap">APP-{{ $app->id }}</td>
                                <td class="fw-semibold text-dark">{{ $app->user->name ?? 'Unknown Student' }}</td>
                                <td class="text-muted">{{ $app->program_name }}</td>
                                <td class="text-nowrap">
                                    <span class="badge bg-secondary text-white text-nowrap">{{ $app->status }}</span>
                                </td>
                                <td class="text-muted small monospace-data text-nowrap">{{ $app->deleted_at->format('M d, Y · h:i A') }}</td>
                                <td class="pe-4 text-end text-nowrap">
                                    <div class="d-flex justify-content-end gap-2 flex-nowrap">
                                        <form action="{{ route('superadmin.applications.restore', $app->id) }}" method="POST" class="d-inline restore-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success text-white fw-bold px-3 text-nowrap" style="border-radius: 8px;">
                                                <i class="fa-solid fa-trash-arrow-up"></i> Restore
                                            </button>
                                        </form>
                                        <form action="{{ route('superadmin.applications.force-delete', $app->id) }}" method="POST" class="d-inline force-delete-form" data-type="application">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold px-3 text-nowrap" style="border-radius: 8px;">
                                                <i class="fa-solid fa-circle-xmark"></i> Permanent Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-2x mb-2 text-muted" style="opacity: 0.5;"></i>
                                    <div>No deleted applications in system trash.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 2. Scholarship Programs --}}
    <div class="tab-pane fade" id="scholarships-pane" role="tabpanel">
        <div class="card" style="border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); background: var(--card-bg); box-shadow: none;">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="text-muted small fw-bold" style="background: var(--clsu-bg);">
                        <tr>
                            <th class="ps-4 py-3 text-nowrap">Program Name</th>
                            <th class="py-3 text-nowrap">Min GWA</th>
                            <th class="py-3 text-center text-nowrap">Status When Closed</th>
                            <th class="py-3 text-nowrap">Deleted Date</th>
                            <th class="pe-4 py-3 text-end text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scholarships as $scholarship)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td class="ps-4 fw-semibold text-dark">{{ $scholarship->name }}</td>
                                <td class="monospace-data text-nowrap">≤ {{ $scholarship->min_gwa_required }}</td>
                                <td class="text-center text-nowrap">
                                    <span class="badge bg-secondary text-white text-nowrap">{{ $scholarship->status }}</span>
                                </td>
                                <td class="text-muted small monospace-data text-nowrap">{{ $scholarship->deleted_at->format('M d, Y · h:i A') }}</td>
                                <td class="pe-4 text-end text-nowrap">
                                    <div class="d-flex justify-content-end gap-2 flex-nowrap">
                                        <form action="{{ route('superadmin.scholarships.restore', $scholarship->id) }}" method="POST" class="d-inline restore-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success text-white fw-bold px-3 text-nowrap" style="border-radius: 8px;">
                                                <i class="fa-solid fa-trash-arrow-up"></i> Restore
                                            </button>
                                        </form>
                                        <form action="{{ route('superadmin.scholarships.force-delete', $scholarship->id) }}" method="POST" class="d-inline force-delete-form" data-type="scholarship">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold px-3 text-nowrap" style="border-radius: 8px;">
                                                <i class="fa-solid fa-circle-xmark"></i> Permanent Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-2x mb-2 text-muted" style="opacity: 0.5;"></i>
                                    <div>No deleted scholarship programs in system trash.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 3. Staff Accounts --}}
    <div class="tab-pane fade" id="staff-pane" role="tabpanel">
        <div class="card" style="border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); background: var(--card-bg); box-shadow: none;">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="text-muted small fw-bold" style="background: var(--clsu-bg);">
                        <tr>
                            <th class="ps-4 py-3 text-nowrap">Staff Name</th>
                            <th class="py-3 text-nowrap">Email</th>
                            <th class="py-3 text-nowrap">Deleted Date</th>
                            <th class="pe-4 py-3 text-end text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffMembers as $staff)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td class="ps-4 fw-semibold text-dark">{{ $staff->name }}</td>
                                <td class="text-muted monospace-data text-nowrap">{{ $staff->email }}</td>
                                <td class="text-muted small monospace-data text-nowrap">{{ $staff->deleted_at->format('M d, Y · h:i A') }}</td>
                                <td class="pe-4 text-end text-nowrap">
                                    <div class="d-flex justify-content-end gap-2 flex-nowrap">
                                        <form action="{{ route('superadmin.staff.restore', $staff->id) }}" method="POST" class="d-inline restore-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success text-white fw-bold px-3 text-nowrap" style="border-radius: 8px;">
                                                <i class="fa-solid fa-trash-arrow-up"></i> Restore
                                            </button>
                                        </form>
                                        <form action="{{ route('superadmin.staff.force-delete', $staff->id) }}" method="POST" class="d-inline force-delete-form" data-type="staff account">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold px-3 text-nowrap" style="border-radius: 8px;">
                                                <i class="fa-solid fa-circle-xmark"></i> Permanent Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-2x mb-2 text-muted" style="opacity: 0.5;"></i>
                                    <div>No deleted staff members in system trash.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    // ── AJAX Restoration Actions ──────────────────────
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('.restore-form');
        if (form) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Restoring...';

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
                    AegisAlert.toast({
                        icon: 'success',
                        title: data.message || 'Record successfully restored!'
                    });
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    AegisAlert.error({ title: 'Restore Failed', text: data.message });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Restore Error:', error);
                AegisAlert.error({ title: 'Error', text: 'An unexpected connection error occurred.' });
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }
    });

    // ── AJAX Force Delete Actions ─────────────────────
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('.force-delete-form');
        if (form) {
            e.preventDefault();
            const type = form.dataset.type || 'record';

            const confirmed = await AegisAlert.delete({
                title: 'Permanent Deletion?',
                text: `WARNING: Are you sure you want to permanently delete this ${type}? This action is irreversible and deletes all associated files and logs from database storage.`,
                confirmText: 'Yes, Permanently Delete'
            });

            if (confirmed) {
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
                        AegisAlert.toast({
                            icon: 'success',
                            title: data.message || 'Record permanently deleted.'
                        });
                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        AegisAlert.error({ title: 'Deletion Failed', text: data.message });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;
                    }
                } catch (error) {
                    console.error('Force Delete Error:', error);
                    AegisAlert.error({ title: 'Error', text: 'An unexpected connection error occurred.' });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            }
        }
    });
</script>
@endpush
