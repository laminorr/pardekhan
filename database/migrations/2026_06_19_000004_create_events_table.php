<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();

            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();

            $table->dateTime('starts_at');
            $table->unsignedSmallInteger('capacity');
            $table->unsignedSmallInteger('min_quorum')->default(1);
            $table->unsignedBigInteger('base_price')->default(0); // تومان

            // وضعیت رویداد
            $table->enum('status', [
                'draft',
                'active',
                'full',
                'closed',           // ثبت‌نام بسته (۱۲ ساعت قبل)
                'needs_decision',   // نرسیده به حد نصاب، منتظر تصمیم مدیر
                'cancelled',
                'completed',
            ])->default('draft');

            $table->boolean('over_capacity_flag')->default(false);

            $table->timestamps();
        });

        // لایه‌های مجاز + تخفیف override برای هر دورهمی
        Schema::create('event_layer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('layer_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('discount_percent')->nullable(); // null = استفاده از تخفیف پایه لایه
            $table->unique(['event_id', 'layer_id']);
        });

        // دعوت اختصاصی
        Schema::create('event_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['event_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_invitations');
        Schema::dropIfExists('event_layer');
        Schema::dropIfExists('events');
    }
};
