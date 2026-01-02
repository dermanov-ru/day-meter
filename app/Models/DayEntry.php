<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DayEntry extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'date',
        'day_note',
    ];

    /**
     * Get the metric values for this day entry.
     */
    public function values(): HasMany
    {
        return $this->hasMany(MetricValue::class);
    }
}
