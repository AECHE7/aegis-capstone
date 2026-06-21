@extends('layouts.app')

@section('title', 'OSA Admin Dashboard | A.E.G.I.S.')

@section('content')
<div class="container-fluid px-md-5 mb-5">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-warning h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Pending Review</div>
                        <h2 class="fw-bold mb-0">{{ $pendingCount }}</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="fa-solid fa-hourglass-half fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-success h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Verified Scholars</div>
                        <h2 class="fw-bold mb-0 text-success">{{ $approvedCount }}</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fa-solid fa-user-graduate fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-danger h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Anomalies Detected</div>
                        <h2 class="fw-bold mb-0 text-danger">{{ $rejectedCount }}</h2>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                        <i class="fa-solid fa-shield-virus fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-info bg-dark text-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-gray-400 small fw-bold text-uppercase tracking-wide mb-1" style="color: #94a3b8;">Avg. Fraud Score</div>
                        <h2 class="fw-bold mb-0 text-info">{{ $avgFraudScore }}<span class="fs-5 text-muted">%</span></h2>
                    </div>
                    <div class="p-3">
                        <i class="fa-solid fa-microchip fa-2x text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-users-viewfinder text-primary me-2"></i> Applicant Evaluation Queue</h5>
            
            <div class="input-group w-25">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control bg-light border-start-0" placeholder="Search ID or Program...">
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase tracking-wide">
                    <tr>
                        <th class="ps-4 py-3">Ref ID</th>
                        <th class="py-3">Applicant Name</th>
                        <th class="py-3">Program / Grant</th>
                        <th class="py-3 text-center">GWA</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3">Date Submitted</th>
                        <th class="pe-4 py-3 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $app)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">APP-{{ $app->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-2">
                                    <i class="fa-solid fa-user text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $app->user->name ?? 'Unknown' }}</div>
                                    <div class="small text-muted">{{ $app->user->profile->clsu_id_number ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="fw-medium">{{ $app->program_name }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2 py-1"><i class="fa-solid fa-graduation-cap text-muted me-1"></i> {{ $app->gwa }}</span>
                        </td>
                        <td class="text-center">
                            @if($app->status == 'Pending')
                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-2 rounded-pill"><i class="fa-solid fa-circle-notch fa-spin me-1"></i> Pending</span>
                            @elseif($app->status == 'Approved')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill"><i class="fa-solid fa-check me-1"></i> Approved</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill"><i class="fa-solid fa-times me-1"></i> Rejected</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $app->created_at->format('M d, Y') }}</td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('admin.review', $app->id) }}" class="btn btn-sm" style="background-color: var(--clsu-green); color: white; border-radius: 8px;">
                                Evaluate <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($applications->isEmpty())
        <div class="text-center py-5">
            <i class="fa-solid fa-inbox fa-3x text-muted opacity-25 mb-3"></i>
            <h6 class="text-muted">The queue is completely empty!</h6>
        </div>
        @endif
        
        <div class="card-footer bg-white border-0 p-3">
            {{ $applications->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection