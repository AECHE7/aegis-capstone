@extends('layouts.app')

@section('title', 'Submit Application | A.E.G.I.S.')

@push('styles')
<style>
    /* Scholarship selector cards */
    .scholarship-grid { display: grid; grid-template-columns: 1fr; gap: 10px; }

    .scholarship-card-select {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
        position: relative;
    }

    .scholarship-card-select:hover {
        border-color: var(--clsu-green);
        background: #f0fdf4;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15,89,52,0.1);
    }

    .scholarship-card-select.selected {
        border-color: var(--clsu-green);
        background: #f0fdf4;
        box-shadow: 0 4px 16px rgba(15,89,52,0.15);
    }

    .scholarship-card-select.selected::after {
        content: '✓';
        position: absolute;
        top: 10px; right: 12px;
        width: 22px; height: 22px;
        background: var(--clsu-green);
        color: white;
        border-radius: 50%;
        font-size: 0.7rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 22px;
        text-align: center;
    }

    /* Drag-and-drop upload zone */
    .upload-zone {
        border: 2.5px dashed #cbd5e1;
        border-radius: 16px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s;
        background: #f8fafc;
        position: relative;
    }

    .upload-zone:hover, .upload-zone.drag-over {
        border-color: var(--clsu-green);
        background: #f0fdf4;
    }

    .upload-zone.has-file {
        border-color: var(--clsu-green);
        background: #f0fdf4;
        padding: 1rem;
    }

    .upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    /* Preview image */
    #previewImg {
        max-height: 280px;
        max-width: 100%;
        border-radius: 10px;
        object-fit: contain;
        display: none;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    /* Eligibility badge */
    .eligibility-badge { display: none; }

    /* Submit button */
    .btn-submit-app {
        background: linear-gradient(135deg, var(--clsu-green), #16703f);
        color: white; border: none;
        padding: 14px; border-radius: 12px;
        font-weight: 700; font-size: 1rem;
        transition: all 0.3s;
        box-shadow: 0 4px 16px rgba(15,89,52,0.25);
    }
    .btn-submit-app:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(15,89,52,0.35);
        color: white;
    }
    .btn-submit-app:disabled { opacity: 0.6; transform: none; box-shadow: none; cursor: not-allowed; }

    /* Tips panel */
    .tips-panel { background: #f8fafc; border-radius: 14px; padding: 1.25rem; border: 1px solid #e2e8f0; }
    .tip-item { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; }
    .tip-item:last-child { margin-bottom: 0; }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 900px; padding: 1.5rem 1rem 3rem;">

    <div class="text-center mb-5">
        <div style="width:60px;height:60px;background:linear-gradient(135deg,var(--clsu-green),#16703f);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;box-shadow:0 8px 20px rgba(15,89,52,0.25);">
            <i class="fa-solid fa-file-signature text-white fs-4"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Submit a New Application</h3>
        <p class="text-muted" style="font-size:0.9rem;">Select your scholarship, enter your GWA, and upload your Certificate of Grades.</p>
    </div>

    <div class="row g-4">
        {{-- LEFT: Form --}}
        <div class="col-lg-7">
            <div class="card p-4" style="border-radius: 20px;">
                <form action="{{ route('student.store') }}" method="POST" enctype="multipart/form-data" id="applicationForm">
                    @csrf
                    <input type="hidden" name="program_name" id="programNameInput">
                    <input type="hidden" name="scholarship_id" id="scholarshipIdInput">

                    {{-- Step 1: Scholarship --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">
                            <span class="badge me-2 rounded-pill" style="background:var(--clsu-green);color:white;font-size:0.7rem;padding:4px 8px;">1</span>
                            Select Scholarship Program
                        </label>
                        <div class="scholarship-grid" id="scholarshipGrid">
                            @foreach($scholarships as $scholarship)
                            <div class="scholarship-card-select"
                                 data-id="{{ $scholarship->id }}"
                                 data-name="{{ $scholarship->name }}"
                                 data-gwa="{{ $scholarship->min_gwa_required }}"
                                 onclick="selectScholarship(this)">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $scholarship->name }}</div>
                                        <div class="text-muted small mt-1">{{ $scholarship->description }}</div>
                                    </div>
                                    <span style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:20px;font-size:0.72rem;font-weight:700;padding:3px 10px;white-space:nowrap;margin-left:10px;">
                                        Max GWA: {{ $scholarship->min_gwa_required }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Step 2: GWA --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">
                            <span class="badge me-2 rounded-pill" style="background:var(--clsu-green);color:white;font-size:0.7rem;padding:4px 8px;">2</span>
                            Declared GWA
                        </label>
                        <input type="number" step="0.01" min="1.00" max="5.00"
                               name="gwa" id="gwaInput"
                               class="form-control"
                               placeholder="e.g. 1.25"
                               required oninput="checkEligibility()">
                        <div class="eligibility-badge mt-2" id="eligibilityBadge"></div>
                    </div>

                    {{-- Step 3: Upload --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">
                            <span class="badge me-2 rounded-pill" style="background:var(--clsu-green);color:white;font-size:0.7rem;padding:4px 8px;">3</span>
                            Upload Certificate of Grades (COG)
                        </label>

                        <div class="upload-zone" id="uploadZone">
                            <input type="file" name="document" id="documentUpload"
                                   accept="image/jpeg, image/png" required
                                   onchange="handleFile(this)">
                            <div id="uploadPlaceholder">
                                <i class="fa-solid fa-cloud-arrow-up fs-1 mb-2 d-block" style="color:#94a3b8;"></i>
                                <div class="fw-semibold text-dark mb-1">Drag & drop your COG here</div>
                                <div class="text-muted small">or <span style="color:var(--clsu-green);text-decoration:underline;cursor:pointer;">browse files</span></div>
                                <div class="text-muted mt-2" style="font-size:0.72rem;">JPEG, PNG · Max 10MB</div>
                            </div>
                            <div id="uploadPreview" style="display:none;">
                                <img id="previewImg" src="#" alt="Preview" style="display:block;">
                                <div class="mt-2 text-muted small" id="fileName"></div>
                                <button type="button" class="btn btn-sm btn-light mt-2 rounded-pill" onclick="clearFile(event)" style="font-size:0.75rem;">
                                    <i class="fa-solid fa-xmark me-1"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-submit-app w-100" id="submitBtn" onclick="showLoading()">
                        <i class="fa-solid fa-paper-plane me-2"></i> Submit Application to OSA
                    </button>
                </form>
            </div>
        </div>

        {{-- RIGHT: Tips Panel --}}
        <div class="col-lg-5">
            <div class="tips-panel mb-3">
                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-lightbulb text-warning me-2"></i> Submission Tips</h6>
                <div class="tip-item">
                    <div style="width:32px;height:32px;border-radius:8px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid fa-image" style="color:#16a34a;font-size:0.8rem;"></i>
                    </div>
                    <div class="small text-muted">Use a <strong class="text-dark">clear, unedited</strong> scan or photo of your official COG. Blurry images may fail verification.</div>
                </div>
                <div class="tip-item">
                    <div style="width:32px;height:32px;border-radius:8px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid fa-star" style="color:#0284c7;font-size:0.8rem;"></i>
                    </div>
                    <div class="small text-muted">Your declared GWA must match the grades on your COG. The AI system checks for consistency.</div>
                </div>
                <div class="tip-item">
                    <div style="width:32px;height:32px;border-radius:8px;background:#fef9c3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid fa-shield-halved" style="color:#ca8a04;font-size:0.8rem;"></i>
                    </div>
                    <div class="small text-muted">Your file is <strong class="text-dark">anonymized and encrypted</strong> upon upload using SHA-256 hashing to protect your identity.</div>
                </div>
            </div>

            {{-- What happens next --}}
            <div class="card p-4" style="border-radius:16px;">
                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-route text-primary me-2"></i> What Happens Next?</h6>
                @foreach([
                    ['icon' => 'fa-paper-plane', 'color' => '#0284c7', 'bg' => '#e0f2fe', 'title' => 'Submission', 'desc' => 'Your application is submitted to the OSA queue.'],
                    ['icon' => 'fa-robot', 'color' => '#7c3aed', 'bg' => '#ede9fe', 'title' => 'AI Scan', 'desc' => 'Our ResNet-50 CNN analyzes your COG for authenticity.'],
                    ['icon' => 'fa-user-shield', 'color' => '#0F5934', 'bg' => '#dcfce7', 'title' => 'OSA Evaluation', 'desc' => 'An OSA administrator reviews the AI report and your GWA.'],
                    ['icon' => 'fa-envelope', 'color' => '#d97706', 'bg' => '#fef9c3', 'title' => 'Notification', 'desc' => 'You receive an email with the final decision.'],
                ] as $step)
                <div class="d-flex gap-3 mb-3 {{ $loop->last ? 'mb-0' : '' }}">
                    <div style="width:34px;height:34px;border-radius:9px;background:{{ $step['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid {{ $step['icon'] }}" style="color:{{ $step['color'] }};font-size:0.8rem;"></i>
                    </div>
                    <div>
                        <div class="fw-semibold text-dark small">{{ $step['title'] }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">{{ $step['desc'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedScholarshipGwa = null;

    function selectScholarship(el) {
        // Deselect all
        document.querySelectorAll('.scholarship-card-select').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');

        // Set hidden inputs
        document.getElementById('programNameInput').value = el.dataset.name;
        document.getElementById('scholarshipIdInput').value = el.dataset.id;
        selectedScholarshipGwa = parseFloat(el.dataset.gwa);

        checkEligibility();
    }

    function checkEligibility() {
        const badge = document.getElementById('eligibilityBadge');
        const submitBtn = document.getElementById('submitBtn');
        const gwaVal = parseFloat(document.getElementById('gwaInput').value);

        if (!selectedScholarshipGwa || isNaN(gwaVal)) {
            badge.style.display = 'none';
            return;
        }

        badge.style.display = 'block';

        if (gwaVal <= selectedScholarshipGwa) {
            badge.innerHTML = `<span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;background:#dcfce7;color:#15803d;border:1px solid #86efac;font-size:0.8rem;font-weight:600;"><i class="fa-solid fa-circle-check"></i> GWA eligible — you qualify for this program</span>`;
            submitBtn.disabled = false;
            submitBtn.classList.remove('disabled');
        } else {
            badge.innerHTML = `<span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;font-size:0.8rem;font-weight:600;"><i class="fa-solid fa-circle-xmark"></i> GWA ${gwaVal.toFixed(2)} exceeds the max of ${selectedScholarshipGwa.toFixed(2)} for this program</span>`;
            submitBtn.disabled = true;
        }
    }

    function handleFile(input) {
        const file = input.files[0];
        if (!file) return;

        const zone = document.getElementById('uploadZone');
        const placeholder = document.getElementById('uploadPlaceholder');
        const preview = document.getElementById('uploadPreview');
        const img = document.getElementById('previewImg');
        const nameEl = document.getElementById('fileName');

        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.style.display = 'block';
            nameEl.textContent = `📎 ${file.name} (${(file.size/1024).toFixed(0)} KB)`;
            placeholder.style.display = 'none';
            preview.style.display = 'block';
            zone.classList.add('has-file');
        };
        reader.readAsDataURL(file);
    }

    function clearFile(e) {
        e.stopPropagation();
        const input = document.getElementById('documentUpload');
        input.value = '';
        document.getElementById('uploadPlaceholder').style.display = 'block';
        document.getElementById('uploadPreview').style.display = 'none';
        document.getElementById('uploadZone').classList.remove('has-file');
        document.getElementById('previewImg').style.display = 'none';
    }

    // Drag and drop
    const zone = document.getElementById('uploadZone');
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('drag-over');
        const dt = e.dataTransfer;
        if (dt.files.length) {
            const input = document.getElementById('documentUpload');
            input.files = dt.files;
            handleFile(input);
        }
    });

    function showLoading() {
        const btn = document.getElementById('submitBtn');
        const form = document.getElementById('applicationForm');
        if (form.checkValidity()) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Encrypting & Submitting...';
            btn.classList.add('disabled');
            btn.disabled = true;
        }
    }

    @if ($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Submission Failed',
        html: `{!! implode('<br>', $errors->all()) !!}`,
        confirmButtonColor: '#0F5934',
        customClass: { popup: 'rounded-4' }
    });
    @endif
</script>
@endpush