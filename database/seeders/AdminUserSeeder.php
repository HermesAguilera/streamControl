<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure role exists
        Role::firstOrCreate(['name' => 'admin']);

        // Create or update admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('AdminPass123!')]
        );

        // Assign role
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('admin');
        }
    }
}
