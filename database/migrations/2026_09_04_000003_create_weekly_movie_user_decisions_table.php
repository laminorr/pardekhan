<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_movie_user_decisions', function (Blueprint $table) {
            $table->id();

            // مخاطب (Member، نه User ادمین)
            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();

            $table->foreignId('weekly_assignment_id')
                ->constrained('weekly_movie_assignments')
                ->cascadeOnDelete();

            // مقادیر: will_watch | will_not_watch  (string نه enum)
            $table->string('decision');

            $table->timestamps();

            // یک رأی برای هر مخاطب در هر تخصیص
            $table->unique(['member_id', 'weekly_assignment_id']);
            $table->index(['weekly_assignment_id', 'decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_movie_user_decisions');
    }
};
