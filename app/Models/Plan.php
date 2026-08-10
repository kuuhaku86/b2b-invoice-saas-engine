<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Plan extends Model
{
    // Plans must always be read from the central DB, even while a tenant
    // connection is active (e.g. feature-gating checks running mid-request
    // on a tenant subdomain).
    use CentralConnection;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'invoice_quota',
        'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
    ];

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }
}
