<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Admin-facing name e.g., "Free Pro Month"
            
            // What type of gift is this?
            // 'subscription', 'app', 'redeem_code', 'addon', 'custom'
            $table->string('type'); 
            
            // --- THE REWARD (What user gets) ---
            // If type='subscription', this links to subscriptions table
            // If type='app', this links to apps table
            $table->nullableMorphs('rewardable'); 
            
            // Extra data for things like 'redeem_code' or 'custom' text
            $table->text('payload')->nullable(); 

            // --- THE TRIGGER (With WHAT is this offered?) ---
            // e.g., Linked to Subscription ID 1 (Gold Plan)
            $table->nullableMorphs('triggerable');

            // --- تنظیمات مخصوص Redeem Code ---
            // وقتی نوع redeem_code انتخاب شود، این‌ها پر می‌شوند تا سیستم بداند چه کدی بسازد
            $table->unsignedBigInteger('generated_amount')->nullable(); // مبلغ کد تولیدی
            $table->string('generated_service_type')->nullable(); // سرویس کد تولیدی
            $table->string('generated_access_level')->nullable(); // exclusive, shareable

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Remove the old simple column
        if (Schema::hasColumn('subscriptions', 'gift_description')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('gift_description');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gifts');
    }
};