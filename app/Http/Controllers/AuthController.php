<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Show the Login Page
    public function showLogin()
    {
        $demoStudent = null;
        $demoAdmin = null;
        $demoSuperAdmin = null;
        $latestInvitation = null;

        if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
            $demoStudent = \App\Models\User::where('role', 'student')->first();
            $demoAdmin = \App\Models\User::where('role', 'admin')->first();
            $demoSuperAdmin = \App\Models\User::where('role', 'superadmin')->first();
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('user_invitations')) {
            $latestInvitation = \App\Models\UserInvitation::latest()->first();
        }

        return view('auth.login', compact('demoStudent', 'demoAdmin', 'demoSuperAdmin', 'latestInvitation'));
    }

    // 2. Process the Login Request
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            if (isset($user->is_active) && !$user->is_active) {
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact the administrator.',
                ])->onlyInput('email');
            }

            // Generate OTP
            $otp = app()->runningUnitTests() ? '123456' : sprintf("%06d", mt_rand(100000, 999999));
            $user->otp_code = $otp;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            // Send OTP Email
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\MfaOtpMail($otp));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send MFA OTP: ' . $e->getMessage());
            }

            // Store user ID in session
            session(['mfa_user_id' => $user->id]);

            return redirect()->route('login.mfa')->with('success', 'A verification code has been sent to your email.');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password. Please try again.',
        ])->onlyInput('email');
    }

    // 2b. Show MFA Form
    public function showMfa()
    {
        if (!session()->has('mfa_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.mfa_verify');
    }

    // 2b-ii. Resend MFA OTP
    public function resendMfa()
    {
        if (!session()->has('mfa_user_id')) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please log in again.'], 401);
        }

        $user = \App\Models\User::findOrFail(session('mfa_user_id'));

        $otp = app()->runningUnitTests() ? '123456' : sprintf("%06d", mt_rand(100000, 999999));
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\MfaOtpMail($otp));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to resend MFA OTP: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'A new verification code has been sent to your email.']);
    }

    // 2c. Verify MFA Code
    public function verifyMfa(Request $request)
    {
        if (!session()->has('mfa_user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = \App\Models\User::findOrFail(session('mfa_user_id'));

        if ($user->otp_code === $request->code && $user->otp_expires_at && $user->otp_expires_at->isFuture()) {
            // Clear OTP
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            // Login user
            Auth::login($user);
            session()->forget('mfa_user_id');
            $request->session()->regenerate();

            // ROLE-BASED REDIRECTION
            $role = $user->role;
            if ($role === 'superadmin') {
                return redirect()->route('superadmin.scholarships');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice');
                }
                return redirect()->route('student.dashboard');
            }
        }

        return back()->withErrors([
            'code' => 'The verification code is invalid or has expired.',
        ]);
    }

    // 2d. Show Security / Change Password Settings
    public function showSecurity()
    {
        return view('auth.change_password');
    }

    // 2e. Update Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Your password has been changed successfully!');
    }

    // 3. Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // 4. Get notifications
    public function getNotifications()
    {
        $notifications = auth()->user()->unreadNotifications()->take(10)->get()->map(function($n) {
            return [
                'id' => $n->id,
                'title' => $n->data['title'] ?? 'Notification',
                'message' => $n->data['message'] ?? '',
                'created_at' => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'notifications' => $notifications,
            'count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    // 5. Mark notification as read
    public function markNotificationAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    // 6. Clear all notifications
    public function clearNotifications()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    public function streamNotifications()
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($user) {
            // Release session lock to prevent blocking concurrent requests
            session_write_close();

            set_time_limit(0);
            $lastChecked = now()->toDateTimeString();

            // Send initial state
            $initialCount = $user->unreadNotifications()->count();
            echo "data: " . json_encode(['count' => $initialCount, 'refresh' => true]) . "\n\n";
            if (ob_get_level() > 0) { ob_flush(); }
            flush();

            $maxCycles = 15; // 30 seconds total (15 * 2s) to prevent worker exhaustion
            $cycle = 0;
            while ($cycle < $maxCycles) {
                if (connection_aborted() || app()->runningUnitTests()) {
                    break;
                }

                $newCount = $user->unreadNotifications()->where('created_at', '>', $lastChecked)->count();

                if ($newCount > 0) {
                    $lastChecked = now()->toDateTimeString();
                    $totalCount = $user->unreadNotifications()->count();
                    echo "data: " . json_encode(['count' => $totalCount, 'refresh' => true]) . "\n\n";
                    if (ob_get_level() > 0) { ob_flush(); }
                    flush();
                }

                sleep(2);
                $cycle++;
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }
}