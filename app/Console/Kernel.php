<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run reminder sending command every minute
        // This allows for more precise timing since we check the exact time in the command
        $schedule->command('reminders:send')
            ->everyMinute()
            ->withoutOverlapping()
            ->onFailure(function () {
                // Log errors if needed
            });

        // Optional: You can also run it at a specific time if all users are in the same timezone
        // $schedule->command('reminders:send')
        //     ->hourly()
        //     ->onFailure(function () {});
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
