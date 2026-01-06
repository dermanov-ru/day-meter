<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'biometric_enabled',
        'webauthn_credential_id',
        'webauthn_public_key',
        'webauthn_counter',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's diseases.
     */
    public function diseases(): HasMany
    {
        return $this->hasMany(Disease::class);
    }

    /**
     * Get the user's cultural activities.
     */
    public function culturalActivities(): HasMany
    {
        return $this->hasMany(CulturalActivity::class);
    }

    /**
     * Get the user's daily insights.
     */
    public function dailyInsights(): HasMany
    {
        return $this->hasMany(DailyInsight::class);
    }

    /**
     * Get the user's push subscriptions.
     */
    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    /**
     * Get the user's notification settings.
     */
    public function notificationSetting(): HasOne
    {
        return $this->hasOne(NotificationSetting::class);
    }

    /**
     * Get or create the user's notification settings.
     */
    public function getOrCreateNotificationSetting(): NotificationSetting
    {
        return $this->notificationSetting()->firstOrCreate([
            'user_id' => $this->id,
        ], [
            'enabled' => false,
            'remind_time' => '09:00',
            'timezone' => 'Europe/Moscow',
        ]);
    }
}
