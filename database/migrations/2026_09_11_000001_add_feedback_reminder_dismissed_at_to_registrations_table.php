<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // زمانِ بستنِ کارتِ یادآوریِ بازخورد توسط عضو (× زدن). NULL = هنوز بسته نشده.
            $table->timestamp('feedback_reminder_dismissed_at')->nullable()->after('attendance_status');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('feedback_reminder_dismissed_at');
        });
    }
};
