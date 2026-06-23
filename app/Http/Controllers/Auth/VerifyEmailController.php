<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->redirectForRole($user);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->redirectForRole($user);
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
