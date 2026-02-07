<?php

use App\Exceptions\ApiExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'webhook.whitelist' => \App\Http\Middleware\WebhookIpWhitelist::class,
            'redirect.frontend' => \App\Http\Middleware\RedirectToFrontend::class,
            'api.auth' => \App\Http\Middleware\ApiTokenAuth::class,
            'api.public' => \App\Http\Middleware\PublicApiToken::class,
            'api.audit' => \App\Http\Middleware\ApiAuditLog::class,
            'api.idempotency' => \App\Http\Middleware\IdempotencyKey::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\ApiAuditLog::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            return (new ApiExceptionHandler)->handle($e, $request);
        });
    })->create();
