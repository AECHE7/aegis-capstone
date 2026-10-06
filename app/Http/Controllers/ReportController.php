<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\StatusLog;
use App\Models\EmailLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    // Build query based on filters
    private function buildReportQuery(Request $request)
    {
        $query = Application::with(['user.profile', 'document.aiResult', 'academicTerm']);

        if (auth()->user()->role === 'admin') {
            $assignedScholarshipIds = auth()->user()->scholarships()->pluck('scholarships.id')->toArray();
            if (!empty($assignedScholarshipIds)) {
                $query->whereIn('scholarship_id', $assignedScholarshipIds);
            }
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
            $search = trim($request->search);
            $normalizedSearch = strtoupper(preg_replace('/\s+/', '', $search));
            $searchHash = hash('sha256', $normalizedSearch);

            $query->where(function ($q) use ($search, $searchHash) {
                $q->where('program_name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search, $searchHash) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhereHas('profile', function ($pq) use ($search, $searchHash) {
                            $pq->where('clsu_id_hash', $searchHash)
                              ->orWhere('clsu_id_number', 'like', "%{$search}%");
                        });
                  });
            });
        }

        return $query->latest();
    }

    // Generate and Download CSV Report
    public function exportCsv(Request $request)
    {
        $this->logExportAccess($request, 'applications_list', 'csv');
        $query = $this->buildReportQuery($request);
        $filename = "ched_dost_masterlist_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Format aligned with institutional and CHED/DOST portal compliance standards
        $columns = [
            'Reference ID',
            'Student Full Name',
            'CLSU ID Number',
            'Course / Degree Program',
            'Year Level',
            'Scholarship Grant / Program',
            'GWA',
            'Evaluation Status',
            'Date Submitted',
            'Portal Format'
        ];

        $callback = function() use($query, $columns) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Microsoft Excel & institutional portal compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, $columns);

            // Chunk by 500 records to prevent memory exhaustion (HIGH-03)
            $query->chunk(500, function ($applications) use ($file) {
                foreach ($applications as $app) {
                    $row = [
                        'APP-' . $app->id,
                        $app->user->name ?? 'Unknown',
                        $app->user->profile?->clsu_id_number ?? 'N/A',
                        $app->user->profile?->course ?? 'N/A',
                        $app->user->profile?->year_level ?? 'N/A',
                        $app->program_name,
                        $app->gwa !== null ? number_format((float)$app->gwa, 2) : 'N/A',
                        $app->status,
                        $app->created_at->format('Y-m-d'),
                        'CHED/DOST Compliant'
                    ];
                    // SEC-03: Neutralize formula injection
                    $sanitizedRow = array_map(function ($val) {
                        if (is_string($val) && in_array(substr($val, 0, 1), ['=', '+', '-', '@', "\t", "\r"], true)) {
                            return "'" . $val;
                        }
                        return $val;
                    }, $row);
                    fputcsv($file, $sanitizedRow);
                }
            });

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // Generate and Download Approved Students CSV Report with Application Form Responses
    public function exportApprovedStudentsCsv(Request $request)
    {
        $this->logExportAccess($request, 'approved_students_list', 'csv');
        $query = $this->buildReportQuery($request)->where('status', 'Approved')->with(['user.profile', 'scholarship.fields', 'customFields']);
        $filename = "approved_scholars_responses_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Determine custom form field column headers
        if ($request->filled('scholarship_id')) {
            $customHeaders = \App\Models\ScholarshipField::where('scholarship_id', $request->scholarship_id)
                ->orderBy('id')
                ->pluck('field_label')
                ->unique()
                ->values()
                ->toArray();
        } else {
            // Collect distinct question labels across active applications
            $customHeaders = \App\Models\ApplicationField::whereHas('application', function ($q) {
                $q->where('status', 'Approved');
            })->distinct()->pluck('field_name')->values()->toArray();
        }

        // Requested base columns + academic details + dynamic application questionnaire responses
        $columns = array_merge([
            'id',
            'name',
            'course',
            'year level',
            'contact number',
            'academic status',
            'gwa',
            'scholarship program'
        ], $customHeaders);

        $callback = function() use($query, $columns, $customHeaders) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, $columns);

            $query->chunk(500, function ($applications) use ($file, $customHeaders) {
                foreach ($applications as $app) {
                    $idNumber = $app->user?->profile?->clsu_id_number ?? ('APP-' . $app->id);
                    $contactNumber = $app->user?->profile?->contact_number ?? 'N/A';
                    $academicStatus = $app->academic_status ?? $app->user?->profile?->academic_status ?? 'Regular';

                    $row = [
                        $idNumber,
                        $app->user?->name ?? 'Unknown',
                        $app->user?->profile?->course ?? 'N/A',
                        $app->user?->profile?->year_level ?? 'N/A',
                        $contactNumber,
                        $academicStatus,
                        $app->gwa !== null ? number_format((float)$app->gwa, 2) : 'N/A',
                        $app->program_name
                    ];

                    // Append applicant answers on the application form
                    foreach ($customHeaders as $header) {
                        $fieldMatch = $app->customFields->first(function ($cf) use ($header) {
                            return strcasecmp(trim($cf->field_name), trim($header)) === 0;
                        });

                        $answer = $fieldMatch ? (string) $fieldMatch->field_value : '';
                        if (str_starts_with($answer, 'documents/') || str_starts_with($answer, 'public/')) {
                            $answer = basename($answer);
                        }
                        $row[] = $answer !== '' ? $answer : 'N/A';
                    }

                    // Neutralize CSV formula injection
                    $sanitizedRow = array_map(function ($val) {
                        if (is_string($val) && in_array(substr($val, 0, 1), ['=', '+', '-', '@', "\t", "\r"], true)) {
                            return "'" . $val;
                        }
                        return $val;
                    }, $row);

                    fputcsv($file, $sanitizedRow);
                }
            });

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // Generate and Download PDF Report
    public function exportPdf(Request $request)
    {
        $this->logExportAccess($request, 'applications_list', 'pdf');
        $query = $this->buildReportQuery($request);

        // RES-02: Memory safety limit for DomPDF rendering
        $maxPdfRecords = 1000;
        $totalMatching = (clone $query)->count();
        if ($totalMatching > $maxPdfRecords) {
            return back()->with('warning', "PDF export is limited to {$maxPdfRecords} records to prevent server memory exhaustion. Please narrow your filters or export via CSV.");
        }

        $applications = $query->get();

        // Calculate statistics for the summary block
        $totalCount    = $applications->count();
        $approvedCount = $applications->where('status', 'Approved')->count();
        $rejectedCount = $applications->where('status', 'Rejected')->count();
        $pendingCount  = $applications->whereIn('status', ['Pending', 'Under Review'])->count();

        $totalFraud  = 0;
        $scoredCount = 0;
        foreach ($applications as $app) {
            if ($app->document && $app->document->aiResult) {
                $totalFraud += $app->document->aiResult->fraud_probability;
                $scoredCount++;
            }
        }
        $avgFraudScore = $scoredCount > 0 ? round($totalFraud / $scoredCount, 1) : 0;

        $stats = [
            'total'      => $totalCount,
            'approved'   => $approvedCount,
            'rejected'   => $rejectedCount,
            'pending'    => $pendingCount,
            'avg_fraud'  => $avgFraudScore,
        ];

        $pdf = Pdf::loadView('admin.report_pdf', compact('applications', 'stats'));
        return $pdf->download('aegis_official_report_' . date('Y-m-d') . '.pdf');
    }

    // ─── AUDIT LOG EXPORTS (Superadmin only) ──────────────────────────────────

    private function logExportAccess(Request $request, string $type, string $format)
    {
        \App\Services\AuditLoggerService::logExportAccess(
            auth()->id() ?? 1,
            $type,
            $request->filled('date_from') ? $request->date_from : null,
            $request->filled('date_to') ? $request->date_to : null,
            $format,
            $request->ip()
        );
    }

    private function buildAuditQuery(Request $request)
    {
        $query = StatusLog::with(['application.user', 'user'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function buildEmailLogQuery(Request $request)
    {
        $query = EmailLog::with(['application.user'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function buildAiScanQuery(Request $request)
    {
        $query = \App\Models\AIResult::with(['document.application.user.profile'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function buildEvaluationDecisionQuery(Request $request)
    {
        $query = StatusLog::with(['application.user.profile', 'application.scholarship', 'user'])
            ->whereIn('status', ['Approved', 'Rejected'])
            ->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function buildAuthLogQuery(Request $request)
    {
        $query = \App\Models\AuthLog::with(['user'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function buildAdminActionQuery(Request $request)
    {
        $query = \App\Models\AdminActionLog::with(['user'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function buildConfigChangeQuery(Request $request)
    {
        $query = \App\Models\ConfigChangeLog::with(['user'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function buildExportAccessLogQuery(Request $request)
    {
        $query = \App\Models\ExportAccessLog::with(['user'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function buildStudentTimelineQuery(Request $request)
    {
        $query = Application::with(['user.profile', 'statusLogs', 'emailLogs', 'document'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function buildDocumentUploadQuery(Request $request)
    {
        $query = \App\Models\Document::with(['application.user.profile', 'uploader'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    // ─── TIER 1 EXPORTS ────────────────────────────────────────────────────────

    public function exportAuditCsv(Request $request)
    {
        $this->logExportAccess($request, 'audit_log', 'csv');
        $logs     = $this->buildAuditQuery($request)->get();
        $filename = "aegis_audit_log_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $columns = ['Log ID', 'App Ref', 'Student Name', 'Status Changed To', 'Remarks', 'Changed By', 'Timestamp'];

        $callback = function () use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    'APP-' . ($log->application->id ?? 'N/A'),
                    $log->application?->user?->name ?? 'Unknown',
                    $log->status,
                    $log->remarks ?? '—',
                    $log->user?->name ?? 'System',
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportAuditPdf(Request $request)
    {
        $this->logExportAccess($request, 'audit_log', 'pdf');
        $logs      = $this->buildAuditQuery($request)->get();
        $dateRange = [
            'from' => $request->date_from ?? null,
            'to'   => $request->date_to   ?? null,
        ];

        $pdf = Pdf::loadView('superadmin.audit_log_pdf', compact('logs', 'dateRange'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('aegis_audit_log_' . date('Y-m-d') . '.pdf');
    }

    public function exportAiScanCsv(Request $request)
    {
        $this->logExportAccess($request, 'ai_scan_log', 'csv');
        $results = $this->buildAiScanQuery($request)->get();
        $filename = "aegis_ai_scan_log_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $columns = ['Scan ID', 'App Ref', 'Student Name', 'CLSU ID', 'Doc Type', 'Classification', 'Fraud Probability', 'Anomaly Flags', 'Timestamp'];

        $callback = function () use ($results, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($results as $res) {
                $flags = '';
                if (!empty($res->anomaly_indicators)) {
                    $flagsArray = is_string($res->anomaly_indicators) ? json_decode($res->anomaly_indicators, true) : $res->anomaly_indicators;
                    $flags = is_array($flagsArray) ? implode(', ', $flagsArray) : '';
                }

                fputcsv($file, [
                    $res->id,
                    'APP-' . ($res->document?->application_id ?? 'N/A'),
                    $res->document?->application?->user?->name ?? 'Unknown',
                    $res->document?->application?->user?->profile?->clsu_id_number ?? 'N/A',
                    $res->document?->document_type ?? 'N/A',
                    ucfirst($res->classification),
                    $res->fraud_probability . '%',
                    $flags ?: 'None',
                    $res->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportAiScanPdf(Request $request)
    {
        $this->logExportAccess($request, 'ai_scan_log', 'pdf');
        $results   = $this->buildAiScanQuery($request)->get();
        $dateRange = [
            'from' => $request->date_from ?? null,
            'to'   => $request->date_to   ?? null,
        ];

        $pdf = Pdf::loadView('superadmin.ai_scan_log_pdf', compact('results', 'dateRange'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('aegis_ai_scan_log_' . date('Y-m-d') . '.pdf');
    }

    public function exportEvaluationDecisionCsv(Request $request)
    {
        $this->logExportAccess($request, 'evaluation_log', 'csv');
        $logs = $this->buildEvaluationDecisionQuery($request)->get();
        $filename = "aegis_evaluation_decisions_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $columns = ['Log ID', 'App Ref', 'Student Name', 'CLSU ID', 'Program/Grant', 'GWA', 'Decision', 'Remarks', 'Evaluated By', 'Timestamp'];

        $callback = function () use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    'APP-' . ($log->application_id ?? 'N/A'),
                    $log->application?->user?->name ?? 'Unknown',
                    $log->application?->user?->profile?->clsu_id_number ?? 'N/A',
                    $log->application?->program_name ?? 'N/A',
                    $log->application?->gwa ?? 'N/A',
                    $log->status,
                    $log->remarks ?: 'None',
                    $log->user?->name ?? 'System',
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportEvaluationDecisionPdf(Request $request)
    {
        $this->logExportAccess($request, 'evaluation_log', 'pdf');
        $logs      = $this->buildEvaluationDecisionQuery($request)->get();
        $dateRange = [
            'from' => $request->date_from ?? null,
            'to'   => $request->date_to   ?? null,
        ];

        $pdf = Pdf::loadView('superadmin.evaluation_decision_log_pdf', compact('logs', 'dateRange'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('aegis_evaluation_decisions_' . date('Y-m-d') . '.pdf');
    }

    // ─── TIER 2 EXPORTS ────────────────────────────────────────────────────────

    public function exportAuthLogCsv(Request $request)
    {
        $this->logExportAccess($request, 'auth_log', 'csv');
        $logs = $this->buildAuthLogQuery($request)->get();
        $filename = "aegis_auth_log_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $columns = ['Log ID', 'User Name', 'Email Attempted', 'Event Type', 'IP Address', 'Browser Agent', 'Status', 'Timestamp'];

        $callback = function () use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'Guest',
                    $log->email_attempted ?: 'N/A',
                    $log->event_type,
                    $log->ip_address,
                    $log->user_agent,
                    $log->status,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportAuthLogPdf(Request $request)
    {
        $this->logExportAccess($request, 'auth_log', 'pdf');
        $logs      = $this->buildAuthLogQuery($request)->get();
        $dateRange = [
            'from' => $request->date_from ?? null,
            'to'   => $request->date_to   ?? null,
        ];

        $pdf = Pdf::loadView('superadmin.auth_log_pdf', compact('logs', 'dateRange'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('aegis_auth_log_' . date('Y-m-d') . '.pdf');
    }

    public function exportAdminActionCsv(Request $request)
    {
        $this->logExportAccess($request, 'admin_action_log', 'csv');
        $logs = $this->buildAdminActionQuery($request)->get();
        $filename = "aegis_admin_action_log_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $columns = ['Log ID', 'Admin Name', 'Action', 'Target Type', 'Target ID', 'Description', 'IP Address', 'Timestamp'];

        $callback = function () use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'System',
                    $log->action,
                    $log->target_type ?: 'System',
                    $log->target_id ?: 'N/A',
                    $log->description,
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportAdminActionPdf(Request $request)
    {
        $this->logExportAccess($request, 'admin_action_log', 'pdf');
        $logs      = $this->buildAdminActionQuery($request)->get();
        $dateRange = [
            'from' => $request->date_from ?? null,
            'to'   => $request->date_to   ?? null,
        ];

        $pdf = Pdf::loadView('superadmin.admin_action_log_pdf', compact('logs', 'dateRange'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('aegis_admin_action_log_' . date('Y-m-d') . '.pdf');
    }

    public function exportConfigChangeCsv(Request $request)
    {
        $this->logExportAccess($request, 'config_change_log', 'csv');
        $logs = $this->buildConfigChangeQuery($request)->get();
        $filename = "aegis_config_change_log_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $columns = ['Log ID', 'Changed By', 'Setting Key', 'Old Value', 'New Value', 'IP Address', 'Timestamp'];

        $callback = function () use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'System',
                    $log->setting_key,
                    $log->old_value ?: 'N/A',
                    $log->new_value ?: 'N/A',
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportConfigChangePdf(Request $request)
    {
        $this->logExportAccess($request, 'config_change_log', 'pdf');
        $logs      = $this->buildConfigChangeQuery($request)->get();
        $dateRange = [
            'from' => $request->date_from ?? null,
            'to'   => $request->date_to   ?? null,
        ];

        $pdf = Pdf::loadView('superadmin.config_change_log_pdf', compact('logs', 'dateRange'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('aegis_config_change_log_' . date('Y-m-d') . '.pdf');
    }

    // ─── TIER 3 EXPORTS ────────────────────────────────────────────────────────

    public function exportEmailLogCsv(Request $request)
    {
        $this->logExportAccess($request, 'email_log', 'csv');
        $logs     = $this->buildEmailLogQuery($request)->get();
        $filename = "aegis_email_log_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $columns = ['Log ID', 'App Ref', 'Student Name', 'Recipient Email', 'Subject', 'Status', 'Error Message', 'Timestamp'];

        $callback = function () use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    'APP-' . ($log->application->id ?? 'N/A'),
                    $log->application?->user?->name ?? 'Unknown',
                    $log->recipient,
                    $log->subject,
                    ucfirst($log->status ?? 'sent'),
                    $log->error_message ?: '—',
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportEmailLogPdf(Request $request)
    {
        $this->logExportAccess($request, 'email_log', 'pdf');
        $logs      = $this->buildEmailLogQuery($request)->get();
        $dateRange = [
            'from' => $request->date_from ?? null,
            'to'   => $request->date_to   ?? null,
        ];

        $pdf = Pdf::loadView('superadmin.email_log_pdf', compact('logs', 'dateRange'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('aegis_email_log_' . date('Y-m-d') . '.pdf');
    }

    public function exportScholarshipChangeCsv(Request $request)
    {
        $this->logExportAccess($request, 'scholarship_change_log', 'csv');
        $logs = $this->buildAdminActionQuery($request)->where('target_type', 'Scholarship')->get();
        $filename = "aegis_scholarship_changes_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $columns = ['Log ID', 'Admin Name', 'Action', 'Scholarship ID', 'Description', 'IP Address', 'Timestamp'];

        $callback = function () use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'System',
                    $log->action,
                    $log->target_id ?: 'N/A',
                    $log->description,
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportScholarshipChangePdf(Request $request)
    {
        $this->logExportAccess($request, 'scholarship_change_log', 'pdf');
        $logs = $this->buildAdminActionQuery($request)->where('target_type', 'Scholarship')->get();
        $dateRange = [
            'from' => $request->date_from ?? null,
            'to'   => $request->date_to   ?? null,
        ];

        $pdf = Pdf::loadView('superadmin.admin_action_log_pdf', compact('logs', 'dateRange'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('aegis_scholarship_changes_' . date('Y-m-d') . '.pdf');
    }

    public function exportExportAccessLogCsv(Request $request)
    {
        $this->logExportAccess($request, 'export_access_log', 'csv');
        $logs = $this->buildExportAccessLogQuery($request)->get();
        $filename = "aegis_export_access_log_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $columns = ['Log ID', 'Admin Name', 'Export Type', 'Filter From', 'Filter To', 'Format', 'IP Address', 'Timestamp'];

        $callback = function () use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'System',
                    $log->export_type,
                    $log->date_from ? $log->date_from->format('Y-m-d') : 'None',
                    $log->date_to ? $log->date_to->format('Y-m-d') : 'None',
                    strtoupper($log->format),
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportExportAccessLogPdf(Request $request)
    {
        $this->logExportAccess($request, 'export_access_log', 'pdf');
        $logs      = $this->buildExportAccessLogQuery($request)->get();
        $dateRange = [
            'from' => $request->date_from ?? null,
            'to'   => $request->date_to   ?? null,
        ];

        $pdf = Pdf::loadView('superadmin.export_access_log_pdf', compact('logs', 'dateRange'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('aegis_export_access_log_' . date('Y-m-d') . '.pdf');
    }

    // ─── TIER 4 EXPORTS ────────────────────────────────────────────────────────

    public function exportStudentTimelineCsv(Request $request)
    {
        $this->logExportAccess($request, 'student_timeline_log', 'csv');
        $applications = $this->buildStudentTimelineQuery($request)->get();
        $filename = "aegis_student_timeline_log_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $columns = ['Student Name', 'CLSU ID', 'App Ref', 'Program/Grant', 'Submitted At', 'Evaluated At', 'Status', 'Remarks'];

        $callback = function () use ($applications, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($applications as $app) {
                $evalDate = $app->statusLogs->whereIn('status', ['Approved', 'Rejected'])->first()?->created_at;
                fputcsv($file, [
                    $app->user->name ?? 'Unknown',
                    $app->user->profile?->clsu_id_number ?? 'N/A',
                    'APP-' . $app->id,
                    $app->program_name,
                    $app->created_at->format('Y-m-d H:i:s'),
                    $evalDate ? $evalDate->format('Y-m-d H:i:s') : 'Not yet evaluated',
                    $app->status,
                    $app->remarks ?: '—',
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportStudentTimelinePdf(Request $request)
    {
        $this->logExportAccess($request, 'student_timeline_log', 'pdf');
        $applications = $this->buildStudentTimelineQuery($request)->get();
        $dateRange = [
            'from' => $request->date_from ?? null,
            'to'   => $request->date_to   ?? null,
        ];

        $pdf = Pdf::loadView('superadmin.student_timeline_log_pdf', compact('applications', 'dateRange'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('aegis_student_timeline_log_' . date('Y-m-d') . '.pdf');
    }

    public function exportDocumentUploadCsv(Request $request)
    {
        $this->logExportAccess($request, 'doc_upload_log', 'csv');
        $docs = $this->buildDocumentUploadQuery($request)->get();
        $filename = "aegis_document_upload_log_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $columns = ['Doc ID', 'App Ref', 'Student Name', 'Original Filename', 'Doc Type', 'Upload Event', 'Uploaded By', 'Timestamp'];

        $callback = function () use ($docs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($docs as $doc) {
                fputcsv($file, [
                    $doc->id,
                    'APP-' . ($doc->application_id ?? 'N/A'),
                    $doc->application?->user?->name ?? 'Unknown',
                    $doc->original_name,
                    $doc->document_type,
                    ucfirst($doc->upload_event ?? 'initial'),
                    $doc->uploader?->name ?? 'Student',
                    $doc->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportDocumentUploadPdf(Request $request)
    {
        $this->logExportAccess($request, 'doc_upload_log', 'pdf');
        $docs      = $this->buildDocumentUploadQuery($request)->get();
        $dateRange = [
            'from' => $request->date_from ?? null,
            'to'   => $request->date_to   ?? null,
        ];

        $pdf = Pdf::loadView('superadmin.doc_upload_log_pdf', compact('docs', 'dateRange'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('aegis_document_upload_log_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Generate and Download 1-Page Official Forensic Audit PDF Certificate
     */
    public function exportForensicReport(Request $request, $id, $docId = null)
    {
        $application = Application::with(['user.profile', 'documents.aiResult', 'scholarship', 'academicTerm'])->findOrFail($id);
        
        $document = $docId 
            ? $application->documents->firstWhere('id', $docId)
            : $application->documents->first();

        if (!$document || !$document->aiResult) {
            return back()->with('error', 'No completed AI forensic analysis found for this document.');
        }

        $aiResult = $document->aiResult;
        $reportData = [
            'application' => $application,
            'document' => $document,
            'aiResult' => $aiResult,
            'generatedAt' => now()->format('F d, Y h:i A'),
            'verifier' => auth()->user()->name ?? 'OSA Administrator',
            'auditId' => 'AEGIS-FA-' . strtoupper(substr(md5($application->id . $document->id . $aiResult->created_at), 0, 10))
        ];

        $pdf = Pdf::loadView('reports.forensic_certificate', $reportData)
                  ->setPaper('a4', 'portrait');

        return $pdf->download("Forensic_Audit_Report_APP-{$application->id}_{$document->document_type}.pdf");
    }
}