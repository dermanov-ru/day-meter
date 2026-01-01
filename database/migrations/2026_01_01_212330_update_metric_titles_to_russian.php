<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('metrics')->where('key', 'mood')->update(['title' => 'Настроение']);
        DB::table('metrics')->where('key', 'irritation')->update(['title' => 'Уровень раздражения']);
        DB::table('metrics')->where('key', 'yell')->update(['title' => 'Кричал']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('metrics')->where('key', 'mood')->update(['title' => 'Mood']);
        DB::table('metrics')->where('key', 'irritation')->update(['title' => 'Irritation']);
        DB::table('metrics')->where('key', 'yell')->update(['title' => 'Yell']);
    }
};
