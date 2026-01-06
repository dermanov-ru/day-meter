<?php

namespace App\Http\Controllers;

use App\Models\DayEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PhotoChronicleController extends Controller
{
    /**
     * Display photo chronicle (timeline of photos) filtered by month.
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
        
        // Get day entries that have photos, ordered by date descending (new to old)
        // Only select date and photos, don't load other data
        $dayEntries = DayEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereHas('photos') // Only days with photos
            ->with(['photos' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            }])
            ->orderBy('date', 'desc') // Newest first
            ->get();
        
        return view('photo-chronicle.index', [
            'dayEntries' => $dayEntries,
            'month' => $month,
            'monthString' => $month->format('Y-m'),
        ]);
    }
}
