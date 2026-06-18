<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('registration_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('method', ['gateway', 'card_to_card', 'wallet']);
            $table->bigInteger('amount');

            $table->enum('status', [
                'pending',       // شروع شده
                'verified',      // تایید شده
                'rejected',      // رد شده توسط ادمین
                'failed',        // ناموفق از بانک
                'refunded',      // بازگشت
                'cancelled',     // لغو
            ])->default('pending');

            // برای کارت به کارت
            $table->string('tracking_number')->nullable(); // شماره پیگیری کاربر

            // برای درگاه
            $table->string('gateway_ref')->nullable();
            $table->string('gateway_authority')->nullable();

            $table->text('admin_note')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });

        // اتصال پرداخت به ثبت‌نام
        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->after('final_price')->constrained('payments')->nullOnDelete();
            $table->enum('payment_status', ['pending', 'verified', 'rejected'])->default('pending')->after('payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_id');
            $table->dropColumn('payment_status');
        });
        Schema::dropIfExists('payments');
    }
};
