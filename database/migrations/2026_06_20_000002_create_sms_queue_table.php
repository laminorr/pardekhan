<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_queue', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('pattern_code');
            $table->json('params');                  // متغیرهای پترن
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('error')->nullable();
            $table->string('purpose')->nullable();   // otp / event / general / waitlist / reminder / feedback
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'attempts']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_queue');
    }
};
