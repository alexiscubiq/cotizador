<?php

namespace App\Filament\Resources\QuoteResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductionMilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'productionMilestones';

    protected static ?string $title = 'Plan de acción y Progreso';

    protected static ?string $icon = 'heroicon-o-clipboard-document-check';

    protected static ?string $modelLabel = 'hito';

    protected static ?string $pluralModelLabel = 'hitos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('milestone')
                    ->label('Hito')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('milestone')
            ->description('Seguimiento detallado según TNA y WIP reportado')
            ->columns([
                Tables\Columns\TextColumn::make('milestone')
                    ->label('Hito')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('planned_at')
                    ->label('Plan (TNA)')
                    ->date('d M, Y')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('actual_at')
                    ->label('Real (WIP)')
                    ->date('d M, Y')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('delay_days')
                    ->label('Δ días')
                    ->colors([
                        'gray' => fn ($state) => is_null($state),
                        'danger' => fn ($state) => !is_null($state) && $state > 0,
                        'success' => fn ($state) => !is_null($state) && $state <= 0,
                    ])
                    ->formatStateUsing(fn ($state) => is_null($state) ? '—' : ($state > 0 ? "+{$state}" : $state))
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'gray' => 'pending',
                        'info' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'delayed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'in_progress' => 'En progreso',
                        'completed' => 'Completado',
                        'delayed' => 'Retrasado',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comentarios')
                    ->color('gray')
                    ->placeholder('Sin comentarios')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'in_progress' => 'En progreso',
                        'completed' => 'Completado',
                        'delayed' => 'Retrasado',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('import_tna')
                    ->label('Importar TNA')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function () {
                        // Acción para importar TNA
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('planned_at', 'asc');
    }
}
