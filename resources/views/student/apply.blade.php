@extends('layouts.app')

@section('title', 'Submit Application | A.E.G.I.S.')

@push('styles')
<style>
    /* Container centering & max-width */
    .apply-container {
        max-width: 840px;
        margin: 0 auto;
        padding: 1.5rem 1rem 3.5rem;
    }

    /* Scholarship selector cards */
    .scholarship-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 12px;
    }

    .scholarship-card-select {
        border: 2px solid var(--border-color);
        border-radius: 14px;
        padding: 14px 16px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background: var(--card-bg);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100px;
    }

    .scholarship-card-select:hover {
        border-color: var(--clsu-green);
        background: var(--clsu-green-muted);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15,89,52,0.1);
    }

    .scholarship-card-select.selected {
        border-color: var(--clsu-green);
        background: var(--clsu-green-muted);
        box-shadow: 0 6px 20px rgba(15,89,52,0.15);
    }

    .scholarship-card-select.selected::after {
        content: '✓';
        position: absolute;
        top: 10px; right: 12px;
        width: 22px; height: 22px;
        background: var(--clsu-green);
        color: white;
        border-radius: 50%;
        font-size: 0.72rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 22px;
        text-align: center;
    }

    /* Stepper System */
    .stepper-bubble {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 700;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        border: 2px solid #e2e8f0;
    }
    .stepper-bubble.active {
        background: var(--clsu-green);
        color: white;
        border-color: var(--clsu-green);
        box-shadow: 0 4px 12px rgba(15,89,52,0.25);
    }
    .stepper-bubble.completed {
        background: #dcfce7;
        color: #15803d;
        border-color: #86efac;
    }
    .stepper-line {
        flex: 1;
        height: 2px;
        background: #e2e8f0;
        margin: 0 8px;
        margin-bottom: 20px;
        transition: all 0.3s;
    }
    .stepper-line.active {
        background: var(--clsu-green);
    }

    /* Upload Dropzone Container */
    .file-dropzone-box {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 1.25rem 1rem;
        background: #f8fafc;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    .file-dropzone-box:hover {
        border-color: var(--clsu-green);
        background: #f0fdf4;
    }
    .file-dropzone-box input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    /* Submit button */
    .btn-submit-app {
        background: linear-gradient(135deg, var(--clsu-green), #16703f);
        color: white; border: none;
        padding: 14px; border-radius: var(--radius-pill);
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
</style>
@endpush

@section('content')
<div class="apply-container">

    {{-- Page Header --}}
    <div class="text-center mb-4">
        <div style="width:54px;height:54px;background:linear-gradient(135deg,var(--clsu-green),#16703f);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;box-shadow:0 6px 16px rgba(15,89,52,0.2);">
            <i class="fa-solid fa-file-signature text-white fs-5"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">{{ __('portal.submit_new_application') }}</h3>
        <p class="text-muted small mb-0">{{ __('portal.select_scholarship_tagline') }}</p>
    </div>

    {{-- Interactive Stepper Header --}}
    <div class="card border-0 shadow-sm p-3 mb-4" style="border-radius:16px;">
        <div class="d-flex align-items-center justify-content-between text-center position-relative">
            <div class="flex-fill" id="stepperStep1">
                <div class="stepper-bubble active mx-auto mb-1" id="stepBubble1">1</div>
                <div class="small fw-bold text-dark" id="stepLabel1" style="font-size:0.75rem;">1. Select Program</div>
            </div>
            <div class="stepper-line" id="stepperLine1"></div>
            <div class="flex-fill" id="stepperStep2">
                <div class="stepper-bubble mx-auto mb-1" id="stepBubble2">2</div>
                <div class="small fw-semibold text-muted" id="stepLabel2" style="font-size:0.75rem;">2. Details & Documents</div>
            </div>
            <div class="stepper-line" id="stepperLine2"></div>
            <div class="flex-fill" id="stepperStep3">
                <div class="stepper-bubble mx-auto mb-1" id="stepBubble3">3</div>
                <div class="small fw-semibold text-muted" id="stepLabel3" style="font-size:0.75rem;">3. Submit to OSA</div>
            </div>
        </div>
    </div>

    {{-- Unified Guidelines Banner --}}
    <div class="alert alert-light border shadow-sm p-3 mb-4 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size:0.78rem;">
        <div class="d-flex align-items-center gap-2.5">
            <div style="width:32px;height:32px;border-radius:8px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-shield-halved text-success" style="font-size:0.85rem;"></i>
            </div>
            <div>
                <strong class="text-dark">Official Student Guidelines:</strong>
                <span class="text-muted d-block" style="font-size:0.72rem;">Submit clear, well-lit scans (PDF, PNG, JPG - Max 10MB). All uploads are encrypted with SHA-256.</span>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill" style="font-size:0.7rem;">
            <i class="fa-solid fa-envelope text-primary me-1"></i> osa@clsu.edu.ph
        </span>
    </div>

    {{-- Form Body Card --}}
    <div class="card p-4 p-md-4.5 border-0 shadow-sm" style="border-radius: 20px;">
        <form action="{{ route('student.store') }}" method="POST" enctype="multipart/form-data" id="applicationForm">
            @csrf
            <input type="hidden" name="program_name" id="programNameInput">
            <input type="hidden" name="scholarship_id" id="scholarshipIdInput">
            @if(isset($prevApp))
                <input type="hidden" name="is_renewal" value="1">
                <input type="hidden" name="previous_application_id" value="{{ $prevApp->id }}">
            @endif

            {{-- Step 1: Program Selector --}}
            <div class="mb-4">
                <label class="form-label fw-bold text-dark mb-2.5 d-flex align-items-center gap-1.5" style="font-size: 0.9rem;">
                    <span class="badge rounded-pill" style="background:var(--clsu-green);color:white;font-size:0.7rem;padding:4px 8px;">1</span>
                    {{ __('portal.select_scholarship_program') }}
                </label>
                
                <div class="scholarship-grid" id="scholarshipGrid">
                    @foreach($scholarships as $scholarship)
                    <div class="scholarship-card-select"
                         data-id="{{ $scholarship->id }}"
                         data-name="{{ $scholarship->name }}"
                         data-gwa="{{ $scholarship->min_gwa_required ?? '' }}"
                         onclick="selectScholarship(this)">
                        <div class="pe-4">
                            <div class="fw-bold text-dark" style="font-size:0.85rem; line-height:1.35;">{{ $scholarship->name }}</div>
                            @if($scholarship->description)
                                <div class="text-muted small mt-1" style="font-size:0.72rem; line-height:1.3;">{{ Str::limit($scholarship->description, 60) }}</div>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <span class="badge rounded-pill bg-light text-dark border" style="font-size:0.68rem;">
                                Max GWA: <strong>{{ $scholarship->min_gwa_required ?? 'None' }}</strong>
                            </span>
                            <small class="text-success fw-semibold" style="font-size:0.7rem;">Select &rarr;</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Step 2: Dynamic Custom Fields & Documents --}}
            <div id="dynamicFieldsContainer" class="mb-4" style="display: none;">
                <label class="form-label fw-bold text-dark mb-2.5 d-flex align-items-center gap-1.5" style="font-size: 0.9rem;">
                    <span class="badge rounded-pill" style="background:var(--clsu-green);color:white;font-size:0.7rem;padding:4px 8px;">2</span>
                    {{ __('portal.configure_parameters') }}
                </label>
                <div class="p-3.5 bg-light-subtle border rounded-3 row g-3 mx-0" id="dynamicFieldsBody">
                    <!-- Dynamic inputs appended via JS -->
                </div>
            </div>

            {{-- Submit Action --}}
            <div class="mt-4 pt-2 border-top">
                <button type="submit" class="btn-submit-app w-100" id="submitBtn" aria-describedby="submitHelpText">
                    <i class="fa-solid fa-paper-plane me-2"></i> {{ __('portal.submit_to_osa') }}
                </button>
            </div>
            <div id="submitHelpText" class="text-danger small mt-2 text-center fw-semibold" style="display:none;" role="alert"></div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    window.portalTranslations = {
        select_scholarship_program: "{{ __('portal.select_scholarship_program') }}",
        optional: "{{ __('portal.optional') }}"
    };

    let selectedScholarshipGwa = null;

    function selectScholarship(el) {
        document.querySelectorAll('.scholarship-card-select').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');

        // Update Stepper
        const b1 = document.getElementById('stepBubble1');
        const b2 = document.getElementById('stepBubble2');
        const l1 = document.getElementById('stepperLine1');
        if (b1) { b1.classList.add('completed'); b1.innerHTML = '✓'; }
        if (b2) { b2.classList.add('active'); }
        if (l1) { l1.classList.add('active'); }

        // Set hidden inputs
        document.getElementById('programNameInput').value = el.dataset.name;
        document.getElementById('scholarshipIdInput').value = el.dataset.id;
        selectedScholarshipGwa = parseFloat(el.dataset.gwa);

        // Run GWA check
        checkGwaEligibility();

        // Fetch custom fields dynamically
        fetch(`/scholarships/${el.dataset.id}/fields`)
            .then(response => response.json())
            .then(fields => {
                const container = document.getElementById('dynamicFieldsContainer');
                const body = document.getElementById('dynamicFieldsBody');
                body.innerHTML = '';
                
                if (fields.length > 0) {
                    container.style.display = 'block';
                    fields.forEach(field => {
                        const formGroup = document.createElement('div');
                        const isFullWidth = (field.field_type === 'textarea' || field.field_type === 'file' || (field.field_label && field.field_label.length > 35));
                        formGroup.className = isFullWidth ? 'col-12 mb-3 text-start' : 'col-md-6 mb-3 text-start';
                        
                        const uniqueId = `custom_field_${field.field_name}`;
                        
                        const label = document.createElement('label');
                        label.className = 'form-label fw-semibold small text-dark mb-1';
                        label.setAttribute('for', uniqueId);
                        label.innerHTML = field.field_label;
                        if (field.is_required) {
                            label.innerHTML += ' <span class="text-danger">*</span>';
                        }
                        formGroup.appendChild(label);
                        
                        let input;
                        
                        if (field.field_type === 'textarea') {
                            input = document.createElement('textarea');
                            input.className = 'form-control shadow-sm';
                            input.rows = 3;
                        } else if (field.field_type === 'select') {
                            input = document.createElement('select');
                            input.className = 'form-select shadow-sm';
                            
                            const defaultOpt = document.createElement('option');
                            defaultOpt.value = '';
                            defaultOpt.textContent = 'Select an option';
                            input.appendChild(defaultOpt);
                            
                            if (field.options && Array.isArray(field.options)) {
                                  field.options.forEach(opt => {
                                      const o = document.createElement('option');
                                      o.value = opt;
                                      o.textContent = opt;
                                      input.appendChild(o);
                                  });
                            }
                        } else if (field.field_type === 'file') {
                            // Styled Dropzone Box
                            const dropzoneBox = document.createElement('div');
                            dropzoneBox.className = 'file-dropzone-box';
                            
                            input = document.createElement('input');
                            input.type = 'file';
                            input.accept = 'image/*,application/pdf';
                            if (window.innerWidth <= 768) {
                                input.setAttribute('capture', 'environment');
                            }

                            const dropzoneContent = document.createElement('div');
                            dropzoneContent.className = 'dropzone-inner';
                            dropzoneContent.innerHTML = `
                                <i class="fa-solid fa-cloud-arrow-up text-success fs-4 mb-1"></i>
                                <div class="fw-semibold text-dark small">Click to browse or drop ${field.field_label}</div>
                                <small class="text-muted" style="font-size:0.7rem;">Accepted: PDF, PNG, JPG (Max 10MB)</small>
                            `;

                            dropzoneBox.appendChild(input);
                            dropzoneBox.appendChild(dropzoneContent);
                            formGroup.appendChild(dropzoneBox);

                            // Add file validation and preview
                            input.addEventListener('change', function(e) {
                                const file = e.target.files[0];
                                const previewId = `file-preview-${uniqueId}`;
                                let preview = document.getElementById(previewId);

                                if (!file) {
                                    if (preview) preview.remove();
                                    dropzoneContent.style.display = 'block';
                                    updateChecklist();
                                    return;
                                }

                                const maxSize = 10 * 1024 * 1024;
                                if (file.size > maxSize) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'File Too Large',
                                        html: `File size: <strong>${(file.size / 1024 / 1024).toFixed(2)} MB</strong><br>Maximum allowed: <strong>10 MB</strong>`,
                                        confirmButtonColor: '#dc2626'
                                    });
                                    input.value = '';
                                    if (preview) preview.remove();
                                    dropzoneContent.style.display = 'block';
                                    updateChecklist();
                                    return;
                                }

                                const validTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf', 'image/webp'];
                                if (!validTypes.includes(file.type)) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Invalid File Format',
                                        html: `File type: <strong>${file.type || 'Unknown'}</strong><br>Accepted formats: <strong>PNG, JPG, PDF, WebP</strong>`,
                                        confirmButtonColor: '#dc2626'
                                    });
                                    input.value = '';
                                    if (preview) preview.remove();
                                    dropzoneContent.style.display = 'block';
                                    updateChecklist();
                                    return;
                                }

                                // Show preview card
                                if (!preview) {
                                    preview = document.createElement('div');
                                    preview.id = previewId;
                                    preview.className = 'alert alert-success mt-2 mb-0 d-flex align-items-center justify-content-between p-2.5';
                                    preview.style.fontSize = '0.82rem';
                                    formGroup.appendChild(preview);
                                }

                                const fileIcon = file.type === 'application/pdf' ? 'fa-file-pdf' : 'fa-file-image';
                                const fileColor = file.type === 'application/pdf' ? '#dc2626' : '#0284c7';
                                preview.innerHTML = `
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid ${fileIcon} fs-4" style="color: ${fileColor};"></i>
                                        <div>
                                            <div class="fw-bold text-dark">${file.name}</div>
                                            <small class="text-muted">${(file.size / 1024).toFixed(1)} KB · ${file.type.split('/')[1].toUpperCase()}</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2 clear-upload-btn" style="font-size:0.75rem;">
                                        <i class="fa-solid fa-xmark"></i> Remove
                                    </button>
                                `;

                                preview.querySelector('.clear-upload-btn').addEventListener('click', function(ev) {
                                    ev.stopPropagation();
                                    input.value = '';
                                    preview.remove();
                                    updateChecklist();
                                });

                                updateChecklist();
                            });
                        } else if (field.field_type === 'number') {
                            input = document.createElement('input');
                            input.type = 'number';
                            input.step = 'any';
                            input.className = 'form-control';
                            input.setAttribute('inputmode', 'decimal');
                        } else if (field.field_type === 'date') {
                            input = document.createElement('input');
                            input.type = 'date';
                            input.className = 'form-control';
                        } else if (field.field_type === 'email') {
                            input = document.createElement('input');
                            input.type = 'email';
                            input.className = 'form-control';
                            input.setAttribute('inputmode', 'email');
                        } else {
                            input = document.createElement('input');
                            input.type = 'text';
                            input.className = 'form-control';
                        }
                        
                        input.name = `custom_fields[${field.field_name}]`;
                        input.id = uniqueId;
                        input.dataset.required = field.is_required ? '1' : '0';
                        input.dataset.label = field.field_label;
                        input.classList.add('custom-field-input');
                        
                        if (field.is_required) {
                            input.required = true;
                        }
                        
                        input.addEventListener('input', updateChecklist);
                        input.addEventListener('change', updateChecklist);
                        
                        if (field.field_type !== 'file') {
                            formGroup.appendChild(input);
                        }
                        body.appendChild(formGroup);
                    });
                } else {
                    container.style.display = 'none';
                }
                updateChecklist();
                checkGwaEligibility();
            })
            .catch(err => {
                console.error('Error fetching dynamic fields:', err);
                updateChecklist();
            });
    }

    function checkGwaEligibility() {
        const gwaInput = document.querySelector('input[name*="gwa" i], input[id*="gwa" i], .custom-field-input[data-label*="gwa" i]');
        let warningDiv = document.getElementById('gwaEligibilityWarning');
        
        if (!gwaInput) {
            if (warningDiv) warningDiv.remove();
            return;
        }
        
        const enteredVal = parseFloat(gwaInput.value);
        if (isNaN(enteredVal) || !selectedScholarshipGwa) {
            if (warningDiv) warningDiv.style.display = 'none';
            return;
        }
        
        if (enteredVal > selectedScholarshipGwa) {
            if (!warningDiv) {
                warningDiv = document.createElement('div');
                warningDiv.id = 'gwaEligibilityWarning';
                warningDiv.className = 'alert alert-warning border-0 small mt-2 d-flex align-items-start gap-2';
                warningDiv.style.borderRadius = '10px';
                warningDiv.style.backgroundColor = '#fffbeb';
                warningDiv.style.color = '#b45309';
                gwaInput.parentNode.appendChild(warningDiv);
            }
            warningDiv.innerHTML = `<i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <div><strong>GWA Warning:</strong> Your entered GWA of <strong>${enteredVal.toFixed(2)}</strong> exceeds the maximum allowed GWA of <strong>${selectedScholarshipGwa.toFixed(2)}</strong> for this scholarship. You may not be eligible to apply.</div>`;
            warningDiv.style.display = 'flex';
        } else {
            if (warningDiv) warningDiv.style.display = 'none';
        }
    }

    function updateChecklist() {
        const submitBtn = document.getElementById('submitBtn');
        const submitHelpText = document.getElementById('submitHelpText');

        let allValid = true;
        let missingFields = [];

        // 1. Scholarship selected check
        const schId = document.getElementById('scholarshipIdInput').value;
        if (!schId) {
            allValid = false;
            missingFields.push(window.portalTranslations.select_scholarship_program);
        }

        // 2. Dynamic custom fields checks
        const customFields = document.querySelectorAll('.custom-field-input');
        customFields.forEach(input => {
            let isFilled = false;
            if (input.type === 'file') {
                isFilled = input.files && input.files.length > 0;
            } else {
                isFilled = input.value.trim() !== '';
            }

            const isRequired = input.dataset.required === '1';

            if (isRequired && !isFilled) {
                allValid = false;
                missingFields.push(input.dataset.label);
            }
        });

        // 3. Stepper state & submit button
        const b3 = document.getElementById('stepBubble3');
        const l2 = document.getElementById('stepperLine2');

        if (allValid) {
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
            submitBtn.style.filter = 'none';
            submitBtn.setAttribute('aria-disabled', 'false');
            if (b3) { b3.classList.add('active'); }
            if (l2) { l2.classList.add('active'); }
            if (submitHelpText) {
                submitHelpText.style.display = 'none';
                submitHelpText.textContent = '';
            }
        } else {
            submitBtn.style.opacity = '0.6';
            submitBtn.style.cursor = 'not-allowed';
            submitBtn.style.filter = 'grayscale(30%)';
            submitBtn.setAttribute('aria-disabled', 'true');
            if (b3) { b3.classList.remove('active'); }
            if (l2) { l2.classList.remove('active'); }
            if (submitHelpText) {
                submitHelpText.style.display = 'block';
                submitHelpText.innerHTML = `<i class="fa-solid fa-circle-exclamation me-1"></i> Please complete: ${missingFields.join(', ')}`;
            }
        }
    }

    // Auto-save form inputs
    const form = document.getElementById('applicationForm');
    const FORM_KEY = 'aegis_apply_form_backup';

    function saveDraft() {
        const data = {};
        form.querySelectorAll('input:not([type="file"]):not([type="hidden"]), select, textarea').forEach(el => {
            if (el.name && el.value) data[el.name] = el.value;
        });
        localStorage.setItem(FORM_KEY, JSON.stringify(data));
    }

    function restoreDraft() {
        const raw = localStorage.getItem(FORM_KEY);
        if (!raw) return;
        try {
            const data = JSON.parse(raw);
            Object.entries(data).forEach(([name, val]) => {
                const el = form.querySelector(`[name="${name}"]`);
                if (el) {
                    el.value = val;
                    el.dispatchEvent(new Event('input'));
                }
            });
        } catch (e) {
            localStorage.removeItem(FORM_KEY);
        }
    }

    form.addEventListener('input', saveDraft);
    form.addEventListener('submit', () => localStorage.removeItem(FORM_KEY));
    document.addEventListener('DOMContentLoaded', restoreDraft);
</script>
@endpush