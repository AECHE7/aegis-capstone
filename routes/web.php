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
Route::get('/login/mfa', [AuthController::class, 'showMfa'])->name('login.mfa');
Route::post('/login/mfa', [AuthController::class, 'verifyMfa'])->middleware('throttle:5,1')->name('login.mfa.verify');
Route::post('/login/mfa/resend', [AuthController::class, 'resendMfa'])->middleware('throttle:3,1')->name('login.mfa.resend');
Route::get('/health', [\App\Http\Controllers\HealthController::class, 'check'])->name('health');
Route::get('/scheduler/run', function (\Illuminate\Http\Request $request) {
    $expectedKey = env('SCHEDULER_KEY', 'aegis_cron_secret');
    if ($request->query('key') !== $expectedKey) {
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

Route::get('/system/reset-uat-data', function () {
    \App\Models\AIResult::query()->delete();
    \App\Models\Document::query()->delete();
    \App\Models\StatusLog::query()->delete();
    \App\Models\EmailLog::query()->delete();
    \App\Models\ApplicationField::query()->delete();
    \App\Models\StudentProfile::query()->delete();
    \App\Models\Application::withTrashed()->forceDelete();
    \App\Models\User::withTrashed()->where('role', 'student')->forceDelete();
    return "Staging database reset successfully! All student accounts and applications have been permanently deleted.";
})->name('system.reset-uat');

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

    // Security & Password Change
    Route::get('/profile/security', [AuthController::class, 'showSecurity'])->name('profile.security');
    Route::post('/profile/security', [AuthController::class, 'updatePassword'])->name('profile.security.update');
    Route::delete('/profile/security/devices/{id}', [AuthController::class, 'revokeDevice'])->name('profile.security.devices.revoke');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');

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
        
        // Deletion & Cancellation Workflows (Soft & Hard deletes for Student)
        Route::post('/application/{id}/cancel', [ApplicationController::class, 'cancel'])->name('student.application.cancel');
        Route::post('/application/{id}/withdraw', [ApplicationController::class, 'withdraw'])->name('student.application.withdraw');
        Route::post('/application/{id}/restore', [ApplicationController::class, 'restore'])->name('student.application.restore');

        // Onboarding Tour & Forfeiture
        Route::post('/student/complete-tour', [ApplicationController::class, 'completeTour'])->name('student.complete-tour');
        Route::post('/application/{id}/forfeit', [ApplicationController::class, 'forfeit'])->name('student.application.forfeit');
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
        
        // Restore soft-deleted application (Admin Action)
        Route::post('/review/{id}/restore', [AdminController::class, 'restoreApplication'])->name('admin.restore');
        Route::post('/applications/bulk-action', [AdminController::class, 'bulkAction'])->name('admin.applications.bulk-action');
        Route::patch('/applications/{id}/notes', [AdminController::class, 'saveNotes'])->name('admin.applications.save-notes');
        
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
        $path = $document->file_path;
        if (str_starts_with($path, 'http')) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(15)->get($path);
                if ($response->successful()) {
                    $mime = $response->header('Content-Type') ?: 'application/octet-stream';
                    return response($response->body(), 200, [
                        'Content-Type' => $mime,
                        'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to stream remote document: " . $e->getMessage());
            }
            return response(
                '<html><body style="font-family:sans-serif; display:flex; flex-direction:column; justify-content:center; align-items:center; height:90vh; color:#64748b; background:#f8fafc; text-align:center; padding:20px;">' .
                '<svg style="width:48px; height:48px; color:#ef4444; margin-bottom:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>' .
                '<h3 style="margin:0 0 6px 0; color:#0f172a; font-size:16px;">Remote Stream Failed</h3>' .
                '<p style="margin:0; font-size:13px; max-width:280px; color:#64748b;">Could not stream the document from Cloudflare R2 bucket. Please check connection.</p>' .
                '</body></html>',
                200,
                ['Content-Type' => 'text/html']
            );
        }
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return response(
                '<html><body style="font-family:sans-serif; display:flex; flex-direction:column; justify-content:center; align-items:center; height:90vh; color:#64748b; background:#f8fafc; text-align:center; padding:20px;">' .
                '<svg style="width:48px; height:48px; color:#f59e0b; margin-bottom:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>' .
                '<h3 style="margin:0 0 6px 0; color:#0f172a; font-size:16px;">File Missing on Server</h3>' .
                '<p style="margin:0; font-size:13px; max-width:280px; color:#64748b;">This local file was wiped from server memory during redeployment. Please configure Cloudflare R2 bucket settings in your environment to ensure persistent uploads.</p>' .
                '</body></html>',
                200,
                ['Content-Type' => 'text/html']
            );
        }
        return response()->file(\Illuminate\Support\Facades\Storage::disk('local')->path($path));
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
        if (empty($path)) {
            return redirect('https://placehold.co/600x800?text=Scan+Failed+Placeholder');
        }
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
        $path = $field->field_value;
        if (str_starts_with($path, 'http')) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(15)->get($path);
                if ($response->successful()) {
                    $mime = $response->header('Content-Type') ?: 'application/octet-stream';
                    return response($response->body(), 200, [
                        'Content-Type' => $mime,
                        'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to stream remote document: " . $e->getMessage());
            }
            return response(
                '<html><body style="font-family:sans-serif; display:flex; flex-direction:column; justify-content:center; align-items:center; height:90vh; color:#64748b; background:#f8fafc; text-align:center; padding:20px;">' .
                '<svg style="width:48px; height:48px; color:#ef4444; margin-bottom:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>' .
                '<h3 style="margin:0 0 6px 0; color:#0f172a; font-size:16px;">Remote Stream Failed</h3>' .
                '<p style="margin:0; font-size:13px; max-width:280px; color:#64748b;">Could not stream the custom field document from Cloudflare R2 bucket. Please check connection.</p>' .
                '</body></html>',
                200,
                ['Content-Type' => 'text/html']
            );
        }
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return response(
                '<html><body style="font-family:sans-serif; display:flex; flex-direction:column; justify-content:center; align-items:center; height:90vh; color:#64748b; background:#f8fafc; text-align:center; padding:20px;">' .
                '<svg style="width:48px; height:48px; color:#f59e0b; margin-bottom:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>' .
                '<h3 style="margin:0 0 6px 0; color:#0f172a; font-size:16px;">File Missing on Server</h3>' .
                '<p style="margin:0; font-size:13px; max-width:280px; color:#64748b;">This local file was wiped from server memory during redeployment. Please configure Cloudflare R2 bucket settings in your environment to ensure persistent uploads.</p>' .
                '</body></html>',
                200,
                ['Content-Type' => 'text/html']
            );
        }
        return response()->file(\Illuminate\Support\Facades\Storage::disk('local')->path($path));
    })->name('application-field.file');

    // UAT FEEDBACK SUBMISSION
    Route::post('/uat-feedback', [\App\Http\Controllers\UatFeedbackController::class, 'store'])->name('uat.store');

});