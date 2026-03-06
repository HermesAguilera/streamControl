<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class PlataformasSectionWidget extends Widget
{
    protected static string $view = 'filament.widgets.section-title';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'title' => 'Plataformas',
        ];
    }
}
