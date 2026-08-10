<?php

use App\Models\Client;
use Laravel\Dusk\Browser;

test('a tenant user can register, add a client, and create an invoice through the browser', function () {
    $tenant = $this->createTestTenant('e2ea');

    $this->browse(function (Browser $browser) use ($tenant) {
        $browser->visit($this->tenantUrl('e2ea', '/register'))
            ->type('name', 'Bob')
            ->type('email', 'bob@e2ea.test')
            ->type('password', 'password123')
            ->type('password_confirmation', 'password123')
            ->press('Register')
            ->assertPathIs('/dashboard')
            ->assertSee('e2ea');

        $browser->visit($this->tenantUrl('e2ea', '/clients/create'))
            ->type('name', 'Wayne Enterprises')
            ->type('email', 'billing@wayne.test')
            ->press('Create')
            ->assertPathIs('/clients')
            ->assertSee('Wayne Enterprises');

        tenancy()->initialize($tenant);
        $client = Client::first();
        tenancy()->end();

        $browser->visit($this->tenantUrl('e2ea', '/invoices/create'))
            ->select('client_id', (string) $client->id)
            ->type('items[0][description]', 'Consulting hours')
            ->type('items[0][quantity]', '5')
            ->type('items[0][unit_price]', '150')
            ->press('Create invoice')
            ->assertSee('INV-')
            ->assertSee('Consulting hours')
            ->assertSee('750.00'); // 5 * 150, no tax/discount configured

        $browser->visit($this->tenantUrl('e2ea', '/invoices'))
            ->assertSee('Wayne Enterprises')
            ->assertSee('750.00');
    });
});
