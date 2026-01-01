<?php

namespace App\Http\Controllers;

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

        return view('stats.month', [
            'stats' => $stats,
            'chartData' => $chartData,
            'month' => $month,
            'monthString' => $month->format('Y-m'),
        ]);
    }
}
