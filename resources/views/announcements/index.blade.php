@extends('layouts.app')

@section('title', 'Manage Announcements | A.E.G.I.S.')
@section('page-title', 'Announcements Manager')
@section('page-subtitle', 'Broadcast official guidelines, updates, and schedule notifications to all student applicants')

@section('content')

<div class="d-flex justify-content-end align-items-center mb-4">
    <button class="btn fw-bold px-4" 
            style="background: linear-gradient(135deg, var(--clsu-green), #16703f); color: white; border-radius: 10px; box-shadow: 0 4px 12px rgba(15,89,52,0.25);"
            data-bs-toggle="modal" data-bs-target="#newAnnouncementModal">
        <i class="fa-solid fa-bullhorn me-1"></i> Publish Announcement
    </button>
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
                        <span class="text-muted small monospace-data">
                            {{ $announcement->created_at->format('M d, Y h:i A') }}
                        </span>
                    </td>
                    <td class="pe-4 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger fw-semibold rounded-pill px-3"
                                onclick="deleteAnnouncement({{ $announcement->id }})" style="font-size:0.78rem;">
                            <i class="fa-solid fa-trash-can me-1"></i> Delete
                        </button>
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
                        <textarea name="content" id="announcementContent" class="form-control" rows="8" required 
                                  placeholder="Provide the complete announcement announcement detail text here..."
                                  style="resize: none;"></textarea>
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
