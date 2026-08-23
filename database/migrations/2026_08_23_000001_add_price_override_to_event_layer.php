<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_layer', function (Blueprint $table) {
            // قیمت مطلق برای هر لایه (تومان)
            // null  = استفاده از تخفیف درصدی (رفتار قبلی)
            // 0     = رایگان برای این لایه
            // عدد   = همین قیمت دقیق، مقدم بر قیمت پایه و درصد تخفیف
            $table->unsignedBigInteger('price_override')->nullable()->after('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('event_layer', function (Blueprint $table) {
            $table->dropColumn('price_override');
        });
    }
};
