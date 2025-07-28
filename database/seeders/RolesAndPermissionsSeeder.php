<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions (use firstOrCreate to avoid duplicates)
        $edit = Permission::firstOrCreate(['name' => 'edit']);
        $delete = Permission::firstOrCreate(['name' => 'delete']);
        $view = Permission::firstOrCreate(['name' => 'view']);

        // Create roles and assign created permissions (use firstOrCreate to avoid duplicates)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([$edit, $delete, $view]);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([$view]);

        // Assign role to a user (example user ID 1)
        $user = User::find(1);
        if ($user) {
            $user->assignRole('admin');
            $this->command->info("Admin role assigned to user: {$user->email}");
        } else {
            $this->command->warn("User with ID 1 not found. Please assign admin role manually.");
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
