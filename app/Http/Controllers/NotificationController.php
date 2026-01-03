<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Show reminders page
     */
    public function remindersPage(Request $request): View
    {
        return view('reminders.index');
    }

    /**
     * Store or update push subscription
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|url',
            'p256dh' => 'required|string',
            'auth' => 'required|string',
        ]);

        $user = $request->user();

        // Check if subscription already exists for this endpoint
        $subscription = PushSubscription::where('user_id', $user->id)
            ->where('endpoint', $validated['endpoint'])
            ->first();

        if ($subscription) {
            // Update existing subscription
            $subscription->update([
                'p256dh' => $validated['p256dh'],
                'auth' => $validated['auth'],
                'is_active' => true,
                'last_error_at' => null,
            ]);
        } else {
            // Create new subscription
            $subscription = PushSubscription::create([
                'user_id' => $user->id,
                'endpoint' => $validated['endpoint'],
                'p256dh' => $validated['p256dh'],
                'auth' => $validated['auth'],
                'is_active' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Push subscription saved',
            'subscription' => $subscription,
        ]);
    }

    /**
     * Unsubscribe from push notifications
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|url',
        ]);

        $user = $request->user();

        PushSubscription::where('user_id', $user->id)
            ->where('endpoint', $validated['endpoint'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Push subscription removed',
        ]);
    }

    /**
     * Get notification settings
     */
    public function getSettings(Request $request): JsonResponse
    {
        $user = $request->user();
        $settings = $user->getOrCreateNotificationSetting();

        return response()->json([
            'success' => true,
            'settings' => [
                'enabled' => $settings->enabled,
                'remind_time' => $settings->remind_time,
                'timezone' => $settings->timezone,
            ],
        ]);
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'remind_time' => 'required|date_format:H:i',
            'timezone' => 'required|timezone',
        ]);

        $user = $request->user();
        $settings = $user->getOrCreateNotificationSetting();

        $settings->update([
            'enabled' => $validated['enabled'],
            'remind_time' => $validated['remind_time'],
            'timezone' => $validated['timezone'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated',
            'settings' => [
                'enabled' => $settings->enabled,
                'remind_time' => $settings->remind_time,
                'timezone' => $settings->timezone,
            ],
        ]);
    }
}
