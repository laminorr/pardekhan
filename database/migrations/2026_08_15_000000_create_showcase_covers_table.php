<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showcase_covers', function (Blueprint $table) {
            $table->id();
            $table->string('image');                         // مسیر عکس کاور
            $table->string('type')->default('film');         // 'film' (ردیف بالا) یا 'book' (ردیف پایین)
            $table->text('imdb_url')->nullable();            // لینک IMDB یا هر لینک دیگر (text برای امنیت در برابر آدرس‌های بلند)
            $table->integer('sort_order')->default(0);       // ترتیب دستی
            $table->boolean('is_active')->default(true);     // فعال/غیرفعال
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showcase_covers');
    }
};
