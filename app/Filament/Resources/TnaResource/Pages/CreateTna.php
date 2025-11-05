<?php

namespace App\Filament\Resources\TnaResource\Pages;

use App\Filament\Resources\TnaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTna extends CreateRecord
{
    protected static string $resource = TnaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['metadata'] = [
            'total_milestones' => count($data['milestones'] ?? []),
            'created_via' => 'manual',
        ];

        return $data;
    }
}
