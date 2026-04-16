<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('provider_invoice_id')->unique();
            $table->morphs('billable'); // billable_type, billable_id
            $table->string('provider_subscription_id')->nullable()->index();
            $table->unsignedBigInteger('amount_due');   // in cents
            $table->unsignedBigInteger('amount_paid');  // in cents
            $table->char('currency', 3)->default('USD');
            $table->string('status'); // 'draft', 'open', 'paid', 'uncollectible', 'void'
            $table->string('invoice_pdf')->nullable();
            $table->string('invoice_number')->nullable();
            $table->timestamp('billing_period_start')->nullable();
            $table->timestamp('billing_period_end')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_invoices');
    }
};
