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
    }
}
