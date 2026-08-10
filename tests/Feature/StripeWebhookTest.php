<?php

use App\Models\WebhookEvent;

function signedStripeRequest(array $payload): array
{
    $body = json_encode($payload);
    $secret = config('services.stripe.webhook_secret');
    $timestamp = time();
    $signature = hash_hmac('sha256', "{$timestamp}.{$body}", $secret);

    return [$body, "t={$timestamp},v1={$signature}"];
}

it('rejects a webhook with an invalid signature', function () {
    [$body] = signedStripeRequest(['id' => 'evt_1', 'type' => 'invoice.payment_succeeded', 'data' => ['object' => []]]);

    $this->call('POST', '/stripe/webhook', [], [], [], [
        'HTTP_HOST' => env('CENTRAL_DOMAIN', 'saas.test'),
        'HTTP_Stripe-Signature' => 't=123,v1=deadbeef',
        'CONTENT_TYPE' => 'application/json',
    ], $body)->assertStatus(400);
});

it('processes a valid webhook and updates the tenant subscription', function () {
    $tenant = $this->createTestTenant('teststripe', ['stripe_customer_id' => 'cus_test_1']);

    [$body, $signature] = signedStripeRequest([
        'id' => 'evt_success_1',
        'type' => 'invoice.payment_succeeded',
        'data' => ['object' => [
            'customer' => 'cus_test_1',
            'subscription' => 'sub_test_1',
            'lines' => ['data' => [['period' => ['end' => now()->addDays(30)->timestamp]]]],
        ]],
    ]);

    $this->call('POST', '/stripe/webhook', [], [], [], [
        'HTTP_HOST' => env('CENTRAL_DOMAIN', 'saas.test'),
        'HTTP_Stripe-Signature' => $signature,
        'CONTENT_TYPE' => 'application/json',
    ], $body)->assertOk();

    expect($tenant->refresh()->subscription_status)->toBe('active');

    tenancy()->initialize($tenant);
    expect(App\Models\Subscription::first()->status)->toBe('active');
    tenancy()->end();
});

it('processes the same event id only once (idempotency)', function () {
    $tenant = $this->createTestTenant('testidempotent', ['stripe_customer_id' => 'cus_test_2']);

    [$body, $signature] = signedStripeRequest([
        'id' => 'evt_replay_1',
        'type' => 'invoice.payment_failed',
        'data' => ['object' => ['customer' => 'cus_test_2', 'subscription' => 'sub_test_2']],
    ]);

    $headers = [
        'HTTP_HOST' => env('CENTRAL_DOMAIN', 'saas.test'),
        'HTTP_Stripe-Signature' => $signature,
        'CONTENT_TYPE' => 'application/json',
    ];

    $this->call('POST', '/stripe/webhook', [], [], [], $headers, $body)->assertOk();
    expect($tenant->refresh()->subscription_status)->toBe('past_due');

    // A different event flips the status away from past_due...
    tenancy()->initialize($tenant);
    App\Models\Subscription::first()->update(['status' => 'active']);
    tenancy()->end();
    $tenant->update(['subscription_status' => 'active']);

    // ...then the FIRST event is replayed (same id, same signature window).
    // If it were reprocessed, it would flip status back to past_due.
    $this->call('POST', '/stripe/webhook', [], [], [], $headers, $body)->assertOk();

    expect(WebhookEvent::where('stripe_event_id', 'evt_replay_1')->count())->toBe(1);
    expect($tenant->refresh()->subscription_status)->toBe('active');
});
