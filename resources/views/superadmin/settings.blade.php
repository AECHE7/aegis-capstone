@extends('layouts.app')

@section('title', 'System Settings | A.E.G.I.S.')
@section('page-title', 'System Settings')
@section('page-subtitle', 'Configure system branding, assets, and AI fraud parameters')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px;" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Branding Settings -->
            <div class="card mb-4" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <div class="card-header bg-transparent py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-palette me-2 text-success"></i> Portal Branding</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="app_name" class="form-label fw-semibold small text-muted">Application Name</label>
                            <input type="text" class="form-control py-2 @error('app_name') is-invalid @enderror" 
                                   id="app_name" name="app_name" 
                                   value="{{ old('app_name', $settings['app_name']) }}" required style="border-radius: 10px;">
                            @error('app_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="university_name" class="form-label fw-semibold small text-muted">University Name</label>
                            <input type="text" class="form-control py-2 @error('university_name') is-invalid @enderror" 
                                   id="university_name" name="university_name" 
                                   value="{{ old('university_name', $settings['university_name']) }}" required style="border-radius: 10px;">
                            @error('university_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Settings -->
            <div class="card mb-4" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <div class="card-header bg-transparent py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-microchip me-2 text-success"></i> AI Fraud Parameters</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="ai_fraud_threshold" class="form-label fw-semibold small text-muted mb-0">AI Fraud Threshold (%)</label>
                                <span id="threshold-val" class="badge bg-success fw-bold px-2 py-1" style="border-radius: 6px;">{{ $settings['ai_fraud_threshold'] }}%</span>
                            </div>
                            <input type="range" class="form-range" id="ai_fraud_threshold" name="ai_fraud_threshold" 
                                   min="0" max="100" step="0.5" 
                                   value="{{ old('ai_fraud_threshold', $settings['ai_fraud_threshold']) }}"
                                   oninput="document.getElementById('threshold-val').innerText = this.value + '%'">
                            <div class="form-text small text-muted mt-2">
                                Flag document scans with scores higher than or equal to this threshold. Lower values increase strictness.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="gwa_discrepancy_tolerance" class="form-label fw-semibold small text-muted">GWA Discrepancy Tolerance</label>
                            <input type="number" class="form-control py-2 @error('gwa_discrepancy_tolerance') is-invalid @enderror" 
                                   id="gwa_discrepancy_tolerance" name="gwa_discrepancy_tolerance" 
                                   step="0.001" min="0" max="5"
                                   value="{{ old('gwa_discrepancy_tolerance', $settings['gwa_discrepancy_tolerance']) }}" required style="border-radius: 10px;">
                            <div class="form-text small text-muted mt-1">
                                Maximum variance allowed between the declared GWA and the AI-extracted document GWA (e.g. 0.01).
                            </div>
                            @error('gwa_discrepancy_tolerance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branding Assets / Logo -->
            <div class="card mb-4" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <div class="card-header bg-transparent py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-image me-2 text-success"></i> Portal Logo Asset</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-4 flex-wrap flex-md-nowrap">
                        <div class="flex-shrink-0" style="width: 100px; height: 100px; border-radius: 16px; background: var(--clsu-bg); border: 2px dashed var(--border-color); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            @if($settings['app_logo'])
                                <img src="{{ route('system.logo') }}" id="logo-preview-img" style="width:100%; height:100%; object-fit:contain;">
                            @else
                                <div id="logo-preview-placeholder" class="text-muted"><i class="fa-solid fa-shield-halved fa-2x"></i></div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <label for="app_logo" class="form-label fw-semibold small text-muted">Upload Custom Logo</label>
                            <input type="file" class="form-control py-2 @error('app_logo') is-invalid @enderror" 
                                   id="app_logo" name="app_logo" accept="image/*" style="border-radius: 10px;">
                            <div class="form-text small text-muted mt-1">
                                Recommended square format (PNG or WebP), max size 2MB.
                            </div>
                            @error('app_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if($settings['app_logo'])
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" name="reset_logo" id="reset_logo" value="1">
                                    <label class="form-check-label text-danger small fw-semibold" for="reset_logo">
                                        Reset to Default Shield Icon
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn fw-bold px-4 py-2" 
                        style="background: linear-gradient(135deg, var(--clsu-green), #16703f); color: white; border-radius: 10px; box-shadow: 0 4px 12px rgba(15,89,52,0.25);">
                    <i class="fa-solid fa-save me-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
