<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    // Build query based on filters
    private function buildReportQuery(Request $request)
    {
        $query = Application::with(['user.profile', 'document.aiResult', 'academicTerm']);

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

        return $query->latest();
    }

    // Generate and Download CSV Report
    public function exportCsv(Request $request)
    {
        $applications = $this->buildReportQuery($request)->get();
        $filename = "aegis_scholarship_report_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Ref ID', 'Student Name', 'CLSU ID', 'Course', 'Year Level', 'Program/Grant', 'GWA', 'Status', 'Date Submitted'];

        $callback = function() use($applications, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($applications as $app) {
                $row = [
                    'APP-' . $app->id,
                    $app->user->name ?? 'Unknown',
                    $app->user->profile->clsu_id_number ?? 'N/A',
                    $app->user->profile->course ?? 'N/A',
                    $app->user->profile->year_level ?? 'N/A',
                    $app->program_name,
                    $app->gwa,
                    $app->status,
                    $app->created_at->format('Y-m-d')
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // Generate and Download PDF Report
    public function exportPdf(Request $request)
    {
        $applications = $this->buildReportQuery($request)->get();
        
        // Calculate statistics for the summary block
        $totalCount = $applications->count();
        $approvedCount = $applications->where('status', 'Approved')->count();
        $rejectedCount = $applications->where('status', 'Rejected')->count();
        $pendingCount = $applications->whereIn('status', ['Pending', 'Under Review'])->count();
        
        $totalFraud = 0;
        $scoredCount = 0;
        foreach ($applications as $app) {
            if ($app->document && $app->document->aiResult) {
                $totalFraud += $app->document->aiResult->fraud_probability;
                $scoredCount++;
            }
        }
        $avgFraudScore = $scoredCount > 0 ? round($totalFraud / $scoredCount, 1) : 0;

        $stats = [
            'total' => $totalCount,
            'approved' => $approvedCount,
            'rejected' => $rejectedCount,
            'pending' => $pendingCount,
            'avg_fraud' => $avgFraudScore
        ];

        // Load the view and pass the data to it
        $pdf = Pdf::loadView('admin.report_pdf', compact('applications', 'stats'));
        
        // Download the generated PDF
        return $pdf->download('aegis_official_report_' . date('Y-m-d') . '.pdf');
    }
}