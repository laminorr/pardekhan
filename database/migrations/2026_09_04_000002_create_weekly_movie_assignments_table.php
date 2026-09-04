<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_movie_assignments', function (Blueprint $table) {
            $table->id();

            // فیلمِ تخصیص‌داده‌شده — با حذف فیلم، تخصیص حذف نشود
            $table->foreignId('film_id')
                ->constrained('daily_films')
                ->restrictOnDelete();

            $table->date('week_start');
            $table->date('week_end');

            // مقادیر: active | superseded  (string نه enum برای سازگاری SQLite/MariaDB)
            $table->string('status')->default('active');

            // مقادیر: manual | automatic
            $table->string('assignment_source')->default('manual');

            // تخصیصِ جایگزین (خودارجاع)
            $table->foreignId('superseded_by_id')
                ->nullable()
                ->constrained('weekly_movie_assignments')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['week_start', 'status']);
            $table->index('film_id');
            $table->index('assignment_source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_movie_assignments');
    }
};
