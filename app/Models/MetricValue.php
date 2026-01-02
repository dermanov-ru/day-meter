<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetricValue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'day_entry_id',
        'metric_id',
        'value_bool',
        'value_int',
        'comment',
    ];

    /**
     * Get the metric for this value.
     */
    public function metric(): BelongsTo
    {
        return $this->belongsTo(Metric::class);
    }
}
