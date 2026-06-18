<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScoreSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'attendance',        'label' => 'حضور موفق در دورهمی',     'points' => 20,  'type' => 'auto'],
            ['key' => 'cancellation',      'label' => 'انصراف با اطلاع',          'points' => 5,   'type' => 'auto'],
            ['key' => 'absence',           'label' => 'غیبت بدون اطلاع',          'points' => -15, 'type' => 'auto'],
            ['key' => 'message_reply',     'label' => 'پاسخ به پیام تعاملی',      'points' => 5,   'type' => 'auto'],
            ['key' => 'invalid_payment',   'label' => 'پرداخت نامعتبر',           'points' => -20, 'type' => 'auto'],
            ['key' => 'profile_complete',  'label' => 'تکمیل پروفایل',            'points' => 10,  'type' => 'auto'],
            ['key' => 'participation',     'label' => 'مشارکت خوب در جلسه',      'points' => 15,  'type' => 'manual'],
            ['key' => 'special_invite',    'label' => 'دعوت ویژه توسط مدیر',      'points' => 10,  'type' => 'manual'],
            ['key' => 'violation',         'label' => 'تخلف یا رفتار نامناسب',   'points' => -30, 'type' => 'manual'],
            ['key' => 'manual_adjust',     'label' => 'تنظیم دستی توسط ادمین',   'points' => 0,   'type' => 'manual'],
        ];

        foreach ($settings as $setting) {
            DB::table('score_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
