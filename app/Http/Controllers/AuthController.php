<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Show the Login Page
    public function showLogin()
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            if ($role === 'superadmin') {
                return redirect()->route('superadmin.scholarships');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                if (Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !Auth::user()->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice');
                }
                return redirect()->route('student.dashboard');
            }
        }

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

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact the administrator.',
                ])->onlyInput('email');
            }

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
            'email' => 'Invalid email or password. Please try again.',
        ])->onlyInput('email');
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
            set_time_limit(0);
            $lastChecked = now()->toDateTimeString();

            // Send initial state
            $initialCount = $user->unreadNotifications()->count();
            echo "data: " . json_encode(['count' => $initialCount, 'refresh' => true]) . "\n\n";
            if (ob_get_level() > 0) { ob_flush(); }
            flush();

            while (true) {
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
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }
}