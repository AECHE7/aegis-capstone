<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    // 1. Load the Student Dashboard (Pizza Tracker)
    // Change the name from index() to dashboard() right here!
    public function dashboard()
    {
        $userId = auth()->id() ?? 1; // Fallback to user 1 for testing
        $application = Application::with(['document.aiResult', 'customFields', 'academicTerm', 'statusLogs' => function($q) {
            $q->orderBy('created_at', 'asc');
        }])
            ->where('user_id', $userId)
            ->latest()
            ->first();

        return view('student.dashboard', compact('application'));
    }

    // 1. Load the Application Form
    public function create()
    {
        $userId = auth()->id() ?? 1;

        // THE FIX: Check only the MOST RECENT application!
        $latestApplication = \App\Models\Application::where('user_id', $userId)->latest()->first();

        // If their latest application is pending, block them.
        if ($latestApplication && $latestApplication->status === 'Pending') {
            return redirect()->route('student.dashboard')
                ->with('error', 'Action Denied: Your most recent application (APP-'.$latestApplication->id.') is still pending review. Please wait for the OSA to evaluate it.');
        }

        $scholarships = \Illuminate\Support\Facades\Cache::remember('active_scholarships_list', 3600, function () {
            return \App\Models\Scholarship::where('status', 'Active')->get();
        });
        return view('student.apply', compact('scholarships'));
    }

    // 2. Save the Submitted Data
    public function store(\Illuminate\Http\Request $request)
    {
        $userId = auth()->id() ?? 1;

        // THE FIX: Backend protection checking only the latest app
        $latestApplication = \App\Models\Application::where('user_id', $userId)->latest()->first();
        if ($latestApplication && $latestApplication->status === 'Pending') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Your most recent application is still pending!'], 400);
            }
            return back()->withErrors(['duplicate' => 'Your most recent application is still pending!']);
        }

        $scholarship = \App\Models\Scholarship::with('fields')->findOrFail($request->scholarship_id);

        if ($request->gwa > $scholarship->min_gwa_required) {
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

        // Build validation rules dynamically
        $rules = [
            'scholarship_id' => 'required',
            'gwa' => 'required|numeric|min:1.00|max:5.00',
            'document' => 'required|image|mimes:jpeg,png|max:5120', 
        ];

        foreach ($scholarship->fields as $field) {
            $fieldRule = [];
            if ($field->is_required) {
                $fieldRule[] = 'required';
            } else {
                $fieldRule[] = 'nullable';
            }

            if ($field->field_type === 'number') {
                $fieldRule[] = 'numeric';
            } elseif ($field->field_type === 'file') {
                $fieldRule[] = 'file';
                $fieldRule[] = 'max:5120';
            } else {
                $fieldRule[] = 'string';
            }

            $rules['custom_fields.' . $field->field_name] = $fieldRule;
        }

        $request->validate($rules);

        $activeTerm = \Illuminate\Support\Facades\Cache::remember('active_academic_term', 86400, function () {
            return \App\Models\AcademicTerm::where('is_active', true)->first();
        });

        $application = \App\Models\Application::create([
            'user_id' => $userId,
            'scholarship_id' => $request->scholarship_id,
            'academic_term_id' => $activeTerm ? $activeTerm->id : null,
            'program_name' => $scholarship->name, 
            'gwa' => $request->gwa,
            'status' => 'Pending'
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
            $extension = $file->getClientOriginalExtension();
            $uuid = (string) \Illuminate\Support\Str::uuid();
            $filename = hash('sha256', $uuid) . '.' . $extension;
            $file->storeAs('uploads', $filename, 'local');

            \App\Models\Document::create([
                'application_id' => $application->id,
                'file_path' => 'uploads/' . $filename,
                'original_name' => $file->getClientOriginalName(),
                'document_type' => 'COG'
            ]);
        }

        // Store custom field values
        if ($request->has('custom_fields')) {
            foreach ($scholarship->fields as $field) {
                $val = null;
                if ($field->field_type === 'file') {
                    if ($request->hasFile('custom_fields.' . $field->field_name)) {
                        $cfile = $request->file('custom_fields.' . $field->field_name);
                        $extension = $cfile->getClientOriginalExtension();
                        $uuid = (string) \Illuminate\Support\Str::uuid();
                        $filename = hash('sha256', $uuid) . '.' . $extension;
                        $cfile->storeAs('uploads', $filename, 'local');
                        $val = 'uploads/' . $filename;
                    }
                } else {
                    $val = $request->input('custom_fields.' . $field->field_name);
                }

                if ($val !== null) {
                    $application->customFields()->create([
                        'field_name' => $field->field_label,
                        'field_value' => $val
                    ]);
                }
            }
        }

        // Dispatch database notifications to assigned staff
        try {
            $assignedStaff = \App\Models\User::where('role', 'admin')
                ->whereHas('scholarships', function($q) use($application) {
                    $q->where('scholarships.id', $application->scholarship_id);
                })->get();

            foreach ($assignedStaff as $staff) {
                $staff->notify(new \App\Notifications\NewApplicationNotification($application));
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
        $user = auth()->user()->load('profile');
        return view('student.profile', compact('user'));
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
            'contact_number' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'],
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
}