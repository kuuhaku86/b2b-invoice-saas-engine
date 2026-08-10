<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Subscription;
use App\Services\PlanQuotaChecker;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimit
{
    public function __construct(private PlanQuotaChecker $quota) {}

    /**
     * Gates invoice creation against the tenant's plan quota, and against a
     * past-due subscription (set by the Stripe webhook handler on a failed
     * payment — see ProcessStripeWebhookJob).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $subscription = Subscription::latest()->first();

        if ($subscription?->status === 'past_due') {
            return back()->withErrors([
                'plan_limit' => 'Your last payment failed. Please update your payment method to keep creating invoices.',
            ])->withInput();
        }

        if ($this->quota->hasReachedLimit(tenant())) {
            $limit = $this->quota->quotaFor(tenant());

            return back()->withErrors([
                'plan_limit' => "You've reached your plan's monthly invoice limit ({$limit}). Upgrade your plan to create more invoices.",
            ])->withInput();
        }

        return $next($request);
    }
}
