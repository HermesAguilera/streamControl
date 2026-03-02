<?php

namespace App\Filament\Resources\PlataformaResource\Pages;

use App\Filament\Concerns\HasStandardCrudNotifications;
use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\PlataformaResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePlataforma extends CreateRecord
{
    use HasStandardCrudNotifications;
    use RedirectsToResourceIndex;

    protected static string $resource = PlataformaResource::class;
}
