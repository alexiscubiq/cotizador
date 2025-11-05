<?php

namespace App\Filament\Resources\TnaResource\Pages;

use App\Filament\Resources\TnaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTna extends ViewRecord
{
    protected static string $resource = TnaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('update_status')
                ->label('Actualizar Estado')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function () {
                    $this->record->updateStatus();
                    \Filament\Notifications\Notification::make()
                        ->title('Estado actualizado')
                        ->success()
                        ->send();
                }),
            Actions\EditAction::make(),
        ];
    }
}
