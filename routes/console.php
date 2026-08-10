<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// withoutOverlapping() guards against a slow run (e.g. many tenants, slow
// PDF generation) still executing when the next scheduled tick fires.
Schedule::command('invoices:process-recurring')
    ->dailyAt('01:00')
    ->withoutOverlapping();
