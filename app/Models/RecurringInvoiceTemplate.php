<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringInvoiceTemplate extends Model
{
    protected $fillable = ['client_id', 'items', 'discount_total', 'interval', 'next_run_date', 'active'];

    protected $casts = [
        'items' => 'array',
        'next_run_date' => 'date',
        'active' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function advanceNextRunDate(): void
    {
        $this->update([
            'next_run_date' => match ($this->interval) {
                'weekly' => $this->next_run_date->copy()->addWeek(),
                default => $this->next_run_date->copy()->addMonthNoOverflow(),
            },
        ]);
    }
}
