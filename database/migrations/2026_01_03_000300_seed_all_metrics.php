<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Metric;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define all metrics
        $metrics = [
            [
                'key' => 'mood',
                'title' => 'Настроение',
                'type' => 'scale',
                'min_value' => 1,
                'max_value' => 10,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'irritation',
                'title' => 'Раздражение',
                'type' => 'scale',
                'min_value' => 1,
                'max_value' => 10,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'yell',
                'title' => 'Кричал',
                'type' => 'boolean',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'was_ill',
                'title' => 'Болел',
                'type' => 'boolean',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'walked_with_kids',
                'title' => 'Гулял с детьми',
                'type' => 'boolean',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'key' => 'read_to_kids',
                'title' => 'Читал детям',
                'type' => 'boolean',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'key' => 'worked_at_night',
                'title' => 'Работал ночью',
                'type' => 'boolean',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'key' => 'was_on_massage',
                'title' => 'Был на массаже',
                'type' => 'boolean',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'key' => 'work_exhausted',
                'title' => 'Утомила работа',
                'type' => 'boolean',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'key' => 'walked_at_night',
                'title' => 'Гулял ночью',
                'type' => 'boolean',
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        // Create metrics if they don't exist
        foreach ($metrics as $metricData) {
            Metric::updateOrCreate(
                ['key' => $metricData['key']], // Check by key
                $metricData // Create or update with all data
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete all metrics created by this migration
        $keys = [
            'mood',
            'irritation',
            'yell',
            'was_ill',
            'walked_with_kids',
            'read_to_kids',
            'worked_at_night',
            'was_on_massage',
            'work_exhausted',
            'walked_at_night',
        ];

        Metric::whereIn('key', $keys)->delete();
    }
};
