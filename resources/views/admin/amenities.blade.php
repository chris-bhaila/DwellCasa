@extends('layouts.admin')

@section('title', 'Amenities - DwellCasa Admin')

@section('content')
{{-- Temporary Mock Data for Previewing --}}
@php
if (!isset($amenities)) {
$amenities = collect([
(object)['id' => 1, 'name' => 'High-Speed WiFi', 'category' => 'utilities', 'icon' => '📶', 'is_active' => 1],
(object)['id' => 2, 'name' => 'Smart TV', 'category' => 'entertainment', 'icon' => '📺', 'is_active' => 1],
(object)['id' => 3, 'name' => 'Mini Bar', 'category' => 'kitchen', 'icon' => '🥂', 'is_active' => 0],
(object)['id' => 4, 'name' => 'Rain Shower', 'category' => 'bathroom', 'icon' => '🚿', 'is_active' => 1],
]);
}
@endphp

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic">Amenities Management</h1>
        <p class="text-slate-500 mt-1">Create and manage amenities available for your properties and rooms.</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <!-- Form Section -->
    <div class="xl:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 id="form-title" class="text-xl font-serif font-bold text-slate-900 italic">Add New Amenity</h2>
            </div>
            <form id="add-amenity-form" action="#" method="POST" class="p-6">
                @csrf
                <input type="hidden" id="amenity_id" name="id" value="">

                <div class="grid grid-cols-1 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Amenity Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. High-Speed WiFi" required>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-700 mb-2">Category</label>
                        <input type="text" name="category" id="category" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. Hygiene">

                    </div>

                    <!-- Icon -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Select Icon <span class="text-red-500">*</span></label>
                        <input type="hidden" name="icon" id="icon" required>

                        <div class="grid grid-cols-6 sm:grid-cols-8 gap-2 h-[250px] overflow-y-auto p-3 border border-slate-200 rounded-xl bg-slate-50 mb-2">
                            @php
                            $biIcons = [
                            // Connectivity & tech
                            'bi-wifi', 'bi-router', 'bi-pc-display', 'bi-tv', 'bi-projector',
                            'bi-speaker', 'bi-telephone', 'bi-phone', 'bi-modem',
                            'bi-plug', 'bi-power', 'bi-usb-plug', 'bi-battery-full',

                            // Climate & comfort
                            'bi-snow', 'bi-fan', 'bi-wind', 'bi-thermometer-half',
                            'bi-thermometer-snow', 'bi-thermometer-sun', 'bi-moisture', 'bi-brightness-high',

                            // Room & furniture
                            'bi-door-open', 'bi-door-closed', 'bi-window', 'bi-lamp',
                            'bi-lightbulb', 'bi-archive', 'bi-lock', 'bi-lock-fill',

                            // Safety & security
                            'bi-safe', 'bi-shield-check', 'bi-key',
                            'bi-person-badge', 'bi-bell', 'bi-alarm',

                            // Bathroom & personal care
                            'bi-droplet', 'bi-brush', 'bi-scissors',

                            // Food & drink (BI)
                            'bi-cup-hot', 'bi-cup-straw', 'bi-cup', 'bi-egg-fried',
                            'bi-basket', 'bi-water',

                            // Transport
                            'bi-car-front', 'bi-bicycle', 'bi-bus-front',
                            'bi-ev-station', 'bi-airplane', 'bi-p-circle',

                            // Nature & outdoor
                            'bi-tree', 'bi-flower1', 'bi-cloud-sun', 'bi-umbrella',
                            'bi-tsunami', 'bi-fire',

                            // Leisure & work
                            'bi-book', 'bi-music-note', 'bi-mic', 'bi-camera',
                            'bi-controller', 'bi-briefcase', 'bi-person-workspace',
                            'bi-newspaper', 'bi-pen', 'bi-printer',

                            // Misc
                            'bi-clock', 'bi-stars', 'bi-heart-pulse', 'bi-shop',
                            'bi-building', 'bi-house-door', 'bi-tools', 'bi-recycle',
                            'bi-bag-check', 'bi-lightning-charge', 'bi-bandaid',
                            ];

                            $lucideIcons = [
                            // Dining & meals
                            'utensils', 'utensils-crossed', 'chef-hat', 'soup', 'sandwich',
                            'salad', 'pizza', 'egg', 'fish', 'beef', 'ham', 'drumstick',
                            'cookie', 'cake-slice', 'croissant', 'popcorn', 'nut',

                            // Drinks
                            'coffee', 'wine', 'wine-off', 'beer', 'milk', 'martini',
                            'glass-water', 'cup-soda', 'flask-conical',

                            // Fruits & vegetables
                            'carrot', 'apple', 'grape', 'banana', 'cherry', 'wheat', 'citrus',

                            // Restaurant & service
                            'receipt', 'ticket', 'store', 'shopping-bag', 'package',
                            'bell-ring', 'concierge-bell', 'hand-platter',

                            // Kitchen & cooking
                            'flame', 'microwave', 'refrigerator', 'cooking-pot', 'scale', 'timer',

                            // Room & furniture
                            'armchair', 'sofa', 'bed', 'bed-single', 'bed-double', 'lamp-ceiling',
                            'lamp-desk', 'lamp-floor', 'blinds',

                            // Bathroom & personal care
                            'bath', 'shower-head', 'toilet', 

                            // Clothing & laundry
                            'shirt', 'footprints',

                            // Safety & room access
                            'vault', 'shield', 'key-round', 'lock',
                            'door-open', 'door-closed',

                            // Luggage & travel
                            'luggage', 'package-2',

                            // Leisure
                            'book-open', 'pen-line', 'flower-2', 'leaf', 'sprout',

                            // Health
                            'pill', 'heart-pulse',

                            // Misc
                            'alarm-clock', 'calendar-check', 'air-vent',
                            'plug-zap', 'thermometer', 'clock-3',
                            ];
                            @endphp

                            @foreach($biIcons as $iconClass)
                            @php
                            $iconName = ucwords(str_replace('-', ' ', substr($iconClass, 3)));
                            @endphp
                            <button type="button" title="{{ $iconName }}"
                                class="icon-select-btn flex items-center justify-center w-10 h-10 rounded-lg border-2 border-transparent hover:bg-white hover:border-primary hover:text-primary transition-all text-slate-500 bg-slate-100"
                                data-icon='<i class="bi {{ $iconClass }}"></i>'>
                                <i class="bi {{ $iconClass }} text-lg pointer-events-none"></i>
                            </button>
                            @endforeach

                            @foreach($lucideIcons as $iconName)
                            @php
                            $label = ucwords(str_replace('-', ' ', $iconName));
                            @endphp
                            <button type="button" title="{{ $label }}"
                                class="icon-select-btn flex items-center justify-center w-10 h-10 rounded-lg border-2 border-transparent hover:bg-white hover:border-primary hover:text-primary transition-all text-slate-500 bg-slate-100"
                                data-icon='<i data-lucide="{{ $iconName }}"></i>'>
                                <i data-lucide="{{ $iconName }}" class="pointer-events-none" style="width:18px;height:18px;"></i>
                            </button>
                            @endforeach
                        </div>
                        <p class="text-xs text-slate-500 flex items-center gap-2">Selected: <span id="selected-icon-preview" class="w-8 h-8 flex items-center justify-center bg-slate-100 rounded border border-slate-200 text-slate-400 text-lg">?</span></p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="Brief description..."></textarea>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-slate-700 mb-2">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" value="0" min="0">
                    </div>

                    <!-- Is Active -->
                    <div class="flex items-center mt-2">
                        <input type="checkbox" name="is_active" id="is_active" class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300" checked>
                        <label for="is_active" class="ml-3 text-sm font-medium text-slate-700">Active (Visible)</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" id="cancel-btn" class="hidden px-6 py-3 rounded-xl font-medium text-slate-600 hover:bg-slate-50 transition-all" onclick="resetForm()">
                        Cancel
                    </button>
                    <button type="submit" id="submit-btn" class="w-full sm:w-auto bg-primary text-white px-8 py-3 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
                        Save Amenity
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- List Section -->
    <div class="xl:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Existing Amenities</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                            <th class="p-4 font-medium">Name</th>
                            <th class="p-4 font-medium">Category</th>
                            <th class="p-4 font-medium">Status</th>
                            <th class="p-4 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        @forelse($amenities as $amenity)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-xl shadow-sm border border-slate-100">
                                        {!! $amenity->icon ?: '✨' !!}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $amenity->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-slate-600 capitalize">
                                <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg text-xs font-medium">{{ $amenity->category ?: 'General' }}</span>
                            </td>
                            <td class="p-4">
                                @if($amenity->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200">Active</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200">Inactive</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" class="edit-amenity-btn relative inline-flex items-center justify-center p-2 text-[#A89070] hover:bg-slate-50 hover:text-[#8E795E] rounded-lg transition-colors group"
                                        data-id="{{ $amenity->id }}"
                                        data-name="{{ $amenity->name }}"
                                        data-category="{{ $amenity->category }}"
                                        data-icon="{{ $amenity->icon }}"
                                        data-description="{{ $amenity->description }}"
                                        data-sort_order="{{ $amenity->sort_order }}"
                                        data-is_active="{{ $amenity->is_active ? 1 : 0 }}">
                                        <i class="bi bi-pencil text-lg"></i>
                                        <span class="absolute -bottom-8 right-0 w-max px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 font-normal shadow-sm">Edit</span>
                                    </button>
                                    <button type="button" class="delete-amenity-btn relative inline-flex items-center justify-center p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors group"
                                        data-id="{{ $amenity->id }}">
                                        <i class="bi bi-trash text-lg"></i>
                                        <span class="absolute -bottom-8 right-0 w-max px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 font-normal shadow-sm">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-500">
                                No amenities found. Add your first amenity using the form.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
        const iconInput = document.getElementById('icon');
        const iconPreview = document.getElementById('selected-icon-preview');
        const iconBtns = document.querySelectorAll('.icon-select-btn');

        iconBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active state from all
                iconBtns.forEach(b => {
                    b.classList.remove('border-primary', 'text-primary', 'bg-white');
                    b.classList.add('border-transparent', 'text-slate-500', 'bg-slate-100');
                });

                // Add active state to clicked
                this.classList.remove('border-transparent', 'text-slate-500', 'bg-slate-100');
                this.classList.add('border-primary', 'text-primary', 'bg-white');

                // Update hidden input and preview
                const iconHtml = this.dataset.icon;
                iconInput.value = iconHtml;
                iconPreview.innerHTML = iconHtml;
                iconPreview.classList.add('text-primary');

                // Render Lucide icon if selected
                if (iconHtml.includes('data-lucide') && window.lucide) {
                    lucide.createIcons({
                        root: iconPreview
                    });
                }
            });
        });

        document.querySelectorAll('.edit-amenity-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('amenity_id').value = this.dataset.id;
                document.getElementById('name').value = this.dataset.name;
                document.getElementById('category').value = this.dataset.category;

                const iconHtml = this.dataset.icon;
                iconInput.value = iconHtml;
                iconPreview.innerHTML = iconHtml || '?';
                if (iconHtml) {
                    iconPreview.classList.add('text-primary');
                    // Render Lucide icon if editing an amenity with one
                    if (iconHtml.includes('data-lucide') && window.lucide) {
                        lucide.createIcons({
                            root: iconPreview
                        });
                    }
                } else {
                    iconPreview.classList.remove('text-primary');
                }

                iconBtns.forEach(b => {
                    if (b.dataset.icon === iconHtml) {
                        b.classList.remove('border-transparent', 'text-slate-500', 'bg-slate-100');
                        b.classList.add('border-primary', 'text-primary', 'bg-white');
                    } else {
                        b.classList.remove('border-primary', 'text-primary', 'bg-white');
                        b.classList.add('border-transparent', 'text-slate-500', 'bg-slate-100');
                    }
                });

                document.getElementById('description').value = this.dataset.description;
                document.getElementById('sort_order').value = this.dataset.sort_order;
                document.getElementById('is_active').checked = this.dataset.is_active === '1';

                document.getElementById('form-title').innerText = 'Edit Amenity';
                document.getElementById('submit-btn').innerText = 'Update Amenity';
                document.getElementById('cancel-btn').classList.remove('hidden');

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });

        window.resetForm = function() {
            document.getElementById('add-amenity-form').reset();
            document.getElementById('amenity_id').value = '';
            iconInput.value = '';
            iconPreview.innerHTML = '?';
            iconPreview.classList.remove('text-primary');
            iconBtns.forEach(b => {
                b.classList.remove('border-primary', 'text-primary', 'bg-white');
                b.classList.add('border-transparent', 'text-slate-500', 'bg-slate-100');
            });
            document.getElementById('form-title').innerText = 'Add New Amenity';
            document.getElementById('submit-btn').innerText = 'Save Amenity';
            document.getElementById('cancel-btn').classList.add('hidden');
        };

        document.getElementById('add-amenity-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            data.is_active = this.querySelector('#is_active').checked;

            const id = data.id;
            delete data.id; // Remove it from the payload

            const url = id ? `/api/amenities/${id}` : '/api/amenities';
            const method = id ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    const error = await response.json();
                    alert('Error adding amenity: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred.');
            }
        });

        document.querySelectorAll('.delete-amenity-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (!confirm('Are you sure you want to delete this amenity?')) return;

                const id = this.dataset.id;
                try {
                    const response = await fetch(`/api/amenities/${id}`, {
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
                        alert('Error deleting amenity: ' + (error.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred.');
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
    @endpush