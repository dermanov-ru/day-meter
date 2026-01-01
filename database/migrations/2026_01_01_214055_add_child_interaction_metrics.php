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
        DB::table('metrics')->insert([
            [
                'key' => 'walked_with_kids',
                'title' => 'Гулял с детьми',
                'type' => 'boolean',
                'min_value' => null,
                'max_value' => null,
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'read_to_kids',
                'title' => 'Читал детям',
                'type' => 'boolean',
                'min_value' => null,
                'max_value' => null,
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('metrics')->whereIn('key', ['walked_with_kids', 'read_to_kids'])->delete();
    }
};
