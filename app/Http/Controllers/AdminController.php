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
    public function index()
    {
        // 1. Fetch the queue of applications (with pagination)
        $applications = \App\Models\Application::with(['user.profile', 'document.aiResult'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);

        // 2. NEW: Calculate the real-time analytics for the top cards
        $pendingCount = \App\Models\Application::where('status', 'Pending')->count();
        $approvedCount = \App\Models\Application::where('status', 'Approved')->count();
        $rejectedCount = \App\Models\Application::where('status', 'Rejected')->count();
        
        $avgFraudScore = \App\Models\AIResult::avg('fraud_probability') ?? 0;
        $avgFraudScore = round($avgFraudScore, 1); // Round to 1 decimal place

        // 3. Send EVERYTHING to the dashboard
        return view('admin.dashboard', compact(
            'applications', 
            'pendingCount', 
            'approvedCount', 
            'rejectedCount', 
            'avgFraudScore'
        ));
    }

    // 2. Display the Document Evaluation Screen
    public function review($id)
    {
        $application = Application::with(['document.aiResult', 'evaluator', 'user.profile'])->findOrFail($id);
        return view('admin.review', compact('application'));
        
        // Auto-update status to "Under Review" if it is Pending
        if ($application->status === 'Pending') {
            $application->update(['status' => 'Under Review']);
        }

        return view('admin.review', compact('application'));
    }

    // We just changed the name from scan() to runScan() here!
    public function runScan($id)
    {
        $application = \App\Models\Application::with('document')->findOrFail($id);
        $document = $application->document;

        // SMART PATH LOCATOR: Cloud Workstations sometimes shift file paths. 
        // We will securely check all possible locations to guarantee we find the image.
        $pathsToTry = [
            public_path($document->file_path),
            storage_path('app/public/' . $document->file_path),
            base_path('public/' . $document->file_path)
        ];

        $actualPath = null;
        foreach ($pathsToTry as $path) {
            if ($path && file_exists($path)) {
                $actualPath = $path;
                break;
            }
        }

        // Failsafe if the file truly didn't upload
        if (!$actualPath) {
            return back()->with('error', 'AI Scan Failed: File is missing from the server disk. Please ask the student to submit a fresh application.');
        }

        try {
            // Send the securely located file to your REAL Python AI Pipeline!
            // Note: We changed 'document' to 'file' and updated the URL to '/analyze-document'
            $response = \Illuminate\Support\Facades\Http::timeout(60)->attach(
                'file', file_get_contents($actualPath), $document->original_name
            )->post('http://127.0.0.1:5000/analyze-document');

            if ($response->successful()) {
                $result = $response->json();
                
                \App\Models\AIResult::updateOrCreate(
                    ['document_id' => $document->id],
                    [
                        'fraud_probability' => $result['fraud_probability'] ?? 10,
                        'classification' => $result['classification'] ?? 'authentic',
                        // Extracting the nested heatmap path from your real AI's response
                        'heatmap_path' => $result['paths']['heatmap_path'] ?? '', 
                    ]
                );

                return back()->with('success', 'Deep Learning analysis complete.');
            } else {
                return back()->with('error', 'Python API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Microservice Offline: Ensure your Python Flask server (app.py) is running! (' . $e->getMessage() . ')');
        }
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
        
        // 3. Update the status and attach the Audit Trail data!
        $application->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
            'evaluated_by' => auth()->id() ?? 1 // Automatically logs WHICH admin made the decision
        ]);

        // 4. SECURE REDIRECT: Kick the user back to the dashboard immediately 
        // so they don't get stuck on this POST route and trigger a GET error!
        return redirect()->route('admin.dashboard')
            ->with('success', 'Application APP-' . $application->id . ' has been successfully ' . $request->status . '.');
    }
}