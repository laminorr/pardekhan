<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // آیا این دورهمی می‌تواند به‌عنوان «دورهمی پیشنهادی» در صفحهٔ اول نشان داده شود؟
            // پیش‌فرض true تا رفتار فعلی برای دورهمی‌های موجود حفظ شود.
            $table->boolean('is_suggested')->default(true)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_suggested');
        });
    }
};
