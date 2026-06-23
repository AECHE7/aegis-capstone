<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? $this->redirectForRole($request->user())
                    : view('auth.verify-email');
    }

    protected function redirectForRole($user): RedirectResponse
    {
        $role = $user->role;
        if ($role === 'superadmin') {
            return redirect()->route('superadmin.scholarships');
        } elseif ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    }
}
