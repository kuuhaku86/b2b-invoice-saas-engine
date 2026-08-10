<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'price' => 0,
                'invoice_quota' => 10,
                'features' => ['pdf_invoices', 'email_notifications'],
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'price' => 29,
                'invoice_quota' => 100,
                'features' => ['pdf_invoices', 'email_notifications', 'recurring_invoices'],
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 99,
                'invoice_quota' => null, // unlimited
                'features' => ['pdf_invoices', 'email_notifications', 'recurring_invoices', 'analytics_dashboard'],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
