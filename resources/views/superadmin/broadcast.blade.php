@extends('layouts.app')

@section('title', 'Email Broadcast Center')
@section('page-title', 'Email Broadcast Center')
@section('page-subtitle', 'Direct email broadcasts to students, scholars, or specific scholarship programs')

@section('content')
<div class="row g-4">
    <!-- Left Column: Compose Broadcast -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="p-3 bg-light rounded-4 text-info">
                        <i class="fa-solid fa-paper-plane fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Compose Email</h5>
                        <p class="text-muted small mb-0">Broadcast alerts or updates instantly</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 small mb-4 py-2" style="background-color: #dcfce7; color: #14532d; border-radius: 10px;">
                        <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('superadmin.broadcast.send') }}" method="POST" id="broadcastForm">
                    @csrf
                    <div class="mb-3">
                        <label for="targetSelect" class="form-label fw-semibold text-dark small">Target Audience</label>
                        <select name="target" id="targetSelect" class="form-select rounded-3" required>
                            <option value="" disabled selected>Select target group...</option>
                            <option value="all_students">All Students (Applicants & Scholars)</option>
                            <option value="approved_scholars">Approved Scholars (Active only)</option>
                            <optgroup label="Scholarship Programs">
                                @foreach($scholarships as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} Applicants</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="broadcastTitle" class="form-label fw-semibold text-dark small">Email Subject</label>
                        <input type="text" name="title" id="broadcastTitle" class="form-control rounded-3" 
                               placeholder="e.g. Mandatory Assembly for GAD Scholars" required autocomplete="off">
                    </div>

                    <div class="mb-4">
                        <label for="broadcastBody" class="form-label fw-semibold text-dark small">Message Body</label>
                        <textarea name="body" id="broadcastBody" class="form-control rounded-3" rows="8" 
                                  placeholder="Write your email announcement details here..." required style="resize: none;"></textarea>
                    </div>

                    <button type="submit" class="btn text-white w-100 py-2.5 fw-semibold rounded-pill" id="sendBtn"
                            style="background-color: #0C4E2D; box-shadow: 0 4px 6px rgba(12, 78, 45, 0.15);">
                        <span id="btnText"><i class="fa-solid fa-paper-plane me-1"></i> Send Broadcast</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: History -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">Broadcast History</h5>
                <p class="text-muted small mb-0">Recently sent bulk system communications</p>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-4" style="font-size: 0.8rem; text-transform: uppercase;">Recipient</th>
                                <th style="font-size: 0.8rem; text-transform: uppercase;">Subject</th>
                                <th style="font-size: 0.8rem; text-transform: uppercase;">Sent At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($broadcasts as $b)
                                <tr class="border-bottom">
                                    <td class="ps-4">
                                        <span class="fw-semibold text-dark small">{{ $b->recipient }}</span>
                                    </td>
                                    <td>
                                        <span class="text-dark small fw-semibold">{{ str_replace('[A.E.G.I.S. Broadcast] ', '', $b->subject) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $b->created_at->format('M d, Y h:i A') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-envelope-open fa-2x mb-2 opacity-30"></i>
                                        <p class="mb-0 small">No broadcast emails found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($broadcasts->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $broadcasts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('broadcastForm').addEventListener('submit', function() {
        document.getElementById('btnText').style.display = 'none';
        document.getElementById('btnSpinner').classList.remove('d-none');
        document.getElementById('sendBtn').disabled = true;
    });
</script>
@endpush
@endsection
