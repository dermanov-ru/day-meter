<?php

namespace Database\Seeders;

use App\Models\Metric;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MetricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Metric::create([
            'key' => 'mood',
            'title' => 'Mood',
            'type' => 'scale',
            'min_value' => 1,
            'max_value' => 10,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Metric::create([
            'key' => 'irritation',
            'title' => 'Irritation',
            'type' => 'scale',
            'min_value' => 1,
            'max_value' => 10,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Metric::create([
            'key' => 'yell',
            'title' => 'Yell',
            'type' => 'boolean',
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}
