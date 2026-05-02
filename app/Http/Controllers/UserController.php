<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends AdminController
{
    private const SUPER_ADMIN_ONLY_PERMISSIONS = ['manage users', 'manage locations', 'manage logs'];

    private function resolveLocationId(): ?int
    {
        $user = auth()->user();
        return $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
    }

    public function index()
    {
        $user = auth()->user();

        $query = User::with(['roles', 'permissions']);

        // Super admin sees all users (or scoped to selected location)
        // Admin/staff only see users in their location
        if ($user->hasRole('super_admin')) {
            if (session('selected_location_id')) {
                $query->where('location_id', session('selected_location_id'));
            }
        } else {
            $query->where('location_id', $user->location_id);
        }

        return response()->json([
            'data'    => $query->get(),
            'message' => 'Users fetched successfully'
        ], 200);
    }

    public function store(Request $request)
    {
        $locationId = $this->resolveLocationId();
        abort_if(!$locationId, 422, 'No location selected.');

        $isSuperAdmin = auth()->user()->hasRole('super_admin');

        // Super admin can assign any role except super_admin via this form
        // Admin can only assign roles that are not super_admin or admin
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8',
            'role'          => 'required|exists:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Enforce role assignment rules
        $requestedRole = $request->role;
        if ($requestedRole === 'super_admin') {
            return response()->json(['message' => 'Cannot assign super_admin role.'], 403);
        }
        if (!$isSuperAdmin && $requestedRole === 'admin') {
            return response()->json(['message' => 'Admins cannot assign the admin role.'], 403);
        }

        if (!$isSuperAdmin) {
            $restricted = array_intersect($request->input('permissions', []), self::SUPER_ADMIN_ONLY_PERMISSIONS);
            if (!empty($restricted)) {
                return response()->json(['message' => 'You cannot assign system-level permissions.'], 403);
            }
        }

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'location_id' => $locationId,
        ]);

        $user->assignRole($requestedRole);
        $user->syncPermissions($request->permissions ?? []);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['location_id' => $locationId])
            ->log('Created user ' . $user->name . ' (' . $user->email . ') with role ' . $requestedRole);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data'    => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $authUser = auth()->user();
        // Admin can only edit users in their own location
        if (!$authUser->hasRole('super_admin') && $user->location_id !== $authUser->location_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Prevent admin from editing their own account
        if (!$authUser->hasRole('super_admin') && $user->id === $authUser->id) {
            return response()->json(['message' => 'You cannot edit your own account.'], 403);
        }

        // Prevent admin from assigning permissions they don't have or system-level permissions
        if (!$authUser->hasRole('super_admin')) {
            $requestedPermissions = $request->input('permissions', []);

            $restricted = array_intersect($requestedPermissions, self::SUPER_ADMIN_ONLY_PERMISSIONS);
            if (!empty($restricted)) {
                return response()->json(['message' => 'You cannot assign system-level permissions.'], 403);
            }

            $allowedPermissions = $authUser->getAllPermissions()->pluck('name')->toArray();
            $unauthorized = array_diff($requestedPermissions, $allowedPermissions);
            if (!empty($unauthorized)) {
                return response()->json(['message' => 'You cannot assign permissions you do not have.'], 403);
            }
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password'      => 'nullable|string|min:8',
            'role'          => 'required|exists:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $requestedRole = $request->role;

        // Prevent role escalation
        if ($requestedRole === 'super_admin' && !$user->hasRole('super_admin')) {
            return response()->json(['message' => 'Cannot assign super_admin role.'], 403);
        }
        if (!$authUser->hasRole('super_admin') && $requestedRole === 'admin') {
            return response()->json(['message' => 'Admins cannot assign the admin role.'], 403);
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        // location_id never changes on update
        // Only super_admin can reassign locations
        if ($authUser->hasRole('super_admin') && $request->has('location_id')) {
            $user->location_id = $request->location_id ?: null;
        }
        $user->save();
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['location_id' => $user->location_id])
            ->log('Updated user ' . $user->name . ' (' . $user->email . ')');

        // Don't let anyone change a super_admin's role
        if (!$user->hasRole('super_admin')) {
            $user->syncRoles([$requestedRole]);
            $user->syncPermissions($request->permissions ?? []);
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data'    => $user
        ], 200);
    }

    public function destroy($id)
    {
        if (auth()->id() == $id) {
            return response()->json(['message' => 'Cannot delete yourself.'], 403);
        }

        $authUser = auth()->user();
        $user = User::findOrFail($id);

        // Admin can only delete users in their location
        if (!$authUser->hasRole('super_admin') && $user->location_id !== $authUser->location_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Nobody can delete a super_admin
        if ($user->hasRole('super_admin')) {
            return response()->json(['message' => 'Cannot delete a super admin.'], 403);
        }
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $user->location_id])
            ->log('Deleted user ' . $user->name . ' (' . $user->email . ')');
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }

    public function storeRole(Request $request)
    {
        // Only super_admin can create roles
        abort_unless(auth()->user()->hasRole('super_admin'), 403, 'Only super admin can create roles.');

        $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $roleName = strtolower(str_replace(' ', '_', $request->name));
        $role = Role::create(['name' => $roleName]);
        $role->syncPermissions($request->permissions ?? []);
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => null])
            ->log('Created role ' . $role->name);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data'    => $role
        ], 201);
    }

    public function updateRolePermissions(Request $request, $id)
    {
        // Only super_admin can modify role permissions
        abort_unless(auth()->user()->hasRole('super_admin'), 403, 'Only super admin can modify role permissions.');

        $request->validate(['permissions' => 'array']);

        $role = Role::findOrFail($id);

        if ($role->name === 'super_admin') {
            return response()->json(['message' => 'Cannot modify super_admin permissions.'], 403);
        }

        $role->syncPermissions($request->permissions ?? []);
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => null])
            ->log('Updated permissions for role ' . $role->name);

        return response()->json([
            'success' => true,
            'message' => 'Role permissions updated successfully'
        ]);
    }

    public function page()
    {
        $authUser     = auth()->user();
        $isSuperAdmin = $authUser->hasRole('super_admin');
        $locationId   = $isSuperAdmin
            ? session('selected_location_id')
            : $authUser->location_id;

        $users = User::with(['roles', 'permissions', 'location'])
            ->when($locationId, fn ($q) => $q->where('location_id', $locationId))
            ->whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'admin']))
            ->where('id', '!=', $authUser->id)
            ->orderBy('name')
            ->get();

        $roles = Role::with('permissions')
            ->when(!$isSuperAdmin, fn ($q) => $q->whereNotIn('name', ['super_admin', 'admin']))
            ->get();

        $permissions = \Spatie\Permission\Models\Permission::all();

        return view('admin.users', compact('users', 'roles', 'permissions'));
    }
}
