<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\HealthController;

// ==========================================
// 🔓 PUBLIC ROUTES (The Front Door)
// ==========================================
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']); 
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
Route::get('/login/mfa', [AuthController::class, 'showMfa'])->name('login.mfa');
Route::post('/login/mfa', [AuthController::class, 'verifyMfa'])->middleware('throttle:5,1')->name('login.mfa.verify');
Route::post('/login/mfa/resend', [AuthController::class, 'resendMfa'])->middleware('throttle:3,1')->name('login.mfa.resend');
Route::get('/locale/{lang}', function (string $lang) {
    if (in_array($lang, ['en', 'ph'], true)) {
        session()->put('locale', $lang);
    }
    return redirect()->back();
})->name('locale.set');

Route::get('/health', [\App\Http\Controllers\HealthController::class, 'check'])->name('health');
Route::get('/scheduler/run', function (\Illuminate\Http\Request $request) {
    $expectedKey = config('services.scheduler.key', 'aegis_cron_secret');
    // hash_equals prevents timing-based attacks on the secret key (MED-06)
    if (!hash_equals((string) $expectedKey, (string) $request->query('key', ''))) {
        abort(403, 'Unauthorized');
    }
    \Illuminate\Support\Facades\Artisan::call('scholarships:close-expired');
    $output = \Illuminate\Support\Facades\Artisan::output();
    return response()->json([
        'success' => true,
        'message' => 'Scheduler run complete.',
        'output' => trim($output)
    ]);
});

Route::get('/favicon.ico', function () {
    $path = public_path('logo.png');
    if (file_exists($path)) {
        return response()->file($path, ['Content-Type' => 'image/png']);
    }
    return response('', 204);
});

Route::get('/robots.txt', function () {
    $content = "User-agent: *\n";
    $content .= "Allow: /\n\n";
    $content .= "# Block private/admin areas from search indexing\n";
    $content .= "Disallow: /admin\n";
    $content .= "Disallow: /superadmin\n";
    $content .= "Disallow: /student/apply\n";
    $content .= "Disallow: /student/dashboard\n";
    $content .= "Disallow: /login\n";
    $content .= "Disallow: /register\n";
    $content .= "Disallow: /forgot-password\n";
    $content .= "Disallow: /storage/\n";
    $content .= "Disallow: /uploads/\n\n";
    $content .= "# Sitemap location\n";
    $content .= "Sitemap: " . url('/sitemap.xml') . "\n";
    
    return response($content, 200)->header('Content-Type', 'text/plain');
});

Route::get('/system/logo', function () {
    $logoPath = \App\Models\Setting::get('app_logo');
    if (empty($logoPath)) {
        abort(404);
    }
    if (str_starts_with($logoPath, 'http')) {
        return redirect($logoPath);
    }
    if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($logoPath)) {
        abort(404);
    }
    return response()->file(\Illuminate\Support\Facades\Storage::disk('local')->path($logoPath));
})->name('system.logo');


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

// Master Account Transfer Accept Route (Available to guest users to handle initial click redirects)
Route::get('/master/accept-transfer/{token}', [\App\Http\Controllers\MasterController::class, 'acceptTransfer'])->name('master.accept-transfer');
Route::post('/master/accept-transfer/{token}', [\App\Http\Controllers\MasterController::class, 'acceptTransfer']);




// ==========================================
// 🔒 SECURED ROUTES (Must be Logged In!)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Logout is secured
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Security & Password Change
    Route::get('/profile/security', [AuthController::class, 'showSecurity'])->name('profile.security');
    Route::post('/profile/security', [AuthController::class, 'updatePassword'])->name('profile.security.update');
    Route::delete('/profile/security/devices/{id}', [AuthController::class, 'revokeDevice'])->name('profile.security.devices.revoke');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Notifications routes
    Route::get('/notifications', [AuthController::class, 'getNotifications'])->name('notifications.index');
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
        
        // Deletion & Cancellation Workflows (Soft & Hard deletes for Student)
        Route::post('/application/{id}/cancel', [ApplicationController::class, 'cancel'])->name('student.application.cancel');
        Route::post('/application/{id}/withdraw', [ApplicationController::class, 'withdraw'])->name('student.application.withdraw');
        Route::post('/application/{id}/restore', [ApplicationController::class, 'restore'])->name('student.application.restore');

        // Onboarding Tour & Forfeiture
        Route::post('/student/complete-tour', [ApplicationController::class, 'completeTour'])->name('student.complete-tour');
        Route::post('/application/{id}/forfeit', [ApplicationController::class, 'forfeit'])->name('student.application.forfeit');
        Route::post('/application/{id}/reupload', [ApplicationController::class, 'reupload'])->name('student.application.reupload');
    });

    // OSA ADMIN DASHBOARD
    Route::prefix('admin')->middleware(['role:admin,superadmin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/review/{id}', [AdminController::class, 'review'])->name('admin.review');
        Route::get('/review/{id}/scan-status', [AdminController::class, 'scanStatus'])->name('admin.scanStatus');
        Route::post('/review/{id}/scan', [AdminController::class, 'runScan'])->name('admin.scan');
        Route::post('/review/{id}/scan-sync', [AdminController::class, 'runScanSync'])->name('admin.scanSync');
        Route::post('/review/{id}/status', [AdminController::class, 'updateStatus'])->name('admin.updateStatus');
        Route::post('/review/{id}/archive', [AdminController::class, 'archive'])->name('admin.archive');
        Route::post('/review/{id}/unarchive', [AdminController::class, 'unarchive'])->name('admin.unarchive');
        Route::get('/document/{id}/download', [AdminController::class, 'downloadDocument'])->name('admin.document.download');
        Route::get('/export-csv', [\App\Http\Controllers\ReportController::class, 'exportCsv'])->name('admin.export');
        Route::get('/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('admin.exportPdf');
        
        // Restore soft-deleted application (Admin Action)
        Route::post('/review/{id}/restore', [AdminController::class, 'restoreApplication'])->name('admin.restore');
        Route::post('/applications/bulk-action', [AdminController::class, 'bulkAction'])->name('admin.applications.bulk-action');
        // Staff Private Notes
        Route::match(['post', 'patch'], '/applications/{id}/notes', [AdminController::class, 'saveNotes'])->name('admin.saveNotes');
        Route::match(['post', 'patch'], '/applications/{id}/save-notes', [AdminController::class, 'saveNotes'])->name('admin.applications.save-notes');
        
        // Announcement Board Management
        Route::get('/announcements', [\App\Http\Controllers\AnnouncementController::class, 'index'])->name('admin.announcements.index');
        Route::post('/announcements', [\App\Http\Controllers\AnnouncementController::class, 'store'])->name('admin.announcements.store');
        Route::patch('/announcements/{id}', [\App\Http\Controllers\AnnouncementController::class, 'update'])->name('admin.announcements.update');
        Route::delete('/announcements/{id}', [\App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');
    });

    // SUPER ADMIN (Scholarship Management)
    Route::prefix('superadmin')->middleware(['role:superadmin'])->group(function () {
        Route::get('/scholarships', [SuperAdminController::class, 'index'])->name('superadmin.scholarships');
        Route::post('/scholarships', [SuperAdminController::class, 'store'])->name('superadmin.scholarships.store');
        Route::post('/scholarships/{id}/toggle', [SuperAdminController::class, 'toggleStatus'])->name('superadmin.scholarships.toggle');
        Route::get('/scholarships/{id}', [SuperAdminController::class, 'show'])->name('superadmin.scholarships.show');
        Route::put('/scholarships/{id}', [SuperAdminController::class, 'update'])->name('superadmin.scholarships.update');
        Route::get('/analytics', [SuperAdminController::class, 'analytics'])->name('superadmin.analytics');
        Route::post('/analytics/seed-mock', [SuperAdminController::class, 'seedMockData'])->name('superadmin.analytics.seed-mock');
        Route::get('/staff', [SuperAdminController::class, 'listStaff'])->name('superadmin.staff');
        Route::post('/staff/invite', [SuperAdminController::class, 'inviteStaff'])->name('superadmin.staff.invite');
        Route::post('/staff/{id}/revoke', [SuperAdminController::class, 'revokeStaff'])->name('superadmin.staff.revoke');
        Route::post('/staff/{id}/reactivate', [SuperAdminController::class, 'reactivateStaff'])->name('superadmin.staff.reactivate');
        Route::post('/staff/{id}/assign', [SuperAdminController::class, 'updateStaffAssignments'])->name('superadmin.staff.assign');

        // System Trash Dashboard
        Route::get('/trash', [SuperAdminController::class, 'trashIndex'])->name('superadmin.trash');
        
        // Trashed Applications actions (SuperAdmin / Director)
        Route::post('/applications/{id}/restore', [SuperAdminController::class, 'restoreApplication'])->name('superadmin.applications.restore');
        Route::delete('/applications/{id}/force-delete', [SuperAdminController::class, 'forceDeleteApplication'])->name('superadmin.applications.force-delete');

        // Trashed Scholarships actions (SuperAdmin / Director)
        Route::delete('/scholarships/{id}/delete', [SuperAdminController::class, 'deleteScholarship'])->name('superadmin.scholarships.delete');
        Route::post('/scholarships/{id}/restore', [SuperAdminController::class, 'restoreScholarship'])->name('superadmin.scholarships.restore');
        Route::delete('/scholarships/{id}/force-delete', [SuperAdminController::class, 'forceDeleteScholarship'])->name('superadmin.scholarships.force-delete');

        // Trashed Staff actions (SuperAdmin / Director)
        Route::delete('/staff/{id}/delete', [SuperAdminController::class, 'deleteStaff'])->name('superadmin.staff.delete');
        Route::post('/staff/{id}/restore', [SuperAdminController::class, 'restoreStaff'])->name('superadmin.staff.restore');
        Route::delete('/staff/{id}/force-delete', [SuperAdminController::class, 'forceDeleteStaff'])->name('superadmin.staff.force-delete');

        // Phase 33 & 55: Audit History Log Exports
        Route::get('/audit-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportAuditCsv'])->name('superadmin.audit.csv');
        Route::get('/audit-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportAuditPdf'])->name('superadmin.audit.pdf');
        Route::get('/email-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportEmailLogCsv'])->name('superadmin.emaillog.csv');
        Route::get('/email-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportEmailLogPdf'])->name('superadmin.emaillog.pdf');

        Route::get('/ai-scan-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportAiScanCsv'])->name('superadmin.export.ai-scan.csv');
        Route::get('/ai-scan-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportAiScanPdf'])->name('superadmin.export.ai-scan.pdf');
        Route::get('/evaluation-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportEvaluationDecisionCsv'])->name('superadmin.export.evaluation.csv');
        Route::get('/evaluation-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportEvaluationDecisionPdf'])->name('superadmin.export.evaluation.pdf');
        Route::get('/auth-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportAuthLogCsv'])->name('superadmin.export.auth-log.csv');
        Route::get('/auth-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportAuthLogPdf'])->name('superadmin.export.auth-log.pdf');
        Route::get('/admin-action-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportAdminActionCsv'])->name('superadmin.export.admin-action.csv');
        Route::get('/admin-action-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportAdminActionPdf'])->name('superadmin.export.admin-action.pdf');
        Route::get('/config-change-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportConfigChangeCsv'])->name('superadmin.export.config-change.csv');
        Route::get('/config-change-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportConfigChangePdf'])->name('superadmin.export.config-change.pdf');
        Route::get('/scholarship-change-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportScholarshipChangeCsv'])->name('superadmin.export.scholarship-change.csv');
        Route::get('/scholarship-change-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportScholarshipChangePdf'])->name('superadmin.export.scholarship-change.pdf');
        Route::get('/export-access-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportExportAccessLogCsv'])->name('superadmin.export.export-access.csv');
        Route::get('/export-access-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportExportAccessLogPdf'])->name('superadmin.export.export-access.pdf');
        Route::get('/student-timeline-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportStudentTimelineCsv'])->name('superadmin.export.student-timeline.csv');
        Route::get('/student-timeline-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportStudentTimelinePdf'])->name('superadmin.export.student-timeline.pdf');
        Route::get('/doc-upload-logs/csv', [\App\Http\Controllers\ReportController::class, 'exportDocumentUploadCsv'])->name('superadmin.export.doc-upload.csv');
        Route::get('/doc-upload-logs/pdf', [\App\Http\Controllers\ReportController::class, 'exportDocumentUploadPdf'])->name('superadmin.export.doc-upload.pdf');

        // Dynamic System Settings Panel
        Route::get('/settings', [SuperAdminController::class, 'settings'])->name('superadmin.settings');
        Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('superadmin.settings.update');
        Route::post('/settings/security-reset', [SuperAdminController::class, 'revokeAllDevices'])->name('superadmin.settings.security-reset');

        // Email Broadcast Center
        Route::get('/broadcast', [SuperAdminController::class, 'showBroadcast'])->name('superadmin.broadcast');
        Route::post('/broadcast', [SuperAdminController::class, 'sendBroadcast'])->name('superadmin.broadcast.send');

        // Staging/UAT database hard reset (prohibited in production)
        // CRIT-02: Changed from GET to POST. Requires typed confirmation token.
        Route::post('/system/reset-uat-data', function (\Illuminate\Http\Request $request) {
            if (app()->isProduction()) {
                abort(403, 'Database resets are prohibited in production environments.');
            }

            // Server-side confirmation guard — prevents accidental/CSRF-triggered resets
            if ($request->input('confirm') !== 'CONFIRM_RESET') {
                return response()->json([
                    'success' => false,
                    'message' => 'Confirmation token required. Send confirm=CONFIRM_RESET in the request body.'
                ], 422);
            }

            \App\Models\AIResult::query()->delete();
            \App\Models\Document::query()->delete();
            \App\Models\StatusLog::query()->delete();
            \App\Models\EmailLog::query()->delete();
            \App\Models\ApplicationField::query()->delete();
            \App\Models\StudentProfile::query()->delete();
            \App\Models\Application::withTrashed()->forceDelete();
            \App\Models\User::withTrashed()->where('role', 'student')->forceDelete();

            \Illuminate\Support\Facades\Log::warning('UAT database reset triggered by superadmin: ' . auth()->id() . ' from IP: ' . $request->ip());

            return response()->json([
                'success' => true,
                'message' => 'Staging database reset successfully. All student accounts and applications have been permanently deleted.'
            ]);
        })->name('system.reset-uat');
    });

    // SECURE FILE VIEWING — extracted from route closures to DocumentController (HIGH-01)
    Route::get('/document/{id}/image', [DocumentController::class, 'view'])->name('document.view');
    Route::get('/document/{id}/heatmap', [DocumentController::class, 'heatmap'])->name('document.heatmap');
    Route::get('/document/{id}/original-page', [DocumentController::class, 'originalPage'])->name('document.originalPage');
    Route::get('/document/{id}/forensic-layer/{layer}', [DocumentController::class, 'forensicLayer'])->name('document.forensicLayer');
    Route::get('/application-field/{id}/file', [DocumentController::class, 'fieldFile'])->name('application-field.file');

    // UAT FEEDBACK SUBMISSION
    Route::post('/uat-feedback', [\App\Http\Controllers\UatFeedbackController::class, 'store'])->name('uat.store');

    // MASTER ACCOUNT GATEWAY & ROLE SWITCHER
    Route::get('/master/gateway', [\App\Http\Controllers\MasterController::class, 'showGateway'])->name('master.gateway');
    Route::post('/master/switch-role', [\App\Http\Controllers\MasterController::class, 'switchRole'])->name('master.switch-role');
    Route::post('/master/transfer', [\App\Http\Controllers\MasterController::class, 'initiateTransfer'])->name('master.transfer');

});