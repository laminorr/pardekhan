<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ثبت‌نام‌ها
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();

            $table->unsignedBigInteger('final_price'); // قیمت نهایی بعد از تخفیف

            // وضعیت حضور
            $table->enum('attendance_status', [
                'registered',          // ثبت‌نام قطعی
                'attended',            // حضور یافته (QR اسکن شده)
                'cancelled_by_user',   // انصراف با اطلاع
                'absent',              // غیبت بدون اطلاع
                'cancelled_by_admin',  // لغو مدیریتی
            ])->default('registered');

            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();

            $table->unique(['event_id', 'member_id']);
        });

        // لیست انتظار
        Schema::create('waiting_list', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at')->useCurrent();
            $table->unique(['event_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waiting_list');
        Schema::dropIfExists('registrations');
    }
};
