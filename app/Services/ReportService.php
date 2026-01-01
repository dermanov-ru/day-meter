<?php

namespace App\Services;

use App\Models\DayEntry;
use App\Models\Metric;
use App\Models\MetricValue;
use Illuminate\Database\Eloquent\Collection;

class ReportService
{
    /**
     * Calculate average for scale metrics.
     */
    public function avgScale(string $metricKey, Collection $dayEntries): ?float
    {
        $metric = Metric::where('key', $metricKey)->first();
        
        if (!$metric || $metric->type !== 'scale') {
            return null;
        }

        $sum = MetricValue::whereIn('day_entry_id', $dayEntries->pluck('id'))
            ->where('metric_id', $metric->id)
            ->whereNotNull('value_int')
            ->sum('value_int');

        $count = MetricValue::whereIn('day_entry_id', $dayEntries->pluck('id'))
            ->where('metric_id', $metric->id)
            ->whereNotNull('value_int')
            ->count();

        if ($count === 0) {
            return null;
        }

        return round($sum / $count, 2);
    }

    /**
     * Count boolean true values as percentage.
     */
    public function countBoolean(string $metricKey, Collection $dayEntries): ?float
    {
        $metric = Metric::where('key', $metricKey)->first();
        
        if (!$metric || $metric->type !== 'boolean') {
            return null;
        }

        $trueCount = MetricValue::whereIn('day_entry_id', $dayEntries->pluck('id'))
            ->where('metric_id', $metric->id)
            ->where('value_bool', true)
            ->count();

        $totalCount = MetricValue::whereIn('day_entry_id', $dayEntries->pluck('id'))
            ->where('metric_id', $metric->id)
            ->count();

        if ($totalCount === 0) {
            return null;
        }

        return round(($trueCount / $totalCount) * 100, 1);
    }

    /**
     * Count days with any data.
     */
    public function daysCount(Collection $dayEntries): int
    {
        return $dayEntries->count();
    }

    /**
     * Get metrics report for a collection of day entries.
     */
    public function getReport(Collection $dayEntries): array
    {
        $metrics = Metric::active()->ordered()->get();
        $report = [];

        foreach ($metrics as $metric) {
            $metricReport = [
                'metric' => $metric,
                'daysCount' => $this->daysCount($dayEntries),
            ];

            if ($metric->type === 'scale') {
                $metricReport['avg'] = $this->avgScale($metric->key, $dayEntries);
            } else {
                $metricReport['percentage'] = $this->countBoolean($metric->key, $dayEntries);
            }

            $report[] = $metricReport;
        }

        return $report;
    }
}
