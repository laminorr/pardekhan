<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // افزودن وضعیت pending_payment به enum
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('active','used','cancelled','pending_payment') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('active','used','cancelled') NOT NULL DEFAULT 'active'");
    }
};
