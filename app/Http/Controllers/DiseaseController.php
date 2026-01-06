<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\DiseaseNote;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DiseaseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of active and closed diseases
     */
    public function index(): View
    {
        $user = auth()->user();
        
        $activeDiseases = $user->diseases()
            ->active()
            ->orderBy('start_date', 'desc')
            ->get();
        
        $closedDiseases = $user->diseases()
            ->closed()
            ->orderBy('end_date', 'desc')
            ->take(10)
            ->get();
        
        return view('health.diseases.index', [
            'activeDiseases' => $activeDiseases,
            'closedDiseases' => $closedDiseases,
        ]);
    }

    /**
     * Show the form for creating a new disease
     */
    public function create(): View
    {
        return view('health.diseases.create');
    }

    /**
     * Store a newly created disease
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ]);

        $disease = auth()->user()->diseases()->create($validated);

        return redirect()->route('diseases.show', $disease)
            ->with('status', 'Болезнь добавлена');
    }

    /**
     * Display the specified disease with its notes
     */
    public function show(Disease $disease): View
    {
        // Authorize: check if disease belongs to authenticated user
        $this->authorize('view', $disease);

        $notes = $disease->notes()->get();

        return view('health.diseases.show', [
            'disease' => $disease,
            'notes' => $notes,
        ]);
    }

    /**
     * Show the form for editing the specified disease
     */
    public function edit(Disease $disease): View
    {
        $this->authorize('update', $disease);
        
        return view('health.diseases.edit', [
            'disease' => $disease,
        ]);
    }

    /**
     * Update the specified disease
     */
    public function update(Request $request, Disease $disease): RedirectResponse
    {
        $this->authorize('update', $disease);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ]);

        $disease->update($validated);

        return redirect()->route('diseases.show', $disease)
            ->with('status', 'Болезнь обновлена');
    }

    /**
     * Close the specified disease
     */
    public function close(Disease $disease): RedirectResponse
    {
        $this->authorize('update', $disease);

        $disease->close();

        return redirect()->route('diseases.show', $disease)
            ->with('status', 'Болезнь завершена');
    }

    /**
     * Delete the specified disease
     */
    public function destroy(Disease $disease): RedirectResponse
    {
        $this->authorize('delete', $disease);

        $disease->delete();

        return redirect()->route('diseases.index')
            ->with('status', 'Болезнь удалена');
    }

    /**
     * Store a new note for the disease
     */
    public function storeNote(Request $request, Disease $disease): RedirectResponse
    {
        $this->authorize('update', $disease);

        $validated = $request->validate([
            'datetime' => 'required|date_format:Y-m-d\\TH:i',
            'type' => 'required|in:symptom,treatment,condition,medication,doctor,free',
            'text' => 'nullable|string|max:2000',
            'temperature' => 'nullable|numeric|min:35|max:42',
            'pain_level' => 'nullable|integer|min:0|max:10',
            'state_flag' => 'nullable|in:worse,better',
        ]);

        // Convert datetime string to proper format
        $validated['datetime'] = \DateTime::createFromFormat('Y-m-d\\TH:i', $validated['datetime'])->format('Y-m-d H:i:00');

        $disease->notes()->create($validated);

        return redirect()->route('diseases.show', $disease)
            ->with('status', 'Запись добавлена');
    }

    /**
     * Delete a note
     */
    public function destroyNote(Disease $disease, DiseaseNote $note): RedirectResponse
    {
        $this->authorize('update', $disease);

        if ($note->disease_id !== $disease->id) {
            abort(403);
        }

        $note->delete();

        return redirect()->route('diseases.show', $disease)
            ->with('status', 'Запись удалена');
    }
}
