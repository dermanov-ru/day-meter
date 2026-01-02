<?php

namespace App\Http\Controllers;

use App\Models\DayEntry;
use App\Models\Metric;
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
        
        // Get day entries for the month, ordered by date descending
        $dayEntries = DayEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('values.metric')
            ->orderBy('date', 'desc')
            ->get();
        
        // Get all active metrics for reference
        $metrics = Metric::active()->ordered()->get();
        
        return view('chronicle.index', [
            'dayEntries' => $dayEntries,
            'metrics' => $metrics,
            'month' => $month,
            'monthString' => $month->format('Y-m'),
        ]);
    }
}
