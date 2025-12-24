<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Wallet;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * این متد بلافاصله بعد از اینکه یوزر در دیتابیس insert شد اجرا می‌شود.
     */
    public function created(User $user): void
    {
        // ایجاد کیف پول خالی برای کاربر جدید
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'is_active' => true,
        ]);
    }
}