@extends('layouts.app')

@section('title', 'Director Analytics | A.E.G.I.S.')

@section('content')
<div class="container-fluid px-md-5 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-chart-line text-primary me-2"></i> System Analytics</h4>
            <p class="text-muted small mb-0">High-level overview of system metrics and evaluator audit logs.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-white shadow-sm h-100 p-4 border-start border-4 border-primary">
                <div class="text-muted small fw-bold text-uppercase mb-1">Registered Students</div>
                <div class="d-flex justify-content-between align-items-end">
                    <h2 class="fw-bold text-dark mb-0">{{ $totalStudents }}</h2>
                    <i class="fa-solid fa-users fa-2x text-primary opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-white shadow-sm h-100 p-4 border-start border-4 border-success">
                <div class="text-muted small fw-bold text-uppercase mb-1">Total Submissions</div>
                <div class="d-flex justify-content-between align-items-end">
                    <h2 class="fw-bold text-dark mb-0">{{ $submissionCount }}</h2>
                    <i class="fa-solid fa-file-invoice fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-white shadow-sm h-100 p-4 border-start border-4 border-danger">
                <div class="text-muted small fw-bold text-uppercase mb-1">Anomalies Detected</div>
                <div class="d-flex justify-content-between align-items-end">
                    <h2 class="fw-bold text-dark mb-0">{{ $anomaliesDetected }}</h2>
                    <i class="fa-solid fa-shield-virus fa-2x text-danger opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark shadow-sm h-100 p-4 border-start border-4 border-info">
                <div class="text-gray-400 small fw-bold text-uppercase mb-1" style="color: #94a3b8;">Avg AI Fraud Score</div>
                <div class="d-flex justify-content-between align-items-end">
                    <h2 class="fw-bold text-info mb-0">{{ $avgFraudScore }}%</h2>
                    <i class="fa-solid fa-microchip fa-2x text-info opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-bottom p-4">
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-clipboard-list text-primary me-2"></i> System Audit Trail & Evaluation Log</h5>
        </div>
        
        <div class="card-body p-0">
            @if($recentEvaluations->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-25"></i>
                    <p>No recent evaluations found in the system.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Timestamp</th>
                                <th class="py-3">Action Taken</th>
                                <th class="py-3">Reference</th>
                                <th class="py-3">Applicant</th>
                                <th class="py-3">Program</th>
                                <th class="pe-4 py-3 text-end">Evaluator ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentEvaluations as $eval)
                            <tr>
                                <td class="ps-4 small text-muted">{{ $eval->updated_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    @if($eval->status == 'Approved')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 rounded-pill"><i class="fa-solid fa-check me-1"></i> Approved</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1 rounded-pill"><i class="fa-solid fa-times me-1"></i> Rejected</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-dark">APP-{{ $eval->id }}</td>
                                <td class="fw-medium">{{ $eval->user->name ?? 'Unknown' }}</td>
                                <td class="small text-muted">{{ $eval->program_name }}</td>
                                <td class="pe-4 text-end"><span class="badge bg-secondary rounded-pill"><i class="fa-solid fa-user-shield me-1"></i> Admin #{{ $eval->evaluated_by }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection