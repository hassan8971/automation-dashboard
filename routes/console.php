<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\AdminChat;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::call(function () {
    AdminChat::truncate();
})->daily(); // هر روز اجرا می‌شود (معمولا ساعت ۰۰:۰۰)