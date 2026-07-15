<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreApplicationRequest;

class ApplicationController extends Controller
{
    // 1. Load the Student Dashboard (Pizza Tracker)
    // Change the name from index() to dashboard() right here!
    public function dashboard()
    {
        $userId = auth()->id(); // auth middleware guarantees non-null (CRIT-05)
        $application = Application::with(['document.aiResult', 'customFields', 'academicTerm', 'statusLogs' => function($q) {
            $q->orderBy('created_at', 'asc');
        }])
            ->where('user_id', $userId)
            ->latest()
            ->first();

        $cancelledApplications = Application::onlyTrashed()
            ->with(['academicTerm'])
            ->where('user_id', $userId)
            ->orderBy('deleted_at', 'desc')
            ->get();

        $announcements = \App\Models\Announcement::with('author')
            ->where(function($q) {
                $q->whereNull('scheduled_publish_at')
                  ->orWhere('scheduled_publish_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('scheduled_delete_at')
                  ->orWhere('scheduled_delete_at', '>', now());
            })
            ->latest()
            ->take(3)
            ->get();

        return view('student.dashboard', compact('application', 'cancelledApplications', 'announcements'));
    }

    // 1. Load the Application Form
    public function create()
    {
        if (auth()->user()->hasActiveApplication()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Action Denied: You already have an active application or scholarship for this academic term.');
        }

        $prevApp = null;
        if (request()->has('renew_from')) {
            $prevApp = Application::where('user_id', auth()->id())
                ->where('status', 'Approved')
                ->findOrFail(request('renew_from'));
        }

        $scholarships = \Illuminate\Support\Facades\Cache::remember('active_scholarships_list', 3600, function () {
            return \App\Models\Scholarship::where('status', 'Active')->get();
        });
        return view('student.apply', compact('scholarships', 'prevApp'));
    }

    // 2. Save the Submitted Data
    public function store(StoreApplicationRequest $request)
    {
        $userId = auth()->id(); // auth middleware guarantees non-null (CRIT-05)

        if (auth()->user()->hasActiveApplication()) {
            $msg = 'Action Denied: You already have an active application or scholarship for this academic term.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return back()->withErrors(['duplicate' => $msg]);
        }

        $scholarship = \App\Models\Scholarship::with('fields')->findOrFail($request->scholarship_id);

        // Renewal limit check
        $approvedCount = \App\Models\Application::where('user_id', $userId)
            ->where('scholarship_id', $scholarship->id)
            ->where('status', 'Approved')
            ->count();

        $maxRenewals = $scholarship->max_renewals ?? 4;
        if ($approvedCount >= $maxRenewals) {
            $msg = 'Application Blocked: You have reached the maximum renewal limit (' . $maxRenewals . ') for the ' . $scholarship->name . '.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['renewal_limit' => $msg])->withInput();
        }

        if ($request->gwa && $scholarship->min_gwa_required && $request->gwa > $scholarship->min_gwa_required) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application Blocked: Your declared GWA of ' . $request->gwa . ' does not meet the minimum requirement (' . $scholarship->min_gwa_required . ') for the ' . $scholarship->name . '.'
                ], 422);
            }
            return back()
                ->withErrors(['gwa' => 'Application Blocked: Your declared GWA of ' . $request->gwa . ' does not meet the minimum requirement (' . $scholarship->min_gwa_required . ') for the ' . $scholarship->name . '.'])
                ->withInput(); 
        }

        // Validation is handled by StoreApplicationRequest

        // HIGH-05: Reduced TTL from 86400 (24hr) to 300 (5min) — prevents stale term after admin switches active term
        $activeTerm = \Illuminate\Support\Facades\Cache::remember('active_academic_term', 300, function () {
            return \App\Models\AcademicTerm::where('is_active', true)->first();
        });

        $application = \App\Models\Application::create([
            'user_id' => $userId,
            'scholarship_id' => $request->scholarship_id,
            'academic_term_id' => $activeTerm ? $activeTerm->id : null,
            'program_name' => $scholarship->name, 
            'gwa' => $request->gwa,
            'status' => 'Pending',
            'is_renewal' => $request->boolean('is_renewal') || !empty($request->previous_application_id),
            'previous_application_id' => $request->previous_application_id ?: null,
        ]);

        // Log the initial status transition
        \App\Models\StatusLog::create([
            'application_id' => $application->id,
            'status' => 'Pending',
            'remarks' => 'Application submitted and entered the verification pipeline.',
            'changed_by' => $userId
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $isSynced = true;
            $filePath = \App\Services\CloudStorageService::upload($file, 'uploads', $isSynced);

            \App\Models\Document::create([
                'application_id' => $application->id,
                'file_path' => $filePath,
                'original_name' => $file->getClientOriginalName(),
                'document_type' => 'COG',
                'upload_event' => 'initial',
                'uploaded_by' => $userId,
                'is_synced' => $isSynced,
            ]);
        }

        // Store custom field values
        if ($request->has('custom_fields')) {
            foreach ($scholarship->fields as $field) {
                $val = null;
                $isSynced = true;
                if ($field->field_type === 'file') {
                    if ($request->hasFile('custom_fields.' . $field->field_name)) {
                        $cfile = $request->file('custom_fields.' . $field->field_name);
                        $filePath = \App\Services\CloudStorageService::upload($cfile, 'uploads', $isSynced);
                        $val = $filePath;

                        // Also register in documents table for AI scanning
                        \App\Models\Document::create([
                            'application_id' => $application->id,
                            'file_path' => $filePath,
                            'original_name' => $cfile->getClientOriginalName(),
                            'document_type' => $field->field_label,
                            'upload_event' => 'initial',
                            'uploaded_by' => $userId,
                            'is_synced' => $isSynced,
                        ]);
                    }
                } else {
                    $val = $request->input('custom_fields.' . $field->field_name);
                }

                if ($val !== null) {
                    $application->customFields()->create([
                        'field_name' => $field->field_label,
                        'field_value' => $val,
                        'is_synced' => $isSynced
                    ]);
                }
            }
        }

        // Auto-trigger background AI scan immediately upon student submission
        $application->load('documents');
        if ($application->documents->count() > 0) {
            foreach ($application->documents as $doc) {
                if (!$doc->aiResult) {
                    \App\Models\AIResult::create([
                        'document_id' => $doc->id,
                        'fraud_probability' => 0.00,
                        'classification' => 'scanning'
                    ]);
                }
            }
            \App\Jobs\ScanDocumentJob::dispatch($application->id);
        }

        // Auto-assign application to staff member
        $assignedStaff = \App\Services\ApplicationAssignmentService::assign($application);

        // Dispatch database notifications to assigned staff
        try {
            if ($assignedStaff) {
                $assignedStaff->notify(new \App\Notifications\NewApplicationNotification($application));
            } else {
                // Fallback: notify all active admins if none assigned
                $admins = \App\Models\User::where('role', 'admin')->where('is_active', true)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\NewApplicationNotification($application));
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to notify staff on new application: ' . $e->getMessage());
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Your application has been submitted successfully to the OSA pipeline!'
            ]);
        }

        return redirect()->route('student.dashboard')
            ->with('success', 'Your application has been submitted successfully to the OSA pipeline!');
    }

    // Dynamic schema helper for students
    public function getScholarshipFields($id)
    {
        $scholarship = Scholarship::with('fields')->findOrFail($id);
        return response()->json($scholarship->fields);
    }

    // 3. Render Profile Page
    public function editProfile()
    {
        $user = auth()->user();
        if ($user->role === 'student') {
            $user->load('profile');
        }

        $devices = $user->mfaDevices()
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('auth.change_password', compact('user', 'devices'));
    }

    // 4. Update Profile Info
    public function updateProfile(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'clsu_id_number' => ['required', 'string', 'max:50', 'regex:/^\d{4}-\d{4}$/'],
            'college' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'year_level' => 'required|string|max:50',
            'contact_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
        ], [
            'clsu_id_number.regex' => 'The CLSU ID number format must be YYYY-XXXX (e.g. 2023-4567).',
            'contact_number.regex' => 'The contact number must be a valid Philippine mobile number (e.g. 09123456789).',
        ]);

        $user->update([
            'name' => $request->name,
        ]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'clsu_id_number' => $request->clsu_id_number,
                'college' => $request->college,
                'course' => $request->course,
                'year_level' => $request->year_level,
                'contact_number' => $request->contact_number,
            ]
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);
        }

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }

    // 5. Cancel application (Soft Delete)
    public function cancel($id)
    {
        $userId = auth()->id(); // auth middleware guarantees non-null (CRIT-05)
        $application = Application::where('user_id', $userId)->findOrFail($id);

        if (!in_array($application->status, ['Pending', 'Under Review'])) {
            return response()->json([
                'success' => false,
                'message' => 'Action Denied: You cannot cancel an application that has already been ' . strtolower($application->status) . '.'
            ], 403);
        }

        // Soft delete the application
        $application->delete();

        // Log the cancellation transition in status_logs
        \App\Models\StatusLog::create([
            'application_id' => $application->id,
            'status' => 'Cancelled',
            'remarks' => 'Application was cancelled by the applicant.',
            'changed_by' => $userId
        ]);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Application cancelled successfully.'
            ]);
        }

        return redirect()->route('student.dashboard')->with('success', 'Application cancelled successfully.');
    }

    // 6. Permanently Withdraw application (Hard Delete)
    public function withdraw($id)
    {
        $userId = auth()->id(); // auth middleware guarantees non-null (CRIT-05)
        // Search in soft-deleted models
        $application = Application::onlyTrashed()
            ->where('user_id', $userId)
            ->findOrFail($id);

        // Can only permanently delete if it was in Pending status when cancelled
        if ($application->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Action Denied: For auditing integrity, only applications in Pending status can be permanently withdrawn.'
            ], 403);
        }

        // Permanently delete all document files (COG + custom uploads) — works for both R2 and local
        $application->load('documents', 'customFields');

        foreach ($application->documents as $doc) {
            \App\Services\CloudStorageService::delete($doc->file_path);
            $doc->forceDelete();
        }

        // Permanently delete custom fields file uploads if any
        if ($application->customFields) {
            foreach ($application->customFields as $field) {
                if (!empty($field->field_value) &&
                    (str_starts_with($field->field_value, 'http') || \Illuminate\Support\Str::startsWith($field->field_value, 'uploads/'))) {
                    \App\Services\CloudStorageService::delete($field->field_value);
                }
                $field->delete();
            }
        }

        // Permanently delete application
        $application->forceDelete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Application permanently withdrawn.'
            ]);
        }

        return redirect()->route('student.dashboard')->with('success', 'Application permanently withdrawn.');
    }

    // 7. Restore cancelled application (Soft Delete Restore)
    public function restore($id)
    {
        $userId = auth()->id(); // auth middleware guarantees non-null (CRIT-05)
        $application = Application::onlyTrashed()
            ->where('user_id', $userId)
            ->findOrFail($id);

        // Check if user already has an active pending application
        $latestActive = Application::where('user_id', $userId)->latest()->first();
        if ($latestActive && $latestActive->status === 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Action Denied: You already have an active pending application (APP-' . $latestActive->id . '). Please withdraw or wait for evaluation before restoring this one.'
            ], 403);
        }

        // Restore
        $application->restore();

        // Log the restoration in status_logs
        \App\Models\StatusLog::create([
            'application_id' => $application->id,
            'status' => $application->status, // Restore to its previous status
            'remarks' => 'Application was restored by the applicant.',
            'changed_by' => $userId
        ]);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Application restored successfully.'
            ]);
        }

        return redirect()->route('student.dashboard')->with('success', 'Application restored successfully.');
    }

    // 8. Complete Guided Onboarding Tour
    public function completeTour()
    {
        $user = auth()->user();
        $user->has_completed_tour = true;
        $user->save();

        return response()->json(['success' => true]);
    }

    // 9. Forfeit / Backout Scholarship
    public function forfeit(Request $request, $id)
    {
        $userId = auth()->id(); // auth middleware guarantees non-null (CRIT-05)
        $application = Application::where('user_id', $userId)
            ->where('status', 'Approved')
            ->findOrFail($id);

        $request->validate([
            'reason' => 'required|string|min:5'
        ]);

        // Update status
        $application->status = 'Cancelled'; // Mark status as Cancelled
        $application->forfeit_reason = $request->reason;
        $application->save();

        // Log the change in status_logs
        \App\Models\StatusLog::create([
            'application_id' => $application->id,
            'status' => 'Cancelled',
            'remarks' => 'Scholarship was forfeited/backed-out by the scholar. Reason: ' . $request->reason,
            'changed_by' => $userId
        ]);

        // Send email notifications
        try {
            // Send status mail
            \Illuminate\Support\Facades\Mail::to($application->user->email)->send(new \App\Mail\ApplicationStatusMail($application));
            
            // Also notify Director (superadmin)
            $director = \App\Models\User::where('role', 'superadmin')->first();
            if ($director) {
                // Log audit trail email to director
                \App\Models\EmailLog::create([
                    'application_id' => $application->id,
                    'recipient' => $director->email,
                    'subject' => '[A.E.G.I.S. Alert] Scholar Forfeiture',
                    'content' => "Scholar {$application->user->name} has backed out from the {$application->program_name} scholarship. Reason: {$request->reason}"
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to notify on forfeiture: ' . $e->getMessage());
        }

        return redirect()->route('student.dashboard')->with('success', 'You have successfully backed out of the scholarship.');
    }
}