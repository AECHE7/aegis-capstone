<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            $role = $request->user()->role;
            if ($role === 'superadmin') {
                return redirect()->route('superadmin.scholarships');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('student.dashboard');
            }
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
