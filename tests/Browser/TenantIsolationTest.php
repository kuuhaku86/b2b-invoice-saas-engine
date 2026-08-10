<?php

use Laravel\Dusk\Browser;

/*
|--------------------------------------------------------------------------
| Why this test exists
|--------------------------------------------------------------------------
|
| Pest's Feature tests (tests/Feature/TenantIsolationTest.php) prove tenant
| data is isolated at the database level, but Laravel's HTTP test client
| shares in-process session/auth state across simulated requests — it can't
| exercise real browser cookie scoping at all. This test uses an actual
| browser to prove the thing that actually protects tenants in production:
| a session cookie set on one tenant subdomain is never sent to another.
*/

test('a login session on one tenant subdomain does not authenticate on another', function () {
    $a = $this->createTestTenant('e2ea');
    $this->createTestTenant('e2eb');

    tenancy()->initialize($a);
    $user = App\Models\User::create([
        'name' => 'Alice',
        'email' => 'alice@e2ea.test',
        'password' => bcrypt('password123'),
    ]);
    tenancy()->end();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->visit($this->tenantUrl('e2ea', '/login'))
            ->type('email', $user->email)
            ->type('password', 'password123')
            ->press('Log in')
            ->assertPathIs('/dashboard')
            ->assertSee('e2ea');

        // Same browser, same cookie jar — but a different tenant subdomain.
        // If the login cookie leaked across subdomains, this would land on
        // e2eb's dashboard instead of being bounced to its login page.
        $browser->visit($this->tenantUrl('e2eb', '/dashboard'))
            ->assertPathIs('/login');
    });
});
