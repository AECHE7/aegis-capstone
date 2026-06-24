@extends('layouts.app')

@section('title', 'Program Manager | A.E.G.I.S.')
@section('page-title', 'Scholarship Manager')
@section('page-subtitle', 'Create grants and manage program availability for students')

@section('content')

<div class="d-flex justify-content-end mb-4">
    <button class="btn fw-bold px-4" 
            style="background: linear-gradient(135deg, var(--clsu-green), #16703f); color: white; border-radius: 10px; box-shadow: 0 4px 12px rgba(15,89,52,0.25);"
            data-bs-toggle="modal" data-bs-target="#newProgramModal">
        <i class="fa-solid fa-plus me-1"></i> New Program
    </button>
</div>

<div class="card" style="border-radius: 20px; overflow: hidden;">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Program Name</th>
                    <th>Description</th>
                    <th class="text-center">Max GWA</th>
                    <th class="text-center">Status</th>
                    <th class="pe-4 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scholarships as $scholarship)
                <tr>
                    <td class="ps-4">
                        <div class="fw-semibold text-dark">{{ $scholarship->name }}</div>
                    </td>
                    <td>
                        <span class="text-muted small">{{ Str::limit($scholarship->description, 60) }}</span>
                    </td>
                    <td class="text-center">
                        <span style="background:#fef9c3;color:#a16207;border:1px solid #fde047;border-radius:20px;padding:3px 12px;font-size:0.78rem;font-weight:700;">
                            <i class="fa-solid fa-star me-1" style="font-size:0.6rem;"></i> ≤ {{ $scholarship->min_gwa_required }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($scholarship->status == 'Active')
                            <span class="status-badge approved">
                                <i class="fa-solid fa-circle-dot" style="font-size:0.5rem;"></i> Open
                            </span>
                        @else
                            <span class="status-badge rejected">
                                <i class="fa-solid fa-circle-dot" style="font-size:0.5rem;"></i> Closed
                            </span>
                        @endif
                    </td>
                    <td class="pe-4 text-end">
                        <form action="{{ route('superadmin.scholarships.toggle', $scholarship->id) }}" method="POST">
                            @csrf
                            @if($scholarship->status == 'Active')
                                <button type="submit" class="btn btn-sm fw-semibold rounded-pill px-3"
                                        style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;font-size:0.78rem;">
                                    <i class="fa-solid fa-lock me-1"></i> Close
                                </button>
                            @else
                                <button type="submit" class="btn btn-sm fw-semibold rounded-pill px-3"
                                        style="background:#dcfce7;color:#15803d;border:1px solid #86efac;font-size:0.78rem;">
                                    <i class="fa-solid fa-lock-open me-1"></i> Open
                                </button>
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
        <div style="width:72px;height:72px;border-radius:18px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <i class="fa-solid fa-folder-open fa-2x" style="color:#cbd5e1;"></i>
        </div>
        <h6 class="fw-bold text-muted">No Programs Yet</h6>
        <p class="text-muted small">Click "New Program" to create the first scholarship grant.</p>
    </div>
    @endif
</div>

{{-- New Program Modal --}}
<div class="modal fade" id="newProgramModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f1f12, #0F5934); padding: 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0">
                        <i class="fa-solid fa-plus-circle text-warning me-2"></i> Create Scholarship Program
                    </h5>
                    <small class="text-white-50">Define a new grant for eligible students</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('superadmin.scholarships.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="programName">Program Name</label>
                        <input type="text" name="name" id="programName" class="form-control" required placeholder="e.g., DOST-SEI Merit Scholarship" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="gwaRequirement">Maximum GWA Requirement</label>
                        <input type="number" step="0.01" min="1.00" max="5.00" name="min_gwa_required" id="gwaRequirement" class="form-control" required placeholder="e.g., 1.75">
                        <div class="form-text small">Students with a GWA higher than this value will be blocked from applying.</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-muted" for="programDesc">Program Description</label>
                        <textarea name="description" id="programDesc" class="form-control" rows="3" required
                                  placeholder="Brief overview of grant requirements and benefits..."
                                  style="resize:none;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light fw-semibold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn fw-bold rounded-pill px-5"
                            style="background: linear-gradient(135deg, var(--clsu-gold), #e09500); color: #1a1a00;">
                        <i class="fa-solid fa-save me-1"></i> Save Program
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection