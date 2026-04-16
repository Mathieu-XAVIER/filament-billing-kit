<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_custom_quote')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('marketing_badge')->nullable();
            $table->unsignedBigInteger('price_monthly')->nullable(); // en centimes
            $table->unsignedBigInteger('price_yearly')->nullable();  // en centimes
            $table->char('currency', 3)->default('EUR');
            $table->unsignedSmallInteger('trial_days')->nullable();
            $table->string('provider_price_id_monthly')->nullable()->index();
            $table->string('provider_price_id_yearly')->nullable()->index();
            $table->string('contact_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
