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
            $assignedScholarshipIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            if (!in_array($application->scholarship_id, $assignedScholarshipIds)) {
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

        $query->with(['user.profile', 'document.aiResult', 'academicTerm']);

        if (auth()->user()->role === 'admin') {
            $assignedScholarshipIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            $query->whereIn('scholarship_id', $assignedScholarshipIds);

            $assignmentFilter = $request->query('assignment', 'mine');
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
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('program_name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhereHas('profile', function ($pq) use ($search) {
                            $pq->where('clsu_id_number', 'like', "%{$search}%");
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
            ->orderByRaw('CASE WHEN a_i_results.fraud_probability >= 50.00 THEN 0 ELSE 1 END ASC')
            ->orderByRaw('CASE WHEN applications.status = "Under Review" THEN 0 ELSE 1 END ASC')
            ->orderByRaw('CASE WHEN applications.status = "Under Review" THEN applications.updated_at ELSE NULL END ASC')
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

        // 2. Calculate the real-time analytics for the top cards (only active applications)
        $pendingCountQuery = \App\Models\Application::where('is_archived', false)->where('status', 'Pending');
        $underReviewCountQuery = \App\Models\Application::where('is_archived', false)->where('status', 'Under Review');
        $approvedCountQuery = \App\Models\Application::where('is_archived', false)->where('status', 'Approved');
        $rejectedCountQuery = \App\Models\Application::where('is_archived', false)->where('status', 'Rejected');
        $archivedCountQuery = \App\Models\Application::where('is_archived', true);
        $cancelledCountQuery = \App\Models\Application::onlyTrashed();

        if (auth()->user()->role === 'admin') {
            $assignedScholarshipIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            $pendingCountQuery->whereIn('scholarship_id', $assignedScholarshipIds);
            $underReviewCountQuery->whereIn('scholarship_id', $assignedScholarshipIds);
            $approvedCountQuery->whereIn('scholarship_id', $assignedScholarshipIds);
            $rejectedCountQuery->whereIn('scholarship_id', $assignedScholarshipIds);
            $archivedCountQuery->whereIn('scholarship_id', $assignedScholarshipIds);
            $cancelledCountQuery->whereIn('scholarship_id', $assignedScholarshipIds);
        }

        $pendingCount = $pendingCountQuery->count();
        $underReviewCount = $underReviewCountQuery->count();
        $approvedCount = $approvedCountQuery->count();
        $rejectedCount = $rejectedCountQuery->count();
        $archivedCount = $archivedCountQuery->count();
        $cancelledCount = $cancelledCountQuery->count();
        
        $avgFraudScore = \App\Models\AIResult::avg('fraud_probability') ?? 0;
        $avgFraudScore = round($avgFraudScore, 1); // Round to 1 decimal place

        // Fetch all scholarships, academic terms, and years for filters
        if (auth()->user()->role === 'admin') {
            $scholarships = auth()->user()->scholarships()->orderBy('name', 'asc')->get();
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
            $activeScholarsQuery->whereIn('scholarship_id', $assignedScholarshipIds);
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
        
        // Auto-update status to "Under Review" if it is Pending and NOT cancelled
        if ($application->status === 'Pending' && !$application->trashed()) {
            $application->update(['status' => 'Under Review']);

            \App\Models\StatusLog::create([
                'application_id' => $application->id,
                'status' => 'Under Review',
                'remarks' => 'Application opened for verification review.',
                'changed_by' => auth()->id() // role middleware guarantees non-null (CRIT-05)
            ]);
        }

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

        // Make sure the file actually exists in storage
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($document->file_path)) {
            return back()->with('error', 'Document file not found in storage.');
        }

        $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($document->file_path);
        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);

        // Construct a human-readable download filename using Application ID and document type
        $docTypeClean = \Illuminate\Support\Str::slug($document->document_type, '_');
        $downloadName = 'APP-' . ($application->id ?? 'unknown') . '_' . ($docTypeClean ?: 'document') . '.' . $extension;

        return response()->download($fullPath, $downloadName);
    }

    // GENERATE EXCEL/CSV REPORT OF APPROVED SCHOLARS
    public function exportCsv()
    {
        // Grab all APPROVED applications with the student's background profile
        $query = \App\Models\Application::where('status', 'Approved')
                            ->with('user.profile');
        if (auth()->user()->role === 'admin') {
            $assignedScholarshipIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            $query->whereIn('scholarship_id', $assignedScholarshipIds);
        }
        $applications = $query->get();

        $filename = "Verified_Scholars_" . date('Y-m-d') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // The columns that will appear in the Excel file
        $columns = ['Ref ID', 'Student Name', 'CLSU ID', 'Course', 'Year Level', 'Program/Grant', 'GWA', 'Date Approved'];

        $callback = function() use($applications, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); // Write headers

            foreach ($applications as $app) {
                $row = [
                    'APP-' . $app->id,
                    $app->user->name ?? 'N/A',
                    $app->user->profile->clsu_id_number ?? 'N/A',
                    $app->user->profile->course ?? 'N/A',
                    $app->user->profile->year_level ?? 'N/A',
                    $app->program_name,
                    $app->gwa !== null ? number_format($app->gwa, 2) : 'N/A',
                    $app->updated_at->format('M d, Y')
                ];
                fputcsv($file, $row); // Write data row
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

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
        $application->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
            'evaluated_by' => $evaluatorId
        ]);

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
            $mailSubject = "[A.E.G.I.S.] Official Update: Application " . strtoupper($application->status);
            try {
                $application->load(['user.profile', 'document.aiResult', 'evaluator']);
                Mail::to($application->user->email)->send(new ApplicationStatusMail($application));

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

            $application->update([
                'status' => $status,
                'remarks' => $remarks,
                'evaluated_by' => $evaluatorId
            ]);

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
            'admin_notes' => 'nullable|string'
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
}