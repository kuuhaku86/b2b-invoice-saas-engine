<?php

use App\Jobs\GenerateInvoicePdfJob;
use App\Jobs\SendInvoiceEmailJob;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\User;
use App\Models\UsageCounter;
use Illuminate\Support\Facades\Bus;

function invoicePayload(string $description): array
{
    return [
        'client_id' => 1,
        'issue_date' => now()->toDateString(),
        'due_date' => now()->addDays(14)->toDateString(),
        'items' => [['description' => $description, 'quantity' => 1, 'unit_price' => 10]],
    ];
}

it('allows an invoice exactly at quota - 1, then blocks the one that would exceed it', function () {
    $plan = Plan::where('slug', 'free')->first(); // quota: 10
    $tenant = $this->createTestTenant('testquota', ['plan_id' => $plan->id]);

    tenancy()->initialize($tenant);
    Client::create(['name' => 'A Client']);
    $user = User::create(['name' => 'Test', 'email' => 'test@tenant.test', 'password' => bcrypt('password')]);
    UsageCounter::create(['period' => now()->startOfMonth()->toDateString(), 'invoices_created' => 9]);
    tenancy()->end();

    // Faked *after* tenant provisioning: that pipeline also runs via
    // dispatch_sync() (see JobPipeline::toListener()), so an unscoped
    // Bus::fake() would silently skip creating the tenant database too.
    Bus::fake([GenerateInvoicePdfJob::class, SendInvoiceEmailJob::class]);

    $this->actingAs($user, 'web');

    // 10th invoice: under quota, should succeed.
    $this->post($this->tenantUrl('testquota', '/invoices'), invoicePayload('10th'))
        ->assertRedirect();

    tenancy()->initialize($tenant);
    expect(Invoice::count())->toBe(1);
    tenancy()->end();

    // 11th invoice: now at quota, should be blocked.
    $this->post($this->tenantUrl('testquota', '/invoices'), invoicePayload('11th'))
        ->assertSessionHasErrors('plan_limit');

    tenancy()->initialize($tenant);
    expect(Invoice::count())->toBe(1); // unchanged
    tenancy()->end();
});

it('blocks invoice creation while the subscription is past_due, regardless of quota', function () {
    $tenant = $this->createTestTenant('testpastdue');

    tenancy()->initialize($tenant);
    Client::create(['name' => 'A Client']);
    $user = User::create(['name' => 'Test', 'email' => 'test@tenant.test', 'password' => bcrypt('password')]);
    App\Models\Subscription::create(['status' => 'past_due']);
    tenancy()->end();

    Bus::fake([GenerateInvoicePdfJob::class, SendInvoiceEmailJob::class]);

    $this->actingAs($user, 'web');

    $this->post($this->tenantUrl('testpastdue', '/invoices'), invoicePayload('blocked'))
        ->assertSessionHasErrors('plan_limit');

    tenancy()->initialize($tenant);
    expect(Invoice::count())->toBe(0);
    tenancy()->end();
});
