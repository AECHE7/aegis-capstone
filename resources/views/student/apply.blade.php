<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Application | A.E.G.I.S.</title>
    <!-- Custom Favicon -->
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert for Error Popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --clsu-green: #0F5934; --clsu-gold: #F2A900; }
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .navbar-custom { background-color: var(--clsu-green); }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .form-control, .form-select { padding: 12px 15px; border-radius: 8px; border: 1px solid #cbd5e1; }
        .form-control:focus, .form-select:focus { border-color: var(--clsu-green); box-shadow: 0 0 0 0.25rem rgba(15, 89, 52, 0.25); }
        .btn-submit { background-color: var(--clsu-green); color: white; padding: 12px; font-weight: 600; border-radius: 8px; transition: all 0.3s; }
        .btn-submit:hover { background-color: #0b4026; color: var(--clsu-gold); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(15, 89, 52, 0.2); }
        
        /* Image Preview Box */
        .preview-container { width: 100%; height: 350px; border: 2px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; background-color: #f8fafc; overflow: hidden; position: relative; }
        .preview-container img { max-width: 100%; max-height: 100%; object-fit: contain; display: none; }
        .preview-placeholder { text-align: center; color: #94a3b8; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom py-3 mb-5">
    <div class="container">
        <div class="d-flex align-items-center">
            <!-- Custom Logo -->
            <img src="{{ asset('logo.png') }}" alt="A.E.G.I.S. Logo" height="35" class="me-2" style="filter: brightness(0) invert(1);">
            <h5 class="mb-0 fw-bold text-white">A.E.G.I.S. Portal</h5>
        </div>
        <div class="text-white">
            <a href="{{ route('student.dashboard') }}" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard</a>
        </div>
    </div>
</nav>

<div class="container mb-5" style="max-width: 900px;">
    
    <div class="text-center mb-4">
        <h3 class="fw-bold text-dark mb-2">Submit New Application</h3>
        <p class="text-muted">Fill out the required details and upload a clear copy of your Certificate of Grades.</p>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="row g-0">
            
            <!-- Form Section -->
            <div class="col-md-6 p-5">
                <form action="{{ route('student.store') }}" method="POST" enctype="multipart/form-data" id="applicationForm">
                    @csrf
                    
                    <!-- DYNAMIC SCHOLARSHIP DROPDOWN -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold"><i class="fa-solid fa-graduation-cap me-1"></i> Select Scholarship Program</label>
                        <select name="scholarship_id" class="form-select bg-light border-0 py-2" required onchange="document.getElementById('programNameInput').value = this.options[this.selectedIndex].text.split(' (')[0].trim();">
                            <option value="" selected disabled>-- Select Active Program --</option>
                            @foreach($scholarships as $scholarship)
                                <option value="{{ $scholarship->id }}">
                                    {{ $scholarship->name }} (Min GWA: {{ $scholarship->min_gwa_required }})
                                </option>
                            @endforeach
                        </select>
                        <!-- Hidden input safely passes the name -->
                        <input type="hidden" name="program_name" id="programNameInput" required>
                    </div>

                    <!-- GWA INPUT -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold"><i class="fa-solid fa-star me-1"></i> Declared GWA</label>
                        <input type="number" step="0.01" min="1.00" max="5.00" name="gwa" class="form-control bg-light border-0" placeholder="e.g. 1.25" required>
                    </div>

                    <!-- FILE UPLOAD -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold"><i class="fa-solid fa-file-arrow-up me-1"></i> Upload Certificate of Grades</label>
                        <input class="form-control" type="file" name="document" id="documentUpload" accept="image/jpeg, image/png" required onchange="previewImage(event)">
                        <div class="form-text small mt-2"><i class="fa-solid fa-circle-info text-primary me-1"></i> Accepted formats: JPEG, PNG.</div>
                    </div>

                    <hr class="my-4">

                    <button type="submit" class="btn btn-submit w-100" id="submitBtn" onclick="showLoading()">
                        <i class="fa-solid fa-paper-plane me-2"></i> Submit Application to OSA
                    </button>
                </form>
            </div>

            <!-- Image Preview Section -->
            <div class="col-md-6 bg-light p-5 d-flex flex-column justify-content-center border-start">
                <h6 class="fw-bold mb-3 text-muted"><i class="fa-regular fa-image me-2"></i> Document Preview</h6>
                <div class="preview-container shadow-sm bg-white">
                    <div class="preview-placeholder" id="placeholderText">
                        <i class="fa-solid fa-cloud-arrow-up fs-1 mb-2 text-secondary"></i>
                        <p class="mb-0 small fw-medium">Image preview will appear here</p>
                    </div>
                    <img id="imagePreview" src="#" alt="Document Preview">
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('imagePreview');
            var placeholder = document.getElementById('placeholderText');
            output.src = reader.result;
            output.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function showLoading() {
        const form = document.getElementById('applicationForm');
        const btn = document.getElementById('submitBtn');
        const doc = document.getElementById('documentUpload');
        
        if(form.checkValidity() && doc.files.length > 0) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Encrypting & Submitting...';
            btn.classList.add('disabled');
        }
    }
</script>

<!-- If Laravel validation fails, show this popup! -->
@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Submission Failed',
        html: '{!! implode("<br>", $errors->all()) !!}',
        confirmButtonColor: '#0F5934'
    });
    // Remove loading spinner
    document.getElementById('submitBtn').innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i> Submit Application to OSA';
    document.getElementById('submitBtn').classList.remove('disabled');
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>