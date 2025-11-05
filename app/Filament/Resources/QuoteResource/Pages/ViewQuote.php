<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQuote extends ViewRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        $isSupplierView = session('view_mode', 'wts') === 'supplier';

        return [
            Actions\EditAction::make()
                ->visible(fn () => !$isSupplierView),
        ];
    }

}
