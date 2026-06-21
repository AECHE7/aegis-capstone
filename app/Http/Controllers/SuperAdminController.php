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

        // Pass the new variable name
        return view('superadmin.analytics', compact(
            'totalStudents', 
            'submissionCount', 
            'totalScholarships', 
            'anomaliesDetected', 
            'recentEvaluations',
            'avgFraudScore'
        ));
    }
}