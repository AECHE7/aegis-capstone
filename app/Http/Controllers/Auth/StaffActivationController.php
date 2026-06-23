<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class StaffActivationController extends Controller
{
    /**
     * Show the activation password-setup view.
     */
    public function showActivationForm(Request $request): View|RedirectResponse
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('login')->withErrors(['email' => 'Invitation token is missing.']);
        }

        $invitation = UserInvitation::where('token', $token)->first();

        if (!$invitation) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid invitation token.']);
        }

        if ($invitation->expires_at->isPast()) {
            return redirect()->route('login')->withErrors(['email' => 'This invitation has expired. Please ask the Super Admin to reinvite you.']);
        }

        $user = $invitation->user;

        return view('auth.activate-account', [
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Set the password, mark verified, delete token, and log in.
     */
    public function activate(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $invitation = UserInvitation::where('token', $request->token)->first();

        if (!$invitation) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid invitation token.']);
        }

        if ($invitation->expires_at->isPast()) {
            return redirect()->route('login')->withErrors(['email' => 'This invitation has expired. Please ask the Super Admin to reinvite you.']);
        }

        $user = $invitation->user;

        // Set password and mark verified
        $user->password = Hash::make($request->password);
        $user->email_verified_at = now();
        $user->save();

        // Delete invitation token
        $invitation->delete();

        // Log the user in
        Auth::login($user);

        // Redirect to admin dashboard
        return redirect()->route('admin.dashboard')->with('success', 'Your account has been activated successfully!');
    }
}
