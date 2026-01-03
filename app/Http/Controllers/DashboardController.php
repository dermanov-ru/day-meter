<?php

namespace App\Http\Controllers;

use App\Models\DayEntry;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics.
     */
    public function index(Request $request, ReportService $reportService)
    {
        $user = Auth::user();
        
        // Get period from query parameter (week or month), default to week
        $period = $request->query('period', 'week');
        
        // Calculate date range
        if ($period === 'month') {
            $startDate = now()->subMonth()->toDateString();
        } else {
            // Default to week
            $startDate = now()->subDays(7)->toDateString();
        }
        
        $endDate = now()->toDateString();

        // Load user's day entries for the period
        $dayEntries = DayEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // Generate report
        $report = $reportService->getReport($dayEntries);

        return view('dashboard', [
            'report' => $report,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dayEntriesCount' => $dayEntries->count(),
        ]);
    }
}
