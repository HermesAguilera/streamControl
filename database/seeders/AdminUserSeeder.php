<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Empresa;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

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

        $role = Role::firstOrNew([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $role->empresa_id = $empresa->id;
        $role->save();

        $user = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('AdminPass123!'),
                'empresa_id' => $empresa->id,
            ]
        );

        // Assign role to user
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($role->name);
        }
    }
}
