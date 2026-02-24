<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $empresaId = Empresa::query()->value('id');

        if (! $empresaId) {
            return;
        }

        $permissions = [
            'dashboard.view',
            'plataformas.view',
            'plataformas.create',
            'plataformas.edit',
            'plataformas.delete',
            'clientes.view',
            'clientes.create',
            'clientes.edit',
            'clientes.delete',
            'cuentas.view',
            'cuentas.create',
            'cuentas.edit',
            'cuentas.delete',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.roles.manage',
        ];

        foreach ($permissions as $permissionName) {
            Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::query()->firstOrCreate([
            'name' => 'administrador',
            'guard_name' => 'web',
            'empresa_id' => $empresaId,
        ]);

        $managerRole = Role::query()->firstOrCreate([
            'name' => 'manager',
            'guard_name' => 'web',
            'empresa_id' => $empresaId,
        ]);

        $adminRole->syncPermissions($permissions);

        $managerRole->syncPermissions([
            'dashboard.view',
            'plataformas.view',
            'clientes.view',
            'clientes.create',
            'clientes.edit',
            'cuentas.view',
        ]);
    }
}
