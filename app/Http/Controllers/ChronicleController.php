<?php

namespace App\Http\Controllers;

use App\Models\DayEntry;
use App\Models\DailyInsight;
use App\Models\DayPhoto;
use App\Models\Metric;
use App\Models\MetricCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChronicleController extends Controller
{
    /**
     * Display chronicle (timeline) of day entries with notes, filtered by month.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get month from request or use current month
        $monthString = $request->query('month');
        
        if ($monthString) {
            try {
                $month = Carbon::createFromFormat('Y-m', $monthString);
            } catch (\Exception $e) {
                $month = now();
            }
        } else {
            $month = now();
        }
        
        // Get start and end of month
        $startDate = $month->copy()->startOfMonth()->toDateString();
        $endDate = $month->copy()->endOfMonth()->toDateString();
        
        // Get day entries for the month, ordered by date ascending (old to new)
        $dayEntries = DayEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('values.metric', 'photos')
            ->orderBy('date', 'asc')
            ->get();
        
        // Get daily insights for the month, keyed by date string
        $dailyInsights = DailyInsight::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(function ($item) {
                return $item->date->toDateString();
            });
        
        // Get all active metrics grouped by active categories
        $categoriesWithMetrics = MetricCategory::active()
            ->ordered()
            ->with(['metrics' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->get()
            ->filter(function ($cat) {
                return $cat->metrics->count() > 0; // Only show categories with active metrics
            });
        
        return view('chronicle.index', [
            'dayEntries' => $dayEntries,
            'dailyInsights' => $dailyInsights,
            'categoriesWithMetrics' => $categoriesWithMetrics,
            'month' => $month,
            'monthString' => $month->format('Y-m'),
        ]);
    }
}
