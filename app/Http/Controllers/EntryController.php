<?php

namespace App\Http\Controllers;

use App\Models\DayEntry;
use App\Models\Metric;
use App\Models\MetricCategory;
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

        // Get active metrics grouped by active categories
        $categoriesWithMetrics = MetricCategory::active()
            ->ordered()
            ->with(['metrics' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->get()
            ->filter(function ($cat) {
                return $cat->metrics->count() > 0; // Only show categories with active metrics
            });

        // Load existing values for this day
        $values = MetricValue::where('day_entry_id', $dayEntry->id)->get();
        $metricValues = [];
        $metricComments = [];
        foreach ($values as $value) {
            $metricValues[$value->metric_id] = $value->value_bool ?? $value->value_int;
            $metricComments[$value->metric_id] = $value->comment;
        }

        return view('entry.show', [
            'dayEntry' => $dayEntry,
            'categoriesWithMetrics' => $categoriesWithMetrics,
            'date' => $date,
            'metricValues' => $metricValues,
            'metricComments' => $metricComments,
            'dayNote' => $dayEntry->day_note,
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

        // Get active metrics grouped by active categories
        $categoriesWithMetrics = MetricCategory::active()
            ->ordered()
            ->with(['metrics' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->get()
            ->filter(function ($cat) {
                return $cat->metrics->count() > 0; // Only show categories with active metrics
            });
        
        // Flatten metrics for validation
        $metrics = collect();
        foreach ($categoriesWithMetrics as $category) {
            foreach ($category->metrics as $metric) {
                $metrics->push($metric);
            }
        }

        // Build validation rules
        $rules = [];
        foreach ($metrics as $metric) {
            if ($metric->type === 'boolean') {
                $rules["metric_{$metric->id}"] = 'sometimes|in:0,1';
            } else {
                // Numeric metrics are required
                $rules["metric_{$metric->id}"] = "required|integer|min:{$metric->min_value}|max:{$metric->max_value}";
            }
            $rules["metric_{$metric->id}_comment"] = 'sometimes|nullable|string|max:500';
        }
        $rules['date'] = 'sometimes'; // Allow date, we already validated it
        $rules['day_note'] = 'sometimes|nullable|string';

        $validated = $request->validate($rules);

        // Save/update metric values
        $saveCount = 0;
        foreach ($metrics as $metric) {
            $key = "metric_{$metric->id}";
            $commentKey = "metric_{$metric->id}_comment";

            if (isset($validated[$key]) || isset($validated[$commentKey])) {
                $saveCount++;
                // Find or create the metric value
                $metricValue = MetricValue::where('day_entry_id', $dayEntry->id)
                    ->where('metric_id', $metric->id)
                    ->first();

                if (!$metricValue) {
                    $metricValue = new MetricValue();
                    $metricValue->day_entry_id = $dayEntry->id;
                    $metricValue->metric_id = $metric->id;
                    // Initialize values
                    $metricValue->value_bool = null;
                    $metricValue->value_int = null;
                }

                // Set the appropriate value based on type
                if (isset($validated[$key])) {
                    $value = $validated[$key];
                    if ($metric->type === 'boolean') {
                        $metricValue->value_bool = (bool) (int) $value; // Convert '0'/'1' string to boolean
                        $metricValue->value_int = null;
                    } else {
                        $metricValue->value_int = (int) $value;
                        $metricValue->value_bool = null;
                    }
                }

                // Set the comment if provided
                if (isset($validated[$commentKey])) {
                    $metricValue->comment = $validated[$commentKey];
                }

                $metricValue->save();
            }
        }

        // Save day_note if provided
        if (isset($validated['day_note'])) {
            $dayEntry->day_note = $validated['day_note'];
            $dayEntry->save();
        }

        // Return JSON for async requests, redirect for regular requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Entry saved successfully'
            ]);
        }

        return redirect()->route('entry.show', ['date' => $date])
            ->with('status', 'Entry saved successfully');
    }
}
