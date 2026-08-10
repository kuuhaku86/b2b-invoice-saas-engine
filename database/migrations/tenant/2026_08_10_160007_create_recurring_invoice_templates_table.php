<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_invoice_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            // [{description, quantity, unit_price, tax_rate}, ...] — a
            // blueprint, not queried per-line, so a JSON blob is simpler
            // than a child items table here.
            $table->json('items');
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->string('interval'); // 'weekly' or 'monthly'
            $table->date('next_run_date');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_invoice_templates');
    }
};
