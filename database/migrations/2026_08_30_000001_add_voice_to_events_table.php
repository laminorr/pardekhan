<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // شرح صوتی دورهمی — قسمتی از پادکست «باهم کتاب» (اختیاری)
            // URL و عنوان را همین‌جا ذخیره می‌کنیم تا صفحهٔ عضو نیازی به خواندن فید نداشته باشد.
            $table->string('voice_url')->nullable()->after('description');
            $table->string('voice_title')->nullable()->after('voice_url');
            $table->string('voice_guid')->nullable()->after('voice_title');
        });

        // آدرس فید «باهم کتاب» در تنظیمات، به‌صورت ثابت در بک‌اند (اگر قبلاً ست نشده).
        if (! Setting::get('podcast_rss_url_bahamketab')) {
            Setting::set('podcast_rss_url_bahamketab', 'https://shenoto.net/feed/bahamketab');
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['voice_url', 'voice_title', 'voice_guid']);
        });
    }
};
