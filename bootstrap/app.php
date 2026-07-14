<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->web(append: [
            \App\Http\Middleware\CheckUserActive::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database connection timeout or busy lockout. Please retry in a few moments.'
                ], 503);
            }
            if ($request->is('/') || $request->is('login')) {
                return response()->view('auth.login', [
                    'demoStudent' => null,
                    'demoAdmin' => null,
                    'demoSuperAdmin' => null,
                    'latestInvitation' => null,
                    'emergencyReadOnly' => true,
                    'errors' => new \Illuminate\Support\ViewErrorBag()
                ]);
            }
            return response()->view('errors.db_error', [
                'message' => 'The database connection is temporarily busy or locked under heavy load. Please reload the page to retry.',
                'exception' => $e
            ], 503);
        });

        $exceptions->render(function (\PDOException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database driver timeout or busy lockout. Please retry in a few moments.'
                ], 503);
            }
            if ($request->is('/') || $request->is('login')) {
                return response()->view('auth.login', [
                    'demoStudent' => null,
                    'demoAdmin' => null,
                    'demoSuperAdmin' => null,
                    'latestInvitation' => null,
                    'emergencyReadOnly' => true,
                    'errors' => new \Illuminate\Support\ViewErrorBag()
                ]);
            }
            return response()->view('errors.db_error', [
                'message' => 'The database driver is temporarily busy or locked under heavy load. Please reload the page to retry.',
                'exception' => $e
            ], 503);
        });
    })->create();
