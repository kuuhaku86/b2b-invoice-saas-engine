<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\RecurringInvoiceTemplate;
use App\Jobs\GenerateInvoicePdfJob;
use App\Jobs\SendInvoiceEmailJob;
use Illuminate\Support\Facades\Bus;

it('generates an invoice for a due recurring template and advances its next run date', function () {
    $tenant = $this->createTestTenant('testrecurring');

    tenancy()->initialize($tenant);
    $client = Client::create(['name' => 'Recurring Client']);
    $template = RecurringInvoiceTemplate::create([
        'client_id' => $client->id,
        'items' => [['description' => 'Retainer', 'quantity' => 1, 'unit_price' => 200, 'tax_rate' => 10]],
        'discount_total' => 0,
        'interval' => 'monthly',
        'next_run_date' => now()->subDay()->toDateString(),
    ]);
    tenancy()->end();

    Bus::fake([GenerateInvoicePdfJob::class, SendInvoiceEmailJob::class]);
    $this->artisan('invoices:process-recurring')->assertSuccessful();

    tenancy()->initialize($tenant);
    expect(Invoice::count())->toBe(1);

    $invoice = Invoice::first();
    expect((float) $invoice->subtotal)->toBe(200.0);
    expect((float) $invoice->tax_total)->toBe(20.0);
    expect((float) $invoice->total)->toBe(220.0);

    $template->refresh();
    expect($template->next_run_date->toDateString())
        ->toBe(now()->subDay()->addMonthNoOverflow()->toDateString());
    tenancy()->end();
});

it('does not generate a duplicate invoice when run again before the next due date', function () {
    $tenant = $this->createTestTenant('testrecurring2');

    tenancy()->initialize($tenant);
    $client = Client::create(['name' => 'Recurring Client']);
    RecurringInvoiceTemplate::create([
        'client_id' => $client->id,
        'items' => [['description' => 'Retainer', 'quantity' => 1, 'unit_price' => 100]],
        'discount_total' => 0,
        'interval' => 'monthly',
        'next_run_date' => now()->subDay()->toDateString(),
    ]);
    tenancy()->end();

    Bus::fake([GenerateInvoicePdfJob::class, SendInvoiceEmailJob::class]);
    $this->artisan('invoices:process-recurring')->assertSuccessful();
    $this->artisan('invoices:process-recurring')->assertSuccessful();

    tenancy()->initialize($tenant);
    expect(Invoice::count())->toBe(1);
    tenancy()->end();
});

it('skips generation once the tenant has reached its plan quota', function () {
    $plan = \App\Models\Plan::where('slug', 'free')->first(); // quota: 10
    $tenant = $this->createTestTenant('testrecurringquota', ['plan_id' => $plan->id]);

    tenancy()->initialize($tenant);
    $client = Client::create(['name' => 'Recurring Client']);
    RecurringInvoiceTemplate::create([
        'client_id' => $client->id,
        'items' => [['description' => 'Retainer', 'quantity' => 1, 'unit_price' => 100]],
        'discount_total' => 0,
        'interval' => 'monthly',
        'next_run_date' => now()->subDay()->toDateString(),
    ]);
    \App\Models\UsageCounter::create([
        'period' => now()->startOfMonth()->toDateString(),
        'invoices_created' => 10,
    ]);
    tenancy()->end();

    Bus::fake([GenerateInvoicePdfJob::class, SendInvoiceEmailJob::class]);
    $this->artisan('invoices:process-recurring')->assertSuccessful();

    tenancy()->initialize($tenant);
    expect(Invoice::count())->toBe(0);
    tenancy()->end();
});
