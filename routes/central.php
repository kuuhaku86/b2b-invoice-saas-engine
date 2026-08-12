<?php

use App\Http\Controllers\Central\AuthController;
use App\Http\Controllers\Central\DashboardController;
use App\Http\Controllers\Central\PlanController;
use App\Http\Controllers\Central\StripeWebhookController;
use App\Http\Controllers\Central\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central Routes
|--------------------------------------------------------------------------
|
| Restricted to the central (apex) domain via Route::domain(), so these
| never match a tenant subdomain request even though this file is loaded
| through the default "web" route group at framework boot (before
| routes/tenant.php is mapped). Landlord/admin endpoints (tenant & plan
| management) belong in this group.
|
*/

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return auth()->check()
                ? redirect()->route('central.dashboard')
                : redirect()->route('central.login');
        });

        Route::name('central.')->group(function () {
            Route::middleware('guest')->group(function () {
                Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
                Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
            });

            // Landlord/admin — provisioned via seeder/tinker, no self-service
            // signup (see Central\AuthController).
            Route::middleware('auth')->group(function () {
                Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
                Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');

                Route::prefix('admin')->group(function () {
                    Route::resource('tenants', TenantController::class)->except('show');
                    Route::resource('plans', PlanController::class)->except('show');
                });
            });
        });

        // Stripe posts here regardless of tenant (see StripeWebhookController).
        // CSRF-excluded in bootstrap/app.php — this is authenticated by
        // signature verification instead.
        Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');
    });
}
