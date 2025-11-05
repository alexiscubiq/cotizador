<?php

namespace App\Filament\Resources\TechpackResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class SampleOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'sampleOrders';

    protected static ?string $title = 'Sample Orders (Muestras)';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('supplier_id')
                    ->label('Proveedor')
                    ->relationship('supplier', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('requested_by')
                    ->label('Solicitada por')
                    ->required(),
                Forms\Components\DatePicker::make('requested_at')
                    ->label('Fecha solicitud')
                    ->required()
                    ->default(now())
                    ->displayFormat('d/m/Y'),
                Forms\Components\DatePicker::make('eta')
                    ->label('ETA')
                    ->displayFormat('d/m/Y'),
                Forms\Components\Textarea::make('shipping_address')
                    ->label('Dirección de envío')
                    ->rows(2),
                Forms\Components\Repeater::make('sizes')
                    ->label('Talles (Cliente / WTS / Recibidas)')
                    ->schema([
                        Forms\Components\Select::make('size')
                            ->label('Talle')
                            ->options([
                                'S' => 'S',
                                'M' => 'M',
                                'L' => 'L',
                                'XL' => 'XL',
                                'XXL' => 'XXL',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('client')
                            ->label('Cliente')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\TextInput::make('wts')
                            ->label('WTS')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\TextInput::make('received')
                            ->label('Recibidas')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(4)
                    ->defaultItems(3)
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'requested' => 'Solicitada',
                        'in_production' => 'En producción',
                        'shipped' => 'Enviada',
                        'received' => 'Recibida',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                    ])
                    ->default('requested')
                    ->required(),
                Forms\Components\Section::make('Información de envío')
                    ->schema([
                        Forms\Components\TextInput::make('courier')
                            ->label('Courier'),
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('Número de seguimiento'),
                        Forms\Components\DatePicker::make('shipped_at')
                            ->label('Fecha de despacho')
                            ->displayFormat('d/m/Y'),
                        Forms\Components\TextInput::make('packages')
                            ->label('Bultos')
                            ->numeric(),
                        Forms\Components\TextInput::make('weight')
                            ->label('Peso (kg)')
                            ->numeric(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('requested_by')
                    ->label('Solicitada por')
                    ->searchable(),
                Tables\Columns\TextColumn::make('requested_at')
                    ->label('Fecha solicitud')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('eta')
                    ->label('ETA')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sizes')
                    ->label('Talles (C/W/R)')
                    ->html()
                    ->getStateUsing(function ($record) {
                        if (!$record->sizes) return 'N/A';

                        $result = [];
                        foreach ($record->sizes as $size => $data) {
                            $client = $data['client'] ?? 0;
                            $wts = $data['wts'] ?? 0;
                            $received = $data['received'] ?? 0;
                            $result[] = "<span class='font-medium'>{$size}</span> {$client}/{$wts}/{$received}";
                        }

                        return implode(' · ', $result);
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'requested',
                        'info' => 'in_production',
                        'warning' => 'shipped',
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
                Tables\Columns\TextColumn::make('wfx_sample_id')
                    ->label('WFX ID')
                    ->badge()
                    ->color('success')
                    ->default('No sincronizado')
                    ->formatStateUsing(fn ($state) => $state ?: 'No sync')
                    ->visible(fn () => session('view_mode', 'wts') === 'wts'),
                Tables\Columns\TextColumn::make('attachments_count')
                    ->label('Adjuntos')
                    ->badge()
                    ->color('gray'),
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
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('Proveedor')
                    ->relationship('supplier', 'name'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nueva muestra')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Convert sizes array to keyed format
                        if (isset($data['sizes']) && is_array($data['sizes'])) {
                            $sizes = [];
                            foreach ($data['sizes'] as $item) {
                                $sizes[$item['size']] = [
                                    'client' => $item['client'],
                                    'wts' => $item['wts'],
                                    'received' => $item['received'],
                                ];
                            }
                            $data['sizes'] = $sizes;
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('sync_wfx')
                    ->label('Sincronizar WFX')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn ($record) => session('view_mode', 'wts') === 'wts' && !$record->isSyncedToWFX())
                    ->requiresConfirmation()
                    ->modalHeading('Sincronizar Sample Order a WFX')
                    ->modalDescription(fn ($record) => $record->hasFabricAssigned()
                        ? 'Esta acción creará el sample order en WFX.'
                        : 'ATENCIÓN: Este estilo no tiene tela asignada. Debe asignar tela antes de sincronizar.')
                    ->modalIcon('heroicon-o-arrow-path')
                    ->action(function ($record) {
                        $result = $record->syncToWFX();

                        if ($result['success']) {
                            \Filament\Notifications\Notification::make()
                                ->title('¡Sincronización exitosa!')
                                ->body($result['message'])
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Error en la sincronización')
                                ->body($result['error'])
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\ViewAction::make()
                    ->modalWidth('5xl'),
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->fillForm(function ($record): array {
                        // Convert sizes keyed format to array for form
                        $data = $record->toArray();
                        if (isset($data['sizes']) && is_array($data['sizes'])) {
                            $sizes = [];
                            foreach ($data['sizes'] as $size => $values) {
                                $sizes[] = [
                                    'size' => $size,
                                    'client' => $values['client'] ?? 0,
                                    'wts' => $values['wts'] ?? 0,
                                    'received' => $values['received'] ?? 0,
                                ];
                            }
                            $data['sizes'] = $sizes;
                        }
                        return $data;
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        // Convert sizes array back to keyed format
                        if (isset($data['sizes']) && is_array($data['sizes'])) {
                            $sizes = [];
                            foreach ($data['sizes'] as $item) {
                                $sizes[$item['size']] = [
                                    'client' => $item['client'],
                                    'wts' => $item['wts'],
                                    'received' => $item['received'],
                                ];
                            }
                            $data['sizes'] = $sizes;
                        }
                        return $data;
                    }),
                Tables\Actions\Action::make('register_receipt')
                    ->label('Registrar recepción')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form(fn ($record) => [
                        Forms\Components\Repeater::make('sizes')
                            ->label('Actualizar cantidades recibidas')
                            ->schema([
                                Forms\Components\TextInput::make('size')
                                    ->label('Talle')
                                    ->disabled(),
                                Forms\Components\TextInput::make('client')
                                    ->label('Cliente')
                                    ->disabled(),
                                Forms\Components\TextInput::make('wts')
                                    ->label('WTS')
                                    ->disabled(),
                                Forms\Components\TextInput::make('received')
                                    ->label('Recibidas')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(4)
                            ->default(function ($record) {
                                if (!$record->sizes) return [];

                                $result = [];
                                foreach ($record->sizes as $size => $data) {
                                    $result[] = [
                                        'size' => $size,
                                        'client' => $data['client'],
                                        'wts' => $data['wts'],
                                        'received' => $data['received'],
                                    ];
                                }
                                return $result;
                            })
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ])
                    ->action(function ($record, array $data) {
                        $sizes = [];
                        foreach ($data['sizes'] as $sizeData) {
                            $sizes[$sizeData['size']] = [
                                'client' => $sizeData['client'],
                                'wts' => $sizeData['wts'],
                                'received' => $sizeData['received'],
                            ];
                        }

                        $record->update(['sizes' => $sizes, 'status' => 'received']);
                    }),
                Tables\Actions\Action::make('attach')
                    ->label('Adjuntar')
                    ->icon('heroicon-o-paper-clip')
                    ->form([
                        Forms\Components\FileUpload::make('attachments')
                            ->label('Archivos adjuntos')
                            ->multiple()
                            ->directory('sample-order-attachments'),
                    ])
                    ->action(function ($record, array $data) {
                        // For MVP, just increment counter
                        $record->increment('attachments_count', count($data['attachments'] ?? []));
                    }),
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
                Section::make('Encabezado')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('techpack.name')
                                    ->label('Techpack'),
                                TextEntry::make('techpack.version')
                                    ->label('Versión'),
                                TextEntry::make('supplier.name')
                                    ->label('Proveedor'),
                                TextEntry::make('status')
                                    ->label('Estado')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'requested' => 'secondary',
                                        'in_production' => 'info',
                                        'shipped' => 'warning',
                                        'received' => 'primary',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'requested' => 'Solicitada',
                                        'in_production' => 'En producción',
                                        'shipped' => 'Enviada',
                                        'received' => 'Recibida',
                                        'approved' => 'Aprobada',
                                        'rejected' => 'Rechazada',
                                        default => $state,
                                    }),
                            ]),
                    ]),
                Section::make('Resumen')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('requested_by')
                                    ->label('Solicitada por'),
                                TextEntry::make('requested_at')
                                    ->label('Fecha solicitud')
                                    ->date('d/m/Y'),
                                TextEntry::make('eta')
                                    ->label('ETA')
                                    ->date('d/m/Y'),
                                TextEntry::make('shipping_address')
                                    ->label('Dirección de envío')
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Section::make('Talles (Cliente / WTS / Recibidas / Faltan)')
                    ->schema([
                        TextEntry::make('sizes')
                            ->label('')
                            ->html()
                            ->getStateUsing(function ($record) {
                                if (!$record->sizes) return 'No hay talles registrados';

                                $html = '<table class="w-full text-sm"><thead><tr class="border-b">';
                                $html .= '<th class="text-left p-2">Talle</th>';
                                $html .= '<th class="text-center p-2">Cliente</th>';
                                $html .= '<th class="text-center p-2">WTS</th>';
                                $html .= '<th class="text-center p-2">Recibidas</th>';
                                $html .= '<th class="text-center p-2">Faltan</th>';
                                $html .= '</tr></thead><tbody>';

                                foreach ($record->sizes as $size => $data) {
                                    $client = $data['client'] ?? 0;
                                    $wts = $data['wts'] ?? 0;
                                    $received = $data['received'] ?? 0;
                                    $missing = ($client + $wts) - $received;

                                    $html .= '<tr class="border-b">';
                                    $html .= "<td class='p-2 font-medium'>{$size}</td>";
                                    $html .= "<td class='text-center p-2'>{$client}</td>";
                                    $html .= "<td class='text-center p-2'>{$wts}</td>";
                                    $html .= "<td class='text-center p-2'>{$received}</td>";
                                    $html .= "<td class='text-center p-2 " . ($missing > 0 ? 'text-red-600 font-semibold' : 'text-green-600') . "'>{$missing}</td>";
                                    $html .= '</tr>';
                                }

                                $html .= '</tbody></table>';
                                return $html;
                            }),
                    ]),
                Section::make('Integración WFX')
                    ->description('Estado de sincronización con WFX')
                    ->icon('heroicon-o-cloud')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('wfx_sample_id')
                                    ->label('WFX Sample ID')
                                    ->badge()
                                    ->color('success')
                                    ->default('No sincronizado'),
                                TextEntry::make('synced_to_wfx_at')
                                    ->label('Fecha de sincronización')
                                    ->dateTime('d/m/Y H:i')
                                    ->default('No sincronizado'),
                                TextEntry::make('wfx_metadata')
                                    ->label('Total de muestras')
                                    ->getStateUsing(fn ($record) => $record->wfx_metadata['total_samples'] ?? $record->getTotalSamplesCount())
                                    ->suffix(' unidades')
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),
                Section::make('Envío')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('courier')
                                    ->label('Courier'),
                                TextEntry::make('tracking_number')
                                    ->label('Tracking'),
                                TextEntry::make('shipped_at')
                                    ->label('Fecha de despacho')
                                    ->date('d/m/Y'),
                                TextEntry::make('packages')
                                    ->label('Bultos'),
                                TextEntry::make('weight')
                                    ->label('Peso (kg)')
                                    ->suffix(' kg'),
                            ]),
                    ]),
                Section::make('Notas y Adjuntos')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Notas')
                            ->default('Sin notas')
                            ->columnSpanFull(),
                        TextEntry::make('attachments_count')
                            ->label('Adjuntos')
                            ->badge()
                            ->color('gray')
                            ->suffix(' archivos'),
                    ]),
            ]);
    }
}
