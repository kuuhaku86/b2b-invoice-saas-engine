<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

// Deliberately NOT using RefreshDatabase: it wraps each test in an open
// transaction on the central connection, and these tests provision real
// tenant databases (CREATE/DROP DATABASE) via separate connections mid-test.
// That combination deadlocks MySQL (DDL elsewhere waits on a metadata lock
// the open central transaction never releases). Instead: migrate the
// central *_testing database fresh once per test run, and let
// Tests\TestCase::tearDown() explicitly drop any tenant databases a test
// created — the only cleanup that actually matters here, since tests use
// unique tenant ids and don't otherwise depend on a pristine central DB.
pest()->extend(Tests\TestCase::class)
    ->beforeEach(function () {
        static $migrated = false;

        if (! $migrated) {
            Illuminate\Support\Facades\Artisan::call('migrate:fresh');
            $migrated = true;
        }

        $this->seed(Database\Seeders\PlanSeeder::class);
    })
    ->in('Feature');

// Browser tests drive a real Chrome instance against the actual running
// dev stack (see tests/DuskTestCase.php) — no DatabaseMigrations/
// RefreshDatabase here either, for the same reason as above, and because
// wiping the schema between tests would nuke real local dev data these
// tests run alongside. Each test provisions and tears down its own
// uniquely-named tenants (see tests/Browser/TenantIsolationTest.php).
pest()->extend(Tests\DuskTestCase::class)
    ->in('Browser');
