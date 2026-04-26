<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('episode_number');
            
            // Film info
            $table->string('title_fa');
            $table->string('title_en');
            $table->unsignedSmallInteger('year');
            $table->string('director');
            $table->string('imdb_url')->nullable();
            $table->string('aparat_hash')->nullable();
            
            // Hero
            $table->text('hero_title_html');
            $table->text('hero_lead');
            
            // Essay intro
            $table->string('essay_title_html');
            $table->json('essay_paragraphs');
            $table->text('opening_quote_text');
            $table->string('opening_quote_cite');
            $table->json('essay_after_paragraphs')->nullable();
            
            // Big quote
            $table->string('big_quote_highlight');
            $table->string('big_quote_rest');
            $table->string('big_quote_source');
            
            // Meta
            $table->string('meta_duration')->default('00:00');
            $table->unsignedTinyInteger('meta_approaches_count')->default(4);
            $table->unsignedTinyInteger('meta_references_count')->default(0);
            $table->unsignedTinyInteger('meta_quotes_count')->default(0);
            $table->string('meta_level')->default('پیشرفته');
            $table->json('meta_tags')->nullable();
            
            // Next episode
            $table->string('next_episode_number')->nullable();
            $table->string('next_episode_title')->nullable();
            $table->string('next_episode_subtitle')->nullable();
            
            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('og_image')->nullable();
            
            // Status
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
