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
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');


// ==========================================
// 🔒 SECURED ROUTES (Must be Logged In!)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Logout is secured
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // STUDENT PORTAL
    Route::get('/apply', [ApplicationController::class, 'create'])->name('student.apply');
    Route::post('/apply', [ApplicationController::class, 'store'])->name('student.store');
    Route::get('/student/dashboard', [ApplicationController::class, 'dashboard'])->name('student.dashboard');

    // OSA ADMIN DASHBOARD
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/review/{id}', [AdminController::class, 'review'])->name('admin.review');
        Route::post('/review/{id}/scan', [AdminController::class, 'runScan'])->name('admin.scan');
        Route::post('/review/{id}/status', [AdminController::class, 'updateStatus'])->name('admin.updateStatus');
        Route::get('/export-csv', [AdminController::class, 'exportCsv'])->name('admin.export');
    });

    // SUPER ADMIN (Scholarship Management)
    Route::prefix('superadmin')->group(function () {
        Route::get('/scholarships', [SuperAdminController::class, 'index'])->name('superadmin.scholarships');
        Route::post('/scholarships', [SuperAdminController::class, 'store'])->name('superadmin.scholarships.store');
        Route::post('/scholarships/{id}/toggle', [SuperAdminController::class, 'toggleStatus'])->name('superadmin.scholarships.toggle');
        Route::get('/analytics', [SuperAdminController::class, 'analytics'])->name('superadmin.analytics');
    });

    // SECURE FILE VIEWING
    Route::get('/document/{id}/image', function ($id) {
        $document = \App\Models\Document::findOrFail($id);
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($document->file_path)) { abort(404); }
        return response()->file(\Illuminate\Support\Facades\Storage::disk('local')->path($document->file_path));
    })->name('document.view');

    Route::get('/document/{id}/heatmap', function ($id) {
        $aiResult = \App\Models\AIResult::where('document_id', $id)->firstOrFail();
        $path = base_path('aegis-ai/' . $aiResult->heatmap_path);
        if (!file_exists($path)) { abort(404); }
        return response()->file($path);
    })->name('document.heatmap');

});