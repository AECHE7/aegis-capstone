<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->role === 'student') {
            // Exempted routes to allow profile viewing, editing, updating, security, and logout
            $exemptRouteNames = [
                'student.profile',
                'student.profile.update',
                'profile.security',
                'profile.security.update',
                'profile.security.devices.revoke',
                'profile.update',
                'logout',
                'verification.notice',
                'verification.verify',
                'verification.send',
                'verification.status',
                'notifications.index',
                'notifications.read',
                'notifications.clear',
                'tour.reset',
            ];

            $currentRoute = $request->route() ? $request->route()->getName() : null;

            if ($currentRoute && in_array($currentRoute, $exemptRouteNames, true)) {
                return $next($request);
            }

            if (!$user->isProfileComplete()) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Profile incomplete. Please complete your basic student information before proceeding.',
                        'redirect' => route('student.profile'),
                    ], 403);
                }

                return redirect()->route('student.profile')->with('warning', 'Please complete your basic student profile information before accessing the scholarship portal.');
            }
        }

        return $next($request);
    }
}
