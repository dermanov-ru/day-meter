<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'enabled',
        'remind_time',
        'timezone',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Get the user this setting belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if notifications are enabled and it's time to send.
     */
    public function shouldSendNotification(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        $now = now()->setTimezone($this->timezone)->format('H:i');
        return $now === $this->remind_time;
    }
}
