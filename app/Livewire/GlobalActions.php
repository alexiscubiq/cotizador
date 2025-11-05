<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Models\Techpack;

class GlobalActions extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public function isWtsMode(): bool
    {
        return session('view_mode', 'wts') === 'wts';
    }

    public function cargarTechpackAction(): Action
    {
        return Action::make('cargarTechpack')
            ->label('Cargar techpack')
            ->icon('heroicon-o-plus')
            ->color('warning')
            ->outlined()
            ->size('sm')
            ->modalHeading('Subir techpack')
            ->modalDescription('Selecciona el cliente y carga uno o más archivos PDF')
            ->modalSubmitActionLabel('Cargar')
            ->form([
                Forms\Components\Select::make('client_id')
                    ->label('Cliente')
                    ->options(\App\Models\Client::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->helperText('Selecciona el cliente al que pertenece este techpack'),

                Forms\Components\FileUpload::make('original_file_path')
                    ->label('Archivos PDF')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(51200) // 50 MB
                    ->multiple()
                    ->directory('techpack-originals')
                    ->helperText('Solo archivos PDF, máximo 50 MB por archivo')
                    ->required()
                    ->columnSpanFull(),
            ])
            ->action(function (array $data): void {
                $files = $data['original_file_path'];
                $clientId = $data['client_id'];

                foreach ($files as $index => $file) {
                    Techpack::create([
                        'client_id' => $clientId,
                        'name' => 'Techpack ' . ($index + 1),
                        'code' => 'TP-' . now()->format('Y') . '-' . str_pad(Techpack::count() + $index + 1, 3, '0', STR_PAD_LEFT),
                        'original_file_path' => $file,
                        'status' => 'pending',
                        'version' => 1,
                        'uploaded_at' => now(),
                    ]);
                }

                Notification::make()
                    ->title('Techpacks cargados')
                    ->body('Los techpacks se están procesando. Te notificaremos cuando estén listos para revisión.')
                    ->success()
                    ->send();
            });
    }

    public function nuevaCotizacionAction(): Action
    {
        return Action::make('nuevaCotizacion')
            ->label('Nueva cotización')
            ->icon('heroicon-o-plus')
            ->color('warning')
            ->size('sm')
            ->url(fn (): string => route('filament.admin.resources.quotes.create'));
    }

    public function render()
    {
        return view('livewire.global-actions');
    }
}
