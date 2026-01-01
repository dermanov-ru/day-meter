<?php

namespace App\Services;

use App\Models\DayEntry;
use App\Models\Metric;
use App\Models\MetricValue;
use App\Models\User;
use Carbon\Carbon;
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

    /**
     * Get detailed monthly statistics for a user.
     */
    public function getMonthlyDetailedStats(User $user, Carbon $month): array
    {
        // Get the first and last day of the month
        $startDate = $month->copy()->startOfMonth()->toDateString();
        $endDate = $month->copy()->endOfMonth()->toDateString();

        // Load user's day entries for the month
        $dayEntries = DayEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // Get all active metrics
        $metrics = Metric::active()->ordered()->get();
        $stats = [];

        foreach ($metrics as $metric) {
            $stat = [
                'metric' => $metric,
                'type' => $metric->type,
            ];

            if ($metric->type === 'scale') {
                $stat['avg'] = $this->avgScale($metric->key, $dayEntries);
                $stat['min'] = $this->minScale($metric->key, $dayEntries);
                $stat['max'] = $this->maxScale($metric->key, $dayEntries);
                $stat['count'] = $this->countValues($metric->key, $dayEntries);
            } else {
                $stat['percentage'] = $this->countBoolean($metric->key, $dayEntries);
                $stat['count'] = $this->countValues($metric->key, $dayEntries);
            }

            $stats[] = $stat;
        }

        return $stats;
    }

    /**
     * Get minimum value for a scale metric.
     */
    public function minScale(string $metricKey, Collection $dayEntries): ?int
    {
        $metric = Metric::where('key', $metricKey)->first();
        
        if (!$metric || $metric->type !== 'scale') {
            return null;
        }

        $min = MetricValue::whereIn('day_entry_id', $dayEntries->pluck('id'))
            ->where('metric_id', $metric->id)
            ->whereNotNull('value_int')
            ->min('value_int');

        return $min;
    }

    /**
     * Get maximum value for a scale metric.
     */
    public function maxScale(string $metricKey, Collection $dayEntries): ?int
    {
        $metric = Metric::where('key', $metricKey)->first();
        
        if (!$metric || $metric->type !== 'scale') {
            return null;
        }

        $max = MetricValue::whereIn('day_entry_id', $dayEntries->pluck('id'))
            ->where('metric_id', $metric->id)
            ->whereNotNull('value_int')
            ->max('value_int');

        return $max;
    }

    /**
     * Count how many times a metric has been recorded.
     */
    public function countValues(string $metricKey, Collection $dayEntries): int
    {
        $metric = Metric::where('key', $metricKey)->first();
        
        if (!$metric) {
            return 0;
        }

        return MetricValue::whereIn('day_entry_id', $dayEntries->pluck('id'))
            ->where('metric_id', $metric->id)
            ->count();
    }

    /**
     * Get chart data for monthly visualization.
     */
    public function getMonthlyChartData(User $user, Carbon $month): array
    {
        // Get the first and last day of the month
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        // Generate array of days in the month
        $days = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $days[] = $current->toDateString();
            $current->addDay();
        }

        // Load user's day entries for the month with metric values
        $dayEntries = DayEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('values.metric')
            ->get();

        // Create a map of date => values for quick lookup
        $valuesByDate = [];
        foreach ($dayEntries as $dayEntry) {
            $valuesByDate[$dayEntry->date] = $dayEntry->values;
        }

        // Get all active metrics
        $metrics = Metric::active()->ordered()->get();
        $chartData = [];

        foreach ($metrics as $metric) {
            $data = [
                'metric' => $metric,
                'type' => $metric->type,
                'labels' => $days,
                'values' => [],
            ];

            // Collect values for each day
            foreach ($days as $day) {
                $value = null;
                if (isset($valuesByDate[$day])) {
                    $metricValue = $valuesByDate[$day]->firstWhere('metric_id', $metric->id);
                    if ($metricValue) {
                        $value = $metric->type === 'scale' ? $metricValue->value_int : ($metricValue->value_bool ? 1 : 0);
                    }
                }
                $data['values'][] = $value;
            }

            $chartData[] = $data;
        }

        return $chartData;
    }
}
