<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicePdfGenerator
{
    /**
     * Render the invoice to PDF and store it on the default disk.
     * Storage paths are automatically tenant-scoped by
     * FilesystemTenancyBootstrapper, so no manual tenant prefixing here.
     */
    public function generate(Invoice $invoice): string
    {
        $invoice->loadMissing('client', 'items');

        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $invoice]);

        $path = "invoices/{$invoice->invoice_number}.pdf";

        Storage::put($path, $pdf->output());

        return $path;
    }
}
