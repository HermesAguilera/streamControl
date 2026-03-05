<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Empresa;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresa = Empresa::firstOrCreate([
            'nombre' => 'Empresa Default',
        ]);

        $role = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
            'empresa_id' => $empresa->id,
        ]);

        $user = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => 'AdminPass123!',
                'empresa_id' => $empresa->id,
            ]
        );

        // Assign role to user
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($role->name);
        }
    }
}
