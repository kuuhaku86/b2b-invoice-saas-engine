<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mirrors the tenant DB's Subscription.status (see
        // ProcessStripeWebhookJob), so central analytics (MRR/ARR/churn) can
        // run as a single query here instead of initializing tenancy and
        // querying every tenant database individually.
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('subscription_status')->nullable()->after('stripe_subscription_id');
            $table->timestamp('subscription_cancelled_at')->nullable()->after('subscription_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['subscription_status', 'subscription_cancelled_at']);
        });
    }
};
