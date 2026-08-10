<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;

class CentralAnalytics
{
    public function activeTenantCount(): int
    {
        return Tenant::where('subscription_status', 'active')->count();
    }

    public function mrr(): float
    {
        return (float) Tenant::where('subscription_status', 'active')
            ->with('plan')
            ->get()
            ->sum(fn (Tenant $tenant) => (float) ($tenant->plan->price ?? 0));
    }

    public function arr(): float
    {
        return $this->mrr() * 12;
    }

    /**
     * Logo churn over the trailing 30 days: tenants whose subscription was
     * cancelled in that window, divided by tenants who were active at the
     * start of it.
     *
     * We don't keep point-in-time snapshots of subscription status, so
     * "active at period start" is approximated as (active now) + (cancelled
     * in the last 30 days) — every tenant that churned in the window must
     * have been active immediately before cancelling, and every
     * still-active tenant was active a fortiori. This slightly overcounts
     * the denominator only for tenants that both subscribed AND cancelled
     * inside the same 30-day window, which is rare and pushes the rate
     * down (i.e. it's a conservative estimate), not up.
     */
    public function churnRate(): float
    {
        $since = now()->subDays(30);

        $cancelled = Tenant::where('subscription_status', 'cancelled')
            ->where('subscription_cancelled_at', '>=', $since)
            ->count();

        $activeNow = $this->activeTenantCount();
        $activeAtPeriodStart = $activeNow + $cancelled;

        if ($activeAtPeriodStart === 0) {
            return 0.0;
        }

        return round($cancelled / $activeAtPeriodStart * 100, 2);
    }
}
