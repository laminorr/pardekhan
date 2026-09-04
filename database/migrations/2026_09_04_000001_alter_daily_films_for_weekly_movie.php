<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_films', function (Blueprint $table) {
            // لینک‌ها را TEXT بگیر تا با URLهای بلند «Data too long» ندهد
            $table->text('imdb_url')->nullable()->after('link');
            $table->text('filimo_url')->nullable()->after('imdb_url');

            // حذف نرم برای کتابخانهٔ فیلم
            $table->softDeletes();
        });

        // show_date دیگر اجباری نیست (فیلم‌های کتابخانه ممکن است بدون تاریخ نمایش باشند)
        Schema::table('daily_films', function (Blueprint $table) {
            $table->date('show_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('daily_films', function (Blueprint $table) {
            $table->dropColumn(['imdb_url', 'filimo_url']);
            $table->dropSoftDeletes();
        });

        Schema::table('daily_films', function (Blueprint $table) {
            $table->date('show_date')->nullable(false)->change();
        });
    }
};
