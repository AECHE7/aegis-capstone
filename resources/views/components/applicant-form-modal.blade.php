{{-- Reusable Applicant Official Form Preview & PDF Export Modal --}}
<div class="modal fade" id="applicantFormModal" tabindex="-1" aria-labelledby="applicantFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden; background: #f8fafc;">
            {{-- Modal Header --}}
            <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(15, 89, 52, 0.1); color: #0F5934; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="applicantFormModalLabel">
                            Official Applicant Information & Evaluation Form
                        </h6>
                        <small class="text-muted" id="applicantFormModalSubtitle">Authenticated institutional record preview</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- 1-Click PDF Export Button --}}
                    <a href="#" id="applicantFormDownloadBtn" target="_blank" class="btn btn-sm btn-success fw-bold px-3 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5" style="background: linear-gradient(135deg, #0F5934, #15803d); border: none;">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Export Official PDF</span>
                    </a>

                    {{-- Print Preview Button --}}
                    <button type="button" id="applicantFormPrintBtn" class="btn btn-sm btn-outline-secondary fw-semibold px-3 py-1.5 rounded-pill d-inline-flex align-items-center gap-1.5">
                        <i class="fa-solid fa-print"></i>
                        <span>Print</span>
                    </button>

                    {{-- Close Button --}}
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-3 p-md-4" style="background-color: #f1f5f9;">
                <div id="applicantFormModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-success mb-2" role="status" style="width: 2.5rem; height: 2.5rem;">
                            <span class="visually-hidden">Loading form...</span>
                        </div>
                        <div class="text-muted small fw-semibold">Generating authentic application form...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #applicantFormModal, #applicantFormModal * {
            visibility: visible;
        }
        #applicantFormModal {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            background: transparent !important;
        }
        .modal-header, .modal-backdrop, .btn, .btn-close {
            display: none !important;
        }
        .applicant-form-sheet {
            box-shadow: none !important;
            border: none !important;
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
        }
    }
</style>

<script>
    window.openApplicantFormModal = function(applicationId) {
        if (!applicationId) return;

        const modalEl = document.getElementById('applicantFormModal');
        if (!modalEl) {
            console.error('applicantFormModal element not found');
            return;
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const bodyEl = document.getElementById('applicantFormModalBody');
        const downloadBtn = document.getElementById('applicantFormDownloadBtn');
        const subtitleEl = document.getElementById('applicantFormModalSubtitle');

        // Set loading state
        bodyEl.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-success mb-2" role="status" style="width: 2.5rem; height: 2.5rem;">
                    <span class="visually-hidden">Loading form...</span>
                </div>
                <div class="text-muted small fw-semibold">Generating authentic application form...</div>
            </div>
        `;

        // Update download PDF link dynamically
        downloadBtn.href = `/admin/review/${applicationId}/download-form`;
        downloadBtn.classList.add('disabled');

        modal.show();

        fetch(`/admin/applications/${applicationId}/preview-form`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok (' + response.status + ')');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.html) {
                bodyEl.innerHTML = data.html;
                if (data.control_no && data.student_name) {
                    subtitleEl.textContent = `${data.control_no} • ${data.student_name} (${data.program_name || 'Scholarship'})`;
                }
                downloadBtn.href = data.download_url || `/admin/review/${applicationId}/download-form`;
                downloadBtn.classList.remove('disabled');
            } else {
                bodyEl.innerHTML = `
                    <div class="alert alert-danger text-center my-4 py-4" style="border-radius: 12px;">
                        <i class="fa-solid fa-circle-exclamation fa-2x mb-2 text-danger"></i>
                        <h6 class="fw-bold">Unable to Load Form</h6>
                        <p class="small text-muted mb-0">We encountered an issue retrieving the official form. Please try downloading the PDF directly.</p>
                    </div>
                `;
                downloadBtn.classList.remove('disabled');
            }
        })
        .catch(err => {
            console.error('Error fetching applicant form preview:', err);
            bodyEl.innerHTML = `
                <div class="alert alert-danger text-center my-4 py-4" style="border-radius: 12px;">
                    <i class="fa-solid fa-triangle-exclamation fa-2x mb-2 text-danger"></i>
                    <h6 class="fw-bold">Failed to load preview</h6>
                    <p class="small text-muted mb-3">${err.message || 'An unexpected network error occurred.'}</p>
                    <a href="/admin/review/${applicationId}/download-form" class="btn btn-sm btn-outline-danger fw-semibold rounded-pill px-3">
                        <i class="fa-solid fa-file-pdf me-1"></i> Try Direct PDF Download
                    </a>
                </div>
            `;
            downloadBtn.classList.remove('disabled');
        });
    };

    // Print handler
    document.addEventListener('DOMContentLoaded', function() {
        const printBtn = document.getElementById('applicantFormPrintBtn');
        if (printBtn) {
            printBtn.addEventListener('click', function() {
                window.print();
            });
        }
    });
</script>
