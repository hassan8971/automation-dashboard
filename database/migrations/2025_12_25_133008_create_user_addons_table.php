<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('addon_id')->constrained('addons'); // Assuming you have an 'addons' table
            
            // Snapshot of price at purchase
            $table->unsignedBigInteger('price_paid');
            
            // Status: active (owned), consumed (used up), expired
            $table->string('status')->default('active');
            
            // Optional: If add-ons expire (e.g. "Speed Boost for 24h")
            $table->dateTime('expires_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addons');
    }
};