<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(): View
    {
        return view('tenant.billing.index', [
            'tenant' => tenant(),
            'plans' => Plan::orderBy('price')->get(),
            'subscription' => Subscription::latest()->first(),
        ]);
    }

    public function checkout(Plan $plan, StripeService $stripe): RedirectResponse
    {
        $session = $stripe->createCheckoutSession(
            tenant(),
            $plan,
            successUrl: route('tenant.billing.index') . '?checkout=success',
            cancelUrl: route('tenant.billing.index') . '?checkout=cancelled',
        );

        return redirect()->away($session->url);
    }
}
