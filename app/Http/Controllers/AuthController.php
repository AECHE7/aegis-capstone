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
            $request->session()->regenerate();

            // ROLE-BASED REDIRECTION
            $user = Auth::user();
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
}