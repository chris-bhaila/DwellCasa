<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $manageItems      = Permission::firstOrCreate(['name' => 'manage inventory items',      'guard_name' => 'web']);
        $manageCategories = Permission::firstOrCreate(['name' => 'manage inventory categories', 'guard_name' => 'web']);

        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo([$manageItems, $manageCategories]);
        }

        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo([$manageItems, $manageCategories]);
        }

        $staff = Role::where('name', 'staff')->first();
        if ($staff) {
            $staff->givePermissionTo($manageItems);
        }
    }

    public function down(): void
    {
        Permission::whereIn('name', ['manage inventory items', 'manage inventory categories'])->delete();
    }
};
