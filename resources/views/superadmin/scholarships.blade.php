@extends('layouts.app')

@section('title', 'Program Manager | A.E.G.I.S.')
@section('page-title', 'Scholarship Manager')
@section('page-subtitle', 'Create grants and manage program availability for students')

@section('content')

<div class="d-flex justify-content-end align-items-center gap-2 mb-4">
    <a href="{{ route('superadmin.trash') }}" class="btn btn-outline-danger fw-bold px-4" style="border-radius: 10px;">
        <i class="fa-solid fa-trash-can me-1"></i> System Trash
    </a>
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
                    <th class="text-center">Max Renewals</th>
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
                        @if($scholarship->min_gwa_required)
                            <span style="background:#fef9c3;color:#a16207;border:1px solid #fde047;border-radius:20px;padding:3px 12px;font-size:0.78rem;font-weight:700;">
                                <i class="fa-solid fa-star me-1" style="font-size:0.6rem;"></i> ≤ {{ $scholarship->min_gwa_required }}
                            </span>
                        @else
                            <span style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:20px;padding:3px 12px;font-size:0.78rem;font-weight:700;">
                                None
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span style="background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd;border-radius:20px;padding:3px 12px;font-size:0.78rem;font-weight:700;">
                            <i class="fa-solid fa-rotate me-1" style="font-size:0.6rem;"></i> {{ $scholarship->max_renewals ?? 4 }}
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
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <form action="{{ route('superadmin.scholarships.toggle', $scholarship->id) }}" method="POST" class="d-inline">
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

                            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-3 edit-scholarship-btn" 
                                    style="font-size:0.78rem;" 
                                    data-id="{{ $scholarship->id }}"
                                    data-url="{{ route('superadmin.scholarships.show', $scholarship->id) }}">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                            </button>

                            <form action="{{ route('superadmin.scholarships.delete', $scholarship->id) }}" method="POST" class="d-inline delete-scholarship-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold rounded-pill px-3" style="font-size:0.78rem;">
                                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                                </button>
                            </form>
                        </div>
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
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f1f12, #0F5934); padding: 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0">
                        <i class="fa-solid fa-plus-circle text-warning me-2"></i> Create Scholarship Program
                    </h5>
                    <small class="text-white-50">Define a new grant and build custom application fields</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('superadmin.scholarships.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-4">
                        {{-- Left Column: Form Builder --}}
                        <div class="col-lg-7 border-end pe-lg-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold small text-muted" for="programName">Program Name</label>
                                    <input type="text" name="name" id="programName" class="form-control" required placeholder="e.g., DOST-SEI Merit Scholarship" autocomplete="off">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted" for="maxRenewals">Max Renewals</label>
                                    <input type="number" min="1" max="12" name="max_renewals" id="maxRenewals" class="form-control" required value="4" placeholder="e.g., 4">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted" for="programDesc">Program Description</label>
                                <textarea name="description" id="programDesc" class="form-control" rows="2" required
                                          placeholder="Brief overview of grant requirements and benefits..."
                                          style="resize:none;"></textarea>
                            </div>

                            <hr class="my-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-list-check text-success me-2"></i> Custom Form Fields</h6>
                                <button type="button" id="addFieldBtn" class="btn btn-sm btn-outline-success fw-bold px-3" style="border-radius: 8px;">
                                    <i class="fa-solid fa-plus me-1"></i> Add Custom Field
                                </button>
                            </div>
                            
                            <div id="fieldsContainer" class="p-3 bg-light border mb-0" style="border-radius: 12px; max-height: 280px; overflow-y: auto;">
                                <div class="text-center text-muted small py-3" id="noFieldsText">
                                    No custom fields added yet. Add custom fields (such as GWA, Profile Details, or Document Uploads) to build your application form.
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Live Form Preview --}}
                        <div class="col-lg-5 ps-lg-4">
                            <div class="card p-3 bg-light border-0 shadow-sm" style="border-radius: 12px;">
                                <h6 class="fw-bold mb-3 text-secondary" style="font-size:0.82rem; letter-spacing:0.5px; text-transform:uppercase;">
                                    <i class="fa-solid fa-eye text-primary me-1"></i> Live Form Preview
                                </h6>

                                {{-- Dynamic Custom Fields Preview Container --}}
                                <div class="small fw-bold text-muted mb-2 text-start" style="font-size: 0.72rem; letter-spacing: 0.3px; text-transform: uppercase;">Custom Parameters</div>
                                <div id="livePreviewContainer" class="d-flex flex-column gap-3">
                                    <div class="text-center text-muted small py-4" id="emptyPreviewText">
                                        Custom fields you add will preview here in real time.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light fw-semibold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn fw-bold rounded-pill px-5 text-white"
                            style="background: linear-gradient(135deg, var(--clsu-green), #16703f);">
                        <i class="fa-solid fa-save me-1"></i> Save Program
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Program Modal --}}
<div class="modal fade" id="editProgramModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f1f12, #0F5934); padding: 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0">
                        <i class="fa-solid fa-pen-to-square text-warning me-2"></i> Edit Scholarship Program
                    </h5>
                    <small class="text-white-50">Modify program parameters and custom field requirements</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProgramForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-4">
                        {{-- Left Column: Form Builder --}}
                        <div class="col-lg-7 border-end pe-lg-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold small text-muted" for="editProgramName">Program Name</label>
                                    <input type="text" name="name" id="editProgramName" class="form-control" required placeholder="e.g., DOST-SEI Merit Scholarship" autocomplete="off">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted" for="editMaxRenewals">Max Renewals</label>
                                    <input type="number" min="1" max="12" name="max_renewals" id="editMaxRenewals" class="form-control" required placeholder="e.g., 4">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted" for="editProgramDesc">Program Description</label>
                                <textarea name="description" id="editProgramDesc" class="form-control" rows="2" required
                                          placeholder="Brief overview of grant requirements and benefits..."
                                          style="resize:none;"></textarea>
                            </div>

                            <hr class="my-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-list-check text-success me-2"></i> Custom Form Fields</h6>
                                <button type="button" id="editAddFieldBtn" class="btn btn-sm btn-outline-success fw-bold px-3" style="border-radius: 8px;">
                                    <i class="fa-solid fa-plus me-1"></i> Add Custom Field
                                </button>
                            </div>
                            
                            <div id="editFieldsContainer" class="p-3 bg-light border mb-0" style="border-radius: 12px; max-height: 280px; overflow-y: auto;">
                                <div class="text-center text-muted small py-3" id="editNoFieldsText">
                                    No custom fields added yet. Add custom fields (such as GWA, Profile Details, or Document Uploads) to build your application form.
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Live Form Preview --}}
                        <div class="col-lg-5 ps-lg-4">
                            <div class="card p-3 bg-light border-0 shadow-sm" style="border-radius: 12px;">
                                <h6 class="fw-bold mb-3 text-secondary" style="font-size:0.82rem; letter-spacing:0.5px; text-transform:uppercase;">
                                    <i class="fa-solid fa-eye text-primary me-1"></i> Live Form Preview
                                </h6>

                                {{-- Dynamic Custom Fields Preview Container --}}
                                <div class="small fw-bold text-muted mb-2 text-start" style="font-size: 0.72rem; letter-spacing: 0.3px; text-transform: uppercase;">Custom Parameters</div>
                                <div id="editLivePreviewContainer" class="d-flex flex-column gap-3">
                                    <div class="text-center text-muted small py-4" id="editEmptyPreviewText">
                                        Custom fields you add will preview here in real time.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light fw-semibold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn fw-bold rounded-pill px-5 text-white"
                            style="background: linear-gradient(135deg, var(--clsu-green), #16703f);">
                        <i class="fa-solid fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let fieldIndex = 0;

    function reindexFields(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const rows = container.querySelectorAll('.field-row');
        rows.forEach((row, index) => {
            const labelInput = row.querySelector('.field-label-input');
            const typeSelect = row.querySelector('.field-type-select');
            const requiredCheck = row.querySelector('.field-required-check');
            const optionsInput = row.querySelector('.field-options-input');

            if (labelInput) labelInput.name = `fields[${index}][label]`;
            if (typeSelect) typeSelect.name = `fields[${index}][type]`;
            if (requiredCheck) requiredCheck.name = `fields[${index}][required]`;
            if (optionsInput) optionsInput.name = `fields[${index}][options]`;
        });
    }

    function renderLivePreview() {
        const container = document.getElementById('livePreviewContainer');
        const rows = document.querySelectorAll('#fieldsContainer .field-row');
        
        if (rows.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted small py-4" id="emptyPreviewText">
                    Custom fields you add will preview here in real time.
                </div>
            `;
            return;
        }

        container.innerHTML = ''; // Clear existing preview

        rows.forEach((row) => {
            const labelInput = row.querySelector('.field-label-input');
            const typeSelect = row.querySelector('.field-type-select');
            const requiredCheck = row.querySelector('.field-required-check');
            const optionsInput = row.querySelector('.field-options-input');

            const label = (labelInput ? labelInput.value.trim() : '') || 'Untitled Field';
            const type = typeSelect ? typeSelect.value : 'text';
            const isRequired = requiredCheck ? requiredCheck.checked : false;
            
            const fieldWrapper = document.createElement('div');
            fieldWrapper.className = 'mb-1 text-start';
            
            let labelHtml = `<label class="form-label small fw-semibold text-dark mb-0">${label}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>`;
            let inputHtml = '';

            if (type === 'text') {
                inputHtml = `<input type="text" class="form-control form-control-sm bg-white" disabled placeholder="Short answer text">`;
            } else if (type === 'number') {
                inputHtml = `<input type="number" class="form-control form-control-sm bg-white" disabled placeholder="0">`;
            } else if (type === 'textarea') {
                inputHtml = `<textarea class="form-control form-control-sm bg-white" rows="2" disabled placeholder="Long answer text" style="resize:none;"></textarea>`;
            } else if (type === 'file') {
                inputHtml = `<input type="file" class="form-control form-control-sm bg-white" disabled>`;
            } else if (type === 'date') {
                inputHtml = `<input type="date" class="form-control form-control-sm bg-white" disabled>`;
            } else if (type === 'email') {
                inputHtml = `<input type="email" class="form-control form-control-sm bg-white" disabled placeholder="student@example.com">`;
            } else if (type === 'select') {
                const optionsVal = optionsInput ? optionsInput.value : '';
                const options = optionsVal ? optionsVal.split(',').map(o => o.trim()).filter(Boolean) : ['Option 1', 'Option 2'];
                
                let selectOptionsHtml = options.map(opt => `<option>${opt}</option>`).join('');
                inputHtml = `<select class="form-select form-select-sm bg-white" disabled>${selectOptionsHtml}</select>`;
            }

            fieldWrapper.innerHTML = labelHtml + inputHtml;
            container.appendChild(fieldWrapper);
        });
    }

    document.getElementById('addFieldBtn').addEventListener('click', function() {
        const container = document.getElementById('fieldsContainer');
        const noFieldsText = document.getElementById('noFieldsText');
        if (noFieldsText) {
            noFieldsText.remove();
        }

        const row = document.createElement('div');
        row.className = 'field-row bg-white p-3 border mb-3 position-relative';
        row.style.borderRadius = '10px';
        row.innerHTML = `
            <div class="position-absolute top-0 end-0 m-2 d-flex gap-1 align-items-center">
                <button type="button" class="btn btn-xs btn-outline-secondary p-1 move-up-btn" style="line-height:1; font-size:0.65rem; border-radius:4px;"><i class="fa-solid fa-arrow-up"></i></button>
                <button type="button" class="btn btn-xs btn-outline-secondary p-1 move-down-btn" style="line-height:1; font-size:0.65rem; border-radius:4px;"><i class="fa-solid fa-arrow-down"></i></button>
                <button type="button" class="btn-close remove-field-btn" style="font-size: 0.75rem; margin-left:4px;"></button>
            </div>
            <div class="row g-2 text-start pt-2">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-muted mb-1">Field Label</label>
                    <input type="text" name="fields[${fieldIndex}][label]" class="form-control form-control-sm field-label-input" required placeholder="e.g., Annual Household Income">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Field Type</label>
                    <select name="fields[${fieldIndex}][type]" class="form-select form-select-sm field-type-select" required>
                        <option value="text">Short Text</option>
                        <option value="number">Number</option>
                        <option value="textarea">Paragraph Text</option>
                        <option value="select">Dropdown Select</option>
                        <option value="file">File Upload</option>
                        <option value="date">Date Picker</option>
                        <option value="email">Email Address</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Required</label>
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input field-required-check" type="checkbox" name="fields[${fieldIndex}][required]" value="1" checked>
                    </div>
                </div>
                <div class="col-md-12 select-options-wrapper d-none">
                    <label class="form-label small fw-semibold text-muted mb-1">Dropdown Options (Comma-separated)</label>
                    <input type="text" name="fields[${fieldIndex}][options]" class="form-control form-control-sm field-options-input" placeholder="Option 1, Option 2, Option 3">
                </div>
            </div>
        `;

        container.appendChild(row);
        reindexFields('fieldsContainer');

        // Bind live preview events
        const labelInput = row.querySelector('.field-label-input');
        const typeSelect = row.querySelector('.field-type-select');
        const requiredCheck = row.querySelector('.field-required-check');
        const optionsInput = row.querySelector('.field-options-input');
        const optionsWrapper = row.querySelector('.select-options-wrapper');

        labelInput.addEventListener('input', renderLivePreview);
        requiredCheck.addEventListener('change', renderLivePreview);
        optionsInput.addEventListener('input', renderLivePreview);

        typeSelect.addEventListener('change', function() {
            if (this.value === 'select') {
                optionsWrapper.classList.remove('d-none');
                optionsWrapper.querySelector('input').setAttribute('required', 'required');
            } else {
                optionsWrapper.classList.add('d-none');
                optionsWrapper.querySelector('input').removeAttribute('required');
            }
            renderLivePreview();
        });

        // Reordering arrows
        row.querySelector('.move-up-btn').addEventListener('click', function() {
            const prev = row.previousElementSibling;
            if (prev && !prev.id.includes('noFieldsText') && prev.classList.contains('field-row')) {
                prev.before(row);
                reindexFields('fieldsContainer');
                renderLivePreview();
            }
        });

        row.querySelector('.move-down-btn').addEventListener('click', function() {
            const next = row.nextElementSibling;
            if (next && next.classList.contains('field-row')) {
                next.after(row);
                reindexFields('fieldsContainer');
                renderLivePreview();
            }
        });

        // Remove row logic
        row.querySelector('.remove-field-btn').addEventListener('click', function() {
            row.remove();
            reindexFields('fieldsContainer');
            if (container.children.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted small py-3" id="noFieldsText">
                        No custom fields added yet. Add custom fields (such as GWA, Profile Details, or Document Uploads) to build your application form.
                    </div>
                `;
            }
            renderLivePreview();
        });

        fieldIndex++;
        renderLivePreview();
    });

    // ── AJAX Program Creation ─────────────────────────
    const newProgramForm = document.querySelector('#newProgramModal form');
    if (newProgramForm) {
        newProgramForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = newProgramForm.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Saving...';

            // Clear previous errors
            newProgramForm.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            newProgramForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            try {
                const response = await fetch(newProgramForm.action, {
                    method: 'POST',
                    body: new FormData(newProgramForm),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (response.status === 422) {
                    // Validation errors
                    Swal.fire({ icon: 'error', title: 'Validation Failed', text: 'Please check the form inputs.', confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                    
                    if (data.errors) {
                        for (const [field, messages] of Object.entries(data.errors)) {
                            // Find corresponding input field
                            let input = newProgramForm.querySelector(`[name="${field}"]`);
                            if (!input && field.startsWith('fields.')) {
                                // Match dynamic field names
                                const parts = field.split('.');
                                const index = parts[1];
                                const subfield = parts[2];
                                input = newProgramForm.querySelector(`[name="fields[${index}][${subfield}]"]`);
                            }
                            
                            if (input) {
                                input.classList.add('is-invalid');
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = messages[0];
                                input.parentElement.appendChild(errorDiv);
                            }
                        }
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                } else if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4' }
                    });
                    
                    // Close Modal and reload
                    const modalEl = document.getElementById('newProgramModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Save Program Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        });
    }

    // ── AJAX Program Toggle Status ────────────────────
    document.querySelector('table').addEventListener('submit', async (e) => {
        const form = e.target;
        if (form.action && form.action.includes('/toggle')) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Updating...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4' }
                    });

                    // Update UI status badge and button inline
                    const row = form.closest('tr');
                    const statusCell = row.querySelector('.status-badge').parentElement;
                    const actionCell = form.parentElement;

                    if (data.status === 'Active') {
                        statusCell.innerHTML = `
                            <span class="status-badge approved">
                                <i class="fa-solid fa-circle-dot" style="font-size:0.5rem;"></i> Open
                            </span>
                        `;
                        actionCell.innerHTML = `
                            <form action="${form.action}" method="POST">
                                <input type="hidden" name="_token" value="${form.querySelector('[name="_token"]').value}">
                                <button type="submit" class="btn btn-sm fw-semibold rounded-pill px-3"
                                        style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;font-size:0.78rem;">
                                    <i class="fa-solid fa-lock me-1"></i> Close
                                </button>
                            </form>
                        `;
                    } else {
                        statusCell.innerHTML = `
                            <span class="status-badge rejected">
                                <i class="fa-solid fa-circle-dot" style="font-size:0.5rem;"></i> Closed
                            </span>
                        `;
                        actionCell.innerHTML = `
                            <form action="${form.action}" method="POST">
                                <input type="hidden" name="_token" value="${form.querySelector('[name="_token"]').value}">
                                <button type="submit" class="btn btn-sm fw-semibold rounded-pill px-3"
                                        style="background:#dcfce7;color:#15803d;border:1px solid #86efac;font-size:0.78rem;">
                                    <i class="fa-solid fa-lock-open me-1"></i> Open
                                </button>
                            </form>
                        `;
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error('Toggle Status Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }
    });

    // ── AJAX Program Editing ─────────────────────────
    let editFieldIndex = 0;

    function renderEditLivePreview() {
        const container = document.getElementById('editLivePreviewContainer');
        const rows = document.querySelectorAll('#editFieldsContainer .field-row');
        
        if (rows.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted small py-4" id="editEmptyPreviewText">
                    Custom fields you add will preview here in real time.
                </div>
            `;
            return;
        }

        container.innerHTML = ''; // Clear existing preview

        rows.forEach((row) => {
            const labelInput = row.querySelector('.field-label-input');
            const typeSelect = row.querySelector('.field-type-select');
            const requiredCheck = row.querySelector('.field-required-check');
            const optionsInput = row.querySelector('.field-options-input');

            const label = (labelInput ? labelInput.value.trim() : '') || 'Untitled Field';
            const type = typeSelect ? typeSelect.value : 'text';
            const isRequired = requiredCheck ? requiredCheck.checked : false;
            
            const fieldWrapper = document.createElement('div');
            fieldWrapper.className = 'mb-1 text-start';
            
            let labelHtml = `<label class="form-label small fw-semibold text-dark mb-0">${label}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>`;
            let inputHtml = '';

            if (type === 'text') {
                inputHtml = `<input type="text" class="form-control form-control-sm bg-white" disabled placeholder="Short answer text">`;
            } else if (type === 'number') {
                inputHtml = `<input type="number" class="form-control form-control-sm bg-white" disabled placeholder="0">`;
            } else if (type === 'textarea') {
                inputHtml = `<textarea class="form-control form-control-sm bg-white" rows="2" disabled placeholder="Long answer text" style="resize:none;"></textarea>`;
            } else if (type === 'file') {
                inputHtml = `<input type="file" class="form-control form-control-sm bg-white" disabled>`;
            } else if (type === 'date') {
                inputHtml = `<input type="date" class="form-control form-control-sm bg-white" disabled>`;
            } else if (type === 'email') {
                inputHtml = `<input type="email" class="form-control form-control-sm bg-white" disabled placeholder="student@example.com">`;
            } else if (type === 'select') {
                const optionsVal = optionsInput ? optionsInput.value : '';
                const options = optionsVal ? optionsVal.split(',').map(o => o.trim()).filter(Boolean) : ['Option 1', 'Option 2'];
                
                let selectOptionsHtml = options.map(opt => `<option>${opt}</option>`).join('');
                inputHtml = `<select class="form-select form-select-sm bg-white" disabled>${selectOptionsHtml}</select>`;
            }

            fieldWrapper.innerHTML = labelHtml + inputHtml;
            container.appendChild(fieldWrapper);
        });
    }

    function addEditFieldRow(label = '', type = 'text', required = true, options = '') {
        const container = document.getElementById('editFieldsContainer');
        const noFieldsText = document.getElementById('editNoFieldsText');
        if (noFieldsText) {
            noFieldsText.remove();
        }

        const row = document.createElement('div');
        row.className = 'field-row bg-white p-3 border mb-3 position-relative';
        row.style.borderRadius = '10px';
        row.innerHTML = `
            <div class="position-absolute top-0 end-0 m-2 d-flex gap-1 align-items-center">
                <button type="button" class="btn btn-xs btn-outline-secondary p-1 move-up-btn" style="line-height:1; font-size:0.65rem; border-radius:4px;"><i class="fa-solid fa-arrow-up"></i></button>
                <button type="button" class="btn btn-xs btn-outline-secondary p-1 move-down-btn" style="line-height:1; font-size:0.65rem; border-radius:4px;"><i class="fa-solid fa-arrow-down"></i></button>
                <button type="button" class="btn-close remove-field-btn" style="font-size: 0.75rem; margin-left:4px;"></button>
            </div>
            <div class="row g-2 text-start pt-2">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-muted mb-1">Field Label</label>
                    <input type="text" name="fields[${editFieldIndex}][label]" class="form-control form-control-sm field-label-input" required placeholder="e.g., Annual Household Income" value="${escapeHtml(label)}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Field Type</label>
                    <select name="fields[${editFieldIndex}][type]" class="form-select form-select-sm field-type-select" required>
                        <option value="text" ${type === 'text' ? 'selected' : ''}>Short Text</option>
                        <option value="number" ${type === 'number' ? 'selected' : ''}>Number</option>
                        <option value="textarea" ${type === 'textarea' ? 'selected' : ''}>Paragraph Text</option>
                        <option value="select" ${type === 'select' ? 'selected' : ''}>Dropdown Select</option>
                        <option value="file" ${type === 'file' ? 'selected' : ''}>File Upload</option>
                        <option value="date" ${type === 'date' ? 'selected' : ''}>Date Picker</option>
                        <option value="email" ${type === 'email' ? 'selected' : ''}>Email Address</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Required</label>
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input field-required-check" type="checkbox" name="fields[${editFieldIndex}][required]" value="1" ${required ? 'checked' : ''}>
                    </div>
                </div>
                <div class="col-md-12 select-options-wrapper ${type === 'select' ? '' : 'd-none'}">
                    <label class="form-label small fw-semibold text-muted mb-1">Dropdown Options (Comma-separated)</label>
                    <input type="text" name="fields[${editFieldIndex}][options]" class="form-control form-control-sm field-options-input" placeholder="Option 1, Option 2, Option 3" value="${escapeHtml(options)}" ${type === 'select' ? 'required' : ''}>
                </div>
            </div>
        `;

        container.appendChild(row);
        reindexFields('editFieldsContainer');

        // Bind live preview events
        const labelInput = row.querySelector('.field-label-input');
        const typeSelect = row.querySelector('.field-type-select');
        const requiredCheck = row.querySelector('.field-required-check');
        const optionsInput = row.querySelector('.field-options-input');
        const optionsWrapper = row.querySelector('.select-options-wrapper');

        labelInput.addEventListener('input', renderEditLivePreview);
        requiredCheck.addEventListener('change', renderEditLivePreview);
        optionsInput.addEventListener('input', renderEditLivePreview);

        typeSelect.addEventListener('change', function() {
            if (this.value === 'select') {
                optionsWrapper.classList.remove('d-none');
                optionsWrapper.querySelector('input').setAttribute('required', 'required');
            } else {
                optionsWrapper.classList.add('d-none');
                optionsWrapper.querySelector('input').removeAttribute('required');
            }
            renderEditLivePreview();
        });

        // Reordering arrows
        row.querySelector('.move-up-btn').addEventListener('click', function() {
            const prev = row.previousElementSibling;
            if (prev && !prev.id.includes('editNoFieldsText') && prev.classList.contains('field-row')) {
                prev.before(row);
                reindexFields('editFieldsContainer');
                renderEditLivePreview();
            }
        });

        row.querySelector('.move-down-btn').addEventListener('click', function() {
            const next = row.nextElementSibling;
            if (next && next.classList.contains('field-row')) {
                next.after(row);
                reindexFields('editFieldsContainer');
                renderEditLivePreview();
            }
        });

        row.querySelector('.remove-field-btn').addEventListener('click', function() {
            row.remove();
            reindexFields('editFieldsContainer');
            if (container.children.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted small py-3" id="editNoFieldsText">
                        No custom fields added yet. Add custom fields (such as GWA, Profile Details, or Document Uploads) to build your application form.
                    </div>
                `;
            }
            renderEditLivePreview();
        });

        editFieldIndex++;
        renderEditLivePreview();
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    document.getElementById('editAddFieldBtn').addEventListener('click', () => addEditFieldRow());

    // Click handler to open Edit Modal and fill details
    document.querySelectorAll('.edit-scholarship-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const url = this.getAttribute('data-url');
            const editModalEl = document.getElementById('editProgramModal');
            const editForm = document.getElementById('editProgramForm');
            const container = document.getElementById('editFieldsContainer');
            
            // Clear fields container
            container.innerHTML = `
                <div class="text-center text-muted small py-3" id="editNoFieldsText">
                    No custom fields added yet. Only the standard GWA and COG upload will be required.
                </div>
            `;
            editFieldIndex = 0;
            renderEditLivePreview();

            Swal.fire({
                title: 'Loading Details...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                customClass: { popup: 'rounded-4' }
            });

            try {
                const response = await fetch(url);
                const data = await response.json();
                Swal.close();

                if (data.success) {
                    const s = data.scholarship;
                    document.getElementById('editProgramName').value = s.name;
                    document.getElementById('editMaxRenewals').value = s.max_renewals ?? 4;
                    document.getElementById('editProgramDesc').value = s.description;

                    // Set form update URL action
                    editForm.action = `/superadmin/scholarships/${s.id}`;

                    // Load custom fields
                    if (s.fields && s.fields.length > 0) {
                        s.fields.forEach(field => {
                            let optionsStr = '';
                            if (field.options && Array.isArray(field.options)) {
                                optionsStr = field.options.join(', ');
                            } else if (field.options) {
                                optionsStr = field.options;
                            }
                            addEditFieldRow(field.field_label, field.field_type, field.is_required, optionsStr);
                        });
                    }

                    // Open bootstrap modal
                    const modal = new bootstrap.Modal(editModalEl);
                    modal.show();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not fetch program details.', confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.', confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
            }
        });
    });

    // AJAX Form submission for edit
    document.getElementById('editProgramForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalHtml = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Saving Changes...';

        this.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            if (response.status === 422) {
                Swal.fire({ icon: 'error', title: 'Validation Failed', text: 'Please check your inputs.', confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                if (data.errors) {
                    for (const [field, messages] of Object.entries(data.errors)) {
                        let input = this.querySelector(`[name="${field}"]`);
                        if (!input && field.startsWith('fields.')) {
                            const parts = field.split('.');
                            const index = parts[1];
                            const subfield = parts[2];
                            input = this.querySelector(`[name="fields[${index}][${subfield}]"]`);
                        }
                        if (input) {
                            input.classList.add('is-invalid');
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback';
                            errorDiv.textContent = messages[0];
                            input.parentElement.appendChild(errorDiv);
                        }
                    }
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            } else if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated Successfully!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-4' }
                });
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProgramModal'));
                if (modal) modal.hide();
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-4' } });
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        } catch (error) {
            console.error('Update Program Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        }
    });

    // ── Delete Scholarship program ────────────────────
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('.delete-scholarship-form');
        if (form) {
            e.preventDefault();
            const result = await Swal.fire({
                title: 'Delete Scholarship Program?',
                text: "This will move the scholarship to the System Trash. Students won't be able to apply to it anymore.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Deleting...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;
                    }
                } catch (error) {
                    console.error('Delete Scholarship Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            }
        }
    });
</script>
@endpush