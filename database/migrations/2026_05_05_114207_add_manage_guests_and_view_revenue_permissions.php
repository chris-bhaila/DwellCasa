<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $manageGuests = Permission::firstOrCreate(['name' => 'manage guests', 'guard_name' => 'web']);
        $viewRevenue  = Permission::firstOrCreate(['name' => 'view revenue',  'guard_name' => 'web']);

        $staff = Role::where('name', 'staff')->first();
        if ($staff) {
            $staff->givePermissionTo($manageGuests);
        }

        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo([$manageGuests, $viewRevenue]);
        }

        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo([$manageGuests, $viewRevenue]);
        }
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::where('name', 'manage guests')->delete();
        Permission::where('name', 'view revenue')->delete();
    }
};
