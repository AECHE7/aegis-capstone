@extends('layouts.app')

@section('title', 'Student Dashboard | A.E.G.I.S.')

@section('content')
<div class="container-fluid px-md-5 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name }}!</h4>
            <p class="text-muted small mb-0">Track your scholarship applications and requirements here.</p>
        </div>
        <a href="{{ route('student.apply') }}" class="btn fw-bold shadow-sm" style="background-color: var(--clsu-gold); color: #0f172a; border-radius: 8px;">
            <i class="fa-solid fa-plus me-1"></i> New Application
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            @if($application)
                <div class="card shadow-sm border-0 rounded-4 p-0 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-secondary mb-2">Ref: APP-{{ $application->id }}</span>
                            <h5 class="fw-bold text-dark mb-0">{{ $application->program_name }}</h5>
                        </div>
                        <div class="text-end text-muted small">
                            <i class="fa-solid fa-calendar me-1"></i> Submitted on<br>
                            <strong class="text-dark">{{ $application->created_at->format('M d, Y - h:i A') }}</strong>
                        </div>
                    </div>
                    
                    <div class="card-body p-5 bg-light">
                        <div class="text-center mb-5">
                            <h6 class="fw-bold text-uppercase tracking-wide text-muted mb-4">Application Status Tracker</h6>
                            
                            <div class="position-relative m-auto" style="max-width: 600px;">
                                <div class="progress" style="height: 4px; position: absolute; top: 50%; left: 0; right: 0; transform: translateY(-50%); z-index: 1;">
                                    <div class="progress-bar" role="progressbar" 
                                        style="background-color: var(--clsu-green); width: {{ $application->status == 'Pending' ? '50%' : '100%' }};">
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                                    <div class="text-center">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto mb-2" style="width: 40px; height: 40px;">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div class="small fw-bold text-success">Submitted</div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <div class="bg-{{ $application->status == 'Pending' ? 'warning text-dark' : 'success text-white' }} rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto mb-2" style="width: 40px; height: 40px; border: 3px solid white;">
                                            <i class="fa-solid fa-{{ $application->status == 'Pending' ? 'hourglass-half fa-spin' : 'check' }}"></i>
                                        </div>
                                        <div class="small fw-bold text-{{ $application->status == 'Pending' ? 'warning' : 'success' }}">OSA Review</div>
                                    </div>
                                    
                                    <div class="text-center">
                                        @if($application->status == 'Pending')
                                            <div class="bg-white text-muted rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto mb-2" style="width: 40px; height: 40px; border: 3px solid #e2e8f0;">
                                                <i class="fa-solid fa-lock"></i>
                                            </div>
                                            <div class="small fw-bold text-muted">Decision</div>
                                        @elseif($application->status == 'Approved')
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-lg mx-auto mb-2" style="width: 45px; height: 45px; border: 3px solid white;">
                                                <i class="fa-solid fa-award"></i>
                                            </div>
                                            <div class="small fw-bold text-success fs-6">Approved!</div>
                                        @else
                                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-lg mx-auto mb-2" style="width: 45px; height: 45px; border: 3px solid white;">
                                                <i class="fa-solid fa-times"></i>
                                            </div>
                                            <div class="small fw-bold text-danger fs-6">Rejected</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($application->status != 'Pending')
                            <div class="alert alert-{{ $application->status == 'Approved' ? 'success' : 'danger' }} border-0 shadow-sm mt-4">
                                <h6 class="fw-bold mb-2"><i class="fa-solid fa-comment-dots me-2"></i> Evaluator Remarks:</h6>
                                <p class="mb-0 text-dark">{{ $application->remarks ?? 'Your application has been processed by the Office of Student Affairs.' }}</p>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 shadow-sm mt-4 text-dark text-center">
                                <i class="fa-solid fa-circle-info me-2"></i> Your application is currently in the evaluation queue. Please check back later.
                            </div>
                        @endif

                    </div>
                </div>
            @else
                <div class="card shadow-sm border-0 rounded-4 p-5 text-center">
                    <div class="mb-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 100px; height: 100px;">
                            <i class="fa-solid fa-folder-open fa-3x text-muted opacity-50"></i>
                        </div>
                        <h4 class="fw-bold">No Active Applications</h4>
                        <p class="text-muted mx-auto" style="max-width: 400px;">You haven't submitted any scholarship applications yet. Check out the available grants and start your journey!</p>
                    </div>
                    <a href="{{ route('student.apply') }}" class="btn btn-lg fw-bold shadow-sm px-4" style="background-color: var(--clsu-green); color: white; border-radius: 12px;">
                        <i class="fa-solid fa-paper-plane me-2"></i> Submit an Application
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection