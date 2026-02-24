<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $empresaId = Empresa::query()->value('id');

        if (! $empresaId) {
            return;
        }

        foreach (['administrador', 'manager'] as $roleName) {
            Role::query()->firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
                'empresa_id' => $empresaId,
            ]);
        }
    }
}
