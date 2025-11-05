<?php

namespace App\Filament\Resources\TechpackResource\Pages;

use App\Filament\Resources\TechpackResource;
use App\Models\SampleOrder;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Notifications\Notification;

class ViewTechpack extends ViewRecord
{
    protected static string $resource = TechpackResource::class;

    protected ?string $maxContentWidth = 'full';

    protected function getHeaderActions(): array
    {
        $isSupplierView = session('view_mode', 'wts') === 'supplier';

        return [
            Actions\Action::make('create_sample_order')
                ->label('Solicitar Sample Order')
                ->icon('heroicon-o-beaker')
                ->color('primary')
                ->visible(fn () => !$isSupplierView && $this->record->sampleOrders()->count() === 0)
                ->form([
                    Forms\Components\Select::make('supplier_id')
                        ->label('Proveedor')
                        ->options(\App\Models\Supplier::pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('requested_by')
                        ->label('Solicitado por')
                        ->default(auth()->user()->name)
                        ->required(),
                    Forms\Components\DatePicker::make('requested_at')
                        ->label('Fecha de solicitud')
                        ->default(now())
                        ->required(),
                    Forms\Components\DatePicker::make('eta')
                        ->label('ETA (Fecha estimada de entrega)')
                        ->required(),
                    Forms\Components\Repeater::make('sizes')
                        ->label('Talles y cantidades')
                        ->schema([
                            Forms\Components\TextInput::make('size')
                                ->label('Talle')
                                ->required(),
                            Forms\Components\TextInput::make('client')
                                ->label('Cantidad Cliente')
                                ->numeric()
                                ->default(0)
                                ->required(),
                            Forms\Components\TextInput::make('wts')
                                ->label('Cantidad WTS')
                                ->numeric()
                                ->default(0)
                                ->required(),
                            Forms\Components\TextInput::make('received')
                                ->label('Cantidad Recibida')
                                ->numeric()
                                ->default(0)
                                ->required(),
                        ])
                        ->columns(4)
                        ->defaultItems(1)
                        ->collapsible(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notas')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    // Convertir el array de sizes a formato JSON con keys
                    $sizesFormatted = [];
                    foreach ($data['sizes'] as $size) {
                        $sizesFormatted[$size['size']] = [
                            'client' => $size['client'],
                            'wts' => $size['wts'],
                            'received' => $size['received'],
                        ];
                    }

                    SampleOrder::create([
                        'techpack_id' => $this->record->id,
                        'supplier_id' => $data['supplier_id'],
                        'requested_by' => $data['requested_by'],
                        'requested_at' => $data['requested_at'],
                        'eta' => $data['eta'],
                        'sizes' => $sizesFormatted,
                        'status' => 'requested',
                        'notes' => $data['notes'] ?? null,
                    ]);

                    Notification::make()
                        ->title('Sample Order creado')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),
            Actions\EditAction::make()
                ->visible(fn () => !$isSupplierView),
            Actions\DeleteAction::make()
                ->visible(fn () => !$isSupplierView),
        ];
    }
}
