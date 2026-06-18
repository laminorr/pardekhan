<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // تنظیمات امتیاز هر رفتار
        Schema::create('score_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // مثلاً: attendance, cancellation, absence
            $table->string('label');         // نام فارسی برای نمایش در پنل
            $table->integer('points');       // مقدار امتیاز (میتونه منفی باشه)
            $table->enum('type', ['auto', 'manual'])->default('auto');
            $table->timestamps();
        });

        // لاگ تغییرات امتیاز
        Schema::create('score_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('reason_key');   // کلید رفتار
            $table->string('reason_label'); // توضیح فارسی
            $table->integer('points');      // امتیاز این تغییر
            $table->integer('score_after'); // امتیاز بعد از تغییر
            $table->nullableMorphs('related'); // مثلاً event، payment
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_logs');
        Schema::dropIfExists('score_settings');
    }
};
