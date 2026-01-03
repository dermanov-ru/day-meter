<?php

namespace App\Http\Controllers;

use App\Models\DayEntry;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatsController extends Controller
{
    /**
     * Display detailed monthly statistics.
     */
    public function month(Request $request, ReportService $reportService)
    {
        $user = Auth::user();
        
        // Get month from request or use current month
        $monthString = $request->query('month');
        
        if ($monthString) {
            try {
                // Parse month string in format YYYY-MM
                $month = Carbon::createFromFormat('Y-m', $monthString);
            } catch (\Exception $e) {
                abort(400, 'Invalid month format');
            }
        } else {
            $month = now();
        }

        // Get detailed stats for the month
        $stats = $reportService->getMonthlyDetailedStats($user, $month);
        
        // Get chart data
        $chartData = $reportService->getMonthlyChartData($user, $month);
        
        // Get daily entries with metric values and comments
        $startDate = $month->copy()->startOfMonth()->toDateString();
        $endDate = $month->copy()->endOfMonth()->toDateString();
        $dayEntries = DayEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('values')
            ->orderBy('date', 'desc')
            ->get();

        return view('stats.month', [
            'stats' => $stats,
            'chartData' => $chartData,
            'dayEntries' => $dayEntries,
            'month' => $month,
            'monthString' => $month->format('Y-m'),
        ]);
    }
}
