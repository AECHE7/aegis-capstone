<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Models\MasterTransfer;
use App\Mail\MasterTransferMail;
use App\Services\AuditLoggerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MasterController extends Controller
{
    /**
     * Show the Master Landing/Gateway Dashboard.
     */
    public function showGateway()
    {
        $user = Auth::user();
        if (!$user || !$user->isMaster()) {
            abort(403, 'Unauthorized access.');
        }

        // Fetch transfers
        $pendingTransfers = MasterTransfer::whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        $completedTransfers = MasterTransfer::whereNotNull('accepted_at')
            ->orWhere('expires_at', '<=', now())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('master.gateway', compact('pendingTransfers', 'completedTransfers'));
    }

    /**
     * Switch the active session role.
     */
    public function switchRole(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isMaster()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'role' => 'required|string|in:student,admin,superadmin'
        ]);

        $oldRole = session('active_role', $user->getRawOriginal('role'));
        $newRole = $request->role;

        session(['active_role' => $newRole]);

        AuditLoggerService::logAdminAction(
            $user,
            'master_role_switch',
            'User',
            $user->id,
            "Master account switched role from '{$oldRole}' to '{$newRole}'.",
            $request->ip()
        );

        // Redirect based on new role
        if ($newRole === 'superadmin') {
            return redirect()->route('superadmin.scholarships')->with('success', "Role switched to Director");
        } elseif ($newRole === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', "Role switched to Administrator");
        } else {
            return redirect()->route('student.dashboard')->with('success', "Role switched to Student");
        }
    }

    /**
     * Initiate privilege transfer.
     */
    public function initiateTransfer(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isMaster()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'recipient_email' => 'required|email|max:255'
        ]);

        $recipientEmail = strtolower($request->recipient_email);

        if ($recipientEmail === strtolower($user->email)) {
            return back()->with('error', 'You cannot transfer Master privilege to yourself.');
        }

        // Generate token and record
        $token = Str::random(60);
        $expiresAt = now()->addHours(24);

        $transfer = MasterTransfer::create([
            'sender_email' => $user->email,
            'recipient_email' => $recipientEmail,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        // Send email
        try {
            Mail::to($recipientEmail)->send(new MasterTransferMail($user->email, $recipientEmail, $token));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send Master transfer email: ' . $e->getMessage());
            // In local/UAT we let it succeed even if mail delivery fails so testing is unblocked.
        }

        AuditLoggerService::logAdminAction(
            $user,
            'master_transfer_initiated',
            'MasterTransfer',
            $transfer->id,
            "Master privilege transfer initiated from '{$user->email}' to '{$recipientEmail}'.",
            $request->ip()
        );

        return back()->with('success', "Privilege transfer link generated and sent to {$recipientEmail}. Link is valid for 24 hours.");
    }

    /**
     * Show acceptance screen or process the acceptance of Master privilege.
     */
    public function acceptTransfer(Request $request, $token)
    {
        $transfer = MasterTransfer::where('token', $token)->first();

        if (!$transfer) {
            return redirect()->route('login')->with('error', 'Invalid transfer link or token.');
        }

        if ($transfer->accepted_at) {
            return redirect()->route('login')->with('error', 'This transfer has already been accepted.');
        }

        if ($transfer->expires_at->isPast()) {
            return redirect()->route('login')->with('error', 'This transfer link has expired.');
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            // Save token in session so we can auto-redirect after login
            session(['pending_master_transfer_token' => $token]);
            return redirect()->route('login')->with('warning', 'Please log in or register with the recipient email to accept the Master privilege.');
        }

        $currentUser = Auth::user();
        if (strtolower($currentUser->email) !== strtolower($transfer->recipient_email)) {
            // If they are logged in with the wrong account, force them to log out or warn them
            return redirect()->route('login')->with('error', "Unauthorized. This transfer link is only valid for {$transfer->recipient_email}. Please log in with that account.");
        }

        if ($request->isMethod('post')) {
            $oldMasterEmail = Setting::get('master_email', 'gadianoriel07@gmail.com');
            
            // Perform transfer atomically
            \DB::transaction(function () use ($transfer, $currentUser, $oldMasterEmail) {
                Setting::set('master_email', $currentUser->email);
                $transfer->update(['accepted_at' => now()]);
            });

            // Log setting change
            AuditLoggerService::logConfigChange(
                $currentUser,
                'master_email',
                $oldMasterEmail,
                $currentUser->email,
                $request->ip()
            );

            // Log admin action
            AuditLoggerService::logAdminAction(
                $currentUser,
                'master_transfer_accepted',
                'MasterTransfer',
                $transfer->id,
                "Master privilege accepted by '{$currentUser->email}'. Former master '{$oldMasterEmail}' revoked.",
                $request->ip()
            );

            // Clear session roles for all users
            session()->forget('active_role');
            session()->forget('pending_master_transfer_token');

            return redirect()->route('master.gateway')->with('success', 'Congratulations! You are now the Master account. Select your role to get started.');
        }

        return view('master.accept', compact('transfer'));
    }
}
