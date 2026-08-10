<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** @var array<int, string> */
    private array $createdTenantIds = [];

    /**
     * Provisions a real tenant database (via the same TenantCreated event
     * pipeline the central admin UI uses) and registers it for teardown.
     */
    protected function createTestTenant(string $id, array $attributes = []): Tenant
    {
        $tenant = Tenant::create(array_merge(['id' => $id], $attributes));
        $tenant->domains()->create(['domain' => $id]);
        $this->createdTenantIds[] = $id;

        return $tenant;
    }

    protected function tenantUrl(string $tenantId, string $path = '/'): string
    {
        return 'http://' . $tenantId . '.' . env('CENTRAL_DOMAIN', 'saas.test') . $path;
    }

    protected function tearDown(): void
    {
        foreach ($this->createdTenantIds as $id) {
            // Drops the tenant's physical database — it was never part of
            // any outer transaction (CREATE/DROP DATABASE isn't
            // transactional in MySQL), so this explicit cleanup is required
            // regardless of RefreshDatabase on the central connection.
            Tenant::find($id)?->delete();
        }

        $this->createdTenantIds = [];

        parent::tearDown();
    }
}
