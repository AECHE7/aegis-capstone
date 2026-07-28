<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, ['en', 'ph'], true)) {
                Session::put('locale', $lang);
                App::setLocale($lang);
            }
        } elseif (Session::has('locale')) {
            App::setLocale((string) Session::get('locale'));
        } else {
            // Auto-detect Accept-Language if present
            $acceptHeader = $request->server('HTTP_ACCEPT_LANGUAGE', 'en');
            $browserLang = is_string($acceptHeader) ? substr($acceptHeader, 0, 2) : 'en';
            if (in_array($browserLang, ['en', 'ph'], true)) {
                App::setLocale($browserLang);
            } else {
                App::setLocale('en');
            }
        }

        return $next($request);
    }
}
