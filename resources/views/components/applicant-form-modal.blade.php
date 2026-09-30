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
        @page {
            size: letter portrait;
            margin: 4mm 6mm;
        }
        /* Hide surrounding portal navigation and modal headers */
        nav, header, footer, .sidebar, .topbar, .mobile-bottom-nav, 
        .modal-header, .modal-backdrop, .btn, .btn-close, .content-header {
            display: none !important;
        }
        body.modal-open {
            overflow: visible !important;
            background: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        #applicantFormModal {
            display: block !important;
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            overflow: visible !important;
            z-index: 99999 !important;
        }
        #applicantFormModal .modal-dialog {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            transform: none !important;
        }
        #applicantFormModal .modal-content {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 0 !important;
        }
        #applicantFormModal .modal-body {
            padding: 0 !important;
            background: transparent !important;
        }
        .applicant-form-sheet {
            box-shadow: none !important;
            border: none !important;
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 7.2pt !important;
            line-height: 1.16 !important;
            page-break-after: avoid !important;
            page-break-inside: avoid !important;
        }
        .applicant-form-sheet .row.align-items-center.pb-3.mb-3 {
            padding-bottom: 2px !important;
            margin-bottom: 3px !important;
        }
        .applicant-form-sheet img {
            max-height: 44px !important;
            width: auto !important;
        }
        .applicant-form-sheet .mb-3 {
            margin-bottom: 3px !important;
        }
        .applicant-form-sheet .p-3 {
            padding: 3px 6px !important;
        }
        .applicant-form-sheet h5 {
            font-size: 8.2pt !important;
            margin-bottom: 2px !important;
            line-height: 1.15 !important;
        }
        .applicant-form-sheet .table {
            margin-bottom: 0 !important;
            font-size: 7pt !important;
        }
        .applicant-form-sheet .table td,
        .applicant-form-sheet .table th {
            padding: 1.5px 4px !important;
            line-height: 1.12 !important;
        }
        .applicant-form-sheet .rounded-top,
        .applicant-form-sheet .section-header {
            padding: 1.5px 5px !important;
            font-size: 7pt !important;
        }
        .applicant-form-sheet .text-muted.text-uppercase {
            font-size: 5.8pt !important;
            margin-bottom: 0 !important;
        }
        .applicant-form-sheet .attestation-box,
        .applicant-form-sheet .bg-light {
            padding: 2.5px 5px !important;
            font-size: 6.2pt !important;
            line-height: 1.15 !important;
            margin-bottom: 3px !important;
        }
        .applicant-form-sheet .row.pt-2.pb-3 {
            padding-top: 2px !important;
            padding-bottom: 3px !important;
        }
        .applicant-form-sheet .border-warning {
            padding: 3px 5px !important;
            margin-bottom: 2px !important;
        }
        .applicant-form-sheet .badge {
            font-size: 5.8pt !important;
            padding: 1px 3px !important;
        }
        .applicant-form-sheet .border-top {
            margin-top: 2px !important;
            padding-top: 2px !important;
            font-size: 5.5pt !important;
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

    // Dedicated Isolated Print Engine for Zero-Failure Modal Printing
    window.printApplicantFormSheet = function() {
        const modalBody = document.getElementById('applicantFormModalBody');
        const sheet = modalBody ? modalBody.querySelector('.applicant-form-sheet') : null;
        if (!sheet) {
            window.print();
            return;
        }

        let printFrame = document.getElementById('applicantFormPrintFrame');
        if (!printFrame) {
            printFrame = document.createElement('iframe');
            printFrame.id = 'applicantFormPrintFrame';
            printFrame.style.position = 'fixed';
            printFrame.style.right = '0';
            printFrame.style.bottom = '0';
            printFrame.style.width = '0';
            printFrame.style.height = '0';
            printFrame.style.border = '0';
            document.body.appendChild(printFrame);
        }

        const doc = printFrame.contentWindow.document;
        doc.open();
        doc.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Official Applicant Information & Evaluation Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @page { size: letter portrait; margin: 4mm 6mm; }
        body { 
            background: #ffffff !important; 
            margin: 0 !important; 
            padding: 0 !important; 
            font-family: Arial, Helvetica, sans-serif !important; 
            font-size: 7.2pt !important; 
            line-height: 1.15 !important; 
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important; 
        }
        .applicant-form-sheet { 
            box-shadow: none !important; 
            border: none !important; 
            max-width: 100% !important; 
            width: 100% !important; 
            padding: 0 !important; 
            margin: 0 !important; 
            font-size: 7.2pt !important; 
            line-height: 1.15 !important; 
            page-break-after: avoid !important;
            page-break-inside: avoid !important; 
        }
        .applicant-form-sheet .row.align-items-center.pb-3.mb-3 {
            padding-bottom: 2px !important;
            margin-bottom: 3px !important;
        }
        .applicant-form-sheet img {
            max-height: 44px !important;
            width: auto !important;
        }
        .applicant-form-sheet h5 {
            font-size: 8.5pt !important;
            margin-bottom: 1px !important;
        }
        .applicant-form-sheet .border-success.p-3 {
            padding: 4px 6px !important;
            margin-bottom: 3px !important;
        }
        .applicant-form-sheet .rounded.d-flex.flex-column {
            width: 60px !important;
            height: 60px !important;
        }
        .applicant-form-sheet .mb-3 {
            margin-bottom: 3px !important;
        }
        .applicant-form-sheet table {
            margin-bottom: 0 !important;
            font-size: 7pt !important;
        }
        .applicant-form-sheet table td,
        .applicant-form-sheet table th {
            padding: 1.5px 4px !important;
            line-height: 1.12 !important;
        }
        .applicant-form-sheet .rounded-top,
        .applicant-form-sheet .section-header {
            padding: 1.5px 5px !important;
            font-size: 7pt !important;
        }
        .applicant-form-sheet .text-muted.text-uppercase {
            font-size: 5.8pt !important;
            margin-bottom: 0 !important;
        }
        .applicant-form-sheet .attestation-box,
        .applicant-form-sheet .bg-light {
            padding: 2.5px 5px !important;
            font-size: 6.2pt !important;
            line-height: 1.15 !important;
            margin-bottom: 3px !important;
        }
        .applicant-form-sheet .row.pt-2.pb-3 {
            padding-top: 2px !important;
            padding-bottom: 3px !important;
        }
        .applicant-form-sheet .border-warning {
            padding: 3px 5px !important;
            margin-bottom: 2px !important;
        }
        .applicant-form-sheet .badge {
            font-size: 5.8pt !important;
            padding: 1px 3px !important;
        }
        .applicant-form-sheet .border-top {
            margin-top: 2px !important;
            padding-top: 2px !important;
            font-size: 5.5pt !important;
        }
    </style>
</head>
<body>
    <div class="applicant-form-sheet">
        ${sheet.innerHTML}
    </div>
</body>
</html>`);
        doc.close();

        setTimeout(() => {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();
        }, 250);
    };

    // Print button handler
    document.addEventListener('DOMContentLoaded', function() {
        const printBtn = document.getElementById('applicantFormPrintBtn');
        if (printBtn) {
            printBtn.addEventListener('click', function() {
                window.printApplicantFormSheet();
            });
        }
    });
</script>
