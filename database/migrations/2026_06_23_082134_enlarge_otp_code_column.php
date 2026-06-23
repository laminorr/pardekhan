<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // hash بسیار طولانی‌تر از 6 کاراکتر است (raw SQL تا به doctrine/dbal نیاز نباشد)
        DB::statement("ALTER TABLE members MODIFY COLUMN otp_code VARCHAR(255) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE members MODIFY COLUMN otp_code VARCHAR(6) NULL");
    }
};
