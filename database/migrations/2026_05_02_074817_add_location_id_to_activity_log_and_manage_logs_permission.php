<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Add dedicated location_id column to activity_log for reliable filtering
        Schema::table('activity_log', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable()->index()->after('properties');
        });

        // Add manage logs permission if it does not already exist
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = Permission::firstOrCreate(['name' => 'manage logs', 'guard_name' => 'web']);

        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin && !$superAdmin->hasPermissionTo('manage logs')) {
            $superAdmin->givePermissionTo($permission);
        }
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });

        Permission::where('name', 'manage logs')->delete();
    }
};
