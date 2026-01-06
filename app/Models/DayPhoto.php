<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DayPhoto extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'day_entry_id',
        'file_path',
        'comment',
        'sort_order',
    ];

    /**
     * Get the user who owns this photo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the day entry this photo belongs to.
     */
    public function dayEntry(): BelongsTo
    {
        return $this->belongsTo(DayEntry::class);
    }

    /**
     * Get the URL for displaying this photo.
     */
    public function getUrl(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get the thumbnail URL (same as full image for now, could be optimized later).
     */
    public function getThumbnailUrl(): string
    {
        return $this->getUrl();
    }
}
