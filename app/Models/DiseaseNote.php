<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiseaseNote extends Model
{
    protected $fillable = [
        'disease_id',
        'datetime',
        'type',
        'text',
        'temperature',
        'pain_level',
        'state_flag',
    ];

    protected $casts = [
        'datetime' => 'datetime',
        'temperature' => 'float',
        'pain_level' => 'integer',
    ];

    /**
     * Get the disease this note belongs to
     */
    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }

    /**
     * Get formatted date for display
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->datetime->format('d.m.Y');
    }

    /**
     * Get formatted time for display
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->datetime->format('H:i');
    }

    /**
     * Get human-readable type
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'symptom' => 'Симптом',
            'treatment' => 'Лечение',
            'condition' => 'Состояние',
            'medication' => 'Лекарство',
            'doctor' => 'У врача',
            'free' => 'Запись',
            default => 'Заметка',
        };
    }

    /**
     * Get emoji for note type
     */
    public function getTypeEmojiAttribute(): string
    {
        return match ($this->type) {
            'symptom' => '🤒',
            'treatment' => '💊',
            'condition' => '📊',
            'medication' => '💉',
            'doctor' => '👨‍⚕️',
            'free' => '📝',
            default => '📌',
        };
    }
}
