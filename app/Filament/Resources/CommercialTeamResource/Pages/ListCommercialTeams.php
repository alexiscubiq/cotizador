<?php

namespace App\Filament\Resources\CommercialTeamResource\Pages;

use App\Filament\Resources\CommercialTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommercialTeams extends ListRecords
{
    protected static string $resource = CommercialTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
