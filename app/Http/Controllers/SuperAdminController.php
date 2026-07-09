<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scholarship;

class SuperAdminController extends Controller
{
    // 1. Load the Manager Page
    public function index()
    {
        $scholarships = Scholarship::latest()->get();
        return view('superadmin.scholarships', compact('scholarships'));
    }

    // 2. Save a New Scholarship
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_gwa_required' => 'nullable|numeric|min:1.00|max:5.00',
            'deadline' => 'nullable|date',
            'max_renewals' => 'nullable|integer|min:1|max:12',
            'fields' => 'nullable|array',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,number,textarea,select,file,date,email',
            'fields.*.required' => 'nullable',
            'fields.*.options' => 'nullable|string',
        ]);

        $scholarship = Scholarship::create([
            'name' => $request->name,
            'description' => $request->description,
            'min_gwa_required' => $request->min_gwa_required,
            'deadline' => $request->deadline,
            'max_renewals' => $request->max_renewals ?? 4,
            'status' => 'Active'
        ]);

        if ($request->has('fields')) {
            foreach ($request->fields as $field) {
                // Generate a field_name from the label (slug or snake_case)
                $fieldName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', trim($field['label'])));
                // Ensure unique within this scholarship
                $fieldName = uniqid($fieldName . '_');

                $optionsArray = null;
                if ($field['type'] === 'select' && !empty($field['options'])) {
                    $optionsArray = array_map('trim', explode(',', $field['options']));
                }

                $scholarship->fields()->create([
                    'field_name' => $fieldName,
                    'field_label' => $field['label'],
                    'field_type' => $field['type'],
                    'is_required' => isset($field['required']) && ($field['required'] == '1' || $field['required'] == 'on' || $field['required'] == true) ? true : false,
                    'options' => $optionsArray
                ]);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'New Scholarship Program and its custom fields successfully created!',
                'scholarship' => $scholarship
            ]);
        }

        return back()->with('success', 'New Scholarship Program and its custom fields successfully created!');
    }

    // Retrieve Scholarship Details for Edit Modal
    public function show($id)
    {
        $scholarship = Scholarship::with('fields')->findOrFail($id);
        return response()->json([
            'success' => true,
            'scholarship' => $scholarship
        ]);
    }

    // Save Scholarship Updates
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_gwa_required' => 'nullable|numeric|min:1.00|max:5.00',
            'deadline' => 'nullable|date',
            'max_renewals' => 'nullable|integer|min:1|max:12',
            'fields' => 'nullable|array',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,number,textarea,select,file,date,email',
            'fields.*.required' => 'nullable',
            'fields.*.options' => 'nullable|string',
        ]);

        $scholarship = Scholarship::findOrFail($id);
        $scholarship->update([
            'name' => $request->name,
            'description' => $request->description,
            'min_gwa_required' => $request->min_gwa_required,
            'deadline' => $request->deadline,
            'max_renewals' => $request->max_renewals ?? 4,
        ]);

        // Wipe and rebuild fields
        $scholarship->fields()->delete();

        if ($request->has('fields')) {
            foreach ($request->fields as $field) {
                // Generate field_name
                $fieldName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', trim($field['label'])));
                $fieldName = uniqid($fieldName . '_');

                $optionsArray = null;
                if ($field['type'] === 'select' && !empty($field['options'])) {
                    if (is_array($field['options'])) {
                        $optionsArray = $field['options'];
                    } else {
                        $optionsArray = array_map('trim', explode(',', $field['options']));
                    }
                }

                $scholarship->fields()->create([
                    'field_name' => $fieldName,
                    'field_label' => $field['label'],
                    'field_type' => $field['type'],
                    'is_required' => isset($field['required']) && ($field['required'] == '1' || $field['required'] == 'on' || $field['required'] == true) ? true : false,
                    'options' => $optionsArray
                ]);
            }
        }

        // Bust the active scholarships list cache
        \Illuminate\Support\Facades\Cache::forget('active_scholarships_list');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Scholarship Program and its custom fields successfully updated!',
                'scholarship' => $scholarship->load('fields')
            ]);
        }

        return back()->with('success', 'Scholarship Program and its custom fields successfully updated!');
    }

    // 3. Toggle Status (Active/Closed)
    public function toggleStatus($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        // Flip the status
        $scholarship->status = $scholarship->status === 'Active' ? 'Closed' : 'Active';
        $scholarship->save();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $scholarship->name . ' is now ' . $scholarship->status . '.',
                'status' => $scholarship->status
            ]);
        }

        return back()->with('success', $scholarship->name . ' is now ' . $scholarship->status . '.');
    }

    // ==========================================
    // SUPER ADMIN: SYSTEM ANALYTICS & AUDIT LOG
    // ==========================================
    public function analytics()
    {
        $termId = request('academic_term_id');
        $scholarshipId = request('scholarship_id');

        // Scoped Application Query
        $query = \App\Models\Application::query();
        if ($termId) {
            $query->where('academic_term_id', $termId);
        }
        if ($scholarshipId) {
            $query->where('scholarship_id', $scholarshipId);
        }

        // 1. High-Level System Metrics (Scoped)
        $studentQuery = \App\Models\User::where('role', 'student');
        if ($termId || $scholarshipId) {
            $studentQuery->whereHas('applications', function ($q) use ($termId, $scholarshipId) {
                if ($termId) $q->where('academic_term_id', $termId);
                if ($scholarshipId) $q->where('scholarship_id', $scholarshipId);
            });
        }
        $totalStudents = $studentQuery->count();
        $submissionCount = (clone $query)->count();
        $totalScholarships = \App\Models\Scholarship::count();
        $anomaliesDetected = (clone $query)->where('status', 'Rejected')->count();

        // 2. The Audit Trail (Scoped)
        $recentEvaluations = (clone $query)->with(['user'])
            ->whereNotNull('evaluated_by')
            ->whereIn('status', ['Approved', 'Rejected'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        // 3. Average Fraud Score (Scoped)
        $avgFraudQuery = \App\Models\AIResult::query();
        if ($termId || $scholarshipId) {
            $avgFraudQuery->whereHas('document.application', function ($q) use ($termId, $scholarshipId) {
                if ($termId) $q->where('academic_term_id', $termId);
                if ($scholarshipId) $q->where('scholarship_id', $scholarshipId);
            });
        }
        $avgFraudScore = $avgFraudQuery->avg('fraud_probability') ?? 0;
        $avgFraudScore = round($avgFraudScore, 1);

        // 4. Grade Integrity Index (Scoped)
        $approvedFraudQuery = \App\Models\AIResult::whereHas('document.application', function ($q) use ($termId, $scholarshipId) {
            $q->where('status', 'Approved');
            if ($termId) $q->where('academic_term_id', $termId);
            if ($scholarshipId) $q->where('scholarship_id', $scholarshipId);
        });
        $avgApprovedFraud = $approvedFraudQuery->avg('fraud_probability') ?? 0;
        $gradeIntegrityIndex = round(100 - $avgApprovedFraud, 1);

        // 5. Average Evaluation Cycle Time in Days (Scoped)
        $cycleTimeQuery = (clone $query)->whereIn('status', ['Approved', 'Rejected']);
        $averageCycleDays = 0;
        if (config('database.default') === 'sqlite') {
            $averageCycleDays = $cycleTimeQuery->selectRaw('avg(julianday(updated_at) - julianday(created_at)) as avg_days')->value('avg_days') ?? 0;
        } else {
            $averageCycleDays = $cycleTimeQuery->selectRaw('avg(extract(epoch from (updated_at - created_at)) / 86400) as avg_days')->value('avg_days') ?? 0;
        }
        $averageCycleDays = round((float)$averageCycleDays, 1);

        // 6. GWA Compliance Rate (Scoped)
        $totalEvaluated = (clone $query)->whereIn('status', ['Approved', 'Rejected'])->count();
        $compliantCount = (clone $query)->where('status', 'Approved')->count();
        $complianceRate = $totalEvaluated > 0 ? round(($compliantCount / $totalEvaluated) * 100, 1) : 0;

        // 7. Aggregate UAT Evaluator Feedbacks (ISO/IEC 25010)
        $uatCount = \App\Models\UatFeedback::count();
        
        $avgFs = \App\Models\UatFeedback::avg('functional_suitability') ?? 0;
        $avgUs = \App\Models\UatFeedback::avg('usability') ?? 0;
        $avgRl = \App\Models\UatFeedback::avg('reliability') ?? 0;
        $avgSc = \App\Models\UatFeedback::avg('security') ?? 0;

        // Round all values to 2 decimal places
        $avgFs = round($avgFs, 2);
        $avgUs = round($avgUs, 2);
        $avgRl = round($avgRl, 2);
        $avgSc = round($avgSc, 2);

        $overallMean = $uatCount > 0 ? round(($avgFs + $avgUs + $avgRl + $avgSc) / 4, 2) : 0;

        $uatStats = [
            'count' => $uatCount,
            'avg_fs' => $avgFs,
            'avg_us' => $avgUs,
            'avg_rl' => $avgRl,
            'avg_sc' => $avgSc,
            'overall_mean' => $overallMean
        ];

        // 8. Chart Data: Application Status Distribution (Scoped)
        $statusCounts = [
            'Pending'      => (clone $query)->where('status', 'Pending')->count(),
            'Under Review' => (clone $query)->where('status', 'Under Review')->count(),
            'Approved'     => (clone $query)->where('status', 'Approved')->count(),
            'Rejected'     => (clone $query)->where('status', 'Rejected')->count(),
        ];

        // 9. Chart Data: Fraud Risk Tier Distribution (Scoped)
        $lowRiskQuery = \App\Models\AIResult::where('fraud_probability', '<', 40);
        $moderateRiskQuery = \App\Models\AIResult::whereBetween('fraud_probability', [40, 69.99]);
        $highRiskQuery = \App\Models\AIResult::where('fraud_probability', '>=', 70);

        if ($termId || $scholarshipId) {
            $scopeFilter = function ($q) use ($termId, $scholarshipId) {
                if ($termId) $q->where('academic_term_id', $termId);
                if ($scholarshipId) $q->where('scholarship_id', $scholarshipId);
            };
            $lowRiskQuery->whereHas('document.application', $scopeFilter);
            $moderateRiskQuery->whereHas('document.application', $scopeFilter);
            $highRiskQuery->whereHas('document.application', $scopeFilter);
        }

        $riskTiers = [
            'Low Risk (0-39%)'       => $lowRiskQuery->count(),
            'Moderate Risk (40-69%)' => $moderateRiskQuery->count(),
            'High Risk (70-100%)'    => $highRiskQuery->count(),
        ];

        // 10. Chart Data: Monthly Application Trend & Processing Speed (Scoped)
        $monthlyTrend = [];
        $monthlyProcessingDays = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $label = $date->format('M Y');
            $monthlyTrend[$label] = (clone $query)->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $monthQuery = (clone $query)->whereIn('status', ['Approved', 'Rejected'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if (config('database.default') === 'sqlite') {
                $avgDays = $monthQuery->selectRaw('avg(julianday(updated_at) - julianday(created_at)) as avg_days')->value('avg_days') ?? 0;
            } else {
                $avgDays = $monthQuery->selectRaw('avg(extract(epoch from (updated_at - created_at)) / 86400) as avg_days')->value('avg_days') ?? 0;
            }
            $monthlyProcessingDays[$label] = round((float)$avgDays, 1);
        }

        // 11. Chart Data: College & Course Distribution (Scoped)
        $collegeStats = (clone $query)
            ->join('student_profiles', 'applications.user_id', '=', 'student_profiles.user_id')
            ->selectRaw('student_profiles.college, count(applications.id) as app_count')
            ->groupBy('student_profiles.college')
            ->get()
            ->pluck('app_count', 'college')
            ->toArray();

        // 12. Chart Data: GWA Distribution Density (Scoped)
        $gwaBrackets = [
            'excellent' => ['min' => 1.00, 'max' => 1.25],
            'very_good' => ['min' => 1.26, 'max' => 1.50],
            'good'      => ['min' => 1.51, 'max' => 1.75],
            'satisfactory'=>['min' => 1.76, 'max' => 2.00],
            'others'    => ['min' => 2.01, 'max' => 5.00]
        ];

        $applicantGwaCounts = [];
        $approvedGwaCounts = [];
        foreach ($gwaBrackets as $key => $range) {
            $applicantGwaCounts[$key] = (clone $query)->whereBetween('gwa', [$range['min'], $range['max']])->count();
            $approvedGwaCounts[$key] = (clone $query)->where('status', 'Approved')->whereBetween('gwa', [$range['min'], $range['max']])->count();
        }

        // 13. Chart Data: AI Anomaly Indicator Frequencies (Scoped)
        $anomalyResults = \App\Models\AIResult::whereHas('document.application', function ($q) use ($termId, $scholarshipId) {
                if ($termId) $q->where('academic_term_id', $termId);
                if ($scholarshipId) $q->where('scholarship_id', $scholarshipId);
            })
            ->whereNotNull('anomaly_indicators')
            ->pluck('anomaly_indicators');

        $anomalyCounts = [];
        foreach ($anomalyResults as $indicators) {
            $array = is_string($indicators) ? json_decode($indicators, true) : $indicators;
            if (is_array($array)) {
                foreach ($array as $indicator) {
                    $anomalyCounts[$indicator] = ($anomalyCounts[$indicator] ?? 0) + 1;
                }
            }
        }
        arsort($anomalyCounts);
        $anomalyCounts = array_slice($anomalyCounts, 0, 5, true);

        // 14. Top Performing Programs — sorted by highest avg approved GWA (Scoped)
        $topPrograms = (clone $query)
            ->selectRaw('scholarship_id, program_name, count(*) as total_apps, avg(gwa) as avg_gwa')
            ->where('status', 'Approved')
            ->whereNotNull('gwa')
            ->groupBy('scholarship_id', 'program_name')
            ->orderBy('avg_gwa', 'asc')
            ->take(5)
            ->get();

        // 15. Process Audit Timeline — stage transition counts (Scoped)
        $processTimeline = [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'Pending')->count(),
            'under_review' => (clone $query)->where('status', 'Under Review')->count(),
            'approved' => (clone $query)->where('status', 'Approved')->count(),
            'rejected' => (clone $query)->where('status', 'Rejected')->count(),
        ];

        // 14. Per-Scholarship Program Breakdown Stats (Scoped)
        $scholarshipsBreakdown = \App\Models\Scholarship::with(['applications.documents.aiResult'])->get()->map(function($scholarship) {
            $apps = $scholarship->applications;
            
            $approvedApps = $apps->where('status', 'Approved');
            $avgGwaApproved = $approvedApps->avg('gwa') ?? 0;
            
            $fraudScores = [];
            foreach ($apps as $app) {
                foreach ($app->documents as $doc) {
                    if ($doc->aiResult && $doc->aiResult->classification !== 'scanning') {
                        $fraudScores[] = $doc->aiResult->fraud_probability;
                    }
                }
            }
            $avgFraud = count($fraudScores) > 0 ? (array_sum($fraudScores) / count($fraudScores)) : 0;

            return [
                'name' => $scholarship->name,
                'status' => $scholarship->status,
                'min_gwa' => $scholarship->min_gwa_required,
                'max_renew' => $scholarship->max_renewals ?? 4,
                'total_apps' => $apps->count(),
                'approved_count' => $approvedApps->count(),
                'rejected_count' => $apps->where('status', 'Rejected')->count(),
                'pending_count' => $apps->whereIn('status', ['Pending', 'Under Review'])->count(),
                'avg_gwa_approved' => round($avgGwaApproved, 2),
                'avg_fraud' => round($avgFraud, 1)
            ];
        });

        // Fetch active scholars system-wide for monitoring
        $activeScholarsQuery = \App\Models\Application::with(['user.profile', 'scholarship', 'academicTerm'])
            ->where('status', 'Approved');
        if ($termId) {
            $activeScholarsQuery->where('academic_term_id', $termId);
        }
        if ($scholarshipId) {
            $activeScholarsQuery->where('scholarship_id', $scholarshipId);
        }
        $activeScholars = $activeScholarsQuery->latest('updated_at')->get();

        // Dropdowns for Filter Panel
        $allTerms = \App\Models\AcademicTerm::orderBy('academic_year', 'desc')->orderBy('semester', 'desc')->get();
        $allScholarships = \App\Models\Scholarship::orderBy('name', 'asc')->get();

        // Pass the variables to dashboard
        return view('superadmin.analytics', compact(
            'totalStudents', 
            'submissionCount', 
            'totalScholarships', 
            'anomaliesDetected', 
            'recentEvaluations',
            'avgFraudScore',
            'gradeIntegrityIndex',
            'averageCycleDays',
            'complianceRate',
            'uatStats',
            'statusCounts',
            'riskTiers',
            'monthlyTrend',
            'monthlyProcessingDays',
            'topPrograms',
            'processTimeline',
            'collegeStats',
            'applicantGwaCounts',
            'approvedGwaCounts',
            'anomalyCounts',
            'scholarshipsBreakdown',
            'activeScholars',
            'allTerms',
            'allScholarships',
            'termId',
            'scholarshipId'
        ));
    }

    // 6. List all staff (Admin role)
    public function listStaff()
    {
        $staffList = \App\Models\User::where('role', 'admin')
            ->with(['invitation', 'scholarships'])
            ->latest()
            ->get();

        $scholarships = \Illuminate\Support\Facades\Cache::remember('active_scholarships_list', 3600, function () {
            return \App\Models\Scholarship::where('status', 'Active')->get();
        });

        return view('superadmin.staff', compact('staffList', 'scholarships'));
    }

    // 7. Invite a new staff member
    public function inviteStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) {
                    $domain = substr(strrchr($value, "@"), 1);
                    if (!in_array($domain, ['clsu.edu.ph', 'clsu2.edu.ph'])) {
                        $fail('Staff email must be a CLSU institutional email (@clsu.edu.ph or @clsu2.edu.ph).');
                    }
                }
            ],
            'scholarship_ids' => 'nullable|array',
            'scholarship_ids.*' => 'exists:scholarships,id',
        ]);

        // Create the user without a password (set random placeholder)
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
            'role' => 'admin',
            'email_verified_at' => null, // must set password first to activate
            'is_active' => true,
        ]);

        // Sync scholarship assignments
        if ($request->has('scholarship_ids')) {
            $user->scholarships()->sync($request->scholarship_ids);
        }

        // Generate invitation token
        $token = \Illuminate\Support\Str::random(40);

        // Store invitation
        \App\Models\UserInvitation::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addDays(3),
        ]);

        // Send invitation notification
        try {
            $user->notify(new \App\Notifications\StaffInvitationNotification($token));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send staff invitation email to {$user->email}: " . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Staff account created and assigned successfully, but the invitation email could not be delivered. Please verify your SMTP config.',
                    'staff' => $user->load('scholarships'),
                    'warning' => true
                ]);
            }
            return back()->with('warning', 'Staff account created and assigned successfully, but the invitation email could not be delivered. Please verify your SMTP settings.');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Staff member successfully invited and assigned! An activation link has been sent to their email.',
                'staff' => $user->load('scholarships')
            ]);
        }

        return back()->with('success', 'Staff member successfully invited and assigned! An activation link has been sent to their email.');
    }

    // 8. Revoke staff access (deactivate)
    public function revokeStaff($id)
    {
        $staff = \App\Models\User::where('role', 'admin')->findOrFail($id);
        $staff->is_active = false;
        $staff->save();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Staff access has been revoked successfully.',
                'is_active' => false
            ]);
        }

        return back()->with('success', 'Staff access has been revoked successfully.');
    }

    // 9. Reactivate staff access
    public function reactivateStaff($id)
    {
        $staff = \App\Models\User::where('role', 'admin')->findOrFail($id);
        $staff->is_active = true;
        $staff->save();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Staff access has been reactivated successfully.',
                'is_active' => true
            ]);
        }

        return back()->with('success', 'Staff access has been reactivated successfully.');
    }

    // 10. Update staff scholarship assignments
    public function updateStaffAssignments(Request $request, $id)
    {
        $staff = \App\Models\User::where('role', 'admin')->findOrFail($id);
        
        $request->validate([
            'scholarship_ids' => 'nullable|array',
            'scholarship_ids.*' => 'exists:scholarships,id',
        ]);

        $staff->scholarships()->sync($request->scholarship_ids ?? []);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Staff scholarship assignments updated successfully.',
                'scholarships' => $staff->scholarships()->get()
            ]);
        }

        return back()->with('success', 'Staff scholarship assignments updated successfully.');
    }

    // ==========================================
    // DIRECTOR (SUPER ADMIN) TRASH MANAGEMENT
    // ==========================================

    public function trashIndex()
    {
        $applications = \App\Models\Application::onlyTrashed()->with('user.profile')->latest()->get();
        $scholarships = \App\Models\Scholarship::onlyTrashed()->latest()->get();
        $staffMembers = \App\Models\User::onlyTrashed()->where('role', 'admin')->latest()->get();

        return view('superadmin.trash', compact('applications', 'scholarships', 'staffMembers'));
    }

    // 1. Applications Trashed Actions
    public function restoreApplication($id)
    {
        $application = \App\Models\Application::onlyTrashed()->findOrFail($id);
        $application->restore();

        \App\Models\StatusLog::create([
            'application_id' => $application->id,
            'status' => $application->status,
            'remarks' => 'Application restored from Trash by Director.',
            'changed_by' => auth()->id() ?? 1
        ]);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Application restored successfully.']);
        }
        return back()->with('success', 'Application restored successfully.');
    }

    public function forceDeleteApplication($id)
    {
        $application = \App\Models\Application::onlyTrashed()->findOrFail($id);

        // Delete COG document file
        if ($application->document) {
            $filePath = $application->document->file_path;
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($filePath)) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($filePath);
            }
            $application->document->forceDelete();
        }

        // Delete custom fields file uploads
        if ($application->customFields) {
            foreach ($application->customFields as $field) {
                if (\Illuminate\Support\Str::startsWith($field->field_value, 'uploads/')) {
                    if (\Illuminate\Support\Facades\Storage::disk('local')->exists($field->field_value)) {
                        \Illuminate\Support\Facades\Storage::disk('local')->delete($field->field_value);
                    }
                }
                $field->delete();
            }
        }

        // Cascade delete logs
        $application->statusLogs()->delete();
        $application->emailLogs()->delete();

        // Permanently delete application
        $application->forceDelete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Application permanently deleted.']);
        }
        return back()->with('success', 'Application permanently deleted.');
    }

    // 2. Scholarships Trashed Actions
    public function deleteScholarship($id)
    {
        $scholarship = \App\Models\Scholarship::findOrFail($id);
        $scholarship->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Scholarship program soft-deleted successfully.']);
        }
        return back()->with('success', 'Scholarship program soft-deleted successfully.');
    }

    public function restoreScholarship($id)
    {
        $scholarship = \App\Models\Scholarship::onlyTrashed()->findOrFail($id);
        $scholarship->restore();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Scholarship program restored successfully.']);
        }
        return back()->with('success', 'Scholarship program restored successfully.');
    }

    public function forceDeleteScholarship($id)
    {
        $scholarship = \App\Models\Scholarship::onlyTrashed()->findOrFail($id);

        // Check if there are any applications referencing it (even soft deleted ones!)
        $appCount = \App\Models\Application::withTrashed()->where('scholarship_id', $scholarship->id)->count();
        if ($appCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Action Blocked: This scholarship program cannot be permanently deleted because it has ' . $appCount . ' associated student applications. You must permanently delete the applications first or keep this scholarship soft-deleted for historical records.'
            ], 422);
        }

        // Permanently delete custom fields configuration
        $scholarship->fields()->delete();

        // Dissociate staff pivot
        $scholarship->staff()->detach();

        // Permanently delete
        $scholarship->forceDelete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Scholarship program permanently deleted.']);
        }
        return back()->with('success', 'Scholarship program permanently deleted.');
    }

    // 3. Staff Trashed Actions
    public function deleteStaff($id)
    {
        $staff = \App\Models\User::where('role', 'admin')->findOrFail($id);
        $staff->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Staff member account soft-deleted successfully.']);
        }
        return back()->with('success', 'Staff member account soft-deleted successfully.');
    }

    public function restoreStaff($id)
    {
        $staff = \App\Models\User::onlyTrashed()->where('role', 'admin')->findOrFail($id);
        $staff->restore();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Staff member account restored successfully.']);
        }
        return back()->with('success', 'Staff member account restored successfully.');
    }

    public function forceDeleteStaff($id)
    {
        $staff = \App\Models\User::onlyTrashed()->where('role', 'admin')->findOrFail($id);

        // Dissociate assigned scholarships
        $staff->scholarships()->detach();

        // Delete invitation link if any
        if ($staff->invitation) {
            $staff->invitation->delete();
        }

        // Permanently delete
        $staff->forceDelete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Staff member account permanently deleted.']);
        }
        return back()->with('success', 'Staff member account permanently deleted.');
    }

    public function settings()
    {
        $settings = [
            'app_name' => \App\Models\Setting::get('app_name', 'A.E.G.I.S.'),
            'university_name' => \App\Models\Setting::get('university_name', 'Central Luzon State University'),
            'ai_fraud_threshold' => \App\Models\Setting::get('ai_fraud_threshold', '50.0'),
            'gwa_discrepancy_tolerance' => \App\Models\Setting::get('gwa_discrepancy_tolerance', '0.01'),
            'app_logo' => \App\Models\Setting::get('app_logo'),
            'mfa_enforcement' => \App\Models\Setting::get('mfa_enforcement', 'all'),
            'total_budget' => \App\Models\Setting::get('total_budget', '5000000'),
        ];
        return view('superadmin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'university_name' => 'required|string|max:255',
            'ai_fraud_threshold' => 'required|numeric|min:0|max:100',
            'gwa_discrepancy_tolerance' => 'required|numeric|min:0|max:5',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'mfa_enforcement' => 'required|in:all,students,none',
            'total_budget' => 'required|numeric|min:0',
        ]);

        \App\Models\Setting::set('app_name', $request->app_name);
        \App\Models\Setting::set('university_name', $request->university_name);
        \App\Models\Setting::set('ai_fraud_threshold', $request->ai_fraud_threshold);
        \App\Models\Setting::set('gwa_discrepancy_tolerance', $request->gwa_discrepancy_tolerance);
        \App\Models\Setting::set('mfa_enforcement', $request->mfa_enforcement);
        \App\Models\Setting::set('total_budget', $request->total_budget);

        if ($request->boolean('reset_logo')) {
            \App\Models\Setting::set('app_logo', null);
        } elseif ($request->hasFile('app_logo')) {
            $logoPath = \App\Services\CloudStorageService::upload($request->file('app_logo'));
            \App\Models\Setting::set('app_logo', $logoPath);
        }

        return back()->with('success', 'System settings updated successfully.');
    }

    public function revokeAllDevices()
    {
        \App\Models\UserMfaDevice::truncate();
        return back()->with('success', 'All trusted devices system-wide have been successfully revoked.');
    }

    public function showBroadcast()
    {
        $scholarships = \App\Models\Scholarship::latest()->get();
        $broadcasts = \App\Models\EmailLog::where('subject', 'like', '[A.E.G.I.S. Broadcast]%')
            ->latest()
            ->paginate(10);
        return view('superadmin.broadcast', compact('scholarships', 'broadcasts'));
    }

    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'target' => 'required|string',
        ]);

        \App\Jobs\BroadcastAnnouncementEmailJob::dispatch($request->title, $request->body, $request->target);

        return back()->with('success', 'Email broadcast has been queued for delivery.');
    }
}