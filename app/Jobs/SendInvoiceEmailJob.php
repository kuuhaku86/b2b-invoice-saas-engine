<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function handle(): void
    {
        if (! $this->invoice->client->email) {
            return;
        }

        Mail::to($this->invoice->client->email)->send(new InvoiceMail($this->invoice));

        $this->invoice->update(['sent_at' => now(), 'status' => 'sent']);
    }
}
