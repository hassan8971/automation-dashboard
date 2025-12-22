<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        // --- CLEANUP: Drop tables if they stuck around from a failed migration ---
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('license_view_logs');
        Schema::dropIfExists('user_redemptions');
        Schema::dropIfExists('user_installed_apps');
        Schema::dropIfExists('user_devices');

        
        // 1. User Devices Table
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('model')->nullable(); // e.g., iPhone 13 Pro
            $table->string('imei')->nullable();
            $table->string('udid')->nullable();
            $table->string('serial')->nullable();
            $table->timestamps();
        });

        // 2. User Installed Apps (Appstore Purchases/Downloads)
        Schema::create('user_installed_apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('app_name'); // Or link to an 'apps' table if you have one
            $table->string('bundle_id')->nullable(); // e.g., com.sibaneh.app
            $table->timestamp('downloaded_at')->useCurrent();
        });

        // 3. User Redeem Codes History
        Schema::create('user_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code');
            $table->timestamp('used_at')->useCurrent();
            // Optional: Link to a 'discounts' table if it exists
        });

        // 4. License View Logs (Metadata for the count)
        Schema::create('license_view_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('mobile')->nullable(); // Snapshot of mobile at that time
            $table->string('device_info')->nullable(); // Short summary of the device
            $table->timestamp('viewed_at')->useCurrent();
        });

        // 5. User Subscriptions (To track active/expired status)
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained('subscriptions');
            $table->dateTime('started_at');
            $table->dateTime('expires_at');
            $table->boolean('is_active')->default(true); // Manually cancellable
            $table->timestamps();
        });

        // 6. Update Users Table (Counters & Cache)
        Schema::table('users', function (Blueprint $table) {
            $table->integer('license_view_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('license_view_logs');
        Schema::dropIfExists('user_redemptions');
        Schema::dropIfExists('user_installed_apps');
        Schema::dropIfExists('user_devices');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('license_view_count');
        });
    }
};