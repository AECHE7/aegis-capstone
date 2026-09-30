<?php

namespace App\Http\Controllers;

use App\Mail\DirectorInvitationMail;
use App\Models\DirectorInvitation;
use App\Models\User;
use App\Services\AuditLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DirectorTransferController extends Controller
{
    // -------------------------------------------------------
    // Show the Director Invitation management panel
    // Only the Master account can access this
    // -------------------------------------------------------
    public function showPanel()
    {
        $user = Auth::user();
        if (!$user || !$user->isMaster()) {
            abort(403, 'Only the Master account can manage Director invitations.');
        }

        $pendingInvitations = DirectorInvitation::whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        $completedInvitations = DirectorInvitation::whereNotNull('accepted_at')
            ->orderBy('accepted_at', 'desc')
            ->take(10)
            ->get();

        $expiredInvitations = DirectorInvitation::whereNull('accepted_at')
            ->where('expires_at', '<=', now())
            ->orderBy('expires_at', 'desc')
            ->take(5)
            ->get();

        $currentDirectors = User::where('role', 'superadmin')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('master.director_transfer', compact(
            'pendingInvitations',
            'completedInvitations',
            'expiredInvitations',
            'currentDirectors'
        ));
    }

    // -------------------------------------------------------
    // Send the invitation email to the client
    // -------------------------------------------------------
    public function sendInvitation(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isMaster()) {
            abort(403, 'Only the Master account can send Director invitations.');
        }

        $request->validate([
            'recipient_email' => 'required|email|max:255',
            'recipient_name'  => 'nullable|string|max:100',
        ]);

        $recipientEmail = strtolower(trim($request->recipient_email));
        $recipientName  = trim($request->recipient_name ?? '');

        // Check for an already pending invite to the same email
        $existingPending = DirectorInvitation::where('recipient_email', $recipientEmail)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingPending) {
            return back()->with('error', "A pending invitation already exists for {$recipientEmail}. It expires on " . $existingPending->expires_at->format('M d, Y h:i A') . '. Revoke it first or wait for it to expire.');
        }

        $token     = Str::random(80);
        $expiresAt = now()->addHours(48);

        $invitation = DirectorInvitation::create([
            'invited_by_email' => $user->email,
            'recipient_email'  => $recipientEmail,
            'recipient_name'   => $recipientName ?: null,
            'token'            => $token,
            'expires_at'       => $expiresAt,
        ]);

        // Send email
        try {
            Mail::to($recipientEmail)->send(
                new DirectorInvitationMail($user->email, $recipientEmail, $recipientName, $token)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send Director invitation email: ' . $e->getMessage());
            // Don't fail the request — the link was still created; master can share it manually.
        }

        AuditLoggerService::logAdminAction(
            $user,
            'director_invitation_sent',
            'DirectorInvitation',
            $invitation->id,
            "Director invitation sent from '{$user->email}' to '{$recipientEmail}'.",
            $request->ip()
        );

        return back()->with('success', "Director invitation sent to {$recipientEmail}. The link expires in 48 hours.");
    }

    // -------------------------------------------------------
    // Revoke a pending invitation
    // -------------------------------------------------------
    public function revokeInvitation(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->isMaster()) {
            abort(403, 'Only the Master account can revoke Director invitations.');
        }

        $invitation = DirectorInvitation::findOrFail($id);

        if ($invitation->accepted_at) {
            return back()->with('error', 'Cannot revoke an invitation that has already been accepted.');
        }

        // Force-expire it instead of deleting so audit history is preserved
        $invitation->update(['expires_at' => now()->subSecond()]);

        AuditLoggerService::logAdminAction(
            $user,
            'director_invitation_revoked',
            'DirectorInvitation',
            $invitation->id,
            "Director invitation to '{$invitation->recipient_email}' was revoked by '{$user->email}'.",
            $request->ip()
        );

        return back()->with('success', "Invitation for {$invitation->recipient_email} has been revoked.");
    }

    // -------------------------------------------------------
    // Show the acceptance page (public — linked from email)
    // -------------------------------------------------------
    public function showAccept(Request $request, string $token)
    {
        $invitation = DirectorInvitation::where('token', $token)->first();

        if (!$invitation) {
            return redirect()->route('login')->with('error', 'Invalid invitation link.');
        }

        if ($invitation->isAccepted()) {
            return redirect()->route('login')->with('error', 'This invitation has already been accepted.');
        }

        if ($invitation->isExpired()) {
            return redirect()->route('login')->with('error', 'This invitation link has expired. Please ask the system administrator to send a new one.');
        }

        // If not logged in, save token and redirect to login/register
        if (!Auth::check()) {
            session(['pending_director_invitation_token' => $token]);
            return redirect()->route('login')->with('info', 'Please log in or create an account using ' . $invitation->recipient_email . ' to accept your Director role.');
        }

        $currentUser = Auth::user();

        // Must be logged in as the invited email
        if (strtolower($currentUser->email) !== strtolower($invitation->recipient_email)) {
            return redirect()->route('login')
                ->with('error', "This invitation is for {$invitation->recipient_email}. You are logged in as {$currentUser->email}. Please log in with the correct account.");
        }

        return view('master.accept_director', compact('invitation'));
    }

    // -------------------------------------------------------
    // Process the acceptance (POST from the accept page)
    // -------------------------------------------------------
    public function acceptInvitation(Request $request, string $token)
    {
        $invitation = DirectorInvitation::where('token', $token)->first();

        if (!$invitation) {
            return redirect()->route('login')->with('error', 'Invalid invitation link.');
        }

        if ($invitation->isAccepted()) {
            return redirect()->route('login')->with('error', 'This invitation has already been accepted.');
        }

        if ($invitation->isExpired()) {
            return redirect()->route('login')->with('error', 'This invitation link has expired.');
        }

        if (!Auth::check()) {
            session(['pending_director_invitation_token' => $token]);
            return redirect()->route('login')->with('info', 'Please log in to accept your Director invitation.');
        }

        $currentUser = Auth::user();

        if (strtolower($currentUser->email) !== strtolower($invitation->recipient_email)) {
            return redirect()->route('login')
                ->with('error', "This invitation is only valid for {$invitation->recipient_email}.");
        }

        DB::transaction(function () use ($invitation, $currentUser) {
            // Promote user to superadmin
            $currentUser->update(['role' => 'superadmin']);

            // Mark invitation as accepted
            $invitation->update([
                'accepted_at'         => now(),
                'accepted_by_user_id' => $currentUser->id,
            ]);
        });

        AuditLoggerService::logAdminAction(
            $currentUser,
            'director_role_accepted',
            'DirectorInvitation',
            $invitation->id,
            "'{$currentUser->email}' accepted Director (superadmin) role invitation from '{$invitation->invited_by_email}'.",
            $request->ip()
        );

        session()->forget('pending_director_invitation_token');

        return redirect()->route('superadmin.scholarships')
            ->with('success', 'Welcome! Your account has been promoted to OSA Director. You now have full access to the A.E.G.I.S. system.');
    }
}
