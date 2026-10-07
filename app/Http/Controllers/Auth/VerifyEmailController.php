<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $userId = $request->route('id');
        $hash = $request->route('hash');

        $user = $request->user() ?: User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User account not found.');
        }

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid or expired email verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            if (!Auth::check()) {
                Auth::login($user);
            }
            return $this->redirectForRole($user)->with('info', 'Your email address is already verified.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        if (!Auth::check()) {
            Auth::login($user);
        }

        return $this->redirectForRole($user)->with('success', 'Your CLSU student email has been verified successfully!');
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
