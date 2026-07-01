<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');                    // عنوان
            $table->string('excerpt')->nullable();      // خلاصه (برای نمای داشبورد)
            $table->longText('body');                    // متن کامل
            $table->string('cover')->nullable();         // عکس کاور
            $table->boolean('is_published')->default(true); // منتشرشده
            $table->timestamp('published_at')->nullable();   // تاریخ انتشار
            $table->unsignedInteger('views')->default(0);    // تعداد بازدید
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
