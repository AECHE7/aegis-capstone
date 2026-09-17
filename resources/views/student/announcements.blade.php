@extends('layouts.app')

@section('title', 'Official Announcements | A.E.G.I.S.')
@section('page-title', 'Official Announcements')
@section('page-subtitle', 'Official memoranda, deadline schedules, and guidelines from the Office of Student Affairs')

@section('content')
<div class="container-fluid px-0" style="max-width: 960px; margin: 0 auto; padding: 0.5rem 0 calc(90px + env(safe-area-inset-bottom, 16px));">

    {{-- Header Banner --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #07331c 0%, #0C4E2D 100%); color: white; overflow: hidden;">
        <div class="p-3 p-md-4.5 position-relative">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold mb-2" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-bullhorn me-1"></i> Public Notice Board
                    </span>
                    <h3 class="fw-bold text-white mb-1" style="font-family: 'Poppins', sans-serif;">
                        CLSU OSA Bulletins & Memoranda
                    </h3>
                    <p class="text-white-50 mb-0 small">
                        Stay informed regarding active scholarship calls, document submission cutoffs, and university announcements.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-white bg-opacity-10 text-white px-3 py-2 rounded-pill border border-white border-opacity-10 fw-semibold" style="font-size: 0.8rem;">
                        <i class="fa-solid fa-bell text-warning me-1"></i> {{ $announcements->total() }} Published Notices
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Announcements Feed --}}
    <div class="d-flex flex-column gap-3">
        @forelse($announcements as $announcement)
            <div class="card border-0 shadow-sm p-3 p-md-4" style="border-radius: 16px; border-left: 4px solid var(--clsu-green) !important;">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                    <div>
                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill fw-semibold mb-1" style="font-size: 0.7rem;">
                            <i class="fa-solid fa-shield-halved me-1"></i> Official Notice
                        </span>
                        <h5 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif; font-size: 1.1rem;">
                            {{ $announcement->title }}
                        </h5>
                    </div>
                    <span class="text-muted small monospace-data flex-shrink-0" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-clock me-1"></i> {{ $announcement->created_at->format('M d, Y h:i A') }}
                    </span>
                </div>

                {{-- Author & Meta Info --}}
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="eval-avatar" style="width: 26px; height: 26px; font-size: 0.72rem;">
                        {{ strtoupper(substr($announcement->author->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="small fw-semibold text-dark" style="font-size: 0.78rem;">
                        {{ $announcement->author->name ?? 'OSA Administrator' }}
                    </span>
                    <span class="badge bg-light text-muted border px-2 py-0.5 rounded-pill" style="font-size: 0.65rem; text-transform: uppercase;">
                        {{ $announcement->author->role ?? 'Staff' }}
                    </span>
                </div>

                {{-- Content Body --}}
                <div class="text-secondary" style="font-size: 0.9rem; line-height: 1.65; white-space: pre-line;">
                    {{ $announcement->content }}
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm p-4 p-md-5 text-center" style="border-radius: 18px;">
                <div class="p-3 rounded-circle bg-light d-inline-block mx-auto mb-3 text-muted" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-bullhorn fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark">No Announcements Yet</h5>
                <p class="text-muted small mb-0">Official updates and guidelines from the Office of Student Affairs will be displayed here.</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($announcements->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
