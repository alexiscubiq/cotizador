<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TnaResource\Pages;
use App\Models\Tna;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\FontWeight;

class TnaResource extends Resource
{
    protected static ?string $model = Tna::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $modelLabel = 'TNA';

    protected static ?string $pluralModelLabel = 'TNAs (Time & Action)';

    protected static ?string $navigationGroup = 'Producción';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\Select::make('quote_id')
                            ->label('Cotización')
                            ->relationship('quote', 'code')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $quote = Quote::find($state);
                                    $set('name', 'TNA - ' . $quote->code);
                                }
                            }),
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del TNA')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(2),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de inicio')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de fin estimada')
                            ->displayFormat('d/m/Y'),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'active' => 'Activo',
                                'on_track' => 'En Tiempo',
                                'at_risk' => 'En Riesgo',
                                'delayed' => 'Retrasado',
                                'completed' => 'Completado',
                            ])
                            ->default('draft')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Asignar a Estilos')
                    ->description('Selecciona los techpacks que usarán este TNA')
                    ->schema([
                        Forms\Components\Select::make('techpacks')
                            ->label('Estilos')
                            ->multiple()
                            ->relationship('techpacks', 'name')
                            ->preload()
                            ->searchable(),
                    ]),

                Forms\Components\Section::make('Hitos del TNA')
                    ->description('Define las tareas y fechas límite')
                    ->schema([
                        Forms\Components\Repeater::make('milestones')
                            ->label('Hitos')
                            ->schema([
                                Forms\Components\TextInput::make('task')
                                    ->label('Tarea')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('responsible')
                                    ->label('Responsable')
                                    ->default('Sin asignar'),
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Fecha límite')
                                    ->required()
                                    ->displayFormat('d/m/Y'),
                                Forms\Components\Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'pending' => 'Pendiente',
                                        'in_progress' => 'En Progreso',
                                        'completed' => 'Completado',
                                        'delayed' => 'Retrasado',
                                    ])
                                    ->default('pending')
                                    ->required(),
                                Forms\Components\DatePicker::make('completed_date')
                                    ->label('Fecha completada')
                                    ->displayFormat('d/m/Y'),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notas')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->defaultItems(5)
                            ->columnSpanFull()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['task'] ?? 'Nueva tarea'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('quote.code')
                    ->label('Cotización')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('milestones')
                    ->label('Progreso')
                    ->formatStateUsing(function ($record) {
                        $percentage = $record->getCompletionPercentage();
                        $total = count($record->milestones ?? []);
                        $completed = (int) round(($percentage / 100) * $total);
                        return "{$completed}/{$total} ({$percentage}%)";
                    })
                    ->badge()
                    ->color(fn ($record) => match(true) {
                        $record->getCompletionPercentage() == 100 => 'success',
                        $record->getCompletionPercentage() >= 75 => 'primary',
                        $record->getCompletionPercentage() >= 50 => 'warning',
                        default => 'gray'
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'active',
                        'success' => 'on_track',
                        'warning' => 'at_risk',
                        'danger' => 'delayed',
                        'primary' => 'completed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Borrador',
                        'active' => 'Activo',
                        'on_track' => 'En Tiempo',
                        'at_risk' => 'En Riesgo',
                        'delayed' => 'Retrasado',
                        'completed' => 'Completado',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('imported_from')
                    ->label('Origen')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => $state ?? 'Manual'),
                Tables\Columns\TextColumn::make('techpacks_count')
                    ->label('Estilos')
                    ->counts('techpacks')
                    ->badge()
                    ->color('info')
                    ->suffix(' estilos'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'active' => 'Activo',
                        'on_track' => 'En Tiempo',
                        'at_risk' => 'En Riesgo',
                        'delayed' => 'Retrasado',
                        'completed' => 'Completado',
                    ]),
                Tables\Filters\SelectFilter::make('quote_id')
                    ->label('Cotización')
                    ->relationship('quote', 'code'),
            ])
            ->actions([
                Tables\Actions\Action::make('update_status')
                    ->label('Actualizar Estado')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function ($record) {
                        $record->updateStatus();
                        \Filament\Notifications\Notification::make()
                            ->title('Estado actualizado')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información General')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nombre')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('quote.code')
                                    ->label('Cotización')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('status')
                                    ->label('Estado')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'active' => 'info',
                                        'on_track' => 'success',
                                        'at_risk' => 'warning',
                                        'delayed' => 'danger',
                                        'completed' => 'primary',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'draft' => 'Borrador',
                                        'active' => 'Activo',
                                        'on_track' => 'En Tiempo',
                                        'at_risk' => 'En Riesgo',
                                        'delayed' => 'Retrasado',
                                        'completed' => 'Completado',
                                        default => $state,
                                    }),
                            ]),
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('start_date')
                                    ->label('Fecha de inicio')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-m-calendar'),
                                TextEntry::make('end_date')
                                    ->label('Fecha de fin')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-m-calendar'),
                                TextEntry::make('completion')
                                    ->label('Progreso')
                                    ->getStateUsing(fn ($record) => $record->getCompletionPercentage() . '%')
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('delayed')
                                    ->label('Retrasados')
                                    ->getStateUsing(fn ($record) => $record->getDelayedCount())
                                    ->badge()
                                    ->color('danger')
                                    ->suffix(' hitos'),
                            ]),
                        TextEntry::make('description')
                            ->label('Descripción')
                            ->columnSpanFull()
                            ->default('Sin descripción'),
                    ]),

                Section::make('Estilos Asociados')
                    ->description('Techpacks que usan este TNA')
                    ->schema([
                        TextEntry::make('techpacks')
                            ->label('')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->formatStateUsing(fn ($record) => $record->techpacks->pluck('style_code')->filter()->implode(', ') ?: $record->techpacks->pluck('name')->implode(', '))
                            ->default('Sin estilos asignados'),
                    ])
                    ->collapsible(),

                Section::make('Hitos del TNA')
                    ->description('Timeline de tareas y fechas límite')
                    ->schema([
                        TextEntry::make('milestones')
                            ->label('')
                            ->columnSpanFull()
                            ->html()
                            ->getStateUsing(function ($record) {
                                $milestones = $record->milestones ?? [];
                                if (empty($milestones)) {
                                    return '<div class="text-sm text-gray-500">No hay hitos registrados</div>';
                                }

                                $html = '<div class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">';
                                $html .= '<table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">';
                                $html .= '<thead class="divide-y divide-gray-200 dark:divide-white/5">';
                                $html .= '<tr class="bg-gray-50 dark:bg-white/5">';
                                $html .= '<th class="fi-ta-header-cell px-3 py-3.5 font-semibold text-sm text-gray-950 dark:text-white" style="width: 40%;">Tarea</th>';
                                $html .= '<th class="fi-ta-header-cell px-3 py-3.5 font-semibold text-sm text-gray-950 dark:text-white">Responsable</th>';
                                $html .= '<th class="fi-ta-header-cell px-3 py-3.5 font-semibold text-sm text-gray-950 dark:text-white text-center">Fecha Límite</th>';
                                $html .= '<th class="fi-ta-header-cell px-3 py-3.5 font-semibold text-sm text-gray-950 dark:text-white text-center">Estado</th>';
                                $html .= '<th class="fi-ta-header-cell px-3 py-3.5 font-semibold text-sm text-gray-950 dark:text-white text-center">Completada</th>';
                                $html .= '</tr>';
                                $html .= '</thead>';
                                $html .= '<tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">';

                                foreach ($milestones as $milestone) {
                                    $statusColor = match($milestone['status'] ?? 'pending') {
                                        'completed' => 'bg-success-50 text-success-600 ring-success-600/10',
                                        'in_progress' => 'bg-info-50 text-info-600 ring-info-600/10',
                                        'delayed' => 'bg-danger-50 text-danger-600 ring-danger-600/10',
                                        default => 'bg-gray-50 text-gray-600 ring-gray-600/10'
                                    };

                                    $statusLabel = match($milestone['status'] ?? 'pending') {
                                        'completed' => 'Completado',
                                        'in_progress' => 'En Progreso',
                                        'delayed' => 'Retrasado',
                                        default => 'Pendiente'
                                    };

                                    $html .= '<tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5">';
                                    $html .= '<td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white font-medium">' . e($milestone['task']) . '</td>';
                                    $html .= '<td class="fi-ta-cell px-3 py-4 text-sm text-gray-500 dark:text-gray-400">' . e($milestone['responsible'] ?? 'Sin asignar') . '</td>';
                                    $html .= '<td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white text-center">' . ($milestone['due_date'] ? date('d/m/Y', strtotime($milestone['due_date'])) : '-') . '</td>';
                                    $html .= '<td class="fi-ta-cell px-3 py-4 text-center"><span class="fi-badge ' . $statusColor . ' px-2 py-1 rounded-md text-xs font-medium">' . $statusLabel . '</span></td>';
                                    $html .= '<td class="fi-ta-cell px-3 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">' . ($milestone['completed_date'] ? date('d/m/Y', strtotime($milestone['completed_date'])) : '-') . '</td>';
                                    $html .= '</tr>';
                                }

                                $html .= '</tbody>';
                                $html .= '</table>';
                                $html .= '</div>';

                                return $html;
                            }),
                    ]),

                Section::make('Información de Importación')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('imported_from')
                                    ->label('Importado desde')
                                    ->badge()
                                    ->color('info')
                                    ->default('Manual'),
                                TextEntry::make('imported_at')
                                    ->label('Fecha de importación')
                                    ->dateTime('d/m/Y H:i')
                                    ->default('N/A'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true),
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
            'index' => Pages\ListTnas::route('/'),
            'create' => Pages\CreateTna::route('/create'),
            'view' => Pages\ViewTna::route('/{record}'),
            'edit' => Pages\EditTna::route('/{record}/edit'),
        ];
    }
}
