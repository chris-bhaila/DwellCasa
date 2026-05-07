@extends('layouts.admin')

@section('title', 'Locations Management - DwellCasa Admin')
@section('header_title', 'Locations')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Locations Management</h1>
        <p class="text-slate-500 mt-1">Manage physical property locations, addresses, and details.</p>
    </div>
    <div>
        <button onclick="openModal()" title="Add New Location" class="bg-primary text-white px-5 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm flex items-center gap-2">
            <i class="bi bi-geo-alt"></i> Add Location
        </button>
    </div>
</div>

<!-- List Section -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                    <th class="p-4 font-medium w-56">Location Details</th>
                    <th class="p-4 font-medium">Contact & Address</th>
                    <th class="p-4 font-medium w-24">Status</th>
                    <th class="p-4 font-medium w-24 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100" id="locations-list">
                @forelse($locations ?? [] as $location)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-4 w-56">
                        <div class="flex items-center gap-4">
                            @if($location->hero_image)
                                <img src="{{ asset('storage/' . $location->hero_image) }}" alt="{{ $location->name }}" class="w-16 h-12 flex-shrink-0 rounded-lg object-cover border border-slate-100">
                            @else
                                <div class="w-16 h-12 flex-shrink-0 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                                    <i class="bi bi-image text-xl"></i>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 truncate">{{ $location->name }}</div>
                                <div class="text-sm text-slate-500 mt-0.5 truncate">/{{ $location->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="font-medium text-slate-700 truncate max-w-xs">{{ $location->address ?: 'No address provided' }}</div>
                        <div class="text-sm text-slate-500 mt-1 flex flex-col gap-0.5">
                            @if($location->email) <span class="truncate max-w-xs"><i class="bi bi-envelope mr-1"></i>{{ $location->email }}</span> @endif
                            @if($location->phone) <span><i class="bi bi-telephone mr-1"></i>{{ $location->phone }}</span> @endif
                        </div>
                    </td>
                    <td class="p-4">
                        @if($location->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-green-50 text-green-700 border border-green-200">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-slate-50 text-slate-700 border border-slate-200">Inactive</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" class="edit-btn w-8 h-8 flex items-center justify-center text-[#A89070] hover:bg-slate-50 hover:text-[#8E795E] rounded-md transition-colors"
                                onclick="editLocation({{ $location->id }})" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            
                            @role('super_admin')
                            <button type="button" class="delete-btn w-8 h-8 flex items-center justify-center text-red-400 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors"
                                onclick="deleteLocation({{ $location->id }})" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endrole
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-500">
                        No locations found. Use the form to add a new location.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="location-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 id="form-title" class="text-xl font-serif font-bold text-slate-900 italic">Add New Location</h2>
            <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="location-form" class="flex flex-col min-h-0" enctype="multipart/form-data">
            <div class="overflow-y-auto p-6 space-y-5">
                @csrf
                <input type="hidden" id="location_id" name="id" value="">
                <div id="modal-error" class="hidden mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required class="w-full rounded-xl border-slate-200 px-4 py-2.5 border focus:ring-primary focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label for="slug" class="block text-sm font-medium text-slate-700 mb-1">Slug <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" id="slug" required class="w-full rounded-xl border-slate-200 px-4 py-2.5 border focus:ring-primary focus:border-primary transition-colors" placeholder="e.g., thamel">
                    </div>
                </div>

                <div>
                    <label for="hero_image" class="block text-sm font-medium text-slate-700 mb-1">Hero Image</label>
                    <div id="image-preview-container" class="hidden mb-3">
                        <img id="image-preview" src="" alt="Preview" class="h-32 rounded-xl object-cover border border-slate-200">
                    </div>
                    <input type="file" name="hero_image" id="hero_image" accept="image/*" class="w-full rounded-xl border-slate-200 px-4 py-2 border focus:ring-primary focus:border-primary transition-colors bg-white">
                    <p class="text-sm text-slate-500 mt-1">Recommended size: 1920x1080px. Max size: 5MB.</p>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                    <input type="text" name="address" id="address" class="w-full rounded-xl border-slate-200 px-4 py-2.5 border focus:ring-primary focus:border-primary transition-colors">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="w-full rounded-xl border-slate-200 px-4 py-2.5 border focus:ring-primary focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" class="w-full rounded-xl border-slate-200 px-4 py-2.5 border focus:ring-primary focus:border-primary transition-colors">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-primary focus:border-primary transition-colors"></textarea>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300" checked>
                    <label for="is_active" class="ml-3 text-sm font-medium text-slate-700">Active (Visible on website)</label>
                </div>
            </div>
            
            <div class="p-6 border-t border-slate-100 flex justify-end gap-3 bg-slate-50/50 shrink-0">
                <button type="button" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-200 transition-colors" onclick="closeModal()">Cancel</button>
                <button type="submit" id="submit-btn" class="w-full sm:w-auto bg-primary text-white px-6 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">Save Location</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const locationsData = @json($locations ?? []);

    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        if (!document.getElementById('location_id').value) {
            document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        }
    });

    window.openModal = function() {
        const modal = document.getElementById('location-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    };

    window.closeModal = function() {
        const modal = document.getElementById('location-modal');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            resetForm();
        }, 300);
    };

    window.resetForm = function() {
        document.getElementById('location-form').reset();
        document.getElementById('location_id').value = '';
        document.getElementById('form-title').innerText = 'Add New Location';
        document.getElementById('submit-btn').innerText = 'Save Location';
        document.getElementById('image-preview-container').classList.add('hidden');
        document.getElementById('modal-error').classList.add('hidden');
    };

    window.editLocation = function(id) {
        const location = locationsData.find(loc => loc.id === id);
        if (!location) return;

        document.getElementById('location_id').value = location.id;
        document.getElementById('name').value = location.name;
        document.getElementById('slug').value = location.slug;
        document.getElementById('address').value = location.address || '';
        document.getElementById('phone').value = location.phone || '';
        document.getElementById('email').value = location.email || '';
        document.getElementById('description').value = location.description || '';
        document.getElementById('is_active').checked = location.is_active;

        if (location.hero_image) {
            document.getElementById('image-preview').src = '{{ asset("storage") }}/' + location.hero_image;
            document.getElementById('image-preview-container').classList.remove('hidden');
        } else {
            document.getElementById('image-preview-container').classList.add('hidden');
        }

        document.getElementById('form-title').innerText = 'Edit Location';
        document.getElementById('submit-btn').innerText = 'Update Location';

        openModal();
    };

    document.getElementById('location-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const id = document.getElementById('location_id').value;
        const errorDiv = document.getElementById('modal-error');
        errorDiv.classList.add('hidden');

        // Handle checkboxes in FormData explicitly if unchecked
        if (!formData.has('is_active')) {
            formData.append('is_active', 0);
        }

        const url = id ? `/api/locations/${id}` : '/api/locations';
        if (id) {
            formData.append('_method', 'PUT');
        }

        const submitBtn = document.getElementById('submit-btn');
        const originalText = submitBtn.innerText;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-2"></i>Saving...';
        submitBtn.disabled = true;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const error = await response.json();
                let errorMsg = error.message || 'Unknown error occurred.';
                if (error.errors) {
                    errorMsg += '\n' + Object.values(error.errors).flat().join('\n');
                }
                errorDiv.innerText = errorMsg;
                errorDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            errorDiv.innerText = 'An error occurred while saving. Please try again.';
            errorDiv.classList.remove('hidden');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });

    window.deleteLocation = async function(id) {
        if (!await adminConfirm('Are you sure you want to delete this location? This action cannot be undone.', { confirmLabel: 'Delete', type: 'danger' })) return;
        
        try {
            const response = await fetch(`/api/locations/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const error = await response.json();
                adminToast('Error deleting location: ' + (error.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            adminToast('An error occurred while attempting to delete.');
        }
    };
</script>
@endpush
