<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'api/payments/webhook/stripe',
        ]);

        $middleware->alias([
            'role.admin'                    => \App\Http\Middleware\EnsureAdmin::class,
            'role.manager_admin'            => \App\Http\Middleware\EnsureManagerOrAdmin::class,
            'role.master'                   => \App\Http\Middleware\EnsureMaster::class,
            'role.manager_admin_or_client'  => \App\Http\Middleware\EnsureManagerAdminOrOwnClient::class,
            'role.manager_admin_or_car'     => \App\Http\Middleware\EnsureManagerAdminOrOwnCar::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
