<?php

use App\Http\Middleware\CheckPlanLimit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/central.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Both sides have 'auth'-protected routes now (tenant dashboard vs.
        // central admin + Horizon) — send guests to whichever login belongs
        // to the context they were in.
        $middleware->redirectGuestsTo(
            fn () => tenancy()->initialized ? route('tenant.login') : route('central.login')
        );
        $middleware->alias(['plan.limit' => CheckPlanLimit::class]);
        // Authenticated by Stripe-Signature verification instead of CSRF —
        // Stripe can't obtain our CSRF token.
        $middleware->validateCsrfTokens(except: ['stripe/webhook']);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // A subdomain with no matching tenant should 404, not leak a 500
        // with a stack trace.
        $exceptions->render(function (TenantCouldNotBeIdentifiedException $e) {
            throw new NotFoundHttpException('Not found.', $e);
        });
    })->create();
