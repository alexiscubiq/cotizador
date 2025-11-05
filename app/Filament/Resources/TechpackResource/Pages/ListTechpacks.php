<?php

namespace App\Filament\Resources\TechpackResource\Pages;

use App\Filament\Resources\TechpackResource;
use App\Models\Techpack;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class ListTechpacks extends ListRecords
{
    protected static string $resource = TechpackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Removido - el botón del header global maneja esto
        ];
    }

    public function getTabs(): array
    {
        return [
            'Todos' => Tab::make()
                ->badge(Techpack::count()),
            'En Revisión' => Tab::make()
                ->badge(Techpack::where('status', 'pending')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            'Aprobados' => Tab::make()
                ->badge(Techpack::where('status', 'approved')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved')),
        ];
    }
}
