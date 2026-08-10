<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateInvoicePdfJob;
use App\Jobs\SendInvoiceEmailJob;
use App\Models\Client;
use App\Models\Invoice;
use App\Services\InvoiceFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        return view('tenant.invoices.index', [
            'invoices' => Invoice::with('client')->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function create(): View
    {
        return view('tenant.invoices.create', [
            'clients' => Client::orderBy('name')->get(),
        ]);
    }

    public function show(Invoice $invoice): View
    {
        return view('tenant.invoices.show', [
            'invoice' => $invoice->load('client', 'items'),
        ]);
    }

    public function store(Request $request, InvoiceFactory $invoices): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'discount_total' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $items = array_values(array_filter(
            $validated['items'],
            fn ($item) => ! empty($item['description'])
        ));

        if (empty($items)) {
            return back()->withErrors(['items' => 'Add at least one line item.'])->withInput();
        }

        $invoice = $invoices->createFromItems(
            Client::findOrFail($validated['client_id']),
            $items,
            (float) ($validated['discount_total'] ?? 0),
            $validated['issue_date'],
            $validated['due_date'],
        );

        // GenerateInvoicePdfJob writes invoice->pdf_path before
        // SendInvoiceEmailJob runs — chained jobs run sequentially, and
        // SerializesModels re-fetches a fresh copy of $invoice in each job.
        Bus::chain([
            new GenerateInvoicePdfJob($invoice),
            new SendInvoiceEmailJob($invoice),
        ])->dispatch();

        return redirect()->route('tenant.invoices.show', $invoice)
            ->with('status', "Invoice {$invoice->invoice_number} created. PDF and email are being generated in the background.");
    }
}
