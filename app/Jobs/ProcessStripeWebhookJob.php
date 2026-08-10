<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessStripeWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array  $event  Decoded Stripe Event (already signature-verified
     *                        by StripeWebhookController before this was dispatched).
     */
    public function __construct(public array $event) {}

    public function handle(): void
    {
        $type = $this->event['type'];
        $object = $this->event['data']['object'];
        $customerId = $object['customer'] ?? null;

        if (! $customerId) {
            Log::info("Stripe webhook [{$type}] had no customer id — ignoring.");

            return;
        }

        $tenant = Tenant::where('stripe_customer_id', $customerId)->first();

        if (! $tenant) {
            Log::warning("Stripe webhook [{$type}] for unknown customer {$customerId} — ignoring.");

            return;
        }

        match ($type) {
            'invoice.payment_succeeded' => $this->handlePaymentSucceeded($tenant, $object),
            'invoice.payment_failed' => $this->handlePaymentFailed($tenant, $object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($tenant, $object),
            default => Log::info("Stripe webhook [{$type}] has no handler — ignoring."),
        };
    }

    private function handlePaymentSucceeded(Tenant $tenant, array $invoice): void
    {
        $subscriptionId = $invoice['subscription'] ?? null;
        $periodEnd = isset($invoice['lines']['data'][0]['period']['end'])
            ? now()->setTimestamp($invoice['lines']['data'][0]['period']['end'])
            : null;

        if ($subscriptionId) {
            $tenant->update(['stripe_subscription_id' => $subscriptionId]);
        }

        tenancy()->initialize($tenant);

        try {
            Subscription::updateOrCreate(
                ['stripe_subscription_id' => $subscriptionId],
                [
                    'plan_id' => $tenant->plan_id,
                    'stripe_customer_id' => $tenant->stripe_customer_id,
                    'status' => 'active',
                    'current_period_ends_at' => $periodEnd,
                ]
            );
        } finally {
            tenancy()->end();
        }
    }

    private function handlePaymentFailed(Tenant $tenant, array $invoice): void
    {
        $subscriptionId = $invoice['subscription'] ?? $tenant->stripe_subscription_id;

        tenancy()->initialize($tenant);

        try {
            Subscription::updateOrCreate(
                ['stripe_subscription_id' => $subscriptionId],
                [
                    'plan_id' => $tenant->plan_id,
                    'stripe_customer_id' => $tenant->stripe_customer_id,
                    'status' => 'past_due',
                ]
            );
        } finally {
            tenancy()->end();
        }
    }

    private function handleSubscriptionDeleted(Tenant $tenant, array $subscription): void
    {
        tenancy()->initialize($tenant);

        try {
            Subscription::where('stripe_subscription_id', $subscription['id'])
                ->update(['status' => 'cancelled']);
        } finally {
            tenancy()->end();
        }
    }
}
