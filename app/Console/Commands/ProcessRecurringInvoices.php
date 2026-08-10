<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\GenerateInvoicePdfJob;
use App\Jobs\SendInvoiceEmailJob;
use App\Models\RecurringInvoiceTemplate;
use App\Models\Tenant;
use App\Services\InvoiceFactory;
use App\Services\PlanQuotaChecker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class ProcessRecurringInvoices extends Command
{
    protected $signature = 'invoices:process-recurring';

    protected $description = 'Generate invoices for every due recurring invoice schedule, across all tenants';

    public function handle(InvoiceFactory $invoices, PlanQuotaChecker $quota): int
    {
        $today = today()->toDateString();
        $generated = 0;
        $skipped = 0;

        Tenant::all()->each(function (Tenant $tenant) use ($today, $invoices, $quota, &$generated, &$skipped) {
            tenancy()->initialize($tenant);

            try {
                RecurringInvoiceTemplate::query()
                    ->where('active', true)
                    ->where('next_run_date', '<=', $today)
                    ->with('client')
                    ->get()
                    ->each(function (RecurringInvoiceTemplate $template) use ($tenant, $invoices, $quota, &$generated, &$skipped) {
                        if ($quota->hasReachedLimit($tenant)) {
                            Log::warning("Skipping recurring invoice for tenant {$tenant->id}: plan quota reached.");
                            $skipped++;

                            return;
                        }

                        $invoice = $invoices->createFromItems(
                            $template->client,
                            $template->items,
                            (float) $template->discount_total,
                            now()->toDateString(),
                            now()->addDays(14)->toDateString(),
                        );

                        Bus::chain([
                            new GenerateInvoicePdfJob($invoice),
                            new SendInvoiceEmailJob($invoice),
                        ])->dispatch();

                        $template->advanceNextRunDate();
                        $generated++;
                    });
            } finally {
                tenancy()->end();
            }
        });

        $this->info("Generated {$generated} recurring invoice(s), skipped {$skipped} (quota reached).");

        return self::SUCCESS;
    }
}
