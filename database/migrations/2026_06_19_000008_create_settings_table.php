<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // مقادیر اولیه
        $defaults = [
            'card_number'        => '',
            'card_holder'        => '',
            'gateway_enabled'    => '0',
            'card_to_card_enabled' => '1',
        ];
        foreach ($defaults as $k => $v) {
            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'key' => $k, 'value' => $v,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
