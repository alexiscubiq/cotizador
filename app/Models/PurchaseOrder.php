<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'quote_id',
        'file_path',
        'file_name',
        'version',
        'uploaded_by',
        'notes',
        'is_current',
    ];

    protected $casts = [
        'version' => 'integer',
        'is_current' => 'boolean',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
