<?php

namespace App\Filament\Resources\QuoteResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseOrders';

    protected static ?string $title = 'Órdenes de Producción (PO)';

    protected static ?string $icon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'purchase order';

    protected static ?string $pluralModelLabel = 'purchase orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('file_name')
                    ->label('Archivo')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_name')
            ->description('Historial de versiones del PO')
            ->columns([
                Tables\Columns\TextColumn::make('version')
                    ->label('Versión')
                    ->formatStateUsing(fn ($state) => "v{$state}")
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('is_current')
                    ->label('Estado')
                    ->colors([
                        'warning' => true,
                        'gray' => false,
                    ])
                    ->formatStateUsing(fn ($state) => $state ? 'Vigente' : 'Histórica'),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('Archivo')
                    ->icon('heroicon-m-document')
                    ->placeholder('Archivo sin nombre')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de carga')
                    ->dateTime('d M, Y H:i')
                    ->icon('heroicon-m-calendar')
                    ->sortable(),
                Tables\Columns\TextColumn::make('uploaded_by')
                    ->label('Subido por')
                    ->icon('heroicon-m-user')
                    ->placeholder('Desconocido')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notas')
                    ->color('gray')
                    ->placeholder('Sin notas')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('current')
                    ->label('Vigente')
                    ->query(fn (Builder $query): Builder => $query->where('is_current', true)),
            ])
            ->headerActions([
                Tables\Actions\Action::make('import_po')
                    ->label('Importar Orden')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->action(function () {
                        // Acción para importar orden de producción
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
            ->defaultSort('version', 'desc');
    }
}
