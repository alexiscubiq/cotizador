<?php

namespace App\Filament\Resources\TnaResource\Pages;

use App\Filament\Resources\TnaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTna extends EditRecord
{
    protected static string $resource = TnaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('update_status')
                ->label('Actualizar Estado Automáticamente')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function () {
                    $this->record->updateStatus();
                    \Filament\Notifications\Notification::make()
                        ->title('Estado actualizado')
                        ->body('El estado se actualizó basándose en el progreso de los hitos.')
                        ->success()
                        ->send();
                }),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['metadata'] = array_merge($data['metadata'] ?? [], [
            'total_milestones' => count($data['milestones'] ?? []),
            'last_updated' => now()->toISOString(),
        ]);

        return $data;
    }

    protected function afterSave(): void
    {
        // Auto-update status after saving
        $this->record->updateStatus();
    }
}
