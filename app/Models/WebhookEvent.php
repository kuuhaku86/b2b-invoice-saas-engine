<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class WebhookEvent extends Model
{
    // Idempotency ledger for inbound provider webhooks. Central, not
    // per-tenant: the event arrives before we know which tenant it's for.
    use CentralConnection;

    protected $fillable = ['stripe_event_id', 'type'];
}
