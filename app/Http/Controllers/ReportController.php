<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    // Generate and Download CSV Report
    public function exportCsv()
    {
        $applications = Application::all();
        $filename = "aegis_scholarship_report_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Reference ID', 'Applicant ID', 'Program', 'GWA', 'Status', 'Date Submitted'];

        $callback = function() use($applications, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($applications as $app) {
                $row['Reference ID']  = 'APP-' . $app->id;
                $row['Applicant ID']  = $app->user_id;
                $row['Program']       = $app->program_name;
                $row['GWA']           = $app->gwa;
                $row['Status']        = $app->status;
                $row['Date Submitted']= $app->created_at->format('Y-m-d');

                fputcsv($file, array($row['Reference ID'], $row['Applicant ID'], $row['Program'], $row['GWA'], $row['Status'], $row['Date Submitted']));
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // Generate and Download PDF Report
    public function exportPdf()
    {
        $applications = Application::latest()->get();
        
        // Load the view and pass the data to it
        $pdf = Pdf::loadView('admin.report_pdf', compact('applications'));
        
        // Download the generated PDF
        return $pdf->download('aegis_official_report_' . date('Y-m-d') . '.pdf');
    }
}