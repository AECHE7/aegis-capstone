<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GzipResponse
{
    /**
     * Handle an incoming request and compress response payload if accepted by client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Do not compress binary stream downloads, image outputs, or already compressed responses
        if (
            function_exists('gzencode') &&
            str_contains($request->header('Accept-Encoding', ''), 'gzip') &&
            !$response->headers->has('Content-Encoding')
        ) {
            $content = $response->getContent();

            // Only compress textual responses greater than 1KB
            if (is_string($content) && strlen($content) > 1024) {
                $compressed = gzenCode($content, 6);
                if ($compressed !== false) {
                    $response->setContent($compressed);
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Vary', 'Accept-Encoding');
                    $response->headers->set('Content-Length', strlen($compressed));
                }
            }
        }

        // Set static cache headers for public assets
        if ($request->is('build/*') || $request->is('logo.*') || $request->is('manifest.json')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        }

        return $response;
    }
}
