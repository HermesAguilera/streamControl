<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class IndicadoresSectionWidget extends Widget
{
    protected static string $view = 'filament.widgets.section-title';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'title' => 'Indicadores',
        ];
    }
}
