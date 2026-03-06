<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdminUserResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdminUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuperAdminUsers extends ListRecords
{
    protected static string $resource = SuperAdminUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear usuario superadmin'),
        ];
    }
}
