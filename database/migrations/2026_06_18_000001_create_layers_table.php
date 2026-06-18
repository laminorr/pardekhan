<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('min_score')->default(0);
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->unsignedSmallInteger('early_access_hours')->default(0);
            $table->boolean('has_exclusive_events')->default(false);
            $table->boolean('has_special_invitations')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layers');
    }
};
