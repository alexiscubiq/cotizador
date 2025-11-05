<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Quote extends Model
{
    use HasRelationships;

    protected $fillable = [
        'code',
        'client_id',
        'supplier_id',
        'quote_type_id',
        'buyer',
        'buyer_department',
        'season',
        'created_date',
        'delivery_date',
        'quantity',
        'unit_price',
        'total_cost',
        'estimated_cost',
        'profit_margin',
        'lead_time_days',
        'minimums_by_color',
        'minimums_by_style',
        'minimums_by_fabric',
        'size_range',
        'fabric_information',
        'trims_list',
        'artwork_details',
        'costsheet_data',
        'status',
        'has_artwork',
    ];

    protected $casts = [
        'created_date' => 'date',
        'delivery_date' => 'date',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'lead_time_days' => 'integer',
        'minimums_by_color' => 'array',
        'minimums_by_style' => 'integer',
        'minimums_by_fabric' => 'array',
        'fabric_information' => 'array',
        'trims_list' => 'array',
        'artwork_details' => 'array',
        'costsheet_data' => 'array',
        'has_artwork' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function techpacks(): BelongsToMany
    {
        return $this->belongsToMany(Techpack::class, 'quote_techpack')
            ->withPivot(['unit_price', 'quantity', 'total_price'])
            ->withTimestamps();
    }

    public function productionMilestones(): HasMany
    {
        return $this->hasMany(ProductionMilestone::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function sampleOrders(): HasManyDeep
    {
        // Get all sample orders for techpacks associated with this quote
        // Quote -> quote_techpack (pivot) -> Techpack -> SampleOrder
        return $this->hasManyDeep(
            SampleOrder::class,
            ['quote_techpack', Techpack::class], // intermediate tables/models
            [
                'quote_id',    // Foreign key on quote_techpack pointing to quotes
                'id',          // Foreign key on techpacks (we join quote_techpack.techpack_id = techpacks.id)
                'techpack_id'  // Foreign key on sample_orders pointing to techpacks
            ],
            [
                'id',          // Local key on quotes
                'techpack_id', // Local key on quote_techpack
                'id'           // Local key on techpacks
            ]
        );
    }

    public function quoteType(): BelongsTo
    {
        return $this->belongsTo(QuoteType::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function tnas(): HasMany
    {
        return $this->hasMany(Tna::class);
    }
}
