<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plan_changes', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_id')->index(); // Cashier subscription stripe_id
            $table->foreignId('from_plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->foreignId('to_plan_id')->constrained('plans')->cascadeOnDelete();
            $table->timestamp('changed_at');
            $table->string('source')->nullable(); // 'stripe_webhook', 'admin', etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_changes');
    }
};
