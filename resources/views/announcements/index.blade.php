@extends('layouts.app')

@section('title', 'Manage Announcements | A.E.G.I.S.')
@section('page-title', 'Announcements Manager')
@section('page-subtitle', 'Broadcast official guidelines, updates, and schedule notifications to all student applicants')

@section('content')

{{-- Top Metrics Deck --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card p-3 border-0 shadow-sm d-flex flex-row align-items-center gap-3" style="border-radius: 14px;">
            <div style="width:44px;height:44px;border-radius:12px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-bullhorn text-primary fs-5"></i>
            </div>
            <div>
                <div class="small fw-bold text-muted text-uppercase" style="font-size:0.68rem; letter-spacing:0.5px;">Total Broadcasts</div>
                <h5 class="fw-bold text-dark mb-0">{{ $announcements->total() }}</h5>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card p-3 border-0 shadow-sm d-flex flex-row align-items-center gap-3" style="border-radius: 14px;">
            <div style="width:44px;height:44px;border-radius:12px;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-circle-check text-success fs-5"></i>
            </div>
            <div>
                <div class="small fw-bold text-muted text-uppercase" style="font-size:0.68rem; letter-spacing:0.5px;">Active on Portal</div>
                <h5 class="fw-bold text-success mb-0">Live</h5>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card p-3 border-0 shadow-sm d-flex align-items-center justify-content-center" style="border-radius: 14px; background: linear-gradient(135deg, var(--clsu-green), #16703f);">
            <button class="btn fw-bold text-white w-100 p-1 border-0" data-bs-toggle="modal" data-bs-target="#newAnnouncementModal">
                <i class="fa-solid fa-plus-circle me-1"></i> New Announcement
            </button>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th class="ps-4">Title</th>
                    <th>Content Preview</th>
                    <th>Author</th>
                    <th>Published At</th>
                    <th class="pe-4 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $announcement)
                <tr id="announcement-row-{{ $announcement->id }}" class="border-bottom">
                    <td class="ps-4">
                        <div class="fw-bold text-dark">{{ $announcement->title }}</div>
                    </td>
                    <td>
                        <span class="text-muted small">{{ Str::limit($announcement->content, 90) }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="eval-avatar">
                                {{ strtoupper(substr($announcement->author->name ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold text-dark small">{{ $announcement->author->name ?? 'System' }}</div>
                                <div class="text-muted text-uppercase" style="font-size: 0.65rem; font-weight: 750;">
                                    {{ $announcement->author->role ?? 'Staff' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <span class="text-muted small monospace-data">
                                {{ $announcement->created_at->format('M d, Y h:i A') }}
                            </span>
                        </div>
                        @if($announcement->scheduled_publish_at)
                            <div class="mt-1">
                                @if($announcement->scheduled_publish_at->isFuture())
                                    <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">
                                        <i class="fa-solid fa-clock me-1"></i> Publish: {{ $announcement->scheduled_publish_at->format('M d, Y h:i A') }}
                                    </span>
                                @else
                                    <span class="badge bg-success" style="font-size: 0.65rem;">
                                        <i class="fa-solid fa-circle-check me-1"></i> Published: {{ $announcement->scheduled_publish_at->format('M d, Y h:i A') }}
                                    </span>
                                @endif
                            </div>
                        @endif
                        @if($announcement->scheduled_delete_at)
                            <div class="mt-1">
                                @if($announcement->scheduled_delete_at->isPast())
                                    <span class="badge bg-secondary" style="font-size: 0.65rem;">
                                        <i class="fa-solid fa-eye-slash me-1"></i> Expired: {{ $announcement->scheduled_delete_at->format('M d, Y h:i A') }}
                                    </span>
                                @else
                                    <span class="badge bg-danger" style="font-size: 0.65rem;">
                                        <i class="fa-solid fa-hourglass-half me-1"></i> Expires: {{ $announcement->scheduled_delete_at->format('M d, Y h:i A') }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="pe-4 text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-3"
                                    data-id="{{ $announcement->id }}"
                                    data-title="{{ $announcement->title }}"
                                    data-content="{{ $announcement->content }}"
                                    data-publish="{{ $announcement->scheduled_publish_at ? $announcement->scheduled_publish_at->format('Y-m-d\TH:i') : '' }}"
                                    data-delete="{{ $announcement->scheduled_delete_at ? $announcement->scheduled_delete_at->format('Y-m-d\TH:i') : '' }}"
                                    onclick="openEditModal(this)" style="font-size:0.78rem;">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger fw-semibold rounded-pill px-3"
                                    onclick="deleteAnnouncement({{ $announcement->id }})" style="font-size:0.78rem;">
                                <i class="fa-solid fa-trash-can me-1"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div style="width:72px;height:72px;border-radius:18px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <i class="fa-solid fa-bullhorn fa-2x text-muted" style="opacity:0.4;"></i>
                        </div>
                        <h6 class="fw-bold text-muted">No Announcements Published</h6>
                        <p class="text-muted small mb-0">Official updates broadcasted to students will appear here.</p>
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

{{-- New Announcement Modal --}}
<div class="modal fade" id="newAnnouncementModal" tabindex="-1" aria-labelledby="newAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #0f1f12, #0F5934); padding: 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="newAnnouncementModalLabel">
                        <i class="fa-solid fa-bullhorn text-warning me-2"></i> Publish New Announcement
                    </h5>
                    <small class="text-white-50">This announcement will be pinned to all student dashboards instantly</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="announcementForm" onsubmit="publishAnnouncement(event)">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="announcementTitle">Announcement Title</label>
                        <input type="text" name="title" id="announcementTitle" class="form-control" required placeholder="e.g., Mandatory Guidelines Update for GAD Scholarship Applications" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="announcementContent">Content Details</label>
                        <textarea name="content" id="announcementContent" class="form-control" rows="6" required 
                                  placeholder="Provide the complete announcement announcement detail text here..."
                                  style="resize: none;"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted" for="scheduledPublishAt">Publish Date & Time (Optional)</label>
                            <input type="datetime-local" name="scheduled_publish_at" id="scheduledPublishAt" class="form-control">
                            <small class="text-muted" style="font-size: 0.72rem;">Leave blank to publish instantly.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted" for="scheduledDeleteAt">Expiration/Delete Date & Time (Optional)</label>
                            <input type="datetime-local" name="scheduled_delete_at" id="scheduledDeleteAt" class="form-control">
                            <small class="text-muted" style="font-size: 0.72rem;">Auto-hide from student feed after this time.</small>
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
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #0f1f12, #0F5934); padding: 1.5rem;">
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
                        <input type="text" name="title" id="editAnnouncementTitle" class="form-control" required placeholder="e.g., Mandatory Guidelines Update for GAD Scholarship Applications" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="editAnnouncementContent">Content Details</label>
                        <textarea name="content" id="editAnnouncementContent" class="form-control" rows="6" required 
                                  placeholder="Provide the complete announcement announcement detail text here..."
                                  style="resize: none;"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted" for="editScheduledPublishAt">Publish Date & Time (Optional)</label>
                            <input type="datetime-local" name="scheduled_publish_at" id="editScheduledPublishAt" class="form-control">
                            <small class="text-muted" style="font-size: 0.72rem;">Leave blank to publish instantly.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted" for="editScheduledDeleteAt">Expiration/Delete Date & Time (Optional)</label>
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

@endsection

@push('scripts')
<script>
    async function publishAnnouncement(event) {
        event.preventDefault();
        const form = document.getElementById('announcementForm');
        const submitBtn = document.getElementById('submitBtn');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Broadcasting...';

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
                Swal.fire({
                    icon: 'success',
                    title: 'Published!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: data.message || 'An error occurred.'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Publish Broadcast';
            }
        } catch (error) {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An unexpected connection error occurred.'
            });
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
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: data.message || 'An error occurred.'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save Changes';
            }
        } catch (error) {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An unexpected connection error occurred.'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save Changes';
        }
    }

    function deleteAnnouncement(id) {
        Swal.fire({
            title: 'Delete Announcement?',
            text: "This will remove the announcement from all dashboards permanently.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#475569',
            confirmButtonText: 'Yes, delete it!'
        }).then(async (result) => {
            if (result.isConfirmed) {
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message,
                            timer: 1000,
                            showConfirmButton: false
                        });
                        
                        const row = document.getElementById(`announcement-row-${id}`);
                        if (row) {
                            row.remove();
                            // If table is now empty, reload to show empty state
                            const rows = document.querySelectorAll('tbody tr');
                            if (rows.length === 0) location.reload();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Deletion failed.'
                        });
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected connection error occurred.'
                    });
                }
            }
        });
    }
</script>
@endpush
