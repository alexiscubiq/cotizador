<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Tna extends Model
{
    protected $fillable = [
        'quote_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'milestones',
        'status',
        'imported_from',
        'imported_at',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'imported_at' => 'datetime',
        'milestones' => 'array',
        'metadata' => 'array',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function techpacks(): BelongsToMany
    {
        return $this->belongsToMany(Techpack::class, 'techpack_tna');
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentage(): int
    {
        $milestones = $this->milestones ?? [];
        if (empty($milestones)) {
            return 0;
        }

        $completed = collect($milestones)->filter(fn($m) => ($m['status'] ?? '') === 'completed')->count();
        return (int) round(($completed / count($milestones)) * 100);
    }

    /**
     * Get delayed milestones count
     */
    public function getDelayedCount(): int
    {
        $today = Carbon::today();
        $milestones = $this->milestones ?? [];

        return collect($milestones)->filter(function($m) use ($today) {
            $status = $m['status'] ?? 'pending';
            $dueDate = isset($m['due_date']) ? Carbon::parse($m['due_date']) : null;

            return $status !== 'completed' && $dueDate && $dueDate->lt($today);
        })->count();
    }

    /**
     * Get at-risk milestones count (due within 3 days)
     */
    public function getAtRiskCount(): int
    {
        $today = Carbon::today();
        $threshold = $today->copy()->addDays(3);
        $milestones = $this->milestones ?? [];

        return collect($milestones)->filter(function($m) use ($today, $threshold) {
            $status = $m['status'] ?? 'pending';
            $dueDate = isset($m['due_date']) ? Carbon::parse($m['due_date']) : null;

            return $status !== 'completed' && $dueDate && $dueDate->between($today, $threshold);
        })->count();
    }

    /**
     * Auto-update status based on milestones
     */
    public function updateStatus(): void
    {
        $delayedCount = $this->getDelayedCount();
        $atRiskCount = $this->getAtRiskCount();
        $completionPercentage = $this->getCompletionPercentage();

        if ($completionPercentage === 100) {
            $this->status = 'completed';
        } elseif ($delayedCount > 0) {
            $this->status = 'delayed';
        } elseif ($atRiskCount > 0) {
            $this->status = 'at_risk';
        } elseif ($completionPercentage > 0) {
            $this->status = 'on_track';
        } else {
            $this->status = 'active';
        }

        $this->save();
    }

    /**
     * Import TNA from CSV data
     */
    public static function importFromCSV(Quote $quote, string $csvContent, array $techpackIds = []): self
    {
        $lines = array_map('str_getcsv', explode("\n", trim($csvContent)));
        $headers = array_shift($lines);

        $milestones = [];
        $startDate = null;
        $endDate = null;

        foreach ($lines as $index => $row) {
            if (count($row) < 3) continue; // Skip invalid rows

            $milestone = [
                'task' => $row[0] ?? "Tarea " . ($index + 1),
                'responsible' => $row[1] ?? 'Sin asignar',
                'due_date' => $row[2] ?? null,
                'status' => $row[3] ?? 'pending',
                'notes' => $row[4] ?? null,
                'completed_date' => null,
            ];

            $milestones[] = $milestone;

            // Track date range
            if ($milestone['due_date']) {
                $date = Carbon::parse($milestone['due_date']);
                if (!$startDate || $date->lt($startDate)) {
                    $startDate = $date;
                }
                if (!$endDate || $date->gt($endDate)) {
                    $endDate = $date;
                }
            }
        }

        $tna = self::create([
            'quote_id' => $quote->id,
            'name' => 'TNA - ' . $quote->code,
            'description' => 'Importado desde CSV',
            'start_date' => $startDate ?? now(),
            'end_date' => $endDate,
            'milestones' => $milestones,
            'status' => 'active',
            'imported_from' => 'CSV',
            'imported_at' => now(),
            'metadata' => [
                'total_milestones' => count($milestones),
                'import_date' => now()->toISOString(),
            ]
        ]);

        // Attach techpacks
        if (!empty($techpackIds)) {
            $tna->techpacks()->attach($techpackIds);
        }

        $tna->updateStatus();

        return $tna;
    }
}
