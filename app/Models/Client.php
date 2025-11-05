<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'legal_name',
        'tax_id',
        'contact',
        'email',
        'phone',
        'whatsapp',
        'address',
        'city',
        'country_code',
        'country',
        'timezone',
        'currency',
        'credit_limit',
        'payment_terms',
        'logo_url',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }
}
