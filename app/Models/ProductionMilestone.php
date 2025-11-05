<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductionMilestone extends Model
{
    protected $fillable = [
        'quote_id',
        'milestone',
        'planned_at',
        'actual_at',
        'delay_days',
        'status',
        'comment',
        'updated_by',
        'attachments_count',
    ];

    protected $casts = [
        'planned_at' => 'date',
        'actual_at' => 'date',
        'delay_days' => 'integer',
        'attachments_count' => 'integer',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    protected function delayDays(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->actual_at || !$this->planned_at) {
                    return null;
                }
                return $this->actual_at->diffInDays($this->planned_at, false);
            }
        );
    }
}
