<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();

            $table->string('title', 200);
            $table->string('slug', 160)->unique();

            $table->string('seo_title', 200)->nullable();
            $table->string('seo_description', 300)->nullable();

            $table->string('hero_kicker', 160)->nullable();
            $table->string('hero_title', 200);
            $table->text('hero_lead')->nullable();

            $table->json('key_concepts')->nullable();
            $table->json('related_tags')->nullable();
            $table->json('featured_episode_slugs')->nullable();
            $table->json('sections')->nullable();
            $table->json('faq')->nullable();

            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
