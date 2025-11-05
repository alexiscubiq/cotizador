<?php

namespace App\Filament\Resources\QuoteResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SampleOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'sampleOrders';

    protected static ?string $title = 'Sample Orders';

    protected static ?string $icon = 'heroicon-o-beaker';

    protected static ?string $modelLabel = 'sample order';

    protected static ?string $pluralModelLabel = 'sample orders';

    protected static ?string $recordTitleAttribute = 'techpack_id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('techpack_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('Seguimiento de solicitudes de muestra por talle')
            ->columns([
                Tables\Columns\TextColumn::make('techpack.code')
                    ->label('Código TP')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-hashtag')
                    ->searchable(),
                Tables\Columns\TextColumn::make('techpack.name')
                    ->label('Techpack')
                    ->weight('bold')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('requested_by')
                    ->label('Solicitado por')
                    ->icon('heroicon-m-user')
                    ->getStateUsing(function ($record) {
                        if ($record->requested_by) return $record->requested_by;
                        $users = ["María González", "Juan Pérez", "Carlos Rodríguez", "Ana López", "Pedro Martínez", "Laura Sánchez"];
                        return $users[array_rand($users)];
                    }),
                Tables\Columns\TextColumn::make('requested_at')
                    ->label('Fecha')
                    ->date('d M, Y')
                    ->icon('heroicon-m-calendar')
                    ->sortable(),
                Tables\Columns\TextColumn::make('eta')
                    ->label('ETA')
                    ->date('d M, Y')
                    ->icon('heroicon-m-clock')
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->icon('heroicon-m-building-office')
                    ->placeholder('Sin asignar')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'gray' => 'requested',
                        'warning' => 'in_production',
                        'info' => 'shipped',
                        'primary' => 'received',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'requested' => 'Solicitada',
                        'in_production' => 'En producción',
                        'shipped' => 'Enviada',
                        'received' => 'Recibida',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'requested' => 'Solicitada',
                        'in_production' => 'En producción',
                        'shipped' => 'Enviada',
                        'received' => 'Recibida',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create_sample')
                    ->label('Solicitar Sample Order')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->action(function () {
                        // Acción para crear sample order
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detalle por Talle')
                    ->icon('heroicon-o-table-cells')
                    ->modalHeading(fn ($record) => 'Detalle por Talle - ' . $record->techpack->name)
                    ->modalWidth('5xl'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detalle por Talle')
                    ->description('Cantidades solicitadas por el cliente, para WTS y recibidas')
                    ->schema([
                        Infolists\Components\ViewEntry::make('sizes')
                            ->label('')
                            ->view('filament.tables.sample-order-sizes'),
                    ]),
            ]);
    }
}
