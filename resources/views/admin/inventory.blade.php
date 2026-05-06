@extends('layouts.admin')

@section('title', 'Inventory Management - DwellCasa Admin')
@section('header_title', 'Inventory')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Inventory Management</h1>
        <p class="text-slate-500 mt-1">Manage stock levels, supplies, and property inventory.</p>
    </div>
    <div>
        <button onclick="openModal()" title="Add New Item" class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center hover:bg-[#8E795E] transition-all shadow-sm">
            <i class="bi bi-plus-lg text-xl"></i>
        </button>
    </div>
</div>

<!-- List Section -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                    <th class="p-4 font-medium">Item Details</th>
                    <th class="p-4 font-medium">Stock Level</th>
                    <th class="p-4 font-medium">Unit Price</th>
                    <th class="p-4 font-medium">Condition</th>
                    <th class="p-4 font-medium">Status</th>
                    <th class="p-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100" id="inventory-list">
                @forelse($inventory ?? [] as $item)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            @if(isset($item->image) && $item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-12 h-12 rounded-lg object-cover border border-slate-100 cursor-pointer hover:opacity-80 transition-opacity" onclick="openLightbox({{ $item->id }})">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                                    <i class="bi bi-box-seam text-xl"></i>
                                </div>
                            @endif
                            <div>
                                <div class="font-bold text-slate-900">{{ $item->name }}</div>
                                <div class="text-xs text-slate-500 capitalize mt-0.5">{{ str_replace('_', ' ', $item->category) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="font-medium text-slate-700">
                            {{ floatval($item->stock) }} {{ $item->unit }}
                        </div>
                        @if($item->minimum_stock > 0)
                            <div class="text-xs text-slate-400 mt-0.5">Min: {{ floatval($item->minimum_stock) }}</div>
                        @endif
                    </td>
                    <td class="p-4 font-medium text-slate-700">
                        @if(!is_null($item->unit_price))
                            Rs. {{ number_format($item->unit_price, 2) }}
                        @else
                            <span class="text-slate-400">N/A</span>
                        @endif
                    </td>
                    <td class="p-4 capitalize text-slate-600 font-medium">
                        {{ $item->unit_condition }}
                    </td>
                    <td class="p-4">
                        @if($item->status === 'available')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200">Available</span>
                        @elseif($item->status === 'low_stock')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">Low Stock</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200">Out of Stock</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" class="edit-btn w-8 h-8 flex items-center justify-center text-[#A89070] hover:bg-slate-50 hover:text-[#8E795E] rounded-md transition-colors"
                                data-item="{{ json_encode($item) }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" class="delete-btn w-8 h-8 flex items-center justify-center text-red-400 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors"
                                data-id="{{ $item->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-500">
                        No inventory items found. Use the form to add new items.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="inventory-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 id="form-title" class="text-xl font-serif font-bold text-slate-900 italic">Add New Item</h2>
            <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="inventory-form" class="flex flex-col min-h-0" enctype="multipart/form-data">
            <div class="overflow-y-auto p-6 space-y-5">
                @csrf
                <input type="hidden" id="item_id" name="id" value="">

                <div>
                    <label for="image" class="block text-sm font-medium text-slate-700 mb-1">Item Image</label>
                    <input type="file" name="image" id="image" accept="image/*" class="w-full rounded-xl border-slate-200 px-4 py-2 border focus:ring-primary focus:border-primary transition-colors bg-white">
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Item Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required class="w-full rounded-xl border-slate-200 px-4 py-2.5 border focus:ring-primary focus:border-primary transition-colors">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category" id="category" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-primary focus:border-primary transition-colors">
                            <option value="housekeeping">Housekeeping</option>
                            <option value="toiletries">Toiletries</option>
                            <option value="food_beverage">Food & Beverage</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="office">Office</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-primary focus:border-primary transition-colors">
                            <option value="available">Available</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="stock" class="block text-sm font-medium text-slate-700 mb-1">Stock <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="stock" id="stock" required class="w-full border rounded-xl border-slate-200 px-4 py-2.5 focus:ring-primary focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label for="minimum_stock" class="block text-sm font-medium text-slate-700 mb-1">Min. Stock</label>
                        <input type="number" step="0.01" name="minimum_stock" id="minimum_stock" class="w-full border rounded-xl border-slate-200 px-4 py-2.5 focus:ring-primary focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label for="unit_price" class="block text-sm font-medium text-slate-700 mb-1">Unit Price</label>
                        <input type="number" step="0.01" name="unit_price" id="unit_price" class="w-full border rounded-xl border-slate-200 px-4 py-2.5 focus:ring-primary focus:border-primary transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="unit" class="block text-sm font-medium text-slate-700 mb-1">Unit <span class="text-red-500">*</span></label>
                        <select name="unit" id="unit" required class="w-full rounded-xl border-slate-200 px-4 py-2.5 border focus:ring-primary focus:border-primary transition-colors">
                            <option value="pieces">Pieces</option>
                            <option value="kg">KG</option>
                            <option value="liters">Liters</option>
                            <option value="boxes">Boxes</option>
                            <option value="rolls">Rolls</option>
                        </select>
                    </div>
                    <div>
                        <label for="unit_condition" class="block text-sm font-medium text-slate-700 mb-1">Condition <span class="text-red-500">*</span></label>
                        <select name="unit_condition" id="unit_condition" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-primary focus:border-primary transition-colors">
                            <option value="new">New</option>
                            <option value="good">Good</option>
                            <option value="fair">Fair</option>
                            <option value="poor">Poor</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-primary focus:border-primary transition-colors"></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 flex justify-end gap-3 bg-slate-50/50 shrink-0">
                <button type="button" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-200 transition-colors" onclick="closeModal()">Cancel</button>
                <button type="submit" id="submit-btn" class="w-full sm:w-auto bg-primary text-white px-6 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">Save Item</button>
            </div>
        </form>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center bg-black/95 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <!-- Controls -->
    <div class="absolute top-0 left-0 w-full p-4 flex justify-end items-center z-10 bg-gradient-to-b from-black/50 to-transparent">
        <button onclick="closeLightbox()" class="text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 flex items-center justify-center rounded-full shadow-sm">
            <i class="bi bi-x-lg text-xl"></i>
        </button>
    </div>

    <!-- Image Container -->
    <div id="lightbox-img-container" class="relative w-full h-full flex items-center justify-center p-4 md:p-12 overflow-hidden touch-pan-y gap-4 md:gap-8">
        <img id="lightbox-img" src="" alt="" class="max-h-full min-w-0 object-contain select-none transition-transform duration-300 shadow-2xl">
    </div>

    <!-- Caption -->
    <div class="absolute bottom-0 left-0 w-full p-6 text-center z-10 bg-gradient-to-t from-black/80 to-transparent">
        <h3 id="lightbox-caption" class="text-white text-lg font-serif italic mb-1"></h3>
        <p id="lightbox-category" class="text-primary text-xs font-bold uppercase tracking-wider"></p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const inventoryItems = @json($inventory ?? []);
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    const lightboxCategory = document.getElementById('lightbox-category');

    window.openLightbox = function(id) {
        const item = inventoryItems.find(i => i.id == id);
        if (item && item.image) {
            lightboxImg.src = '{{ asset("storage") }}/' + item.image;
            lightboxImg.alt = item.name;
            lightboxCaption.textContent = item.name;
            lightboxCategory.textContent = item.category ? item.category.replace(/_/g, ' ') : '';
            
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            setTimeout(() => {
                lightbox.classList.remove('opacity-0');
            }, 10);
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeLightbox = function() {
        lightbox.classList.add('opacity-0');
        setTimeout(() => {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.style.overflow = '';
        }, 300);
    };

    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('hidden') && e.key === 'Escape') closeLightbox();
    });

    window.openModal = function() {
        const modal = document.getElementById('inventory-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    };

    window.closeModal = function() {
        const modal = document.getElementById('inventory-modal');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            resetForm();
        }, 300);
    };

    window.resetForm = function() {
        document.getElementById('inventory-form').reset();
        document.getElementById('item_id').value = '';
        document.getElementById('form-title').innerText = 'Add New Item';
        document.getElementById('submit-btn').innerText = 'Save Item';
    };

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = JSON.parse(this.dataset.item);
            document.getElementById('item_id').value = item.id;
            document.getElementById('name').value = item.name;
            document.getElementById('category').value = item.category;
            document.getElementById('stock').value = parseFloat(item.stock);
            document.getElementById('unit').value = item.unit;
            document.getElementById('minimum_stock').value = parseFloat(item.minimum_stock);
            document.getElementById('unit_price').value = parseFloat(item.unit_price || 0);
            document.getElementById('unit_condition').value = item.unit_condition;
            document.getElementById('description').value = item.description || '';
            document.getElementById('status').value = item.status;

            document.getElementById('form-title').innerText = 'Edit Item';
            document.getElementById('submit-btn').innerText = 'Update Item';

            openModal();
        });
    });

    document.getElementById('inventory-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const id = document.getElementById('item_id').value;
        const isUpdate = !!id;

        const url = isUpdate ? `/api/inventory/${id}` : '/api/inventory';
        // When using FormData, especially with file uploads, the request method must be POST.
        // For updates, Laravel uses a hidden '_method' field to determine the intended action (PUT/PATCH).
        const method = 'POST';

        if (isUpdate) {
            formData.append('_method', 'PUT');
        }

        try {
            const response = await fetch(url, {
                method: method,
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
                let errorMsg = error.message || 'Unknown error';
                if (error.errors) {
                    errorMsg += '\n' + Object.values(error.errors).flat().join('\n');
                }
                adminToast('Error saving item: ' + errorMsg);
            }
        } catch (error) {
            console.error('Error:', error);
            adminToast('An error occurred.');
        }
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!await adminConfirm('Are you sure you want to delete this item?', { confirmLabel: 'Delete', type: 'danger' })) return;
            
            const id = this.dataset.id;
            try {
                const response = await fetch(`/api/inventory/${id}`, {
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
                    adminToast('Error deleting item: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                adminToast('An error occurred.');
            }
        });
    });
</script>
@endpush
