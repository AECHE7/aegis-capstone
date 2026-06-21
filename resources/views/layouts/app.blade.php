<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'A.E.G.I.S. Portal')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --clsu-green: #0F5934;
            --clsu-green-dark: #0a4025;
            --clsu-gold: #F2A900;
            --clsu-dark: #0f172a; /* Executive Dark Mode */
            --clsu-bg: #f8fafc;   /* Standard Surface Background */
        }

        body {
            background-color: var(--clsu-bg);
            font-family: 'Inter', sans-serif;
            color: #334155;
        }

        /* Headings use Poppins for a modern, geometric look */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            color: #0f172a;
        }

        /* The Master Navbar */
        .navbar-custom {
            background-color: var(--clsu-dark);
            border-bottom: 4px solid var(--clsu-gold);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Master Card Rules: Soft borders, floating shadows */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: all 0.2s ease-in-out;
        }

        /* Master Input Rules: Clean focus rings */
        .form-control:focus, .form-select:focus {
            border-color: var(--clsu-green);
            box-shadow: 0 0 0 0.25rem rgba(15, 89, 52, 0.25);
        }
    </style>
    
    @stack('styles')
</head>
<body>

    @auth
    <nav class="navbar navbar-expand-lg navbar-custom py-3 mb-4 sticky-top">
        <div class="container-fluid px-md-5">
            <a class="navbar-brand d-flex align-items-center text-white" href="#">
                <i class="fa-solid fa-shield-halved text-warning me-2 fs-4"></i>
                <span class="fw-bold tracking-wide">A.E.G.I.S. Portal</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-toggle="target="#mainNav">
                <i class="fa-solid fa-bars text-white"></i>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="mainNav">
                <ul class="navbar-nav align-items-center gap-3">
                    
                    @if(auth()->user()->role === 'superadmin')
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('superadmin.analytics') }}"><i class="fa-solid fa-chart-line me-1"></i> Analytics</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('superadmin.scholarships') }}"><i class="fa-solid fa-list-check me-1"></i> Programs</a></li>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2 ms-2"><i class="fa-solid fa-crown me-1"></i> Director</span>
                    
                    @elseif(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-layer-group me-1"></i> Queue</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.export') }}"><i class="fa-solid fa-file-csv me-1"></i> Export Report</a></li>
                        <span class="badge bg-success rounded-pill px-3 py-2 ms-2"><i class="fa-solid fa-user-shield me-1"></i> OSA Admin</span>
                    
                    @else
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('student.dashboard') }}"><i class="fa-solid fa-house me-1"></i> Tracker</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('student.apply') }}"><i class="fa-solid fa-plus me-1"></i> New Application</a></li>
                        <span class="badge bg-primary rounded-pill px-3 py-2 ms-2"><i class="fa-solid fa-user-graduate me-1"></i> Applicant</span>
                    @endif

                    <li class="nav-item ms-md-3 mt-2 mt-md-0">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm fw-bold rounded-pill px-3 shadow-sm">
                                <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>