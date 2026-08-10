<?php

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
            return view('welcome');
        });
    });
}
