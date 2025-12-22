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
        Schema::create('redeem_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique(); // کد یکتا (دستی یا تصادفی)
            $table->unsignedBigInteger('amount'); // مبلغ اعتبار (تومان)
            
            // سرویس‌های مجاز: appstore, macstore, sibaneh_code, etc.
            $table->string('service_type')->default('appstore'); 
            
            $table->integer('usage_limit')->default(1); // تعداد دفعات مجاز استفاده
            $table->integer('used_count')->default(0); // تعداد دفعات استفاده شده
            
            $table->dateTime('expires_at')->nullable(); // تاریخ انقضا
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redeem_codes');
    }
};
