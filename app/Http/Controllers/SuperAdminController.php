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
            'min_gwa_required' => 'required|numeric|min:1.00|max:5.00',
            'deadline' => 'nullable|date'
        ]);

        Scholarship::create([
            'name' => $request->name,
            'description' => $request->description,
            'min_gwa_required' => $request->min_gwa_required,
            'deadline' => $request->deadline,
            'status' => 'Active'
        ]);

        return back()->with('success', 'New Scholarship Program successfully created!');
    }

    // 3. Toggle Status (Active/Closed)
    public function toggleStatus($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        // Flip the status
        $scholarship->status = $scholarship->status === 'Active' ? 'Closed' : 'Active';
        $scholarship->save();

        return back()->with('success', $scholarship->name . ' is now ' . $scholarship->status . '.');
    }

    // ==========================================
    // SUPER ADMIN: SYSTEM ANALYTICS & AUDIT LOG
    // ==========================================
    public function analytics()
    {
        // 1. High-Level System Metrics
        $totalStudents = \App\Models\User::where('role', 'student')->count();
        
        // RENAMED VARIABLE TO FORCE A CACHE REFRESH
        $submissionCount = \App\Models\Application::count(); 
        
        $totalScholarships = \App\Models\Scholarship::count();
        $anomaliesDetected = \App\Models\Application::where('status', 'Rejected')->count();

        // 2. The Audit Trail
        $recentEvaluations = \App\Models\Application::with(['user'])
            ->whereNotNull('evaluated_by')
            ->whereIn('status', ['Approved', 'Rejected'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        // 3. Average Fraud Score
        $avgFraudScore = \App\Models\AIResult::avg('fraud_probability') ?? 0;
        $avgFraudScore = round($avgFraudScore, 1);

        // 4. Aggregate UAT Evaluator Feedbacks (ISO/IEC 25010)
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

        // 5. Chart Data: Application Status Distribution
        $statusCounts = [
            'Pending'      => \App\Models\Application::where('status', 'Pending')->count(),
            'Under Review' => \App\Models\Application::where('status', 'Under Review')->count(),
            'Approved'     => \App\Models\Application::where('status', 'Approved')->count(),
            'Rejected'     => \App\Models\Application::where('status', 'Rejected')->count(),
        ];

        // 6. Chart Data: Fraud Risk Tier Distribution (based on fraud_probability)
        $lowRisk      = \App\Models\AIResult::where('fraud_probability', '<', 40)->count();
        $moderateRisk = \App\Models\AIResult::whereBetween('fraud_probability', [40, 69.99])->count();
        $highRisk     = \App\Models\AIResult::where('fraud_probability', '>=', 70)->count();
        $riskTiers = [
            'Low Risk (0-39%)'       => $lowRisk,
            'Moderate Risk (40-69%)' => $moderateRisk,
            'High Risk (70-100%)'    => $highRisk,
        ];

        // 7. Chart Data: Monthly Application Trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyTrend[$date->format('M Y')] = \App\Models\Application::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Pass the new variables to dashboard
        return view('superadmin.analytics', compact(
            'totalStudents', 
            'submissionCount', 
            'totalScholarships', 
            'anomaliesDetected', 
            'recentEvaluations',
            'avgFraudScore',
            'uatStats',
            'statusCounts',
            'riskTiers',
            'monthlyTrend'
        ));
    }

    // 6. List all staff (Admin role)
    public function listStaff()
    {
        $staffList = \App\Models\User::where('role', 'admin')
            ->with('invitation')
            ->latest()
            ->get();

        return view('superadmin.staff', compact('staffList'));
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
        ]);

        // Create the user without a password (set random placeholder)
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
            'role' => 'admin',
            'email_verified_at' => null, // must set password first to activate
        ]);

        // Generate invitation token
        $token = \Illuminate\Support\Str::random(40);

        // Store invitation
        \App\Models\UserInvitation::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addDays(3),
        ]);

        // Send invitation notification
        $user->notify(new \App\Notifications\StaffInvitationNotification($token));

        return back()->with('success', 'Staff member successfully invited! An activation link has been sent to their email.');
    }
}