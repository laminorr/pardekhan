<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // کتاب مرتبط با این دورهمی (اختیاری، رابطهٔ یک‌به‌یک)
            $table->foreignId('book_id')->nullable()->after('image')
                ->constrained('books')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('book_id');
        });
    }
};
