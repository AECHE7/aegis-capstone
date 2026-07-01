<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AuthController;

// ==========================================
// 🔓 PUBLIC ROUTES (The Front Door)
// ==========================================
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']); 
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
Route::get('/health', [\App\Http\Controllers\HealthController::class, 'check'])->name('health');

Route::middleware('guest')->group(function () {
    Route::get('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/activate-account', [\App\Http\Controllers\Auth\StaffActivationController::class, 'showActivationForm'])->name('activate.form');
    Route::post('/activate-account', [\App\Http\Controllers\Auth\StaffActivationController::class, 'activate'])->name('activate.submit');
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->middleware('throttle:3,1')->name('password.email');
});

// Reset password routes available to both guest and authenticated users
Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('password.store');




// ==========================================
// 🔒 SECURED ROUTES (Must be Logged In!)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Logout is secured
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Notifications routes
    Route::get('/notifications', [AuthController::class, 'getNotifications'])->name('notifications.index');
    Route::get('/notifications/stream', [AuthController::class, 'streamNotifications'])->name('notifications.stream');
    Route::post('/notifications/{id}/read', [AuthController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/clear', [AuthController::class, 'clearNotifications'])->name('notifications.clear');

    // Email Verification Routes
    Route::get('/email/verify', [\App\Http\Controllers\Auth\EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])->middleware(['throttle:6,1'])->name('verification.send');
    Route::get('/email/verification-status', function () {
        return response()->json([
            'verified' => auth()->user()->hasVerifiedEmail(),
        ]);
    })->name('verification.status');

    // STUDENT PORTAL (Requires Email Verification)
    Route::middleware(['verified'])->group(function () {
        Route::get('/apply', [ApplicationController::class, 'create'])->name('student.apply');
        Route::post('/apply', [ApplicationController::class, 'store'])->name('student.store');
        Route::get('/student/dashboard', [ApplicationController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/student/profile', [ApplicationController::class, 'editProfile'])->name('student.profile');
        Route::post('/student/profile', [ApplicationController::class, 'updateProfile'])->name('student.profile.update');
        Route::get('/scholarships/{id}/fields', [ApplicationController::class, 'getScholarshipFields'])->name('scholarships.fields');
    });

    // OSA ADMIN DASHBOARD
    Route::prefix('admin')->middleware(['role:admin,superadmin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/review/{id}', [AdminController::class, 'review'])->name('admin.review');
        Route::post('/review/{id}/scan', [AdminController::class, 'runScan'])->name('admin.scan');
        Route::post('/review/{id}/status', [AdminController::class, 'updateStatus'])->name('admin.updateStatus');
        Route::post('/review/{id}/archive', [AdminController::class, 'archive'])->name('admin.archive');
        Route::post('/review/{id}/unarchive', [AdminController::class, 'unarchive'])->name('admin.unarchive');
        Route::get('/document/{id}/download', [AdminController::class, 'downloadDocument'])->name('admin.document.download');
        Route::get('/export-csv', [\App\Http\Controllers\ReportController::class, 'exportCsv'])->name('admin.export');
        Route::get('/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('admin.exportPdf');
    });

    // SUPER ADMIN (Scholarship Management)
    Route::prefix('superadmin')->middleware(['role:superadmin'])->group(function () {
        Route::get('/scholarships', [SuperAdminController::class, 'index'])->name('superadmin.scholarships');
        Route::post('/scholarships', [SuperAdminController::class, 'store'])->name('superadmin.scholarships.store');
        Route::post('/scholarships/{id}/toggle', [SuperAdminController::class, 'toggleStatus'])->name('superadmin.scholarships.toggle');
        Route::get('/analytics', [SuperAdminController::class, 'analytics'])->name('superadmin.analytics');
        Route::get('/staff', [SuperAdminController::class, 'listStaff'])->name('superadmin.staff');
        Route::post('/staff/invite', [SuperAdminController::class, 'inviteStaff'])->name('superadmin.staff.invite');
        Route::post('/staff/{id}/revoke', [SuperAdminController::class, 'revokeStaff'])->name('superadmin.staff.revoke');
        Route::post('/staff/{id}/reactivate', [SuperAdminController::class, 'reactivateStaff'])->name('superadmin.staff.reactivate');
        Route::post('/staff/{id}/assign', [SuperAdminController::class, 'updateStaffAssignments'])->name('superadmin.staff.assign');

        // Phase 33: Audit History Log Exports (Skill 7 – API Gateway: superadmin-only gate)
        Route::get('/audit-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportAuditCsv'])->name('superadmin.audit.csv');
        Route::get('/audit-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportAuditPdf'])->name('superadmin.audit.pdf');
        Route::get('/email-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportEmailLogCsv'])->name('superadmin.emaillog.csv');
        Route::get('/email-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportEmailLogPdf'])->name('superadmin.emaillog.pdf');
    });

    // SECURE FILE VIEWING
    Route::get('/document/{id}/image', function ($id) {
        $document = \App\Models\Document::with('application')->findOrFail($id);
        if (auth()->user()->role === 'student' && ($document->application->user_id ?? null) !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        if (auth()->user()->role === 'admin') {
            $assignedIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            if (!in_array($document->application->scholarship_id, $assignedIds)) {
                abort(403, 'Unauthorized access.');
            }
        }
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($document->file_path)) { abort(404); }
        return response()->file(\Illuminate\Support\Facades\Storage::disk('local')->path($document->file_path));
    })->name('document.view');

    Route::get('/document/{id}/heatmap', function ($id) {
        $aiResult = \App\Models\AIResult::with('document.application')->where('document_id', $id)->firstOrFail();
        if (auth()->user()->role === 'student' && ($aiResult->document->application->user_id ?? null) !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        if (auth()->user()->role === 'admin') {
            $assignedIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            if (!in_array($aiResult->document->application->scholarship_id, $assignedIds)) {
                abort(403, 'Unauthorized access.');
            }
        }
        $path = $aiResult->heatmap_path;
        // If Cloudinary (or any full URL) — redirect straight to CDN
        if (str_starts_with($path, 'http')) {
            return redirect($path);
        }
        // Fallback: proxy via the AI microservice /heatmap/ endpoint
        $aiUrl = rtrim(config('services.ai.url'), '/');
        return redirect($aiUrl . '/heatmap/' . basename($path));
    })->name('document.heatmap');

    Route::get('/application-field/{id}/file', function ($id) {
        $field = \App\Models\ApplicationField::with('application')->findOrFail($id);
        if (auth()->user()->role === 'student' && ($field->application->user_id ?? null) !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        if (auth()->user()->role === 'admin') {
            $assignedIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            if (!in_array($field->application->scholarship_id, $assignedIds)) {
                abort(403, 'Unauthorized access.');
            }
        }
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($field->field_value)) { abort(404); }
        return response()->file(\Illuminate\Support\Facades\Storage::disk('local')->path($field->field_value));
    })->name('application-field.file');

    // UAT FEEDBACK SUBMISSION
    Route::post('/uat-feedback', [\App\Http\Controllers\UatFeedbackController::class, 'store'])->name('uat.store');

});