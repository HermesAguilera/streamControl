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
        // Ensure a default Empresa exists and use its id
        $empresa = Empresa::firstOrCreate(
            ['nombre' => 'Default Empresa'],
            ['descripcion' => 'Empresa creada por el seeder']
        );

        // Ensure role exists and is associated to the empresa
        $role = Role::where('name', 'admin')->first();

        if ($role) {
            if (empty($role->empresa_id)) {
                $role->empresa_id = $empresa->id;
                $role->save();
            }
        } else {
            // Create role with empresa_id
            $role = Role::create([
                'name' => 'admin',
                'guard_name' => 'web',
                'empresa_id' => $empresa->id,
            ]);
        }

        // Create or update admin user and set empresa_id (plain password will be hashed by model cast)
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'AdminPass123!', 'empresa_id' => $empresa->id]
        );

        // Assign role to user
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('admin');
        }
    }
}
