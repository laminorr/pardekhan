<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_moods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->index();  // عضو
            $table->unsignedTinyInteger('mood');       // حال روز: ۱ (بد) تا ۵ (عالی)
            $table->date('mood_date');                 // تاریخ ثبت حال (به وقت تهران)
            $table->timestamps();

            // یک ردیف برای هر عضو در هر روز — ثبت دوبارهٔ همان روز آپدیت می‌شود
            $table->unique(['member_id', 'mood_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_moods');
    }
};
