<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $statusCounts = Invoice::query()
            ->selectRaw('status, count(*) as count, sum(total) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return Inertia::render('Tenant/Dashboard', [
            'tenantId' => tenant('id'),
            'user' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ],
            'invoiceStatusBreakdown' => $statusCounts->map(fn ($row) => [
                'count' => (int) $row->count,
                'total' => (float) $row->total,
            ]),
        ]);
    }
}
