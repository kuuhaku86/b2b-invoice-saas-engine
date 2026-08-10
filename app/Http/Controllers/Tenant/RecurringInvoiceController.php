<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\RecurringInvoiceTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecurringInvoiceController extends Controller
{
    public function index(): View
    {
        return view('tenant.recurring.index', [
            'templates' => RecurringInvoiceTemplate::with('client')->orderBy('next_run_date')->get(),
        ]);
    }

    public function create(): View
    {
        return view('tenant.recurring.create', [
            'clients' => Client::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'description' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_total' => ['nullable', 'numeric', 'min:0'],
            'interval' => ['required', 'in:weekly,monthly'],
            'next_run_date' => ['required', 'date'],
        ]);

        RecurringInvoiceTemplate::create([
            'client_id' => $validated['client_id'],
            'items' => [[
                'description' => $validated['description'],
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'tax_rate' => $validated['tax_rate'] ?? 0,
            ]],
            'discount_total' => $validated['discount_total'] ?? 0,
            'interval' => $validated['interval'],
            'next_run_date' => $validated['next_run_date'],
        ]);

        return redirect()->route('tenant.recurring.index')->with('status', 'Recurring invoice schedule created.');
    }

    public function destroy(RecurringInvoiceTemplate $recurring): RedirectResponse
    {
        $recurring->delete();

        return redirect()->route('tenant.recurring.index')->with('status', 'Recurring invoice schedule deleted.');
    }
}
