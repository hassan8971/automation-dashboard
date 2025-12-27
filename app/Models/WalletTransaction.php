<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id', 'type', 'amount', 'status', 'reference_id', 'description', 'service_name'
    ];

    // ثابت‌ها برای استفاده در کد (جلوگیری از تایپ اشتباه)
    const TYPE_DEPOSIT = 'deposit';     // شارژ درگاه
    const TYPE_WITHDRAW = 'withdraw';   // خرید محصول
    const TYPE_MANUAL_ADD = 'manual_add'; // شارژ ادمین
    const TYPE_MANUAL_SUB = 'manual_sub'; // کسر ادمین
}