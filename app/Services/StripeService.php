<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plan;
use App\Models\Tenant;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create (or reuse) the Stripe Customer for a tenant, then start a
     * Checkout Session for a subscription to the given plan.
     */
    public function createCheckoutSession(Tenant $tenant, Plan $plan, string $successUrl, string $cancelUrl): Session
    {
        if (! $tenant->stripe_customer_id) {
            $customer = $this->client->customers->create([
                'name' => $tenant->name,
                'metadata' => ['tenant_id' => $tenant->id],
            ]);

            $tenant->update(['stripe_customer_id' => $customer->id]);
        }

        return $this->client->checkout->sessions->create([
            'customer' => $tenant->stripe_customer_id,
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }

    public function cancelSubscription(Tenant $tenant): void
    {
        if ($tenant->stripe_subscription_id) {
            $this->client->subscriptions->cancel($tenant->stripe_subscription_id);
        }
    }
}
