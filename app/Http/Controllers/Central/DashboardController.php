<?php

declare(strict_types=1);

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Services\CentralAnalytics;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(CentralAnalytics $analytics): Response
    {
        return Inertia::render('Central/Dashboard', [
            'mrr' => $analytics->mrr(),
            'arr' => $analytics->arr(),
            'activeSubscribers' => $analytics->activeTenantCount(),
            'churnRate' => $analytics->churnRate(),
        ]);
    }
}
