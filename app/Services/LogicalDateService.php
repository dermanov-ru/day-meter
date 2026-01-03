<?php

namespace App\Services;

use Carbon\Carbon;

class LogicalDateService
{
    /**
     * The cutoff hour before which the day belongs to the previous calendar day.
     * For example, 6 means entries made between 00:00-05:59 belong to the previous day.
     */
    protected int $cutoffHour = 6;

    /**
     * Get the logical date for a given timestamp.
     * If the current hour is before the cutoff hour, returns the previous day.
     *
     * @param Carbon|null $time The time to calculate the logical date for. Defaults to now.
     * @return Carbon The logical date (always at 00:00:00).
     */
    public function getLogicalDate(?Carbon $time = null): Carbon
    {
        if ($time === null) {
            $time = now();
        }

        // Get a copy to avoid modifying the original
        $date = $time->copy()->startOfDay();

        // If current hour is before cutoff, use previous day
        if ($time->hour < $this->cutoffHour) {
            $date = $date->subDay();
        }

        return $date;
    }

    /**
     * Get the logical date as a string in Y-m-d format.
     *
     * @param Carbon|null $time The time to calculate the logical date for. Defaults to now.
     * @return string The logical date string.
     */
    public function getLogicalDateString(?Carbon $time = null): string
    {
        return $this->getLogicalDate($time)->toDateString();
    }

    /**
     * Check if a given calendar date is the logical date for the given time.
     *
     * @param string $calendarDate The calendar date to check (Y-m-d format).
     * @param Carbon|null $time The time to check against. Defaults to now.
     * @return bool True if the calendar date matches the logical date.
     */
    public function isLogicalDateFor(string $calendarDate, ?Carbon $time = null): bool
    {
        return $this->getLogicalDateString($time) === $calendarDate;
    }

    /**
     * Get the cutoff hour.
     *
     * @return int The cutoff hour (0-23).
     */
    public function getCutoffHour(): int
    {
        return $this->cutoffHour;
    }
}
