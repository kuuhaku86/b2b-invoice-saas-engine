<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\UsageCounter;
use Illuminate\Support\Facades\DB;

class InvoiceFactory
{
    /**
     * Create an invoice with line items from raw item rows, computing
     * per-line tax and the invoice's totals. Shared by manual invoice
     * creation (Tenant\InvoiceController) and the recurring-invoice command,
     * so both compute totals identically.
     *
     * @param  array<int, array{description: string, quantity: float|string, unit_price: float|string, tax_rate?: float|string}>  $items
     */
    public function createFromItems(
        Client $client,
        array $items,
        float $discountTotal,
        string $issueDate,
        string $dueDate,
    ): Invoice {
        return DB::transaction(function () use ($client, $items, $discountTotal, $issueDate, $dueDate) {
            $subtotal = 0;
            $taxTotal = 0;
            $rows = [];

            foreach ($items as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $taxRate = (float) ($item['tax_rate'] ?? 0);

                $lineSubtotal = round($quantity * $unitPrice, 2);
                $lineTax = round($lineSubtotal * $taxRate / 100, 2);

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;

                $rows[] = [
                    'description' => $item['description'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_rate' => $taxRate,
                    'total' => $lineSubtotal + $lineTax,
                ];
            }

            $invoice = Invoice::create([
                'client_id' => $client->id,
                'invoice_number' => 'PENDING',
                'status' => 'draft',
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'discount_total' => $discountTotal,
                'total' => $subtotal + $taxTotal - $discountTotal,
            ]);

            $invoice->update(['invoice_number' => 'INV-' . str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT)]);
            $invoice->items()->createMany($rows);

            // Counters are keyed by calendar month, so a new month starts
            // fresh automatically — no separate reset job needed.
            $period = now()->startOfMonth()->toDateString();
            UsageCounter::firstOrCreate(['period' => $period])->increment('invoices_created');

            return $invoice;
        });
    }
}
