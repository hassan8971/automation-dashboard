<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Categories (Simple Table)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique(); // Slugs are used for clean URLs, e.g., /categories/t-shirts
            $table->text('description')->nullable();
            $table->boolean('is_visible')->default(true); // Control public visibility
            $table->timestamps();
        });

        // 2. Products (Applications)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // --- Product Metadata ---
            $table->string('title'); // Title
            $table->string('name_fa')->nullable(); // Persian Name
            $table->string('name_en')->nullable(); // English Name
            $table->string('slug')->unique();
            
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            
            // Prices
            $table->unsignedBigInteger('price_appstore')->default(0); // Price on Appstore (Display)
            $table->unsignedBigInteger('price_sibaneh')->default(0); // Price on Sibaneh (Real)
            
            // Tech Specs
            $table->string('bundle_id')->nullable();
            $table->string('version')->default('1.0.0');
            $table->string('size')->nullable(); // e.g. "150 MB"
            $table->string('seller')->nullable(); // Developer/Seller Name
            $table->string('seller_website')->nullable();
            
            $table->boolean('is_stable')->default(true); // Stability Checkbox
            $table->string('availability')->default('available'); // available, not_available
            
            // Stats
            $table->unsignedBigInteger('download_count')->default(0);
            $table->decimal('rating', 3, 1)->default(5.0);
            $table->unsignedBigInteger('rating_count')->default(0);
            
            // Links & Install Info
            $table->string('how_to_install_url')->nullable();
            $table->string('appstore_link')->nullable(); // Original Link on Apple Appstore
            $table->string('age_rating')->nullable(); // e.g. +4, +12
            $table->text('description')->nullable(); // Intro/Description
            
            // --- Visual Metadata ---
            $table->string('icon_path')->nullable();
            $table->string('banner_detail_path')->nullable(); // Detail Page
            $table->string('banner_vitrin_path')->nullable(); // Vitrin List
            $table->string('video_url')->nullable(); // Intro Video

            // --- Publish Types (Config) ---
            
            // 1. PWA
            $table->boolean('type_pwa')->default(false);
            $table->unsignedBigInteger('pwa_price')->nullable();
            $table->string('pwa_url')->nullable(); // File Directory
            
            // 2. Adhoc (Next Version)
            $table->boolean('type_adhoc')->default(false);
            
            // 3. Internal
            $table->boolean('type_internal')->default(false);
            $table->unsignedBigInteger('internal_price')->nullable();
            $table->string('internal_url')->nullable(); // File Directory

            // 4. Appstore (Native)
            $table->boolean('type_appstore')->default(false);
            $table->string('native_appstore_url')->nullable(); // DL Link
            $table->string('native_appstore_username')->nullable();
            $table->string('native_appstore_password')->nullable();

            $table->timestamp('app_updated_at')->nullable(); // Manual Update Date
            $table->timestamps();
        });

        // 3. Product Screenshots
        Schema::create('product_screenshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Pivot: Product <-> Supported Subscriptions
        Schema::create('product_subscription', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            // Assuming your subscription table is named 'subscriptions' from previous steps
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_subscription');
        Schema::dropIfExists('product_screenshots');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};