<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('app_tabs', function (Blueprint $table) {
            $table->id();
            $table->string('title');      // عنوان تب (مثلاً خانه)
            $table->string('link');       // لینک مقصد (مثلاً home)
            $table->string('icon')->nullable(); // کلاس آیکون (fas fa-home)
            $table->integer('sort_order')->default(0); // ترتیب نمایش
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('app_tabs');
    }
};