<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Assuming your products table is named 'products'
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            
            // Optional: Store how much they paid (useful for refunds/analytics)
            $table->decimal('price_paid', 15, 0)->default(0);
            $table->string('purchase_method')->default('normal');
            
            $table->timestamps();
            
            // Ensure a user can't buy the same app twice
            $table->unique(['user_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_apps');
    }
};