<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\Mail::extend('brevo_api', function (array $config) {
            return new \App\Mail\Transport\BrevoTransport(env('BREVO_API_KEY') ?: env('MAIL_PASSWORD'));
        });
    }
}
