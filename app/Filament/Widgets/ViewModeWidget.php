<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ViewModeWidget extends Widget
{
    protected static string $view = 'filament.widgets.view-mode-widget';

    protected int | string | array $columnSpan = 'full';
}
