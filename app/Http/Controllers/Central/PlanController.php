<?php

declare(strict_types=1);

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        return view('central.plans.index', [
            'plans' => Plan::withCount('tenants')->orderBy('price')->get(),
        ]);
    }

    public function create(): View
    {
        return view('central.plans.create', ['plan' => new Plan()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        Plan::create($validated + ['slug' => Str::slug($validated['name'])]);

        return redirect()->route('central.plans.index')->with('status', 'Plan created.');
    }

    public function edit(Plan $plan): View
    {
        return view('central.plans.edit', ['plan' => $plan]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $plan->update($this->validated($request));

        return redirect()->route('central.plans.index')->with('status', 'Plan updated.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()->route('central.plans.index')->with('status', 'Plan deleted. Tenants on this plan are now unassigned.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'invoice_quota' => ['nullable', 'integer', 'min:1'],
        ]);
    }
}
