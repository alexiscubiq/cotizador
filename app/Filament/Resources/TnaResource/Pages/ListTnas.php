<?php

namespace App\Filament\Resources\TnaResource\Pages;

use App\Filament\Resources\TnaResource;
use App\Models\Tna;
use App\Models\Quote;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;

class ListTnas extends ListRecords
{
    protected static string $resource = TnaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_csv')
                ->label('Importar desde CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    Forms\Components\Select::make('quote_id')
                        ->label('Cotización')
                        ->options(Quote::all()->pluck('code', 'id'))
                        ->required()
                        ->searchable()
                        ->reactive(),
                    Forms\Components\Select::make('techpacks')
                        ->label('Estilos a asignar')
                        ->multiple()
                        ->options(function (callable $get) {
                            $quoteId = $get('quote_id');
                            if (!$quoteId) {
                                return [];
                            }
                            $quote = Quote::find($quoteId);
                            return $quote ? $quote->techpacks->pluck('name', 'id') : [];
                        })
                        ->helperText('Selecciona los estilos que usarán este TNA'),
                    Forms\Components\FileUpload::make('csv_file')
                        ->label('Archivo CSV')
                        ->acceptedFileTypes(['text/csv', 'text/plain'])
                        ->helperText('Formato: Tarea, Responsable, Fecha Límite (YYYY-MM-DD), Estado, Notas')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $quote = Quote::find($data['quote_id']);
                    $csvPath = storage_path('app/public/' . $data['csv_file']);
                    $csvContent = file_get_contents($csvPath);

                    $tna = Tna::importFromCSV($quote, $csvContent, $data['techpacks'] ?? []);

                    \Filament\Notifications\Notification::make()
                        ->title('TNA importado exitosamente')
                        ->body("Se creó el TNA '{$tna->name}' con " . count($tna->milestones) . " hitos.")
                        ->success()
                        ->send();

                    // Clean up uploaded file
                    @unlink($csvPath);

                    return redirect()->to(TnaResource::getUrl('view', ['record' => $tna]));
                }),
            Actions\CreateAction::make()
                ->label('Crear TNA Manual'),
        ];
    }
}
