<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluate APP-{{ $application->id }} | A.E.G.I.S. Forensics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --clsu-green: #0F5934; --clsu-gold: #F2A900; }
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .navbar-custom { background-color: #0f172a; border-bottom: 4px solid var(--clsu-gold); }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        
        /* Dark AI Card Styling */
        .ai-card { background: linear-gradient(145deg, #0f172a, #1e293b); color: white; position: relative; overflow: hidden; }
        .ai-card::after { content: ''; position: absolute; top: 0; right: 0; width: 150px; height: 150px; background: radial-gradient(circle, rgba(242,169,0,0.1) 0%, rgba(0,0,0,0) 70%); border-radius: 50%; }
        
        /* Side-by-Side Viewer Styling */
        .viewer-box { 
            background-color: #f1f5f9; 
            border-radius: 10px; 
            padding: 10px; 
            height: 100%; 
            display: flex; 
            flex-direction: column;
            align-items: center; 
            position: relative;
            border: 2px solid #e2e8f0;
        }
        .viewer-box.danger-box { border-color: rgba(220, 53, 69, 0.4); background-color: #fff5f5; }
        .viewer-box img { 
            max-height: 550px; 
            width: 100%; 
            object-fit: contain; 
            border-radius: 6px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            background: white;
            padding: 5px;
        }
        .badge-floating { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); z-index: 10; font-size: 0.8rem; letter-spacing: 0.5px; padding: 6px 12px; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        /* Dynamic Risk Colors */
        .text-high-risk { color: #ef4444; }
        .text-mod-risk { color: #f59e0b; }
        .text-low-risk { color: #10b981; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom py-3 mb-4">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 fw-bold text-white"><i class="fa-solid fa-shield-halved text-warning me-2"></i> Document Forensics Suite</h5>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-arrow-left me-1"></i> Back to Queue</a>
        </div>
    </div>
</nav>

<div class="container-fluid px-md-5 mb-5">
    
    <!-- Top Applicant Info Header -->
    <div class="card p-4 mb-4 border-top border-4 border-success">
        <div class="d-flex justify-content-between align-items-md-center flex-column flex-md-row gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-secondary">APP-{{ $application->id }}</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $application->program_name }}</h4>
                </div>
                <div class="d-flex flex-wrap gap-3 mt-2 text-muted small">
                    <span><i class="fa-solid fa-user text-primary me-1"></i> <strong>Applicant:</strong> {{ $application->user->name ?? 'Unknown' }}</span>
                    <span><i class="fa-solid fa-id-card text-primary me-1"></i> <strong>CLSU ID:</strong> {{ $application->user->profile->clsu_id_number ?? 'N/A' }}</span>
                    <span><i class="fa-solid fa-graduation-cap text-primary me-1"></i> <strong>Course:</strong> {{ $application->user->profile->course ?? 'N/A' }} ({{ $application->user->profile->year_level ?? 'N/A' }})</span>
                    <span><i class="fa-solid fa-star text-warning me-1"></i> <strong>Declared GWA:</strong> <span class="badge bg-warning text-dark">{{ $application->gwa }}</span></span>
                </div>
            </div>
            <div>
                <span class="badge bg-{{ $application->status == 'Pending' ? 'warning text-dark' : ($application->status == 'Approved' ? 'success' : 'danger') }} px-3 py-2 fs-6 rounded-pill">
                    <i class="fa-solid fa-circle-info me-1"></i> Status: {{ $application->status }}
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- LEFT COLUMN: AI Analysis & Decision Panel -->
        <div class="col-lg-4">
            
            @php
                $hasAiResult = $application->document && $application->document->aiResult;
                $fraudScore = $hasAiResult ? $application->document->aiResult->fraud_probability : 0;
                
                // Dynamic styling based on AI score
                $riskColor = $fraudScore >= 60 ? 'danger' : ($fraudScore >= 30 ? 'warning' : 'success');
                $riskText = $fraudScore >= 60 ? 'HIGH RISK' : ($fraudScore >= 30 ? 'MODERATE RISK' : 'AUTHENTIC');
            @endphp

            <!-- AI Analytics Card -->
            <div class="card ai-card p-4 mb-4 shadow-lg">
                <div class="text-center mb-4">
                    <div class="text-uppercase tracking-wide small text-info fw-bold mb-2"><i class="fa-solid fa-microchip me-1"></i> Deep Learning Analysis</div>
                    <hr class="opacity-25 my-2">
                </div>

                @if($hasAiResult)
                    <div class="text-center mb-4">
                        <div class="small text-gray-400 mb-1">FRAUD PROBABILITY SCORE</div>
                        <h1 class="display-3 fw-bold mb-0 text-{{ $riskColor }}">{{ $fraudScore }}<span class="fs-4 text-muted">%</span></h1>
                    </div>

                    <div class="progress mb-3" style="height: 10px; background-color: rgba(255,255,255,0.1);">
                        <div class="progress-bar bg-{{ $riskColor }} progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $fraudScore }}%"></div>
                    </div>

                    <div class="text-center mb-4">
                        <span class="badge bg-{{ $riskColor }} text-uppercase px-3 py-2 rounded-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $riskText }}</span>
                    </div>

                    <div class="bg-white bg-opacity-10 rounded p-3 small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-white-50"><i class="fa-solid fa-network-wired me-1"></i> Architecture:</span>
                            <span class="fw-bold">ResNet-50 CNN</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-white-50"><i class="fa-solid fa-layer-group me-1"></i> Preprocessing:</span>
                            <span class="fw-bold">Error Level Analysis</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-white-50"><i class="fa-solid fa-tag me-1"></i> Classification:</span>
                            <span class="fw-bold text-{{ $riskColor }}">{{ ucfirst($application->document->aiResult->classification) }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa-solid fa-file-shield fa-4x text-white-50 mb-3"></i>
                        <p class="text-white-50 small mb-4">Document requires verification.<br>Awaiting manual execution.</p>
                        
                        <form action="{{ route('admin.scan', $application->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info text-dark fw-bold w-100 py-2 shadow"><i class="fa-solid fa-bolt me-1"></i> Execute AI Scan</button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Decision Form Card -->
            <div class="card p-4 border-0 shadow-sm">
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-gavel text-primary me-2"></i> Final Eligibility Decision</h6>
                
                <form action="{{ route('admin.updateStatus', $application->id) }}" method="POST" id="decisionForm">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold"><i class="fa-solid fa-pen-to-square me-1"></i> Evaluator Remarks</label>
                        <textarea name="remarks" class="form-control bg-light" rows="3" required placeholder="e.g., GWA verified. Cleared for DOST-SEI Merit.">{{ $application->remarks }}</textarea>
                    </div>

                    @if($application->status == 'Pending')
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success w-50 fw-bold" onclick="confirmDecision('Approved')"><i class="fa-solid fa-check-circle me-1"></i> Approve</button>
                            <button type="button" class="btn btn-danger w-50 fw-bold" onclick="confirmDecision('Rejected')"><i class="fa-solid fa-times-circle me-1"></i> Reject</button>
                        </div>
                        <input type="hidden" name="status" id="statusInput">
                    @else
                        <div class="alert alert-{{ $application->status == 'Approved' ? 'success' : 'danger' }} mb-0 text-center fw-bold">
                            <i class="fa-solid fa-lock me-1"></i> This application is finalized.
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- RIGHT COLUMN: Side-by-Side Document Viewer -->
        <div class="col-lg-8">
            <div class="card p-4 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-images text-primary me-2"></i> Document Forensics Viewer</h6>
                    <span class="badge bg-light text-dark border"><i class="fa-solid fa-file-image me-1"></i> JPEG/PNG</span>
                </div>

                <div class="row g-4 h-100">
                    <!-- Original Document Box -->
                    <div class="col-md-6">
                        <div class="viewer-box mt-3">
                            <span class="badge bg-secondary rounded-pill badge-floating"><i class="fa-solid fa-file me-1"></i> Original Document</span>
                            <!-- FIX: Use asset() to correctly load the image from the public/uploads folder -->
                            @if($application->document)
                                <img src="{{ asset($application->document->file_path) }}" alt="Original Student Document" onerror="this.src='https://via.placeholder.com/600x800?text=Image+Not+Found'">
                            @else
                                <div class="d-flex justify-content-center align-items-center h-100 text-muted">No Document Found</div>
                            @endif
                        </div>
                    </div>

                    <!-- Heatmap Document Box -->
                    <div class="col-md-6">
                        <div class="viewer-box danger-box mt-3">
                            <span class="badge bg-danger rounded-pill badge-floating shadow-sm"><i class="fa-solid fa-fire me-1"></i> Grad-CAM Heatmap</span>
                            @if($hasAiResult)
                                <img src="{{ route('document.heatmap', $application->document->id) }}" alt="AI Heatmap Overlay">
                            @else
                                <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted w-100" style="background: rgba(220,53,69,0.05); border-radius: 6px;">
                                    <i class="fa-solid fa-robot fa-3x text-danger opacity-25 mb-2"></i>
                                    <small>Awaiting AI Scan Execution</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer Alert -->
                <div class="alert alert-secondary mt-4 mb-0 small border-0 bg-light text-muted">
                    <i class="fa-solid fa-circle-info text-primary me-1"></i> <strong>How to read this:</strong> Red/Hot areas on the heatmap indicate pixels with anomalous compression levels detected by the ResNet-50 CNN, heavily implying digital manipulation or copy-pasting.
                </div>

            </div>
        </div>

    </div>
</div>

<!-- SweetAlert Confirmation Logic -->
<script>
    function confirmDecision(status) {
        Swal.fire({
            title: `Confirm ${status}?`,
            text: `You are about to permanently mark this application as ${status}.`,
            icon: status === 'Approved' ? 'success' : 'warning',
            showCancelButton: true,
            confirmButtonColor: status === 'Approved' ? '#0F5934' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${status} it!`
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('statusInput').value = status;
                document.getElementById('decisionForm').submit();
            }
        });
    }

    // Success/Error Popups from Controller
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Action Successful', text: "{{ session('success') }}", confirmButtonColor: '#0F5934' });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Scan Failed', text: "{{ session('error') }}", confirmButtonColor: '#dc3545' });
    @endif
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>