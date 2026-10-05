<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GzipResponse
{
    /**
     * Handle an incoming request.
     * Transport-level compression (Gzip / Brotli) is handled natively by Nginx and Cloudflare.
     * This middleware ensures asset cache headers and guards against response encoding corruption.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Set static cache headers for public assets
        if (
            $request->is('build/*') ||
            $request->is('logo.*') ||
            $request->is('manifest.json') ||
            $request->is('samples/*') ||
            $request->is('documents/*')
        ) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        }

        return $response;
    }
}
