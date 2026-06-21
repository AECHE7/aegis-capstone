@extends('layouts.app')

@section('title', 'Program Manager | A.E.G.I.S.')

@section('content')
<div class="container-fluid px-md-5 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-list-check text-primary me-2"></i> Scholarship Manager</h4>
            <p class="text-muted small mb-0">Create new grants and toggle their availability to students.</p>
        </div>
        <button class="btn fw-bold shadow-sm" style="background-color: var(--clsu-green); color: white; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#newProgramModal">
            <i class="fa-solid fa-plus me-1"></i> New Program
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Program Name</th>
                        <th class="py-3">Description</th>
                        <th class="py-3 text-center">GWA Requirement</th>
                        <th class="py-3 text-center">Current Status</th>
                        <th class="pe-4 py-3 text-end">Quick Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scholarships as $scholarship)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $scholarship->name }}</td>
                        <td class="small text-muted" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $scholarship->description }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2 py-1"><i class="fa-solid fa-star text-warning me-1"></i> {{ $scholarship->min_gwa_required }} or lower</span>
                        </td>
                        <td class="text-center">
                            @if($scholarship->status == 'Active')
                                <span class="badge bg-success px-3 py-2 rounded-pill">Open / Active</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 rounded-pill">Closed</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <form action="{{ route('superadmin.scholarships.toggle', $scholarship->id) }}" method="POST">
                                @csrf
                                @if($scholarship->status == 'Active')
                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3">Close Program</button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3">Open Program</button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($scholarships->isEmpty())
            <div class="text-center py-5">
                <p class="text-muted">No scholarship programs exist yet.</p>
            </div>
        @endif
    </div>

</div>

<div class="modal fade" id="newProgramModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus-circle text-success me-2"></i> Create Scholarship</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('superadmin.scholarships.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Program Name</label>
                        <input type="text" name="name" class="form-control bg-light" required placeholder="e.g., DOST-SEI Merit">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">GWA Requirement (Maximum allowed)</label>
                        <input type="number" step="0.01" name="min_gwa_required" class="form-control bg-light" required placeholder="e.g., 1.75">
                        <div class="form-text small">Students with grades higher than this number will be auto-blocked.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Description</label>
                        <textarea name="description" class="form-control bg-light" rows="3" required placeholder="Brief description of the grant..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn fw-bold px-4" style="background-color: var(--clsu-green); color: white;">Save Program</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection