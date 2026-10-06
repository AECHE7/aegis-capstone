<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Services\AIVerificationService;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationStatusMail;

class AdminController extends Controller
{
    private function validateAdminAccess(Application $application)
    {
        if (auth()->user()->role === 'admin') {
            // If the application is explicitly assigned to this staff member, always permit access
            if ($application->assigned_to === auth()->id()) {
                return;
            }

            $assignedScholarshipIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            if (!empty($assignedScholarshipIds) && !in_array($application->scholarship_id, $assignedScholarshipIds)) {
                abort(403, 'Unauthorized access.');
            }
        }
    }
    // SPRINT 4: Load Admin Dashboard
    public function index(\Illuminate\Http\Request $request)
    {
        // 1. Fetch search and filtering parameters
        if ($request->query('status') === 'Cancelled') {
            $query = \App\Models\Application::onlyTrashed();
        } else {
            $query = \App\Models\Application::query();
        }

        $query->with(['user.profile', 'document.aiResult', 'academicTerm', 'scholarship', 'customFields']);

        if (auth()->user()->role === 'admin') {
            $assignedScholarshipIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            if (!empty($assignedScholarshipIds)) {
                $query->where(function ($sq) use ($assignedScholarshipIds) {
                    $sq->whereIn('scholarship_id', $assignedScholarshipIds)
                       ->orWhere('assigned_to', auth()->id());
                });
            }

            $assignmentFilter = $request->query('assignment', 'all');
            if ($assignmentFilter === 'mine') {
                $query->where('assigned_to', auth()->id());
            } elseif ($assignmentFilter === 'unassigned') {
                $query->whereNull('assigned_to');
            }
        } elseif ($request->filled('assigned_to_staff')) {
            if ($request->assigned_to_staff === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to_staff);
            }
        }

        if ($request->query('archived') == '1') {
            $query->where('is_archived', true);
        } elseif ($request->query('status') === 'Rejected') {
            // Rejected applications are automatically archived, so show archived when specifically filtering by Rejected
            $query->where('is_archived', true);
        } else {
            $query->where('is_archived', false);
        }

        if ($request->filled('scholarship_id')) {
            $query->where('scholarship_id', $request->scholarship_id);
        }

        if ($request->filled('status') && $request->status !== 'Cancelled') {
            $query->where('status', $request->status);
        }

        if ($request->filled('academic_term_id')) {
            $query->where('academic_term_id', $request->academic_term_id);
        } elseif ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('type')) {
            if ($request->type === 'renewal') {
                $query->where('is_renewal', true);
            } elseif ($request->type === 'new') {
                $query->where('is_renewal', false);
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $normalizedSearch = strtoupper(preg_replace('/\s+/', '', $search));
            $searchHash = hash('sha256', $normalizedSearch);

            $query->where(function ($q) use ($search, $searchHash) {
                $q->where('program_name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search, $searchHash) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhereHas('profile', function ($pq) use ($searchHash) {
                            // MED-8: clsu_id_number is AES-256 encrypted — LIKE never matches ciphertext.
                            // Use the SHA-256 hash index for exact ID lookups only.
                            $pq->where('clsu_id_hash', $searchHash);
                        });
                  });
            });
        }

        if ($request->query('sort') === 'priority') {
            $query->leftJoin('documents', function ($join) {
                $join->on('applications.id', '=', 'documents.application_id')
                     ->where('documents.document_type', '=', 'COG');
            })
            ->leftJoin('a_i_results', 'documents.id', '=', 'a_i_results.document_id')
            ->select('applications.*')
            ->orderByRaw("CASE WHEN a_i_results.fraud_probability >= 70.00 THEN 0 ELSE 1 END ASC")
            ->orderByRaw("CASE WHEN applications.status = 'Under Review' THEN 0 ELSE 1 END ASC")
            ->orderByRaw("CASE WHEN applications.status = 'Under Review' THEN applications.updated_at ELSE NULL END ASC")
            ->orderBy('applications.created_at', 'desc');
        } elseif ($request->query('sort') === 'gwa_asc') {
            $query->orderBy('applications.gwa', 'asc');
        } elseif ($request->query('sort') === 'gwa_desc') {
            $query->orderBy('applications.gwa', 'desc');
        } elseif ($request->query('sort') === 'oldest') {
            $query->orderBy('applications.created_at', 'asc');
        } else {
            $query->orderBy('applications.created_at', 'desc');
        }

        $applications = $query->paginate(15)
                            ->withQueryString();

        // 2. Calculate the real-time analytics for the top cards using consolidated aggregation (M-01)
        $baseAnalyticsQuery = \App\Models\Application::query();

        if (auth()->user()->role === 'admin') {
            $assignedScholarshipIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            if (!empty($assignedScholarshipIds)) {
                $baseAnalyticsQuery->where(function ($sq) use ($assignedScholarshipIds) {
                    $sq->whereIn('scholarship_id', $assignedScholarshipIds)
                       ->orWhere('assigned_to', auth()->id());
                });
            }
        }

        if ($request->filled('scholarship_id')) {
            $baseAnalyticsQuery->where('scholarship_id', $request->scholarship_id);
        }

        // Single grouped query for all active application status counts
        $statusCounts = (clone $baseAnalyticsQuery)
            ->where('is_archived', false)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $pendingCount     = (int) ($statusCounts['Pending'] ?? 0);
        $underReviewCount = (int) ($statusCounts['Under Review'] ?? 0);
        $approvedCount    = (int) ($statusCounts['Approved'] ?? 0);
        $rejectedCount    = (clone $baseAnalyticsQuery)->where('status', 'Rejected')->count();
        $archivedCount    = (clone $baseAnalyticsQuery)->where('is_archived', true)->count();
        $cancelledCount   = (clone $baseAnalyticsQuery)->onlyTrashed()->count();
        
        // Scope avgFraudScore strictly to assigned / filtered scholarships
        $avgFraudQuery = \App\Models\AIResult::whereHas('document.application', function ($q) use ($request) {
            if (auth()->user()->role === 'admin') {
                $assignedScholarshipIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
                if (!empty($assignedScholarshipIds)) {
                    $q->where(function ($sq) use ($assignedScholarshipIds) {
                        $sq->whereIn('scholarship_id', $assignedScholarshipIds)
                           ->orWhere('assigned_to', auth()->id());
                    });
                }
            }
            if ($request->filled('scholarship_id')) {
                $q->where('scholarship_id', $request->scholarship_id);
            }
        });
        $avgFraudScore = $avgFraudQuery->avg('fraud_probability') ?? 0;
        $avgFraudScore = round($avgFraudScore, 1); // Round to 1 decimal place

        // Fetch all scholarships, academic terms, and years for filters
        if (auth()->user()->role === 'admin') {
            $userScholarships = auth()->user()->scholarships()->orderBy('name', 'asc')->get();
            $scholarships = $userScholarships->isNotEmpty() ? $userScholarships : \App\Models\Scholarship::orderBy('name', 'asc')->get();
        } else {
            $scholarships = \App\Models\Scholarship::orderBy('name', 'asc')->get();
        }
        
        $academicTerms = \App\Models\AcademicTerm::orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $years = \App\Models\Application::orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->map(fn($date) => $date ? $date->format('Y') : null)
            ->filter()
            ->unique()
            ->values();

        // 3. Send EVERYTHING to the dashboard
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('admin.partials.application_table', compact('applications', 'archivedCount', 'cancelledCount'))->render(),
                'counts' => [
                    'pending' => $pendingCount,
                    'under_review' => $underReviewCount,
                    'approved' => $approvedCount,
                    'rejected' => $rejectedCount,
                    'archived' => $archivedCount,
                    'cancelled' => $cancelledCount,
                ]
            ]);
        }

        // Fetch active scholars for monitoring panel (Approved scholars)
        $activeScholarsQuery = \App\Models\Application::with(['user.profile', 'scholarship', 'academicTerm'])
            ->where('status', 'Approved');
        if (auth()->user()->role === 'admin') {
            $assignedScholarshipIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            if (!empty($assignedScholarshipIds)) {
                $activeScholarsQuery->whereIn('scholarship_id', $assignedScholarshipIds);
            }
        }
        $activeScholars = $activeScholarsQuery->latest('updated_at')->take(10)->get();

        $staffMembers = \App\Models\User::where('role', 'admin')->where('is_active', true)->orderBy('name', 'asc')->get();

        return view('admin.dashboard', compact(
            'applications', 
            'pendingCount',
            'underReviewCount',
            'approvedCount', 
            'rejectedCount', 
            'avgFraudScore',
            'scholarships',
            'years',
            'academicTerms',
            'archivedCount',
            'cancelledCount',
            'activeScholars',
            'staffMembers'
        ));
    }

    // 2. Display the Document Evaluation Screen
    public function review($id)
    {
        $application = Application::withTrashed()->with(['documents.aiResult', 'evaluator', 'user.profile', 'customFields'])->findOrFail($id);
        $this->validateAdminAccess($application);
        
        // Auto-update status to "Under Review" if it is Pending and NOT cancelled.
        // MED-6: lockForUpdate() prevents duplicate StatusLog entries when two admins open
        // the same Pending application simultaneously (TOCTOU race condition).
        if ($application->status === 'Pending' && !$application->trashed()) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($application) {
                $fresh = Application::lockForUpdate()->find($application->id);
                if ($fresh && $fresh->status === 'Pending') {
                    $fresh->update(['status' => 'Under Review']);
                    $application->status = 'Under Review'; // sync the in-memory model

                    \App\Models\StatusLog::create([
                        'application_id' => $fresh->id,
                        'status' => 'Under Review',
                        'remarks' => 'Application opened for verification review.',
                        'changed_by' => auth()->id() // role middleware guarantees non-null
                    ]);
                }
            });
        }

        // Bridge any custom field files into documents table if missing so they reflect on reviewer canvas
        foreach ($application->customFields as $cfield) {
            $isFieldFile = \Illuminate\Support\Str::startsWith($cfield->field_value, 'uploads/') || 
                          (\Illuminate\Support\Str::startsWith($cfield->field_value, 'http') && 
                           collect(['.pdf', '.png', '.jpg', '.jpeg', '.docx', '.webp'])->contains(fn($ext) => \Illuminate\Support\Str::endsWith(strtolower($cfield->field_value), $ext)));
            if ($isFieldFile) {
                $exists = $application->documents->first(fn($d) => $d->file_path === $cfield->field_value || $d->document_type === $cfield->field_name);
                if (!$exists) {
                    \App\Models\Document::create([
                        'application_id' => $application->id,
                        'file_path' => $cfield->field_value,
                        'original_name' => basename($cfield->field_value),
                        'document_type' => $cfield->field_name,
                        'upload_event' => 'initial',
                        'uploaded_by' => $application->user_id,
                        'is_synced' => true,
                    ]);
                }
            }
        }
        $application->load('documents.aiResult');

        // Auto-trigger AI scan for all documents that do not have an AI result yet and NOT cancelled
        if (!$application->trashed()) {
            $triggerScan = false;
            foreach ($application->documents as $doc) {
                if (!$doc->aiResult) {
                    \App\Models\AIResult::create([
                        'document_id' => $doc->id,
                        'fraud_probability' => 0.00,
                        'classification' => 'scanning'
                    ]);
                    $triggerScan = true;
                }
            }
            if ($triggerScan) {
                \App\Jobs\ScanDocumentJob::dispatch($application->id);
            }
            // Reload relation to reflect the scanning state in the view
            $application->load('documents.aiResult');
        }

        $history = Application::where('user_id', $application->user_id)
            ->where('scholarship_id', $application->scholarship_id)
            ->where('id', '!=', $application->id)
            ->with('academicTerm')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $scholarship = $application->scholarship;

        return view('admin.review', compact('application', 'history', 'scholarship'));
    }

    /**
     * Lightweight API endpoint to check background AI scan status.
     */
    public function scanStatus($id)
    {
        $application = Application::withTrashed()->with('documents.aiResult')->findOrFail($id);
        $this->validateAdminAccess($application);

        $isScanning = $application->documents->contains(
            fn($d) => $d->aiResult && $d->aiResult->classification === 'scanning'
        );

        return response()->json([
            'success' => true,
            'is_scanning' => $isScanning,
            'documents' => $application->documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'document_type' => $doc->document_type,
                    'classification' => $doc->aiResult?->classification,
                    'fraud_probability' => $doc->aiResult?->fraud_probability,
                ];
            })
        ]);
    }

    // 2.5. Restore soft-deleted application (Admin Action)
    public function restoreApplication($id)
    {
        $application = Application::onlyTrashed()->findOrFail($id);
        $this->validateAdminAccess($application);

        $application->restore();

        // Log restoration in status_logs
        \App\Models\StatusLog::create([
            'application_id' => $application->id,
            'status' => $application->status,
            'remarks' => 'Application restored by OSA Admin.',
            'changed_by' => auth()->id() // role middleware guarantees non-null (CRIT-05)
        ]);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Application restored successfully.',
                'status' => $application->status
            ]);
        }

        return redirect()->route('admin.review', $application->id)->with('success', 'Application restored successfully.');
    }

    // We just changed the name from scan() to runScan() here!
    public function runScan($id)
    {
        $application = \App\Models\Application::with('documents')->findOrFail($id);
        $this->validateAdminAccess($application);
        $documents = $application->documents;

        if ($documents->isEmpty()) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'AI Scan Failed: No documents found.'], 404);
            }
            return back()->with('error', 'AI Scan Failed: No documents found.');
        }

        $mode = request()->input('mode', 'standard');

        foreach ($documents as $doc) {
            // 1. Create a placeholder scanning result
            \App\Models\AIResult::updateOrCreate(
                ['document_id' => $doc->id],
                [
                    'fraud_probability' => 0.00,
                    'classification' => 'scanning',
                    'heatmap_path' => null
                ]
            );
        }

        // 2. Dispatch the background job with selected mode
        \App\Jobs\ScanDocumentJob::dispatch($application->id, $mode);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => ucfirst($mode) . ' document verification scan started in the background.']);
        }

        return back()->with('success', ucfirst($mode) . ' document verification scan started in the background.');
    }

    public function runScanSync($id)
    {
        $application = \App\Models\Application::with('documents')->findOrFail($id);
        $this->validateAdminAccess($application);
        $documents = $application->documents;

        if ($documents->isEmpty()) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'AI Scan Failed: No documents found.'], 404);
            }
            return back()->with('error', 'AI Scan Failed: No documents found.');
        }

        $mode = request()->input('mode', 'standard');

        foreach ($documents as $doc) {
            \App\Models\AIResult::updateOrCreate(
                ['document_id' => $doc->id],
                [
                    'fraud_probability' => 0.00,
                    'classification' => 'scanning',
                    'heatmap_path' => null
                ]
            );
        }

        try {
            \App\Jobs\ScanDocumentJob::dispatchSync($application->id, $mode);
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Scan completed successfully.']);
            }
            return back()->with('success', 'Scan completed successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Synchronous scan failed: " . $e->getMessage());
            foreach ($documents as $doc) {
                \App\Models\AIResult::updateOrCreate(
                    ['document_id' => $doc->id],
                    ['classification' => 'failed']
                );
            }
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sync scan failed: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Sync scan failed: ' . $e->getMessage());
        }
    }

    // SECURE DOCUMENT DOWNLOAD FOR ADMIN REVIEW
    public function downloadDocument($id)
    {
        $document = \App\Models\Document::findOrFail($id);
        $application = $document->application;

        if ($application) {
            $this->validateAdminAccess($application);
        } else {
            abort(404, 'Application not found.');
        }

        $docTypeClean = \Illuminate\Support\Str::slug($document->document_type, '_');
        $downloadName = 'APP-' . ($application->id ?? 'unknown') . '_' . ($docTypeClean ?: 'document') . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION);

        // CRIT-5: When R2/cloud storage is active, file_path is a full HTTPS URL.
        // Proxy the remote file through the server so access control is preserved.
        if (str_starts_with($document->file_path, 'http')) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(30)->get($document->file_path);
                if ($response->successful()) {
                    $mime = $response->header('Content-Type') ?: 'application/octet-stream';
                    return response($response->body(), 200, [
                        'Content-Type'        => $mime,
                        'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                        'Cache-Control'       => 'private, no-cache, no-store, must-revalidate',
                    ]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Admin document download (R2) failed for doc #{$id}: " . $e->getMessage());
            }
            return back()->with('error', 'Could not retrieve the document from cloud storage. Please try again.');
        }

        // Local disk path
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($document->file_path)) {
            // Fallback: serve from base64-encoded DB copy if available
            if (!empty($document->file_data)) {
                $binary = base64_decode($document->file_data);
                $ext = strtolower(pathinfo($document->original_name ?? 'file.pdf', PATHINFO_EXTENSION));
                $mime = match($ext) {
                    'pdf' => 'application/pdf',
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    default => 'application/octet-stream'
                };
                return response($binary, 200, [
                    'Content-Type'        => $mime,
                    'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                    'Cache-Control'       => 'private, no-cache, no-store, must-revalidate',
                ]);
            }
            return back()->with('error', 'Document file not found in storage.');
        }

        $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($document->file_path);
        return response()->download($fullPath, $downloadName);
    }

    // MED-2: Removed duplicate exportCsv() method from AdminController.
    // The authoritative, chunked, audit-logged CSV export lives in ReportController::exportCsv().
    // Route admin.export already maps to ReportController. This method is no longer needed.

    public function updateStatus(\Illuminate\Http\Request $request, $id)
    {
        // 1. Validate the incoming decision and remarks
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Returned',
            'remarks' => 'nullable|string'
        ]);

        // 2. Find the application in the database
        $application = \App\Models\Application::findOrFail($id);
        $this->validateAdminAccess($application);
        
        $evaluatorId = auth()->id(); // role middleware guarantees non-null (CRIT-05)

        // 3. Update the status and attach the Audit Trail data!
        $updatePayload = [
            'status' => $request->status,
            'remarks' => $request->remarks,
            'evaluated_by' => $evaluatorId
        ];
        if ($request->status === 'Rejected') {
            $updatePayload['is_archived'] = true;
        }

        $application->update($updatePayload);

        // Log to StatusLog
        \App\Models\StatusLog::create([
            'application_id' => $application->id,
            'status' => $request->status,
            'remarks' => $request->remarks,
            'changed_by' => $evaluatorId
        ]);

        $actionType = 'evaluate_application';
        if (strtolower($request->status) === 'approved') {
            $actionType = 'approve_application';
        } elseif (strtolower($request->status) === 'rejected') {
            $actionType = 'reject_application';
        } elseif (strtolower($request->status) === 'returned') {
            $actionType = 'return_application';
        }

        \App\Services\AuditLoggerService::logAdminAction(
            $evaluatorId,
            $actionType,
            'Application',
            $application->id,
            "Evaluated application APP-{$application->id} (Status: {$request->status})",
            $request->ip()
        );

        // Dispatch database notification
        try {
            if ($application->user) {
                $application->user->notify(new \App\Notifications\ApplicationStatusNotification($application));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send application status database notification: ' . $e->getMessage());
        }

        // 4. Send automated email notification
        if ($application->user && $application->user->email) {
            $isApprovedRenewal = ($application->status === 'Approved' && $application->is_renewal);
            $mailSubject = $isApprovedRenewal
                ? "[A.E.G.I.S.] Scholarship Grant Successfully Renewed: {$application->program_name}"
                : "[A.E.G.I.S.] Official Update: Application " . strtoupper($application->status);

            try {
                $application->load(['user.profile', 'document.aiResult', 'evaluator', 'academicTerm']);
                if ($isApprovedRenewal) {
                    Mail::to($application->user->email)->send(new \App\Mail\ScholarshipRenewalMail($application));
                } else {
                    Mail::to($application->user->email)->send(new ApplicationStatusMail($application));
                }

                // Log email in EmailLog
                \App\Models\EmailLog::create([
                    'application_id' => $application->id,
                    'recipient' => $application->user->email,
                    'subject' => $mailSubject,
                    'content' => "Status updated to: {$application->status}. Remarks: " . ($application->remarks ?? 'None'),
                    'status' => 'sent',
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send application status email: ' . $e->getMessage());
                \App\Models\EmailLog::create([
                    'application_id' => $application->id,
                    'recipient' => $application->user->email,
                    'subject' => $mailSubject,
                    'content' => "Status updated to: {$application->status}. Remarks: " . ($application->remarks ?? 'None'),
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        // 5. SECURE REDIRECT: Kick the user back to the dashboard immediately 
        // so they don't get stuck on this POST route and trigger a GET error!
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Application APP-' . $application->id . ' has been successfully ' . $application->status . '.',
                'status' => $application->status,
                'remarks' => $application->remarks,
                'evaluated_by' => $application->evaluator->name ?? 'System'
            ]);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Application APP-' . $application->id . ' has been successfully ' . $request->status . '.');
    }

    /**
     * Revoke or remove an approved scholarship grant (Scholarship Revocation Workflow).
     */
    public function revokeScholarship(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:5|max:1000'
        ]);

        $application = \App\Models\Application::findOrFail($id);
        $this->validateAdminAccess($application);

        $evaluatorId = auth()->id();
        $reason = $request->input('reason');

        $application->update([
            'status' => 'Revoked',
            'remarks' => $reason,
            'evaluated_by' => $evaluatorId,
        ]);

        \App\Models\StatusLog::create([
            'application_id' => $application->id,
            'status' => 'Revoked',
            'remarks' => "Grant revoked: {$reason}",
            'changed_by' => $evaluatorId,
        ]);

        \App\Services\AuditLoggerService::logAdminAction(
            $evaluatorId,
            'scholarship_revoked',
            'Application',
            $application->id,
            "Revoked scholarship grant for APP-{$application->id} ({$application->program_name}). Reason: {$reason}",
            $request->ip()
        );

        // Send Revocation Email Notification
        if ($application->user && $application->user->email) {
            $mailSubject = "[A.E.G.I.S.] Official Notice: Scholarship Grant Revocation ({$application->program_name})";
            try {
                $application->load(['user.profile', 'evaluator']);
                Mail::to($application->user->email)->send(new \App\Mail\ScholarshipRevocationMail($application, $reason));

                \App\Models\EmailLog::create([
                    'application_id' => $application->id,
                    'recipient' => $application->user->email,
                    'subject' => $mailSubject,
                    'content' => "Grant revoked: {$reason}",
                    'status' => 'sent',
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send scholarship revocation email: ' . $e->getMessage());
                \App\Models\EmailLog::create([
                    'application_id' => $application->id,
                    'recipient' => $application->user->email,
                    'subject' => $mailSubject,
                    'content' => "Grant revoked: {$reason}",
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Scholarship grant for APP-{$application->id} has been revoked and the student notified.",
                'status' => 'Revoked',
            ]);
        }

        return redirect()->route('admin.review', $application->id)
            ->with('success', "Scholarship grant for APP-{$application->id} has been revoked and the student notified.");
    }

    // 4. Archive Application
    public function archive($id)
    {
        $application = Application::findOrFail($id);
        $this->validateAdminAccess($application);
        $application->is_archived = true;
        $application->save();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Application APP-' . $application->id . ' has been archived successfully.',
                'is_archived' => true
            ]);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Application APP-' . $application->id . ' has been archived successfully.');
    }

    // 5. Unarchive Application
    public function unarchive($id)
    {
        $application = Application::findOrFail($id);
        $this->validateAdminAccess($application);
        $application->is_archived = false;
        $application->save();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Application APP-' . $application->id . ' has been unarchived successfully.',
                'is_archived' => false
            ]);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Application APP-' . $application->id . ' has been unarchived successfully.');
    }

    // 6. Bulk Action (Approve / Reject)
    public function bulkAction(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'required|exists:applications,id',
            'status' => 'required|in:Approved,Rejected',
            'remarks' => 'nullable|string'
        ]);

        $status = $request->status;
        $remarks = $request->remarks ?? 'Bulk processed by OSA Administrator.';
        $evaluatorId = auth()->id(); // role middleware guarantees non-null (CRIT-05)

        $count = 0;
        foreach ($request->application_ids as $id) {
            $application = Application::findOrFail($id);
            
            try {
                $this->validateAdminAccess($application);
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                continue; // Skip unauthorized
            }

            $bulkUpdatePayload = [
                'status' => $status,
                'remarks' => $remarks,
                'evaluated_by' => $evaluatorId
            ];
            if ($status === 'Rejected') {
                $bulkUpdatePayload['is_archived'] = true;
            }

            $application->update($bulkUpdatePayload);

            \App\Models\StatusLog::create([
                'application_id' => $application->id,
                'status' => $status,
                'remarks' => $remarks,
                'changed_by' => $evaluatorId
            ]);

            \App\Services\AuditLoggerService::logAdminAction(
                $evaluatorId,
                strtolower($status) === 'approved' ? 'bulk_approve_application' : 'bulk_reject_application',
                'Application',
                $application->id,
                "Bulk evaluated application APP-{$application->id} (Status: {$status})",
                $request->ip()
            );

            try {
                if ($application->user) {
                    $application->user->notify(new \App\Notifications\ApplicationStatusNotification($application));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send bulk database notification: ' . $e->getMessage());
            }

            try {
                if ($application->user && $application->user->email) {
                    \App\Jobs\SendBulkStatusEmailJob::dispatch($application->id);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to dispatch bulk email job: ' . $e->getMessage());
            }

            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$count} application(s) as {$status}."
        ]);
    }

    // 7. Save Admin Notes (AJAX)
    public function saveNotes(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            // MED-5: Enforce a maximum length to prevent DB bloat from oversized notes.
            'admin_notes' => 'nullable|string|max:10000'
        ]);

        $application = Application::findOrFail($id);
        $this->validateAdminAccess($application);

        $application->update([
            'admin_notes' => $request->admin_notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Staff notes updated successfully.'
        ]);
    }

    /**
     * Download or print the official scholarship application and evaluation form PDF.
     */
    public function downloadApprovedForm($id)
    {
        $application = Application::withTrashed()
            ->with([
                'user.profile', 
                'scholarship', 
                'academicTerm', 
                'customFields', 
                'document.aiResult', 
                'documents', 
                'evaluator'
            ])
            ->findOrFail($id);

        $this->validateAdminAccess($application);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.application_form_pdf', ['application' => $application])
            ->setPaper('letter', 'portrait');
        return $pdf->download("APP-{$application->id}_Official_Evaluation_Form.pdf");
    }

    /**
     * Live on-screen preview of the official application and student information form.
     */
    public function previewForm($id)
    {
        $application = Application::withTrashed()
            ->with([
                'user.profile', 
                'scholarship.fields', 
                'academicTerm', 
                'customFields', 
                'document.aiResult', 
                'documents', 
                'evaluator'
            ])
            ->findOrFail($id);

        $this->validateAdminAccess($application);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'application_id' => $application->id,
                'control_no' => 'APP-' . str_pad((string)$application->id, 5, '0', STR_PAD_LEFT),
                'student_name' => $application->user->name ?? 'Student Applicant',
                'program_name' => $application->scholarship?->name ?? 'Scholarship Grant',
                'status' => $application->status,
                'download_url' => route('admin.application.download-form', $application->id),
                'html' => view('components.applicant-form-content', compact('application'))->render(),
            ]);
        }

        return view('components.applicant-form-content', compact('application'));
    }

    /**
     * Dedicated Applicant & Student Information Forms Module (Staff & Director).
     * Browse, search, filter, preview on-screen, and export official PDF forms.
     */
    public function applicantFormsIndex(Request $request)
    {
        $user = auth()->user();
        $query = Application::query()
            ->with(['user.profile', 'scholarship', 'academicTerm', 'evaluator', 'documents.aiResult'])
            ->where('is_archived', false);

        $assignedScholarshipIds = [];
        if ($user->role === 'admin') {
            $assignedScholarshipIds = $user->scholarships()->pluck('scholarships.id')->toArray();
            if (!empty($assignedScholarshipIds)) {
                $query->whereIn('scholarship_id', $assignedScholarshipIds);
            }
        }

        // Search Filter (Student Name, Email, CLSU ID, or Application ID)
        if ($request->filled('q')) {
            $searchTerm = trim($request->q);
            $numericId = (int) preg_replace('/[^0-9]/', '', $searchTerm);
            $normalizedSearch = strtoupper(preg_replace('/\s+/', '', $searchTerm));
            $searchHash = hash('sha256', $normalizedSearch);

            $query->where(function ($q) use ($searchTerm, $numericId, $searchHash) {
                if ($numericId > 0) {
                    $q->orWhere('id', $numericId);
                }
                $q->orWhereHas('user', function ($uq) use ($searchTerm, $searchHash) {
                    $uq->where('name', 'like', "%{$searchTerm}%")
                       ->orWhere('email', 'like', "%{$searchTerm}%")
                       ->orWhereHas('profile', function ($pq) use ($searchTerm, $searchHash) {
                           // MED-8: clsu_id_number is AES-256 encrypted — search via deterministic SHA-256 hash
                           $pq->where('clsu_id_hash', $searchHash)
                              ->orWhere('college', 'like', "%{$searchTerm}%")
                              ->orWhere('course', 'like', "%{$searchTerm}%");
                       });
                })->orWhere('program_name', 'like', "%{$searchTerm}%");
            });
        }

        // Program Filter
        if ($request->filled('scholarship_id')) {
            $query->where('scholarship_id', $request->scholarship_id);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Academic Term Filter
        if ($request->filled('academic_term_id')) {
            $query->where('academic_term_id', $request->academic_term_id);
        }

        // Quick Statistics
        $statsBase = Application::query()->where('is_archived', false);
        if ($user->role === 'admin' && !empty($assignedScholarshipIds)) {
            $statsBase->whereIn('scholarship_id', $assignedScholarshipIds);
        }

        $stats = [
            'total' => (clone $statsBase)->count(),
            'approved' => (clone $statsBase)->where('status', 'Approved')->count(),
            'review' => (clone $statsBase)->whereIn('status', ['Under Review', 'Pending Verification'])->count(),
            'today' => (clone $statsBase)->whereDate('created_at', now()->today())->count(),
        ];

        $applications = $query->latest()->paginate(12)->withQueryString();

        $scholarships = ($user->role === 'admin')
            ? $user->scholarships()->orderBy('name')->get()
            : \App\Models\Scholarship::orderBy('name')->get();

        $academicTerms = \App\Models\AcademicTerm::orderByDesc('is_active')->orderBy('academic_year')->get();

        return view('admin.applicant_forms', compact('applications', 'scholarships', 'academicTerms', 'stats'));
    }
}