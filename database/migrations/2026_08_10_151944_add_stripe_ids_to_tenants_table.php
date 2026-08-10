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
        // Central index mapping Stripe identifiers to tenants, so the
        // webhook handler (which has no subdomain to resolve a tenant from)
        // can find the right tenant by customer id. The detailed
        // subscription record itself lives per-tenant in the tenant DB's
        // `subscriptions` table.
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->unique()->after('plan_id');
            $table->string('stripe_subscription_id')->nullable()->unique()->after('stripe_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['stripe_customer_id', 'stripe_subscription_id']);
        });
    }
};
