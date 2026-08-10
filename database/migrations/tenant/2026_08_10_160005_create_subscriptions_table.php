<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tracks this tenant's subscription to OUR platform (not the tenant's
        // own customers). plan_id references the central `plans` table by id,
        // but without a real FK constraint — cross-database references can't
        // be enforced at the DB level in a database-per-tenant setup.
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->string('status')->default('trialing'); // trialing, active, past_due, cancelled
            $table->timestamp('current_period_ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
