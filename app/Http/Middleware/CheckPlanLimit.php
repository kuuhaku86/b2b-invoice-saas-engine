<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Plan;
use App\Models\UsageCounter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimit
{
    /**
     * Gates invoice creation against the tenant's plan quota. The quota
     * comes from the tenant's assigned central Plan (set in central admin,
     * see App\Models\Tenant::plan()) — independent of Stripe subscription
     * status, so this works even for tenants with no billing history yet.
     * A tenant with no plan assigned is treated as if on the Free plan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();
        $quota = $tenant->plan?->invoice_quota
            ?? Plan::where('slug', 'free')->value('invoice_quota');

        if ($quota === null) {
            return $next($request); // null quota = unlimited
        }

        $period = now()->startOfMonth()->toDateString();
        $used = UsageCounter::where('period', $period)->value('invoices_created') ?? 0;

        if ($used >= $quota) {
            return back()->withErrors([
                'plan_limit' => "You've reached your plan's monthly invoice limit ({$quota}). Upgrade your plan to create more invoices.",
            ])->withInput();
        }

        return $next($request);
    }
}
