@extends('layouts.admin')

@section('title', 'My Profile - DwellCasa Admin')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic">My Profile</h1>
        <p class="text-slate-500 mt-1">Manage your account settings and password.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: User Info Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 flex flex-col items-center border-b border-slate-100">
                <div class="w-24 h-24 bg-[#A89070]/10 rounded-full flex items-center justify-center text-[#A89070] text-3xl font-bold mb-4">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <h2 class="text-xl font-bold text-slate-900">{{ auth()->user()->name }}</h2>
                <p class="text-slate-500 text-sm mb-3">{{ auth()->user()->email }}</p>
                <div class="flex flex-wrap gap-2 justify-center">
                    @foreach(auth()->user()->roles as $role)
                        @if($role->name === 'super_admin')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-800 text-white shadow-sm">Super Admin</span>
                        @elseif($role->name === 'admin')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-[#A89070] text-white shadow-sm">Admin</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-700 shadow-sm capitalize">{{ $role->name }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="p-6 bg-slate-50/50">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-medium text-slate-500">Member Since</span>
                    <span class="text-sm font-medium text-slate-900">{{ auth()->user()->created_at ? auth()->user()->created_at->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-slate-500">Direct Permissions</span>
                    <span class="bg-slate-200 border border-slate-300 px-2 py-1 rounded-md text-xs font-medium text-slate-700">
                        {{ auth()->user()->permissions->count() }} Overrides
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Edit Forms -->
    <div class="lg:col-span-2 space-y-8">
        @hasanyrole('super_admin|admin')
        <!-- Update Profile Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Profile Information</h2>
            
            <form id="profile-form" method="POST" action="{{ route('admin.profile.update') }}">
                @csrf
                @method('PUT')
                
                @if(session('status') === 'profile-updated')
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center">
                        <i class="bi bi-check-circle-fill mr-2 text-lg"></i> Profile updated successfully.
                    </div>
                @endif

                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-[#A89070] focus:border-[#A89070] transition-colors @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-[#A89070] focus:border-[#A89070] transition-colors @error('email') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-[#A89070] text-white px-8 py-3 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm flex items-center gap-2">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
        @endhasanyrole

        <!-- Update Password Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Update Password</h2>
            
            <form id="password-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                @if(session('status') === 'password-updated')
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center">
                        <i class="bi bi-check-circle-fill mr-2 text-lg"></i> Password updated successfully.
                    </div>
                @endif

                <div class="space-y-5">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-slate-700 mb-2">Current Password</label>
                        <input type="password" name="current_password" id="current_password" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-[#A89070] focus:border-[#A89070] transition-colors @error('current_password', 'updatePassword') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('current_password', 'updatePassword')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">New Password</label>
                        <input type="password" name="password" id="password" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-[#A89070] focus:border-[#A89070] transition-colors @error('password', 'updatePassword') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('password', 'updatePassword')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-[#A89070] focus:border-[#A89070] transition-colors @error('password_confirmation', 'updatePassword') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('password_confirmation', 'updatePassword')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-slate-800 text-white px-8 py-3 rounded-xl font-medium hover:bg-slate-700 transition-all shadow-sm flex items-center gap-2">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
