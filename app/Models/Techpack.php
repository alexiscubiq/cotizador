<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Techpack extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'code',
        'style_code',
        'wfx_id',
        'buyer',
        'buyer_department',
        'season',
        'image_url',
        'original_file_path',
        'garment_type',
        'version',
        'status',
        'description',
        'uploaded_at',
        'synced_to_wfx_at',
        'fabric_construction',
        'fabric_yarn_count',
        'fabric_content',
        'fabric_dyeing_type',
        'fabric_weight',
        'fabric_width',
        'fabric_finishing',
        'fabric_article_code',
        'lead_time_days',
        'minimums_by_color',
        'minimums_by_style',
        'minimums_by_fabric',
        'size_range',
        'trims',
        'artwork_comments',
        'development_chart_path',
        // Campos del Excel - Development Chart
        'sketch_image',
        'front_artwork_image',
        'front_technique',
        'back_artwork_image',
        'back_technique',
        'sleeve_artwork_image',
        'sleeve_technique',
        'color',
        'dyed_process',
        'initial_request_date',
        'sms_x_date',
        'sms_comments',
        'pp_sample',
        'costsheet',
        'unit_price',
        'profit_margin',
    ];

    protected $casts = [
        'version' => 'integer',
        'uploaded_at' => 'datetime',
        'synced_to_wfx_at' => 'datetime',
        'trims' => 'array',
        'costsheet' => 'array',
        'initial_request_date' => 'date',
        'sms_x_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function quotes(): BelongsToMany
    {
        return $this->belongsToMany(Quote::class, 'quote_techpack')
            ->withTimestamps();
    }

    public function sampleOrders(): HasMany
    {
        return $this->hasMany(SampleOrder::class);
    }

    /**
     * Mock sync to WFX
     */
    public function syncToWFX(): array
    {
        // Simulate API delay
        sleep(1);

        // Generate mock WFX ID if not exists
        if (!$this->wfx_id) {
            $this->wfx_id = 'WFX-' . strtoupper(substr(md5($this->code), 0, 8));
            $this->style_code = $this->style_code ?: 'STYLE-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->synced_to_wfx_at = now();
            $this->save();
        }

        return [
            'success' => true,
            'wfx_id' => $this->wfx_id,
            'style_code' => $this->style_code,
            'message' => "Estilo {$this->style_code} sincronizado exitosamente con WFX"
        ];
    }

    /**
     * Check if synced to WFX
     */
    public function isSyncedToWFX(): bool
    {
        return !is_null($this->wfx_id) && !is_null($this->synced_to_wfx_at);
    }

    public function tnas(): BelongsToMany
    {
        return $this->belongsToMany(Tna::class, 'techpack_tna');
    }
}
