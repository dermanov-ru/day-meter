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

        Metric::create([
            'key' => 'walked_with_kids',
            'title' => 'Гулял с детьми',
            'type' => 'boolean',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        Metric::create([
            'key' => 'read_to_kids',
            'title' => 'Читал детям',
            'type' => 'boolean',
            'is_active' => true,
            'sort_order' => 5,
        ]);
    }
}
