<?php

namespace App\Http\Controllers;

use App\Models\DayEntry;
use App\Models\Metric;
use App\Models\MetricValue;
use App\Services\LogicalDateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EntryController extends Controller
{
    /**
     * Show the entry form for a specific date.
     */
    public function show(Request $request, LogicalDateService $logicalDateService, ?string $date = null)
    {
        $user = Auth::user();
        $date = $request->query('date', $date);

        // Use logical date if no date provided
        if ($date === null) {
            $date = $logicalDateService->getLogicalDateString();
        } else {
            // Validate date format
            try {
                $date = Carbon::createFromFormat('Y-m-d', $date)->toDateString();
            } catch (\Exception $e) {
                abort(400, 'Invalid date format');
            }
        }

        // Get or create the day entry (already scoped to current user)
        // Users can only view their own entries by virtue of the where() clause

        // Get or create the day entry
        $dayEntry = DayEntry::firstOrCreate(
            ['user_id' => $user->id, 'date' => $date]
        );

        // Get active metrics ordered
        $metrics = Metric::active()->ordered()->get();

        // Load existing values for this day
        $values = MetricValue::where('day_entry_id', $dayEntry->id)->get();
        $metricValues = [];
        foreach ($values as $value) {
            $metricValues[$value->metric_id] = $value->value_bool ?? $value->value_int;
        }

        return view('entry.show', [
            'dayEntry' => $dayEntry,
            'metrics' => $metrics,
            'date' => $date,
            'metricValues' => $metricValues,
        ]);
    }

    /**
     * Store the entry data.
     */
    public function store(Request $request, LogicalDateService $logicalDateService)
    {
        $user = Auth::user();
        $date = $request->input('date', $logicalDateService->getLogicalDateString());

        // Validate date format
        try {
            $date = Carbon::createFromFormat('Y-m-d', $date)->toDateString();
        } catch (\Exception $e) {
            abort(400, 'Invalid date format');
        }

        // Get the day entry (already scoped to current user)
        $dayEntry = DayEntry::where('user_id', $user->id)
            ->where('date', $date)
            ->first();

        if (!$dayEntry) {
            abort(404, 'Day entry not found');
        }

        // Access is already restricted since we only query the user's own entries

        // Get active metrics
        $metrics = Metric::active()->get();

        // Build validation rules
        $rules = [];
        foreach ($metrics as $metric) {
            if ($metric->type === 'boolean') {
                $rules["metric_{$metric->id}"] = 'sometimes|boolean';
            } else {
                $rules["metric_{$metric->id}"] = "sometimes|integer|min:{$metric->min_value}|max:{$metric->max_value}";
            }
        }

        $validated = $request->validate($rules);

        // Save/update metric values
        foreach ($metrics as $metric) {
            $key = "metric_{$metric->id}";

            if (isset($validated[$key])) {
                $value = $validated[$key];

                // Find or create the metric value
                $metricValue = MetricValue::where('day_entry_id', $dayEntry->id)
                    ->where('metric_id', $metric->id)
                    ->first();

                if (!$metricValue) {
                    $metricValue = new MetricValue();
                    $metricValue->day_entry_id = $dayEntry->id;
                    $metricValue->metric_id = $metric->id;
                }

                // Set the appropriate value based on type
                if ($metric->type === 'boolean') {
                    $metricValue->value_bool = $value;
                    $metricValue->value_int = null;
                } else {
                    $metricValue->value_int = $value;
                    $metricValue->value_bool = null;
                }

                $metricValue->save();
            }
        }

        return redirect()->route('entry.show', ['date' => $date])
            ->with('status', 'Entry saved successfully');
    }
}
