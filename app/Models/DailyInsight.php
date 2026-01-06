<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyInsight extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'text',
        'type',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the user that owns this insight
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get display name for type
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'insight' => 'Осознание',
            'conclusion' => 'Вывод',
            'quote' => 'Цитата',
            default => 'Вывод дня',
        };
    }

    /**
     * Scope: Get insights for a specific date range
     */
    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'desc');
    }

    /**
     * Scope: Get insights for a month
     */
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc');
    }

    /**
     * Scope: Get insights for a year
     */
    public function scopeForYear($query, $year)
    {
        return $query->whereYear('date', $year)->orderBy('date', 'desc');
    }
}
