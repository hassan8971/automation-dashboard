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
        // 1. جدول اصلی کیف پول (هر کاربر یک کیف پول دارد)
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('service_name')->default('appstore');
            $table->unsignedBigInteger('balance')->default(0); // موجودی به تومان
            $table->boolean('is_active')->default(true); // مسدود کردن کیف پول
            $table->timestamps();
        });

        // 2. جدول تراکنش‌های کیف پول (History)
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->string('service_name')->default('appstore');
            
            // نوع تراکنش: deposit (واریز), withdraw (برداشت/خرید), manual_add (شارژ دستی ادمین), manual_sub (کسر دستی ادمین)
            $table->string('type'); 
            
            $table->unsignedBigInteger('amount'); // مبلغ تراکنش
            
            // وضعیت: pending (در انتظار پرداخت), confirmed (موفق), failed (ناموفق), rejected
            $table->string('status')->default('pending');
            
            $table->string('reference_id')->nullable(); // کد پیگیری درگاه یا شماره سفارش
            $table->text('description')->nullable(); // توضیحات (مثلا: خرید اپلیکیشن X)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
