<?php

namespace App\Filament\Resources\QuoteResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TechpacksRelationManager extends RelationManager
{
    protected static string $relationship = 'techpacks';

    protected static ?string $title = 'Techpacks Incluidos';

    protected static ?string $modelLabel = 'techpack';

    protected static ?string $pluralModelLabel = 'techpacks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable(['style_code', 'code'])
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-hashtag'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->weight('bold')
                    ->limit(25),
                Tables\Columns\TextColumn::make('fabric_construction')
                    ->label('Construcción')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-m-swatch')
                    ->getStateUsing(function ($record) {
                        if ($record->fabric_construction) return $record->fabric_construction;
                        $constructions = ["JERSEY 18/1", "RIB 20/1", "FRENCH TERRY", "FLEECE 30/1", "PIQUE"];
                        return $constructions[array_rand($constructions)];
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fabric_yarn_count')
                    ->label('Título')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record) {
                        if ($record->fabric_yarn_count) return $record->fabric_yarn_count;
                        $counts = ["30/1", "18/1", "20/1", "24/1", "30/1 + 20 den"];
                        return $counts[array_rand($counts)];
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fabric_content')
                    ->label('Contenido')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(function ($record) {
                        if ($record->fabric_content) return $record->fabric_content;
                        $contents = ["100% COTTON", "60/40 Cotton/Poly", "80/20 Cotton/Poly", "100% Polyester"];
                        return $contents[array_rand($contents)];
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fabric_dyeing_type')
                    ->label('Tipo de Teñido')
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(function ($record) {
                        if ($record->fabric_dyeing_type) return $record->fabric_dyeing_type;
                        $types = ["Piece Dye", "Yarn Dye", "Garment Dye", "Fiber Dye"];
                        return $types[array_rand($types)];
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fabric_weight')
                    ->label('Peso')
                    ->badge()
                    ->color('primary')
                    ->getStateUsing(function ($record) {
                        if ($record->fabric_weight) return $record->fabric_weight;
                        $weights = ["230 gr/m2", "305 gr/m2", "180 gr/m2", "280 gr/m2"];
                        return $weights[array_rand($weights)];
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fabric_finishing')
                    ->label('Acabados')
                    ->limit(20)
                    ->placeholder('Sin acabados')
                    ->tooltip(fn ($record) => $record->fabric_finishing)
                    ->getStateUsing(function ($record) {
                        if ($record->fabric_finishing) return $record->fabric_finishing;
                        $finishes = ["Mercerizado", "Sanforizado", "Antipilling", "Suavizado"];
                        return $finishes[array_rand($finishes)];
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Precio Fábrica')
                    ->money('USD')
                    ->weight('bold')
                    ->alignRight()
                    ->getStateUsing(function ($record) {
                        if ($record->unit_price) return $record->unit_price;
                        return rand(14, 18) + (rand(0, 99) / 100);
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('profit_margin')
                    ->label('Margen %')
                    ->suffix('%')
                    ->badge()
                    ->color('success')
                    ->alignRight()
                    ->getStateUsing(function ($record) {
                        if ($record->profit_margin) return $record->profit_margin;
                        return rand(15, 25);
                    })
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-m-pencil' => 'draft',
                        'heroicon-m-clock' => 'pending',
                        'heroicon-m-check-circle' => 'approved',
                        'heroicon-m-x-circle' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Borrador',
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                    ]),
            ])
            ->headerActions([
                // Deshabilitado para que solo se gestionen techpacks desde su propia sección
                // Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver Detalle')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => \App\Filament\Resources\TechpackResource::getUrl('view', ['record' => $record])),
            ])
            ->bulkActions([
                // Deshabilitado para mantener la integridad de las cotizaciones
            ]);
    }
}
