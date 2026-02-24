<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Empresa;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $empresaId = auth()->user()?->empresa_id ?? Empresa::query()->value('id');

        $data['empresa_id'] = $empresaId;
        $data['persona_id'] = null;

        return $data;
    }
}
