@extends('layouts.admin')

@section('title', 'User Management - DwellCasa Admin')

@section('content')
@php
$permissionGroups = [
'Bookings' => ['view bookings', 'create bookings', 'edit bookings', 'cancel bookings'],
'Operations' => ['check-in guests', 'check-out guests'],
'Inventory' => ['view inventory', 'edit inventory'],
'Content' => ['manage room types', 'manage rooms', 'manage amenities', 'manage gallery', 'manage website info'],
'Communication' => ['manage inquiries', 'manage reviews'],
'System' => ['manage users', 'manage locations', 'manage logs'],
];
$superAdminOnlyPerms = ['manage users', 'manage locations', 'manage logs'];
@endphp

<div x-data="{ activeTab: 'users' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-serif font-bold text-slate-900 italic">User Management</h1>
            <p class="text-slate-500 mt-1">Manage admin panel access, roles, and permissions.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <div x-show="activeTab === 'users'">
                <button onclick="openModal('add')" class="bg-primary text-white px-5 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm flex items-center gap-2">
                    <i class="bi bi-person-plus"></i> Add User
                </button>
            </div>
            @role('super_admin')
            <div x-show="activeTab === 'roles'" x-cloak>
                <button onclick="openRoleModal()" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl font-medium hover:bg-slate-700 transition-all shadow-sm flex items-center gap-2">
                    <i class="bi bi-shield-plus"></i> Add Role
                </button>
            </div>
            @endrole
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex space-x-6 mb-8 border-b border-slate-200">
        <button @click="activeTab = 'users'" :class="activeTab === 'users' ? 'border-primary text-primary border-b-2' : 'text-slate-500 hover:text-slate-700'" class="pb-3 font-medium px-2 transition-colors">Users</button>
        @role('super_admin')
        <button @click="activeTab = 'roles'" :class="activeTab === 'roles' ? 'border-primary text-primary border-b-2' : 'text-slate-500 hover:text-slate-700'" class="pb-3 font-medium px-2 transition-colors">Role Permissions</button>
        @endrole
    </div>

    <!-- Tab 1: Users List -->
    <div x-show="activeTab === 'users'" x-cloak class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                        <th class="p-4 font-medium">Name</th>
                        <th class="p-4 font-medium">Email</th>
                        <th class="p-4 font-medium">Location</th>
                        <th class="p-4 font-medium">Role</th>
                        <th class="p-4 font-medium">Direct Permissions</th>
                        <th class="p-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 font-bold text-slate-900">{{ $user->name }}</td>
                        <td class="p-4 text-slate-600">{{ $user->email }}</td>
                        <td class="p-4">
                            @if($user->location)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-100 text-emerald-700">
                                {{ $user->location->name }}
                            </span>
                            @else
                            <span class="text-slate-400 text-xs italic">No location</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @foreach($user->roles as $role)
                            @if($role->name === 'super_admin')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-800 text-white shadow-sm">Super Admin</span>
                            @elseif($role->name === 'admin')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-[#A89070] text-white shadow-sm">Admin</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-700 shadow-sm capitalize">{{ str_replace('_', ' ', $role->name) }}</span>
                            @endif
                            @endforeach
                        </td>
                        <td class="p-4 text-slate-600">
                            <span class="bg-slate-100 border border-slate-200 px-2 py-1 rounded-lg text-xs font-medium text-slate-700">
                                {{ $user->permissions->count() }} Overrides
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" class="w-8 h-8 flex items-center justify-center text-[#A89070] hover:bg-slate-50 hover:text-[#8E795E] rounded-md transition-colors"
                                    onclick="openModal('edit', {{ $user->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @if(auth()->id() !== $user->id)
                                <button type="button" class="w-8 h-8 flex items-center justify-center text-red-400 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors"
                                    onclick="deleteUser({{ $user->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab 2: Role Permissions (super_admin only) -->
    @role('super_admin')
    <div x-show="activeTab === 'roles'" x-cloak class="space-y-8">
        @foreach($roles as $role)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <form class="role-permissions-form" data-role-id="{{ $role->id }}" onsubmit="saveRolePermissions(event, {{ $role->id }})">
                <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold text-slate-900 capitalize">{{ str_replace('_', ' ', $role->name) }}</h3>
                        @if($role->name === 'super_admin')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-800 text-white shadow-sm">All Access</span>
                        @endif
                    </div>
                    @if($role->name !== 'super_admin')
                    <button type="submit" class="bg-primary text-white px-5 py-2 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
                        Save Changes
                    </button>
                    @endif
                </div>

                <div id="role-error-{{ $role->id }}" class="hidden mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($permissionGroups as $group => $perms)
                    <div>
                        <h4 class="text-xs font-bold text-slate-500 uppercase mb-3">{{ $group }}</h4>
                        <div class="space-y-2">
                            @foreach($perms as $perm)
                            <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer group">
                                <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                                    class="rounded border-slate-300 text-primary focus:ring-primary w-4 h-4 disabled:opacity-50"
                                    @if($role->name === 'super_admin') checked disabled
                                @elseif($role->permissions->contains('name', $perm)) checked
                                @endif>
                                <span class="group-hover:text-primary transition-colors @if($role->name === 'super_admin') opacity-50 @endif">
                                    {{ ucwords(str_replace('-', ' ', $perm)) }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </form>
        </div>
        @endforeach
    </div>
    @endrole

    <!-- Add / Edit Modal -->
    <div id="user-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 id="modal-title" class="text-xl font-serif font-bold text-slate-900 italic">Add User</h2>
                <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>

            <form id="user-form" class="flex flex-col min-h-0">
                <div class="overflow-y-auto p-6">
                    <div id="modal-error" class="hidden mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm"></div>

                    <input type="hidden" id="user_id" name="id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Role <span class="text-red-500">*</span></label>
                            <select id="role-select" name="role" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors bg-white">
                                <option value="" disabled selected>Select Role</option>
                                @foreach($roles->where('name', '!=', 'super_admin') as $role)
                                @if(auth()->user()->hasRole('super_admin') || $role->name !== 'admin')
                                <option value="{{ $role->name }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        @role('super_admin')
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Location <span class="text-red-500">*</span></label>
                            <select id="location-select" name="location_id"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors bg-white">
                                <option value="" disabled selected>Select Location</option>
                                @foreach(\App\Models\Location::where('is_active', true)->orderBy('name')->get() as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endrole
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Password <span class="text-red-500" id="password-req-star">*</span></label>
                            <input type="password" id="password" name="password" minlength="8" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            <p id="password-hint" class="text-xs text-slate-500 mt-1 hidden">Leave blank to keep current password.</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Direct Permission Overrides</h3>
                        <p class="text-xs text-slate-500 mb-6">Select any additional permissions this user should have on top of their role defaults.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($permissionGroups as $group => $perms)
                            <div>
                                <h4 class="text-xs font-bold text-slate-500 uppercase mb-3">{{ $group }}</h4>
                                <div class="space-y-2">
                                    @foreach($perms as $perm)
                                    @if(auth()->user()->hasRole('super_admin') || (auth()->user()->hasPermissionTo($perm) && !in_array($perm, $superAdminOnlyPerms)))
                                    <label class="flex items-center gap-2 text-sm text-slate-700 perm-wrapper cursor-pointer group">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm }}" class="user-perm-checkbox rounded border-slate-300 text-primary focus:ring-primary w-4 h-4">
                                        <span class="group-hover:text-primary transition-colors">{{ ucwords(str_replace('-', ' ', $perm)) }}</span>
                                    </label>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100 flex justify-end gap-3 bg-slate-50/50 shrink-0">
                    <button type="button" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-200 transition-colors" onclick="closeModal()">Cancel</button>
                    <button type="submit" id="submit-btn" class="w-full sm:w-auto bg-primary text-white px-6 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div id="role-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Add New Role</h2>
                <button type="button" onclick="closeRoleModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>

            <form id="new-role-form" class="flex flex-col min-h-0">
                <div class="overflow-y-auto p-6">
                    <div id="role-modal-error" class="hidden mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm"></div>

                    <div class="mb-8">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Role Name <span class="text-red-500">*</span></label>
                        <input type="text" id="new_role_name" name="name" required placeholder="e.g. Front Desk" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Role Permissions</h3>
                        <p class="text-xs text-slate-500 mb-6">Select the default permissions for this role.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($permissionGroups as $group => $perms)
                            <div>
                                <h4 class="text-xs font-bold text-slate-500 uppercase mb-3">{{ $group }}</h4>
                                <div class="space-y-2">
                                    @foreach($perms as $perm)
                                    @if(auth()->user()->hasRole('super_admin') || (auth()->user()->hasPermissionTo($perm) && !in_array($perm, $superAdminOnlyPerms)))
                                    <label class="flex items-center gap-2 text-sm text-slate-700 perm-wrapper cursor-pointer group">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm }}" class="user-perm-checkbox rounded border-slate-300 text-primary focus:ring-primary w-4 h-4">
                                        <span class="group-hover:text-primary transition-colors">{{ ucwords(str_replace('-', ' ', $perm)) }}</span>
                                    </label>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100 flex justify-end gap-3 bg-slate-50/50 shrink-0">
                    <button type="button" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-200 transition-colors" onclick="closeRoleModal()">Cancel</button>
                    <button type="submit" id="submit-role-btn" class="w-full sm:w-auto bg-primary text-white px-6 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">Save Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@php
$usersJson = $users->map(fn($u) => [
'id' => $u->id,
'name' => $u->name,
'email' => $u->email,
'role' => $u->roles->first()?->name,
'location_id' => $u->location_id,
'permissions' => $u->permissions->pluck('name')->toArray(),
]);
$rolesJson = $roles->map(fn($r) => [
'id' => $r->id,
'name' => $r->name,
'permissions' => $r->permissions->pluck('name')->toArray(),
]);
@endphp
@php $allPermissionNames = $permissions -> pluck('name');
@endphp
@push('scripts')
<script>
    const users = @json($usersJson);
    const rolesData = @json($rolesJson);

    const allPermissions = @json($allPermissionNames);

    window.updatePermissionCheckboxes = function(roleName, userDirectPerms = []) {
        const role = rolesData.find(r => r.name === roleName);
        const rolePerms = role ? role.permissions : [];
        const isSuperAdmin = roleName === 'super_admin';

        document.querySelectorAll('.user-perm-checkbox').forEach(cb => {
            const isRolePerm = isSuperAdmin || rolePerms.includes(cb.value);
            const isDirectPerm = userDirectPerms.includes(cb.value);

            cb.checked = isRolePerm || isDirectPerm;
            cb.disabled = isRolePerm; // Inherited permissions are disabled so they aren't submitted as redundant direct overrides

            const span = cb.nextElementSibling;
            if (isRolePerm) {
                span.classList.add('opacity-50');
                span.title = 'Inherited from role';
            } else {
                span.classList.remove('opacity-50');
                span.title = '';
            }
        });
    };

    document.getElementById('role-select').addEventListener('change', function() {
        const currentUserId = document.getElementById('user_id').value;
        let userDirectPerms = [];
        if (currentUserId) {
            const user = users.find(u => u.id == currentUserId);
            if (user) userDirectPerms = user.permissions;
        }
        updatePermissionCheckboxes(this.value, userDirectPerms);
    });

    window.openModal = function(mode, userId = null) {
        const modal = document.getElementById('user-modal');
        const form = document.getElementById('user-form');
        const errorDiv = document.getElementById('modal-error');

        form.reset();
        errorDiv.classList.add('hidden');
        errorDiv.innerText = '';

        if (mode === 'add') {
            document.getElementById('modal-title').innerText = 'Add User';
            document.getElementById('user_id').value = '';
            document.getElementById('password').required = true;
            document.getElementById('password-req-star').style.display = 'inline';
            document.getElementById('password-hint').classList.add('hidden');
            document.getElementById('role-select').disabled = false;

            updatePermissionCheckboxes('');
        } else {
            document.getElementById('modal-title').innerText = 'Edit User';
            document.getElementById('user_id').value = userId;
            document.getElementById('password').required = false;
            document.getElementById('password-req-star').style.display = 'none';
            document.getElementById('password-hint').classList.remove('hidden');

            const user = users.find(u => u.id === userId);
            if (user) {
                document.getElementById('name').value = user.name;
                document.getElementById('email').value = user.email;

                // Populate location if the select exists (super_admin only)
                const locationSelect = document.getElementById('location-select');
                if (locationSelect && user.location_id) {
                    locationSelect.value = String(user.location_id);
                }
                if (user.role === 'super_admin') {
                    let opt = document.createElement('option');
                    opt.value = 'super_admin';
                    opt.text = 'Super Admin';
                    opt.id = 'temp-super-admin-opt';
                    document.getElementById('role-select').appendChild(opt);
                    document.getElementById('role-select').value = 'super_admin';
                    document.getElementById('role-select').disabled = true;
                } else {
                    document.getElementById('role-select').value = user.role;
                    document.getElementById('role-select').disabled = false;
                }

                updatePermissionCheckboxes(user.role, user.permissions);
            }
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    };

    window.closeModal = function() {
        const modal = document.getElementById('user-modal');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            const saOpt = document.getElementById('temp-super-admin-opt');
            if (saOpt) saOpt.remove();
        }, 300);
    };

    document.getElementById('user-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submit-btn');
        const errorDiv = document.getElementById('modal-error');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-2"></i>Saving...';
        submitBtn.disabled = true;
        errorDiv.classList.add('hidden');

        const id = document.getElementById('user_id').value;
        const isUpdate = !!id;

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        if (document.getElementById('role-select').disabled) {
            data.role = document.getElementById('role-select').value;
        }

        data.permissions = formData.getAll('permissions[]');

        const url = isUpdate ? `/api/users/${id}` : `/api/users`;
        const method = isUpdate ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                window.location.reload();
            } else {
                let errorMsg = result.message || 'Unknown error';
                if (result.errors) {
                    errorMsg += '\n' + Object.values(result.errors).flat().join('\n');
                }
                errorDiv.innerText = errorMsg;
                errorDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            errorDiv.innerText = 'An error occurred while saving the user.';
            errorDiv.classList.remove('hidden');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });

    window.deleteUser = async function(id) {
        if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;

        try {
            const response = await fetch(`/api/users/${id}`, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const error = await response.json();
                alert('Error deleting user: ' + (error.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while deleting the user.');
        }
    };

    window.saveRolePermissions = async function(e, roleId) {
        e.preventDefault();

        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const errorDiv = document.getElementById(`role-error-${roleId}`);

        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-2"></i>Saving...';
        submitBtn.disabled = true;
        errorDiv.classList.add('hidden');

        const formData = new FormData(form);
        const permissions = formData.getAll('permissions[]');

        try {
            const response = await fetch(`/api/roles/${roleId}/permissions`, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    permissions
                })
            });

            const result = await response.json();

            if (response.ok) {
                window.location.reload();
            } else {
                errorDiv.innerText = result.message || 'Unknown error occurred.';
                errorDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            errorDiv.innerText = 'An error occurred while updating role permissions.';
            errorDiv.classList.remove('hidden');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    };

    window.openRoleModal = function() {
        const modal = document.getElementById('role-modal');
        const form = document.getElementById('new-role-form');
        const errorDiv = document.getElementById('role-modal-error');

        form.reset();
        errorDiv.classList.add('hidden');
        errorDiv.innerText = '';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    };

    window.closeRoleModal = function() {
        const modal = document.getElementById('role-modal');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    };

    document.getElementById('new-role-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submit-role-btn');
        const errorDiv = document.getElementById('role-modal-error');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-2"></i>Saving...';
        submitBtn.disabled = true;
        errorDiv.classList.add('hidden');

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        console.log('Submitting:', JSON.stringify(data));
        data.permissions = formData.getAll('permissions[]');

        try {
            const response = await fetch(`/api/roles`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                window.location.reload();
            } else {
                let errorMsg = result.message || 'Unknown error';
                if (result.errors) {
                    errorMsg += '\n' + Object.values(result.errors).flat().join('\n');
                }
                errorDiv.innerText = errorMsg;
                errorDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            errorDiv.innerText = 'An error occurred while creating the role.';
            errorDiv.classList.remove('hidden');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
</script>
@endpush