<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'endpoint',
        'p256dh',
        'auth',
        'is_active',
        'last_error_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_error_at' => 'datetime',
    ];

    /**
     * Get the user this subscription belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark subscription as inactive due to error.
     */
    public function markAsError(): void
    {
        $this->update([
            'is_active' => false,
            'last_error_at' => now(),
        ]);
    }

    /**
     * Get subscription data for Web Push Protocol.
     */
    public function getSubscriptionData(): array
    {
        return [
            'endpoint' => $this->endpoint,
            'keys' => [
                'p256dh' => $this->p256dh,
                'auth' => $this->auth,
            ],
        ];
    }
}
