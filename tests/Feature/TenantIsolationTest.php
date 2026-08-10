<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

it('provisions a separate physical database per tenant', function () {
    $a = $this->createTestTenant('testa');
    $b = $this->createTestTenant('testb');

    tenancy()->initialize($a);
    $dbA = \Illuminate\Support\Facades\DB::connection('tenant')->getDatabaseName();
    tenancy()->end();

    tenancy()->initialize($b);
    $dbB = \Illuminate\Support\Facades\DB::connection('tenant')->getDatabaseName();
    tenancy()->end();

    expect($dbA)->not->toBe($dbB);
});

it('keeps registered users isolated per tenant, even with identical emails', function () {
    $this->createTestTenant('testa');
    $this->createTestTenant('testb');

    $payload = [
        'name' => 'Duplicate Email User',
        'email' => 'same@example.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $this->post($this->tenantUrl('testa', '/register'), $payload)->assertRedirect($this->tenantUrl('testa', '/dashboard'));

    // Laravel's test client shares in-process Auth/session state across
    // sequential simulated requests — it doesn't model real browsers'
    // host-scoped cookies (which we verified manually with curl: a
    // tenant-A session cookie is never sent to tenant B). Without this
    // reset, the second /register call would 302 to testb's dashboard
    // without ever running the controller, since 'guest' middleware still
    // sees the (real, in-process) login from testa's request above.
    Auth::guard('web')->logout();
    $this->flushSession();

    $this->post($this->tenantUrl('testb', '/register'), $payload)->assertRedirect($this->tenantUrl('testb', '/dashboard'));

    tenancy()->initialize(App\Models\Tenant::find('testa'));
    expect(User::count())->toBe(1);
    expect(User::first()->email)->toBe('same@example.test');
    tenancy()->end();

    tenancy()->initialize(App\Models\Tenant::find('testb'));
    expect(User::count())->toBe(1);
    expect(User::first()->email)->toBe('same@example.test');
    tenancy()->end();
});

it('404s for a subdomain with no matching tenant instead of leaking central content', function () {
    $this->get($this->tenantUrl('no-such-tenant', '/'))->assertNotFound();
});
