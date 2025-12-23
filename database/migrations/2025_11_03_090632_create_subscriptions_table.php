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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Sibaneh Pro Subscription
            $table->string('slug')->unique(); // e.g., sibaneh-pro
            $table->unsignedBigInteger('price'); // Stored in smallest unit (e.g., Tomans/Rials)
            $table->integer('duration_in_days'); // e.g., 30, 90, 365
            $table->text('description')->nullable();
            $table->foreignId('gift_id')->nullable()->constrained('gifts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
