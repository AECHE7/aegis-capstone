<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
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
        $response = $next($request);

        // ── Clickjacking protection ──────────────────────────────────────
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // ── MIME-type sniffing prevention ────────────────────────────────
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // ── Referrer leakage control ─────────────────────────────────────
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ── HSTS — enforce HTTPS for 1 year (only active over HTTPS) ─────
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // ── Permissions Policy — deny access to sensitive browser APIs ────
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()');

        // ── Cross-Origin isolation headers ───────────────────────────────
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-site');

        // ── Content Security Policy ──────────────────────────────────────
        // Note: 'unsafe-inline' retained for Bootstrap/FA compatibility.
        // 'unsafe-eval' retained for SweetAlert2 compatibility.
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
               "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; " .
               "font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; " .
               "img-src 'self' data: https://res.cloudinary.com https://placehold.co; " .
               "connect-src 'self' https://cdn.jsdelivr.net; " .
               "frame-ancestors 'self';";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
