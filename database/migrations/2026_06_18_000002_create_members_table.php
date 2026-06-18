<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            // اطلاعات پایه
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 15)->unique();
            $table->string('password');

            // وضعیت حساب
            $table->enum('status', [
                'otp_pending',
                'questionnaire_pending',
                'pending_review',
                'needs_more_info',
                'approved',
                'rejected',
                'suspended',
            ])->default('otp_pending');

            // OTP
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->unsignedTinyInteger('otp_attempts')->default(0);
            $table->timestamp('otp_locked_until')->nullable();

            // لایه و امتیاز
            $table->foreignId('layer_id')->nullable()->constrained('layers')->nullOnDelete();
            $table->integer('score')->default(0);

            // پروفایل (اختیاری)
            $table->date('birth_date')->nullable();
            $table->string('city')->nullable();
            $table->string('job')->nullable();
            $table->string('education')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('avatar_approved')->default(false);
            $table->boolean('profile_completed')->default(false);

            // یادداشت خصوصی ادمین
            $table->text('admin_note')->nullable();

            // Remember token برای session
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
