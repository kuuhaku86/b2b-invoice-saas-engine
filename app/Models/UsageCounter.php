<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageCounter extends Model
{
    protected $fillable = ['period', 'invoices_created'];

    protected $casts = [
        'period' => 'date',
    ];
}
