<?php

namespace App\Console\Commands;

use App\Models\DayEntry;
use App\Models\NotificationSetting;
use App\Services\WebPushService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var stringAlready has entry for today
     */
    protected $description = 'Send push notifications to users based on their reminder settings';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get current time in Moscow timezone
        $currentTimeMsk = now('Europe/Moscow')->format('H:i');
        $this->info("[Reminders] Starting reminder job at {$currentTimeMsk} (MSK)");

        // Get all enabled notification settings
        $settings = NotificationSetting::where('enabled', true)
            ->with(['user' => function ($query) {
                $query->with('pushSubscriptions');
            }])
            ->get();

        $this->info("[Reminders] Found {$settings->count()} enabled notification settings");

        if ($settings->isEmpty()) {
            $this->info('[Reminders] No users with enabled notifications');
            return Command::SUCCESS;
        }

        $webPushService = new WebPushService();
        $sentCount = 0;
        $skippedCount = 0;

        foreach ($settings as $setting) {
            $user = $setting->user;

            // Check if time matches
            if ($setting->remind_time !== $currentTimeMsk) {
                $this->line("[User {$user->id}] Time mismatch: {$setting->remind_time} != {$currentTimeMsk}, skipping");
                $skippedCount++;
                continue;
            }

            $this->line("[User {$user->id}] Time matches ({$setting->remind_time}), processing reminder");

            // Get active subscriptions
            $activeSubscriptions = $user->pushSubscriptions()
                ->where('is_active', true)
                ->get();

            if ($activeSubscriptions->isEmpty()) {
                $this->line("[User {$user->id}] No active push subscriptions");
                $skippedCount++;
                continue;
            }

            $this->line("[User {$user->id}] Found {$activeSubscriptions->count()} active subscription(s)");

            // Send push to all active subscriptions
            foreach ($activeSubscriptions as $subscription) {
                $payload = [
                    'title' => 'Day Meter',
                    'body' => 'Пора занести данные за день',
                    'url' => '/entry/today',
                ];

                try {
                    $result = $webPushService->send($subscription, $payload);
                    if ($result) {
                        $sentCount++;
                        $this->line("[User {$user->id}] ✓ Successfully sent push to subscription {$subscription->id}");
                    } else {
                        $this->warn("[User {$user->id}] ✗ Failed to send push to subscription {$subscription->id}");
                    }
                } catch (\Exception $e) {
                    $this->error("[User {$user->id}] ✗ Exception sending push: {$e->getMessage()}");
                }
            }
        }

        $this->info("\n[Reminders] Summary:");
        $this->info("  Sent: {$sentCount} notification(s)");
        $this->info("  Skipped: {$skippedCount} user(s)");
        $this->info("[Reminders] Reminder job completed");

        return Command::SUCCESS;
    }
}
