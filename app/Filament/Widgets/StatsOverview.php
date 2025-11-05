<?php

namespace App\Filament\Widgets;

use App\Models\Quote;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $activeStatuses = ['pending', 'in_production'];
        $now = now();

        $activeQuotes = Quote::whereIn('status', $activeStatuses)->count();
        $expiredQuotes = Quote::whereIn('status', $activeStatuses)
            ->whereDate('delivery_date', '<', $now->toDateString())
            ->count();

        $completedThisMonth = Quote::where('status', 'completed')
            ->whereBetween('updated_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();

        $completedLastMonth = Quote::where('status', 'completed')
            ->whereBetween('updated_at', [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()])
            ->count();

        $totalActiveValue = Quote::whereIn('status', $activeStatuses)
            ->sum('total_cost');

        $slaTargetQuotes = Quote::where('status', 'completed')
            ->whereNotNull('delivery_date')
            ->count();

        $slaMet = Quote::where('status', 'completed')
            ->whereNotNull('delivery_date')
            ->whereColumn('delivery_date', '>=', 'updated_at')
            ->count();

        $slaPerformance = $slaTargetQuotes > 0
            ? round(($slaMet / $slaTargetQuotes) * 100)
            : 0;

        $completedDiff = $completedThisMonth - $completedLastMonth;
        $completedTrend = $completedDiff > 0
            ? 'Incremento vs mes anterior'
            : ($completedDiff < 0 ? 'Por debajo del mes anterior' : 'Igual que el mes anterior');

        return [
            Stat::make('Cotizaciones Activas', $activeQuotes)
                ->description($expiredQuotes . ' vencidas')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color($expiredQuotes > 0 ? 'warning' : 'success'),

            Stat::make('Performance SLA', $slaPerformance . '%')
                ->description('Cumplimiento este mes')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($slaPerformance >= 90 ? 'success' : ($slaPerformance >= 70 ? 'warning' : 'danger')),

            Stat::make('Valor Total Activo', '$' . number_format($totalActiveValue, 0, '.', ','))
                ->description('En cotizaciones abiertas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Completadas este Mes', $completedThisMonth)
                ->description($completedTrend)
                ->descriptionIcon($completedDiff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($completedDiff >= 0 ? 'success' : 'warning'),
        ];
    }
}
