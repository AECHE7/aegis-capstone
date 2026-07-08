@extends('layouts.app')

@section('title', 'Change Password')
@section('page-title', 'Security Settings')
@section('page-subtitle', 'Manage your account password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="p-3 bg-light rounded-4 text-success">
                        <i class="fa-solid fa-key fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Update Password</h4>
                        <p class="text-muted small mb-0">Ensure your account is using a secure password</p>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 small mb-4" style="background-color: #fee2e2; color: #7f1d1d; border-radius: 10px;">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.security.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold text-dark small">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control rounded-3" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold text-dark small">New Password</label>
                        <input type="password" name="password" id="password" class="form-control rounded-3" required>
                        <small class="text-muted" style="font-size: 0.75rem;">Password must be at least 8 characters long.</small>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold text-dark small">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control rounded-3" required>
                    </div>

                    <button type="submit" class="btn text-white w-100 py-2.5 fw-semibold rounded-pill" 
                            style="background-color: #0C4E2D; box-shadow: 0 4px 6px rgba(12, 78, 45, 0.15);">
                        <i class="fa-solid fa-save me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
