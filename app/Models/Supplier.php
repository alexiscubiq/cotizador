<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact',
        'email',
        'phone',
        'country',
        'city',
        'address',
    ];

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function sampleOrders(): HasMany
    {
        return $this->hasMany(SampleOrder::class);
    }
}
