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
<div class="container" style="max-width: 1050px; padding: 1.5rem 1rem 3rem;">

    <div class="text-center mb-5">
        <div style="width:60px;height:60px;background:linear-gradient(135deg,var(--clsu-green),#16703f);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;box-shadow:0 8px 20px rgba(15,89,52,0.25);">
            <i class="fa-solid fa-file-signature text-white fs-4"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">{{ __('portal.submit_new_application') }}</h3>
        <p class="text-muted" style="font-size:0.9rem;">{{ __('portal.select_scholarship_tagline') }}</p>
    </div>

    <div class="row g-4">
        {{-- LEFT: Form --}}
        <div class="col-lg-7 order-last order-lg-first">
            <div class="card p-4" style="border-radius: 20px;">
                <form action="{{ route('student.store') }}" method="POST" enctype="multipart/form-data" id="applicationForm">
                    @csrf
                    <input type="hidden" name="program_name" id="programNameInput">
                    <input type="hidden" name="scholarship_id" id="scholarshipIdInput">
                    @if(isset($prevApp))
                        <input type="hidden" name="is_renewal" value="1">
                        <input type="hidden" name="previous_application_id" value="{{ $prevApp->id }}">
                    @endif

                    {{-- Step 1: Scholarship --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">
                            <span class="badge me-2 rounded-pill" style="background:var(--clsu-green);color:white;font-size:0.7rem;padding:4px 8px;">1</span>
                            {{ __('portal.select_scholarship_program') }}
                        </label>
                        <div class="scholarship-grid" id="scholarshipGrid">
                            @foreach($scholarships as $scholarship)
                            <div class="scholarship-card-select"
                                 data-id="{{ $scholarship->id }}"
                                 data-name="{{ $scholarship->name }}"
                                 data-gwa="{{ $scholarship->min_gwa_required ?? '' }}"
                                 onclick="selectScholarship(this)">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $scholarship->name }}</div>
                                        <div class="text-muted small mt-1">{{ $scholarship->description }}</div>
                                    </div>
                                    <span style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:20px;font-size:0.72rem;font-weight:700;padding:3px 10px;white-space:nowrap;margin-left:10px;">
                                        {{ __('portal.max_gwa') }}: {{ $scholarship->min_gwa_required ?? __('portal.none') }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Dynamic Custom Fields Container --}}
                    <div id="dynamicFieldsContainer" class="mb-4" style="display: none;">
                        <label class="form-label fw-bold text-dark mb-2">
                            <span class="badge me-2 rounded-pill" style="background:var(--clsu-green);color:white;font-size:0.7rem;padding:4px 8px;">2</span>
                            {{ __('portal.configure_parameters') }}
                        </label>
                        <div class="p-3 bg-light border row g-3 mx-0" id="dynamicFieldsBody" style="border-radius: 12px;">
                            <!-- Dynamic inputs will be appended here via JS -->
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="mobile-sticky-action-bar">
                        <button type="submit" class="btn-submit-app w-100" id="submitBtn" aria-describedby="submitHelpText">
                            <i class="fa-solid fa-paper-plane me-2"></i> {{ __('portal.submit_to_osa') }}
                        </button>
                    </div>
                    <div id="submitHelpText" class="text-danger small mt-2 text-center fw-semibold" style="display:none;" role="alert"></div>
                </form>
            </div>
        </div>

        {{-- RIGHT: Tips Panel --}}
        <div class="col-lg-5 order-first order-lg-last">
            {{-- Application Checklist Card --}}
            <div class="card p-4 mb-3 border-0 shadow-sm" style="border-radius:16px;">
                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list-check text-success me-2"></i> {{ __('portal.checklist') }}</h6>
                <div id="checklistItems" class="d-flex flex-column gap-2 small">
                    <div class="d-flex align-items-center justify-content-between" id="chkScholarship">
                        <span class="text-muted">1. {{ __('portal.select_scholarship') }}</span>
                        <span class="badge bg-danger rounded-pill"><i class="fa-solid fa-xmark"></i></span>
                    </div>
                </div>
            </div>

            <div class="tips-panel mb-3">
                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-lightbulb text-warning me-2"></i> {{ __('portal.submission_tips') }}</h6>
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
                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-route text-primary me-2"></i> {{ __('portal.what_happens_next') }}</h6>
                @foreach([
                    ['icon' => 'fa-paper-plane', 'color' => '#0284c7', 'bg' => '#e0f2fe', 'title' => __('portal.step_submission_title'), 'desc' => __('portal.step_submission_desc')],
                    ['icon' => 'fa-robot', 'color' => '#7c3aed', 'bg' => '#ede9fe', 'title' => __('portal.step_aiscan_title'), 'desc' => __('portal.step_aiscan_desc')],
                    ['icon' => 'fa-user-shield', 'color' => '#0F5934', 'bg' => '#dcfce7', 'title' => __('portal.step_evaluation_title'), 'desc' => __('portal.step_evaluation_desc')],
                    ['icon' => 'fa-envelope', 'color' => '#d97706', 'bg' => '#fef9c3', 'title' => __('portal.step_notification_title'), 'desc' => __('portal.step_notification_desc')],
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
    // Initialize localization dictionaries for JS usage
    window.portalTranslations = {
        select_scholarship_program: "{{ __('portal.select_scholarship_program') }}",
        optional: "{{ __('portal.optional') }}"
    };

    let selectedScholarshipGwa = null;

    function selectScholarship(el) {
        // Deselect all
        document.querySelectorAll('.scholarship-card-select').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');

        // Set hidden inputs
        document.getElementById('programNameInput').value = el.dataset.name;
        document.getElementById('scholarshipIdInput').value = el.dataset.id;
        selectedScholarshipGwa = parseFloat(el.dataset.gwa);

        // Run GWA check if input already has value
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
                        // Use full 12-column width for file uploads, textareas, or long field labels
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
                            input = document.createElement('input');
                            input.type = 'file';
                            input.className = 'form-control shadow-sm p-2 bg-white';
                            input.accept = 'image/*,application/pdf';

                            // Add file validation and preview
                            input.addEventListener('change', function(e) {
                                const file = e.target.files[0];
                                const previewId = `file-preview-${uniqueId}`;
                                let preview = document.getElementById(previewId);

                                if (!file) {
                                    if (preview) preview.remove();
                                    updateChecklist();
                                    return;
                                }

                                // Size check (10MB limit)
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
                                    updateChecklist();
                                    return;
                                }

                                // Format check
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
                                    updateChecklist();
                                    return;
                                }

                                // Show preview card
                                if (!preview) {
                                    preview = document.createElement('div');
                                    preview.id = previewId;
                                    preview.className = 'alert alert-success mt-2 mb-0 d-flex align-items-center justify-content-between';
                                    preview.style.fontSize = '0.85rem';
                                    formGroup.appendChild(preview);
                                }

                                const fileIcon = file.type === 'application/pdf' ? 'fa-file-pdf' : 'fa-file-image';
                                const fileColor = file.type === 'application/pdf' ? '#dc2626' : '#0284c7';
                                preview.innerHTML = `
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid ${fileIcon} fa-2x" style="color: ${fileColor};"></i>
                                        <div>
                                            <div class="fw-bold">${file.name}</div>
                                            <small class="text-muted">${(file.size / 1024).toFixed(1)} KB · ${file.type.split('/')[1].toUpperCase()}</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger clear-upload-btn">
                                        <i class="fa-solid fa-xmark"></i> Clear
                                    </button>
                                `;

                                preview.querySelector('.clear-upload-btn').addEventListener('click', function() {
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
                            // GWA, Income, and other numeric inputs get decimal keypad on mobile
                            input.setAttribute('inputmode', 'decimal');
                        } else if (field.field_type === 'date') {
                            input = document.createElement('input');
                            input.type = 'date';
                            input.className = 'form-control';
                        } else if (field.field_type === 'email') {
                            input = document.createElement('input');
                            input.type = 'email';
                            input.className = 'form-control';
                            input.placeholder = 'e.g., student@example.com';
                        } else {
                            input = document.createElement('input');
                            input.type = 'text';
                            input.className = 'form-control';
                        }
                        
                        input.id = uniqueId;
                        input.name = `custom_fields[${field.field_name}]`;
                        input.classList.add('custom-field-input');
                        input.dataset.label = field.field_label;
                        input.dataset.required = field.is_required ? '1' : '0';
                        if (field.is_required) {
                            input.setAttribute('required', 'required');
                            input.setAttribute('aria-required', 'true');
                            input.classList.add('required-custom-field');
                        }
                        
                        formGroup.appendChild(input);
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
        
        // In the Philippine grading scale, a larger number means a worse grade (1.0 = best, 3.0 = pass, 5.0 = fail)
        // If the student's entered GWA is higher than the scholarship GWA limit, show warning
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
        const chkScholarship = document.getElementById('chkScholarship');
        const submitBtn = document.getElementById('submitBtn');
        const submitHelpText = document.getElementById('submitHelpText');

        let allValid = true;
        let missingFields = [];

        // 1. Scholarship selected check
        const schId = document.getElementById('scholarshipIdInput').value;
        if (schId) {
            chkScholarship.querySelector('.badge').className = 'badge bg-success rounded-pill';
            chkScholarship.querySelector('.badge i').className = 'fa-solid fa-check';
            chkScholarship.querySelector('span').className = 'text-dark fw-semibold';
        } else {
            chkScholarship.querySelector('.badge').className = 'badge bg-danger rounded-pill';
            chkScholarship.querySelector('.badge i').className = 'fa-solid fa-xmark';
            chkScholarship.querySelector('span').className = 'text-muted';
            allValid = false;
            missingFields.push(window.portalTranslations.select_scholarship_program);
        }

        // 2. Dynamic custom fields checks
        const customFields = document.querySelectorAll('.custom-field-input');
        // Remove existing dynamic checklist items
        document.querySelectorAll('.dynamic-checklist-item').forEach(el => el.remove());

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

            const checklistItems = document.getElementById('checklistItems');
            const item = document.createElement('div');
            item.className = 'd-flex align-items-center justify-content-between dynamic-checklist-item';
            
            let badgeHtml = '';
            if (isFilled) {
                badgeHtml = `
                    <span class="badge bg-success rounded-pill">
                        <i class="fa-solid fa-check"></i>
                    </span>
                `;
            } else if (isRequired) {
                badgeHtml = `
                    <span class="badge bg-danger rounded-pill">
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                `;
            } else {
                badgeHtml = `
                    <span class="badge bg-secondary rounded-pill" style="font-size: 0.65rem;">
                        ${window.portalTranslations.optional}
                    </span>
                `;
            }

            item.innerHTML = `
                <span class="${isFilled ? 'text-dark fw-semibold' : 'text-muted'}">${input.dataset.label}${isRequired ? '' : ' (' + window.portalTranslations.optional + ')'}</span>
                ${badgeHtml}
            `;
            checklistItems.appendChild(item);
        });

        // Accessible disabled states: Button remains focusable but styled disabled
        if (allValid) {
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
            submitBtn.style.filter = 'none';
            submitBtn.setAttribute('aria-disabled', 'false');
            if (submitHelpText) {
                submitHelpText.style.display = 'none';
                submitHelpText.textContent = '';
            }
        } else {
            submitBtn.style.opacity = '0.6';
            submitBtn.style.cursor = 'not-allowed';
            submitBtn.style.filter = 'grayscale(30%)';
            submitBtn.setAttribute('aria-disabled', 'true');
            if (submitHelpText) {
                submitHelpText.style.display = 'block';
                submitHelpText.innerHTML = `<i class="fa-solid fa-circle-exclamation me-1"></i> Please complete: ${missingFields.join(', ')}`;
            }
        }
        
        // Cache the validation status on the form element
        document.getElementById('applicationForm').dataset.valid = allValid ? '1' : '0';
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateChecklist();
        const dynamicBody = document.getElementById('dynamicFieldsBody');
        if (dynamicBody) {
            dynamicBody.addEventListener('input', function(e) {
                updateChecklist();
                if (e.target.name && e.target.name.toLowerCase().includes('gwa')) {
                    checkGwaEligibility();
                }
            });
            dynamicBody.addEventListener('change', function(e) {
                updateChecklist();
                if (e.target.name && e.target.name.toLowerCase().includes('gwa')) {
                    checkGwaEligibility();
                }
            });
        }
        
        @if(isset($prevApp))
            const prevCard = document.querySelector('.scholarship-card-select[data-id="{{ $prevApp->scholarship_id }}"]');
            if (prevCard) {
                selectScholarship(prevCard);
            }
        @endif
    });

    // ── AJAX Application Form Submission ──────────────
    const appForm = document.getElementById('applicationForm');
    if (appForm) {
        appForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Perform accessibility and completion validation check
            if (appForm.dataset.valid !== '1') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Form Incomplete',
                    text: 'Please complete all required fields on the application form first.',
                    confirmButtonColor: '#0C4E2D'
                });
                return;
            }

            if (!appForm.checkValidity()) {
                appForm.reportValidity();
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Submitting...';

            // Show Uploading dialog with progress bar
            Swal.fire({
                title: 'Submitting Application',
                html: `
                    <p class="small text-muted mb-2">Encrypting files and uploading to OSA pipeline...</p>
                    <div class="progress" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: { popup: 'rounded-4' },
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send via XMLHttpRequest for upload progress tracking
            const xhr = new XMLHttpRequest();
            xhr.open('POST', appForm.action);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.upload.addEventListener('progress', function(event) {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    const progressBar = Swal.getPopup().querySelector('.progress-bar');
                    if (progressBar) {
                        progressBar.style.width = percent + '%';
                        progressBar.setAttribute('aria-valuenow', percent);
                    }
                }
            });

            xhr.onload = function() {
                let response = {};
                try {
                    response = JSON.parse(xhr.responseText);
                } catch(e) {
                    console.error('Error parsing response:', e);
                }

                if (xhr.status === 200 && response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonColor: '#0F5934',
                        customClass: { popup: 'rounded-4' }
                    }).then(() => {
                        window.location.href = "{{ route('student.dashboard') }}";
                    });
                } else {
                    let errMsg = response.message || 'Failed to submit application. Please try again.';
                    if (xhr.status === 422 && response.errors) {
                        errMsg = Object.values(response.errors).flat().join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        html: errMsg,
                        confirmButtonColor: '#dc2626',
                        customClass: { popup: 'rounded-4' }
                    });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            };

            xhr.onerror = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'A network error occurred. Please check your connection and try again.',
                    confirmButtonColor: '#dc2626',
                    customClass: { popup: 'rounded-4' }
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            };

            xhr.send(new FormData(appForm));
        });
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