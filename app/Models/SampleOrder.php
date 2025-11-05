<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SampleOrder extends Model
{
    protected $fillable = [
        'techpack_id',
        'supplier_id',
        'requested_by',
        'requested_at',
        'eta',
        'sizes',
        'status',
        'shipping_address',
        'courier',
        'tracking_number',
        'wfx_sample_id',
        'synced_to_wfx_at',
        'wfx_metadata',
        'shipped_at',
        'packages',
        'weight',
        'notes',
        'attachments_count',
    ];

    protected $casts = [
        'requested_at' => 'date',
        'eta' => 'date',
        'shipped_at' => 'date',
        'synced_to_wfx_at' => 'datetime',
        'sizes' => 'array',
        'wfx_metadata' => 'array',
        'packages' => 'integer',
        'weight' => 'decimal:2',
        'attachments_count' => 'integer',
    ];

    public function techpack(): BelongsTo
    {
        return $this->belongsTo(Techpack::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    protected function sizesInline(): Attribute
    {
        return Attribute::make(
            get: function () {
                $sizes = $this->sizes ?? [];
                $result = [];

                foreach ($sizes as $size => $data) {
                    $client = $data['client'] ?? 0;
                    $wts = $data['wts'] ?? 0;
                    $received = $data['received'] ?? 0;
                    $result[] = "{$size} {$client}/{$wts}/{$received}";
                }

                return implode(' · ', $result);
            }
        );
    }

    /**
     * Check if techpack has fabric assigned
     */
    public function hasFabricAssigned(): bool
    {
        // For mockup purposes, check if techpack has fabric_information in quotes
        return $this->techpack && $this->techpack->quotes()
            ->whereNotNull('fabric_information')
            ->exists();
    }

    /**
     * Sync sample order to WFX (mocked)
     */
    public function syncToWFX(): array
    {
        // Validate fabric is assigned
        if (!$this->hasFabricAssigned()) {
            return [
                'success' => false,
                'error' => 'No se puede sincronizar: el estilo debe tener tela asignada',
                'wfx_sample_id' => null
            ];
        }

        // Simulate API delay
        sleep(1);

        if (!$this->wfx_sample_id) {
            $this->wfx_sample_id = 'WFXSMPL-' . strtoupper(substr(md5($this->id . time()), 0, 10));
            $this->synced_to_wfx_at = now();
            $this->wfx_metadata = [
                'sync_date' => now()->toISOString(),
                'techpack_wfx_id' => $this->techpack->wfx_id ?? null,
                'style_code' => $this->techpack->style_code ?? null,
                'total_samples' => $this->getTotalSamplesCount(),
            ];
            $this->save();
        }

        return [
            'success' => true,
            'wfx_sample_id' => $this->wfx_sample_id,
            'message' => "Sample Order {$this->id} sincronizado exitosamente con WFX"
        ];
    }

    /**
     * Check if synced to WFX
     */
    public function isSyncedToWFX(): bool
    {
        return !is_null($this->wfx_sample_id) && !is_null($this->synced_to_wfx_at);
    }

    /**
     * Get total samples count
     */
    public function getTotalSamplesCount(): int
    {
        $total = 0;
        foreach ($this->sizes ?? [] as $data) {
            $total += ($data['client'] ?? 0) + ($data['wts'] ?? 0);
        }
        return $total;
    }

    /**
     * Get total received count
     */
    public function getTotalReceivedCount(): int
    {
        $total = 0;
        foreach ($this->sizes ?? [] as $data) {
            $total += ($data['received'] ?? 0);
        }
        return $total;
    }
}
