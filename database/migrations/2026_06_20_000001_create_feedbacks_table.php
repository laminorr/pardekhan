<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');        // امتیاز ۱ تا ۵
            $table->text('comment')->nullable();          // نظر متنی
            $table->text('admin_reply')->nullable();      // پاسخ مدیریت
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'member_id']);    // هر نفر یک بازخورد
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
