<?php

declare(strict_types=1);

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(): View
    {
        return view('central.tenants.index', [
            'tenants' => Tenant::with(['domains', 'plan'])->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function create(): View
    {
        return view('central.tenants.create', ['plans' => Plan::orderBy('price')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => [
                'required', 'string', 'max:63', 'alpha_dash', 'lowercase',
                Rule::notIn(config('tenancy.central_domains')),
                Rule::unique('domains', 'domain'),
            ],
            'plan_id' => ['nullable', Rule::exists('plans', 'id')],
        ]);

        // Synchronous by default (see TenancyServiceProvider::events()): the
        // request blocks until the tenant DB is created and migrated.
        $tenant = Tenant::create([
            'id' => $validated['subdomain'],
            'name' => $validated['name'],
            'plan_id' => $validated['plan_id'] ?? null,
        ]);

        $tenant->domains()->create(['domain' => $validated['subdomain']]);

        $host = $validated['subdomain'] . '.' . env('CENTRAL_DOMAIN', 'saas.test');

        return redirect()->route('central.tenants.index')->with('status', "Tenant \"{$validated['name']}\" created at {$host}.");
    }

    public function edit(Tenant $tenant): View
    {
        return view('central.tenants.edit', [
            'tenant' => $tenant,
            'plans' => Plan::orderBy('price')->get(),
        ]);
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'plan_id' => ['nullable', Rule::exists('plans', 'id')],
        ]);

        $tenant->update($validated);

        return redirect()->route('central.tenants.index')->with('status', 'Tenant updated.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        // Triggers the TenantDeleted -> DeleteDatabase job pipeline, which
        // drops the tenant's database synchronously (see TenancyServiceProvider).
        $tenant->delete();

        return redirect()->route('central.tenants.index')->with('status', 'Tenant and its database were deleted.');
    }
}
