<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->text('reference_fa')->change();
            $table->text('reference_en')->change();
            $table->text('approach')->change();
            $table->text('quote')->change();
            $table->text('simple_explanation')->change();
            $table->longText('paragraph')->change();
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->text('description')->change();
            $table->longText('example')->change();
        });

        Schema::table('episodes', function (Blueprint $table) {
            $table->text('hero_lead')->change();
            $table->text('opening_quote_text')->change();
            $table->text('big_quote_highlight')->change();
            $table->text('big_quote_rest')->change();
            $table->text('seo_description')->change();
        });
    }

    public function down(): void
    {
        //
    }
};
