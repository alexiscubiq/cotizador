<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommercialTeamResource\Pages;
use App\Filament\Resources\CommercialTeamResource\RelationManagers;
use App\Models\CommercialTeam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommercialTeamResource extends Resource
{
    protected static ?string $model = CommercialTeam::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Equipos comerciales';

    protected static ?string $modelLabel = 'Equipo comercial';

    protected static ?string $pluralModelLabel = 'Equipos comerciales';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        return session('view_mode', 'wts') === 'wts';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommercialTeams::route('/'),
            'create' => Pages\CreateCommercialTeam::route('/create'),
            'edit' => Pages\EditCommercialTeam::route('/{record}/edit'),
        ];
    }
}
