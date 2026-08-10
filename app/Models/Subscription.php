<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'plan_id', 'stripe_customer_id', 'stripe_subscription_id',
        'status', 'current_period_ends_at',
    ];

    protected $casts = [
        'current_period_ends_at' => 'datetime',
    ];

    // plan_id points at the central `plans` table — no real FK across
    // databases, so this is a manual lookup rather than a relation.
    public function plan(): ?Plan
    {
        return $this->plan_id ? Plan::find($this->plan_id) : null;
    }
}
