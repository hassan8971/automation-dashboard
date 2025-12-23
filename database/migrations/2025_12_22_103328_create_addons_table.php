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
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Arkade Bundle
            $table->string('slug')->unique();
            $table->unsignedBigInteger('price');
            $table->integer('duration_in_days'); // Metadata: Duration
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
        Schema::dropIfExists('addons');
    }
};
