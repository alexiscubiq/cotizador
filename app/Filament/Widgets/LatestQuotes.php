<?php

namespace App\Filament\Widgets;

use App\Models\Quote;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestQuotes extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $isWtsInternal = session('view_mode', 'wts') === 'wts';

        return $table
            ->heading('Últimas cotizaciones')
            ->query(
                Quote::query()->latest()->limit(15)
            )
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('N° RFQ')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->icon('heroicon-m-hashtag')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-building-office')
                    ->visible($isWtsInternal),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-user')
                    ->visible(!$isWtsInternal),
                Tables\Columns\TextColumn::make('buyer')
                    ->label('Comprador')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-building-office')
                    ->visible($isWtsInternal)
                    ->getStateUsing(function ($record) {
                        if ($record->buyer) return $record->buyer;
                        $buyers = ["Fashion Retail Corp", "Original Favorites", "Global Apparel Inc", "Textile Sourcing SAC"];
                        return $buyers[array_rand($buyers)];
                    }),
                Tables\Columns\TextColumn::make('buyer_department')
                    ->label('Departamento')
                    ->badge()
                    ->color('gray')
                    ->visible($isWtsInternal)
                    ->getStateUsing(function ($record) {
                        if ($record->buyer_department) return $record->buyer_department;
                        $departments = ["Caballeros", "Damas", "Niños", "Deportivo"];
                        return $departments[array_rand($departments)];
                    }),
                Tables\Columns\TextColumn::make('season')
                    ->label('Temporada')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-m-calendar')
                    ->getStateUsing(function ($record) {
                        if ($record->season) return $record->season;
                        $seasons = ["Primavera 2025", "Verano 2025", "Otoño 2025", "Invierno 2025"];
                        return $seasons[array_rand($seasons)];
                    }),
                Tables\Columns\TextColumn::make('created_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar'),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Entrega')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->delivery_date && $record->delivery_date->isPast() ? 'danger' : 'success')
                    ->icon('heroicon-m-clock')
                    ->tooltip(fn ($record) => $record->delivery_date && $record->delivery_date->isPast()
                        ? '¡Vencida!'
                        : 'Días restantes: ' . now()->diffInDays($record->delivery_date, false)),
                Tables\Columns\TextColumn::make('techpacks_count')
                    ->label('Tech Packs')
                    ->counts('techpacks')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-document-duplicate')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Precio FOB')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('profit_margin')
                    ->label('Margen')
                    ->suffix('%')
                    ->visible($isWtsInternal)
                    ->color('success')
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'pending',
                        'info' => 'in_production',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->icons([
                        'heroicon-m-pencil' => 'draft',
                        'heroicon-m-clock' => 'pending',
                        'heroicon-m-cog-6-tooth' => 'in_production',
                        'heroicon-m-check-circle' => 'completed',
                        'heroicon-m-x-circle' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Borrador',
                        'pending' => 'Pendiente',
                        'in_production' => 'En Producción',
                        'completed' => 'Completado',
                        'cancelled' => 'Cancelado',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('Proveedor')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'pending' => 'Pendiente',
                        'in_production' => 'En Producción',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Quote $record): string => route('filament.admin.resources.quotes.view', $record)),
            ]);
    }
}
