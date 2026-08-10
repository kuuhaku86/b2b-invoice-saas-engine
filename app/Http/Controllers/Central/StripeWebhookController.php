<?php

declare(strict_types=1);

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessStripeWebhookJob;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    /**
     * Stripe posts to this single fixed URL regardless of tenant — there's
     * no subdomain to resolve tenancy from. The event payload's `customer`
     * id is used downstream (in the job) to look up the tenant via the
     * central tenants.stripe_customer_id index.
     */
    public function __invoke(Request $request): Response
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
                config('services.stripe.webhook_secret'),
            );
        } catch (SignatureVerificationException|\UnexpectedValueException) {
            return response('Invalid signature', 400);
        }

        // Idempotency: Stripe retries on timeout/non-2xx, and the same
        // event can be delivered more than once. The unique constraint on
        // stripe_event_id is the actual dedupe guarantee — the query is
        // just an early exit to skip work on the common case.
        if (WebhookEvent::where('stripe_event_id', $event->id)->exists()) {
            return response('Already processed', 200);
        }

        try {
            WebhookEvent::create(['stripe_event_id' => $event->id, 'type' => $event->type]);
        } catch (\Illuminate\Database\QueryException) {
            // Lost the race with a concurrent delivery of the same event —
            // the other request's job is already queued.
            return response('Already processed', 200);
        }

        ProcessStripeWebhookJob::dispatch($event->toArray());

        return response('OK', 200);
    }
}
