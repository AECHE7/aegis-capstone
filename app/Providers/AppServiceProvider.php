<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->isProduction() || str_starts_with(config('app.url', ''), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Model::shouldBeStrict(! $this->app->isProduction());

        \Illuminate\Support\Facades\Mail::extend('brevo_api', function (array $config) {
            return new \App\Mail\Transport\BrevoTransport($config['key'] ?? null);
        });

        \Illuminate\Validation\Rules\Password::defaults(function () {
            $rule = \Illuminate\Validation\Rules\Password::min(8);

            return !$this->app->isLocal() && !$this->app->runningUnitTests()
                ? $rule->mixedCase()->letters()->numbers()->symbols()->uncompromised()
                : $rule;
        });

        \Illuminate\Support\Facades\RateLimiter::for('login', function (\Illuminate\Http\Request $request) {
            $email = (string) $request->email;
            return [
                \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip()),
                \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by(strtolower($email) . '|' . $request->ip()),
            ];
        });

        \Illuminate\Support\Facades\View::composer(['layouts.app', 'layouts.sidebar'], function ($view) {
            try {
                $globalActiveTerm = \Illuminate\Support\Facades\Cache::remember('active_academic_term', 300, function () {
                    return \App\Models\AcademicTerm::where('is_active', true)->first();
                });
                $view->with('globalActiveTerm', $globalActiveTerm);
            } catch (\Throwable $e) {
                $view->with('globalActiveTerm', null);
            }
        });
    }
}
