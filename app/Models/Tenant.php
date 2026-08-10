<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    // Real, queryable columns on the tenants table. Anything not listed here
    // (e.g. ->name) is transparently stored in the `data` JSON column instead
    // — see database/migrations/2019_09_15_000010_create_tenants_table.php.
    public static function getCustomColumns(): array
    {
        return array_merge(parent::getCustomColumns(), [
            'plan_id',
        ]);
    }
}
