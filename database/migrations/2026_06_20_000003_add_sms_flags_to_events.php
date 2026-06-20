<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('reminder_sent')->default(false);
            $table->boolean('feedback_requested')->default(false);
        });

        Schema::table('waiting_list', function (Blueprint $table) {
            $table->boolean('notified')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent', 'feedback_requested']);
        });

        Schema::table('waiting_list', function (Blueprint $table) {
            $table->dropColumn('notified');
        });
    }
};
