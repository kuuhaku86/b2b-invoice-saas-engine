<p>Hi {{ $invoice->client->name }},</p>

<p>Please find attached invoice {{ $invoice->invoice_number }} for {{ $invoice->currency }} {{ number_format($invoice->total, 2) }}, due {{ $invoice->due_date->toFormattedDateString() }}.</p>

<p>Thanks!</p>
