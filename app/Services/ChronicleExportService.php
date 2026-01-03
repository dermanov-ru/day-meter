<?php

namespace App\Services;

use App\Models\DayEntry;
use App\Models\MetricCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ChronicleExportService
{
    /**
     * Generate chronicle markdown content without saving to disk.
     */
    public function generateContent(User $user, Carbon $from, Carbon $to): string
    {
        // Get day entries for the period
        $dayEntries = DayEntry::where('user_id', $user->id)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->with(['values.metric.category'])
            ->orderBy('date', 'asc')
            ->get();

        // Get active categories with active metrics
        $categoriesWithMetrics = MetricCategory::active()
            ->ordered()
            ->with(['metrics' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->get()
            ->filter(function ($cat) {
                return $cat->metrics->count() > 0;
            });

        // Build and return markdown content
        return $this->buildMarkdown($dayEntries, $categoriesWithMetrics, $from, $to);
    }

    /**
     * Export chronicle to markdown file.
     */
    public function export(User $user, Carbon $from, Carbon $to): string
    {
        // Generate content
        $content = $this->generateContent($user, $from, $to);

        // Save to file
        $filename = $this->getFilename($user->id, $from, $to);
        $path = "exports/{$filename}";

        // Ensure exports directory exists
        if (!Storage::disk('local')->exists('exports')) {
            Storage::disk('local')->makeDirectory('exports');
        }

        Storage::disk('local')->put($path, $content);

        return $path;
    }

    /**
     * Build markdown content.
     */
    private function buildMarkdown($dayEntries, $categoriesWithMetrics, Carbon $from, Carbon $to): string
    {
        $lines = [];
        $lines[] = '# Летопись';
        $lines[] = "Период: {$from->format('d.m.Y')} — {$to->format('d.m.Y')}";
        $lines[] = '';
        $lines[] = 'Legend:';
        $lines[] = '💬 — comment explaining the metric value';
        $lines[] = '';
        $lines[] = '---';
        $lines[] = '';

        foreach ($dayEntries as $dayEntry) {
            $date = Carbon::parse($dayEntry->date);
            $dayName = $date->translatedFormat('l');
            $formattedDate = $date->format('d.m.Y');
            $lines[] = "## {$formattedDate} ({$dayName})";
            $lines[] = '';

            // Add day note if present
            if ($dayEntry->day_note) {
                $lines[] = 'Описание:';
                $lines[] = $dayEntry->day_note;
                $lines[] = '';
            }

            // Group metrics by category
            $metricsByCategory = [];
            foreach ($dayEntry->values as $value) {
                if (!$value->metric || !$value->metric->is_active) {
                    continue;
                }

                $categoryId = $value->metric->metric_category_id;
                if (!isset($metricsByCategory[$categoryId])) {
                    $metricsByCategory[$categoryId] = [
                        'category' => $value->metric->category,
                        'metrics' => []
                    ];
                }

                $metricsByCategory[$categoryId]['metrics'][] = $value;
            }

            // Output categories and metrics
            foreach ($categoriesWithMetrics as $category) {
                if (!isset($metricsByCategory[$category->id])) {
                    continue;
                }

                $lines[] = $category->title . ':';

                foreach ($metricsByCategory[$category->id]['metrics'] as $value) {
                    $metricTitle = $value->metric->title;
                    $formattedValue = $this->formatValue($value, $value->metric->type);
                    $lines[] = "- {$metricTitle}: {$formattedValue}";
                    
                    // Add comment if present
                    if ($value->comment) {
                        $lines[] = "  💬 {$value->comment}";
                    }
                }

                $lines[] = '';
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Format metric value based on type.
     */
    private function formatValue($metricValue, string $type): string
    {
        if ($type === 'boolean') {
            return $metricValue->value_bool ? 'да' : 'нет';
        }

        return (string) $metricValue->value_int;
    }

    /**
     * Get filename for export.
     */
    private function getFilename(int $userId, Carbon $from, Carbon $to): string
    {
        return "chronicle_{$userId}_{$from->format('Y-m-d')}_{$to->format('Y-m-d')}.md";
    }
}
