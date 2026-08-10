<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\UsageCounter;

class PlanQuotaChecker
{
    /**
     * Shared by CheckPlanLimit (HTTP) and ProcessRecurringInvoices (CLI) so
     * both enforce the exact same monthly invoice quota. A tenant with no
     * plan assigned is treated as if on the Free plan.
     */
    public function quotaFor(Tenant $tenant): ?int
    {
        return $tenant->plan?->invoice_quota
            ?? Plan::where('slug', 'free')->value('invoice_quota');
    }

    public function hasReachedLimit(Tenant $tenant): bool
    {
        $quota = $this->quotaFor($tenant);

        if ($quota === null) {
            return false;
        }

        $period = now()->startOfMonth()->toDateString();
        $used = UsageCounter::where('period', $period)->value('invoices_created') ?? 0;

        return $used >= $quota;
    }
}
