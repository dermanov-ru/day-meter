<?php

namespace App\Http\Controllers;

use App\Models\CulturalActivity;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CulturalActivityController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of activities
     */
    public function index(): View
    {
        $user = auth()->user();

        $inProgress = $user->culturalActivities()
            ->active()
            ->duration()
            ->orderBy('date_start', 'desc')
            ->get();

        $recentInstant = $user->culturalActivities()
            ->instant()
            ->orderBy('date_at', 'desc')
            ->limit(10)
            ->get();

        $finished = $user->culturalActivities()
            ->finished()
            ->orderBy('date_end', 'desc')
            ->limit(10)
            ->get();

        return view('culture.activities.index', [
            'inProgress' => $inProgress,
            'recentInstant' => $recentInstant,
            'finished' => $finished,
        ]);
    }

    /**
     * Show the form for creating a new activity
     */
    public function create(): View
    {
        return view('culture.activities.create');
    }

    /**
     * Store a newly created activity
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:movie,book,theater,series,concert',
            'title' => 'required|string|max:255',
            'format' => 'required|in:cinema,streaming,paper,electronic,audio,offline',
            'impressions' => 'nullable|string|max:2000',
            'rating' => 'nullable|numeric|min:0|max:10',
            'date_at' => 'nullable|date_format:Y-m-d\TH:i',
            'date_start' => 'nullable|date',
        ]);

        // Determine temporal type and set appropriate date field
        $isInstant = in_array($validated['type'], ['movie', 'theater', 'concert']);

        if ($isInstant) {
            $validated['date_at'] = $validated['date_at'] ?? now();
            unset($validated['date_start']);
        } else {
            $validated['date_start'] = $validated['date_start'] ?? now()->toDateString();
            unset($validated['date_at']);
        }

        $activity = auth()->user()->culturalActivities()->create($validated);

        return redirect()->route('activities.show', $activity)
            ->with('status', 'Активность добавлена');
    }

    /**
     * Display the specified activity
     */
    public function show(CulturalActivity $activity): View
    {
        $this->authorize('view', $activity);

        return view('culture.activities.show', [
            'activity' => $activity,
        ]);
    }

    /**
     * Show the form for editing the activity
     */
    public function edit(CulturalActivity $activity): View
    {
        $this->authorize('update', $activity);

        return view('culture.activities.edit', [
            'activity' => $activity,
        ]);
    }

    /**
     * Update the specified activity
     */
    public function update(Request $request, CulturalActivity $activity): RedirectResponse
    {
        $this->authorize('update', $activity);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'format' => 'required|in:cinema,streaming,paper,electronic,audio,offline',
            'impressions' => 'nullable|string|max:2000',
            'rating' => 'nullable|numeric|min:0|max:10',
            'date_at' => 'nullable|date_format:Y-m-d\TH:i',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date',
        ]);

        // Prevent changing type
        unset($validated['type']);

        $activity->update($validated);

        return redirect()->route('activities.show', $activity)
            ->with('status', 'Активность обновлена');
    }

    /**
     * Delete the specified activity
     */
    public function destroy(CulturalActivity $activity): RedirectResponse
    {
        $this->authorize('delete', $activity);

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('status', 'Активность удалена');
    }
}
