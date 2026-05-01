<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view bookings',
            'create bookings',
            'edit bookings',
            'cancel bookings',
            'check-in guests',
            'check-out guests',
            'view inventory',
            'edit inventory',
            'manage room types',
            'manage rooms',
            'manage amenities',
            'manage gallery',
            'manage website info',
            'manage inquiries',
            'manage reviews',
            'manage users',
            'manage locations',
            'manage logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Staff — operational permissions only
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions([
            'view bookings',
            'create bookings',
            'edit bookings',
            'check-in guests',
            'check-out guests',
            'view inventory',
            'edit inventory',
            'manage inquiries',
        ]);

        // Admin — all except user and location management
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'view bookings',
            'create bookings',
            'edit bookings',
            'cancel bookings',
            'check-in guests',
            'check-out guests',
            'view inventory',
            'edit inventory',
            'manage room types',
            'manage rooms',
            'manage amenities',
            'manage gallery',
            'manage website info',
            'manage inquiries',
            'manage reviews',
            'manage locations',
        ]);

        // Super Admin — all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Assign super_admin role to existing admin user
        $user = \App\Models\User::where('email', 'admin@dwellcasa')->first();
        if ($user) {
            $user->assignRole('super_admin');
        }
    }
}