<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->string('key');
            $table->enum('type', ['boolean', 'numeric']);
            $table->string('value'); // 'true'/'false' or a number as string
            $table->string('label')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};
