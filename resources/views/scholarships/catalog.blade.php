@if(auth()->check())
    @extends('layouts.app')
    @section('title', 'Available Scholarships | A.E.G.I.S.')
    @section('page-title', 'Available Scholarships')
    @section('page-subtitle', 'Explore active scholarship programs, eligibility criteria, and application guidelines')
@else
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Available Scholarships | {{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }}</title>
        <link rel="icon" type="image/webp" href="{{ asset('logo.webp') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root {
                --clsu-green: #0C4E2D;
                --clsu-gold: #D97706;
                --clsu-bg: #f8fafc;
            }
            body { font-family: 'Inter', sans-serif; background: var(--clsu-bg); color: #334155; }
            .public-header { background: #07331c; border-bottom: 3px solid var(--clsu-gold); padding: 1rem 0; }
        </style>
    </head>
    <body>
        <header class="public-header sticky-top">
            <div class="container d-flex justify-content-between align-items-center">
                <a href="/" class="d-flex align-items-center gap-2 text-decoration-none text-white">
                    <img src="{{ asset('logo.webp') }}" alt="A.E.G.I.S." width="36" height="36">
                    <span class="fw-bold fs-5">A.E.G.I.S. Portal</span>
                </a>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm px-3 rounded-pill">Sign In</a>
                    <a href="{{ route('register') }}" class="btn btn-warning btn-sm px-3 rounded-pill fw-bold text-dark">Register</a>
                </div>
            </div>
        </header>
@endif

@if(auth()->check())
    @section('content')
@else
    <main class="py-4">
        <div class="container">
@endif

{{-- Header Banner & Search Filter --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #07331c 0%, #0C4E2D 100%); color: white; overflow: hidden; position: relative;">
    <div class="p-4 p-md-5 position-relative" style="z-index: 2;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold mb-2" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-graduation-cap me-1"></i> CLSU Office of Student Affairs
                </span>
                <h2 class="fw-bold text-white mb-1" style="font-family: 'Poppins', sans-serif; letter-spacing: -0.5px;">
                    Available Scholarship Programs
                </h2>
                <p class="text-white-50 mb-0 small" style="max-width: 600px;">
                    Review complete requirements, maximum GWA thresholds, renewal limits, and program descriptions for the current academic term.
                </p>
            </div>
            @if(isset($activeTerm) && $activeTerm)
                <div class="text-md-end bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-10">
                    <span class="text-white-50 d-block small" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;">Active Academic Term</span>
                    <strong class="text-warning fs-6">{{ $activeTerm->semester }} Semester, A.Y. {{ $activeTerm->academic_year }}</strong>
                </div>
            @endif
        </div>

        {{-- Live Search Filter --}}
        <form method="GET" action="{{ route('scholarships.catalog') }}" class="mt-4">
            <div class="row g-2 align-items-center">
                <div class="col-md-8 col-lg-9">
                    <div class="input-group bg-white rounded-pill p-1 shadow-sm">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 bg-transparent py-2" 
                               placeholder="Search scholarship name, eligibility terms, or guidelines..." 
                               value="{{ request('search') }}" style="box-shadow: none;">
                        @if(request('search'))
                            <a href="{{ route('scholarships.catalog') }}" class="btn btn-link text-muted text-decoration-none">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <button type="submit" class="btn btn-warning w-100 fw-bold rounded-pill py-2.5 text-dark">
                        <i class="fa-solid fa-filter me-1"></i> Filter Programs
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Programs Summary Badge Bar --}}
<div class="d-flex justify-content-between align-items-center mb-3 px-1">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold" style="font-size: 0.8rem;">
            <i class="fa-solid fa-award text-success me-1"></i> {{ $scholarships->count() }} Active Scholarships
        </span>
        @if(request('search'))
            <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill small">
                Results for: "{{ request('search') }}"
            </span>
        @endif
    </div>
</div>

{{-- Scholarship Cards Grid --}}
<div class="row g-4">
    @forelse($scholarships as $scholarship)
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm d-flex flex-column justify-content-between p-4" 
             style="border-radius: 18px; border-top: 4px solid #0C4E2D !important; transition: transform 0.2s, box-shadow 0.2s;">
            <div>
                {{-- Card Header: Title & Badges --}}
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div>
                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill fw-bold mb-1" style="font-size: 0.68rem;">
                            <i class="fa-solid fa-circle-check me-1"></i> Active Grant
                        </span>
                        <h5 class="fw-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif; font-size: 1.15rem; line-height: 1.35;">
                            {{ $scholarship->name }}
                        </h5>
                    </div>
                    @if($scholarship->min_gwa_required)
                        <div class="text-end flex-shrink-0">
                            <span class="badge bg-warning-subtle text-dark border border-warning border-opacity-50 px-2.5 py-1.5 rounded-pill fw-bold" style="font-size: 0.75rem;">
                                Max GWA: {{ number_format($scholarship->min_gwa_required, 2) }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Full Description --}}
                <div class="mt-3 text-muted" style="font-size: 0.88rem; line-height: 1.6; white-space: pre-line;">
                    {{ $scholarship->description ?: 'No detailed program description provided. Please consult the CLSU Office of Student Affairs for guidelines.' }}
                </div>

                {{-- Key Criteria & Parameters --}}
                <div class="row g-2 mt-3 pt-3 border-top">
                    <div class="col-sm-6">
                        <div class="p-2.5 bg-light rounded-3 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-rotate-right text-success"></i>
                            <div style="font-size: 0.78rem;">
                                <span class="text-muted d-block">Max Renewals</span>
                                <strong class="text-dark">{{ $scholarship->max_renewals ?? 4 }} Academic Terms</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-2.5 bg-light rounded-3 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-calendar-check text-primary"></i>
                            <div style="font-size: 0.78rem;">
                                <span class="text-muted d-block">Application Status</span>
                                <strong class="text-success">Accepting Applications</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Required Supporting Documents & Custom Fields --}}
                @if($scholarship->fields && $scholarship->fields->count() > 0)
                    <div class="mt-3">
                        <span class="text-muted d-block small mb-1.5 fw-semibold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-file-circle-check text-warning me-1"></i> Program Required Attachments & Inputs:
                        </span>
                        <div class="d-flex flex-wrap gap-1.5">
                            @foreach($scholarship->fields as $field)
                                <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill" style="font-size: 0.72rem; font-weight: 500;">
                                    @if($field->field_type === 'file')
                                        <i class="fa-solid fa-paperclip text-muted me-1"></i>
                                    @else
                                        <i class="fa-solid fa-pen text-muted me-1"></i>
                                    @endif
                                    {{ $field->field_label }}
                                    @if($field->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Card Footer Action --}}
            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted small" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-shield-halved text-success me-1"></i> Verified by CLSU OSA
                </span>
                @if(auth()->check())
                    @if(auth()->user()->role === 'student')
                        <a href="{{ route('student.apply', ['program' => $scholarship->id]) }}" 
                           class="btn btn-success fw-bold px-4 py-2 rounded-pill" 
                           style="font-size: 0.85rem; background: var(--clsu-green);">
                            Apply Now <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill small">
                            Evaluation View
                        </span>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-success fw-bold px-4 py-2 rounded-pill" style="font-size: 0.85rem;">
                        Sign in to Apply <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 20px;">
            <div class="p-3 rounded-circle bg-light d-inline-block mx-auto mb-3 text-muted" style="width: 60px; height: 60px;">
                <i class="fa-solid fa-inbox fs-3"></i>
            </div>
            <h5 class="fw-bold text-dark">No Scholarships Found</h5>
            <p class="text-muted small mb-3">No scholarship programs matched your search query.</p>
            <div>
                <a href="{{ route('scholarships.catalog') }}" class="btn btn-outline-success rounded-pill px-4">
                    View All Active Programs
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if(auth()->check())
    @endsection
@else
        </div>
    </main>
    <footer class="py-4 text-center text-muted small border-top bg-white mt-5">
        <div class="container">
            &copy; {{ date('Y') }} Central Luzon State University — Office of Student Affairs. Academic Evaluation & Grant Integrity System.
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
@endif
