<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Services\AIVerificationService;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationStatusMail;

class AdminController extends Controller
{
    // SPRINT 4: Load Admin Dashboard
    public function index(\Illuminate\Http\Request $request)
    {
        // 1. Fetch search and filtering parameters
        $query = \App\Models\Application::with(['user.profile', 'document.aiResult', 'academicTerm']);

        if (auth()->user()->role === 'admin') {
            $assignedScholarshipIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            $query->whereIn('scholarship_id', $assignedScholarshipIds);
        }

        if ($request->query('archived') == '1') {
            $query->where('is_archived', true);
        } else {
            $query->where('is_archived', false);
        }

        if ($request->filled('scholarship_id')) {
            $query->where('scholarship_id', $request->scholarship_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('academic_term_id')) {
            $query->where('academic_term_id', $request->academic_term_id);
        } elseif ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
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

        $applications = $query->orderBy('created_at', 'desc')
                            ->paginate(15)
                            ->withQueryString();

        // 2. Calculate the real-time analytics for the top cards (only active applications)
        $pendingCountQuery = \App\Models\Application::where('is_archived', false)->where('status', 'Pending');
        $underReviewCountQuery = \App\Models\Application::where('is_archived', false)->where('status', 'Under Review');
        $approvedCountQuery = \App\Models\Application::where('is_archived', false)->where('status', 'Approved');
        $rejectedCountQuery = \App\Models\Application::where('is_archived', false)->where('status', 'Rejected');
        $archivedCountQuery = \App\Models\Application::where('is_archived', true);

        if (auth()->user()->role === 'admin') {
            $assignedScholarshipIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            $pendingCountQuery->whereIn('scholarship_id', $assignedScholarshipIds);
            $underReviewCountQuery->whereIn('scholarship_id', $assignedScholarshipIds);
            $approvedCountQuery->whereIn('scholarship_id', $assignedScholarshipIds);
            $rejectedCountQuery->whereIn('scholarship_id', $assignedScholarshipIds);
            $archivedCountQuery->whereIn('scholarship_id', $assignedScholarshipIds);
        }

        $pendingCount = $pendingCountQuery->count();
        $underReviewCount = $underReviewCountQuery->count();
        $approvedCount = $approvedCountQuery->count();
        $rejectedCount = $rejectedCountQuery->count();
        $archivedCount = $archivedCountQuery->count();
        
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
            'archivedCount'
        ));
    }

    // 2. Display the Document Evaluation Screen
    public function review($id)
    {
        $application = Application::with(['document.aiResult', 'evaluator', 'user.profile'])->findOrFail($id);
        
        // Auto-update status to "Under Review" if it is Pending
        if ($application->status === 'Pending') {
            $application->update(['status' => 'Under Review']);

            \App\Models\StatusLog::create([
                'application_id' => $application->id,
                'status' => 'Under Review',
                'remarks' => 'Application opened for verification review.',
                'changed_by' => auth()->id() ?? 2
            ]);
        }

        // Auto-trigger AI scan if a document exists and no AI result exists yet
        if ($application->document && !$application->document->aiResult) {
            \App\Models\AIResult::create([
                'document_id' => $application->document->id,
                'fraud_probability' => 0.00,
                'classification' => 'scanning',
                'heatmap_path' => null
            ]);

            \App\Jobs\ScanDocumentJob::dispatch($application->id);

            // Reload relation to reflect the scanning state in the view
            $application->load('document.aiResult');
        }

        return view('admin.review', compact('application'));
    }

    // We just changed the name from scan() to runScan() here!
    public function runScan($id)
    {
        $application = \App\Models\Application::with('document')->findOrFail($id);
        $document = $application->document;

        if (!$document) {
            return back()->with('error', 'AI Scan Failed: Document not found.');
        }

        // 1. Create a placeholder scanning result
        \App\Models\AIResult::updateOrCreate(
            ['document_id' => $document->id],
            [
                'fraud_probability' => 0.00,
                'classification' => 'scanning',
                'heatmap_path' => null
            ]
        );

        // 2. Dispatch the background job
        \App\Jobs\ScanDocumentJob::dispatch($application->id);

        return back()->with('success', 'Document verification scan started in the background.');
    }

    // SECURE DOCUMENT DOWNLOAD FOR ADMIN REVIEW
    public function downloadDocument($id)
    {
        $document = \App\Models\Document::findOrFail($id);

        // Make sure the file actually exists in storage
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($document->file_path)) {
            return back()->with('error', 'Document file not found in storage.');
        }

        $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($document->file_path);
        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);

        // Construct a human-readable download filename using Application ID
        $application = \App\Models\Application::where('document_id', $document->id)->first()
                      ?? \App\Models\Application::whereHas('document', fn($q) => $q->where('id', $document->id))->first();

        $downloadName = 'APP-' . ($application->id ?? 'unknown') . '_COG.' . $extension;

        return response()->download($fullPath, $downloadName);
    }

    // GENERATE EXCEL/CSV REPORT OF APPROVED SCHOLARS
    public function exportCsv()
    {
        // Grab all APPROVED applications with the student's background profile
        $applications = \App\Models\Application::where('status', 'Approved')
                            ->with('user.profile')
                            ->get();

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
                    $app->gwa,
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
            'status' => 'required|in:Approved,Rejected',
            'remarks' => 'nullable|string'
        ]);

        // 2. Find the application in the database
        $application = \App\Models\Application::findOrFail($id);
        
        $evaluatorId = auth()->id() ?? 2; // Default to admin user 2 if none logged in

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

        // Dispatch database notification
        try {
            if ($application->user) {
                $application->user->notify(new \App\Notifications\ApplicationStatusNotification($application));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send application status database notification: ' . $e->getMessage());
        }

        // 4. Send automated email notification
        try {
            $application->load(['user.profile', 'document.aiResult', 'evaluator']);
            if ($application->user && $application->user->email) {
                $mailSubject = "[A.E.G.I.S.] Official Update: Application " . strtoupper($application->status);
                Mail::to($application->user->email)->send(new ApplicationStatusMail($application));

                // Log email in EmailLog
                \App\Models\EmailLog::create([
                    'application_id' => $application->id,
                    'recipient' => $application->user->email,
                    'subject' => $mailSubject,
                    'content' => "Status updated to: {$application->status}. Remarks: " . ($application->remarks ?? 'None')
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send application status email: ' . $e->getMessage());
        }

        // 5. SECURE REDIRECT: Kick the user back to the dashboard immediately 
        // so they don't get stuck on this POST route and trigger a GET error!
        return redirect()->route('admin.dashboard')
            ->with('success', 'Application APP-' . $application->id . ' has been successfully ' . $request->status . '.');
    }

    // 4. Archive Application
    public function archive($id)
    {
        $application = Application::findOrFail($id);
        $application->is_archived = true;
        $application->save();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Application APP-' . $application->id . ' has been archived successfully.');
    }

    // 5. Unarchive Application
    public function unarchive($id)
    {
        $application = Application::findOrFail($id);
        $application->is_archived = false;
        $application->save();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Application APP-' . $application->id . ' has been unarchived successfully.');
    }
}