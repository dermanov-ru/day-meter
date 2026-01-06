<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CulturalActivity extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'temporal_type',
        'title',
        'date_at',
        'date_start',
        'date_end',
        'format',
        'impressions',
        'rating',
    ];

    protected $casts = [
        'date_at' => 'datetime',
        'date_start' => 'date',
        'date_end' => 'date',
        'rating' => 'float',
    ];

    /**
     * Types that are instant events
     */
    private static array $instantTypes = ['movie', 'theater', 'concert'];

    /**
     * Types that are duration events
     */
    private static array $durationTypes = ['book', 'series'];

    /**
     * Boot the model
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            // Auto-set temporal_type based on type
            $model->temporal_type = self::getTemporalType($model->type);
        });

        static::updating(function (self $model) {
            // Auto-set temporal_type based on type
            $model->temporal_type = self::getTemporalType($model->type);
        });
    }

    /**
     * Get temporal type based on activity type
     */
    private static function getTemporalType(string $type): string
    {
        return in_array($type, self::$instantTypes) ? 'instant' : 'duration';
    }

    /**
     * Get the user that owns this activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status based on date_end
     */
    public function getStatusAttribute(): string
    {
        if ($this->temporal_type === 'instant') {
            return 'finished';
        }

        return $this->date_end !== null ? 'finished' : 'in_progress';
    }

    /**
     * Get duration in days for duration activities
     */
    public function getDurationDaysAttribute(): ?int
    {
        if ($this->temporal_type === 'instant') {
            return null;
        }

        if ($this->date_start === null) {
            return null;
        }

        $endDate = $this->date_end ?? now()->toDateString();
        return $this->date_start->diffInDays($endDate);
    }

    /**
     * Get duration in days for duration activities (method version)
     */
    public function getDurationDays(): ?int
    {
        if ($this->temporal_type === 'instant') {
            return null;
        }

        if ($this->date_start === null) {
            return null;
        }

        $endDate = $this->date_end ?? now()->toDateString();
        return $this->date_start->diffInDays($endDate);
    }

    /**
     * Check if activity is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Finish a duration activity
     */
    public function finish(): bool
    {
        $this->update(['date_end' => now()->toDateString()]);
        return true;
    }

    /**
     * Get display name for type
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'movie' => '🎬 Фильм',
            'book' => '📚 Книга',
            'theater' => '🎭 Театр',
            'series' => '📺 Сериал',
            'concert' => '🎵 Концерт',
            default => 'Активность',
        };
    }

    /**
     * Get display name for format
     */
    public function getFormatDisplayAttribute(): string
    {
        return match ($this->format) {
            'cinema' => 'Кинотеатр',
            'streaming' => 'Стриминг',
            'paper' => 'Бумажная',
            'electronic' => 'Электронная',
            'audio' => 'Аудиокнига',
            'offline' => 'Оффлайн',
            default => '',
        };
    }

    /**
     * Scope: Get active (in progress) activities
     */
    public function scopeActive($query)
    {
        return $query->whereNull('date_end');
    }

    /**
     * Scope: Get finished activities
     */
    public function scopeFinished($query)
    {
        return $query->whereNotNull('date_end')->orWhere('temporal_type', 'instant');
    }

    /**
     * Scope: Get instant activities
     */
    public function scopeInstant($query)
    {
        return $query->where('temporal_type', 'instant');
    }

    /**
     * Scope: Get duration activities
     */
    public function scopeDuration($query)
    {
        return $query->where('temporal_type', 'duration');
    }
}
