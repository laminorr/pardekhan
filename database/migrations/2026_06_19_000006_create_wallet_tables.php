<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // موجودی کیف پول روی جدول members
        Schema::table('members', function (Blueprint $table) {
            $table->bigInteger('wallet_balance')->default(0)->after('score');
        });

        // تراکنش‌های کیف پول
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['recharge', 'payment', 'refund', 'adjustment']);
            $table->bigInteger('amount'); // مثبت یا منفی
            $table->bigInteger('balance_after');
            $table->string('tracking_code')->unique();
            $table->text('description')->nullable();
            $table->nullableMorphs('related'); // event، payment و...
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('wallet_balance');
        });
    }
};
