<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_films', function (Blueprint $table) {
            $table->id();
            $table->string('title');                      // عنوان فیلم
            $table->string('original_title')->nullable(); // عنوان اصلی/انگلیسی
            $table->string('year')->nullable();           // سال ساخت
            $table->string('director')->nullable();       // کارگردان
            $table->string('genre')->nullable();          // ژانر
            $table->string('cover')->nullable();          // عکس کاور (آپلودی)
            $table->string('cover_url')->nullable();      // یا لینک عکس
            $table->text('description')->nullable();       // معرفی/توضیح
            $table->string('link')->nullable();           // لینک (تریلر/تماشا)
            $table->date('show_date');                     // تاریخ نمایش
            $table->boolean('is_active')->default(true);   // فعال/غیرفعال
            $table->timestamps();

            $table->index(['is_active', 'show_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_films');
    }
};
