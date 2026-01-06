<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disease extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'category',
        'start_date',
        'end_date',
        'status',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the user that owns this disease record
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all notes for this disease
     */
    public function notes(): HasMany
    {
        return $this->hasMany(DiseaseNote::class)->orderBy('datetime', 'asc');
    }

    /**
     * Scope: Get active diseases (no end_date)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('end_date')->orWhere('status', 'active');
    }

    /**
     * Scope: Get closed diseases
     */
    public function scopeClosed($query)
    {
        return $query->whereNotNull('end_date')->orWhere('status', 'closed');
    }

    /**
     * Get duration in days
     */
    public function getDurationDaysAttribute(): ?int
    {
        if ($this->start_date === null) {
            return null;
        }

        $endDate = $this->end_date ?? now()->toDateString();
        return $this->start_date->diffInDays($endDate);
    }

    /**
     * Check if disease is active
     */
    public function isActive(): bool
    {
        return is_null($this->end_date) && $this->status === 'active';
    }

    /**
     * Close the disease
     */
    public function close(): bool
    {
        $this->update([
            'end_date' => now()->toDateString(),
            'status' => 'closed',
        ]);
        return true;
    }
}
