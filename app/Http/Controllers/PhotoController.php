<?php

namespace App\Http\Controllers;

use App\Models\DayEntry;
use App\Models\DayPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PhotoController extends Controller
{
    /**
     * Upload a photo for a specific day entry.
     */
    public function upload(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Validate the uploaded file
        $validated = $request->validate([
            'file' => 'required|image|max:10240', // Max 10MB
            'date' => 'required|date_format:Y-m-d',
        ]);

        // Get or verify the day entry exists
        $dayEntry = DayEntry::where('user_id', $user->id)
            ->where('date', $validated['date'])
            ->firstOrFail();

        // Store the file
        $file = $request->file('file');
        $timestamp = now()->timestamp;
        $filename = $timestamp . '_' . $file->getClientOriginalName();
        
        // Store in: storage/app/public/photos/{user_id}/{date}/
        $path = "photos/{$user->id}/{$validated['date']}";
        $storedPath = $file->storeAs($path, $filename, 'public');

        // Create DayPhoto record
        $photo = DayPhoto::create([
            'user_id' => $user->id,
            'day_entry_id' => $dayEntry->id,
            'file_path' => $storedPath,
            'sort_order' => DayPhoto::where('day_entry_id', $dayEntry->id)->max('sort_order') + 1,
        ]);

        return response()->json([
            'success' => true,
            'photo' => [
                'id' => $photo->id,
                'url' => $photo->getUrl(),
                'thumbnail_url' => $photo->getThumbnailUrl(),
                'comment' => $photo->comment,
            ],
        ]);
    }

    /**
     * Delete a photo.
     */
    public function delete(Request $request, DayPhoto $photo): JsonResponse
    {
        $user = Auth::user();

        // Verify user owns this photo
        if ($photo->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete the file from storage
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        // Delete the database record
        $photo->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Update the comment for a photo.
     */
    public function updateComment(Request $request, DayPhoto $photo): JsonResponse
    {
        $user = Auth::user();

        // Verify user owns this photo
        if ($photo->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the comment
        $validated = $request->validate([
            'comment' => 'nullable|string|max:500',
        ]);

        // Update the comment
        $photo->update([
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'comment' => $photo->comment,
        ]);
    }
}
